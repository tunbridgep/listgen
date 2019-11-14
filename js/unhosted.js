function DocumentLoad()
{
    var checkboxes = document.querySelectorAll('[data-id]');
    debugger;

    for(var i = 0;i < checkboxes.length;i++)
    {
        var id = checkboxes[i].getAttribute("data-id");
        var istrue = $.jStorage.get(id,false);
        if (istrue)
            checkboxes[i].checked = true;
        else
            checkboxes[i].checked = false;

        var readonly = checkboxes[i].getAttribute("readonly");
        if (readonly)
            checkboxes[i].onclick = function() { return false; };
        else
            checkboxes[i].onclick = function() { ToggleCheckbox(this.getAttribute("data-id")); };
        SetSummarySections(id);
    }

    var sections = document.querySelectorAll('[class="section"]');
    for(var j = 0;j < sections.length; j++)
    {
        debugger;
        var tab = sections[j].getAttribute('tab-id');
        var section = sections[j].getAttribute('section-id');
        UpdateHeader(section,tab);
    }
    
    var proper_sections = document.querySelectorAll('[section-header-id]');
    for (var k = 0; k < proper_sections.length; k++)
    {
        var id = proper_sections[k].getAttribute('section-header-id');
        debugger;
        //SetHeader(id);
    }

    prepare_tabs();
    prepare_filters();
}

function activate_tab(name)
{
    debugger;
    //deactivate all tabs
    all_tabs = document.querySelectorAll('[data-tab]');
    all_tabs_nav = document.querySelectorAll('[data-tab-nav]');
    for (i = 0; i < all_tabs.length; i++)
        all_tabs[i].className = "tab-pane";
    for (j = 0; j < all_tabs_nav.length; j++)
        all_tabs_nav[j].className = "";

    //activate new tab
    tab = document.querySelectorAll('[data-tab="'+name+'"]')[0];
    tab_nav = document.querySelectorAll('[data-tab-nav="'+name+'"]')[0];
    
    tab_nav.className = "active";
    tab.className = "tab-pane active";
}

function remove_filters()
{
    debugger;
    //deactivate all other filtered elements
    var current_unfilters = document.querySelectorAll('.filter-unhighlight');
    for (i = 0;i < current_unfilters.length;i++)
        current_unfilters[i].classList.remove("filter-unhighlight");
    
    //deactivate all other filters
    var current_filters = document.querySelectorAll('.filter-highlight');
    for (i = 0;i < current_filters.length;i++)
        current_filters[i].classList.remove("filter-highlight");
    
    //deactivate old filter
    var old_filter = document.querySelectorAll('.filter-active')[0];
    if (old_filter != null)
        old_filter.classList.remove("filter-active");
}

function activate_filter(filter)
{

    remove_filters();

    //set new filter
    //var all_elements_matching = document.evaluate("//li[contains(., '"+filter+"')]", document, null, XPathResult.ANY_TYPE, null );;
    //var all_elements_not_matching = document.evaluate("//li[not(contains(., '"+filter+"'))]", document, null, XPathResult.ANY_TYPE, null );;
    var element_types = 'self::li or self::p or self::table or self::h4'; 
    var all_elements_matching = document.evaluate("//*/*["+element_types+"][contains(., '"+filter+"')]", document, null, XPathResult.ANY_TYPE, null );;
    var all_elements_not_matching = document.evaluate("//*/*["+element_types+"][not(contains(., '"+filter+"'))]", document, null, XPathResult.ANY_TYPE, null );;
    var elements_array = Array();
    var not_elements_array = Array();
    while (thisElement = all_elements_matching.iterateNext())
        elements_array.push(thisElement);
    
    while (thisElement = all_elements_not_matching.iterateNext())
        not_elements_array.push(thisElement);

    for (var i = 0;i < elements_array.length;i++)
        elements_array[i].classList.add("filter-highlight");
    for (var i = 0;i < not_elements_array.length;i++)
        not_elements_array[i].classList.add("filter-unhighlight");

    debugger;

    //activate new filter
    var fl = document.querySelectorAll('[data-filter="'+filter+'"]')[0];
    if (fl != null)
        fl.classList.add("filter-active");
}

function prepare_filters()
{
    filters = document.querySelectorAll('[data-filter]');
    
    //add onclick events to every tab nav
    for(i = 0; i < filters.length; i++)
    {
        //name = tab_navs[i].getAttribute('data-tab-nav');
        filters[i].onclick = function() { activate_filter( this.getAttribute('data-filter')); };
    }

    no_filter = document.querySelectorAll('[data-filter-none]')[0].onclick=function() { remove_filters(); };
}

function prepare_tabs()
{
    tabs = document.querySelectorAll('[data-tab]');
    tab_navs = document.querySelectorAll('[data-tab-nav]');
    
    //activate first tab
    first_tab = tabs[0];
    activate_tab(first_tab.getAttribute('data-tab'));

    //add onclick events to every tab nav
    for(i = 0; i < tab_navs.length; i++)
    {
        //name = tab_navs[i].getAttribute('data-tab-nav');
        tab_navs[i].onclick = function() { activate_tab( this.getAttribute('data-tab-nav')); };
    }
}

function ToggleCheckbox(id)
{
    element = document.querySelectorAll('[data-id="'+id+'"]')[0];
    if (!element)
        return;
    tag = element.attributes.getNamedItem('data-tag');
    debugger;

    StoreCheck(element);

    //check or uncheck all other checkboxes containing the same tag
    if (tag)
    {
        var others = document.querySelectorAll('[type="checkbox"][data-tag="'+tag.value+'"]');
        for (i = 0;i < others.length;i++)
        {
            other_id = others[i].getAttribute('data-id');
            change = others[i].getAttribute('tag-change') == "true";
            if (other_id != id && change) //don't change ourself, and only change if we allow changing
            {
                others[i].checked = element.checked;
                StoreCheck(others[i]);
            }
        }
    }

    SetSummarySections(id);
    var section = element.getAttribute("section-id");
    var tab = element.getAttribute("tab-id");
    UpdateHeader(section,tab);
}

function StoreCheck(element)
{
    if (element)
    {
        id = element.getAttribute('data-id');
        if (id)
        {
            if (element.checked == false)
                $.jStorage.set(id,false);
            else if (element.checked == true)
                $.jStorage.set(id,true);
        }
    }
}

function SetSummarySections(id)
{
    element = document.querySelectorAll('[data-id="'+id+'"]')[0];
    tag = element.attributes.getNamedItem('data-tag');
    debugger; 

    if (tag)
    {
        var checkboxes = document.querySelectorAll('[type="checkbox"][data-tag="'+tag.value+'"]');
        summary_elements = document.querySelectorAll('span.readonly[data-tag="'+tag.value+'"]','span.readonly.in-progress[data-tag="'+tag.value+'"]','span.readonly.incomplete[data-tag="'+tag.value+'"]','span.readonly.complete[data-tag="'+tag.value+'"]');

        //check if all the checkboxes are checked
        count = 0;
        for (i = 0;i < checkboxes.length;i++)
            if (checkboxes[i].checked == true)
                count++;

        for (i = 0;i < summary_elements.length; i++)
        {
            if (count == checkboxes.length)
            {
                summary_elements[i].innerHTML = "&#10004;";
                summary_elements[i].classList.add('complete');
                summary_elements[i].classList.remove('incomplete');
                summary_elements[i].classList.remove('in-progress');
            }
            else if (count == 0)
            {
                summary_elements[i].innerHTML = "&#10006;";
                summary_elements[i].classList.add('incomplete');
                summary_elements[i].classList.remove('complete');
                summary_elements[i].classList.remove('in-progress');
            }
            else
            {
                summary_elements[i].innerHTML = "&#10070;";
                summary_elements[i].classList.add('in-progress');
                summary_elements[i].classList.remove('complete');
                summary_elements[i].classList.remove('incomplete');
            }
            var section = summary_elements[i].getAttribute("section-id");
            var tab = summary_elements[i].getAttribute("tab-id");
            UpdateHeader(section,tab);
        }
    }
}

function ClearAll()
{
    var checkboxes = document.querySelectorAll('[data-id]');
    debugger;
    
    for(var i = 0;i < checkboxes.length;i++)
    {
        var id = checkboxes[i].getAttribute("data-id");
        checkboxes[i].checked = false;
        ToggleCheckbox(id);
    }
}

function SelectAll()
{
    var checkboxes = document.querySelectorAll('[data-id]');
    debugger;
    
    for(var i = 0;i < checkboxes.length;i++)
    {
        var id = checkboxes[i].getAttribute("data-id");
        checkboxes[i].checked = true;
        ToggleCheckbox(id);
    }
}

function UpdateHeader(section,tab)
{
    debugger;
    var checkboxes = document.querySelectorAll('[type="checkbox"][tab-id="'+tab+'"][section-id="'+section+'"]');
    var summaries = document.querySelectorAll('.readonly[tab-id="'+tab+'"][section-id="'+section+'"]');

    if (checkboxes.length == 0 && summaries.length == 0)
        return;

    var total = checkboxes.length + summaries.length;
    var count = 0;
    for (var i = 0;i < checkboxes.length;i++)
        if (checkboxes[i].checked == true)
            count++;
    for (var j = 0;j < summaries.length;j++)
        if (summaries[j].classList.contains('complete'))
            count++;

    var title = document.querySelectorAll('[title-id="title_'+tab+'_'+section+'"]')[0];
    var title_header = document.querySelectorAll('[title-header-id="title_'+tab+'_'+section+'"]')[0];

    if (count == total)
    {
        title.innerText = "[DONE]";
        title.className = "done";
        title_header.innerText = "[DONE]";
        title_header.className = "done";
    }
    else
    {
        title.innerText = "[" + count + "/" + total + "]";
        title.className = "in_progress";
        title_header.innerText = "[" + count + "/" + total + "]";
        title_header.className = "in_progress";
    }

}

function SetHeader(id)
{
    var header = document.querySelectorAll('[section-header-id="'+id+'"')[0];
    var header_pos = $.jStorage.get("header_collapse_"+id);
    if (header_pos == true)
        HideHeader(id);
    else
        ShowHeader(id);
    
}

function ShowHeader(id)
{
    var header = document.querySelectorAll('[section-header-id="'+id+'"')[0];
    header.className = "";
    $.jStorage.set("header_collapse_"+id,false);
}

function HideHeader(id)
{
    var header = document.querySelectorAll('[section-header-id="'+id+'"')[0];
    header.className = "collapse";
    $.jStorage.set("header_collapse_"+id,true);
}

function ToggleHeader(id)
{
    var header = document.querySelectorAll('[section-header-id="'+id+'"')[0];
    var header_pos = $.jStorage.get("header_collapse_"+id);
    if (header_pos == true)
        ShowHeader(id);
    else
        HideHeader(id);
}

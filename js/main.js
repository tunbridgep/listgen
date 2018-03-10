function DocumentLoad()
{
    var checkboxes = document.querySelectorAll('[data-id]');
    debugger;

    for(var i = 0;i < checkboxes.length;i++)
    {
        var id = checkboxes[i].getAttribute("data-id");
        var istrue = $.jStorage.get(id,true);
        checkboxes[i].checked = istrue;
        checkboxes[i].onclick = function() { ToggleCheckbox(this.getAttribute("data-id")); };
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
    tag = element.attributes.getNamedItem('data-tag');
    debugger;

    if (tag)
    {
        similar_elements = document.querySelectorAll('[data-tag="'+tag.value+'"]');
        for (i = 0;i < similar_elements.length; i++)
        {
            similar_element_id = similar_elements[i].attributes.getNamedItem('data-id').value;
            current_element_id = element.attributes.getNamedItem('data-id').value;
            if (similar_element_id != current_element_id)
            {
                if (element.checked == true)
                {
                    similar_elements[i].checked = true;
                    $.jStorage.set(similar_element_id,true);
                }
                else
                {
                    similar_elements[i].checked = false;
                    $.jStorage.set(similar_element_id,false);
                }
            }
        }
    }

    if (element.checked == false)
        $.jStorage.set(id,false);
    else if (element.checked == true)
        $.jStorage.set(id,true);

    var section = element.getAttribute("section-id");
    var tab = element.getAttribute("tab-id");
    UpdateHeader(section,tab);
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

    if (checkboxes.length == 0)
        return;

    var total = checkboxes.length;
    var count = 0;
    for (var i = 0;i < total;i++)
        if (checkboxes[i].checked == true)
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

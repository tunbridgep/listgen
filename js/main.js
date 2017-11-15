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

    var sections = document.querySelectorAll('[title-id]');
    for(var j = 0;j < sections.length; j++)
    {
        debugger;
        var id = sections[j].getAttribute('title-section');
        UpdateHeader(id);
    }
    
    var proper_sections = document.querySelectorAll('[section-header-id]');
    for (var k = 0; k < proper_sections.length; k++)
    {
        var id = proper_sections[k].getAttribute('section-header-id');
        debugger;
        SetHeader(id);
    }
}

function ToggleCheckbox(id)
{
    element = document.querySelectorAll('[data-id="'+id+'"]')[0];
    debugger;
    if (element.checked == false)
        $.jStorage.set(id,false);
    else if (element.checked == true)
        $.jStorage.set(id,true);

    var section = element.getAttribute("section-id");
    UpdateHeader(section);
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

function UpdateHeader(id)
{
    var checkboxes = document.querySelectorAll('[section-id="'+id+'"]');
    var total = checkboxes.length;
    var count = 0;
    for (var i = 0;i < total;i++)
        if (checkboxes[i].checked == true)
            count++;

    var title = document.querySelectorAll('[title-id="title_'+id+'"]')[0];

    if (count == total)
    {
        title.innerText = "[DONE]";
        title.className = "done";
    }
    else
    {
        title.innerText = "[" + count + "/" + total + "]";
        title.className = "in_progress";
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

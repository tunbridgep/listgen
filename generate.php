<?php

echo "Reading Config file\n\n";
require("config.php");

echo "Generating output file\n\n";
foreach(glob("php_lib/*.php") as $filename)
    require_once($filename);


$default_link_location = rtrim($default_link_location,"/\\");


$body = new Template($template);
$body->SetTitle($title);
$body->SetDesc($desc);
$tabs = 0;

#generate all tabs
foreach(glob("sections/*",GLOB_ONLYDIR) as $path)
{
    $name = basename($path);
    $tab = new Tab($name,"This is a test tab","Warning: Test Tab",$path,$tabs++);
    $body->AddTab($tab);
}

foreach(glob("js/*.js") as $filename) $body->AddJSFile($filename);
foreach(glob("css/*.css") as $filename) $body->AddCSSFile($filename);

# Create Output File
@unlink("output.html");
file_put_contents("output.html",$body->GetText());

die();


























$body->SetListName($listname);
$body->SetDesc($desc);
$body->SetLongDesc($long_desc);
$body->SetSubtitle($long_desc_subtitle);
$body->AddHeaderContent("<ul>","\n");

#create content
foreach(glob("sections/*",GLOB_ONLYDIR) as $folder)
{
    $folder = basename($folder);

    $body->AddContent('<div id="tab'.$folder.'">',"\n");
    $body->AddTab($folder);
    foreach(glob("sections/".$folder."/*.md") as $filename)
    {
        $section++;
        $s = new Section($default_link_location,$default_link_space_character,$section);
        $s->GenerateSection($filename);

        $body->AddContent("<a id=\"".$s->GetSectionLinkName()."\"></a><div section-header-id=\"".$section."\">","\n");
        $body->AddContent("<h3 onclick=\"ToggleHeader(".$section.")\">".$s->GetNiceName()." <span title-section=\"".$section."\" title-id=\"title_".$section."\"></span></h3>","\n");
        $body->AddContent($s->GetContentString(),"\n");
        $body->AddContent("</div>","\n");
        $body->AddHeaderContent("<li><a href=\"#".$s->GetSectionLinkName()."\" >".$s->GetNiceName()."</a> <span title-header-id=\"title_".$section."\"></span></li>","\n");
    }
    $body->AddContent('</div>',"\n");
}
$body->AddHeaderContent("</ul>","\n");

foreach(glob("js/*.js") as $filename) $body->AddJSFile($filename);
foreach(glob("css/*.css") as $filename) $body->AddCSSFile($filename);

# Create Output File
@unlink("output.html");
file_put_contents("output.html",$body->GetText());
?>

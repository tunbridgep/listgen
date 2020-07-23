<?php
    #THIS IS DESIGNED TO GENERATE SHEETS which are hosted, rather than standalone

foreach(glob("php_lib/*.php") as $filename)
    require_once($filename);

###GENERATED FOR OFFLINE USE
if (isset($argv[0]))
{
    if (!isset($argv[1]))
        die("No name specified - aborting");

    $name = $argv[1];
    $location = "lists/".$name;
    $config_location = "lists/".$name."/config.php";
    if (!isset($config_location))
        die("Config file was not found at ".$config_location);

    echo "Reading Config file for ".$name."\n\n";
    require($config_location);
    echo "Generating output file ".$output."\n\n";
    $body = generate_file($location);
    $body->AddJSFile("js/_jstorage.js");
    $body->AddJSFile("js/unhosted.js");
    foreach(glob("css/*.css") as $filename) $body->AddCSSFile($filename);
    @unlink($output);
    file_put_contents($output,$body->GetText());

}
###CALLED UP FROM A WEBPAGE
if (isset($_GET['page']))
{
    $db = new db(); 
    $location = "lists/".$_GET['page'];
    require($location."/config.php");
    $body = generate_file($location);

    //$body->AddJSFile("js/common.js");
    //$body->AddJSFile("js/hosted.js");
    $body->AddJSFile("js/_jstorage.js");
    $body->AddJSFile("js/unhosted.js");
    foreach(glob("css/*.css") as $filename) $body->AddCSSLink($filename);

    echo $body->GetText();
    echo PHP_EOL."<p><a href=\".\">Back to main page</a></p>".PHP_EOL;
}
#we are requesting the JS file
else if (isset($_GET['js']))
{
    echo "";
}
else if (!isset($argv[0]))
{
    echo "<ul>";
    foreach(glob("lists/*",GLOB_ONLYDIR) as $list)
        if (basename($list) != ".git")
            echo "<li><a href=\"?page=".basename($list)."\">".basename($list)."</li>";
    echo "</ul>";
}


function generate_file($location)
{
    global $title;
    global $desc;
    global $template;
    $body = new Template($template);
    $body->SetTitle($title);
    $body->SetDesc($desc);
    $tabs = 0;

    #generate all tabs
    foreach(glob($location."/sections/*",GLOB_ONLYDIR) as $path)
    {
        #echo $path."\n";
        $name = basename($path);
        $hash = hash("md5",$name);
        $tab = new Tab($name,"This is a test tab","Warning: Test Tab",$path,$hash);
        $body->AddTab($tab);
    }
    return $body;
}

?>

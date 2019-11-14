<?php

foreach(glob("php_lib/*.php") as $filename)
    require_once($filename);

echo "Reading Config file\n\n";
require("config.php");

echo "Generating output file ".$output."\n\n";

$body = new Template($template);
$body->SetTitle($title);
$body->SetDesc($desc);
$tabs = 0;

#generate all tabs
foreach(glob("sections/*",GLOB_ONLYDIR) as $path)
{
    #echo $path."\n";
    $name = basename($path);
    $hash = hash("md5",$name);
    $tab = new Tab($name,"This is a test tab","Warning: Test Tab",$path,$hash);
    $body->AddTab($tab);
}

foreach(glob("js/*.js") as $filename) $body->AddJSFile($filename);
foreach(glob("css/*.css") as $filename) $body->AddCSSFile($filename);

# Create Output File
@unlink($output);
file_put_contents($output,$body->GetText());

?>

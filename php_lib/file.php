<?php

#takes an array of tag names/values, and replaces each of the tag names with their values, returning the whole file
function return_file_without_tags(string $input_file, array $mod_keys, string $outputfile)
{
    $input = file_get_contents($input_file);

    foreach($mod_keys as $key => $modification)
        $input = str_replace($key,$modification,$input);

    return $input;
}

?>

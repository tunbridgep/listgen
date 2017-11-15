<?php
class Section
{
    private $content = array();
    private $name;
    private $nicename;
    private $header;
    private $link_prefix;
    private $link_space;
    private $checkbox_count;
    private $section_counter;

    public function __construct($prefix,$space,$section_num)
    {
        $this->link_prefix = $prefix;
        $this->link_space = $space;
        $this->section_counter = $section_num;
        $this->checkbox_count = 0;
    }

    public function GenerateSection($md_file)
    {

        $pd = new Parsedown();

        #get the lines
        $lines = file($md_file, FILE_IGNORE_NEW_LINES);

        #parse each line
        foreach($lines as $line => $text)
        {
            #replace line text with it's parsed version
            $lines[$line] = $this->GenerateLinks($text);
        }

        #get HTML for the md file
        $lines_string = implode("\n",$lines);
        $html_lines = explode("\n",$pd->text($lines_string));

        #parse each HTML line and add a checkbox
        foreach($html_lines as $line => $text)
        {
            #replace line text with it's parsed version
            $html_lines[$line] = $this->GenerateCheckbox($text);
        }

        #once we have done string modifications, we can then store the result
        $this->content = $html_lines;
        $this->name = $this->GenerateName($md_file);
        $this->nicename = $this->GenerateNiceName($md_file);
    }

    private function GenerateCheckbox($line)
    {
        if(strpos($line,"@ign") !== FALSE)
            return str_replace("@ign","",$line);
        else
        {
            $line = str_replace("<li>","<li><label><input section-id=\"".$this->section_counter."\" type=\"checkbox\" data-id=\"list_".$this->section_counter."_".($this->checkbox_count++)."\"/>",$line);
            if (strpos($line,"<ul>") !== FALSE)
                $line = str_replace("<ul>","</label><ul>",$line);
            else
                $line = str_replace("</li>","</label></li>",$line);
            return $line;
        }

    }

    private function GenerateLinks($line)
    {
        #echo "LINE BEFORE PROCESSING: ".$line."\n";
        $pos = strpos($line,"@x");
        while ($pos !== FALSE)
        {

            $count_end = 1;
            do
            {
                $count_end--;
                $char = substr($line,$pos + $count_end,1);
            }
            while($char != "]");

            $count_start = $count_end;
            do
            {
                $count_start--;
                $char = substr($line,$pos + $count_start,1);
            }
            while($char != "[");

            $pos_start = $pos+$count_start+1;
            $pos_length = $pos+$count_end-$pos_start;


            $link_text_unsanitised = substr($line,$pos_start,$pos_length) . "\n\n\n\n";
            $link_text = str_replace(" ",$this->link_space,$link_text_unsanitised);
            $link_text = trim($this->link_prefix . "/" . $link_text);

            $line = $this->str_replace_first("(@x)","(".$link_text.")",$line);
            
            $pos = strpos($line,"@x");
        }

        #fix up "@ex" links
        $line = str_replace("@ex/",$this->link_prefix."/",$line);

        #echo "LINE: ".$line."\n";
        return $line;
    }

    function str_replace_first($from, $to, $subject)
    {
        $from = '/'.preg_quote($from, '/').'/';

        return preg_replace($from, $to, $subject, 1);
    }

    private function GenerateName($string)
    {
        $base = basename($string);
        $noext = pathinfo($base,PATHINFO_FILENAME);

        return $noext;
    }

    public function SetHeader($string)
    {
        $this->header = $string;
    }

    public function GetHeader()
    {
        return $this->header;
    }

    private function GenerateNiceName($string)
    {
        $name = $this->GenerateName($string); #get base name
        $no_number = ltrim($name,"0123456789"); #strip numbers
        $no_underscore = str_replace('_',' ',$no_number); #convert underscores to spaces
        $case = ucwords($no_underscore); #convert words to title case

        return $case;
    }

    public function GetContent()
    {
        return $this->content;
    }

    public function GetContentString()
    {
        return implode("\n",$this->content);
    }

    public function SetContent(array $md)
    {
        $this->content = $md;
    }

    public function GetName()
    {
        return $this->name;
    }

    public function GetNiceName()
    {
        return $this->nicename;
    }
} 
?>

<?php
class Section
{
    private $content = array();
    private $name;
    private $nicename;
    private $sectionlink_name;
    private $header;
    private $link_prefix;
    private $link_space;
    private $checkbox_count;
    private $section_counter;
    private $tab_counter;

    public function __construct($prefix,$space,$section_num,$tab_num)
    {
        $this->link_prefix = $prefix;
        $this->link_space = $space;
        $this->section_counter = $section_num;
        $this->checkbox_count = 0;
        $this->tab_counter = $tab_num;
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
            #$lines[$line] = $this->GenerateLinks($text);
            $linked = $this->GenerateLinks($text);
            $get_img = $this->GenerateImg($linked);
            $lines[$line] = $get_img;
        }

        #get HTML for the md file
        $lines_string = implode("\n",$lines);
        $html_lines = explode("\n",$pd->text($lines_string));

        #parse each HTML line and add a checkbox
        foreach($html_lines as $line => $text)
        {
            #replace line text with it's parsed version
            $checkbox = $this->GenerateCheckbox($text);
            $tagged = $this->GenerateTag($checkbox);
            $special = $this->ParseSpecial($tagged);
            $html_lines[$line] = $special;
        }

        #once we have done string modifications, we can then store the result
        $this->content = $html_lines;
        $this->name = $this->GenerateName($md_file);
        $this->sectionlink_name = $this->GenerateSectionLinkName($md_file);
        $this->nicename = $this->GenerateNiceName($md_file);
    }

    private function GenerateCheckbox($line)
    {
        if(strpos($line,"@ign") !== FALSE)
            return str_replace("@ign","",$line);
        else if(strpos($line,"@readonly") !== FALSE)
        {
            $line = str_replace("@readonly","",$line);
            $line = str_replace('<li>','<li><span class="readonly" tab-id="'.$this->tab_counter.'" section-id="'.$this->section_counter.'"></span> ',$line);
            return $line;
        }
        else
        {
            $this->checkbox_count++;
            $hash = hash("md5",$line);
            $line = str_replace("<li>","<li><label><input type=\"checkbox\" tab-id=\"".$this->tab_counter."\" section-id=\"".$this->section_counter."\" checkbox-id=\"".$hash."\" type=\"checkbox\" data-id=\"list_".$this->tab_counter."_".$this->section_counter."_".$hash."\"/>",$line);
            
            if (strpos($line,"<ul>") !== FALSE)
                $line = str_replace("<ul>","</label><ul>",$line);
            else
                $line = str_replace("</li>","</label></li>",$line);
            return $line;
        }

    }

    #Do any special parsing that the line needs
    private function ParseSpecial($line)
    {
        $str = $line;
        if(strpos($line,"@imp") !== FALSE)
        {
            $str = str_replace("@imp","",$str);
            $str = str_replace("<li","<li class=\"important\"",$str);
            $str = str_replace("<p","<p class=\"important\"",$str);
        }

        if(strpos($line,"data-tag") !== FALSE)
        {
            if(strpos($line,"@nochange") !== FALSE)
            {
                $str = str_replace("@nochange","",$str);
                $str = str_replace("<input","<input tag-change=\"false\"",$str);
            }
            else
                $str = str_replace("<input","<input tag-change=\"true\"",$str);
        }
        return $str;
    }

    private function GenerateImg($line)
    {
        global $standalone;
        #get a start point
        $pos = strpos($line,"@img{");
        $after = substr($line,$pos);

        #make sure we have an opening and closing brace
        if ($pos === FALSE || strpos($after,"}") == FALSE)
            return $line;

        #our end point is at the same location as our start point
        $forward = 0;

        #keep moving the end point towards the end of the string until we find the closing brace
        $character = "";
        do
        {
            $forward++;
            $character = substr($line,$pos + $forward,1);
        }
        while ($character !== "}");

        #calculate tag using our start and end points
        #we are offseting by 5 to remove the @tag{ characters from the string
        $url = substr($line,$pos+5,$forward-5);

        $local = $this->IsLocalURL($url);

        #echo "url: ".$url."\n";

        if ($local || $standalone)
        {
            $img = file_get_contents($url);
            $ext = pathinfo($url)['extension'];

            #check if something failed
            if ($img === FALSE || $ext == "")
                return $line;
            
            $b64 = base64_encode($img);

            #echo "URL is local\nextension: ".$ext."\n";
            #echo "b64: ".$b64."\n";

            #generate an img tag from image link
            $line = str_replace("@img{".$url."}",'<img src="data:image/'.$ext.';base64,'.$b64.'">',$line);
        }
        else
        {
            #we are using an external image. Link to it directly
            $line = str_replace("@img{".$url."}",'<img src="'.$url.'">',$line);
        }
        return $line;
    }

    private function IsLocalURL($url)
    {
        return realpath($url) != "";
    }

    private function GenerateTag($line)
    {
        #get a start point
        $pos = strpos($line,"@tag{");
        $after = substr($line,$pos);

        #make sure we have an opening and closing brace
        if ($pos === FALSE || strpos($after,"}") == FALSE)
            return $line;

        #our end point is at the same location as our start point
        $forward = 0;

        #keep moving the end point towards the end of the string until we find the closing brace
        $character = "";
        do
        {
            $forward++;
            $character = substr($line,$pos + $forward,1);
        }
        while ($character !== "}");

        #calculate tag using our start and end points
        #we are offseting by 5 to remove the @tag{ characters from the string
        $tag = substr($line,$pos+5,$forward-5);

        #remove @tag from original string
        $line = str_replace("@tag{".$tag."}","",$line);


        #modify HTML to contain tag name in the input data-tag field
        if ($tag != "")
        {
            if(strpos($line,'class="readonly"') !== FALSE)
                return str_replace("<span","<span data-tag=\"".$tag."\"",$line);
            else
                return str_replace("<input","<input data-tag=\"".$tag."\"",$line);
        }
        else
            return $line;

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


            $link_text_unsanitised = substr($line,$pos_start,$pos_length);
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

    private function GenerateSectionLinkName($string)
    {
        $name = $this->GenerateName($string);
        $nospaces = str_replace(" ","_",$name);
        return $nospaces;
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
        $html  = '<a name="'.$this->sectionlink_name.'"></a>'."\n";
        $html .= '<div class="section" section-id="'.$this->section_counter.'" tab-id="'.$this->tab_counter.'">';
        $html .= '<h2>'.$this->GetNiceName().' <span title-id="title_'.$this->tab_counter.'_'.$this->section_counter.'"></span></h2>';
        $html .= implode("\n",$this->content);
        $html .= '</div>';
        return $html;
    }

    public function SetContent(array $md)
    {
        $this->content = $md;
    }

    public function GetName()
    {
        return $this->name;
    }

    public function GetSectionLinkName()
    {
        return $this->sectionlink_name;
    }

    public function GetHash()
    {
        return $this->section_counter;
    }

    public function GetNiceName()
    {
        return $this->nicename;
    }
} 
?>


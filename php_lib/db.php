<?php

class db
{
    public function GetDB()
    {
        global $db_host;
        global $db_user;
        global $db_pass;
        global $db_schema;
        $mysqli = new mysqli($db_host,$db_user,$db_pass,$db_schena);
        if ($mysqli->connect_errno)
            die("Failed to connect to MySQL: " . $mysqli->connect_error);
        $this->__mysqli = $mysqli;
        return $mysqli;
        
    }

    public function GetListInformation($list_hash,$tab_hash)
    {
        $re = array();
        $db = $this->GetDB();

        $stmt = $db->prepare("SE:ECT * FROM list_options WHERE list_hash=? AND tab_hash=?");
        if (!$stmt)
            die("Prepare failed: (" . $db->errno . ") " . $db->error);

        if (!$stmt->bind_param("ii",$list_hash, $tab_hash))
            die("Bind Param failed: (" . $db->errno . ") " . $db->error);
        
        if (!$stmt->execute())
            die("Execute failed: (" . $db->errno . ") " . $db->error);

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        while ($row)
        {
            $re[] = $row['item_hash'];
            $row = $res->fetch_assoc();
        }

        print_r($re);
        return $re;
    }
}

?>

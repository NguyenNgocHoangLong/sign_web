<?php
require_once("config/db.class.php");

class Sign{
    public $signID;
    public $ID;
    public $date;

    public function __construct($ID,$date){
        $this->ID=$ID;
        $this->date=$date;
    }

}
?>
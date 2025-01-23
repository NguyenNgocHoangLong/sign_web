<?php
require_once("config/db.class.php");

class Khach{
    public $khachID;
    public $khach_name;
    public $position;
    public $email;
    public $sign;
    public $password;

    public function __construct($khach_name,$position,$email,$sign,$password){
        $this->khach_name=$khach_name;
        $this->position=$position;
        $this->email=$email;
        $this->sign=$sign;
        $this->password=$password;
    }

}
?>
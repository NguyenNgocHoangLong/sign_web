<?php
require_once("config/db.class.php");

class Khach{
    public $khachID;
    public $khach_name;
    public $email;
    public $sign;
    public $password;

    public function __construct($khach_name,$email,$sign,$password){
        $this->khach_name=$khach_name;
        $this->email=$email;
        $this->sign=$sign;
        $this->password=$password;
    }

}
?>
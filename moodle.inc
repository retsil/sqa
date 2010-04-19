<?php
global $USER;
global $CFG;

class User 
{
  public $id = 1;
  public $firstname = 'Robert';
  public $lastname = 'Barnett';
  public $username = 'robert';
  public $email = 'robert@barnett.id.au';
}

$USER = new User();

class Cfg 
{
  public $wwwroot = 'http://localhost/';
}

$CFG = new Cfg();

function isloggedin() {
	return 1;
}
?>
<?php


$activePage = basename($_SERVER['PHP_SELF'], ".php");
include_once 'lib/dao.php';
include_once 'lib/model.php';

$d = new dao();
$m = new model();



$con=$d->dbCon();


date_default_timezone_set("Asia/Calcutta");
header('Access-Control-Allow-Origin: *');  //I have also tried the * wildcard and get the same response
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');





$key = $_SERVER['HTTP_KEY'];  
$auth_user_name= $_SERVER['PHP_AUTH_USER'];
$auth_password= $_SERVER['PHP_AUTH_PW'];


?>
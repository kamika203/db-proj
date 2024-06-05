<?php

$dbloc='localhost';
$dbname='budget';
$dbuser="root";
$dbpass="";

$id=@mysqli_connect($dbloc,$dbuser,$dbpass,$dbname);

if (!$id){
  exit("<p>К сожалению, ошибка: ".mysqli_connect_error()."</p>");
}

mysqli_set_charset($id,"utf8")


?>

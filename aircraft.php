<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

session_start();

$path = __DIR__.'/closed/secret.php';
include $path;

//Initial connection setup.                                                  
$dbhost = 'oniddb.cws.oregonstate.edu';
$dbname = 'fischmaj-db';
$dbuser = 'fischmaj-db';

$mysql_handle = new mysqli($dbhost, $dbuser, $dbpass, $dbname)
  or die("Error connecting to database server");

mysqli_select_db($mysql_handle, $dbname)
or die("Error selecting database: $dbname");

/****************************************
 ** getAcft - this function queries DB
 ** and returns all aircraft as a JSON object
 ** Used in Javascript forms
 ****************************************/
function getAcft($sql_handle){  
  
  $result = $sql_handle->query("SELECT * FROM Aircraft");
  $myArray = array();
  
  while($row = $result->fetch_assoc()){
    array_push($myArray, $row);
  }
  
  $result->close();
  return json_encode($myArray);
}

if (isset($_SESSION["pilot_id"])){
  echo getAcft($sql_handle);
}
?>
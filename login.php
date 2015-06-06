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

$email = $_POST['email'];
$pwordplain = $_POST['pword'];
$pwordcrypt = password_hash($pwordplain, PASSWORD_DEFAULT);



function checkPwd ($email, $pwd, $db_handle) {
  //Prepare query  
  if(!($pwd_query = $db_handle->prepare('SELECT Email, Password, FNAME
                                  FROM Pilot
                                  WHERE Email = ? '))){
    echo "Prepare failed: (" . $db_handle->errno . ") " . $db_handle->error;
  }

  //Bind Parameters
  if(!$pwd_query->bind_param("s", $email)){
    echo "Bind failed: (" . $pwd_query->errno . ") " . $pwd_query->error;
  }

  //Execute query
  if (!$pwd_query->execute()) {
    echo "Execute failed: (" . $pwd_query->errno . ") " . $pwd_querry->error;
  }


  $result = $pwd_query->get_result(); 
  $row = $result->fetch_assoc();

  if ($row == NULL){
    $_SESSION = array();
    echo("1");

  } elseif (password_verify($pwd, $row["Password"] )) {

    $_SESSION = array();
    header("refresh:5; url=userpage.php");
    echo "login successful.";

  } else {
    echo "2";
  }
}



checkPwd ($email, $pwordplain, $mysql_handle);


mysqli_close($mysql_handle);


?>
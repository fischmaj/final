<?php
<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
session_start();


$path = __DIR__."/closed/secret.php";
include $path;

//Initial connection setup.                                                  
$dbhost = 'oniddb.cws.oregonstate.edu';
$dbname = 'fischmaj-db';
$dbuser = 'fischmaj-db';

$mysql_handle = new mysqli($dbhost, $dbuser, $dbpass, $dbname)
  or die("Error connecting to database server");

mysqli_select_db($mysql_handle, $dbname)
or die("Error selecting database: $dbname");


function addUser ($email, $FName, $LName, $pwd, $db_handle) {

  //Prepare the query  
  if(!($pwd_query = $db_handle->prepare('INSERT INTO PILOT
                                (FName, LName, Email, Password)
                                 VALUES (?,?,?,?)'))){
    header("refresh:5; url = newuser.php");
    echo "Prepare failed: (" . $db_handle->errno . ") " . $db_handle->error;
  }
  //Bind Parameters
  if(!$pwd_query->bind_param("ssss", $FName, $LName, $email, $pwd)){
    header("refresh:5; url = newuser.php");
    echo "Bind failed: (" . $pwd_query->errno . ") " . $pwd_query->error;
  }
  //Execute query
  if (!$pwd_query->execute()) {
    header("refresh:5; url = newuser.php");
    echo "Execute failed: (" . $pwd_query->errno . ") " . $pwd_querry->error;
  }

}

//Get the POSTED values and return to newuser.php with error on failure
$email =$_POST["Email"]; 
if ($email===NULL){
  header("refresh:5; url = newuser.php?errcode=1");
  echo "<p style=\"color:red;\">>Error detected on email address. <br>";
  echo "Your account must included a valid and unique email address.</p>";
}

$FName = $_POST["FName"];
if ($FName===NULL){
  header("refresh:5; url = newuser.php?errcode=2");
  echo "<p style=\"color:red;\">>Error detected on \"First Name.\" <br>";
  echo "Your account must included a \"First Name.\"</p>";
}

$LName = $_POST["LName"];
if ($LName===NULL){
  header("refresh:5; url = newuser.php?errcode=3");
  echo "<p style=\"color:red;\">>Error detected on \"Last Name.\" <br>";
  echo "Your account must included a \"Last Name.\"</p>";
}

$pwdplain = $_POST["Password"];
if (($pwdplain===NULL)
    ||(strlen($pwd)<8)){   //checking password for >= 8 characters
  header("refresh:5; url = newuser.php?errcode=4");
  echo "<p style=\"color:red;\">>Error detected. <br>";
  echo "Your password cannot be less than 8 characters.</p>";
}

$pwdcrypt = password_hash($pwdplain, PASSWORD_DEFAULT);

//If we've made it this far into execution, we should have 4 valid user fields
addUser($email, $FName, $LName, $pwdcrypt, $mysql_handle);
header("Location: userpage.php");
?>
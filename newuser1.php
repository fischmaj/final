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

//get POSTed variables
$fName = $_POST['fName'];
$lName = $_POST['lName'];
$email = $_POST['email'];
$pwordplain = $_POST['password'];
$pwordcrypt = password_hash($pwordplain, PASSWORD_DEFAULT);



function newUser ($fName, $lName, $email, $pwd, $db_handle) {
  //Prepare query  
  $unique = true;

  if(!($uniqueUser_query = $db_handle->prepare('SELECT Email
                                  FROM Pilot
                                  WHERE Email = ? '))){
    echo "Prepare failed: (" . $db_handle->errno . ") " . $db_handle->error;
  }

  //Bind Parameters
  if(!$uniqueUser_query->bind_param("s", $email)){
    echo "Bind failed: (" . $uniqueUser_query->errno . ") " .
      $uniqueUser_query->error;
  }

  //Execute query
  if (!$uniqueUser_query->execute()) {
    echo "Execute failed: (" . $uniqueUser_query->errno . ") " .
      $uniqueUser_querry->error;
  }


  $result = $uniqueUser_query->get_result(); 
  $row = $result->fetch_assoc();

  if ($row == NULL){ //email, and therefore user is unique!
    
    if(!($newUser_query = $db_handle->prepare('INSERT INTO Pilot 
                                  (fName, lName, Email, Password) 
                                  VALUES (?,?,?,?) '))){
      echo "Prepare failed: (" . $db_handle->errno . ") " . $db_handle->error;
    }

    $pwd_crypt =  password_hash($pwd, PASSWORD_DEFAULT);

    //Bind Parameters
    if(!$newUser_query->bind_param("ssss", $fName, $lName, $email, 
				    $pwd_crypt)){
      echo "Bind failed: (" . $newUswer_query->errno . ") " .
	$newUser_query->error;
    }

    //Execute query
    if (!$newUser_query->execute()) {
      echo "Execute failed: (" . $newUser_query->errno . ") " . 
	$newUser_query->error;
    } else {
      echo("0");
    }

    $newUser_query->close();

  } else {  // Row is not unique. 
    echo "1";
  }
  $uniqueUser_query->close();
 
}



newUser ($fName, $lName, $email, $pwordplain, $mysql_handle);


mysqli_close($mysql_handle);

?>
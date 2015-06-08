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
 ** pageTop - this function produces the
 ** html at the top of the page 
 ****************************************/
function pageTop(){
  echo "<DOCType HTML>";
  echo "<head>";
  echo "<link rel =\"stylesheet\" type=\"text/css\" href=\"style.css\">";
  echo "<script src = \"editflt.js\"></script>";
  echo "<script src = \"deleteflt.js\"></script>";
  echo "</head>";
  echo "<body>";

  echo '<!-- Code for the top banner -->';
  echo '<div id="topsplash"><h1>Pilot Logbook </h1>';
  echo '<h3>Your flight tracking solution</h3>';


  echo '<form action = "welcome.php" method = "post">';
  echo '<input type= "submit" value = "LOGOUT">';
  echo '</form></div>';
  

}

/****************************************
 ** displayEventList - this function 
 ** displays all active events with a   
 ** radio button, and a text box for a 
 ** new event. 
 ****************************************/
function displayEventList($sql_handle){  
       
  //Query for all of the possible events
  if($result = $sql_handle->query('SELECT * FROM Event')){
    echo '<form action ="userpage.php" method ="post" >';
    echo 'Select the check boxes below to remove events ';
    echo 'from the event list.<br> <b><em><p style="color:red">';
    echo 'WARNING: Removing ';
    echo 'events from the event list will DELETE ALL RECORDS';
    echo ' of this event.</p></em></b>'; 
    echo '<p style = "color:red">';
    //hidden input to pass the editEventList action
    echo '<input type ="hidden" name ="editEventList"> ';

    //create an input line for each event. 
    while ($object = $result->fetch_object()){
      echo $object->name; 
      echo '<input type = "checkbox" name ="eventList[';
      echo $object->id.']"><br>';
    }
  } else{
    echo "Query failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }
  echo '</p><br><br>or you can ADD a new event (1 at a time) typing the';
  echo ' name here:';
  echo '<input type ="text" name = "eventList[add]"><br>';
  echo '<input type ="submit" value = "Add/Update Events List">';
  echo '</form><form action = "userpage.php" method = "post">';
  echo '<input type= "submit" value = "Cancel and return to previous page">';
  echo '</form>';
  $result->close();
 

}




//EXECUTION BEGINS HERE
//First, check the pilot id session
if (!isset($_SESSION["pilot_id"])){
    header("refresh:5, url = welcome.php");
    echo "Error- undetected login- redirecting to logon page";
}

  
pageTop();
displayEventList($mysql_handle);

?>
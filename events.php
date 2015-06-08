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
 ** getAcftId - this function queries DB
 ** with the tail number, and returns that 
 ** acft id
 ****************************************/
function getAcftId($sql_handle, $param){  
  
  //Prepare query
  if(!($stmt = $sql_handle->prepare('SElECT id FROM Aircraft
                                      WHERE tail_number=? '))){
    echo "Prepare failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }
  //Bind Parameters
  if(!$stmt->bind_param("s", $param)){
    echo "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  //Execute query
  if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
  }

  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  
  $stmt->close();
  return $row["id"];
}

/****************************************
 ** displayFlightEvents - this function 
 ** displays flight info and builds a  
 ** table for entering events
 ****************************************/
function displayFlightEvents($sql_handle, $param){  
    
  //Prepare query
  if(!($stmt1 = $sql_handle->prepare('SELECT 
                                  f.id, dep_date, dep_time, arr_date, arr_time,
                                  tail_number 
                                  FROM Flight f
                                  INNER JOIN Aircraft a
                                  ON a.ID = f.acft_id
                                  WHERE f.id = ? '))){
    echo "Prepare failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }
  //Bind Parameters
  if(!$stmt1->bind_param("s", $param)){
    echo "Bind failed: (" . $stmt1->errno . ") " . $stmt1->error;
  }
  //Execute query
  if (!$stmt1->execute()) {
    echo "Execute failed: (" . $stmt1->errno . ") " . $stmt1->error;
  }

  $result = $stmt1->get_result();
  
  echo '<div id = "myEventEdit">'; 
  echo '<table><tr><th>Departure Date</th><th>Departure Time</th>';
  echo '<th>Arrival Date</th><th>Arrival Time</th><th>Aircraft ID</th></tr>';
  $row = $result->fetch_assoc();
  echo '<tr>';
 
  //display the flight data for the flight in question
  foreach ($row as $key=>$value){
    if ($key != "id"){
      echo "<td>".$value."</td>";
    }
  }
  echo '</tr></table>';
  $stmt1->close();  
   
  //Query for all of the possible events
  if($result = $sql_handle->query('SELECT * FROM Event')){
    echo '<form action ="userpage.php" method ="post" >';
    //hidden input to record the flight we are adding events to
    echo '<input type ="hidden" name ="editEvents" ';
    echo 'value ="'.$param.'">'; //pass the flight whose events are edited

    //create an input line for each event. 
    while ($object = $result->fetch_object()){
      echo $object->name; 
      echo '<input type = "number" min="0" value ="1" name ="event[';
      echo $object->id.']"><br>';
    }
  } else{
    echo "Query failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }

  echo '<input type ="submit" value = "Add or Update Events for this';
  echo ' Flight"></form>';

  echo '<form action = "userpage.php" method = "post">';
  echo '<input type= "submit" value = "Cancel and return to previous page">';
  echo '</form>';
  $result->close();
 

}


/****************************************
 ** getMyAcft - this function queries DB
 ** with the pilot ID, returns all flights 
 ** as an object.
 ****************************************/
function getMyAcft($sql_handle, $pilot_id){  

  //Prepare query
  if(!($stmt1 = $sql_handle->prepare('SELECT *
                                  FROM Aircraft
                                  WHERE pilot_id = ? '))){
    echo "Prepare failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }
  //Bind Parameters
  if(!$stmt1->bind_param("s", $pilot_id)){
    echo "Bind failed: (" . $stmt1->errno . ") " . $stmt1->error;
  }
  //Execute query
  if (!$stmt1->execute()) {
    echo "Execute failed: (" . $stmt1->errno . ") " . $stmt1->error;
  }

  $result = $stmt1->get_result();
  
  $aircraft = array();
  
  while ($row=$result->fetch_assoc()){
    array_push($aircraft, $row);
  }
  
  $stmt1->close();
  return $aircraft;
}
  



//EXECUTION BEGINS HERE
//First, check the pilot id session
if (!isset($_SESSION["pilot_id"])){
    header("refresh:5, url = welcome.php");
    echo "Error- undetected login- redirecting to logon page";
}

  
pageTop();
displayFlightEvents($mysql_handle, $_POST["editEvents"]);

?>
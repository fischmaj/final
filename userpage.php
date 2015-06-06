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


/****************************************
 ** getPilotID - this function queries DB
 ** with the passed in email, returns pilot 
 ** ID, to ease future queries. 
 ****************************************/
function getPilotID($sql_handle, $email){  
  //Prepare query  
  if(!($stmt1 = $sql_handle->prepare('SELECT ID
                                  FROM Pilot
                                  WHERE Email = ? '))){
    echo "Prepare failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }
  //Bind Parameters
  if(!$stmt1->bind_param("s", $email)){
    echo "Bind failed: (" . $stmt1->errno . ") " . $stmt1->error;
  }
  //Execute query
  if (!$stmt1->execute()) {
    echo "Execute failed: (" . $stmt1->errno . ") " . $stmt1->error;
  }

  $result = $stmt1->get_result(); 
  $pilot_id = $result->fetch_assoc(); 
  echo "PILOT ID Type =".gettype($pilot_id['ID'])."<br>"; 
  return $pilot_id['ID'];
}




/****************************************
 ** displayFlights - this function queries DB
 ** with the pilot ID, returns all flights 
 ** and displays them in a table
 ****************************************/
function displayFlights($sql_handle, $pilot_id){  

  echo gettype($pilot_id);

  //Prepare query
  if(!($stmt1 = $sql_handle->prepare('SELECT 
                                  dep_date, dep_time, arr_date, arr_time, tail_number 
                                  FROM Flight f
                                  INNER JOIN Aircraft a
                                  ON a.ID = f.acft_id
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
  
  echo ' <form action = "http://web.engr.oregonstate.edu/';
  echo '~fischmaj/final/userpage.php"  method = "post">';
  echo '<table><tr><th>Departure Date</th><th>Departure Time</th><th>Arrival Date</th>';
  echo '<th>Arrival Time</th><th>Aircraft ID</th><tr>';
  ;

  while ($row = $result->fetch_assoc()){
    echo '<tr>';
    foreach ($row as $key=>$value){
      echo "<td>".$value."</td>";
    }
    echo '</tr>';
  }
  echo '</table></form>';
  $stmt1->close();

}

//debug line
unset($_SESSION["pilot_id"]);

//EXECUTION BEGINS HERE
//First, get the pilot id
if (!isset($_SESSION["pilot_id"])){
  echo "in if";
  $_SESSION["pilot_id"] = getPilotID($mysql_handle, $email);
  var_dump($_SESSION);
}

$pilot_id = $_SESSION["pilot_id"];
echo $pilot_id;
echo gettype($pilot_id);
displayFlights($mysql_handle, $pilot_id);

?>

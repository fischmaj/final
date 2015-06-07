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
  echo '</div>';

}


/****************************************
 ** getAcftId - this function queries DB
 ** with the tail number, and returns that 
 ** acft id
 ****************************************/
function getAcftId($sql_handle, $param){  
  
  //Prepare query
  if(!($stmt = $sql_handle->prepare('SElECT ID FROM Aircraft
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
  return $row["ID"];
}

/****************************************
 ** displayFlight - this function displays
 ** the info of the flight to be edited 
 ** and builds the form. 
 ****************************************/
function displayFlight($sql_handle){  

  //Prepare query
  if(!($stmt1 = $sql_handle->prepare('SELECT 
                                  f.ID, dep_date, dep_time, arr_date, arr_time,
                                  tail_number 
                                  FROM Flight f
                                  INNER JOIN Aircraft a
                                  ON a.ID = f.acft_id
                                  WHERE f.id = ? '))){
    echo "Prepare failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }
  //Bind Parameters
  if(!$stmt1->bind_param("s", $_POST["edit"])){
    echo "Bind failed: (" . $stmt1->errno . ") " . $stmt1->error;
  }
  //Execute query
  if (!$stmt1->execute()) {
    echo "Execute failed: (" . $stmt1->errno . ") " . $stmt1->error;
  }

  $result = $stmt1->get_result();
  
  echo '<div id = "myFlights">'; 
  echo '<table><tr><th>Departure Date</th><th>Departure Time</th>';
  echo '<th>Arrival Date</th><th>Arrival Time</th><th>Aircraft ID</th></tr>';
  

  while ($row = $result->fetch_assoc()){
    echo '<tr>';

    foreach ($row as $key=>$value){
      if ($key != "ID"){
	echo "<td>".$value."</td>";
      }
     }
    echo '</tr>';  
  
    //2nd row contains the editable form
    echo '<form action = "userpage.php" method ="post" >';
    echo '<input type ="hidden" name ="editflt" value = "'.$row['ID'].'">'; 
    echo '<tr>';


    foreach ($row as $key=>$value){
     
      if ($key != "ID" && $key != "tail_number"){
      	//adding inputs to capture data from a row for editing
      	echo '<td><input type="text" name ="'.$key.'"';
      	echo' value ="'.$value.'" \></td>';
      } elseif ($key == "tail_number") {

      	$aircraft = getMyAcft($sql_handle, $_SESSION["pilot_id"]);
        echo '<td><select name ="acft_id">';
        foreach ($aircraft as $acft_row){
          echo '<option value ="'. $acft_row["id"] .'" selected>';
          echo $acft_row["tail_number"].'</option>';
      	}

         echo '</select></td>';
      }
    }
   
    echo '</tr>';
  }
  
  echo '</table></div>';
  echo '<input type = "submit" value = "update" ></form><br>';

  echo 'NOTE: Only your previously stored aircraft are options here.';
  echo 'To apply a different aircraft to this flight, they must be added';
  echo ' first.<br>Click <a href = "addaircraft.php">';
  echo 'here</a> to go to the ADD aircraft form page.';
  $stmt1->close();

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
displayFlight($mysql_handle);

?>
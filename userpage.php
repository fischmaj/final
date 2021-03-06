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
  return $pilot_id['ID'];
}




/****************************************
 ** displayFlights - this function queries DB
 ** with the pilot ID, returns all flights 
 ** and displays them in a table
 ****************************************/
function displayFlights($sql_handle, $pilot_id){  

  //Prepare query
  if(!($stmt1 = $sql_handle->prepare('SELECT 
                                  f.id, dep_date, dep_time, arr_date, arr_time,
                                  tail_number 
                                  FROM Flight f
                                  INNER JOIN Aircraft a
                                  ON a.ID = f.acft_id
                                  WHERE f.pilot_id = ? '))){
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
  
  echo '<div id = "myFlights">';
  echo '<h2>My Flights</h2>';
  echo '<table><tr><th/><th/><th></th><th>Departure Date</th>';
  echo '<th>Departure Time</th>';
  echo '<th>Arrival Date</th><th>Arrival Time</th><th>Tail #</th></tr>';
  

  while ($row = $result->fetch_assoc()){
    echo '<tr>';
    //building an edit and delete button for each flight. 
    $editbutton = '<form action ="http://web.engr.oregonstate.edu/';
    $editbutton = $editbutton . '~fischmaj/final/fltedit.php" ';
    $editbutton = $editbutton . 'method="post" >';
    $editbutton = $editbutton . '<button type="submit" name ="editflt" ';
    $editbutton = $editbutton . 'value ="' . $row['id'] . '" />Edit';
    $editbutton = $editbutton . "</button></form>";
 
    //building a delete button for each flight. 
    $deletebutton = '<form action ="http://web.engr.oregonstate.edu/';
    $deletebutton = $deletebutton . '~fischmaj/final/userpage.php" ';
    $deletebutton = $deletebutton . 'method="post" >';
    $deletebutton = $deletebutton . '<button type="submit" name="deleteflt" ';
    $deletebutton = $deletebutton . 'value ="' . $row['id'] . '" />Delete';
    $deletebutton = $deletebutton . "</button></form>";
 
    //building an add Events button for each flight. 
    $eventbutton = '<form action ="http://web.engr.oregonstate.edu/';
    $eventbutton = $eventbutton . '~fischmaj/final/events.php" ';
    $eventbutton = $eventbutton . 'method="post" >';
    $eventbutton = $eventbutton . '<button type="submit" name="editEvents" ';
    $eventbutton = $eventbutton . 'value ="' . $row['id'];
    $eventbutton = $eventbutton . '" />Add/Edit Events';
    $eventbutton = $eventbutton . "</button></form>";


    echo "<td>".$editbutton."</td><td>".$deletebutton."</td><td>";
    echo $eventbutton.'</td>';
    foreach ($row as $key=>$value){
      if ($key != "id"){
	echo "<td>".$value;
       } 
    }
    echo '</tr>';
  }
  echo '</table>';
  echo '<form action = "addflt.php" method = "post">';
  echo '<input type ="submit" value = "Add A Flight"></form></div>';

  $stmt1->close();

}

/****************************************
 ** displayMyAcft - this function queries DB
 ** with the pilot ID, returns all flights 
 ** and displays them in a table
 ****************************************/
function displayMyAcft($sql_handle, $pilot_id){  

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
  
  echo '<div id="myAcft">';
  echo'<h2>My Aircraft</h2>';
  echo '<table><tr><th/><th/><th>Make</th><th>Model</th>';
  echo '<th>Year</th><th>Tail # </th><th>Engines</th>';
  echo '<th>Complex</th></tr>';

  while ($row = $result->fetch_assoc()){
    echo '<tr>';

    //building an edit button for each acft. 
    $editbutton = '<form action ="http://web.engr.oregonstate.edu/';
    $editbutton = $editbutton . '~fischmaj/final/acftedit.php" ';
    $editbutton = $editbutton . 'method="post" >';
    $editbutton = $editbutton . '<button type="submit" name ="editacft" ';
    $editbutton = $editbutton . 'value ="' . $row['id'] . '" />Edit';
    $editbutton = $editbutton . "</button></form>";

    //building a delete button for each acft. 
    $deletebutton = '<form action ="http://web.engr.oregonstate.edu/';
    $deletebutton = $deletebutton . '~fischmaj/final/userpage.php" ';
    $deletebutton = $deletebutton . 'method="post" >';
    $deletebutton = $deletebutton . '<button type="submit" name="deleteacft" ';
    $deletebutton = $deletebutton . 'value ="' . $row['id'] . '" />Delete';
    $deletebutton = $deletebutton . "</button></form>";


    echo "<td>".$editbutton."</td><td>".$deletebutton."</td>";
    foreach ($row as $key=>$value){
      if ($key != "id" && $key !="pilot_id"){
        if ($key == "complex"){
	  echo "<td>";
          echo $value ? 'true' : 'false';
	} else{
	  echo'<td>';
	  echo $value ? $value: 'NO Entry';
	}
      }
    }
    echo '</tr>';
  }
  echo '</table></form>';

  echo '<form action = "addAircraft.php" method = "post">';
  echo '<input type ="submit" value = "Add An Aircraft"></form></div>';
  $stmt1->close();

}

/****************************************
 ** displayMyEvents - this function queries DB
 ** with the pilot ID, returns all of the  
 ** events that pilot has accomplished
 ****************************************/
function displayMyEvents($sql_handle, $pilot_id){  

  //Prepare query
  if(!($stmt1 = $sql_handle->prepare('SELECT fe.id, f.arr_date, e.name,
                                      fe.number
                                  FROM Flight f
                                  INNER JOIN Flight_Event fe
                                  ON fe.fp_id = f.ID
                                  INNER JOIN Event e
                                  ON fe.e_id = e.id
                                  WHERE f.pilot_id = ? '))){
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
  
  echo '<div id="myEvents">';
  echo '<h2> My Events</h2>';
  echo '<p style ="color:red">These are edited under "My Flights."';
  echo '  The button below is for adding or deleting new TYPES';
  echo' of events, rather than individual lines.</p>';
  echo ' <form action = "http://web.engr.oregonstate.edu/';
  echo '~fischmaj/final/userpage.php"  method = "post"';
  echo 'onsubmit = "return false;" >';
  echo '<table><tr><th>Date Accomplished</th><th>Event</th>';
  echo '<th>Number</th></tr>';

  while ($row = $result->fetch_assoc()){
    echo '<tr>';
    foreach ($row as $key=>$value){
      if ($key != "id"){
	echo "<td>".$value;
      }
    }
    echo '</tr>';
  }
  echo '</table></form>';

  echo '<div id = "createEventButton">';
  echo '<form action = "createEvents.php" method = "post">';
  echo '<input type ="submit" value = "Create New Event Types';
  echo '/Edit Existing Event Types"></form>';
  echo '</div></div>';

  $stmt1->close();

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
 ** editFlt - this function queries DB
 ** with a list of parameters for a flt
 ** and updates the flt info of that flt
 ** number. 
 ****************************************/
function editFlt($sql_handle, $params){  
  
  //Prepare query
  if(!($stmt = $sql_handle->prepare('UPDATE Flight
                                     SET
                                     dep_date = ?,
                                     dep_time = ?, 
                                     arr_date = ?, 
                                     arr_time = ?, 
                                     acft_id = ?
                                     WHERE id = ? '))){
    echo "Prepare failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }
  //Bind Parameters
  if(!$stmt->bind_param("sssssi", $params[1], $params[2], $params[3],
			$params[4], $params[5], $params[0])){
    echo "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  //Execute query
  if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  $stmt->close();
}

/****************************************
 ** editEvent - wrapper function that 
 ** runs a new query for each event 
 ** passed by the event.php page
 ****************************************/
function editEvent($sql_handle, $params){  
  $flightId = $params[0];
  $numEvents = $params[1]; //This is the count of the number of events

  //cycle through remaining params- each is a pair of event ID and number
  //and pass each set to the updateEvent function
  for ($i = 1; $i <$numEvents*2; $i+=2){
    $eventId = $params[$i+1];
    $eventNum = $params[$i+2];
    $paramList = array();
    array_push($paramList, $flightId, $eventId, $eventNum);
    updateEvent($sql_handle, $paramList);
  }   
}

/****************************************
 ** updateEvent - internal function 
 ** which runs the queries for the 
 ** editEvent function.  Uses mySQL Insert
 ** on duplicate key UPDATE to work with 
 ** both add and edit. 
 ****************************************/
function updateEvent($sql_handle, $params){  
  //Prepare query
  if(!($stmt = $sql_handle->prepare('INSERT INTO Flight_Event
                                     (fp_id, e_id, number)
                                     VALUES (?, ?, ?)
                                     ON DUPLICATE KEY UPDATE
                                     fp_id =?, e_id = ?, number=? '))){
    echo "Prepare failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }
  //Bind Parameters
  if(!$stmt->bind_param("iiiiii", $params[0], $params[1], $params[2],
			$params[0], $params[1], $params[2])){
    echo "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  //Execute query
  if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  $stmt->close();
}



/****************************************
 ** editEventList - wrapper function that 
 ** runs a new query for each event 
 ** passed by the createEvent.php page
 ****************************************/
function editEventList($sql_handle, $params){  
  $numEvents = $params[0]; //This is the count of the number of events

  //cycle through remaining params- each is a pair of event ID and number
  //and pass each set to the updateEvent function
  for ($i = 1; $i <$numEvents*2; $i+=2){
    if($params[$i]== "add" && $params[$i+1] != ""){
      addEvent($sql_handle, $params[$i+1]);
    } else {
      deleteEvent($sql_handle, $params[$i]);
    }
  }

}

/****************************************
 ** addEvent - internal function 
 ** which runs the queries for the 
 ** editEventList function.  Uses mySQL Insert
 ** on duplicate key UPDATE to work with 
 ** both add and edit. 
 ****************************************/
function addEvent($sql_handle, $param){  
  //Prepare query
  if(!($stmt = $sql_handle->prepare('INSERT INTO Event
                                     (name)
                                     VALUES (?)
                                     ON DUPLICATE KEY UPDATE name=name 
                                      '))){
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
  $stmt->close();
}

/****************************************
 ** deleteEvent - internal function 
 ** which runs the queries for the 
 ** editEventList function.  Uses mySQL Insert
 ** on duplicate key UPDATE to work with 
 ** both add and edit. 
 ****************************************/
function deleteEvent($sql_handle, $param){  
  //Prepare query
  if(!($stmt = $sql_handle->prepare('DELETE FROM Event
                                    WHERE id= ? '))){
    echo "Prepare failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }
  //Bind Parameters
  if(!$stmt->bind_param("i", $param)){
    echo "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  //Execute query
  if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  $stmt->close();
}




/****************************************
 ** editAcft - this function queries DB
 ** with a list of parameters for an 
 ** an aircraft to change it's info. These
 ** changes will cascade to flights and 
 ** events table.  NOTE: pilot_id cannot
 ** be updated, since this represents the
 ** owner of the aircraft/user of the site.
 ** Pilot's should not be swapping planes 
 ** on this site 
 ****************************************/
function editAcft($sql_handle, $params){  

  //Prepare query
  if(!($stmt = $sql_handle->prepare('UPDATE Aircraft
                                     SET
                                     make = ?,
                                     model = ?, 
                                     year = ?, 
                                     tail_number = ?, 
                                     engines = ?,
                                     complex = ?
                                     WHERE id = ? '))){
    echo "Prepare failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }
  //Bind Parameters
  if(!$stmt->bind_param("ssssiii", $params[1], $params[2], $params[3],
			$params[4], $params[5], $params[6],$params[0])){
    echo "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  //Execute query
  if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  $stmt->close();
}


/****************************************
 ** addFlt - this function queries DB
 ** with a list of parameters for a flt
 ** and updates the flt info of that flt
 ** number. 
 ****************************************/
function addFlt($sql_handle, $params){  

  //Prepare query
  if(!($stmt = $sql_handle->prepare('INSERT INTO  Flight
                                     (pilot_id, dep_date, dep_time, arr_date,
                                     arr_time, acft_id)
                                     VALUES 
                                     (?,?,?,?,?,?)'))){
    echo "Prepare failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }

  //Bind Parameters
  if(!$stmt->bind_param("issssi", $params[0], $params[1], $params[2],
			$params[3], $params[4], $params[5])){
    echo "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  //Execute query
  if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  $stmt->close();
}


/****************************************
 ** addAcft - this function queries DB
 ** with a list of parameters for a flt
 ** and updates the flt info of that flt
 ** number. 
 ****************************************/
function addAcft($sql_handle, $params){  


  //Prepare query
  if(!($stmt = $sql_handle->prepare('INSERT INTO  Aircraft
                                     (make, model, year, tail_number, 
                                     engines, complex, pilot_id)
                                     VALUES 
                                     (?,?,?,?,?,?,?)'))){
    echo "Prepare failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }
  //Bind Parameters
  if(!$stmt->bind_param("sssssii", $params[1], $params[2], $params[3],
			$params[4], $params[5], $params[6], $params[0])){
    echo "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  //Execute query
  if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  $stmt->close();
}


/****************************************
 ** deleteFlight - this function queries DB
 ** to delete an individual flight.  This  
 ** cascades to deleting the associated
 ** events.
 ****************************************/
function deleteFlight($sql_handle, $param){  


  //Prepare query
  if(!($stmt = $sql_handle->prepare('DELETE FROM  Flight
                                    WHERE id = ?'))){
    echo "Prepare failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }
  //Bind Parameters
  if(!$stmt->bind_param("i", $param)){
    echo "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  //Execute query
  if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  $stmt->close();
}


/****************************************
 ** deleteAcft - this function queries DB
 ** to delete an aircraft.  This will 
 ** cascade to deleting the associated
 ** flights and events.
 ****************************************/
function deleteAcft($sql_handle, $param){  


  //Prepare query
  if(!($stmt = $sql_handle->prepare('DELETE FROM  Aircraft
                                    WHERE id = ?'))){
    echo "Prepare failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }
  //Bind Parameters
  if(!$stmt->bind_param("i", $param)){
    echo "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  //Execute query
  if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  $stmt->close();
}

/**********************************************************************
 **EXECUTION BEGINS HERE
 *********************************************************************/

//First, get/set the pilot id
//if we're just coming from the login page, we need $email for query
if (!isset($_SESSION["pilot_id"])){
  if (isset($_POST['email'])){
    $email = $_POST['email']; 
    $_SESSION["pilot_id"] = getPilotID($mysql_handle, $email);
  } else { //session not set, so revert to welcome page
    header("refresh:5, url = welcome.php");
    echo "Error- undetected login- redirecting to logon page";
  }
}


//Everything below executes only on valid login ($_SESSION["pilot_id"] is set)
pageTop();
$pilot_id = $_SESSION["pilot_id"];

if (isset($_POST['editflt'])){
  $params = array();
  array_push($params, $_POST["editflt"], $_POST["dep_date"],
  	     $_POST["dep_time"], $_POST["arr_date"], $_POST["arr_time"],
  	     $_POST["acft_id"]);
  editFlt($mysql_handle, $params);

}

if (isset($_POST['addflt'])){
  $params = array();
  array_push($params, $_SESSION["pilot_id"], $_POST["dep_date"],
  	     $_POST["dep_time"], $_POST["arr_date"], $_POST["arr_time"],
  	     $_POST["acft_id"]);
  addFlt($mysql_handle, $params);

}

if (isset($_POST['addacft'])){
  $params = array();
  array_push($params, $_SESSION["pilot_id"], $_POST["make"],
  	     $_POST["model"], $_POST["year"], $_POST["tail_number"],
  	     $_POST["engines"], $_POST["complex"]);
  addAcft($mysql_handle, $params);

}

if (isset($_POST['editacft'])){
  $params = array();
  array_push($params, $_POST["editacft"], $_POST["make"],
  	     $_POST["model"], $_POST["year"], $_POST["tail_number"],
  	     $_POST["engines"], $_POST["complex"]);
  editAcft($mysql_handle, $params);

}

if (isset($_POST['deleteacft'])){
  $param = $_POST['deleteacft']; 
  deleteAcft($mysql_handle, $param);
}

if (isset($_POST['deleteflt'])){
  $param = $_POST['deleteflt']; 
  deleteFlight($mysql_handle, $param);
}

if (isset($_POST['editEvents'])){
  $params = array();
  $eventNum = count($_POST['event']);
  $eventArray = $_POST['event'];

  array_push($params, $_POST['editEvents']);
  array_push($params, $eventNum);
 
  foreach ($eventArray as $key=>$value){  
    array_push($params, $key);
    array_push($params, $value);
  }
  editEvent($mysql_handle, $params);
}

if (isset($_POST["editEventList"])){
  $params = array();
  $count = count($_POST["eventList"]);
  $eventListArray = $_POST["eventList"];
  
  array_push($params, $count);
  foreach($eventListArray as $key=>$value){
    array_push($params, $key);
    array_push($params, $value);
  }
  editEventList($mysql_handle, $params);
}


displayMyAcft($mysql_handle, $pilot_id);
displayFlights($mysql_handle, $pilot_id);
displayMyEvents($mysql_handle, $pilot_id);
?>
  


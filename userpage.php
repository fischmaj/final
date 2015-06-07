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
                                  f.ID, dep_date, dep_time, arr_date, arr_time,
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
  
  echo ' <form action = "http://web.engr.oregonstate.edu/';
  echo '~fischmaj/final/userpage.php"  method = "post"';
  echo 'onsubmit = "return false;" >';
  echo '<table><tr><th/><th/><th>Departure Date</th><th>Departure Time</th>';
  echo '<th>Arrival Date</th><th>Arrival Time</th><th>Aircraft ID</th></tr>';
  

  while ($row = $result->fetch_assoc()){
    echo '<tr>';
    //building an edit and delete button for each flight. 
    $editbutton = "<input type=\"submit\" name = \"edit\" ";
    $editbutton =$editbutton."value =\"Edit\" id=\"".$row['ID'] ."\"";
    $editbutton =$editbutton."onclick = \"fltEdit(".$row['ID'].")\" />";

    $deletebutton = "<input type=\"button\" name = \"delete\" ";
    $deletebutton = $deletebutton."value =\"Delete\" id=\" ".$row['ID'] ."\"";
    $deletebutton = $deletebutton."onclick =\"fltDelete()\" \>";

    echo "<td>".$editbutton."</td><td>".$deletebutton."</td>";
    foreach ($row as $key=>$value){
      if ($key != "ID"){
	echo "<td>".$value;

        //adding hidden inputs to capture data from a row for editing
        echo "<input type=\"hidden\" name =\"".$key."\"";
        echo "value =\"".$value."\" \></td>";
      }
        echo "<input type=\"hidden\" name =\"fltid\"";
        echo "value =\"".intval($row["ID"])."\" \></td>";
    }
    echo '</tr>';
  }
  echo '</table></form>';
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
  echo ' <form action = "http://web.engr.oregonstate.edu/';
  echo '~fischmaj/final/userpage.php"  method = "post"';
  echo 'onsubmit = "return false;" >';
  echo '<table><tr><th/><th/><th>Make</th><th>Model</th>';
  echo '<th>Year</th><th>Tail Number</th><th>Engines</th>';
  echo '<th>Engines</th><th>Complex</th></tr>';

  while ($row = $result->fetch_assoc()){
    echo '<tr>';
    //building an edit and delete button for each flight. 
    $editbutton = "<input type=\"submit\" name = \"edit\" ";
    $editbutton =$editbutton."value =\"Edit\" id=\"".$row['id'] ."\"";
    $editbutton =$editbutton."onclick = \"fltEdit(".$row['id'].")\" />";

    $deletebutton = "<input type=\"button\" name = \"delete\" ";
    $deletebutton = $deletebutton."value =\"Delete\" id=\" ".$row['id'] ."\"";
    $deletebutton = $deletebutton."onclick =\"fltDelete()\" \>";

    echo "<td>".$editbutton."</td><td>".$deletebutton."</td>";
    foreach ($row as $key=>$value){
      if ($key != "id"){
	echo "<td>".$value;

        //adding hidden inputs to capture data from a row for editing
        echo "<input type=\"hidden\" name =\"".$key."\"";
        echo "value =\"".$value."\" \></td>";
      }
        echo "<input type=\"hidden\" name =\"fltid\"";
        echo "value =\"".intval($row["id"])."\" \></td>";
    }
    echo '</tr>';
  }
  echo '</table></form></div>';
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
  
  echo '<div id="myAcft">';
  echo ' <form action = "http://web.engr.oregonstate.edu/';
  echo '~fischmaj/final/userpage.php"  method = "post"';
  echo 'onsubmit = "return false;" >';
  echo '<table><tr><th/><th/><th>Date Accomplished</th><th>Event</th>';
  echo '<th>Number</th></tr>';

  while ($row = $result->fetch_assoc()){
    echo '<tr>';
    //building an edit and delete button for each flight. 
    $editbutton = "<input type=\"submit\" name = \"edit\" ";
    $editbutton =$editbutton."value =\"Edit\" id=\"".$row['id'] ."\"";
    $editbutton =$editbutton."onclick = \"fltEdit(".$row['id'].")\" />";

    $deletebutton = "<input type=\"button\" name = \"delete\" ";
    $deletebutton = $deletebutton."value =\"Delete\" id=\" ".$row['id'] ."\"";
    $deletebutton = $deletebutton."onclick =\"fltDelete()\" \>";

    echo "<td>".$editbutton."</td><td>".$deletebutton."</td>";
    foreach ($row as $key=>$value){
      if ($key != "id"){
	echo "<td>".$value;

        //adding hidden inputs to capture data from a row for editing
        echo "<input type=\"hidden\" name =\"".$key."\"";
        echo "value =\"".$value."\" \></td>";
      }
        echo "<input type=\"hidden\" name =\"fltid\"";
        echo "value =\"".intval($row["id"])."\" \></td>";
    }
    echo '</tr>';
  }
  echo '</table></form></div>';
  $stmt1->close();

}

/****************************************
 ** editFlights - this function queries DB
 ** with the flight ID, and edits that flight
 ** Since tail number is provided but acft id
 ** is required, it calls getAcftId
 ****************************************/
function editFlights($sql_handle, $params){  
  
  $acft_id = getAcftId($sql_handle, $params[4]); 

  //Prepare query
  if(!($stmt1 = $sql_handle->prepare('UPDATE Flight 
                                      SET
                                      dep_date = ?,
                                      dep_time = ?,
                                      arr_date = ?, 
                                      arr_time = ?,
                                      acft_id = ? 
                                      WHERE ID = ? '))){
    echo "Prepare failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }
  //Bind Parameters
  if(!$stmt1->bind_param("ssssss", $params[0], $params[1], $params[2],
			 $params[3], $acft_id, $params[5])){
    echo "Bind failed: (" . $stmt1->errno . ") " . $stmt1->error;
  }
  //Execute query
  if (!$stmt1->execute()) {
    echo "Execute failed: (" . $stmt1->errno . ") " . $stmt1->error;
  }

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










//EXECUTION BEGINS HERE
//First, get the pilot id

//if we're just coming from the login page, we need $email for query
if (!isset($_SESSION["pilotid"])){
  if(isset($_POST['email'])){
    $email = $_POST['email']; 
    $_SESSION["pilot_id"] = getPilotID($mysql_handle, $email);
  } else {
    header("refresh:5, url = welcome.html");
    echo "Error- undetected login";
  }
}

pageTop();

$pilot_id = $_SESSION["pilot_id"];

if (isset($_POST["edit"])){
  $params = [ $_POST["dep_date"], $_POST["dep_time"], $_POST["arr_date"], 
	      $_POST["arr_time"], $_POST["tail_number"],$_POST["fltid"]];

  editFlights($mysql_handle, $params);
}
displayMyAcft($mysql_handle, $pilot_id);
displayFlights($mysql_handle, $pilot_id);
displayMyEvents($mysql_handle, $pilot_id);
?>
  


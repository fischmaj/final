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
 ** displayEditAcft - this function displays
 ** the info of the flight to be edited 
 ** and builds the form. 
 ****************************************/
function displayEditAcft($sql_handle){  

  //Prepare query
  if(!($stmt1 = $sql_handle->prepare('SELECT *
                                  FROM Aircraft
                                  WHERE id = ? '))){
    echo "Prepare failed: (" . $sql_handle->errno . ") " . $sql_handle->error;
  }

  //Bind Parameters
  if(!$stmt1->bind_param("s", $_POST["editacft"])){
    echo "Bind failed: (" . $stmt1->errno . ") " . $stmt1->error;
  }
  //Execute query
  if (!$stmt1->execute()) {
    echo "Execute failed: (" . $stmt1->errno . ") " . $stmt1->error;
  }

  $result = $stmt1->get_result();
  
  echo '<div id = "myFlights">'; 
  echo '<table><tr><th>Make</th><th>Model</th><th>Year</td>';
  echo '<th>Tail #</th><th>Engines</th><th>Complex</th></tr>';
 
  $row = $result->fetch_assoc();
  echo '<tr>';

  foreach ($row as $key=>$value){
    if ($key != "id" && $key !="pilot_id"){
      if ($key == "complex"){
	echo "<td>";
	echo $value ? 'true' : 'false';
      } else {
	echo'<td>';
	echo $value ? $value: 'NO Entry';
      }
    }
  }

    echo '</tr>';  
  
 
  //2nd row contains the editable form
  echo '<form action = "userpage.php" method ="post" >';
  echo '<tr><input type ="hidden" name ="editacft"';
  echo ' value = "'.$row['id'].'"></td>';
  echo '<td><input type ="text" name ="make" value = "Cessna"/></td>';
  echo '<td><input type ="text" name ="model" value = "C172"/></td>';
  echo '<td><input type ="text" name ="year" value = "1967"/></td>';
  echo '<td><input type ="text" name ="tail_number" value = "N5678"/></td>';
  echo '<td><input type ="number" name ="engines" min ="0" max ="10"';
  echo ' value = "1"/></td>';
  
  echo '<td><select name ="complex">';
  echo '<option value ="0" selected>False</option>';
  echo '<option value =1" >True</option>';
  echo '</select></td> </tr>';
  
  echo '</table></div>';
  echo '<input type = "submit" value = "Update" ></form><br>';
  echo '<form action = "userpage.php" method = "post">';
  echo '<input type = "submit" value = "Cancel"></form>';

  echo 'NOTE: "Model" and "Year" are required. "Engines" is required and ';
  echo 'must be 0 (glider aircraft) or greater.';
  $stmt1->close();
}


  


//EXECUTION BEGINS HERE
//First, check the pilot id session
if (!isset($_SESSION["pilot_id"])){
    header("refresh:5, url = welcome.php");
    echo "Error- undetected login- redirecting to logon page";
}

  
pageTop();
displayEditAcft($mysql_handle);

?>
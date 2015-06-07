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
 ** displayAcft - this function displays
 ** the info of the aircraft to be edited 
 ** and builds the form. 
 ****************************************/
function displayAcft(){  
 
  
  echo '<div id = "myAcft">'; 
  echo '<table><tr><th>Make</th><th>Model</th><th>Year</th>';
  echo '<th>Tail #</th><th>Engines</th><th>Complex</th></tr>';
 
  //2nd row contains the editable form
  echo '<form action = "userpage.php" method ="post" >';
  echo '<tr><input type ="hidden" name ="addacft" value = "null"></td>';   
  echo '<td><input type ="text" name ="Make" value = "Cessna"/></td>';
  echo '<td><input type ="text" name ="Model" value = "C172"/></td>';
  echo '<td><input type ="text" name ="Year" value = "1967"/></td>';
  echo '<td><input type ="text" name ="Tail #" value = "N5678"/></td>';
  echo '<td><input type ="number" name ="Engines" min ="0" max ="10"';
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
}



//EXECUTION BEGINS HERE
//First, check the pilot id session
if (!isset($_SESSION["pilot_id"])){
    header("refresh:5, url = welcome.php");
    echo "Error- undetected login- redirecting to logon page";
}

  
pageTop();
displayAcft();

?>
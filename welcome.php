<?php
session_start();
session_unset();
$_SESSION = array();
session_destroy();
?>

<!DOCType HTML>
<head>
<link rel ="stylesheet" type="text/css" href="style.css">
<script src ="logCheck.js"></script>
<script src ="newUserCheck.js"></script>
</head>

<body>

<!-- Code for the top banner -->
<div id="topsplash"><h1>Pilot Logbook </h1>
<h3>Your flight tracking solution</h3>
</div>

<!-- Code for the login div -->
<div id="logsplash">
<div id="logquestion">Already have a login?</div>
<div id ="newuserquestion">  If not, 
  <input type="button" onclick = "newUser()"  value ="Create An Account">
</input> here! </div>
<br>

<div id="logform">
<form id ="login"  onSubmit= "return runForm()" method="post" action="userpage.php">
Email: 
<input type ="text" id="email" name = "email"></input>
Password: 
<input type ="password" name = "pword" id="password">
   <input type ="submit" name ="Login" >
<p id = "error" style = "{color:red}"></p>
</form>
</div>

<br>

</div>
</body>

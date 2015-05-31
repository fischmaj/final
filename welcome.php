<?php

$errcode = $_GET["errcode"];

if ($errcode == 1){  //user's email address not found.
  echo "<p style = \"color: red;\" >The email that you entered does not ";
  echo "match any user in our records. <br> Please check the ";
  echo "email again and resubmit, or click \"Create An Account\" ";
  echo "below to create a new account. </p>";

} elseif ($errcode == 2){ //user's password not correct
  echo "<p style = \"color: red;\">That password is incorrect. Check that ";
  echo "your CAPS LOCK key is not on and try again </p>";
}

$errcode = NULL; 



?>


<!DOCType HTML>
<head>
</head>

<body>
<script>

checkInputs = function() {
  var myForm = document.getElementById("login"); 
  var emailElement = document.getElementById("email");
  var emailAddy = emailElement.value().toLowerCase();
  emailElement.value = emailAddy; 
  return true; 

}

</script>
Already have a login?  
<form id ="login" action = "login.php" method="post">
Email: 
<input type ="text" id="email" name = "email"></input>
Password: 
<input type ="password" name = "pword">
  <input type ="submit" name ="Login" onclick= checkInputs()>
</form>
<br>
If not, <a href="newuser.php"><em><b>Create An Account</b></em></a> here!. 

</body>

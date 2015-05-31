<?php

$errcode = $_GET["errcode"];

if ($errcode == 1){  //user's email address not found.
  echo "<p style = \"color: red;\" >The email that you entered was invalid, ";
  echo "or is already in use. <br/>";
  echo "Check the email address again. If you already have an account";
  echo "you may login <a href=\"welcome.php\">here</a>.";

} elseif ($errcode == 2){ //user's password not correct
  echo "<p style = \"color: red;\">Error detected on first name. <br>";
  echo "The first name field cannot be blank. </p>";

} elseif ($errcode == 3){ //user's password not correct
  echo "<p style = \"color: red;\">Error detected on last name. <br>";
  echo "The last name field cannot be blank. </p>";

} elseif ($errcode == 4){ //user's password not correct
  echo "<p style = \"color: red;\">Error detected on password.. <br>";
  echo "The password must be at least 8 characters.. </p>";
}

$errcode = NULL; 
?>


<!DOCType HTML>
<head>
</head>

<body>
<script language= "JavaScript">

 //This function validates email for proper format using regular expressions
 function validateEmail(email) {
   var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
  return re.test(email);
 }


function checkInputs() {

  alert("checkInputs function is running.");

  var myForm = document.getElementById("userCreate"); 
  var emailElement = document.getElementById("email");
  var emailAddy = emailElement.value;
  
  var FNameElement = document.getElementById("FName");
  var FName = FNameElement.value;
 
  var LNameElement = document.getElementById("LName");
  var LName = LNameElement.value;

  var PasswordElement = document.getElementById("Password");
  var Password = PasswordElement.value; 

  var ErrorElement = document.getElementById("Errors");
  ErrorElement.innerHTML = "";

  var inputsGood = true;
 
  if (!validateEmail(emailAddy) || emailAddy==null || emailAddy==""){
    ErrorElement.innerHTML+= "Detected an invalid email address format.<br>";  
    inputsGood = false;
  } else {
    emailElement.value = emailAddy.toLowerCase(); 
  }

  if ((FName == null)|| (FName =="")){
    ErrorElement.innerHTML+= "First Name cannot be blank.<br>";  
    inputsGood = false;
  }
    
  if ((LName == null)||(LName =="")){
    ErrorElement.innerHTML+= "Last Name cannot be blank.<br>";  
    inputsGood = false;
  }

  if ((Password.length <8)||(Password == null)){
    ErrorElement.innerHTML+= "Password must be at least 8 characters.<br>";  
    inputsGood = false;
  }
 
  console.log(inputsGood); 
   return inputsGood; 
}
</script>

New User Details:   
<form id ="userCreate" onsubmit="return checkInputs();"  action = "newuserback.php" method="post">
Email: 
<input type ="text" id="email" name = "email"></input>
First Name:
<input type ="text" id="FName" name = "FName"></input>
Last Name:
<input type ="text" id="LName" name = "LName"></input>

Password: 
<input type ="Password" name = "Password" id="Password"></input>

<input type ="submit" name ="Create" value ="Create Account"></input>
</form>
<br>

If you already have an account, login 
 <a href="welcome.php"><em><b>here</b></em></a>! 
 <br/> <br/>

 <!-- This paragraph begins blank.  Modified by javascript above if errors
  are detected in the form. -->
 <p id="Errors" style="color:red"></p>
</body>

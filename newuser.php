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


checkInputs = function() {

  alert("checkInputs function is running.");

  return false;
  var myForm = document.getElementById("userCreate"); 
  var emailElement = document.getElementById("email");
  var emailAddy = emailElement.value().toLowerCase();
  emailElement.value = emailAddy; 

  var FNameElement = document.getElementByID("FName");
  var FName = FNameElement.value();
 
  var LNameElement = document.getElementByID("LName");
  var LName = LNameElement.value();

  var PasswordElement = document.getElementByID("Passowrd");
  var Password = PasswordElement.value(); 

  var ErrorElement = document.getElementByID("Errors");

  var inputsGood = true;
 
  if (!validateEmail(emailAddy) || emailAddy==NULL || emailAddy==""){
    ErrorElement.innerHTML+= "Detected an invalid email address format.<br>";  
    global inputsGood = false;
  }

  if ((FName == NULL)|| (FName =="")){
    ErrorElement.innerHTML+= "First Name cannot be blank.<br>";  
    global inputsGood = false;
  }
    
  if ((LName == NULL)||(LName =="")){
    ErrorElement.innerHTML+= "Last Name cannot be blank.<br>";  
    global inputsGood = false;
  }

  if ((Password.length <8)||(Password == NULL)){
    ErrorElement.innerHTML+= "Password must be at least 8 characters.<br>";  
    global inputsGood = false;
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
<input type ="Password" name = "Password"></input>

<input type ="submit" name ="Create" value ="Create Account" onclick="checkInputs()"></input>
</form>
<br>

If you already have an account, login 
 <a href="welcome.php"><em><b>here</b></em></a>! 
 <br/> <br/>

 <!-- This paragraph begins blank.  Modified by javascript above if errors
  are detected in the form. -->
 <p id="Errors" style="color:red"></p>
</body>

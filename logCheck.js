
function validateEmail(email) {
  var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
  return re.test(email);
}



checkInputs = function() {
  console.log ("In checkInputs()");
  var goodInputs = true; 
  var myForm = document.getElementById("login");
  var errorElement = document.getElementById("error");
  errorElement.innerHTML = "";
  var emailElement = document.getElementById("email");

  if (emailElement.value){
    var emailAddy = new String(emailElement.value.toLowerCase());
    emailElement.value = emailAddy;
  } 

  var passwordElement = document.getElementById("password");  
  if (passwordElement.value){
    var pword = passwordElement.value;
  } else {
    var pword = undefined;
  }
  console.log("emailAddy="+emailAddy);
  console.log(validateEmail(emailAddy));
  if (!validateEmail(emailAddy)){
    console.log("error 1");
    errorElement.innerHTML += "Detected an incorrectly formatted E-mail address.<br>";
    goodInputs = false; 
  }

  if ((pword==null)||(pword.length < 1)){
    console.log("error2");
    errorElement.innerHTML += "Password cannot be blank. <br>";
    goodInputs = false;
  }


  return goodInputs; 

}
   

runForm= function(){
   if ( checkInputs() ) { 
     console.log("prepping AJAX");
     var myReq = new XMLHttpRequest();
     var myReq2 = new XMLHttpRequest();

     //openning the first request (checking login status)
     myReq.open("POST", "login.php", true);
     myReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  
     //prepping the form data
     var email = document.getElementById("email").value;
     var pword = document.getElementById("password").value;
     var sendstring = "email="+email+"&pword="+pword;
     myReq.send(sendstring);

     //checking the results
     myReq.onreadystatechange =  function(){
       console.log(this.readyState);
       if (this.readyState ==4 && this.status == 200){

	 var errorcode = this.responseText;
	 var errorElement = document.getElementById("error");

	 if (errorcode == 1){ //bad user name
	   var errorstring  = "That user name was not found.  <br>";
	   errorstring += "Please check the spelling of the entered email,";
	   errorstring +="<br> Or click \"Create An Account\" to register.";
	   errorElement.innerHTML = errorstring;
           return false;

	 } else if (errorcode == 2) {//bad password
	   var errorstring  = "Incorrect password. <br>";
	   errorstring += "(Ensure CAPS Lock is off)<br>";
	   errorElement.innerHTML = errorstring;
           return false;

	 } else if (errorcode == "login successful.") {
           var myForm = document.getElementById("login");
           myForm.onsubmit =function() {return true}; 
           myForm.submit();  
	 }
       }
     }
   }
   return false;
}

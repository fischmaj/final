newUser = function(){

    //If user presses Create Account, blank out login lines and error
    document.getElementById("email").value = ""; 
    document.getElementById("password").value = "";
    document.getElementById("error").innerHTML = "";

    //And create the new account form
    newUserFormCreate();
}



function validateEmail(email) {
  var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
  return re.test(email);
}


GetInputs = function(){

  //get all the elements and values
  var fNameElement = document.getElementById("fName");
  var lNameElement = document.getElementById("lName");
  var emailElement = document.getElementById("newEmail");
  var passwordElement = document.getElementById("newPass");
  var fNameStr = fNameElement.value;
  var lNameStr = lNameElement.value;
  var newEmailStr =emailElement.value;
  var newPassStr = passwordElement.value;
 
  var submitString = "fName="+fNameStr + "&lName=" + lNameStr;
  submitString += "&email="+newEmailStr + "&password="+newPassStr;

  return submitString;
}


HandleRequest = function (){

  //get and blank out the error message
  var errorElement = document.getElementById("error");
  errorElement.innerHTML ="";
  var errorStr = "";

  console.log("In HandleRequest, state=" +this.readyState);
  if (this.readyState == 4 && this.status ==200){

    var errorcode = this.responseText;
    if (errorcode == 1 ){  //user Email address in use already.
      errorStr = "That e-mail already has an associated account! <br>";
      errorStr +="Only 1 account can be associated to each e-mail address!<br>";
      errorStr += "Log in to the account using the login line above."; 
    } else if (errorcode == 0) {
      var myForm = document.getElementById("newUserForm");

      /*
      console.log(myForm);      
      for (node in myForm.childNodes){
	if (node.type="input"){
	  console.log(node+ myForm.childNodes[node]+myForm.childNodes[node].value);
	}
      }
      */

      myForm.onsubmit =function() {return true};
      myForm.submit();
    }
  
    errorElement.innerHTML = errorStr;
  }
}


newUserCheckInputs = function(){

  console.log("In newUserCheckInputs()");
  var inputsGood = true;   
  var errorStr ="";
 
  //get all the elements and values
  var errorElement = document.getElementById("error"); 
  var fNameElement = document.getElementById("fName");
  var lNameElement = document.getElementById("lName");
  var emailElement = document.getElementById("newEmail");
  var passwordElement = document.getElementById("newPass");
  var passReTypeElement = document.getElementById("newPassReType");
  var fNameStr = fNameElement.value;
  var lNameStr = lNameElement.value;
  var newEmailStr =emailElement.value;
  var newPassStr = passwordElement.value;
  var newPassReTypeStr = passReTypeElement.value;
 
  //reset the error list. 
  errorElement.innerHTML = errorStr;
 
  if ((fNameStr==null) || !(fNameStr.length > 0)){
    inpustGood = false; 
    errorStr += "First Name cannot be blank!<br>";
  }
 
  if ((lNameStr == null) || !(lNameStr.length >0)){
    inputsGood = false; 
    errorStr += "Last Name cannot be blank!<br>";
  }

  if ((newEmailStr == null) || !(newEmailStr.length >0)){
    inputsGood = false;
    errorStr += "Email address cannot be blank! <br>";
  } else  if (!validateEmail(newEmailStr)){
    inputsGood= false;
    errorStr+="Invalid email address format detected! <br>";
  }

  if ((newPassStr == null) || !(newPassStr.length >0)){
    inputsGood = false;
    errorStr += "Password cannot be blank! <br>"; 
  } else if (newPassStr != newPassReTypeStr) {
    inputsGood = false; 
    errorStr += "Your password confirmation does not match."; 
  }

  if (!inputsGood){  //there are errors, display them
      errorElement.innerHTML = errorStr;
  }
  return inputsGood;
}




submitUser = function() {
  console.log("In submitUser()");
  if (newUserCheckInputs()){
	var newUserCheckUnique = new XMLHttpRequest();
	newUserCheckUnique.open("POST", "newuser1.php", true); 
        newUserCheckUnique.setRequestHeader("Content-type", 
		       		     "application/x-www-form-urlencoded");

        var submitstring = GetInputs(); 
        console.log("submitstring="+submitstring);
        newUserCheckUnique.onreadystatechange = HandleRequest;

        newUserCheckUnique.send(submitstring); 
    }
  return false;
}
 
newUserFormCreate = function(){
    var formElement = document.createElement("FORM");
    formElement.id = "newUserForm";
    formElement.setAttribute("action","userpage.php");
    formElement.setAttribute("method","post");   
    formElement.onsubmit = "return submitUser()";
   
    var formElements = Array();

    var firstLabel = document.createElement("LABEL"); 
    firstLabel.setAttribute("for","fName"); 
    firstLabel.innerHTML = "First Name:"; 
    formElements[0] = firstLabel; 

    var firstName = document.createElement("INPUT");
    firstName.type ="text";
    firstName.id ="fName"; 
    firstName.name ="fName";
    formElements[1]= firstName; 

   
    var lastLabel = document.createElement("LABEL"); 
    lastLabel.setAttribute("for", "lName"); 
    lastLabel.innerHTML = "Last Name:"; 
    formElements[2]= lastLabel; 


    var lastName = document.createElement("INPUT");
    lastName.type ="text";
    lastName.id ="lName";
    lastName.name = "lName";
    formElements[3] = lastName;

    var emailLabel = document.createElement("LABEL"); 
    emailLabel.setAttribute("for","newEmail"); 
    emailLabel.innerHTML = "Email:"; 
    formElements[4]= emailLabel; 

    var newEmail = document.createElement("INPUT");
    newEmail.type ="text";
    newEmail.id ="newEmail";
    newEmail.name ="Email"; 
    formElements[5] = newEmail; 


    var passwordLabel = document.createElement("LABEL"); 
    passwordLabel.setAttribute("for", "newPass"); 
    passwordLabel.innerHTML = "Password:"; 
    formElements[6]= passwordLabel; 

    var newPass= document.createElement("INPUT");
    newPass.type ="password";
    newPass.id ="newPass"; 
    newPass.name ="Password";
    formElements[7] = newPass; 

    var passwordReTypeLabel = document.createElement("LABEL"); 
    passwordReTypeLabel.setAttribute("for", "newPassReType"); 
    passwordReTypeLabel.innerHTML = "Re-type Password:"; 
    formElements[8]= passwordReTypeLabel; 

    var newPassReType= document.createElement("INPUT");
    newPassReType.type ="password";
    newPassReType.id ="newPassReType"; 
    formElements[9] = newPassReType; 

    var btn = document.createElement("INPUT");
    btn.type ="BUTTON"; 
    btn.id ="REGISTER";
    btn.onclick = submitUser;
    btn.value = "REGISTER"; 
    formElements[10] = btn; 

    //append all the form elements to the form
    for (var i= 0; i <  formElements.length; i++){
     
      formElement.appendChild(formElements[i]);
      var br=document.createElement('br');
      formElement.appendChild(br);
    }

    //append the form to the document body.
    document.body.appendChild(formElement);
}
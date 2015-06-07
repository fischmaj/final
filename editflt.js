fltEdit = function (fltid){
   
    var XHR = new XMLHttpRequest();
    XHR.open("POST", "aircraft.php", true);
    XHR.onreadystatechange = HandleRequest;
    XHR.send();
    

    console.log(fltid);    
    var editbtn = document.getElementById(fltid); //gets the input in that row
    console.log(editbtn);
    var editRow = editbtn.parentNode.parentNode;
    console.log(editRow);

    var depDate = editRow.querySelectorAll('[name = "dep_date"]')[0];
    console.log(depDate);
    depDate.setAttribute("type", "text");

    var depTime = editRow.querySelectorAll('[name = "dep_time"]')[0];
    console.log(depDate);
    depTime.setAttribute("type", "text");

    var arrDate = editRow.querySelectorAll('[name = "arr_date"]')[0];
    console.log(depDate);
    arrDate.setAttribute("type", "text");

    var arrTime = editRow.querySelectorAll('[name = "arr_time"]')[0];
    console.log(depDate);
    arrTime.setAttribute("type", "text");

   
}

HandleRequest = function(){
  if (this.readyState==4 && this.status ==200){
    var settings = this.responseText;
    localeStorage.setItem('settings', settings);
  }
}
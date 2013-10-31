function validateForm()
{
var x=document.forms["search"]["find"].value; //get the query input
if (x==null || x=="" || x.length < 2 || x == "\" \"") // invalid query detectors
  {
  alert("Inaccurate Query input"); //display alert to user
  return false;
  }
  return OnSubmitForm(); //if query is valid trigger the onSubmitForm() function
}


function OnSubmitForm() //determine wheter clustering or aggregation was selected
{
  if(document.search.operation[0].checked == true) //if aggregation
  {
    document.search.action ="search.php"; //set action to search.php
  }
  else
  if(document.search.operation[1].checked == true) //if clustering
  {
    document.search.action ="displayclustering.php"; //set action to displayclustering.php
  }
  return true;
}
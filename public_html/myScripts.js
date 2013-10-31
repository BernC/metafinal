//show/hide div code. Adampted from http://www.randomsnippets.com/2008/02/12/how-to-hide-and-show-your-div/

function toggle(showHideDiv, switchTextDiv, ReplaceText) {
	var ele = document.getElementById(showHideDiv);
	var text = document.getElementById(switchTextDiv);
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = ReplaceText; //replacement text varies depending on situation
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "Hide";
	}
} 

//display clustered results
function display_cluster(Group,Start,End){
var html_code = "  "; //need to be initialised to prevent "undefined" error
//console.log(Group); //DEBUG
//console.log(Start);
//console.log(End);
for(var i = Start; i < End; i++) //cluster size sent as part of input
{
    
    if(clusters[Group][i][0][3]) //if result came from google
    {
        var j = clusters[Group][i][0][3] - 1;
        html_code += "<a href=\""+google.items[j].link+"\"><h3>"+google.items[j].htmlTitle+"</h3></a>";
        html_code += "<p class=\"display_link\">"+google.items[j].displayLink+"</p>";
        html_code += "<p>"+google.items[j].htmlSnippet+"</p>";
    
    }else if(clusters[Group][i][0][4]){ //if result came from bing
        var j = clusters[Group][i][0][4] - 1;
        html_code += "<a href=\""+bing.d.results[j].Url+"\"><h3>"+bing.d.results[j].Title+"</h3></a>";
        html_code += "<p class=\"display_link\">"+bing.d.results[j].DisplayUrl+"</p>";
        html_code += "<p>"+bing.d.results[j].Description+"</p>";
    
    }else{
        var j = clusters[Group][i][0][5] - 1; //if result came from blekko
        html_code += "<a href=\""+blekko.RESULT[j].url+"\"><h3>"+blekko.RESULT[j].url_title+"</h3></a>";
        html_code += "<p class=\"display_link\">"+blekko.RESULT[j].display_url+"</p>";
        html_code += "<p>"+blekko.RESULT[j].snippet+"</p>";
        
    }
}
document.getElementById("results_display").innerHTML = html_code; //rewrite result_display div with html
}

//display aggregated results
function display_aggregated(start,end){
var end = start + end; //check for list length
var html_code = " "; //need to initilise html holder
if(results != null) //check for null data sets
{
	for(var i = start; i < end; i++) //iterate through list
	{
    //function operates by dynamically building html code to subsitiute into a div
    	var rank = i + 1;
    	if(results[i][5]) //if result location bool is true for google
    	{
       	 	var j = results[i][5] - 1; //scores/ranks are offset compared to indices
			console.log(j);
    	    html_code += "<a href=\""+google.items[j].link+"\"><h3>"+google.items[j].htmlTitle+"</h3></a>";
    	   html_code += "<p class=\"display_link\">"+google.items[j].displayLink+"</p>"; 
			html_code += "<p>"+google.items[j].htmlSnippet+"</p>";
        
    
    }else if(results[i][6]){ //if result location bool is true for bing
        var j = results[i][6] - 1;
				console.log(j);
        html_code += "<a href=\""+bing.d.results[j].Url+"\"><h3>"+bing.d.results[j].Title+"</h3></a>";
        html_code += "<p class=\"display_link\">"+bing.d.results[j].DisplayUrl+"</p>";
        html_code += "<p>"+bing.d.results[j].Description+"</p>";
    
    }else{ //otherwise result must have come from blekko
        var j = results[i][7] - 1; 
				console.log(j);
        html_code += "<a href=\""+blekko.RESULT[j].url+"\"><h3>"+blekko.RESULT[j].url_title+"</h3></a>";
        html_code += "<p class=\"display_link\">"+blekko.RESULT[j].display_url+"</p>";
        html_code += "<p>"+blekko.RESULT[j].snippet+"</p>";
        
    }
}
}else{
	html_code ="<h2>No Results Returned</h2>";  //error text for blank result set
	}
document.getElementById('results_display').innerHTML = html_code; //replace the existing code in the result_display code with this code
}

function display_google(variable){
var stop = (variable+1)*10; //size of result set to display
if((typeof google != 'undefined') && (google)) //check for empy result sets
{
if(stop > google.items.length){ //check for end of result set
	stop = google.items.length;
}
var html_code =" ";
for(i = variable*10; i < stop; i++) //loop used to generate inner html
{
        html_code += "<a href=\""+google.items[i].link+"\"><h3>"+google.items[i].htmlTitle+"</h3></a>";
       html_code += "<p class=\"display_link\">"+google.items[i].displayLink+"</p>"; 
		html_code += "<p>"+google.items[i].htmlSnippet+"</p>";
        
 }
}else{
	html_code ="<h2>No Results Returned</h2>"; //error message
}
document.getElementById('results_display').innerHTML = html_code; //rewrite result_display html
}

function display_bing(variable){ //bing display code
if((typeof bing != 'undefined') && (bing)) //check for null data
{
var stop = (variable+1)*10; //set size of result list to be displayed
if(stop > bing.d.results.length){
	stop = bing.d.results.length;
}
var html_code =" ";
for(i = variable*10; i < stop; i++) //generate html code
{
		html_code += "<a href=\""+bing.d.results[i].Url+"\"><h3>"+bing.d.results[i].Title+"</h3></a>";
        html_code += "<p class=\"display_link\">"+bing.d.results[i].DisplayUrl+"</p>";
        html_code += "<p>"+bing.d.results[i].Description+"</p>";
 }
}else{
	 html_code = "<h2> No Results Resturned </h2>"; //error messaging
 }
document.getElementById('results_display').innerHTML = html_code; //rewrite result_display html
}

function display_blekko(variable){ //blekko result display code
	if((typeof blekko != 'undefined') && (blekko)) //check for null data sets
{
var stop = (variable+1)*10; //determining length of result list
if(stop > blekko.RESULT.length){
	stop = blekko.RESULT.length;
}
var html_code =" ";
for(i = variable*10; i < stop; i++) //generate html code
{
		html_code += "<a href=\""+blekko.RESULT[i].url+"\"><h3>"+blekko.RESULT[i].url_title+"</h3></a>";
        html_code += "<p class=\"display_link\">"+blekko.RESULT[i].display_url+"</p>";
        html_code += "<p>"+blekko.RESULT[i].snippet+"</p>";
 }
}else{ html_code = "<h2> No Results Resturned </h2>"; //error messaging
}
document.getElementById('results_display').innerHTML = html_code; //rewrite result_display html
}
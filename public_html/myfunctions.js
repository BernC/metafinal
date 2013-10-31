function toggle(showHideDiv, switchTextDiv) {
	var ele = document.getElementById(showHideDiv);
	var text = document.getElementById(switchTextDiv);
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "show";
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "hide";
	}
} 

function display_aggregated(start,end){
var end = start + end;
var html_code = "<h2>"+end+"</h2>";
var results = <?php echo(json_encode($sorted));?>;
for(var i = start; i < end; i++)
{
    
//document.write(results[i][5]+"    "+results[i][6]+"    "+results[i][7]+"<br>");
    var rank = i + 1;
    if(results[i][5])
    {
        var j = results[i][5] - 1;
        html_code += "<a href=\""+google.items[j].link+"\"><h3>"+google.items[j].htmlTitle+"</h3></a>";
        html_code += "<p>"+google.items[j].htmlSnippet+"</p>";
        html_code += "<p>"+google.items[j].displayLink+"</p>";
    
    }else if(results[i][6]){
        var j = results[i][6] - 1;
        html_code += "<a href=\""+bing.d.results[j].Url+"\"><h3>"+bing.d.results[j].Title+"</h3></a>";
        html_code += "<p>"+bing.d.results[j].DisplayUrl+"</p>";
        html_code += "<p>"+bing.d.results[j].Description+"</p>";
    
    }else{
        var j = results[i][7] - 1;
        html_code += "<a href=\""+blekko.RESULT[j].url+"\"><h3>"+blekko.RESULT[j].url_title+"</h3></a>";
        html_code += "<p>"+blekko.RESULT[j].display_url+"</p>";
        html_code += "<p>"+blekko.RESULT[j].snippet+"</p>";
        
    }
}
document.getElementById('wrapper').innerHTML = html_code;
}

function display_google(variable){
var stop = (variable+1)*10;
if(stop > google.items.length){
	stop = google.items.length;
}
var html_code ="<h2>"+google.items.length+"</h2>";
for(i = variable*10; i < stop; i++)
{
        html_code += "<a href=\""+google.items[i].link+"\"><h3>"+google.items[i].htmlTitle+"</h3></a>";
        html_code += "<p>"+google.items[i].htmlSnippet+"</p>";
        html_code += "<p>"+google.items[i].displayLink+"</p>";
 }
document.getElementById('wrapper2').innerHTML = html_code;
}

function display_bing(variable){
var stop = (variable+1)*10;
if(stop > bing.d.results.length){
	stop = bing.d.results.length;
}
var html_code ="<h2>"+bing.d.results.length+"</h2>";
for(i = variable*10; i < stop; i++)
{
		html_code += "<a href=\""+bing.d.results[i].Url+"\"><h3>"+bing.d.results[i].Title+"</h3></a>";
        html_code += "<p>"+bing.d.results[i].DisplayUrl+"</p>";
        html_code += "<p>"+bing.d.results[i].Description+"</p>";
 }
document.getElementById('wrapper3').innerHTML = html_code;
}

function display_blekko(variable){
var stop = (variable+1)*10;
if(stop > blekko.RESULT.length){
	stop = blekko.RESULT.length;
}
var html_code ="<h2>"+blekko.RESULT.length+"</h2>";
for(i = variable*10; i < stop; i++)
{
		html_code += "<a href=\""+blekko.RESULT[i].url+"\"><h3>"+blekko.RESULT[i].url_title+"</h3></a>";
        html_code += "<p>"+blekko.RESULT[i].display_url+"</p>";
        html_code += "<p>"+blekko.RESULT[i].snippet+"</p>";
 }
document.getElementById('wrapper4').innerHTML = html_code;
}
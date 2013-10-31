<?php
//unused code designed to access next 100 result sets
if(isset($_POST['page_num']))
{
	$result_page = intval($_POST['page_num']);
	//var_dump($result_page);
	$next_page = $result_page + 1;
}
else{
	$next_page = 0;
}

?>
<!DOCTYPE html>
<html>
<head>
<title> Metasearch Engine </title>
<link rel="shortcut icon"
 href="favicon.ico" />
<link href="meta_style.css" rel="stylesheet" type="text/css">
</head>
<body onload="display_aggregated(0,10)">
<script src="myScripts.js"></script>
<script src="searchbox_scripts.js"></script>
<?php
include 'querypreprocess.php'; //processes bolean logic etc for different search engines
include 'aggregation_method.php'; //contains sorting and ranking algorithms
include 'fullresults.php'; //cURL retrieval code
include 'query_expansion.php'; //query expansion code
?>
<div id="mainbox">
<div id="heading">
<div id="logo"></div>

<form name="search" id="search" method="post" onsubmit="return validateForm();" >
 Seach for: <input type="text" size="40" placeholder="Type Your Query" name="find" /> 
 <input type="submit" name="submit" id="submit" value="Search" /><br>
  <input type="radio" name="operation" value="1" checked>Aggregation
 <input type="radio" name="operation" value="2">Clustering
 <input type="hidden" name="searching" value="yes" />
 </form>
</div>
<div id="query_expansion">
<?php
$query = $_POST["find"];


$query_expanded = query_expansion($query); //call to query expansion function
if($query_expanded)
{
//expanded queries are submitted as forms. CSS used to style them as links
echo("<p>Would you like to try other similar searches:"); 
for($u = 0; $u < count($query_expanded); $u++)
{
echo("<form action=\"search.php\" method=\"post\" class=\"query_exp\">");
echo("<input type=\"hidden\" name=\"find\" value=\"".$query_expanded[$u]."\">");
echo("<button type=\"submit\" class=\"query_exp\" >".$query_expanded[$u]."</button>");
echo("</form>");
}
echo("</p>");
}
?>
</div>
<div id="main_page_content">
<div id="results_display"></div>
<?php

//this function is used to search whether an URL already exists in the result array or not
function search($ranks, $string, $counter, &$address) //INPUTS
{
$i = 0;
	while($i != $counter)
	{
		
		if($ranks[$i][1] == $string)
		{
		$address = $i;	
		return FALSE; //False when $string is present in array
		
		}
	$i++;
	}
return TRUE; //true if $string is not in the array
}

//url normalistion function. required because some urls contain a 'query' portion whereas others do not
function return_url($original_url)
{
	$temp = parse_url($original_url);
	if(isset($temp['query'])){
		$normalised_url = $temp['host'].$temp['path'].$temp['query'];
	}else{
		$normalised_url = $temp['host'].$temp['path'];
	}


	return $normalised_url;
}

/*
//DEBUG code designed to calculate reciprocral rank for each entry
function recip_rank($row)
{
	$rank = 0;
	for($i =0; $i < 3; $i++)
	{
		if($row[$i+2] != 0)
		{
			$rank = $rank + (1/(60 + $row[$i+2]));
		}
	}

	return $rank;
}
*/

/***************************************************RETURN 100 CODE BLOCK*************************/

$query_tokens = explode(" ", $query); //explode original query

$googlequery = google_query($query_tokens); //create query suitable for google
$data = google100($googlequery, $next_page); //retrieve google results

$bingquery = bing_query($query_tokens); //create query suitable for bing
$bingdata = bing100($bingquery, $next_page); //retrieve bing results

$blekkoquery = blekko_query($query_tokens); //create query suitable for blekko
$blekkodata = blekko100($blekkoquery, $next_page); //retrieve blekko results


/***************************************************RETURN 100 CODE BLOCK*************************/


/***************************************************Local Debug Block*****************************/
/*
	$google = file_get_contents("Google/civil right movement.txt");
	$bing = file_get_contents("Bing/civil right movement.txt");
	$blekko = file_get_contents("Blekko/civil right movement.txt");


	//$data = json_decode($google,true);
	$blekkodata = json_decode($blekko,true);
	//$bingdata = json_decode($bing,true);
*/
/***************************************************Local Debug Block*****************************/


/***************************************************Return 10 Result Block*************************/
/*
$res = return_Set10("$query");
$data = json_decode($res[0],true);
$blekkodata = json_decode($res[1],true);	
$bingdata = json_decode($res[2],true);	
*/
/***************************************************Return 10 Result Block*************************/



/***************************************************Start of Result array Creation Code*************/
	//	________________________________________________________________________________________________
	// | rank | address |	google_rank | bing_rank | blekko_rank | google_bool | bing_bool | blekko_bool |
	//	------------------------------------------------------------------------------------------------

$ranks = array(); //initialise storage array
$subarray = array(0,"httpaddress",0,0,0,false,false,false); //template for all results entered

$counter = 0; //result counter for google

if(isset($data['items'][0]['link'])){ //check for null result set
	
	//Begin filling array with results from google	
	foreach ($data['items'] AS $element)
	{
		$normalised_url = return_url($element['link']); //create normalised URL
 		$subarray[0] = $counter + 1; //set debug rank
 		$subarray[2] = $counter + 1;	//set google rank
 		$subarray[1] = $normalised_url; //store nomalised array
 		$subarray[5] = $counter+1; //set google boolean to true

 		array_push($ranks,$subarray); //add result to result array
 		$counter = $counter + 1; //increment counter
	}
}else{ $data = NULL;} //set results to null for JavaScript to read during display

if(isset($blekkodata['RESULT'][0]['url'])) //check for null result set
{

//Add the results from Blekko
foreach ($blekkodata['RESULT'] as $blekkoelement) {
	 $normalised_url = return_url($blekkoelement['url']); //normalised URL

	if(search($ranks,$normalised_url,$counter,$duplicate_address)) //check if result is a duplicate using normalised URL
	{
		$counter++; //increment counter
		$subarray[0] = $counter; //set debug rank
 		$subarray[4] = $blekkoelement['c']; //store blekko rank
 		$subarray[1] = $normalised_url; //store normalised url
 		$subarray[2] = 0; //this is because blekko is returning a result that google does not have
 		$subarray[5] = false; 
 		$subarray[6] = false;
 		$subarray[7] = $blekkoelement['c']; //set blekko boolean to true
 		array_push($ranks, $subarray); //add result to collection of results



	}
	else
	{
		$ranks[$duplicate_address][4] = $blekkoelement['c']; //update original result with blekko data 
		$ranks[$duplicate_address][7] = false;

	}
}
}else{
	$blekkodata = NULL; //set results to null for JavaScript to read during display
}

if(isset($bingdata['d']['results'][0]['Url'])) //check for NULL bing data
{

//Add in the results from Bing
$bingcounter = 1; //initialise bing counter
foreach ($bingdata['d']['results'] as $bingelement) {
	$normalised_url = return_url($bingelement['Url']); //normalise URL

	if(search($ranks,$normalised_url,$counter,$duplicate_address)) //check if result has already been found
	{
		$counter++;
		$subarray[0] = $counter;  //set debug rank
 		$subarray[3] = $bingcounter; //set being rank
 		$subarray[1] = $normalised_url;  //set normalised url
 		$subarray[2] = 0; //this is because bing is returning a result that google does not have
 		$subarray[4] = 0; //this is because bing is returning a result that blekko does not have
 		$subarray[5] = false; //working
 		$subarray[6] = $bingcounter; //set bing boolean to true
 		$subarray[7] = false;
 		array_push($ranks, $subarray); //add result to result array
	}
	else
	{
		$ranks[$duplicate_address][3] = $bingcounter;	//add bing data to exisitng result
		$ranks[$duplicate_address][6] = false;	
	}

	$bingcounter++; //increment bing counter
}
}else{ $bingdata = NULL;} //set results to null for JavaScript to read during display

/***************************************************Start of Result array Creation Code*************/

$sorted = myquicksort($ranks); //call to modified quicksort to sort array

//file_put_contents("Results/".$query."txt", json_encode($sorted));

if(count($ranks) < 2){ //check for null result set
$sorted = NULL; 
} 
?>


<script type="text/javascript">
//outputting variables to client
var google =<?php echo(json_encode($data));?>; //google result data
var blekko =<?php echo(json_encode($blekkodata));?>; //blekko result data
var bing =<?php echo(json_encode($bingdata));?>; //bing result data
var count =<?php echo(count($sorted)); ?>; //number of aggregated results
var results = <?php echo(json_encode($sorted));?>; // aggregated results data
</script>

<a id="displayText" class="show_result" href="javascript:toggle('toggleText','displayText','Aggregated');">Aggregated</a>
<div id="toggleText" style="display: none">
<div id="aggreg_list" class="result_toggle">
<script>
if(results) //if there are results to display
{
for(var i = 0; i < count ; i+=10)
{
	var set = (i/10)+1;
	var num_results = 10;	
	var fin = i + 10;
	if((count - fin) < 0)
	{
		num_results = fin - count;
	}
	
	//print result buttons
	document.write("<button onclick=\"display_aggregated\("+i+","+num_results+"\)\" class=\"button\">"+set+"</button>"); 
}
}else{
	document.write("<b>NO RESULTS</b>"); //display error text
}
</script>
</div>
<div id="wrapper"></div>
</div>

<a id="GoogledisplayText" class="show_result" href="javascript:toggle('GoogletoggleText','GoogledisplayText','Google');">Google</a>
<div id="GoogletoggleText" style="display: none">
<div id="google_list" class="result_toggle"><script>
if(google) //if there are results to dislay
{
for(var i = 0; i < (google.items.length/10); i++)
{
    //print result buttons
	document.write("<button onclick=\"display_google\("+i+"\)\" class=\"button\">"+(i+1)+"</button>");
}
}else{
	 document.write("<b>NO RESULTS</b>"); //display error
}

</script></div>
<div id="wrapper2"></div>
</div>


<a id="BingdisplayText" class="show_result" href="javascript:toggle('BingtoggleText','BingdisplayText','Bing');">Bing</a>
<div id="BingtoggleText" style="display: none">
<div id="bing_list" class="result_toggle"><script>
if(bing){ //if there are results to display
{
for(var i = 0; i < (bing.d.results.length/10); i ++)
	//print result buttons
    document.write("<button onclick=\"display_bing\("+i+"\)\" class=\"button\">"+(i+1)+"</button>");
}
}else{
	 document.write("<b>NO RESULTS</b>"); //display error
}
</script></div>
<div id="wrapper3"></div>
</div>


<a id="BlekkodisplayText" class="show_result" href="javascript:toggle('BlekkotoggleText','BlekkodisplayText','Blekko');">Blekko</a>
<div id="BlekkotoggleText" style="display: none">
<div id="blekko_list" class="result_toggle">
<script>
if(blekko) // if there are results to display
{
for(var i = 0; i < (blekko.RESULT.length/10); i++)
{
	//print result buttons
    document.write("<button onclick=\"display_blekko\("+i+"\)\" class=\"button\">"+(i+1)+"</button>");
}
}else{
	document.write("<b>NO RESULTS</b>"); //display error
}
</script></div>
<div id="wrapper4"></div>
</div>
<div id= "footer"><?php include 'footer.php'; ?>
<!-- disabled code to allow more result sets to load
<form id="next_page" action="search.php" method="post">
<?php if(isset($_POST["find"]))
{
echo "<input type=\"hidden\" id=\"page_num\" name=\"page_num\" value=\"";echo($_POST["find"]); echo"\">";
}
if(isset($result_page))
{
echo "<input type=\"hidden\" id=\"page_num\" name=\"page_num\" value="; echo($next_page); echo ">";
}else{
echo "<input type=\"hidden\" id=\"page_num\" name=\"page_num\" value=0>";
}	
?>
<input type="submit" class="button_extra" value="Next Set of Results">
</form> -->
</div> 
</div>
</div>
</body>
</html>


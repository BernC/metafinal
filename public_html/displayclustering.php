<?php
// disabled code to allow access to later result sets
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
<script src="myScripts.js"></script>
<script src="searchbox_scripts.js"></script>
<?php
include 'vectorization.php';
include 'clustering.php';
include 'query_expansion.php';
include 'fullresults.php'; //cURL retrieval code
include 'querypreprocess.php'; //processes bollean logic etc for different search engines
include 'generate_list.php'; //generate standardised input for vectorization algorithms
?>

</head>
<body onLoad="display_cluster(0,0,10)">
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

if($query_expanded) //query expansion dialog only appears if query was validly expanded
{
echo("<p>Would you like to try other similar searches:"); 

for($u = 0; $u < count($query_expanded); $u++) //print out a button for each expanded query
{
echo("<form action=\"display_clustering.php\" method=\"post\" class=\"query_exp\">");
echo("<input type=\"hidden\" name=\"find\" value=\"".$query_expanded[$u]."\">");
echo("<button type=\"submit\" class=\"query_exp\" >".$query_expanded[$u]."</button>");
echo("</form>");
}
echo"</p>";
}
?>
</div>
 <div id="main_page_content">
 <div id="results_display"></div>




<?php 
function array_most_common($input)  
{ 
  $counted = array_count_values($input);  //counts the frequency of each element in an array setting the key to an element and the value to its frequency
  arsort($counted); // sort the array in terms of increasing values
  return(key($counted));  //return first key i.e. most frequent term   
}

function generate_common_term($input) //create an input array for 
{
	$temp = array();
	foreach ($input as $key) {
	
    $temp[] = $key[1];

	}
return array_most_common($temp);
}
/***************************************************RETURN 100 CODE BLOCK*************************/

$query_tokens = explode(" ", $query); //explode original query

$googlequery = google_query($query_tokens); //create query suitable for google
$google = google100($googlequery, $next_page); //retrieve google results

$bingquery = bing_query($query_tokens); //create query suitable for bing
$bing = bing100($bingquery, $next_page); //retrieve bing results

$blekkoquery = blekko_query($query_tokens); //create query suitable for blekko
$blekko = blekko100($blekkoquery, $next_page); //retrieve blekko results


/***************************************************RETURN 100 CODE BLOCK*************************/


/***************************************************Local Debug Block*****************************/
/*	$query_tokens = array("civil","rights","movement");
   	$google = file_get_contents("Google/civil right movement.txt");
   	$bing = file_get_contents("Bing/civil right movement.txt");
    $blekko = file_get_contents("Blekko/civil right movement.txt");


    $google = json_decode($google,true);
    $blekko = json_decode($blekko,true);
    $bing = json_decode($bing,true);
*/
/***************************************************Local Debug Block*****************************/


/***************************************************Return 10 Result Block*************************/
/*
$res = return_Set10("$query");
$google = json_decode($res[0],true);
$blekko = json_decode($res[1],true);    
$bing = json_decode($res[2],true);  
*/
/***************************************************Return 10 Result Block*************************/
//error checking

if(!isset($google['items'][0]['link']))
{
	$google = NULL;
}

if(!isset($blekko['RESULT'][0]['url']))
{
	$blekko = NULL;
}

if(!isset($bing['d']['results'][0]['Url']))
{
	$bing = NULL;
}


if($google || $bing || $blekko) //only start clustering if at least one full result set was returned
{
/************************************** Vectorization ****************************************************/

$term_vector = create_term_vector($blekko, $bing, $google, $query_tokens); //vectorise the results

/************************************** Clustering *******************************************************/

$clusters = cluster_vectors($term_vector, 3, 4 );   // ($weighted_term_vector, $num_clusters_reqd, $num_iterations) call to clustering algorithm

}else{
	$clusters = NULL; //set clusters to null if none were created
}
/************************************** Organise Results For CLient Side Display ************************/


if($clusters) //occurs only if valid clustering occurred
{
	$num_clusters = count($clusters);

	$cluster_names = array();

	for($i = 0; $i < count($clusters); $i++)
	{    
   		$cluster_names[] = generate_common_term($clusters[$i]);
    
	}
}
?>

<script>
var google =<?php echo(json_encode($google));?>  //google results
var blekko =<?php echo(json_encode($blekko));?>; //blekko results
var bing =<?php echo(json_encode($bing));?>; //bing results
var clusters =<?php echo(json_encode($clusters));?>; //clustering data
var cluster_names=<?php echo(json_encode($cluster_names));?>; //cluster names
var num_clusters =<?php echo($num_clusters);?>; //number of clusters
</script>
<?php

if($clusters)
{
		for($i = 0; $i < $num_clusters; $i++)
		{
			$num_results = count($clusters[$i]); //number of results in each cluster set

			//writes a display button to the screen for each cluster generated. name taken from cluster names variable
			echo("<a id=\"displayText".$i."\" class=\"show_result\"  href=\"javascript:toggle('toggleText".$i."','displayText".$i."','".$cluster_names[$i]."');\">".$cluster_names[$i]."</a>");
			echo("<div id=\"toggleText".$i."\" style=\"display: none\">\n");
			echo("<div id = \"result_box\" class = \"result_toggle\">");
			for($k = 0; $k < $num_results; $k += 10)
				{
					$Start = $k;
					$End = $k + 10;
					if($End > $num_results) //error checking on last set of results per cluster
					{
						$End = $num_results;
						echo("<button onclick=\"display_cluster(".$i.",".$Start.",".$End.") \"class=\"button\">".(($k/10)+1)."</button>");
					}
					else
					{
						echo("<button onclick=\"display_cluster(".$i.",".$Start.",".$End.")\" class=\"button\">".(($k/10)+1)."</button>");
					}
					
				}
			echo("</div>");
			echo("</div>\n");
		}
}
else
{
	echo("<h2> NO RESULTS RETURNED </h2>"); //error message displayed if clustering failed
}
?>

</div>

<div id="footer">
	<?php include 'footer.php'; ?>
<!-- disabled code to deal with result page selection
<form name="next_page" action="search.php" method="post">
<?php 
if(isset($result_page))
{
echo "<input type=\"hidden\" id=\"page_num\" name=\"page_num\" value="; echo($next_page); echo ">";
}else{
echo "<input type=\"hidden\" id=\"page_num\" name=\"page_num\" value=0>";
}   
?>
<input type="submit" value="Next Set of Results">
</form> -->
</div>
</div>
</body>
</html>


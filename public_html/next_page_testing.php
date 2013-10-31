<?php


include 'querypreprocess.php'; //processes bollean logic etc for different search engines
include 'condorcetfusion.php'; //contains sorting and ranking algorithms
include 'fullresults.php'; //cURL retrieval code

$query = "tiger NOT woods NOT affair";

$next_page = 3;
//$clustering = $_POST["searchtype"];

$query_tokens = explode(" ", $query);

//$googlequery = google_query($query_tokens);
//$google = google100($googlequery, $next_page);

//$bingquery = bing_query($query_tokens);
//$bing = bing100($bingquery, $next_page);

$blekkoquery = blekko_query($query_tokens);
?><h2><?php echo $blekkoquery; ?></h2><?php
//$blekko = blekko100("boxer+dog", $next_page);

//file_put_contents("RESULTS.txt", json_encode($blekko));

?>


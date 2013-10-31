<html>
<head> </head>

<body>

<?php
	
	$query = $_POST["find"];
	echo $query;
?>

<?php 
  
  $ch = curl_init();
  $googleurl = 'https://www.googleapis.com/customsearch/v1?key=REMOVED&cx=REMOVED&q='.$query.'&alt=json';
  curl_setopt($ch, CURLOPT_URL, $googleurl);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  
  $response = curl_exec($ch);
  curl_close($ch);
   
  $data = json_decode($response,true); //decode reutns object by default, this converst it too array see http://stackoverflow.com/questions/6815520/cannot-use-object-of-type-stdclass-as-array
 // var_dump($response);
 // var_dump($data);
foreach ($data['items'] AS $element)
  {
  ?>
  <h3>
  <?php
  echo $element['link'];
  
 ?>
 </h3>
<?php
  
	}
?> 

<h2> Start of Blekko Results </h2>

<?php

	$blekko = curl_init();
	$blekkourl = 'http://blekko.com/ws/?q='.$query.'/json&auth=REMOVED/';	              
	curl_setopt($blekko, CURLOPT_URL, $blekkourl);
	//curl_setopt($blekko, CURLOPT_HEADER, 0);
	curl_setopt($blekko, CURLOPT_RETURNTRANSFER, 1);

	$response2 = curl_exec($blekko);
	curl_close($blekko);
	
	//var_dump($response2);
	
	$data2 = json_decode($response2,true);
	
	
	
	
	foreach ($data2['RESULT'] AS $element2)
	{
	?>
	<h3>
	<?php
	echo $element2['display_url'];
	?>
	</h3>
	<?php
	}
	?>

<h2> Bing Results </h2>

<?php 

	$bing = curl_init();
	$bingurl = 'https://api.datamarket.azure.com/Bing/Search/Web?$top=10&$format=json&Query=%27'.$query.'%27';
	$accountKey = 'REMOVED';
	
	
	curl_setopt($bing, CURLOPT_URL, $bingurl);
    //curl_setopt($bing, CURLOPT_HEADER, 0);
    curl_setopt($bing, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($bing, CURLOPT_USERPWD,  $accountKey . ":" . $accountKey);
	
	$bingresponse = curl_exec($bing);
	$data2 = json_decode($bingresponse,true);
	
	foreach($data2['d']['results'] As $result)
	  {
  ?>
  <h3>
  <?php
  echo $result['Url'];
  
 ?>
 </h3>
	<?php
	
	}

?>
</body>


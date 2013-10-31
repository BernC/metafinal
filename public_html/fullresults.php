<?php
//CODE ON THIS PAGE REQUESTS 100 results frommeach search engine
//cURL multi code adapted from http://php.net/manual/en/function.curl-multi-exec.php

function google100($query, $nextpage)  //returns an array of 100 results from google
{
// initialise a multi curl request
$mh = curl_multi_init();
//multiple keys needed for testing due to api restrictions
$newkey = 'removed';
$oldkey = 'removed';
$newengid = 'removed';
$oldengid = 'removed';


//add 4 request urls to the mult_Curl request
for($request = 0; $request < 10; $request++)
{
  $page = ($request * 10) + 1 ; //page numbers start at 1 not 0
  $result[$request] =curl_init();
  $googleurl = 'https://www.googleapis.com/customsearch/v1?key='.$newkey.'&cx='.$newengid.'&q='.$query.'&start='.$page.'&alt=json'; //format request
  curl_setopt($result[$request], CURLOPT_URL, $googleurl); //set url
  curl_setopt($result[$request], CURLOPT_HEADER, 0); //set header
  curl_setopt($result[$request], CURLOPT_RETURNTRANSFER, 1); //set transfer
  curl_multi_add_handle($mh, $result[$request]); //add the new result Curl variable to multi_Curl
}

do {
    $status = curl_multi_exec($mh, $active); // run the multi_Curl
    $info = curl_multi_info_read($mh);
    if (false !== $info) { //debug status code 
        //var_dump($info);
    }
} while ($status === CURLM_CALL_MULTI_PERFORM || $active); //continue running multi_Curl until all requests have completed

for($i = 0 ; $i < 10; $i++) {
    $res[$i] = curl_multi_getcontent($result[$i]); //get the results of each search
    curl_close($result[$i]); //close each result cUrl as you read them
}

/****************************************************Result Combination Code***************************************/

$gateway = true; //one way switch for first set of results
foreach($res AS $group_of_results) //foreach to loop through each set
{

  $data = json_decode($group_of_results, true); //decode individual set
  if($gateway) //checks if this is the first set
  {
    $fullresults = $data; //initialises full results array
    $gateway = false; //closes gate

  }else{
    
    $temp = array_merge_recursive($fullresults, $data); //merge full results and next group of results
    $fullresults = $temp;

  }
}
/****************************************************Result Combination Code***************************************/
return $fullresults;
}

function bing100($query, $set_num) //returns an array of 100 results from bing next page function works 1 = second 2 = third etc
{
// initialise a multi curl request
$mh = curl_multi_init();


//add 4 request urls to the mult_Curl request
for($request = 0; $request < 2; $request++)
{
  if($set_num == 0)
  {
      if($request == 1)
      {
        $page = '$skip=50&$top=50'; //retrieve second set of bing results
      }
      else
      {
        $page = '$top=50'; //retrieve first set of results
      }
  }else{ //start of disabled code used to retrieve second set of results
    $first = $set_num*100;
    $second = (50 + $set_num*100);
     if($request == 1)
      {
        $page = "\$skip=".$second."&\$top=50";
      }
      else
      {
        $page = "\$skip=".$first."&\$top=50";
      }
  } //end of disabled code

  //page numbers start at 1 not 0
  $result[$request] =curl_init();
  $bingurl = 'https://api.datamarket.azure.com/Bing/Search/Web?'.$page.'&$format=json&Query=%27'.$query.'%27';
  $accountKey = 'removed';
  curl_setopt($result[$request], CURLOPT_URL, $bingurl);
  curl_setopt($result[$request], CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($result[$request], CURLOPT_USERPWD,  $accountKey . ":" . $accountKey);
  curl_multi_add_handle($mh, $result[$request]); //add the new result Curl variable to multi_Curl
}

do {
    $status = curl_multi_exec($mh, $active); // run the multi_Curl
    $info = curl_multi_info_read($mh);
    if (false !== $info) { //debug code 
        //var_dump($info);
    }
} while ($status === CURLM_CALL_MULTI_PERFORM || $active); //continue running multi_Curl until all requests have completed

for($i = 0 ; $i < 2; $i++) {
    $res[$i] = curl_multi_getcontent($result[$i]); //get the results of each search
    curl_close($result[$i]); //close each result cUrl as you read them
}

$gateway = true; //one way switch for first set of results
foreach($res AS $group_of_results) //foreach to loop through each set
{

  $data = json_decode($group_of_results, true); //decode individual set
  if($gateway) //checks if this is the first set
  {
    $fullresults = $data; //initialises full results array
    $gateway = false; //closes gate

  }else{
    
    $temp = array_merge_recursive($fullresults, $data); //merge full results and next group of results
    $fullresults = $temp;

  }
}

return $fullresults;
}

function blekko100($query, $set_num) //multipage results work, second page index = 1, third index = 2 etc ....
{

  $blekko = curl_init();
  if($set_num == 0)
  {
  $blekkourl = 'http://blekko.com/ws/?q=%27'.$query.'$27+/json+/ps=100&auth=removed/';  
  }else{
  $blekkourl = 'http://blekko.com/ws/?q=%27'.$query.'%27+/json+/ps=100&auth=removed/&p='.$set_num; //disabled code
  }              
  curl_setopt($blekko, CURLOPT_URL, $blekkourl);
  curl_setopt($blekko, CURLOPT_RETURNTRANSFER, 1);

  $response2 = curl_exec($blekko); //curl_multi not required as blekko allows 100 results at a time
  curl_close($blekko);
  $blokkojson = json_decode($response2, true);

  return $blokkojson;
}

function return_Set10($query)
{
//creating query strings
$query_tokens = explode(" ", $query);
$googlequery = google_query($query_tokens);
$bingquery = bing_query($query_tokens);
$blekkoquery = blekko_query($query_tokens);
$result = array();

$mh = curl_multi_init(); //curl multi to allow request to occur simultaneously

//setup google request
$result[0] = curl_init();
$googleurl = 'https://www.googleapis.com/customsearch/v1?key=removed&cx=removed&q='.$googlequery.'&alt=json';

curl_setopt($result[0], CURLOPT_URL, $googleurl);
curl_setopt($result[0], CURLOPT_RETURNTRANSFER, 1);
curl_multi_add_handle($mh, $result[0]);

//setup blekko request
$result[1] = curl_init();
$blekkourl = 'http://blekko.com/ws/?q=%27'.$blekkoquery.'$27+/json&auth=removed/';   

curl_setopt($result[1], CURLOPT_URL, $blekkourl);
curl_setopt($result[1], CURLOPT_RETURNTRANSFER, 1);
curl_multi_add_handle($mh, $result[1]); 

//setup bing request
$result[2] = curl_init();
$bingurl = 'https://api.datamarket.azure.com/Bing/Search/Web?$top=10&$format=json&Query=%27'.$bingquery.'%27';
$accountKey = 'removed';

curl_setopt($result[2], CURLOPT_URL, $bingurl);
curl_setopt($result[2], CURLOPT_RETURNTRANSFER, 1);
curl_setopt($result[2], CURLOPT_USERPWD,  $accountKey . ":" . $accountKey);
curl_multi_add_handle($mh, $result[2]);

 //run cURL mult request
do {
    $status = curl_multi_exec($mh, $active); // run the multi_Curl
    $info = curl_multi_info_read($mh);
    if (false !== $info) { //debug status code to be removed later
        //var_dump($info);
    }
} while ($status === CURLM_CALL_MULTI_PERFORM || $active); //continue running multi_Curl until all requests have completed

for($i = 0 ; $i < 3; $i++) {
    $res[$i] = curl_multi_getcontent($result[$i]); //get the results of each search
    curl_close($result[$i]); //close each result cUrl as you read them
}

return $res;
}
?>
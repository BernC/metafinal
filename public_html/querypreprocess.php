<?php


function blekko_query($tokens) //formats input query in a suitable form for belkko
{
	$blekkoquery = $tokens[0]; //used to prevent joiner being placed before first term
	$i = 0;

	foreach($tokens as $word)
	{

		if($i != 0)
		$blekkoquery = $blekkoquery."+".$word; //rejoining quert in suitable manner
		$i++;
	}
//pattern removal and replacement
$pattern = array();
$pattern[0] = "{\\\}"; //this works to remove a backslash
$pattern[1] = "/\"/"; //removes double quotes
$pattern[2] = "/NOT\\+/";
$pattern[3] = "/\//";
$pattern[4] = "/\\+OR\\+/";



$replacement = array(); 
$replacement[0] = "%5C"; //this replaces a backslash
$replacement[1] = "%22"; //replaces double quotes
$replacement[2] = "-";
$replacement[3] = "%2F";
$replacement[4] = "+";


return preg_replace($pattern, $replacement, $blekkoquery);
}

function bing_query($tokens) //formats query in a suitable manner for bing
{
	$bingquery = $tokens[0];
	$i = 0;

	foreach($tokens as $word)
	{
		if($i != 0)
		$bingquery = $bingquery."+".$word;
		$i++;
	}

$pattern = array();
$pattern[0] = "{\\\}"; //this works to remove a backslash
$pattern[1] = "/\'/"; //removes double quotes
$pattern[2] = "/\'/"; //removes single quotes
$pattern[3] = "/\(/"; //removes (
$pattern[4] = "/\)/"; //removes )
$pattern[5] = "/\//";	//removes /


//replacement characters for above symbols
$replacement = array(); 
$replacement[0] = "%5C"; 
$replacement[1] = "%27"; 
$replacement[2] = "%27";
$replacement[3] = "%28";
$replacement[4] = "%29";
$replacement[5] = "%2F";


return preg_replace($pattern, $replacement, $bingquery);
}

function google_query($tokens) //reformats a google query
{
	$googlequery = $tokens[0];
	$i = 0;

	foreach($tokens as $word)
	{
		if($i != 0)
		$googlequery = $googlequery."+".$word;
		$i++;
	}

//patterns to be removed
$pattern = array();
$pattern[0] = "{\\\}"; //this works to remove a backslash
$pattern[1] = "/NOT\+/";
$pattern[2] = "/\//";



//patterns to replace them with
$replacement = array(); 
$replacement[0] = "%5C"; //this replaces a backslash
$replacement[1] = "-";
$replacement[2] = "%2F";


return preg_replace($pattern, $replacement, $googlequery);
}

	
	


?>



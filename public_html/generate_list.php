<?php

function search($ranks, $string, $counter, &$address) //INPUTS
{
$i = 0;
	while($i != $counter)
	{
		
		if($ranks[$i][1] == $string)
		{
		$address = $i;	
		return FALSE; //False is $string is present in array
		
		}
	$i++;
	}
return TRUE; //true if $string is not in the array
}

function return_url($original_url)
{
	$temp = parse_url($original_url); //split the url into component parts
	
	if(isset($temp['query'])) //if url contains a query section
	{
		$normalised_url = $temp['host'].$temp['path'].$temp['query']; //normalised url
	}else
	{
		$normalised_url = $temp['host'].$temp['path']; //alternate standardised url
	}


	return $normalised_url;
}





function generate_list($googledata, $blekkodata, $bingdata)
{
		//  ____________________________________________________________________________________________________
		// | address | snippet | google rank | bing rank |	blekko rank | google bool | bing bool | blekko bool |
		//	----------------------------------------------------------------------------------------------------
	
	$ranks = array();
	$subarray = array(0,"httpaddress",0,0,0,false,false,false);

	$counter = 0;

	//Begin filling array with results from google	
	if($googledata){ //check if google data was returned
	
	foreach ($googledata['items'] AS $element)
	{
	 $normalised_url = return_url($element['link']);

	 $subarray[0] = $normalised_url; 
	 $subarray[1] = $element['snippet']; 
	 $subarray[2] = $counter + 1; //ranking data
	 $subarray[5] = $counter+1; //location data

	 array_push($ranks,$subarray); //add to result array
	 $counter = $counter + 1;
	}
	}

	//Add the results from Blekko
	if($blekkodata) //check was blekko data returned
	{
		foreach ($blekkodata['RESULT'] as $blekkoelement) 
		{
			 $normalised_url = return_url($blekkoelement['url']);
			if(search($ranks,$normalised_url,$counter,$duplicate_address))
			{
				$counter++;
				$subarray[0] = $normalised_url; 
				if(isset($blekkoelement['snippet'])) //unlike other engines belkko occasionaly returns results without a snippet attached
				{		
				$subarray[1] = $blekkoelement['snippet']; 
				}
				$subarray[2] = 0; //this is because blekko is returning a result that google does not have
				$subarray[4] = $blekkoelement['c']; //ranking data
				$subarray[5] = false; //working
				$subarray[6] = false;
				$subarray[7] = $blekkoelement['c']; //location data
				array_push($ranks, $subarray);
			}
		else //update existing entry
			{
				$ranks[$duplicate_address][4] = $blekkoelement['c'];
				$ranks[$duplicate_address][7] = false;

			}
		}
	}
	


	//Add in the results from Bing
	$bingcounter = 1;
	if($bingdata) //check were bing results returned
	{
		foreach ($bingdata['d']['results'] as $bingelement) 
		{
			$normalised_url = return_url($bingelement['Url']);
	
				if(search($ranks,$normalised_url,$counter,$duplicate_address))
				{
					$counter++;
					$subarray[0] = $normalised_url; 
					$subarray[3] = $bingcounter; //ranking data
					$subarray[1] = $bingelement['Description']; 
					$subarray[2] = 0; //this is because bing is returning a result that google does not have
					$subarray[4] = 0; //this is because bing is returning a result that blekko does not have
					$subarray[5] = false; //working
					$subarray[6] = $bingcounter; //location data
					$subarray[7] = false;
		
		
					array_push($ranks, $subarray);
				}
				else //update existing entry
				{
					$ranks[$duplicate_address][3] = $bingcounter;	
					$ranks[$duplicate_address][6] = false;	
				}
	
			$bingcounter++;
		}
	}
return $ranks; //return standardised data set for vectorization
}
?>
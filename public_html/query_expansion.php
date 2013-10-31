
<?php

function query_expansion($query)
{
	$tokens = preg_split("/[^[:alpha:]]+/" ,$query); //split string into tokens

	$lines = file("stopwords.txt", FILE_IGNORE_NEW_LINES); //creating list of stopwords from the stopwords.txt file

	$storage = array(); //initialise storage

	foreach ($tokens as $word) { //create list of terms to look up that aren't stopwords
		if(!in_array(strtolower($word), $lines))
		{
			$storage[] = $word;
		}
	}
	
	$num_words = count($storage); //check number of words
	//attempting to limit poor rewrites from being displayed
	if(($num_words > 3) || ($num_words == 0)) //cause function to return false if conditions are not met
	{
		return false;
	}
	
		
	$mh = curl_multi_init(); //initialise multi_curl variable
	
	//add request urls to the mult_Curl request
	for($request = 0; $request < count($storage); $request++)
	{
	  $result[$request] =curl_init();
	  $request_url = 'http://words.bighugelabs.com/api/2/key_removed/'.$storage[$request].'/json';
	  curl_setopt($result[$request], CURLOPT_URL, $request_url);
	  curl_setopt($result[$request], CURLOPT_HEADER, 0);
	  curl_setopt($result[$request], CURLOPT_RETURNTRANSFER, 1);
	  curl_multi_add_handle($mh, $result[$request]); //add the new result Curl variable to multi_Curl
	}

	do {
	    $status = curl_multi_exec($mh, $active); // run the multi_Curl
	    $info = curl_multi_info_read($mh);

	} while ($status === CURLM_CALL_MULTI_PERFORM || $active); //continue running multi_Curl until all requests have completed

	for($i = 0 ; $i < count($storage); $i++) {
	    $res[$i] = curl_multi_getcontent($result[$i]); //get the results of each search
	    curl_close($result[$i]); //close each result cUrl as you read them
	}
	$data = $res;
	$myresults = array();

	foreach ($res as $key) { //decoding result streams
		$myresults[] = json_decode($key);	
	}

	$valid_rewrite = false;
	$i = 0;
	$gate = 0;
	$rewrites = array();
	while($i < 3 && $gate < 3)
	{
		//messy method of resetting the temp variable. prevents all the different rewrites from ending up on the same line
		$temp;
		unset($temp);
		$temp = NULL;
		$word_num = 0;

	foreach ($myresults as $key) { //this structure was neccessary as the thesaurase returns a different json depending on whether a noun verb or adjective was queried
		if(isset($key->adjective->syn[$i])) //if an adjective was used
		{
		$temp = $temp.$key->adjective->syn[$i]." "; //adding " " to end of word
		$valid_rewrite = true;
		}elseif (isset($key->noun->syn[$i])) { //if noun was used
		$temp = $temp.$key->noun->syn[$i]." ";
		$valid_rewrite = true;
		}elseif (isset($key->verb->syn[$i])) { //if verb was used
		$temp = $temp.$key->verb->syn[$i]." ";
		$valid_rewrite = true;		
		}else{
		$temp = $temp.$storage[$word_num]." "; //used to access original words the synonyms were generated for
		$gate++;	//restrict the number of times that the original query words may be used to 3
		}
	$word_num++; //track which orignal word is being replaced
	}
	$i++;
	$rewrites[] = $temp; //store the rewritten query

	}
	


if($valid_rewrite){ //if at least one valid rewrite occured
	return $rewrites;
}else{
	return false;
}
}





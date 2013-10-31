<?php

include 'porteralgorithm.php'; // porter stemming used later

function full_document_wordlist($list, $lines) //generates a stemmed, stopworded list of terms from the snippets
{
$words = array(); //list of terms being generated
foreach($list AS $snippet)	
{

	$tokens = preg_split("/[^[:alpha:]]+/" ,$snippet[1]); //split the snippets with a non alphabetical delimiter
	foreach ($tokens as $single_token) 
	{
		$presentinsnippet = true;
		$single_token = strtolower($single_token); //lower case the token where neccessary
		$single_token = PorterStemmer::Stem($single_token); //stem the token using porter algorithm
		

		if(!in_array($single_token, $lines))// disregard token if it is on the stopword list
		{

			if(!empty($single_token)) //check is the string empty, may occur in due to ... or other discrepancies in snippets
			{
				if(strlen($single_token) > 2) //used to further remove non-descriminatory terms
				{
					$switch = true; //switch used to see is word already in term list
					for ($i = 0; $i < count($words); $i++)  // for loop required to allow manipulation of $words elements
					{
						if($single_token == $words[$i][0]) //check does word already exist in term list
						{
							$words[$i][1]++; //increment counter if word has already been collected
							$switch = false; //set switch to false if word is already on list
						}
					}
					
					if($switch) //only adds new words if they dont already exist in array
					{
						$temp = array();
						
						array_push($temp, $single_token,1,0,0); // term ,overall_frequency, snippet_frequency, idf
						array_push($words, $temp); //add new term to overall list
						
					}

				}			
			}
		}
	}
}


$adjusted = array(); //storage for adjusted array
	foreach($words as $term)
	{
		if($term[1] > 1) //only count terms which appear in more than one snippet. Otherwise the term can't help determine the closeness anyway
		{
			$adjusted[] = $term;
		}
	}
	return $adjusted;
}


//$duplicate_address; // holds the numerical address of where a dublicated link is stored in the $ranks array




function create_term_vector($blekkodata, $bingdata, $googledata, $query_tokens) //require a decoded json of each search engines output
{
	$list = generate_list($googledata, $blekkodata, $bingdata); //list contains all snippets and accompanying urls
	$lines = file("stopwords.txt", FILE_IGNORE_NEW_LINES); //creating list of stopwords from the stopwords.txt file
	
	foreach($query_tokens AS $query) //add query tokens to stopword list. this removes the terms that all result SHOULD have in common from clustering process
	{
		array_push($lines,$query);
	}
	
	

	$words = full_document_wordlist($list, $lines); //creates a list of all the important words in the snippets as well as their frequency accross all snippets

	$vector_array = array();

	foreach ($list as $snippet) {
		//used to store result location and ranking data in their own sub index. Prevents it from interfering with the clustering operation
		$printout = array();
		for($g = 0; $g < 6; $g++)
		{
			$printout[$g] = $snippet[$g+2];
		}
		
		$tokens = preg_split("/[^[:alpha:]]+/" ,$snippet[1]); //split the snippets with a non alphabetical delimiter into tokens
		$temp = array_fill(0, count($words), 0); //generate a blank vector, number of terms on list = size of vector

		$most_common_term = array(0,0);
			foreach ($tokens as $single_token) //check every token found in the array
			{
			$single_token = strtolower($single_token); //lowercase word
			$single_token = PorterStemmer::Stem($single_token); //stem word
			$i = 0;
			$trigger = false; 
			foreach ($words as $entry) { //using the term list generated earlier to 
				if($entry[0] == $single_token) //if the term matches the word from the snippet being examined
				{
					$temp[$i]++; //count number of times each term appears
					if($temp[$i] > $most_common_term[0]) //check current term against high bar
					{
						$most_common_term[0] = $temp[$i]; //set new high bar for most common term
						$most_common_term[1] = $single_token; //set the new most common term
					}
				}
				$i++; //move to next term of vector
				
			}
			

			}
			$storage = array();												//								   ______________________________________________________________
			array_push($storage, $printout, $temp, $most_common_term[1]); //generates an entry for the vector | Display & Ranking Data | term count array | most common term |
			array_push($vector_array, $storage); //add entry to array 										   --------------------------------------------------------------

	}
	//code to check how many snippets each term appears in. needed to calculate tf-idf 
	for($i = 0; $i < count($vector_array); $i++)
		{
		$j = 0;	
		foreach ($vector_array[$i][1] as $term) //checking the term part of array only
		{
			if($term) //if the term was present in the snippet being examined
			{
				$words[$j][2]++; //increment count in original term list
			}
			$j++; //move to next term on $word list
		}

		}

	/********************************************creating proper weighted terms for each vector*****************************/

	//working out constants for term weighting

	// $most_freq_accross all snippets
	$most_freq_accross_all = 0;
	foreach ($words as $word) 
	{
		if($most_freq_accross_all < $word[2])
			$most_freq_accross_all = $word[2];
	}

	/**************************************************************************************************/
	//calculate idf of each term

	$num_documents = count($list); //count the number of unique documents in the collection

	for($i = 0; $i < count($words); $i++) 
	{
		$words[$i][3] = log(($num_documents/$words[$i][2]),2.0);
	}

	/**************************************************************************************************/

	//Creating Term Weighting Array
	for ($i = 0; $i < count($vector_array); $i++) //iterate through each result
	{
		for($j = 0; $j < count($vector_array[$i][1]); $j++) //iterate through each term for each result
		{
			$vector_array[$i][1][$j] = (($vector_array[$i][1][$j]/$most_freq_accross_all)*$words[$j][3]);	//calulate weight for each term		
		}

	}

	//$clustering_data = json_encode($vector_array);
	//file_put_contents("clustering2.json", $clustering_data);

	return $vector_array;
}

/**************************************************************************************************/

?>
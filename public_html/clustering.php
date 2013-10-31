<?
include 'aggregation_method.php'; //contains sorting and ranking algorithms

function compare_similarity($ArrayOne, $ArrayTwo) //calculates the cosine similarity when given two arrays
{
$sum = 0;
$vectorA = 0;
$vectorB = 0;

	for($i = 0; $i < count($ArrayOne); $i++)
	{	
		$sum = $sum + ($ArrayOne[$i]*$ArrayTwo[$i]); //calculate the sum of the product of all array terms
		$vectorA = $vectorA + pow($ArrayOne[$i], 2); //calculate the sum of square of all terms for each array
		$vectorB = $vectorB + pow($ArrayTwo[$i], 2);
	}

$vectorA = sqrt($vectorA); //get the square root of each sum of squares
$vectorB = sqrt($vectorB);

if($vectorA == 0) //case where snippet has a 0 term vector eg full of stop words or garbage
{
	return 0;
}
if($vectorB == 0)
{
	return 0;
}

$cosine_similarity = $sum/($vectorA*$vectorB);  //calculate the cosine similarity using above terms

return $cosine_similarity;

}

function add_values($a, $b) { return $a + $b; }; //function to add terms of same index of multiple arrays anonymous functions not supported by server
function divide_values($a, $b) { return $a/$b; }; //function to divide all the terms of an index by a number anonymous functions not supported by server

function recompute_centroid($full_vector_array,$num_arrays) //recompute a centroid given a cluster and number of arrays in cluster
{
		$variable_to_return = array();
		$string = array();
		$string[0] = "null";// used to standardise output
		$newcentroid = array(); //storage for new centroid
		$divider = array_fill(0, count($full_vector_array[0][1]), $num_arrays); //generate an array to divide by

		for($i = 0; $i < count($full_vector_array); $i++) //sum all terms of the same index accross all arrays 
		{
    		$newcentroid = array_map("add_values",$newcentroid,$full_vector_array[$i][1]); //calling functions declared earlier to act on all array elements   	
		}
		$newcentroid = array_map("divide_values",$newcentroid,$divider); //divide this by number of arrays to get average point

		array_push($string, $newcentroid); //create new centroid terms
		//array_push($variable_to_return, $string ); //create new return term (doesnt do anything)
		return $string;
}

//function input is an array of centroids which have been generated
function compare_vector_to_centroids($array_of_centroids, $array_to_test) //compares a single array to all current centroids
{
	$centroid_number= array(0,0);
	for($i = 0; $i < count($array_of_centroids); $i++)
	{
		$similarity = compare_similarity($array_of_centroids[$i][1], $array_to_test); //compare similarity of single centroid to array
		if($similarity > $centroid_number[1]){ //compare newly calculated similarity to current best
			$centroid_number[0] = $i; //store index of centroid
			$centroid_number[1] = $similarity; //store similarity for comparison
		}
	}
	return $centroid_number[0]; //return index / centroid number that vector was most similar to
}

function check_nearest_centroid($full_vector_array, $array_of_centroids) //given a group of vectors find the centroids they are closest to
{
	$centroid_assignment = array();
	for($i = 0; $i < count($full_vector_array); $i++) //check every term against the array of centroids
	{
		$centroid_assignment[$i] = compare_vector_to_centroids($array_of_centroids, $full_vector_array[$i][1]);
	}
	return $centroid_assignment; //what centoid/cluster is each result assigned to
}



function calculate_random_centroids($seed_array, $num_required) //picks a chosen number of centroids at random from document pool
{
	$centroid_array = array(); //storage for centoids
	$highest_rand = count($seed_array) - 1; //arrays start at zero so need to compensate when setting max random index allowable
	for($i = 0; $i < $num_required; $i++)
	{
		$j = rand(0, $highest_rand); //generate a random index 
		array_push($centroid_array, $seed_array[$j]); //add it to centroid array
	}

	return $centroid_array;
}

function calculate_centroid_movement($old_centroids, $newcentroids) //calculates how much centroid movement has occured
{
	$difference = array();
	for($i = 0; $i < count($old_centroids); $i++)
	{
		$difference[$i] = compare_similarity($newcentroids[$i][1],$old_centroids[$i][1]);	//compare difference between old and new centroids	
	}


$var = min($difference);

if($var > .97) // this means the centoid will contine to be recalculated until the minimum cosine similarity between an old and new centroid exceed .97
{ 
return false; //stop clustering
}

	return true; //perform another iteration
}

//splits the full set of vectors up by their cluster, user must input the cluster number to use
function divide_array_by_centroid($centroid_num, $full_vector_array, $current_clustering){

$single_cluster = array();

	for($i = 0; $i < count($current_clustering); $i++) //iterate through each result
	{
		if($current_clustering[$i] == $centroid_num) //check does assignment match the requested cluster
		{
			array_push($single_cluster, $full_vector_array[$i]);
		}

	}
	if(isset($single_cluster))
	{ //check if that cluster actually contained any documents
		return $single_cluster;
	}else
	{
		return false; //returned if cluster was empty
	}
}


//All above are function to allow clustering to operate. the below code calls them in correct sequence

function cluster_vectors($term_vector, $num_clusters, $num_iterations)
{

	$initial_centroids = calculate_random_centroids($term_vector, $num_clusters); //this only happens at start, generate random centroids

	$minimumdifference = true; //set the check for converged clustering to true

	$testing = array();

	for($count = 0; $count < $num_iterations && $minimumdifference; $count++) //continue clustering while the number of iteration and minimum difference are true
	{

		$testing = check_nearest_centroid($term_vector, $initial_centroids); //checking which docs are closest to which clusters. will happen multiple times

		$newcentroids = array();
		
		for($i = 0; $i < $num_clusters; $i++) //this loop will recompute clusters 
		{
			$single_cluster = divide_array_by_centroid($i,$term_vector, $testing); //target a single cluster

			if($single_cluster) //check cluster is valid
			{

					 $temp = recompute_centroid($single_cluster,count($single_cluster)); //recompute centroid for that cluster
					 $newcentroids[] = $temp; //store new centroid

			}
			else //cluster was blank
			{
				$random_centroid = calculate_random_centroids($term_vector, 1); //create a single new centroid randomly
				$newcentroids[] = $random_centroid[0]; 
			}


			
		}
		
		$minimumdifference = calculate_centroid_movement($initial_centroids,$newcentroids); //check has minimum distance been assigned
		
		$initial_centroids = $newcentroids; //set centroids to newly computed centroids


	}

	//*******************************************************Clustering Complete*******************************************************/

	$clusters = array();

	//remove unnnesscessay information from return
	for($i = 0; $i < $num_clusters; $i++)
	{
		$single_cluster = divide_array_by_centroid($i,$term_vector,$testing);
		for($k = 0; $k < count($single_cluster); $k++)
		{
			$single_cluster[$k][1] = $single_cluster[$k][2];
			$single_cluster[$k][2] = $single_cluster[$k][0][0];
			$single_cluster[$k][3] = $single_cluster[$k][0][1];
			$single_cluster[$k][4] = $single_cluster[$k][0][2];

		}

		array_push($clusters, $single_cluster);

	}

	for($j = 0; $j < count($clusters); $j++)
	{
		$clusters[$j] = myquicksort($clusters[$j]); //rank each cluster using RRF algorithm
	}
return $clusters;
}



?>
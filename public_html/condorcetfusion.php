
<?php
//bubble sort was used originally before switching to quick sort
/*
FUNCTION BubbleSort($sort_array,$reverse) 
{ 
	FOR ($i = 0; $i < SIZEOF($sort_array); $i++)
	{ 
		
		FOR ($j = $i + 1; $j < SIZEOF($sort_array); $j++)
		{ 
			echo "Called";
			echo $i;
			echo $j;
			IF($reverse)
			{ 
				IF (condorcet_fuse($sort_array[$i],$sort_array[$j]))
				{ 
					echo $i;
			echo $j;

					echo "array i "; print_r($sort_array[$i]); ?><br><?php
					echo "array j "; print_r($sort_array[$j]); ?><br><?php
					$tmp = $sort_array[$i]; 
					$sort_array[$i] = $sort_array[$j]; 
					$sort_array[$j] = $tmp; 
				} 

			}ELSE
			{  
					$tmp = $sort_array[$i]; 
					$sort_array[$i] = $sort_array[$j]; 
					$sort_array[$j] = $tmp; 

			} 
		} 
	} 

	RETURN $sort_array; 
} 
*/
function myquicksort( $array ) //modified quicksort algorithm adapted from http://pageconfig.com/post/implementing-quicksort-in-php
{
    if( count( $array ) < 2 ) 
    {
        return $array;
    }
    
    $left = $right = array();
    reset( $array );
    $pivot_key  = key( $array );
    $pivot  = array_shift( $array );
    foreach( $array as $v ) 

    {
        if(reciprocral_rank($v,$pivot)) //reciprocral rank used as comparison method
        {
            array_push($left,$v);
        }    

        else
         {
         	array_push($right, $v);
         }   
            
    }
    
    return array_merge(myquicksort($left), array($pivot_key => $pivot), myquicksort($right));
}

//original condorcet fusion comparison algorithm. abandoned due to unsatisfactory results
/*
function condorcet_fuse($document,$pivot_document)
{
  echo "Called condorcet";?><br><?php
    $num_engines = 3;
    $count = 0;
    for($i = 0; $i < $num_engines; $i++)
        {


            if(($document[$i+2] != 0) AND ($pivot_document[$i+2] != 0))
            {
              if($document[$i+2] < $pivot_document[$i+2])
              {
               $count++;
              }elseif ($document[$i+2] > $pivot_document[$i+2]) {
                $count++;
              }
            }elseif (($document[$i+2] == 0) AND ($pivot_document[$i+2] != 0)) {
              $count--;
            }else{
              $count++;
            }

        }


    if($count > 0)
    {
     // echo "Output True"; ?><br><?php
        return True;
    }
        
    else
    {
      //echo "Output False"; ?><br><?php
        return False;
    }

}

*/

function reciprocral_rank($document,$pivot_document)
{
    $num_engines = 3; //fixed number of engines
    $document_score = 0;
    $pivot_document_score = 0;
    for($i = 0; $i < $num_engines; $i++) //calculate scores
        {
          if($document[$i+2] != 0) //scoring only occurs when an engine ranked the document
          {
          $document_score = $document_score + (1/(60 + $document[$i+2])); //document score
        }
        if($pivot_document[$i+2] != 0)
        {
          $pivot_document_score = $pivot_document_score + (1/(60 + $pivot_document[$i+2])); //pivot score
        }
        }

    if($document_score > $pivot_document_score)
    {
      return TRUE;
    }else{
      return False;
    }
  }

?>

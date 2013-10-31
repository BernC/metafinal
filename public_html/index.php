<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<title> Metasearch Engine </title>
<link rel="shortcut icon"
 href="favicon.ico" />
<link href="meta_style.css" rel="stylesheet" type="text/css">
<script src="searchbox_scripts.js"></script>
</head>
<body>
  <div id="searchpage">
  <div id="logo"></div>
  <div id="search_form">
 <form name="search" id="search" method="post" onsubmit="return validateForm();" >
 Seach for: <input type="text" size="40" placeholder="Type Your Query" name="find" /> 
 <input type="submit" name="submit" id="submit" value="Search" /> <br>
  <input type="radio" name="operation" value="1" checked><a href='#' class="tooltip">Aggregation <span>
                                                                                                        Select Aggregation to combine<br>
																													the results of multiple search<br>
																													engines into a single ranked list
                                                                                               </span>
                                                        </a>
                                                        
                                                        
 <input type="radio" name="operation" value="2"><a href='#' class="tooltip">Clustering<span>
Clustering is a search technology <br>
which attempts to group results<br>
with a similar meaning into<br>
the same category. as this <br>
technology is computationally<br>
expensive the search may take some<br>
time to complete</span></a>

 <input type="hidden" name="searching" value="yes" />
 </form>
 </div>
 </div>

</body>

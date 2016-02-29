<?php
   
require ('../includes/config.inc.php');
require (MYSQL);
function get_books($dbc){
 	// Define the query:
	$q = "SELECT b_id,b_title,b_categ,author_name,b_price,b_ratings FROM azteca_books WHERE b_status = 20";		
	$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc)); // Run the query.

    $json_array = array();
    while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
            
    	$json_array[] = $row ;
    	
    } // End of WHILE loop.

	mysqli_free_result ($r);
	return $json_array;
}

function get_top_books($dbc,$top){
 	// Define the query:
	$q = "SELECT b_id,b_title,b_categ,author_name,b_price,b_ratings FROM azteca_books WHERE b_status = 20 ORDER BY b_ratings DESC LIMIT $top";		
	$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc)); // Run the query.

    $json_array = array();
    while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
            
    	$json_array[] = $row ;
    	
    } // End of WHILE loop.

	mysqli_free_result ($r);
	return $json_array;
}

if(isset($_GET['list'])){
	if($_GET['list']=='all'){
	
		$json_array = get_books($dbc);
		echo json_encode($json_array);
	}
	
}

if(isset($_GET['Top']) && is_numeric($_GET['Top'])){

	$json_array = get_top_books($dbc,$_GET['Top']);
	echo json_encode($json_array);
	
}

    /* close statement */
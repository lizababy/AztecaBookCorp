<?php
   
require ('../includes/config.inc.php');
require (MYSQL);

function get_book($dbc, $b_id){
 	// Define the query:
    $q = "SELECT b_title, author_name, b_categ FROM azteca_books where b_id =? ";		
    $stmt = mysqli_stmt_init($dbc);
    // Prepare the statement:
    $stmt = mysqli_prepare($dbc, $q);

    // Bind the variables:
    mysqli_stmt_bind_param($stmt, 'i',$b_id);

    // Execute the query:
    mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
    $r = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
	$json_array = array();
    // Fetch and print all the columns for current book.
    if ($row) {  
    
    	$json_array[] = $row ;
    	
    };
	/* free result */
    mysqli_stmt_free_result($stmt);

	return $json_array;
}



if(isset($_GET['ID']) && is_numeric($_GET['ID']) ){

	$json_array = get_book($dbc,$_GET['ID']);
	echo json_encode($json_array);
	
}

    /* close statement */
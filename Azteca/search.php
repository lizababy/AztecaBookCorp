<?php

require ('../includes/config.inc.php');
$page_title = 'Book Details';
include (HEADER);
require_once (FUN_DEFS);
require (MYSQL);

if($_SERVER['REQUEST_METHOD'] == 'POST' ) {
    $b_title = $_POST['b_title'];//Set variable
    // Make the full text query 
    $q = "SELECT b_id FROM Azteca_books WHERE b_status=20 AND MATCH (b_title) AGAINST ( '$b_title')";	
    
    if(isset($_SESSION['user_level'])){
        if(($_SESSION['user_level'])>10)           
            $q = "SELECT b_id FROM Azteca_books WHERE MATCH (b_title) AGAINST ( '$b_title')";	
    }

    $r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc)); // Run the query.

    if ( mysqli_num_rows( $r ) > 0 )
    {
        echo '<h2>Full Search Results</h2>';
        echo 'There are currently '.count($r) .' number of books matching.';
        while ( $row = mysqli_fetch_array( $r, MYSQLI_ASSOC ))
        { 
            get_book_details($dbc, $row['b_id']);
        }
    }else {
        echo '<p>There are currently no books matching.</p>' ;
    }
    mysqli_close($dbc);
}
include (FOOTER); 
<?php 
// This page is for editing a book record.
// This page is accessed through manage_publisher_books.php

require ('../includes/config.inc.php');
$page_title = 'Edit a Book';
include (HEADER);
require (MYSQL);
require_once (FUN_DEFS);

redirect_ifNotLoggedIn();
$b_pub_id = $_SESSION['user_id'];
// Check for a valid book ID, through GET or POST:
if ( (isset($_GET['b_id'])) && (is_numeric($_GET['b_id'])) ) { // From view_users.php
	$b_id = $_GET['b_id'];
} elseif ( (isset($_POST['b_id'])) && (is_numeric($_POST['b_id'])) ) { // Form submission.
	$b_id = $_POST['b_id'];
} else { // No valid ID, kill the script.
	echo '<p class="error">This page has been accessed in error.</p>';
	include (FOOTER); 
	exit();
}
 
// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
	 // Trim all the incoming data:
        $trimmed = array_map('trim', $_POST); 
        $b_title = mysqli_real_escape_string ($dbc, $trimmed['b_title']);
		$author_name = mysqli_real_escape_string ($dbc, $trimmed['author_name']);
		$b_categ = mysqli_real_escape_string ($dbc, $trimmed['b_categ']);
		$b_desc =  mysqli_real_escape_string ($dbc, $trimmed['b_desc']);
		$pub_name = mysqli_real_escape_string ($dbc, $trimmed['pub_name']);
		$b_price = mysqli_real_escape_string ($dbc, $trimmed['b_price']);
		$b_ratings = mysqli_real_escape_string ($dbc, $trimmed['b_ratings']);
		
    if ($b_title && $b_desc && $b_categ && $author_name &&
            $pub_name && $b_price && $b_ratings) { // If everything's OK.
            // Make the query:
            $q = "UPDATE azteca_books SET b_pub_id = ?, b_title=?, author_name=?,"
                    . " b_categ=?, b_desc=?,"
                    . " pub_name =?,b_price =?,b_ratings =? WHERE b_id=? LIMIT 1";
            // Prepare the statement:
            $stmt = mysqli_prepare($dbc, $q);

            // Bind the variables:
            mysqli_stmt_bind_param($stmt, 'isssssdii',$b_pub_id,$b_title,$author_name,$b_categ,$b_desc,$pub_name,$b_price,$b_ratings,$b_id);

            // Execute the query:
            mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
            if (mysqli_stmt_affected_rows($stmt) == 1) { // If it ran OK.
                // Print a message:
		echo '<h3 class="text-success">The book details has been edited successfully!</h3>';	
			
            } else { // If it did not run OK.
                echo '<p class="error">The book could not be edited due to a system error. We apologize for any inconvenience.</p>'; // Public message
            }		
		
    } else { // If one of the data tests failed.

            echo '<p class="error">Please try again.</p>';
            //show the form
            echo '<h3>Edit Details: </h3>';
            include '../includes/book_details_form.inc.html';	
    }
}else{// Retrieve the book details:
    
    $q = "SELECT b_title,author_name, b_categ,b_desc, pub_name, b_price, b_ratings  FROM azteca_books WHERE b_id = $b_id";		
    $r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));

    if (mysqli_num_rows($r) == 1) { // Valid book ID, show the form.
	
	$row = mysqli_fetch_array ($r, MYSQLI_ASSOC);
	$b_title = $row['b_title'];
        $b_desc = $row['b_desc'];
        $b_categ = $row['b_categ'];
        $author_name= $row['author_name'];
        $pub_name = $row['pub_name'];
        $b_price = $row['b_price'];       
        $b_ratings = $row['b_ratings'];
        //show the form
	echo '<h3>Edit Details: </h3>';
        include '../includes/book_details_form.inc.html';
    }  
}        
get_book_details($dbc, $b_id);
mysqli_close($dbc);
include (FOOTER);

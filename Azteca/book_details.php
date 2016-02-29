<?php
// This page is for detailed view of a book.

require ('../includes/config.inc.php');
$page_title = 'Book Details';
include (HEADER);
require_once (FUN_DEFS);
require (MYSQL);

// Check for a valid book ID, through GET or POST:
if ( (isset($_GET['b_id'])) && (is_numeric($_GET['b_id'])) ) { // From manage_my_books.php 
	$b_id = $_GET['b_id'];

} else { // No valid ID, kill the script.
	echo '<p class="error">This page has been accessed in error.</p>';
	include (FOOTER); 
	exit();
}

get_book_details($dbc, $b_id);
$teaser_path =  "../../tmpmedia/teaser/".$b_id.".teaser.jpg";
if(file_exists($teaser_path)){
        echo('<div><h3>Book Teaser :</h3><img src='.$teaser_path.'></div>');
}
$cover_path =  "../../tmpmedia/cover/".$b_id.".cover.jpg";
if(file_exists($cover_path)){
        echo('<div><h3>Book Cover :</h3><img src='.$cover_path.'></div>');
}
mysqli_close($dbc);
include (FOOTER); 
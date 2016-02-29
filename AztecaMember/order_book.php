<?php
// This script retrieves records of books of a particular author
// This can be viewed and edited by admin,publishers and author
// Each role will have different permissions and limited access as defined
// Also allows the results to be sorted in different ways.

require ('../includes/config.inc.php');
include (HEADER);;
require (MYSQL);
require_once (FUN_DEFS);

$page_title = 'update the Book Details';
redirect_ifNotLoggedIn();
// Number of records to show per page:
$display = 10;

// Determine how many pages there are...
if (isset($_GET['p']) && is_numeric($_GET['p'])) { // Already been determined.
	$pages = $_GET['p'];
} else { // Need to determine.
    // Count the number of records:
    $q = "SELECT COUNT(b_id) FROM azteca_books WHERE b_status = 20";
    $pages = get_page_no($dbc,$q,$display);
} // End of p IF.

// Determine where in the database to start returning results...
if (isset($_GET['s']) && is_numeric($_GET['s'])) {
	$start = $_GET['s'];
} else {
	$start = 0;
}

// Determine the sort...
// Default is by book created date.
$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'b_categ';

// Determine the order...
// Default is by book created date ascending.
$order_by = (isset($_GET['sort'])) ? b_sort_function($sort) : 'b_categ ASC';

 // Define the query:
$q = "SELECT b_id,b_title,b_categ,author_name,b_price,b_ratings FROM azteca_books WHERE b_status = 20 ORDER BY $order_by LIMIT $start, $display";		
$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc)); // Run the query.

    // Table header:
    echo '</br><table class="table table-bordered">
        <thead>
            <tr bgcolor = "#E0CCE0">
                  <th><b>#</b></th>
                  <th><b><a href="order_book.php?sort=b_title"> Title</a></b></th>
                  <th><b><a href="order_book.php?sort=b_categ"> Category</a></b></th>
                  <th><b><a href="order_book.php?sort=author_name"> Author</a></b></th>
                  <th><b><a href="order_book.php?sort=b_price"> Price <span class="glyphicon glyphicon-usd"></span></a></b></th> 
                  <th><b><a< href="order_book.php?sort=b_ratings"> Ratings</a></b></th> 
                  <th><b> Add to Cart</b></th> 
            </tr>    
        </thead>';
            
   
    // Fetch and print all the records....
    $bg = '#FFFAFA'; 
    $si_no = 0;
    while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
            $bg = ($bg=='#FFFAFA' ? '#FFFFFF' : '#FFFAFA'); 
            echo '
            <tbody>
                <tr bgcolor="' . $bg . '">
                    <td>' . ++$si_no . '</td>
                    <td align="left"><a href="../Azteca/book_details.php?b_id=' . $row['b_id'] . '">' .$row['b_title'] . ' <span class="glyphicon glyphicon-book"></a></td>
                    <td>' . $row['b_categ'] . '</td>
                    <td>' . $row['author_name'] . '</td>
                    <td>' . $row['b_price'] . ' $</td>
                    <td>';
                    for($i = 0 ; $i < $row['b_ratings']; $i++)
                        echo ' <span class="glyphicon glyphicon-star"></span>';
                    echo '</td>
                        <td align="center"><a href="my_cart.php?b_id=' . $row['b_id'] . '"><span class="glyphicon glyphicon-shopping-cart"></a></td>
                </tr>
            </tbody>';
    } // End of WHILE loop.

echo '</table>';
mysqli_free_result ($r);
mysqli_close($dbc);

link_section($pages, $start, $display,$sort, basename($_SERVER['PHP_SELF']));
	
include (FOOTER);
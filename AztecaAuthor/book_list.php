<?php
// This script retrieves records of all books
// This can be viewed by admin,publishers and author
// Each role will have different permissions and limited access as defined
// Also allows the results to be sorted in different ways.

require ('../includes/config.inc.php');
include (HEADER);
require (MYSQL);
require_once (FUN_DEFS);

$page_title = 'View All Books';
redirect_ifNotLoggedIn();
// Number of records to show per page:
$display = 5;
$role = 0;

// Determine how many pages there are...
if (isset($_GET['p']) && is_numeric($_GET['p'])) { // Already been determined.
	$pages = $_GET['p'];
} else { // Need to determine.
    // Count the number of records:
    $q = "SELECT COUNT(b_id) FROM azteca_books";
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
$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'b_status';

// Determine the order...
// Default is by book created date ascending.
$order_by = (isset($_GET['sort'])) ? b_sort_function($sort) : 'b_status ASC';

$role = $_SESSION['user_level'];
	
if($role>10){
	 // Define the query:
	$q = "SELECT b_id,b_title,b_categ,author_name, b_status, DATE_FORMAT(b_created_on, '%M %d, %Y') AS b_created_date FROM azteca_books ORDER BY $order_by LIMIT $start, $display";		
	$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc)); // Run the query.

    // Table header:
    echo '</br><table class="table table-bordered">
        <thead>
            <tr bgcolor = "#E0CCE0">
                  <th><b>#</b></th>
                  <th><b><a href="book_list.php?sort=b_title">Title</a></b></th>
                  <th><b>Thumb</b></th>
                  <th><b><a href="book_list.php?sort=b_categ">Category</a></b></th>
                  <th><b><a href="book_list.php?sort=author_name">Author</a></b></th>
                  <th><b><a href="book_list.php?sort=b_created_date">Date Created</a></b></th> 
                  <th><b><a href="book_list.php?sort=b_status">Status</a></b></th> 
            </tr>    
        </thead>';
            
   
    // Fetch and print all the records....
    $bg = '#FFFAFA'; 
    $si_no = 0;
    while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
            $bg = ($bg=='#FFFAFA' ? '#FFFFFF' : '#FFFAFA');
            $thumbnail_path =  "../../tmpmedia/thumbnail/".$row['b_id'].".thumbnail.jpg";
            echo '
            <tbody>
                <tr bgcolor="' . $bg . '">
                    <td>' . ++$si_no . '</td>
                    <td align="left"><a href="../Azteca/book_details.php?b_id=' . $row['b_id'] . '">' .$row['b_title'] . ' <span class="glyphicon glyphicon-book"></a></td>';
                    if(file_exists($thumbnail_path)){
                        echo '<td><img src='.$thumbnail_path.'></td>';
                    }else{
                       echo '<td> No Image Available </td>';
                    }
                    echo '<td>' . $row['b_categ'] . '</td>
                    <td>' . $row['author_name'] . '</td>
                    <td>' . $row['b_created_date'] . '</td>
                    <td>' . status_toString($row['b_status']) . '<div class="progress">
                        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow=' . ($row['b_status']*5) .  
                        'aria-valuemin="0" aria-valuemax="100" style="width:' . $row['b_status']*5 .'%">' . $row['b_status']*5 .'% Complete </div></div></td>
                </tr>
            </tbody>';
    } // End of WHILE loop.
    
	echo '</table>';
	mysqli_free_result ($r);
	mysqli_close($dbc);
	link_section($pages, $start, $display,$sort, basename($_SERVER['PHP_SELF']));
}else{	//not a valid user
    
    echo '<p class="error">Unauthorized Access.</p>';    
}



	
include (FOOTER);
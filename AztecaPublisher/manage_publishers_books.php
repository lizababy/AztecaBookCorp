<?php
// This script retrieves records of books that a publisher published
// This can be viewed and edited by admin,publishers
// Also allows the results to be sorted in different ways.
require ('../includes/config.inc.php');
include (HEADER);
require (MYSQL);
require_once (FUN_DEFS);

$page_title = 'My Published Books';

// Number of records to show per page:
$display = 5;

// If no user_id session variable exists, redirect the user:
redirect_ifNotLoggedIn();

$id = $_SESSION['user_id'] ;

if(isset($_GET['b_id'])&&is_numeric($_GET['b_id'])){
     $b_id = $_GET['b_id'];
     get_book_details($dbc, $b_id); // Run the query.
        // Display the record being completed:
		echo '<h4 class="text-info">On Submission, this book will be published and available for members to order!
                    However you are allowed to update the basic details of the book always!
                    Please make sure you have set true price and ratings before publish and sell!<h4>
		<h5 class="text-primary">Are you sure you want to publish?</h5>';
		
		// Create the form:
		echo '<form action="manage_publishers_books.php" method="post">
	<input type="radio" name="sure" value="Yes" /> Yes 
	<input type="radio" name="sure" value="No" checked="checked" /> No
	<input type="submit" name="submit" value="Submit" />
	<input type="hidden" name="b_id" value="' . $b_id . '" />
	</form>';
}elseif (isset($_POST['b_id']) && is_numeric($_POST['b_id']) ) { // Form submission.
	$b_id = $_POST['b_id'];
} 
// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($_POST['sure'] == 'Yes') { // update status of the book.
                $b_status = 20;
                $b_pub_on = date('Y-m-d H:i:s');
		// Make the query:
		$q = "UPDATE azteca_books SET b_status = ?, b_pub_on = ? WHERE b_id = ? LIMIT 1";
                // Prepare the statement:
                $stmt = mysqli_prepare($dbc, $q);

                // Bind the variables:
                mysqli_stmt_bind_param($stmt, 'isi',$b_status,$b_pub_on,$b_id);

                // Execute the query:
                mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));

		if (mysqli_stmt_affected_rows($stmt) == 1) { // If it ran OK.

			// Print a message:
			echo '<h4 class="text-success">The book is now published. Your book will be seen by public on searching for available books and
                            Members can order from now onwards!
                            You can update the basic details of the book!<h4>';	

		} else { // If the query did not run OK.
			echo "<p class='error'>Can't complete the  action due to a system error.</p>"; // Public message.
			
		}
	
	} else { // No confirmation.
		echo '<h4 class="text-success">Cancelled Publish!</h4>';	
	}

} 


// Determine how many pages there are...
if (isset($_GET['p']) && is_numeric($_GET['p'])) { // Already been determined.
	$pages = $_GET['p'];
} else { // Need to determine.    
    // Count the number of records:
    $q = "SELECT COUNT(b_id) FROM azteca_books WHERE b_pub_id = $id";
    $pages = get_page_no($dbc,$q, $display);
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
 // Define the query:
$q = "SELECT b_id,b_title,b_categ,author_name, b_status, "
        . "DATE_FORMAT(b_created_on, '%M %d, %Y') AS b_created_date "
        . "FROM azteca_books WHERE b_pub_id = ?"
        . " ORDER BY $order_by LIMIT $start, $display";	
$stmt = mysqli_stmt_init($dbc);
// Prepare the statement:
mysqli_stmt_prepare($stmt, $q);

// Bind the variables:
mysqli_stmt_bind_param($stmt, 'i',$id);

// Execute the query:
mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
$r = mysqli_stmt_get_result($stmt);
if($role>30){
    // Table header:
    echo '</br><table class="table table-bordered">
        <thead>
            <tr bgcolor = "#E0CCE0">
                  <th><b>#</b></th>
                  <th><b><a href="manage_publishers_books.php?sort=b_title">Title</a></b></th>
                  <th><b>Thumb</b></th>
                  <th><b><a href="manage_publishers_books.php?sort=b_categ">Category</a></b></th>                   
                  <th><b><a href="manage_publishers_books.php?sort=author_name">author_name</a></b></th>
                  <th><b><a href="manage_publishers_books.php?sort=b_created_date">Date Created</a></b></th> 
                  <th><b><a href="manage_publishers_books.php?sort=b_status">Status</a></b></th>
				  <th>Process Book Image</th>
                  <th>Update</th>
                  <th>Publish/sell</th>
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
					<td align="center"><a href="process_image.php?b_id=' . $row['b_id'] . '"><span class="glyphicon glyphicon-picture"></span></a></td>
                    
					<td align="center"><a href="update_publishers_book.php?b_id=' . $row['b_id'] . '"><span class="glyphicon glyphicon-edit"></span></a></td>';
                    if($row['b_status'] !=20){
                         echo '<td align="center"><a href="manage_publishers_books.php?b_id=' . $row['b_id'] . '"><span class="glyphicon glyphicon-ok-circle"></span></a></td>';                  
                    }
                    echo '</tr>
            </tbody>';
    } // End of WHILE loop.
}else{	//not a valid user
    
    echo '<p class="error">This page has been accessed in error.</p>';    
}

echo '</table>';
/* free result */
mysqli_stmt_free_result($stmt);

/* close statement */
mysqli_stmt_close($stmt);
mysqli_free_result ($r);
mysqli_close($dbc);

link_section($pages, $start, $display,$sort, basename(htmlspecialchars($_SERVER['PHP_SELF'])));
	
include (FOOTER);
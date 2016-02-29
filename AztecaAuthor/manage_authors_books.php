<?php
// This script retrieves records of books of a particular author
// This can be viewed and edited by admin,publishers and author
// Each role will have different permissions and limited access as defined
// Also allows the results to be sorted in different ways.

require ('../includes/config.inc.php');
include (HEADER);
require (MYSQL);
require_once (FUN_DEFS);

$page_title = 'Manage My Books';

// Number of records to show per page:
$display = 5;

// If no user_id session variable exists, redirect the user:
redirect_ifNotLoggedIn();

$id = $_SESSION['user_id'];

if(isset($_GET['b_id'])&&is_numeric($_GET['b_id'])){
     $b_id = $_GET['b_id'];
     $q = "SELECT b_title FROM azteca_books WHERE b_id = ?";
     $stmt = mysqli_stmt_init($dbc);
     // Prepare the statement:
      mysqli_stmt_prepare($stmt, $q);

      // Bind the variables:
     mysqli_stmt_bind_param($stmt, 'i',$b_id);

     // Execute the query:
     mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
     $r = mysqli_stmt_get_result($stmt);
      if(mysqli_num_rows($r)==1){
         $row = mysqli_fetch_array($r);
        // Display the record being completed:
		echo '<h3 class="text-info">Book Title: $row[0]</h3>
                    <h4 class="text-info">Submission of this book, notifies publishers that the book is completed and is reay to publish!
                    However you are allowed to update the basic details of the book always!<h4>
		<h5 class="text-primary">Are you sure you want to complete submission?</h5>';
		
		// Create the form:
		echo '<form action="manage_authors_books.php" method="post">
	<input type="radio" name="sure" value="Yes" /> Yes 
	<input type="radio" name="sure" value="No" checked="checked" /> No
	<input type="submit" name="submit" value="Submit" />
	<input type="hidden" name="b_id" value="' . $b_id . '" />
	</form>';
    }
}elseif (isset($_POST['b_id']) && is_numeric($_POST['b_id']) ) { // Form submission.
	$b_id = $_POST['b_id'];
}
 
// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($_POST['sure'] == 'Yes') { // update status of the book.
                $b_status = 10;
		// Make the query:
		$q = "UPDATE azteca_books SET b_status=? WHERE b_id = ? LIMIT 1";
                // Prepare the statement:
                $stmt = mysqli_prepare($dbc, $q);

                 // Bind the variables:
                mysqli_stmt_bind_param($stmt, 'ii',$b_status,$b_id);

                // Execute the query:
                mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
                
                if (mysqli_stmt_affected_rows($stmt) == 1) { // If it ran OK.

			// Print a message:
			echo '<h4 class="text-success">The book is now finished with editing and is reay to publish. Your book will be published soon after proof reading!
                            You can update the basic details of the book!<h4>';	

		} else { // If the query did not run OK.
			echo "<p class='error'>Can't complete the  action due to a system error.</p>"; // Public message.
			
		}
	
	} else { // No confirmation.
		echo '<h4 class="text-success">Cancelled submission!</h4>';	
	}

} 


// Determine how many pages there are...
if (isset($_GET['p']) && is_numeric($_GET['p'])) { // Already been determined.
	$pages = $_GET['p'];
}else { // Need to determine.    
    // Count the number of records:
    $q = "SELECT COUNT(b_id) FROM azteca_books WHERE b_author_id = $id";
    
    $pages = get_page_no($dbc, $q, $display);
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
$q = "SELECT b_id,b_title,b_categ, b_status, DATE_FORMAT(b_created_on, '%M %d, %Y') AS b_created_date FROM azteca_books WHERE b_author_id = ? ORDER BY $order_by LIMIT $start, $display";		
$stmt = mysqli_stmt_init($dbc);
// Prepare the statement:
mysqli_stmt_prepare($stmt, $q);

// Bind the variables:
mysqli_stmt_bind_param($stmt, 'i',$id);

// Execute the query:
mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
$r = mysqli_stmt_get_result($stmt);
     
if($role>10){
    // Table header:
    echo '</br><table class="table table-bordered">
        <thead>
            <tr bgcolor = "#E0CCE0">
                  <th><b>#</b></th>
                  <th><b><a href="manage_authors_books.php?sort=b_title">Title</a></b></th>
                  <th><b><a href="manage_authors_books.php?sort=b_categ">Category</a></b></th>                  
                  <th><b><a href="manage_authors_books.php?sort=b_created_date">Date Created</a></b></th> 
                  <th><b><a href="manage_authors_books.php?sort=b_status">Status</a></b></th>                
                  <th>Edit</th>
                  <th>Remove</th>
                  <th>Complete Drafting</th>
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
                    <td>' . $row['b_created_date'] . '</td>
                    <td>' . status_toString($row['b_status']) . '<div class="progress">
                        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow=' . ($row['b_status']*5) .  
                        'aria-valuemin="0" aria-valuemax="100" style="width:' . $row['b_status']*5 .'%">' . $row['b_status']*5 .'% Complete </div></div></td>
                    <td align="center"><a href="update_authors_book.php?b_id=' . $row['b_id'] . '"><span class="glyphicon glyphicon-edit"></span></a></td>
                    <td align="center"><a href="delete_book.php?b_id=' . $row['b_id'] . '"><span class="glyphicon glyphicon-trash"></span></a></td>';
                    if($row['b_status']<10){
                        echo '<td align="center"><a href="manage_authors_books.php?b_id=' . $row['b_id'] . '"><span class="glyphicon glyphicon-check"></span></a></td>';               
                    }    
                echo '</tr>
            </tbody>';
    } // End of WHILE loop.
}else{	//not a valid user
    
    echo '<p class="error">UnAuthorized Access</p>';    
}

echo '</table>';
mysqli_free_result ($r);
mysqli_close($dbc);

link_section($pages, $start, $display,$sort, basename(htmlspecialchars($_SERVER['PHP_SELF'])));
	
include (FOOTER);
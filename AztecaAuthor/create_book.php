<?php
// This page is to create new book by an author.
// This has access to all users above level of author

require ('../includes/config.inc.php');
$page_title = 'add new book';

include (HEADER);
require_once(FUN_DEFS);
// If no user_id session variable exists or user level lesser than author, redirect the user:
redirect_ifNotLoggedIn();
if($_SESSION['user_level']<=10){
    ob_end_clean(); // Delete the buffer.
    
    redirect_user();
}

// Assume invalid values:
$b_title =$author_name = $b_categ = $b_desc  = FALSE;

// Check for form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Need the database connection:
    require_once (MYSQL);
    
    $author_id = $_SESSION['user_id'];
    
    // Trim all the incoming data:
    $trimmed = array_map('trim', $_POST);
    
    $b_title = mysqli_real_escape_string ($dbc, $trimmed['b_title']);
	$author_name = mysqli_real_escape_string ($dbc, $trimmed['author_name']);
	$b_categ = mysqli_real_escape_string ($dbc, $trimmed['b_categ']);
	$b_desc =  mysqli_real_escape_string ($dbc, $trimmed['b_desc']);
	
    if ($b_title && $b_categ && $author_name && $b_desc ) { // If everything's OK...
                    		
        // add the book in the database...
        // Make the query:
        $q = 'INSERT INTO azteca_books (b_title, author_name, b_categ,'
                . 'b_desc, b_author_id, b_created_on) VALUES (?,?,?,?,?,NOW() )';

        /// Prepare the statement:
			$stmt = mysqli_prepare($dbc, $q);

			// Bind the variables:
			mysqli_stmt_bind_param($stmt, 'ssssi', $b_title, $author_name, $b_categ, $b_desc, $author_id);
	
			
			// Execute the query:
			mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));


            if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
                         
               // Print a message:                    
                echo '<h3 class="text-success">You have completed first procedure of authoring a new book at Azteca Book Corp!</h3>                          
                     <p>You can edit your book as many times you wish. Once you finished editing and wish 
                     to publish your book you can submit your book to notify publishers that this book is ready to get published.
                     Authorized person can review your book, edit, set price, publish and sell.</p><p><br /></p>
                     <p><a href="manage_authors_books.php">Click here to View and manage books you created</a></p>';
                        
                // Include the footer and quit the script:
                include (FOOTER); 
                exit();
		                       
            } else { // If it did not run OK.
                    // Public message:
                echo '<h1>System Error</h1>
                      <p class="error">You could not add new book due to a system error. We apologize for any inconvenience.</p>'; 

            } // End of if ($r) IF.
        
        } else { // If one of the data tests failed.

            echo '<p class="error">Please try again.</p>';	
        } 

mysqli_close($dbc); // Close the database connection.    	

} // End of the main Submit conditional.
?>
<h1>Create a new Book</h1>
<?php
include '../includes/book_details_form.inc.html';

include (FOOTER); 
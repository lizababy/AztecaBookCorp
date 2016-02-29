<?php
// This script retrieves records of all books
// This can be viewed and edited by only admin
// Also allows the results to be sorted in different ways.

require ('../includes/config.inc.php');
include (HEADER);
require (MYSQL);
require_once (FUN_DEFS);

$page_title = 'Manage All Books';

// Number of records to show per page:
$display = 10;

  redirect_ifNotLoggedIn();
if ( $_SESSION['user_level']!=100) {
        echo '<p class="error">TUnAuthorized Access.</p>';
        include (FOOTER);
        exit();
        
}
$id = $_SESSION['user_id'];

if(isset($_GET['b_id'])&&is_numeric($_GET['b_id'])){
     $b_id = $_GET['b_id'];
     $b_status = get_book_details($dbc, $b_id);
     switch($b_status){
         case '5':
              // Display the record being completed:
		echo "<h4>Submission of this book, marks that the book drafting is completed and is reay to publish!<h4>
		<h5>Are you sure you want to complete submission?</h5>";
		break;
         case '10':
             echo "<h4>On submission this book will be moved to the queue of "."to publish books".
                 "This notifies that the book is ready to publish.
                  Publisher/Admin are allowed to proof read and edit the contents of the book!
                  You can set price, rating and sell the book before publishing!<h4>
		<h5>Are you sure you want to confirm this action?</h5>";
		
             
             break;
         case '15':
             echo "<h4>On Submission, this book will be published and marked as published/ready to order. After publishing 
                     this book will be available for members to order and public can see the book.
                    Please make sure you have set true price and ratings before publish and sell!<h4>
		<h5>Are you sure you want to publish?</h5>";
             break;
         default : echo 'System error! We appologize for in convenience!';
             include (FOOTER);
             exit();
                    
             
     }
        // Create the form:
    echo '<form action="manage_all_books.php" method="post">
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
                
                $b_status = get_b_detail($dbc, "b_status", $b_id);
                $b_price = get_b_detail($dbc, "b_price", $b_id);
                $b_ratings = get_b_detail($dbc,"b_ratings", $b_id);
                
                if($b_status==15 && (empty($b_price)||empty($b_ratings))){
                    echo '<h4>You have not set price and ratings!. This is required to publish/sell a book!'
                    . '<a href = "../AztecaPublisher/update_publishers_book.php?b_id=' . $b_id . '"><br>Click here to update the book</a></h4>';
                    
                }else{
                    // Make the query:
                    if($b_status == 15){
                        $b_status = 20;
                        $b_pub_on = date('Y-m-d H:i:s');
                        // Make the query:
                        $q = "UPDATE azteca_books SET b_status=?, b_pub_on = ? WHERE b_id =? LIMIT 1";;		
                        // Prepare the statement:
                        $stmt = mysqli_prepare($dbc, $q);

                        // Bind the variables:
                        mysqli_stmt_bind_param($stmt, 'ssi',$b_status,$b_pub_on,$b_id);

                        // Execute the query:
                        mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
                    }elseif($b_status ==5 ||$b_status == 10){
                       $b_status +=5;
                       $q = "UPDATE azteca_books SET b_status=? WHERE b_id = ? LIMIT 1";;		
                       // Prepare the statement:
                        $stmt = mysqli_prepare($dbc, $q);

                        // Bind the variables:
                        mysqli_stmt_bind_param($stmt, 'si',$b_status,$b_id);

                        // Execute the query:
                        mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
                    }
                    if (mysqli_stmt_affected_rows($stmt) == 1) { // If it ran OK.
                    // 
                            $b_status = get_book_details($dbc, $b_id);
                        
                            // Print a message:
                            switch($b_status){
                                case '10':
                                     // Display the record being completed:
                                       echo '<h4 class="text-success">The book is now finished with drafting and is reay to publish.Publishers and Admins can see this
                                           update and book can be published after proof reading!
                                            You can update the basic details of the book!<h4>';	
                                       break;
                                case '15':
                                    echo '<h4 class="text-success">The book is now given to publish!<h4>';
                                    break;
                                case '20':
                                    echo '<h4 class="text-success">The book is now published.Book will be seen by public on searching for available books and
                                        Members can order from now onwards!
                                        You can update the basic details of the book!<h4>';	
                                    break;
                                default:
                                    redirect_user(basename($_SERVER['PHP_SELF']));
                            }
                    } else { // If the query did not run OK.
                            echo "<p class='error'>Can't complete the  action due to a system error.</p>"; // Public message.

                    }
                }
	
	} else { // No confirmation.
		echo '<h4 class="text-success">Cancelled Action!</h4>';	
	}

} 


// Determine how many pages there are...
if (isset($_GET['p']) && is_numeric($_GET['p'])) { // Already been determined.
	$pages = $_GET['p'];
}else { // Need to determine.    
    // Count the number of records:
    $q = "SELECT COUNT(b_id) FROM azteca_books";
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
$q = "SELECT b_id,b_title,b_categ, b_status, DATE_FORMAT(b_created_on, '%M %d, %Y') AS b_created_date FROM azteca_books ORDER BY $order_by LIMIT $start, $display";		
$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc)); // Run the query.

if($role == 100){
    // Table header:
    echo '</br><table class="table table-bordered">
        <thead>
            <tr bgcolor = "#E0CCE0">
                  <th><b>#</b></th>
                  <th><b><a href="manage_all_books.php?sort=b_title">Title</a></b></th>
                  <th><b><a href="manage_all_books.php?sort=b_categ">Category</a></b></th>                  
                  <th><b><a href="manage_all_books.php?sort=b_created_date">Date Created</a></b></th> 
                  <th><b><a href="manage_all_books.php?sort=b_status">Status</a></b></th> 
                  <th align = "center"> Actions <span class="glyphicon glyphicon-tasks"</span></th>
                  <th>Update details</th>
                  <th>Remove</th>
            </tr>    
        </thead>';
            
   
    // Fetch and print all the records....
    $bg = '#FFFFFF'; 
    $si_no = 0;
    while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
            $bg = ($bg=='#FFFFFF' ? '#EEEEEE' : '#FFFFFF'); 
            echo '
            <tbody>
                <tr bgcolor="' . $bg . '">
                    <td>' . ++$si_no . '</td>
                    <td align="left"><a href="../Azteca/book_details.php?b_id=' . $row['b_id'] . '">' .$row['b_title'] . ' <span class="glyphicon glyphicon-book"</span></a></td>
                    <td>' . $row['b_categ'] . '</td>
                    <td>' . $row['b_created_date'] . '</td>
                    <td>' . status_toString($row['b_status']) . '<div class="progress">
                        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow=' . ($row['b_status']*5) .  
                        'aria-valuemin="0" aria-valuemax="100" style="width:' . $row['b_status']*5 .'%">' . $row['b_status']*5 .'% Complete </div></div></td>';
                    
                    if($row['b_status']==5){
                        echo '<td align="center"><a href="manage_all_books.php?b_id=' . $row['b_id'] . '"><span class="glyphicon glyphicon-check"></span> Complete Editing </a></td>';               
                    }else if($row['b_status']==10){
                        echo '<td align="center"><a href="manage_all_books.php?b_id=' . $row['b_id'] . '"><span class="glyphicon glyphicon-move"></span> Move to Publish </a></td>';
                    }else if($row['b_status']  == 15){
                         echo '<td align="center"><a href="manage_all_books.php?b_id=' . $row['b_id'] . '"><span class="glyphicon glyphicon-ok-circle"></span> Publish/sell </a></td>';                  
                    }else{
                        echo '<td></td>';
                    }
                    echo '<td align="center"><a href="../AztecaPublisher/update_publishers_book.php?b_id=' . $row['b_id'] . '"><span class="glyphicon glyphicon-edit"></span></a></td>';
                    
                    
                      
                echo '<td align="center"><a href="../AztecaAuthor/delete_book.php?b_id=' . $row['b_id'] . '"><span class="glyphicon glyphicon-trash"></span></a></td>
                    </tr>
            </tbody>';
    } // End of WHILE loop.
}else{	//not a valid user
    
    echo '<p class="error">This page has been accessed in error.</p>';    
}

echo '</table>';
mysqli_free_result ($r);
mysqli_close($dbc);

link_section($pages, $start, $display,$sort, basename($_SERVER['PHP_SELF']));
	
include (FOOTER);
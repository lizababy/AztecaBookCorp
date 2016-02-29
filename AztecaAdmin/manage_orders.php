<?php
// This script retrieves records of all orders
// This can be viewed and edited by only admin

require ('../includes/config.inc.php');
include (HEADER);
require (MYSQL);

$page_title = 'View orders';
  redirect_ifNotLoggedIn();
                 
   // Table header:
    echo '</br><table class="table table-bordered">
        <thead>
            <tr bgcolor = "#E0CCE0">
                  <th><b>#</b></th>
                  <th><b>Order Id</b></th>
                  <th><b>Customer name</b></th>
                  <th><b>Book Title</b></th>
                  <th><b>Order Date</b></th>
                  <th><b>Quantity</b></th>
                  <th><b>Book Price <span class="glyphicon glyphicon-usd"></span></b></th>
                  <th><b>Sub Total <span class="glyphicon glyphicon-usd"></span></b></th>
            </tr>    
        </thead>';
            
   
    // Fetch and print all the records....
    $bg = '#FFFAFA'; 
    $si_no = 0;
    
    $sum = 0;
    $q = "SELECT * FROM azteca_orders" ;
    $r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
	
    // create the order table
   while ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC))
   {    
       $bg = ($bg=='#FFFAFA' ? '#FFFFFF' : '#FFFAFA'); 
        $sum += $row['total_price'];
       	$b_title = get_b_detail($dbc, 'b_title', $row['b_id']);
        $b_price = get_b_detail($dbc, 'b_price', $row['b_id']);
        $user_full_name = get_user_full_name($dbc,$row['user_id']);
        		
        // Display the table$bg = ($bg=='#FFFAFA' ? '#FFFFFF' : '#FFFAFA'); 
            echo '<tbody>
                <tr bgcolor="' . $bg . '">
            <td>' . ++$si_no . '</td>
            <td>'. $row['order_id'] . '</td>
            <td>'. $user_full_name . '</td>
            <td>'. $b_title . '</td>
            <td>' . $row['order_date'] . '</td>
            <td>' . $row['b_qty'] . '</td>
            <td>at ' . $b_price . ' each </td> 
            <td>'.number_format($row['total_price'], 2). '$</td></tr>';
  
   }
   // Display the total
   echo ' <tr><td colspan="8" class="bg-primary" style="text-align:right">

   Total = '.number_format($sum,2).'<span class="glyphicon glyphicon-usd"></span></td> </tr>
            </tbody>
</table>';
   
mysqli_close($dbc);


include (FOOTER);


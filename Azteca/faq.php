<?php
// Include the configuration file:
require ('../includes/config.inc.php'); 

// Set the page title and include the HTML header:
$page_title = 'About';
include (HEADER);
echo '<h2 class="text-info">FAQS</h2><hr>';
echo '<ol type="1">
  <li>Can I see and order books without registering?</li>
  <p>You can see the books that the company published. But if you want to order a book you need to register</p>
  <li>What should i need to order a book?</li>
  <p>You need to first register and login. Then you can select book of your choice and add to cart</p>
  <li>What do i need to do to author a book?</li>
  <p>You can author a book only if you are an author.Author can add new book and set the status 
  complete when they finished with rafting.</p>
  <li>How can i become an author?</li>
  <p>You can become an author or publisher by requesting the admin</p>
  <li>How can i publish a book?</li>
  <p>In order to publish a book, you should be assigned as a role of publisher by admin.</p>
  <li>Can an author decide price of a book?</li>
  <p>No. An author is not allowed to set price of his book. It is st by publisher</p>
  <li>Can a publisher author a book?</li>
  <p>Yes a publisher can author a book as well as publish a book.</p>
  <li>Can a member see unpublished books?</li>
  <p>No a. A member can only see published books.</p>
  <li>Who all can see unpublished books?</li>
  <p>A publisher and admin can see unpublished books</p>
  


</ol>  ';
    include (FOOTER);
    
<?php
// Include the configuration file:
require ('../includes/config.inc.php'); 

// Set the page title and include the HTML header:
$page_title = 'Member Benefits';
include (HEADER);
echo '<h2 class="text-info">Member Benefits</h2><hr>';
echo '<p>Azteca Book Corp is a website to help members to order books,'
. 'Authors to author,Publisher to publish and sell books. All these roles are hierarchial.'
        . 'An author is a member and author.A publisher ia a member, author and a publisher. An admin will manage the things around all these roles.'
        . 'Followingare the benefits of each role:</p>';
echo '<br><h3 class="text-info">Public</h3><p>Unauthenticated users (this is the public visiting  website, or a user that has not registered with your website yet</p>';
echo '<br><h3 class="text-info">Member</h3><p>Authenticated user with minimal permission. A new user that can order a book from the site</p>';
echo '<br><h3 class="text-info">Author</h3><p>authenticated user with the ability to add a new book. This user can edit and delete any book that they are the author of. This user cannot create edit or delete the price of a book</p>';
echo '<br><h3 class="text-info">Publisher</h3><p>authenticated user with all the abilities of an author, but also can set edit and change the price of a book. This user can edit if a book is displayed on the best seller list. Publishers have the ability to mark a book as published.</p>';
echo '<br><h3 class="text-info">Administrator</h3><p>authenticated super user with all the abilities of a publisher, but can also promote lower level users to any role below theirs</p>';

include (FOOTER);
    
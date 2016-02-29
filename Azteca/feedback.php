<?php
// Include the configuration file:
require ('../includes/config.inc.php'); 

// Set the page title and include the HTML header:
$page_title = 'About';
include (HEADER);
echo '<h2 class="text-info">ABOUT AZTECA BOOKCORP</h2><hr>';
echo '<h4>AztecaBookCorp is the leading provider of supported self-publishing services'
. ' for authors around the globe, with over 1,000 titles released. Whether you'
        . ' dream of seeing your book in bookstores, on TV, on the radio, or '
        . 'adapted into a film, AztecaBookCorp is committed to providing the tools'
        . ' and services to help you get started and realize your publishing dreams. '
        . 'For more information '
. 'or to start your publishing journey please sign up and login</h4>';
 
    include (FOOTER);
    
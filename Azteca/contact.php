<?php

    // Include the configuration file:
    require ('../includes/config.inc.php'); 

    // Set the page title and include the HTML header:
    $page_title = 'Contact!';
    include (HEADER);
    
            echo '<h2 class="text-info">Contact US <hr></h2>';
        echo '<h4> Mailing Address:<br><h4>
<h5>AztecaBookCorp<br>
8550 Campanile Drive<br>
San Diego, CA 92120</h5>

<h4>Phone Numbers:</h4>
<h5>Toll-free: 888-888-8888<br>
If calling from outside the US: 892-399-0000</h5>

<br><h4>EmailAddress:</h4>
<h5>administrator@abcorp.com</h5>';
    
    include (FOOTER);
    
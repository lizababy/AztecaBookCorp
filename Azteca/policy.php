<?php
// Include the configuration file:
require ('../includes/config.inc.php'); 

// Set the page title and include the HTML header:
$page_title = 'Policy';
include (HEADER);
echo '<h2 class="text-info">Privacy Policy</h2><hr>';
echo '<h4>AztecaBookCorp respects the privacy of all'
. ' personal information that you provide via our website. We do not sell or disclose '
        . 'our customer lists or information about our customers to any advertisers or'
        . ' third parties. We do collect personal information (name, address, phone number,'
        . ' fax number, e-mail address, site activity, and any other information you may disclose) '
        . 'about our customers. This information is used for internal review purposes '
        . '(such as improving our website, customer service, and systems administration). '
        . 'We build mailing lists using this information on a completely voluntary basis '
        . 'that enables us to notify our customers of promotions or new developments. Unless '
        . 'we specifically indicate otherwise, these mailing lists and any other personal '
        . 'information will be used solely by AztecaBookCorp. and its employees '
        . 'for internal purposes and will not be published, sold, or otherwise transferred to '
        . 'third parties. We also use “cookies” that allow you to use our services with greater'
        . ' ease and efficiency. Cookies are small pieces of information that are stored in a '
        . 'designated file on your computer. Our cookies contain no personal information,'
        . ' but are transferred to your hard drive in order to facilitate your use of '
        . 'our site. They may also provide us with information about your site usage. '
        . 'We reserve the right to modify our Privacy Policy at any time.'
. ' If we ever change our Privacy Policy, we will post any changes on this page.</h4>';
  
    include (FOOTER);
    
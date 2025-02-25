<?php
require_once __DIR__ . '/init.php';

// Check if page variables are set
$page = $page ?? '';
$subpage = $subpage ?? '';
$pageTitle = $pageTitle ?? 'Kisan.ai';

// Include the header part of dashboard
include 'dashboard_header.php';
?>

<!-- Main Content Area -->
<div class="content-wrapper">
    <?php 
    // Output the content if it exists
    if (isset($content)) {
        echo $content;
    }
    ?>
</div>

<?php
// Include the footer part of dashboard
include 'dashboard_footer.php';
?> 
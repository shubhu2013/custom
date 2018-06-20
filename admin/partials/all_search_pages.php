<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Custom_Search
 * @subpackage Custom_Search/admin/partials
 */
//http://wpengineer.com/2426/wp_list_table-a-step-by-step-guide/
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
<?php include_once('notification.php'); ?>
	<div id="poststuff" class="">

        <div id="post-body">
            <div id="post-body-content">
             <h1 class="wp-heading-inline">Search Pages</h1>
             <a href="admin.php?page=new-search-page" class="page-title-action">Add New</a>
            <hr class="wp-header-end">
             <?php
              $myListTable = new Custom_List_Table();
		          $myListTable->prepare_items(); ?>
              <form method="post">
                <input type="hidden" name="page" value="ttest_list_table">
                <?php
                $myListTable->search_box( 'search', 'search_id' );
              $myListTable->display(); 
              echo '</form>'; 
             ?>
            </div>
        </div>
    </div>        
</div>
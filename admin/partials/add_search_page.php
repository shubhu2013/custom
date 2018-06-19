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

$text_before ='';
$text_after  ='';
// $_POST['text_before'];
if(!empty($_POST)){
	print_r($_POST);
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

<div class="update-nag notice">
    <p>This................</p>
</div>

	<div id="poststuff" class="">
        <div id="post-body">
            <div id="post-body-content">
            	<form method="post" id="reg_form" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                    <?php
			        // this prevent automated script for unwanted spam
			        if(function_exists('wp_nonce_field'))
			            wp_nonce_field('search_page_reg', 'search_page_reg');
			        ?>
                     
                    <table class="form-table">

                        <tr valign="top">
                            <th scope="row">
                                <label><?php _e("Search Keyword", $this->plugin_name); ?></label>
                            </th>
                            <td>
                                <input type="text" name="keyword" value='<?php echo (!empty($apiResponse["keyword"])) ? $apiResponse["keyword"] : $user_info["keyword"]; ?>' class='wide' placeholder="Enter here the keyword" required />
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">
                                <label><?php _e("Count of Results", $this->plugin_name); ?></label>
                            </th>
                            <td>
                                <select name="count" value='<?php echo (!empty($apiResponse["count"])) ? $apiResponse["count"] :  $user_info["count"]; ?>' class='wide' required>
                                <option value="">Select Count</option>
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>                            
                                </select>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">
                                <label><?php _e("Title", $this->plugin_name); ?></label>
                            </th>
                            <td>
                                <input type="text" name="title" value='<?php echo (!empty($apiResponse["title"])) ? $apiResponse["title"] : $user_info["title"]; ?>' class='wide' placeholder="Enter here title" />
                              
                            </td>
                        </tr>

                         <tr valign="top">
                            <th scope="row">
                                <label><?php _e("Meta Description", $this->plugin_name); ?></label>
                            </th>
                            <td>
                                <input type="text" name="meta_desc" value='<?php echo (!empty($apiResponse["meta_desc"])) ? $apiResponse["meta_desc"] : $user_info["meta_desc"]; ?>' class='wide' placeholder="Enter here Meta Description" />
                              
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">
                                <label><?php _e("Text Before", $this->plugin_name); ?></label>
                            </th>
                            <td>
                               <?php echo wp_editor( $text_before, 'text_before' );?>
                              
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">
                                <label><?php _e("Text After", $this->plugin_name); ?></label>
                            </th>
                            <td>
                               <?php echo wp_editor( $text_after, 'text_after' );?>
                              
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">
                                <label><?php _e("Show Active Ingredients", $this->plugin_name); ?></label>
                            </th>
                            <td>
                               <input type="checkbox" name="active_ingredient" value="1">
                            </td>
                        </tr>

                    </table>


                    <div>
                        <p class="submit">
                            <input type="submit" class="button-primary" value="<?php _e('Save Form') ?>" />
                        </p>
                    </div>		
                </form>
            </div>
        </div>
    </div>        
</div>
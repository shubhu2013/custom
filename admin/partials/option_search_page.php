<link rel='stylesheet' href='<?php echo plugins_url().'/custom-search/admin/css/select2.min.css'; ?>' type='text/css' media='all' />

<script type='text/javascript' src='<?php echo plugins_url().'/custom-search/admin/js/select2.min.js'; ?>'></script>
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
 
 /**
 * Check if WooCommerce is active
 **/
 
$products = array(); 
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    $query = new WC_Product_Query( array(
	    'limit' => -1,
	    'orderby' => 'ID',
	    'order' => 'ASC',
	    'status' => 'publish',
	    //'return' => 'ids,title',
	) );
	$products = $query->get_products();
}else{
	$this->cs_add_notice("WooCommerce Plugin is not active. Please activate.",'error');
}
 
 
global $wpdb;
$table = $wpdb->prefix.'search_forms';
if(isset($_POST['submit_page'])) {
    if ( ! isset( $_POST['search_page_nonce_field'] ) || ! wp_verify_nonce( $_POST['search_page_nonce_field'], 'search_page_action' ) ) {
       print 'Sorry, your nonce did not verify.';
       exit;

    } else {
         print_r($_POST);exit;
         $per_page    = trim($_POST['cs_pro_per_page']);
         $order_col   = trim($_POST['cs_pro_order_col']);
         $order_by    = trim($_POST['cs_pro_order_by']);
         $pro_lists   = $_POST['cs_pro_lists'];
         
         $pro_lists = maybe_serialize($pro_lists);
         
         update_option('cs_pro_per_page'  , $per_page);
         update_option('cs_pro_order_col' , $order_col);
         update_option('cs_pro_order_by'  , $order_by);
         update_option('cs_pro_lists'     , $pro_lists);
         $this->cs_add_notice("Options saved Successfully",'note');
         //wp_redirect("admin.php?page=search-pages");exit;
    }
}
$perPage  =  get_option('cs_pro_per_page');
$orderCol = get_option('cs_pro_order_col');
$orderBy  = get_option('cs_pro_order_by');
$proLists = maybe_unserialize( get_option('cs_pro_lists') );

/*var_dump($perPage);
var_dump($orderCol);
var_dump($orderBy);
var_dump($proLists);*/
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

<?php include_once('notification.php'); ?>

	<div id="poststuff" class="">
        <div id="post-body">
          <h1 class="wp-heading-inline">Manage Options</h1>
            <div id="post-body-content">
            	<form method="post" id="reg_form" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                    <?php
			        // this prevent automated script for unwanted spam
			        if(function_exists('wp_nonce_field'))
                        wp_nonce_field( 'search_page_action', 'search_page_nonce_field' );
			        ?>
                     
                    <table class="form-table">

                        <tr valign="top">
                            <th scope="row">
                                <label><?php _e("Products Per Page", $this->plugin_name); ?> </label>
                            </th>
                            <td>
                                <input type="number" name="cs_pro_per_page" value='<?php echo ($perPage) ? $perPage : '10'; ?>' class='wide' min="1" />
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">
                                <label><?php _e("Exact List of Products", $this->plugin_name); ?></label>
                            </th>
                            <td>
                                <select name="cs_pro_lists[]" multiple="multiple" data-placeholder="Select Product(s)" class='wide select2'>
                                <?php foreach($products as $product){ ?>
                                <option value="<?php echo $product->get_id();?>" <?php echo (get_option('cs_pro_lists') && in_array($product->get_id(),$proLists)) ? 'selected' : ''; ?> ><?php echo $product->get_name();?></option>
                                <?php } ?>              
                                </select>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">
                                <label><?php _e("Sort Product Order By", $this->plugin_name); ?>  </label>
                                                 
                            </th>
                            <td>
                                <select name="cs_pro_order_col" data-placeholder="Select Product Column" class='wide select2' style="width: 25%;">
                                <option value="ID" <?php echo ($orderCol && $orderCol=='ID') ? 'selected' :  ''; ?>>ID</option>
                                <option value="name" <?php echo ($orderCol && $orderCol=='name') ? 'selected' :  ''; ?>>Name</option>
                                <option value="price" <?php echo ($orderCol && $orderCol=='price') ? 'selected' :  ''; ?>>Price</option>
                                <option value="date" <?php echo ($orderCol && $orderCol=='date') ? 'selected' :  ''; ?>>Date</option>
                                 </select>
                                 
                                 <select name="cs_pro_order_by" class='wide select2' style="width: 25%;">
                                <option value="ASC" <?php echo ($orderBy && $orderBy=='ASC') ? 'selected' :  ''; ?>>Ascending</option>
                                <option value="DESC" <?php echo ($orderBy && $orderBy=='DESC') ? 'selected' :  ''; ?>>Descending</option>
                                 </select> 
                              
                            </td>
                        </tr>

                    </table>


                    <div>
                        <p class="submit">
                            <input type="submit" name="submit_page" class="button button-primary button-large" value="<?php _e('Save Changes') ?>" />
                        </p>
                    </div>		
                </form>
            </div>
        </div>
    </div>        
</div>
<script>
   jQuery('document').ready(function(){
   		jQuery('.select2').select2();
   });
</script>
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
$text_before ='';
$text_after  ='';
$active_ingredient = '0';
$table = $wpdb->prefix.'search_forms';
if(isset($_POST['submit_page'])) {
    if ( ! isset( $_POST['search_page_nonce_field'] ) || ! wp_verify_nonce( $_POST['search_page_nonce_field'], 'search_page_action' ) ) {
       print 'Sorry, your nonce did not verify.';
       exit;

    } else {
         //print_r($_POST);
         $keyword = trim($_POST['keyword']);
         $count   = trim($_POST['count']);
         $title   = trim($_POST['title']);
         $meta_desc = trim($_POST['meta_desc']);
         $text_before = wp_kses_post( stripslashes($_POST['text_before']));
         $text_after = wp_kses_post( stripslashes($_POST['text_after']));
         $active_ingredient =  (array_key_exists("active_ingredient",$_POST))?'1':'0';
         
         $per_page    = trim($_POST['cs_pro_per_page']);
         $order_col   = trim($_POST['cs_pro_order_col']);
         $order_by    = trim($_POST['cs_pro_order_by']);
         $pro_lists   = $_POST['cs_pro_lists'];
         
         $pro_lists = maybe_serialize($pro_lists);

         $data = array(
            'keyword' => $keyword,
            'count' => $count,
            'title' => $title,
            'meta_desc' => $meta_desc,
            'text_before' => $text_before,
            'text_after' => $text_after,
            'per_page' => $per_page,
            'product_lists' => $pro_lists,
            'order_col' => $order_col,
            'order_by' => $order_by,
            'active_ingredient' => $active_ingredient,
            'author' => get_current_user_id(),
        );
        if(isset($_REQUEST['action']) && $_REQUEST['action']=='edit'){
             $id = $_REQUEST['id'];
             $wpdb->update($table,$data, array('id' =>$id ));
             $this->cs_add_notice("Form updated Successfully",'note');
             wp_redirect("admin.php?page=search-pages");exit;
        }else{
            if($wpdb->insert($table,$data)){
             $this->cs_add_notice("New Form Inserted Successfully",'note');
             wp_redirect("admin.php?page=search-pages");
             exit;
             }else{
                $this->cs_add_notice("Form not Inserted! Please try again.",'error');
             }
        }
    }
}
$proLists = array();
$perPage  = '';
$orderCol = '';
$orderBy  = '';
if(isset($_REQUEST['action']) && $_REQUEST['action']=='edit'){
    $id = $_REQUEST['id'];
    if(!$id)
        return;

    $sql = "SELECT * FROM $table WHERE id='$id'";
    $results = $wpdb->get_results($sql,'ARRAY_A');
    if($wpdb->num_rows > 0){
        $results = $results[0];
        $text_before = $results['text_before'];
        $text_after  = $results['text_after'];
        $proLists = maybe_unserialize($results['product_lists']);
    }
    //print_r($results);
}

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

<?php include_once('notification.php'); ?>

	<div id="poststuff" class="">
        <div id="post-body">
          <h1 class="wp-heading-inline">Search Page Detail</h1>
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
                                <label><?php _e("Search Keyword", $this->plugin_name); ?> * </label>
                            </th>
                            <td>
                                <input type="text" name="keyword" value='<?php echo (!empty($results)) ? $results["keyword"] : ''; ?>' class='wide' placeholder="Enter here the keyword" required />
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">
                                <label><?php _e("Count of Results", $this->plugin_name); ?> * </label>
                            </th>
                            <td>
                                <select name="count" class='wide select2' data-placeholder="Select count" required>
                                <option value="5" <?php echo (!empty($results) && $results["count"]=='5') ? 'selected' :  ''; ?>>5</option>
                                <option value="10" <?php echo (!empty($results) && $results["count"]=='10') ? 'selected' :  ''; ?>>10</option>
                                <option value="15" <?php echo (!empty($results) && $results["count"]=='15') ? 'selected' :  ''; ?>>15</option>
                                <option value="20" <?php echo (!empty($results) && $results["count"]=='20') ? 'selected' :  ''; ?>>20</option>                            
                                </select>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">
                                <label><?php _e("Title", $this->plugin_name); ?> * </label>
                            </th>
                            <td>
                                <input type="text" name="title" value='<?php echo (!empty($results)) ? $results["title"] : ''; ?>' class='wide' placeholder="Enter here title" />
                              
                            </td>
                        </tr>

                         <tr valign="top">
                            <th scope="row">
                                <label><?php _e("Meta Description", $this->plugin_name); ?> * </label>
                            </th>
                            <td>
                                <input type="text" name="meta_desc" value='<?php echo (!empty($results)) ? $results["meta_desc"] : ''; ?>' class='wide' placeholder="Enter here Meta Description" />
                              
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">
                                <label><?php _e("Products Per Page", $this->plugin_name); ?> </label>
                            </th>
                            <td>
                                <input type="number" name="cs_pro_per_page" value='<?php echo (!empty($results)) ? $results["per_page"] : '10'; ?>' class='wide' min="1" />
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">
                                <label><?php _e("Exact List of Products", $this->plugin_name); ?></label>
                            </th>
                            <td>
                                <select name="cs_pro_lists[]" multiple="multiple" data-placeholder="Select Product(s)" class='wide select2'>
                                <?php foreach($products as $product){ ?>
                                <option value="<?php echo $product->get_id();?>" <?php echo (!empty($results) && in_array($product->get_id(),$proLists)) ? 'selected' : ''; ?> ><?php echo $product->get_name();?></option>
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
                                <option value="ID" <?php echo (!empty($results) && $results["order_col"]=='ID') ? 'selected' :  ''; ?> >ID</option>
                                <option value="name" <?php echo (!empty($results) && $results["order_col"]=='name') ? 'selected' :  ''; ?>>Name</option>
                                <option value="price" <?php echo (!empty($results) && $results["order_col"]=='price') ? 'selected' :  ''; ?>>Price</option>
                                <option value="date" <?php echo (!empty($results) && $results["order_col"]=='date') ? 'selected' :  ''; ?>>Date</option>
                                 </select>
                                 
                                 <select name="cs_pro_order_by" class='wide select2' style="width: 25%;">
                                <option value="ASC" <?php echo (!empty($results) && $results["order_by"]=='ASC') ? 'selected' :  ''; ?>>Ascending</option>
                                <option value="DESC" <?php echo (!empty($results) && $results["order_by"]=='DESC') ? 'selected' :  ''; ?>>Descending</option>
                                 </select> 
                              
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
                               <input type="checkbox" name="active_ingredient" value="1" <?php echo (!empty($results) && $results["active_ingredient"]=='1') ? 'checked' :  ''; ?>>
                            </td>
                        </tr>

                    </table>


                    <div>
                        <p class="submit">
                            <input type="submit" name="submit_page" class="button button-primary button-large" value="<?php _e('Save Form') ?>" />
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
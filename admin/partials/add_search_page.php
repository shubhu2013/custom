<link rel='stylesheet' href='<?php echo plugins_url().'/custom-search/admin/css/select2.min.css'; ?>' type='text/css' media='all' />
<link rel='stylesheet' href='<?php echo plugins_url().'/custom-search/admin/css/multi-select.css'; ?>' type='text/css' media='all' />
<script type='text/javascript' src='<?php echo plugins_url().'/custom-search/admin/js/select2.min.js'; ?>'></script>
<!--<script type='text/javascript' src='<?php echo plugins_url().'/custom-search/admin/js/jquery.multi-select.js'; ?>'></script>
<script type='text/javascript' src='<?php echo plugins_url().'/custom-search/admin/js/jquery.quicksearch.js'; ?>'></script>-->
<style type="text/css">
.ms-container .ms-selection {
    float: right;
    width: 309px;
}
.ms-container .ms-selectable {
    width: 45%;
}
.ms-container{
  width: 700px;
}
.ms-container .ms-selection li.ms-elem-selection{
  width: 215px;
}
input[type=number] {
    height: 36px;
    line-height: 1;
    width: 50px;
    float: right;
    margin-top: -52px;
}
.select-header{
	text-align: center;
    font-size: 16px;
    float: right;
    font-weight: 700;
}
.clearfix{
	clear:both;
}
.ms-container .ms-list{
	height: 400px;
}
img.pro-image{
	width:50px;
	vertical-align: middle;
}
.ms-container .ms-selectable li.ms-elem-selectable:hover{
	cursor: pointer;
	background-color: #9999994a;
	
}
.ms-container .ms-selection li.ms-elem-selection:hover{
	cursor: pointer;
	background: transparent url('<?php echo plugins_url(); ?>/custom-search/admin/img/remove.png') no-repeat 100% 50%;
}
</style>
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
         $pro_array = array();
         foreach($products as $pid){
		  $order =	$_POST['order-'.$pid->get_id()];
		  if($order!=''){
		    $pro_array[$pid->get_id()] = $order;
		  }
		 }
		/*print_r($pro_array);
		die;*/
         $keyword = trim($_POST['keyword']);
         $count   = trim($_POST['count']);
         $title   = trim($_POST['title']);
         $meta_desc = trim($_POST['meta_desc']);
         $text_before = wp_kses_post( stripslashes($_POST['text_before']));
         $text_after = wp_kses_post( stripslashes($_POST['text_after']));
         $active_ingredient =  (array_key_exists("active_ingredient",$_POST))?'1':'0';
         
         $pro_lists = maybe_serialize($pro_array);

         $data = array(
            'keyword' => $keyword,
            'count' => $count,
            'title' => $title,
            'meta_desc' => $meta_desc,
            'text_before' => $text_before,
            'text_after' => $text_after,
            'product_lists' => $pro_lists,
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
    print_r($proLists);
    echo "<br>";
    asort($proLists);
    print_r($proLists);
    
}

function get_product_name($product_id,$name){
	
	return  (get_post_meta($product_id,'en_productname',true)) ? get_post_meta($product_id,'en_productname',true):$name;
	
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

                        <tr valign="top">
                            <th scope="row">
                                <label><?php _e("Select Products to be included", $this->plugin_name); ?></label>
                            </th>
                            <td>
                                
                                <div class="ms-container" id="ms-multiSelect">
                                <div class="ms-selectable">
                                <span class="select-header">Include</span><div class="clearfix"></div>
                                <ul class="ms-list" tabindex="-1">
                                <?php foreach($products as $product){ 
                                
                                $order =  (array_key_exists($product->get_id(),$proLists))? $proLists[$product->get_id()]:false;
                                ?>
                                <li data-id="<?php echo $product->get_id();?>" id="<?php echo $product->get_id();?>-selectable" class="ms-elem-selectable" <?php echo (!empty($results) && $order)? 'style=display:none;':''; ?>>
                                <img src="<?php echo get_the_post_thumbnail_url($product->get_id(),'post-thumbnail');?>" class="pro-image"/>
                                <?php echo get_product_name($product->get_id(),$product->get_name());?></li>
                                <?php } ?>
                               
                                </ul>
                                </div>
                                
                                <div class="ms-selection">
                                <span class="select-header">Sort Order</span>
                                <div class="clearfix"></div>
                                <ul class="ms-list" tabindex="-1" id="selectedDiv">
                                <?php 
                                // for soring order with ascdending
                                if(!empty($proLists) && !empty($results)){
                                  foreach ($proLists as $pID => $pOrder) { ?>

                                    <li data-id="<?php echo $pID;?>" id="<?php echo $pID;?>-selection" class="ms-elem-selection" <?php echo (!empty($results) && $order)? 'style=display:block;':'style=display:none;'; ?>>

                                  <img src="<?php echo get_the_post_thumbnail_url($pID,'post-thumbnail');?>" class="pro-image"/>

                                  <?php echo get_product_name($pID, get_the_title($pID));?>

                                  </li>

                                  <input type="number" min="1" class="order-num order-<?php echo $pID;?>" name="order-<?php echo $pID;?>" value="<?php echo $pOrder; ?>"  />

                                <?php } } ?>

                                  <?php
                                  // hide rest of products
                                   foreach($products as $product){ 

                                  $restPro =  (array_key_exists($product->get_id(),$proLists))? true:false;
                                  if(!$restPro){
                                  ?>
                                  <li data-id="<?php echo $product->get_id();?>" id="<?php echo $product->get_id();?>-selection" class="ms-elem-selection" style="display:none;">

                                  <img src="<?php echo get_the_post_thumbnail_url($product->get_id(),'post-thumbnail');?>" class="pro-image"/>

                                  <?php echo get_product_name($product->get_id(),$product->get_name());?>

                                  </li>

                                  <input type="number" min="1" class="order-num order-<?php echo $product->get_id();?>" name="order-<?php echo $product->get_id();?>" value="" style="display:none;" />

                                  <?php } } ?>
                                </ul>
                                </div>
                                </div>
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
var countArr = [];
   jQuery('document').ready(function(){

   		jQuery('.select2').select2();

      jQuery(".ms-elem-selectable").on('click',function(){
      	var ele_id = jQuery(this).data('id');
      	//console.log(ele_id);
      	jQuery("#"+ele_id+"-selection").show();
      	jQuery(".order-"+ele_id).attr('required','required');
         var maxValues = getMaxOrderValueFromSelected();
      	jQuery(".order-"+ele_id).val(++maxValues);
      	jQuery(".order-"+ele_id).show();
      	jQuery(this).hide();
      	
      	var elem = document.getElementById('selectedDiv');
  		  elem.scrollTop = elem.scrollHeight;

        
      });
      
      jQuery(".ms-elem-selection").on('click',function(){
      	var ele_id = jQuery(this).data('id');
      	//console.log(ele_id);
      	jQuery("#"+ele_id+"-selectable").show();
      	jQuery(".order-"+ele_id).hide();
      	jQuery(".order-"+ele_id).removeAttr('required');
      	jQuery(".order-"+ele_id).val('');
      	jQuery(this).hide();
      });
      
      
   });

   function getMaxOrderValueFromSelected(){
     countArr= [];
     jQuery(".order-num").each(function(){ 
      var o = jQuery(this).val();
        console.log(o); 
        countArr.push(o);
    });
     console.log(countArr);
     console.log(Math.max.apply(undefined, countArr));
      return Math.max.apply(undefined, countArr);
   }
</script>
<?php
get_header(); ?>
<?php
global $wpdb;

$table = $wpdb->prefix.'search_forms';
$productTable = $wpdb->prefix.'search_form_products';

$keyword = urldecode(get_query_var('keywords'));

if (! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	exit;
}
if (isset($_GET['pageno'])) {
    $pageno = $_GET['pageno'];
} else {
    $pageno = 1;
}
$no_of_records_per_page = 10;
 $offset = ($pageno-1) * $no_of_records_per_page;
 $sql = "SELECT * FROM $table AS sf JOIN $productTable AS pt ON sf.id = pt.form_id WHERE sf.keyword='$keyword' ORDER BY pt.product_order ASC  LIMIT $offset , $no_of_records_per_page";



$totalsql = "SELECT * FROM $table AS sf JOIN $productTable AS pt ON sf.id = pt.form_id WHERE sf.keyword='$keyword' ORDER BY pt.product_order ASC";

$results = $wpdb->get_results($sql,'ARRAY_A');

$totalresults = $wpdb->get_results($totalsql,'ARRAY_A');

//$total_pages_sql = $sql;
 $total_rows = count($totalresults);
 $total_pages = ceil($total_rows / $no_of_records_per_page);
//print_r($results);
?>
<div class="wrap cs-wrap">
<p class="woocommerce-result-count">Showing <?php echo count($results);?> results</p>
<ul class="products columns-<?php echo esc_attr( wc_get_loop_prop( 'columns' ) ); ?>">
	<?php	

		if(!empty($results)){
	        foreach($results as $prod){
				$product_id = $prod['product_id']; 
				$product = new WC_product($product_id);
				?>

				<li <?php echo wc_product_class('custom',$product_id); ?>>
					<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), array('250','250') );?>
					<a href="<?php  echo $product->get_permalink(); ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
    				<img src="<?php  echo $image[0]; ?>" data-id="<?php echo $product_id; ?>" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail wp-post-image" alt="">

    				<h2 class="woocommerce-loop-product__title"><?php  echo $prod['name']; ?></h2>
					</a>
					<p class="cs-p-text"><?php  echo $prod['text_before']; ?></p>
						<?php echo $product->get_price_html();?>
					<p class="cs-p-text"><?php  echo $prod['text_after']; ?></p>
					<?php 
					 if ($product->is_in_stock() && $product->add_to_cart_url() != '') {
					 	echo do_shortcode('[add_to_cart id="'.$product_id.'" show_price = "false" style= "border:0px;padding: 5px;"]');
					 }
					?>

				</li>
			
			<?php } ?>
			
	<?php  }else{ ?>
	    	<p class="woocommerce-info"><?php _e( 'No products were found matching your selection.', 'woocommerce' ); ?></p>
	   <?php }
    ?>

</ul>
<ul class="pagination">
        <li><a href="?pageno=1"><?php _e( 'First', 'custom-search' ); ?></a></li>
        <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
            <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>"><?php _e( 'Prev', 'custom-search' ); ?></a>
        </li>
        <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
            <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>"><?php _e( 'Next', 'custom-search' ); ?></a>
        </li>
        <li><a href="?pageno=<?php echo $total_pages; ?>"><?php _e( 'Last', 'custom-search' ); ?></a></li>
</ul>
<!--/.products-->

</div>
<?php
get_footer();
?>
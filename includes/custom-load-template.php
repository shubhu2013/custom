<?php
get_header();?>
<link rel='stylesheet' id='woocommerce-layout-css'  href='http://localhost/wordpress/wp-content/plugins/woocommerce/assets/css/woocommerce-layout.css?ver=3.4.3' type='text/css' media='all' />
<link rel='stylesheet' id='woocommerce-smallscreen-css'  href='http://localhost/wordpress/wp-content/plugins/woocommerce/assets/css/woocommerce-smallscreen.css?ver=3.4.3' type='text/css' media='only screen and (max-width: 768px)' />
<link rel='stylesheet' id='woocommerce-general-css'  href='//localhost/wordpress/wp-content/plugins/woocommerce/assets/css/twenty-seventeen.css?ver=3.4.3' type='text/css' media='all' />
<?php 
global $wpdb;

$table = $wpdb->prefix.'search_forms';
$productTable = $wpdb->prefix.'search_form_products';

$keyword = urldecode(get_query_var('keywords'));


if (! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	exit;
}
$ids = array();
 $sql = "SELECT * FROM $table AS sf JOIN $productTable AS pt ON sf.id = pt.form_id WHERE sf.keyword='$keyword' ORDER BY pt.product_order ASC";
$results = $wpdb->get_results($sql,'ARRAY_A');
    if($wpdb->num_rows > 0){
        
        
        
        foreach($results as $products){
			$ids[] = $products['product_id'];
		}
		
		print_r($ids);
		//$ids =  implode(',',$ids);
    }
    //exit;
?>
<div class="wrap">
<ul class="products columns-4">
	<?php	
	
	 $paged = get_query_var( 'page' ) ? get_query_var( 'page' ) : 1;
	//var_dump(get_query_var( 's' ));
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => 12,
			'post__in'=> $ids,
			'nopaging'    => false,
			'paged'       => $paged,
			'orderby'   =>'none'
			);
		$loop = new WP_Query( $args );
		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) : $loop->the_post();
				echo the_ID();
				wc_get_template_part( 'content', 'product' );
			endwhile;
		} else {
			echo __( 'No products found' );
		}?>
		<div class="pagination">
    <?php 
        echo paginate_links( array(
            'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
            'total'        => $loop->max_num_pages,
            'current'      => max( 1, get_query_var( 'paged' ) ),
            'format'       => '?paged=%#%',
            'show_all'     => false,
            'type'         => 'plain',
            'end_size'     => 2,
            'mid_size'     => 1,
            'prev_next'    => true,
            'prev_text'    => sprintf( '<i></i> %1$s', __( 'Newer Posts', 'text-domain' ) ),
            'next_text'    => sprintf( '%1$s <i></i>', __( 'Older Posts', 'text-domain' ) ),
            'add_args'     => false,
            'add_fragment' => '',
        ) );
    ?>
</div>
<?php
		wp_reset_postdata();
		
		//echo do_shortcode('[products limit="10" columns="3" paginate="true" ids="56,14,41,21,37,53,68,71,73" ]');
	?>
</ul><!--/.products-->

</div>
<?php
get_footer();
?>
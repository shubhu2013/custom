<?php
get_header();

global $wpdb;
$wpdb->prefix;
echo $keywords =  get_query_var('keywords');
if (! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	exit;
}
?>
<div class="wrap">
<ul class="products columns-4">
	<?php
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => 12,
			'orderby' => 'ID',
			'order'   => 'ASC',
			//'post__in'=> array(8,51,50,2),
			);
		$loop = new WP_Query( $args );
		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) : $loop->the_post();
				echo the_title();
				wc_get_template_part( 'content', 'product' );
			endwhile;
		} else {
			echo __( 'No products found' );
		}
		wp_reset_postdata();
	?>
</ul><!--/.products-->
</div>
<?php
get_footer();
?>
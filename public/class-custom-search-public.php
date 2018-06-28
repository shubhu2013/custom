<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Custom_Search
 * @subpackage Custom_Search/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Custom_Search
 * @subpackage Custom_Search/public
 * @author     John <john@info.com>
 */
class Custom_Search_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Custom_Search_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Custom_Search_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/custom-search-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Custom_Search_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Custom_Search_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/custom-search-public.js', array( 'jquery' ), $this->version, false );

	}
	function cs_add_query_vars($vars){
		$vars[] = "property_id";
    	return $vars;
	}
	public function cs_template_redirect(){
		//global $wp_query;
		$url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');

		$url_path = explode('/', $url_path);
		 
		  if ( $url_path[1] == 's' ) {
		  	set_query_var( 'keywords', $url_path[2]);
		  	add_filter( 'template_include', function() {
	            return plugin_dir_path( dirname( __FILE__ ) ).'includes/custom-load-template.php';
	        });

		  }
		
	}
	function cs_init(){
		add_rewrite_rule(
        '^properties/?([^/]*)/?','index.php?pagename=properties&property_id=$matches[1]','bottom' );
	}

}

<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Custom_Search
 * @subpackage Custom_Search/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Custom_Search
 * @subpackage Custom_Search/admin
 * @author     John <john@info.com>
 */
class Custom_Search_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/custom-search-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/custom-search-admin.js', array( 'jquery' ), $this->version, false );

	}
	public function cs_admin_menu(){
		$cs_screen_page = add_menu_page('Search Pages', 'Search Pages', 'administrator', 'search-pages',array(&$this, 'all_search_pages'),'dashicons-admin-site',5);
		 add_submenu_page( 'search-pages', 'New Search Page', 'Add New', 'administrator', 'new-search-page', array(&$this, 'add_search_page') );
		 add_submenu_page( 'search-pages', 'Options Page', 'Options', 'administrator', 'option-page', array(&$this, 'option_search_page') );
		 add_action("load-$cs_screen_page", array(&$this, 'cs_sample_screen_options'));

	}
	function cs_sample_screen_options(){
 
		$screen = get_current_screen();
		// get out of here if we are not on our settings page
		if(!is_object($screen) || $screen->id != 'toplevel_page_search-pages')
			return;
	 
		$args = array(
			'label' => __('Search per page', $this->plugin_name),
			'default' => 10,
			'option' => 'search_per_page'
		);
		add_screen_option( 'per_page', $args );

	}
	function cs_set_screen_option($status, $option, $value) {
		if ( 'search_per_page' == $option ) return $value;
	}

	function all_search_pages(){
		include_once 'partials/all_search_pages.php';	
	}
	function add_search_page(){
		include_once 'partials/add_search_page.php';	
	}
	function option_search_page(){
		include_once 'partials/option_search_page.php';	
	}
	public function cs_add_notice($notice, $type = 'error')
	{
		$types = array(
			'error' => 'error',
			'warning' => 'update-nag',
			'info' => 'check-column',
			'note' => 'updated',
			'none' => '',
		);
		if (!array_key_exists($type, $types))
			$type = 'none';

		$notice_data = array('class' => $types[$type], 'message' => $notice);

		$key = 'cs_admin_notices_' . get_current_user_id();
		$notices = get_transient($key);

		if (FALSE === $notices)
			$notices = array($notice_data);

		// only add the message if it's not already there
		$found = FALSE;
		foreach ($notices as $notice) {
			if ($notice_data['message'] === $notice['message'])
				$found = TRUE;
		}
		if (!$found)
			$notices[] = $notice_data;

		set_transient($key, $notices, 3600);
	}

}

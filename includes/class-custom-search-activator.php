<?php

/**
 * Fired during plugin activation
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Custom_Search
 * @subpackage Custom_Search/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Custom_Search
 * @subpackage Custom_Search/includes
 * @author     John <john@info.com>
 */
class Custom_Search_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$table_forms         = $wpdb->prefix . 'search_forms';
		$table_form_products = $wpdb->prefix . 'search_form_products';

		/* 1. 
         * CREATE Search Forms TABLE
         */
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_forms'") != $table_forms) {
            if (!empty($wpdb->charset))
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if (!empty($wpdb->collate))
                $charset_collate .= " COLLATE $wpdb->collate";
            $sql = "CREATE TABLE IF NOT EXISTS $table_forms (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `keyword` varchar(200)  NOT NULL,
                `count` int(11) NOT NULL,
                `title` varchar(200) NOT NULL,
			    `meta_desc` varchar(200) NOT NULL,
			    `text_before` longtext NOT NULL,
			    `text_after` longtext NOT NULL,
			    `active_ingredient` enum('0','1') NOT NULL DEFAULT '0',
			    `author` int(11) NOT NULL,
			    `add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                 PRIMARY KEY (id)) $charset_collate;";
            dbDelta($sql);
		
    	}
    	/* 1. 
         * CREATE Form products TABLE
         */
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_form_products'") != $table_form_products) {
            if (!empty($wpdb->charset))
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if (!empty($wpdb->collate))
                $charset_collate .= " COLLATE $wpdb->collate";
            $sql2 = "CREATE TABLE IF NOT EXISTS $table_form_products (
                 `id` int(11) NOT NULL,
  				 `form_id` int(11) NOT NULL,
                 `product_id` int(11) NOT NULL,
                 `name` varchar(200) NOT NULL,
                 `product_order` int(11) NOT NULL,
                 `ingredients` TEXT NOT NULL,
                 `description` LONGTEXT NOT NULL,
                  PRIMARY KEY (id)) $charset_collate;";
            dbDelta($sql2);
		
    	}      

	}

}

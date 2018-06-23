<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Custom_List_Table extends WP_List_Table {
	
    function __construct(){
    global $status, $page;

        parent::__construct( array(
            'singular'  => __( 'search form', 'custom-search' ),     //singular name of the listed records
            'plural'    => __( 'search forms', 'custom-search' ),   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?

    ) );
        //print_r($_REQUEST);
    add_action( 'admin_head', array( &$this, 'admin_header' ) ); 
    
    
    }

  function admin_header() {
   $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
    if( 'search-pages' != $page )
    return;
    echo '<style type="text/css">';
    echo '.wp-list-table .column-id { width: 5%; }';
    echo '.wp-list-table .column-booktitle { width: 40%; }';
    echo '.wp-list-table .column-author { width: 35%; }';
    echo '.wp-list-table .column-isbn { width: 20%;}';
    echo '</style>';
  }
  
  public static function get_records( $per_page, $page_number = 1 ) {
  		
		//echo $per_page;
		// now use $per_page to set the number of items displayed
		global $wpdb;
		$search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
	 	// var_dump($search);
		$sql = "SELECT * FROM {$wpdb->prefix}search_forms";
		$where = ' ';
		if($search){
			$where = " WHERE keyword LIKE '%".$search."%' OR count LIKE '%".$search."%' OR title LIKE '%".$search."%' OR meta_desc LIKE '%".$search."%' OR text_before LIKE '%".$search."%' OR text_after LIKE '%".$search."%' ";
		}
        $sql .= $where;
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}else{
			$sql .= ' ORDER BY add_date DESC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		//echo $sql;
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		

		return $result;
	}
	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}search_forms";

		return $wpdb->get_var( $sql );
	}
	
	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_record( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}search_forms",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}

  function no_items() {
    _e( 'No search form found, dude.' );
  }

  function column_default( $item, $column_name ) {
    switch( $column_name ) { 
        case 'keyword':
        case 'title':
        case 'meta_desc':
        case 'author':
        case 'count':
            return $item[ $column_name ];
        default:
            return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
    }
  }

	function get_sortable_columns() {
	  $sortable_columns = array(
	    'keyword'  => array('keyword',false),
	    'title' => array('title',false),
	    'meta_desc'   => array('meta_desc',false),
	    'count'   => array('count',false),
	    'add_date'   => array('add_date',false)
	  );
	  return $sortable_columns;
	}

	function get_columns(){
	        $columns = array(
	            'cb'        => '<input type="checkbox" />',
	            'keyword' => __( 'Keyword', 'custom-search' ),
	            'title'    => __( 'Title', 'custom-search' ),
	            'meta_desc'      => __( 'Meta Description', 'custom-search' ),
	            'author'      => __( 'Author', 'custom-search' ),
	            'count'      => __( 'Results count', 'custom-search' ),
	            'add_date'      => __( 'Date', 'custom-search' ),
	        );
	         return $columns;
	    }

	function usort_reorder( $a, $b ) {
	  // If no sort, default to title
	  $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'booktitle';
	  // If no order, default to asc
	  $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
	  // Determine sort order
	  $result = strcmp( $a[$orderby], $b[$orderby] );
	  // Send final sort direction to usort
	  return ( $order === 'asc' ) ? $result : -$result;
	}

	function column_keyword($item){
		$delete_nonce = wp_create_nonce( 'cs_delete_record' );
	  $actions = array(
	            'edit'      => sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>','new-search-page','edit',$item['id']),
	            'delete' => sprintf( '<a href="?page=%s&action=%s&searchId=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
	        );

	  return sprintf('%1$s %2$s', $item['keyword'], $this->row_actions($actions) );
	}
	function column_author($item){
		$author_obj = get_user_by('id', $item['author']);
	  return '<a href="user-edit.php?user_id='.$item['author'].'">'.$author_obj->user_login.'</a>';
	}
	function column_add_date($item){
		$date=date_create($item['add_date']);
	  return 'Published'.'<br>'.'<abbr title="'.date_format($date,"Y/m/d H:i:s A").'">'.date_format($date,"Y/m/d ").'</abbr>';
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}

	function column_cb($item) {
	        return sprintf(
	            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
	        );    
	    }

	function prepare_items() {
	  $columns  = $this->get_columns();
	  $hidden   = array();
	  $sortable = $this->get_sortable_columns();
	  $this->_column_headers = array( $columns, $hidden, $sortable );
	  //usort( $this->example_data, array( &$this, 'usort_reorder' ) );
	  /** Process bulk action */
	  $this->process_bulk_action();

	  $user = get_current_user_id();
	  $screen = get_current_screen();
	  $option = $screen->get_option('per_page', 'option');	 
	  $per_page = get_user_meta($user, $option, true);
	  if ( empty ( $per_page) || $per_page < 1 ) {
		 
		$per_page = $screen->get_option( 'per_page', 'default' );
		 
	  }

	  $current_page = $this->get_pagenum();
	   $total_items = self::record_count();

	  // only ncessary because we have sample data
	  //$found_data = array_slice( $this->example_data,( ( $current_page-1 )* $per_page ), $per_page );

	  $this->set_pagination_args( array(
	    'total_items' => $total_items,                  //WE have to calculate the total number of items
	    'per_page'    => $per_page                     //WE have to determine how many items to show on a page
	  ) );
	  //$this->items = $found_data;
	  
	  $this->items = self::get_records( $per_page, $current_page );
	}
	
	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );
			if ( ! wp_verify_nonce( $nonce, 'cs_delete_record' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_record( absint( $_GET['searchId'] ) );
				 $this->cs_add_notice2("Form deleted Successfully",'note');
             	 wp_redirect("admin.php?page=search-pages");
				 exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_record( $id );

			}

			 $this->cs_add_notice2("Form deleted Successfully",'note');
         	 wp_redirect("admin.php?page=search-pages");
			 exit;
		}
	}
	public function cs_add_notice2($notice, $type = 'error')
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



<?php
/* Post class definition
 * 
 *
*/
namespace Actions;

use Helper\Core;
		
 

 class Post
{
	//Construct function for Post class
	public function __construct(){
			
	}
    
	//Add db tables for book post type to  wordpress database
	public static function book_post_type_create_db() {

		global $wpdb;
		$version = get_option( 'my_plugin_version', '1.0' );
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'book_info';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			isbn varchar(20),
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	
		if ( version_compare( $version, '2.0' ) < 0 ) {
			$sql = "CREATE TABLE $table_name (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  isbn varchar(20),
			  blog_id smallint(5) NOT NULL,
			  UNIQUE KEY id (id)
			) $charset_collate;";
			dbDelta( $sql );
			update_option( 'my_plugin_version', '2.0' );	
		}	
   }


    //Add custom book type to wordpress
	public static function custom_book_post_type() {

			$labels = array(
			'name'                =>__( 'Books','bookpost'),
			'isbn'                =>__( 'ISBN','bookpost'),
			'isbn_number'                =>__( 'ISBN numbers','bookpost'),
			'singular_name'       => __( 'Book','bookpost'),
			'menu_name'           =>  __('Books','bookpost'),
			'parent_item_colon'   => __( 'Parent Book','bookpost'),
			'all_items'           => __( 'All Books','bookpost'),
			'view_item'           => __( 'View Book','bookpost'),
			'add_new_item'        => __( 'Add New Book','bookpost'),
			'add_new'             => __( 'Add New','bookpost'),
			'edit_item'           => __( 'Edit Book','bookpost'),
			'update_item'         => __( 'Update Book','bookpost'),
			'search_items'        => __( 'Search Book','bookpost'),
			'not_found'           => __( 'Not Found','bookpost'),
			'not_found_in_trash'  => __( 'Not found in Trash','bookpost')
		);
		$args = array(
			'label'               => __( 'books','bookpost'),
			'description'         => __( 'Best Crunchify Books','bookpost'),
			'labels'              => $labels,
			'supports'            => array( 'title','excerpt'),
			'public'              => true,
			'hierarchical'        => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'has_archive'         => true,
			'can_export'          => true,
			'exclude_from_search' => false,
				'yarpp_support'       => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'page'
	    );
		register_post_type( 'books', $args );
	

	}
 
    //Add custom taxonomy for  book  custom type
    public static function custom_book_taxonomy() {
				   $labels = array(
			'name' => __( 'Publishers','bookpost' ),
			'singular_name' => _x( 'Publisher','bookpost' ),
			'search_items' =>  __( 'Search Publishers','bookpost' ),
			'all_items' => __( 'All Publishers','bookpost' ),
			'parent_item' => __( 'Parent Publisher','bookpost' ),
			'parent_item_colon' => __( 'Parent Publisher:','bookpost' ),
			'edit_item' => __( 'Edit Publisher','bookpost'), 
			'update_item' => __( 'Update Publisher','bookpost' ),
			'add_new_item' => __( 'Add New Publisher','bookpost' ),
			'new_item_name' => __( 'New Publisher Name','bookpost' ),
			'menu_name' => __( 'Publishers','bookpost' ),
		  ); 	
		 
		  register_taxonomy('Publishers',array('books'), array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'Publisher' ),
		  ));
		  
		  
		   $labels = array(
			'name' => __( 'Authors', 'bookpost' ),
			'singular_name' => __( 'Author' ,'bookpost' ),
			'search_items' =>  __( 'Search Authors' ,'bookpost'),
			'all_items' => __( 'All Authors','bookpost' ),
			'parent_item' => __( 'Parent Author' ,'bookpost'),
			'parent_item_colon' => __( 'Parent Author:','bookpost' ),
			'edit_item' => __( 'Edit Author','bookpost' ), 
			'update_item' => __( 'Update Author','bookpost' ),
			'add_new_item' => __( 'Add New Author' ,'bookpost'),
			'new_item_name' => __( 'New Author Name' ,'bookpost'),
			'menu_name' => __( 'Authors' ,'bookpost'),
		  ); 	
		 
		  register_taxonomy('Authors',array('books'), array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'Author' ),
		  ));
    }
	
	 // Add meta box to post type
	 function isbn_number_meta_box() {

		add_meta_box(
			'isbn_number',
			__( 'ISBN Number', 'bookpost' ),
			 array($this,'isbn_number_meta_box_callback'),
			'books'
		);
		$all_post_ids = get_posts(array(
		'fields'          => 'ids',
		'posts_per_page'  => -1,
		'post_type' => 'case_studies'
	     ));
	}
     
	  // call back function definiton for meta box
	 function isbn_number_meta_box_callback( $post ) {

		// Add a nonce field so we can check for it later.
		wp_nonce_field( 'isbn_number_nonce', 'isbn_number_nonce' );

		$value = get_post_meta( $post->ID, '_isbn_number', true );

		echo '<textarea style="width:100%" id="isbn_number" name="isbn_number">' . esc_attr( $value ) . '</textarea>';
		printf( __( 'The post type is: %s', 'bookpost' ), get_post_type( get_the_ID() ) );
   }
	
	
	 //Add save function to meta box
	function save_isbn_number_meta_box_data( $post_id ) {

		/* OK, it's safe for us to save the data now. */

		// Make sure that it is set.
		if ( ! isset( $_POST['isbn_number'] ) ) {
			return;
		}

		// Sanitize user input.
		$my_data = sanitize_text_field( $_POST['isbn_number'] );
		
		 $this->update_book_info( $my_data);
		
		
		// Update the meta field in the database.
		update_post_meta( $post_id, '_isbn_number', $my_data );
	}
	
	//Add action for save meta box
    function add_save(){
     add_action( 'save_post', array($this,'save_isbn_number_meta_box_data') );
	}
    //Update book_info table for plugin
	function update_book_info($isbn){
		global $wpdb;
		$table_name = $wpdb->prefix . 'book_info';
		$id=$this->get_id_book();
		echo $table_name;
		
		$sql = "INSERT INTO `$table_name` (`id`, `isbn`) VALUES ('$id', '$isbn');";
		dbDelta($sql );
	}
	
    //Return id book according book info table
	function get_id_book(){
		$args = array(
		  'post_type' => 'books'
		);
		$the_query = new \WP_Query( $args );
		$count_books_number=$the_query->found_posts;
		$id_current=get_the_ID();
		$flag_increment=true;
		
		$loop = new \WP_Query( array( 'post_type' => 'books', 'posts_per_page' => 100 ) ); 
		$id_book=1;
		while ( $loop->have_posts() ) : $loop->the_post(); 
			if($flag_increment && $id_current!=get_the_ID()){
			$id_book++;
			}
			else{
			$flag_increment=false;
				}
			$id=get_the_ID();
			the_title( '<h2 class="entry-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h2>' );
		endwhile; 
		return $count_books_number-$id_book+1;	
	}

    //Add isbn number page to admin
	function book_type_plugin_setup_menu(){
			add_menu_page( 'ISBN Plugin Page', __( 'ISBN numbers','bookpost'), 'manage_options', 'bookpost',array($this,'isbn_numbers_page_init'));
			
	}
    //Function display isbn number in menu page
	function isbn_numbers_page_init(){
		echo  '<h1>'.__( 'ISBN numbers','bookpost').'</h1>';
		global $wpdb;
		$table_name = $wpdb->prefix . 'book_info';
		$books= $wpdb->get_results("SELECT * FROM `$table_name`");
	   
		foreach($books as $row){ 
		$book=$row; 
		echo $book->id.':'.$book->isbn.'<hr>';
		}
						
	}
		
}

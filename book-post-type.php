<?php
/**
 * Plugin Name:  Book Post Type WordPress Plugin
 * Description: A plugin for adding book post type to wordpress
 * Plugin URI: https://blog.stackprogramer.xyz/
 * Version:     .1
 * Author:      stackprogramer
 * Author URI:  https://blog.stackprogramer.xyz/
 * License:     MIT
 * Text Domain: bookpost
 * Domain Path: /languages
 * Creating a function to create our CPT
 */


    // For plugin translation
	function book_post_type_load_textdomain() {
			load_plugin_textdomain( 'bookpost', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
	}
	add_action( 'init', 'book_post_type_load_textdomain',0);

	function my_plugin_create_db() {

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
 /* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/
 
add_action( 'init', 'my_plugin_create_db', 0 );
function custom_book_post_type() {
 
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
 
/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/
 
add_action( 'init', 'custom_book_post_type', 0 );


function custom_book_taxonomy() {
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
add_action( 'init', 'custom_book_taxonomy', 0 );

function isbn_number_meta_box() {

    add_meta_box(
        'isbn_number',
        __( 'ISBN Number', 'bookpost' ),
        'isbn_number_meta_box_callback',
        'books'
    );
	$all_post_ids = get_posts(array(
    'fields'          => 'ids',
    'posts_per_page'  => -1,
    'post_type' => 'case_studies'
));
}

add_action( 'add_meta_boxes', 'isbn_number_meta_box' );

function isbn_number_meta_box_callback( $post ) {

    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'isbn_number_nonce', 'isbn_number_nonce' );

    $value = get_post_meta( $post->ID, '_isbn_number', true );

    echo '<textarea style="width:100%" id="isbn_number" name="isbn_number">' . esc_attr( $value ) . '</textarea>';
	printf( __( 'The post type is: %s', 'bookpost' ), get_post_type( get_the_ID() ) );
    get_id_book();


}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id
 */
function save_isbn_number_meta_box_data( $post_id ) {

    // Check if our nonce is set.
    if ( ! isset( $_POST['isbn_number_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['isbn_number_nonce'], 'isbn_number_nonce' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    }
    else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */

    // Make sure that it is set.
    if ( ! isset( $_POST['isbn_number'] ) ) {
        return;
    }

    // Sanitize user input.
    $my_data = sanitize_text_field( $_POST['isbn_number'] );
	
	 update_book_info( $my_data);
	
	
    // Update the meta field in the database.
    update_post_meta( $post_id, '_isbn_number', $my_data );
}

add_action( 'save_post', 'save_isbn_number_meta_box_data' );

function update_book_info($isbn){
     /**
	 * update
	 */
	global $wpdb;
	$table_name = $wpdb->prefix . 'book_info';
	$id=get_id_book();
	echo $table_name;
	
	$sql = "INSERT INTO `$table_name` (`id`, `isbn`) VALUES ('$id', '$isbn');";
	dbDelta($sql );

	//$wpdb->insert( $wpdb->$table_name, array( 'isbn' => $isbn) );	
}

function get_id_book(){
	$args = array(
	  'post_type' => 'books'
	);
	$the_query = new WP_Query( $args );
	$count_books_number=$the_query->found_posts;
	$id_current=get_the_ID();
	$flag_increment=true;
	
    $loop = new WP_Query( array( 'post_type' => 'books', 'posts_per_page' => 100 ) ); 
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
        //echo'<div class="entry-content">'.the_content().'</div>';
    endwhile; 
	//echo 'The id current book is'.($count_books_number-$id_book+1);
	return $count_books_number-$id_book+1;
	
}

add_action('admin_menu', 'book_type_plugin_setup_menu');
 
function book_type_plugin_setup_menu(){
		add_menu_page( 'ISBN Plugin Page', __( 'ISBN numbers','bookpost'), 'manage_options', 'bookpost', 'test_init' );
		
}
 
function test_init(){
                    echo  '<h1>'.__( 'ISBN numbers','bookpost').'</h1>';
					global $wpdb;
		            $table_name = $wpdb->prefix . 'book_info';
				    $books= $wpdb->get_results("SELECT * FROM `$table_name`");
				   
		            foreach($books as $row){ 
					$book=$row; 
					echo $book->id.':'.$book->isbn.'<hr>';
					}
					
}




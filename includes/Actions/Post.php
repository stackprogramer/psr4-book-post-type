<?php

namespace Actions;

use Helper\Core;
		

class Post
{
  
	//Add db tables for book post type 
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
			'name'                => __( 'Books','book-post-type'),
			'singular_name'       => __( 'Book','book-post-type'),
			'menu_name'           => __( 'Books','book-post-type'),
			'parent_item_colon'   => __( 'Parent Book','book-post-type'),
			'all_items'           => __( 'All Books','book-post-type'),
			'view_item'           => __( 'View Book','book-post-type'),
			'add_new_item'        => __( 'Add New Book','book-post-type'),
			'add_new'             => __( 'Add New','book-post-type'),
			'edit_item'           => __( 'Edit Book','book-post-type'),
			'update_item'         => __( 'Update Book','book-post-type'),
			'search_items'        => __( 'Search Book','book-post-type'),
			'not_found'           => __( 'Not Found','book-post-type'),
			'not_found_in_trash'  => __( 'Not found in Trash','book-post-type')
		);
		$args = array(
			'label'               => __( 'books','book-post-type'),
			'description'         => __( 'Best Crunchify Books','book-post-type'),
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
			'name' => __( 'Publishers','book-post-type' ),
			'singular_name' => _x( 'Publisher','book-post-type' ),
			'search_items' =>  __( 'Search Publishers','book-post-type' ),
			'all_items' => __( 'All Publishers','book-post-type' ),
			'parent_item' => __( 'Parent Publisher','book-post-type' ),
			'parent_item_colon' => __( 'Parent Publisher:','book-post-type' ),
			'edit_item' => __( 'Edit Publisher','book-post-type'), 
			'update_item' => __( 'Update Publisher','book-post-type' ),
			'add_new_item' => __( 'Add New Publisher','book-post-type' ),
			'new_item_name' => __( 'New Publisher Name','book-post-type' ),
			'menu_name' => __( 'Publishers','book-post-type' ),
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
			'name' => __( 'Authors', 'book-post-type' ),
			'singular_name' => __( 'Author' ,'book-post-type' ),
			'search_items' =>  __( 'Search Authors' ,'book-post-type'),
			'all_items' => __( 'All Authors','book-post-type' ),
			'parent_item' => __( 'Parent Author' ,'book-post-type'),
			'parent_item_colon' => __( 'Parent Author:','book-post-type' ),
			'edit_item' => __( 'Edit Author','book-post-type' ), 
			'update_item' => __( 'Update Author','book-post-type' ),
			'add_new_item' => __( 'Add New Author' ,'book-post-type'),
			'new_item_name' => __( 'New Author Name' ,'book-post-type'),
			'menu_name' => __( 'Authors' ,'book-post-type'),
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
	
	
	
	
	
}

<?php
/**
 * Plugin Name:  Book Post Type WordPress Plugin
 * Description: A plugin for adding book post type to wordpress
 * Plugin URI: https://blog.stackprogramer.xyz/
 * Version:     .2
 * Author:      stackprogramer
 * Author URI:  https://blog.stackprogramer.xyz/
 * License:     GPL v3
 * Text Domain: bookpost
 * Domain Path: /languages
 * 
 *
 * Creating a function to create our CPT book type and it's taxonomies.
*/


add_action('init', array(Book_Post_Type::get_instance(), 'plugin_setup'),0);
add_action( 'textdomain', array(Book_Post_Type::book_post_type_load_textdomain(),'load_textdomain'),0);




class  Book_Post_Type
{ 
    /**
     * Plugin instance.
     *
     * @see get_instance()
     * @type object
     */
    protected static $instance = NULL;
    /**
     * URL to this plugin's directory.
     *
     * @type string
     */
    public $plugin_url = '';
    /**
     * Path to this plugin's directory.
     *
     * @type string
     */
    public $plugin_path = '';
 
    /**
     * Access this plugin’s working instance
     *
     * @wp-hook plugins_loaded
     * @since   2012.09.13
     * @return  object of this class
     */
    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }

    /**
     * Used for regular plugin work.
     *
     * @wp-hook plugins_loaded
     * @return  void
     */
    public function plugin_setup()
    {
        $this->plugin_url = plugins_url('/', __FILE__);
        $this->plugin_path = plugin_dir_path(__FILE__);

        spl_autoload_register(array($this, 'autoload'));

        // Example: Modify the Contents
		Actions\Post::book_post_type_create_db();
		Actions\Post::custom_book_post_type();
		Actions\Post::custom_book_taxonomy();
		
		$post_book=new Actions\Post();
		$post_book-> isbn_number_meta_box();
		$post_book->add_save();
	    $post_book->book_type_plugin_setup_menu();
	

    }

    /**
     * Constructor. Intentionally left empty and public.
     *
     * @see plugin_setup()
     */
    public function __construct()
    {
    }

    /**
     * Loads translation file.
     *
     * Accessible to other classes to load different language files (admin and
     * front-end for example).
     *
     * @wp-hook init
     * @param   string $domain
     * @return  void
     */
    
    // For plugin translation text domain
	public static function book_post_type_load_textdomain() {
			load_plugin_textdomain( 'bookpost', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
	}



    /**
     * @param $class
     *
     */
    public function autoload($class)
    {
        $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

        if (!class_exists($class)) {
            $class_full_path = $this->plugin_path . 'includes/' . $class . '.php';

            if (file_exists($class_full_path)) {
                require $class_full_path;
            }
        }
    }
}


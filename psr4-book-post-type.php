<?php
/**
 * Plugin Name: PSR4 Book Post Type WordPress Plugin
 * Description: A plugin for adding book post type to wordpress
 * Plugin URI: https://blog.stackprogramer.xyz/
 * Version:     .1
 * Author:      stackprogramer
 * Author URI:  https://blog.stackprogramer.xyz/
 * License:     MIT
 * Text Domain: psr4-book-post-type
 * Domain Path: /languages
 */

add_action('plugins_loaded', array(PSR4_Book_Post_Type::get_instance(), 'plugin_setup'));

class  PSR4_Book_Post_Type
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
        $this->load_language('psr4-wordpress-plugin');

        spl_autoload_register(array($this, 'autoload'));

        // Example: Modify the Contents
        Actions\Post::addEmojiToContents();
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
    public function load_language($domain)
    {
        load_plugin_textdomain($domain, FALSE, $this->plugin_path . '/languages');
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
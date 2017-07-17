<?php
/*
Plugin Name: GB Search and replace
Plugin URI: http://gb-plugins.com/
Description: GB Search and replace is a small plugin that help you replace text/tags in all posts types like pages, pages...
Version: 1
Author: GB-plugins
Author URI: http://gb-plugins.com/
*/

defined('GB_REPLACE_SLUG') or define('GB_REPLACE_SLUG','gb-search-and-replace');
defined('GB_REPLACE_NAME') or define('GB_REPLACE_NAME',__('GB Search and replace',GB_REPLACE_SLUG));
defined('GB_REPLACE_SRC') or define('GB_REPLACE_SRC',plugins_url('',__FILE__).'/');
defined('GB_REPLACE_DIR') or define('GB_REPLACE_DIR',plugin_dir_path(__FILE__).'/');

add_action( 'admin_menu', 'gb_package_custom_menu_page' );
function gb_package_custom_menu_page(){
    add_menu_page( GB_REPLACE_NAME, GB_REPLACE_NAME, 'manage_options',GB_REPLACE_SLUG.'/'.GB_REPLACE_SLUG.'_page.php', '', '', 7 );
}

//Load all scripts and styles
add_action( 'admin_enqueue_scripts', 'gb_replace_load_scripts' );
function gb_replace_load_scripts(){
    wp_register_script( GB_REPLACE_SLUG.'-script', plugins_url( GB_REPLACE_SLUG.'/'.GB_REPLACE_SLUG.'.js' ) );
    wp_register_style( GB_REPLACE_SLUG.'-style', plugins_url( GB_REPLACE_SLUG.'/'.GB_REPLACE_SLUG.'.css' ) );
    wp_register_style( GB_REPLACE_SLUG.'-style-rtl', plugins_url( GB_REPLACE_SLUG.'/'.GB_REPLACE_SLUG.'-rtl.css' ) );
}

//Load replace to tag functions
include_once( 'functions.php' );
?>

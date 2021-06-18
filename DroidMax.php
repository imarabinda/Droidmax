<?php
/***
Plugin Name: DroidMax
Description: Feedback Helper! Advanced
Version: 1.0.2
Author: Arabinda
Plugin URI: https://www.yourwebsiteurl.com/
Author URI: http://yourwebsiteurl.com/
 ***/


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
if(wp_doing_ajax()){
    return;
}


define( 'DROIDMAX_VERSION', '1.0.2' );

define( 'DROIDMAX__FILE__', __FILE__ );
define( 'DROIDMAX_PLUGIN_BASE', plugin_basename( DROIDMAX__FILE__ ) );

define( 'DROIDMAX_PATH', plugin_dir_path( DROIDMAX__FILE__ ) );
define( 'DROIDMAX_URL', plugins_url( '/', DROIDMAX__FILE__ ) );

define( 'DROIDMAX_ASSETS_PATH', DROIDMAX_PATH . 'assets/' );
define( 'DROIDMAX_ASSETS_URL', DROIDMAX_URL . 'assets/' );

define( 'DROIDMAX_INCLUDES_PATH', DROIDMAX_PATH . 'includes/' );
define( 'DROIDMAX_INCLUDES_URL', DROIDMAX_URL . 'includes/' );

include (DROIDMAX_INCLUDES_PATH.'uninstall.php');
include (DROIDMAX_INCLUDES_PATH.'feedback.php');
include (DROIDMAX_INCLUDES_PATH.'admin_form.php');
include (DROIDMAX_INCLUDES_PATH.'settings.php');
include (DROIDMAX_INCLUDES_PATH.'styles.php');
include (DROIDMAX_INCLUDES_PATH.'extra.php');



<?php


/**
 * Feedback Plugin styles.
 *
 * @since 1.0
 */
function register_plugin_styles() {
    global $wp_styles;
    if(wp_doing_ajax()){
        return;
    }
$droidmax_otions=get_droidmax_options('droidmax_options');
    if(!in_array(get_post_type(),$droidmax_otions['dm-show-on'],TRUE)){
        return;
    }

    wp_enqueue_style( 'droidmax_feedback_css', DROIDMAX_ASSETS_URL.'css/droidmax_feedback.css', array(), DROIDMAX_VERSION, 'all' );
    wp_enqueue_script( 'droidmax_feedback_js', DROIDMAX_ASSETS_URL.'js/droidmax_feedback.js', array('jquery'), DROIDMAX_VERSION, 'all' );
    // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
    wp_localize_script( 'droidmax_feedback_js', 'DroidmaxFeedback', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    wp_add_inline_style( 'droidmax_feedback_css', '' );
}
add_action('wp_enqueue_scripts',  'register_plugin_styles' );

/**
 * Add custom css for admin section
 * @since 1.2
 */
function register_plugin_admin_styles($page){

    if(wp_doing_ajax()){
        return;
    }
    if($page!='toplevel_page_droidmax-settings'){
        return;
    }
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script('droidmax_feedback_admin_js', DROIDMAX_ASSETS_URL. 'js/droidmax_feedback_admin.js', array('jquery','wp-color-picker'), DROIDMAX_VERSION, 'all' );
    wp_register_style('droidmax_feedback_admin_css', DROIDMAX_ASSETS_URL.'css/droidmax_feedback_admin.css', false, DROIDMAX_VERSION );
    wp_enqueue_style( 'droidmax_feedback_admin_css' );
    wp_localize_script( 'droidmax_feedback_admin_js', 'DroidmaxFeedback', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    wp_enqueue_media();
}

add_action('admin_enqueue_scripts', 'register_plugin_admin_styles' ) ;


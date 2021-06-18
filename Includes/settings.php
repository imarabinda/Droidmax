<?php


/**
 * Add Menu page in Settings for configuring plugin
 *
 * @since 1.0
 */
function register_menu(){

//    add_submenu_page( 'options-general.php', 'DroidMax Settings', 'DroidMax', 'activate_plugins', 'droidmax-settings', 'submenu_page');
    add_menu_page('DroidMax', 'DroidMax', 'manage_options', 'droidmax-settings', 'menu_page','
dashicons-smiley');
//    add_submenu_page('my-menu', 'Submenu Page Title', 'Whatever You Want', 'manage_options', 'my-menu' );
    /*add_submenu_page('my-menu', 'Submenu Page Title2', 'Whatever You Want2', 'manage_options', 'my-menu2' );*/
}
add_action( 'admin_menu', 'register_menu' ) ;




/*******
 * Feedback options
 */
function register_settings(){
    register_setting( 'droidmax_options', 'droidmax_options' );
}
add_action( 'admin_init', 'register_settings' ) ;




/*****
 * Load defaults
 * register hook
 */
function load_defaults(){

    update_option( 'droidmax_options', get_defaults() );

}

register_activation_hook( DROIDMAX__FILE__,  'load_defaults');

/*********
 * @param bool $preset
 * @return array
 * Default values
 */
function get_defaults($preset=true) {
    return array(
        'dm-select-position' => 'after-content',
        'dm-show-on' => $preset ? array('post') : array(),
        'dm-title-phrase-1'=>'',
        'dm-title-phrase-2'=>'',
        'dm-title-phrase-3'=>'',
        'dm-title-phrase-5'=>'',
        'dm-title-phrase-10'=>'',
        'dm-exclude-on' => '',
        'dm-button-submit' => '',
        'dm-color-submit' => '#ff4b5a',
        'dm-color-submit-hover' => '#333333',
        'dm-contact-mail'=>'so@pretty.you',
        'dm-font-size'=>'2.4',
        'dm-button-yes'=> '',
        'dm-button-no'=> '',
    );
}

/*****
 * @return array
 * Returns Feedback options with defaults
 */
function get_droidmax_options() {
    return array_merge( get_defaults(false), get_option('droidmax_options') );
}

/***
 * @param $title
 * @param $default
 * @return mixed
 */
function get_feedback_default_title($title,$default) {
    if( isset($title) && $title!=""):
        return 	$title;
    else:
        return $default;
    endif;
}

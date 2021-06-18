<?php



/********
 * Uninstall everything
 */
function droidmax_uninstall(){

    delete_option('droidmax_options');
    unregister_post_type( 'droidmax');

    global $wpdb;
    $table = $wpdb->prefix.'postmeta';
    $wpdb->delete ($table, array('meta_key' => 'feedback_response'));
    $wpdb->delete ($table, array('meta_key' => 'feedback_email'));
    $wpdb->delete ($table, array('meta_key' => 'feedback_name'));
    $wpdb->delete ($table, array('meta_key' => 'feedback_post_id'));
    $wpdb->delete ($table, array('meta_key' => 'feedback_message'));
    $wpdb->delete ($table, array('meta_key' => 'btn_yes'));
    $wpdb->delete ($table, array('meta_key' => 'btn_no'));

    $allposts= get_posts( array('post_type'=>'droidmax','numberposts'=>-1) );
    foreach ($allposts as $eachpost) {
        wp_delete_post( $eachpost->ID, true );
    }

}

register_uninstall_hook( DROIDMAX__FILE__, 'droidmax_uninstall' );


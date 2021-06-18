<?php

/*****
 * @param $user_login
 * @param $user
 * Add user meta Last Login
 */
function droidmax_add_meta_users( $user_login, $user )
{
    update_user_meta( $user->ID, 'last_login', current_time('mysql') );
}
add_action( 'wp_login', 'droidmax_add_meta_users', 10, 2);


/*******
 * @param $columns
 * @return mixed
 * Add Column To users
 */
function droidmax_manage_users_columns( $columns )
{
    $columns_new = array(
        'last_login'=>'Last login',
        'registration_date'=>'Registration Date'
    );
    $columns = array_merge($columns,$columns_new);
    return $columns;
}
add_filter( 'manage_users_columns', 'droidmax_manage_users_columns');



/********
 * @param $value
 * @param $column_name
 * @param $user_id
 * @return false|string
 * Add column content
 */
function droidmax_manage_users_custom_column( $output, $column_name, $user_id )
{
    $date_format = 'j M, Y H:i';

    switch ( $column_name ) {
        case 'registration_date' :
            $output = date( $date_format, strtotime( get_the_author_meta( 'registered', $user_id ) ) );
            return $output;
            break;
        case 'last_login':
            $last_login = get_user_meta( $user_id, 'last_login', true );
            $output = $last_login ? date( $date_format, $last_login ) : '-';
            return $output;
            break;
        default:
            return $output;
            break;
    }
    return $output;

}
add_filter('manage_users_custom_column',  'droidmax_manage_users_custom_column', 10, 3);



/*********
 * @param $columns
 * @return mixed
 * Filter functions
 */
function droidmax_make_registered_column_sortable( $columns ) {
    $array=array(
        'registration_date' => 'registered',
    );
    return wp_parse_args( $array, $columns );
}
add_filter( 'manage_users_sortable_columns', 'droidmax_make_registered_column_sortable' );


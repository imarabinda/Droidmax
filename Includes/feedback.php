<?php


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}





/**
 * Feedback Append HTML with Content with Buttons.
 *
 * @since 1.0
 */

function append_feedback_html( $content ) {
    $droidmax_options = get_droidmax_options('droidmax_options');
    // get current post's id
    global $post;
    $post_id=$post->ID;
    $selected_post_types = $droidmax_options['dm-show-on'];
    if( in_array($post_id,explode(',',$droidmax_options['dm-exclude-on'])) )
        return $content;
    $feedback_html_markup = feedback_content();
    // show on only selected post types
    if(is_singular($selected_post_types)){
        if(  $droidmax_options['dm-select-position']=='before-content' )
            $content = $feedback_html_markup.$content;
        if( $droidmax_options['dm-select-position']=='after-content' )
            $content .= $feedback_html_markup;
    }
    return $content;
}
add_filter( 'the_content', 'append_feedback_html' );

/**
 * Feedback send article Feedback by mail to article author or custom provided mail id
 *
 * @since 1.0
 */

function get_droidmax_feedback(){

    if(!wp_doing_ajax()){
        return;
    }
    $droidmax_options = get_option('droidmax_options');
    $name=$feedback=$email=$response=$meta_name=$post_id='';
    if(isset($_POST['name']) && isset($_POST['feedback']) && isset($_POST['email']) && isset($_POST['response'])){
        $name             = sanitize_text_field($_POST['name']);
        $feedback         = sanitize_text_field($_POST['feedback']);
        $email            = sanitize_email($_POST['email']);
        $response         = sanitize_text_field($_POST['response']);
    }

    if(isset($_POST['btn']) || isset($_POST['post_id'])){
        $post_id         = sanitize_text_field($_POST['post_id']);
        $meta_name         = sanitize_text_field($_POST['btn']);
    }
    else{
        echo 'No naughty business please.';
        exit("No naughty business please.");
    }

    // Cookie check
    if(isset($_COOKIE["helpful_id_".$post_id])){
        exit("No naughty business please.");
    }

    // Get
    $current_post_value = get_post_meta($post_id, $meta_name, true);
    // Make it zero if empty
    if(empty($current_post_value)){
        $current_post_value = 0;
    }
    // Update value
    $new_value = $current_post_value + 1;


    if($meta_name == 'btn_yes'){
        update_post_meta($post_id, $meta_name, $new_value);
    }else if($meta_name == 'btn_no'){
        $my_feedback = array(
            'post_title'   => wp_strip_all_tags($name) . wp_strip_all_tags($email),
            'post_content' => $feedback,
            'post_status'  => 'publish',
            'post_type'    => 'droidmax',
        );
        if($feedback_id = wp_insert_post($my_feedback)){
            update_post_meta($feedback_id, 'feedback_name', $name);
            update_post_meta($feedback_id, 'feedback_email', $email);
            update_post_meta($feedback_id, 'feedback_message', $feedback);
            update_post_meta($feedback_id, 'feedback_post_id', $post_id);
            update_post_meta($feedback_id, 'feedback_response', $response);
            update_post_meta($post_id, $meta_name, $new_value);
        }
    }

}
add_action('wp_ajax_droidmax_feedback', 'get_droidmax_feedback');
add_action('wp_ajax_nopriv_droidmax_feedback', 'get_droidmax_feedback');


/*******
 * Custom post type support for column
 */
function droidmax_post_type_support(){

    // Get selected post types
    $droidmax_options= get_droidmax_options('droidmax_options');
    $selected_post_types = $droidmax_options['dm-show-on'];
    // loop selected type
    if(!empty($selected_post_types)){
        foreach ($selected_post_types as $selected_type) {
            add_filter('manage_'.$selected_type.'_posts_columns', 'droidmax_helpful_column');
            add_action('manage_'.$selected_type.'_posts_custom_column','droidmax_helpful_column_content', 10, 2);
        }
    }
}

add_action("init", "droidmax_post_type_support");

/*****************
 * @param $columns
 * @return array
 * Create custom help ful column
 */
function droidmax_helpful_column($columns) {
    return array_merge($columns, array('helpful' => 'Helpful'));
}


/********************
 * @param $column
 * @param $post_id
 * Custom Helpful columns ratio content
 */
function droidmax_helpful_column_content($column, $post_id) {

    // Variables
    $positive_value = intval(get_post_meta($post_id, "btn_yes", true));
    $negative_value = intval(get_post_meta($post_id, "btn_no", true));

    // Total
    $total = $positive_value + $negative_value;

    if($total > 0){
        $ratio = intval($positive_value * 100 / $total);
    }

    // helpful ration
    if($column == 'helpful'){

        if($total > 0){
            echo "<strong style='display:block;'>" . $ratio . "%</strong>";
            echo "<em style='display:block;color:rgba(0,0,0,.55);'>".$positive_value . " helpful" . " / ".$negative_value." not helpful</em>";
            echo "<div style='margin-top: 5px;width:100%;max-width:100px;background:rgba(0,0,0,.12);line-height:0px;font-size:0px;border-radius:3px;'>
<span style='width:".$ratio."%;background:rgba(0,0,0,.55);height:4px;display:inline-block;border-radius:3px;'></span>
</div>";
        }else{
            echo "—";
        }

    }

}




/***
 * Feedback register
 */
function post_type_register(){
    global $data;
    $labels = array(
        'name'               => __( 'Feedback', 'droidmax-feedback' ),
        'singular_name'      => __( 'Feedback', 'droidmax-feedback' ),
        'add_new'            => __( 'Add Feedback', 'droidmax-feedback' ),
        'add_new_item'       => __( 'Add Feedback', 'droidmax-feedback' ),
        'edit_item'          => __( 'Edit Feedback', 'droidmax-feedback' ),
        'new_item'           => __( 'Add Feedback', 'droidmax-feedback' ),
        'view_item'          => __( 'View Feedback', 'droidmax-feedback' ),
        'search_items'       => __( 'Search Feedback', 'droidmax-feedback' ),
        'not_found'          => __( 'No feedback items found', 'droidmax-feedback' ),
        'not_found_in_trash' => __( 'No feedback items found in trash', 'droidmax-feedback' )
    );

    $args = array(
        'labels'          => $labels,
        'public'          => false,
        'show_ui'         => true,
        'has_archive'     => false,
        'capability_type' => 'post',
        'capabilities'    => array(
            'create_posts'=> false,
        ),
        'map_meta_cap' 	  => true,
        'hierarchical' 	  => false,
        'menu_position'   => 22,
        'menu_icon'       => 'dashicons-megaphone',
        'rewrite'      	  => array('slug' => false),
        'supports'     	  => array('title', 'editor'),
    );

    register_post_type( 'droidmax' , $args );
}
add_action( 'init', 'post_type_register' );


/***************************
 * @param $feedback_columns
 * @return array
 * Feedback columns
 */
function feedback_edit_columns( $feedback_columns ) {
    $feedback_columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "name" => _x('Author', 'feedbackposttype'),
        "post" => __('Post', 'feedbackposttype'),
        "feedback" => __('Feedback', 'feedbackposttype'),
        "date" => __('Date', 'feedbackposttype'),
    );
    //$feedback_columns['comments'] = '<div class="vers"><img alt="Comments" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>';
    return $feedback_columns;
}

add_filter( 'manage_edit-droidmax_columns',  'feedback_edit_columns'  );

/***
 * @param $feedback_columns
 * @param $post_id
 * Feedback Post type columns content
 */
function feedback_column_display( $feedback_columns, $post_id ) {
    switch ( $feedback_columns ) {
        case "name":
            echo '<strong style="font-size:15px;color:#333;">'. get_post_meta( $post_id, 'feedback_name', true ) . '</strong><br>';
            echo  get_post_meta( $post_id, 'feedback_email', true ) . '<br>';
            break;

        case "post":
            echo '<a href="'.get_permalink(get_post_meta( $post_id, 'feedback_post_id', true )).'">'.get_the_title(get_post_meta( $post_id, 'feedback_post_id', true )).'</a>';
            break;
        case "feedback":
            echo  get_post_meta( $post_id, 'feedback_message', true ). '<br>';
            echo '<i>'. get_post_meta( $post_id, 'feedback_response', true ) .'</i>';
            break;
    }
}

add_action( 'manage_posts_custom_column', 'feedback_column_display' , 10, 2 );




/**
 * Convert hexdec color string to rgb(a) string
 *
 * If we want make opacity, we have to convert hexadecimal into rgb(a), because wordpress customizer give to us hexadecimal colour
 * @link https://mekshq.com/how-to-convert-hexadecimal-color-code-to-rgb-or-rgba-using-php/
 */
function hex2rgbaRe( $color, $opacity = false ) {

    $default = 'rgb( 0, 0, 0 )';

    /**
     * Return default if no color provided
     */
    if( empty( $color ) ) {

        return $default;

    }

    /**
     * Sanitize $color if "#" is provided
     */
    if ( $color[0] == '#' ) {

        $color = substr( $color, 1 );

    }

    /**
     * Check if color has 6 or 3 characters and get values
     */
    if ( strlen($color) == 6 ) {

        $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );

    } elseif ( strlen( $color ) == 3 ) {

        $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );

    } else {

        return $default;

    }

    /**
     * [$rgb description]
     * @var array
     */
    $rgb =  array_map( 'hexdec', $hex );

    /**
     * Check if opacity is set(rgba or rgb)
     */
    if( $opacity ) {

        if( abs( $opacity ) > 1 )

            $opacity = 1.0;

        $output = 'rgba( ' . implode( "," ,$rgb ) . ',' . $opacity . ' )';

    } else {

        $output = 'rgb( ' . implode( "," , $rgb ) . ' )';

    }

    /**
     * Return rgb(a) color string
     */
    return $output;
}


/**
 * Feedback Content.
 *
 * @since 1.0
 */
function feedback_content() {
    global $post;
    $post_id =$post->ID;
    $droidmax_options = get_droidmax_options('droidmax_options');
    $onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;";

    if(isset($_COOKIE["helpful_id_".$post_id])){
        return '
<div class="droidmax-holder"><div class="droidmax-inactive">
				<span>'.get_feedback_default_title($droidmax_options['dm-title-phrase-2'],'We appreciate your helpul feedback!').'</span>
			</div></div>';
    }else{
        $color = get_feedback_default_title($droidmax_options['dm-color-submit'],'#ff4b5a');
        $rgba = hex2rgbaRe( $color, 0.5 );

        $color_hover = get_feedback_default_title($droidmax_options['dm-color-submit-hover'],'#333333');
        $rgba_hover = hex2rgbaRe( $color_hover, 0.7 );


        $image_id_yes = $droidmax_options['dm-button-yes'];
        $image_id_attr_yes=$image_id_attr_no='';

        if(is_numeric($image_id_yes)){
            $image_id_attr_yes = esc_attr( $image_id_yes);
            // Change with the image size you want to use
            $image_yes = wp_get_attachment_image( $image_id_yes, 'medium', false, array( 'class' => 'dm-preview-image-yes' ) );
        }else{
            $image_yes = '<img src="'.DROIDMAX_DEFAULT_YES.'" class="dm-preview-image-yes">';
        }

        $image_id_no = $droidmax_options['dm-button-no'];
        if(is_numeric($image_id_no)){
            $image_id_attr_no = esc_attr( $image_id_no );
            // Change with the image size you want to use
            $image_no = wp_get_attachment_image( $image_id_no, 'medium', false, array( 'class' => 'dm-preview-image-no' ) );

        }else{
            $image_no = '<img src="'.DROIDMAX_DEFAULT_NO.'" class="dm-preview-image-no">';
        }




        return
            '<style>
.contact100-form-btn {
background-color: '.$color.';
 box-shadow: 0 10px 30px 0px '.$rgba.';
    -moz-box-shadow: 0 10px 30px 0px '.$rgba.';
    -webkit-box-shadow: 0 10px 30px 0px '.$rgba.';
    -o-box-shadow: 0 10px 30px 0px '.$rgba.';
    -ms-box-shadow: 0 10px 30px 0px '.$rgba.';
}
.contact100-form-btn:hover {
    background-color: '.$color_hover.';
    box-shadow: 0 10px 30px 0px '.$rgba_hover.';
    -moz-box-shadow: 0 10px 30px 0px '.$rgba_hover.';
    -webkit-box-shadow: 0 10px 30px 0px '.$rgba_hover.';
    -o-box-shadow: 0 10px 30px 0px '.$rgba_hover.';
    -ms-box-shadow: 0 10px 30px 0px '.$rgba_hover.';
    }
</style>
<div class="droidmax-holder">
                <div class="droidmax-container">
				<div class="droidmax-title"><h3>'.get_feedback_default_title($droidmax_options['dm-title-phrase-1'],'Was this article helpful?').'</h3></div>
				<div class="droidmax-emoji">
				<droidmax class="droidmax-yes">'.$image_yes.'</droidmax>
				
				<droidmax class="droidmax-no">'.$image_no.'</droidmax>
				</div>	
			       </div>
			</div>       
			       
			
			
			
			
			<div class="modal droidmax-popup-1">
					<div class="modal-content droidmax-popup-1-content">
						<a href="#" class="droidmax-close-popup">&times;</a>
						<h3>'.get_feedback_default_title($droidmax_options['dm-title-phrase-5'],'What went wrong?').'</h3>
						<span class="droidmax-responses">
							<a href="#" class="droidmax-response-select" >This article contains incorrect information</a>
						</span>
						<span class="droidmax-responses">
							<a href="#" class="droidmax-response-select" >This article does not have the information I am looking for</a>
						</span>
						<span class="droidmax-responses">
							<a href="#" class="droidmax-response-select">Other reason</a>
						</span>
					</div>
			</div>
			
			<div class="modal droidmax-popup-2">
					<div class="modal-content">
						<span class="droidmax-close-popup">&times;</span>
						<span class="contact100-form-title">
					'.get_feedback_default_title($droidmax_options['dm-title-phrase-10'],'How can we improve it?').'
				</span>
				
			<form class="contact100-form validate-form droidmax-feedback-form" accept-charset="UTF-8" method="post">
			        '.wp_nonce_field(-1,'authenticity_token',true, false).'
							<input type="hidden" name="action" value="droidmax_response"/>
							<input type="hidden" class="droidmax-response" name="droidmax-response" value="You look so pretty today"/>
							<input type="hidden" class="droidmax-post-id" name="droidmax-post-id" value="'.urldecode($post->ID).'"/>
				

				<div class="wrap-input100 rs1-wrap-input100 validate-input" data-validate="Name is required">
					<span class="label-input100">Your Name</span>
					<input class="input100 droidmax-name" type="text" name="name" placeholder="Enter your name">
					<span class="focus-input100"></span>
				</div>

				<div class="wrap-input100 rs1-wrap-input100 validate-input" data-validate = "Valid email is required: ex@snake.bite">
					<span class="label-input100">Email</span>
					<input class="input100 droidmax-email" type="text" name="email" placeholder="Enter your email addess">
					<span class="focus-input100"></span>
				</div>

				<div class="wrap-input100 validate-input" data-validate = "Feedback is required">
					<span class="label-input100">Feedback</span>
					<textarea class="input100 droidmax-feedback" name="message" placeholder="Describe here"></textarea>
					<span class="focus-input100"></span>
				</div>
				
				
				<div class="container-contact100-form-btn">
					<button class="contact100-form-btn">
						<span>
							'.get_feedback_default_title($droidmax_options['dm-button-submit'],'Submit').'
						</span>
					</button>
				</div>
				
				
			</form>
			<span class="contact100-more">
				For any question send mail at <span class="contact100-more-highlight">'.get_feedback_default_title($droidmax_options['dm-contact-mail'],'so@pretty.you').'</span>
			</span>
					</div>
			</div>
			
			<div class="modal droidmax-popup-3">
					<div class="modal-content droidmax-poopup-content-3">
						<a href="#" class="droidmax-close-popup">&times;</a>
						<h3 class="thank-you">'.get_feedback_default_title($droidmax_options['dm-title-phrase-2'],'We appreciate your helpul feedback!').'</h3>
						<span class="droidmax-responses">'.get_feedback_default_title($droidmax_options['dm-title-phrase-3'],'Your answer will be used to improve our content. The more feedback you give us, the better our pages can be.').'</span>
						</div>
			</div>
			';
    }
}


add_shortcode('droidmax_feedback', 'feedback_content' ) ;




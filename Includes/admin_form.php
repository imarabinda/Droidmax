<?php



define('DROIDMAX_DEFAULT_YES',DROIDMAX_ASSETS_URL.'images/love-emoji.png');
define('DROIDMAX_DEFAULT_NO',DROIDMAX_ASSETS_URL.'images/neutral-emoji.png');

// Ajax action to refresh the user image
add_action( 'wp_ajax_droidmax_get_image', 'droidmax_get_image'   );
function droidmax_get_image() {
    if(!wp_doing_ajax()){
        return;
    }
    if(isset($_GET['id']) && $_GET['button_type'] ){
        $id='no-shit-business-here';
        $button_type = sanitize_text_field($_GET['button_type']);

        if($button_type =='yes' || $button_type == 'no'){
            $id='dm-preview-image-'.$button_type;
        }
        $image_id = sanitize_text_field($_GET['id']);

        if(!is_numeric($image_id)){
            exit('You bloody Phoquer');
        }

        $image = wp_get_attachment_image( $image_id, 'medium', false, array( 'class' => $id ) );
        $data = array(
            'image'    => $image,
            'id'=> $id
        );
        wp_send_json_success( $data );
    } else {
        wp_send_json_error();
    }
}



/********
 * Callback for add_submenu_page for generating markup of page
 */
function menu_page() {
    $droidmax_options = get_option('droidmax_options');
    echo '<div class="wrap">
        <h2 class="boxed-header">Droidmax Settings</h2>
        <div class="activate-boxed-highlight activate-boxed-option">
            <form method="POST" action="options.php">';
    echo settings_fields('droidmax_options');
    echo admin_form($droidmax_options);
    echo'</div>
        <div class="activate-use-option sidebox first-sidebox">
            <h3>Instruction to use Plugin</h3>
            <hr />
            <h3>Using Shortcode</h3>
            <p>You can place the shortcode<code>[droidmax_feedback]</code>wherever you want to display the Droidmax Feedback</p>
            <hr />
        </div>
    </div>';

}





/**
 * Admin form for Feedabck Settings
 *
 *@since 1.0
 */
function admin_form( $droidmax_options ){
    $image_id_attr_yes=$image_id_attr_no='';
    $image_id_yes = $droidmax_options['dm-button-yes'];
    if(is_numeric($image_id_yes) && $image_id_yes !=''){
        $image_id_attr_yes = esc_attr( $image_id_yes);
        // Change with the image size you want to use
        $image_yes = wp_get_attachment_image( $image_id_yes, 'medium', false, array( 'class' => 'dm-preview-image-yes' ) );
    }else{
        $image_yes = '<img src="'.DROIDMAX_DEFAULT_YES.'" class="dm-preview-image-yes">';
    }
    $image_id_no = $droidmax_options['dm-button-no'];
    if(is_numeric($image_id_no) && $image_id_no !=''){
        $image_id_attr_no = esc_attr( $image_id_no );
        // Change with the image size you want to use
        $image_no = wp_get_attachment_image( $image_id_no, 'medium', false, array( 'class' => 'dm-preview-image-no' ) );
    }else{
        $image_no = '<img src="'.DROIDMAX_DEFAULT_NO.'" class="dm-preview-image-no">';
    }





    // Post Types
    $post_types = get_post_types(array('public' => true), 'names');

    echo '<table class="form-table settings-table">
                <tr>
				<th><label for="dm-select-postion">'.__('Show on','droidmax-feedback').'</label></th>
				<td>';
    // Foreach
    foreach ($post_types as $post_type) {

        // Skip Attachment
        if($post_type == 'attachment'){
            continue;
        }
        // print inputs
        echo '
					<input type="checkbox" name="droidmax_options[dm-show-on][]" id="'.$post_type.'" class="css-checkbox" value="'.$post_type.'" '.__checked_selected_helper( in_array( $post_type, (array)$droidmax_options['dm-show-on'] ),true, false,'checked' ).'>
					<label for="'.$post_type.'" class="css-label cb0">'.__($post_type,'droidmax-feedback').'</label>					
				';
    }
    echo '
            </td>
			</tr>
			<tr>
				<th><label for="dm-select-postion">'.__('Select Position','droidmax-feedback').'</label></th>
				<td>
					<input type="radio" name="droidmax_options[dm-select-position]" id="before-content" value="before-content" '.__checked_selected_helper( $droidmax_options['dm-select-position'] ,'before-content', false,'checked' ).'>Before Content
					<input type="radio" name="droidmax_options[dm-select-position]" id="after-content" value="after-content" '.__checked_selected_helper($droidmax_options['dm-select-position'],'after-content', false,'checked' ).'>After Content
										
				
					
				</td>
			</tr>
		
			<tr>
				<th><label for="dm-title-phrase-1">'.__('Title','droidmax-feedback').'</label></th>
				<td>
					<input type="text" name="droidmax_options[dm-title-phrase-1]" value="'.$droidmax_options['dm-title-phrase-1'].'">';
    if($droidmax_options['dm-title-phrase-1']=='') {
        echo '<small><em>' . __('Ex: Was this article helpful?', 'droidmax-feedback') . ' </em></small>';
    }
    echo '</td>
			</tr>
			<tr>
				<th><label for="dm-title-phrase-2">'.__('Thank you title','droidmax-feedback').'</label></th>
				<td>
					<input type="text" name="droidmax_options[dm-title-phrase-2]" value="'.$droidmax_options['dm-title-phrase-2'].'">';
    if($droidmax_options['dm-title-phrase-2']=='') {
        echo '<small><em>' . __('Ex: We appreciate your helpul feedback!', 'droidmax-feedback') . ' </em></small>';
    }
    echo '</td>
			</tr>
			<tr>
				<th><label for="dm-title-phrase-3">'.__('Thank you sub-title','droidmax-feedback').'</label></th>
				<td>
					<input type="text" name="droidmax_options[dm-title-phrase-3]" value="'.$droidmax_options['dm-title-phrase-3'].'">';
    if($droidmax_options['dm-title-phrase-3']=='') {
        echo '<small><em>'.__('Ex: Your answer will be used to improve our content. The more feedback you give us, the better our pages can be.','droidmax-feedback').' </em></small>';
    }
    echo '</td>
			</tr>	
			<tr>
				<th><label for="dm-title-phrase-5">'.__('Question Selection Title','droidmax-feedback').'</label></th>
				<td>
					<input type="text" name="droidmax_options[dm-title-phrase-5]" value="'.$droidmax_options['dm-title-phrase-5'].'">';
    if($droidmax_options['dm-title-phrase-5']=='') {
        echo '<small><em>' . __('Ex: What went wrong?', 'droidmax-feedback') . ' </em></small>';
    }echo '</td>
			</tr>
			<tr>
				<th><label for="dm-title-phrase-9">'.__('Feedback Form Title','droidmax-feedback').'</label></th>
				<td>
					<input type="text" name="droidmax_options[dm-title-phrase-10]" value="'.$droidmax_options['dm-title-phrase-10'].'">';
    if($droidmax_options['dm-title-phrase-10']=='') {
        echo '<small><em>' . __('Ex: How can we improve it?', 'droidmax-feedback') . ' </em></small>';
    } echo'</td>
			</tr>
			
			<tr>
				<th><label for="dm-title-phrase-9">'.__('Contact Mail','droidmax-feedback').'</label></th>
				<td>
					<input type="text" name="droidmax_options[dm-contact-mail]" value="'.$droidmax_options['dm-contact-mail'].'">';
    if($droidmax_options['dm-contact-mail']=='') {
        echo '<small><em>' . __('Ex: For any question send mail at', 'droidmax-feedback') . ' </em><code>so@pretty.you</code></small>';
    }
    echo '</td>
			</tr>
			
			<tr>
				<th><label>'.__('Button Yes Icon','droidmax-feedback').'</label></th>
				<td>
				<div class="preview-image-container">'.$image_yes.'</div>
                <div><input type="hidden" name="droidmax_options[dm-button-yes]" id="dm-button-yes" value="'.$image_id_attr_yes.'" class="regular-text" />
                <input type="button" class="button-primary dm-button-manager" value="Select a image" data-for="yes"/>
				</div>
				</td>
			</tr>
			<tr>
				<th><label>'.__('Button No Icon','droidmax-feedback').'</label></th>
				<td>
				<div class="preview-image-container">'.$image_no.'</div>
                <div><input type="hidden" name="droidmax_options[dm-button-no]" id="dm-button-no" value="'.$image_id_attr_no.'" class="regular-text" />
                <input type="button" class="button-primary dm-button-manager" value="Select a image" data-for="no"/>
				</div>
				</td>
			</tr>
			<tr>
				<th><label>'.__('Button Submit','droidmax-feedback').'</label></th>
				<td>
					<input type="text" name="droidmax_options[dm-button-submit]" placeholder="Default is Submit" value="'.$droidmax_options['dm-button-submit'].'">
				</td>
			</tr>
			
			<tr>
				<th><label for="dm-exclude-on">'.__('Exclude on','droidmax-feedback').'</label></th>
				<td>
					<input type="text" name="droidmax_options[dm-exclude-on]" value="'.$droidmax_options['dm-exclude-on'].'">
					<small><em>'.__('Comma seperated post id\'s Eg:','droidmax-feedback').' </em><code>1207,1222</code></small>
				</td>
			</tr>
			
			<tr>
				<th><label for="dm-color-submit">'.__('Submit button color','droidmax-feedback').'</label></th>
				<td>
					<input class="color-picker" type="text" name="droidmax_options[dm-color-submit]" data-default-color="#ff4b5a" value="'.$droidmax_options['dm-color-submit'].'">';
    if($droidmax_options['dm-color-submit']=='#ff4b5a') {
        echo '<small><em>' . __('Default is Pinkish-Red', 'droidmax-feedback') . ' </em></small>';
    }echo'</td>
			</tr>		
			<tr>
				<th><label for="dm-submit-hover">'.__('Submit button color on hover','droidmax-feedback').'</label></th>
				<td>
					<input class="color-picker" type="text" name="droidmax_options[dm-color-submit-hover]" data-default-color="#333333" value="'.$droidmax_options['dm-color-submit-hover'].'">';
    if($droidmax_options['dm-color-submit']=='#333333') {
        echo '<small><em>' . __('Default is Black', 'droidmax-feedback') . ' </em></small>';
    }echo '</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="'.__('Save Changes','droidmax-feedback').'">
		</p>
	</form>';

}

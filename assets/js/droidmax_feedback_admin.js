
// Auto Call plugin is class is color-picker
jQuery( document ).ready( function( $ ) {
    $( '.color-picker' ).wpColorPicker();
    jQuery("#fader").on("input",function () {
        //jQuery('#fontsize').css('font-size',jQuery(this).val() + "em");
        jQuery('#fontsize').html(jQuery(this).val() + "em");
        jQuery('#fader').val(jQuery(this).val());
    });


    $( ".dm-button-manager" ).each(function(index) {
        $(this).on("click", function(e){
            e.preventDefault();
            var image_frame;
            var button = $(this).data('for');
            var valueId= '#dm-button-'+button;
            if(image_frame){
                image_frame.open();
            }
            // Define image_frame as wp.media object
            image_frame = wp.media({
                title: 'Select Media',
                multiple : false,
                library : {
                    type : 'image',
                }
            });

            image_frame.on('close',function() {
                // On close, get selections and save to the hidden input
                // plus other AJAX stuff to refresh the image preview
                var selection =  image_frame.state().get('selection');
                var gallery_ids = new Array();
                var my_index = 0;
                selection.each(function(attachment) {
                    gallery_ids[my_index] = attachment['id'];
                    my_index++;
                });
                var ids = gallery_ids.join(",");
                jQuery(valueId).val(ids);
                Refresh_Image(ids,button);
            });

            image_frame.on('open',function() {
                // On open, get the id from the hidden input
                // and select the appropiate images in the media manager
                var selection =  image_frame.state().get('selection');
                var ids = jQuery(valueId).val().split(',');
                ids.forEach(function(id) {
                    var attachment = wp.media.attachment(id);
                    attachment.fetch();
                    selection.add( attachment ? [ attachment ] : [] );
                });

            });

            image_frame.open();
        });
    });


    // Ajax request to refresh the image preview
    function Refresh_Image(the_id,button){
        var data = {
            action: 'droidmax_get_image',
            id: the_id,
            button_type: button
        };

        jQuery.get(DroidmaxFeedback.ajaxurl, data, function(response) {

            if(response.success === true) {
                jQuery("."+response.data.id).replaceWith( response.data.image );
            }
        });

    }

});


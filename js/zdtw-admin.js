/**
 * Created by Sam on 23/05/14.
 */

jQuery(document).ready(function($){

    if( $('.zd-insert-image-button').length ) {

        var custom_uploader,
            insertImgBtn = $('.zd-insert-image-button');

        insertImgBtn.on('click', function(e) {

            e.preventDefault();

            var $this = $(this),
                parentElement = $this.parent(),
                srcOutput = parentElement.find("[id^='zd-image-src']"),
                idOutput = parentElement.find("input[id^='zd-image-id']"),
                imagePreview = parentElement.find("div[id^='zd-image-preview']");

            //If the uploader object has already been created, reopen the dialog
            if (custom_uploader) {
                custom_uploader.open();
                return;
            }

            //Extend the wp.media object
            custom_uploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: false
            });

            //When a file is selected, grab the URL and set it as the text field's value
            custom_uploader.on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();

                srcOutput.val(attachment.url);
                idOutput.val( attachment.id );
                imagePreview
                    .css({
                        'background': 'url(' + attachment.url +') no-repeat',
                        'width': attachment.width,
                        'height': attachment.height
                    });

//                custom_uploader.close();
                $('.image-preview-label').removeClass('zd-hide');
            });

            //Open the uploader dialog
            custom_uploader.open();

        });

        $('.zd-remove-image-button').on('click', function() {

            var $this = $(this),
                parentElement = $this.parent(),
                srcOutput = parentElement.find("[id^='zd-image-src']"),
                idOutput = parentElement.find("input[id^='zd-image-id']"),
                imagePreview = parentElement.find("div[id^='zd-image-preview']");

            srcOutput.val('');
            idOutput.val('');
            imagePreview.removeAttr('style');

            insertImgBtn.text('Insert image');

            $('.image-preview-label').addClass('zd-hide');

            return false;
        });
    }
});
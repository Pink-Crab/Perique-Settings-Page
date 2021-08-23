jQuery(document).ready(function($) {

    // Open media library.
    $('.access_media_library').click(function(e) {
        var key = $(this).data('key');

        e.preventDefault();
        var image_frame;
        if (image_frame) {
            image_frame.open();
        }
        // Define image_frame as wp.media object
        image_frame = wp.media({
            title: 'Select Media',
            multiple: false,
            library: {
                type: 'image',
            }
        });

        // On image selection
        image_frame.on('select', function() {
            var selection = image_frame.state().get('selection');
            var gallery_ids = new Array();
            var my_index = 0;

            selection.each(function(attachment) {
                gallery_ids[my_index] = attachment['id'];
                my_index++;
            });

            var ids = gallery_ids.join(",");
            $('input#' + key).val(ids);
            reloadPreviewImage(ids, key);
        });

        // On opening the media library.
        image_frame.on('open', function() {
            var selection = image_frame.state().get('selection');
            var ids = $('input#' + key).val().split(', ');

            ids.forEach(function(id) {
                var attachment = wp.media.attachment(id);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            });

        });

        image_frame.open();
    });

    /**
     * Reloads the preview image from media libaray rest api.
     * @param {int} mediaId The media library id
     * @param {sting} key The field key.
     */
    function reloadPreviewImage(mediaId, key) {
        $.get(
            pc_setting_page.mediaLibraryPreviewEndPoint + '/' + mediaId, {},
            function(response) {
                console.log(response);
                if (typeof response.media_details !== 'undefined' && response.media_details.sizes.medium.source_url) {
                    $('#media_upload_' + key + ' .media_upload__preview img').attr("src", response.media_details.sizes.medium.source_url);
                    $('#' + key + '_title').text(response.title.rendered);
                }
            });
    }

    // Clear media library selection.
    $('.media_upload_clear').click(function(e) {
        var key = $(this).data('key');
        e.preventDefault();

        $('#media_upload_' + key + ' .media_upload__preview img').attr("src", pc_setting_page.mediaLibraryNoImagePlaceholder);
        $('#' + key).val('');
    });
});
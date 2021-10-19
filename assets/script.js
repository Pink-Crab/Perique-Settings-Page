jQuery(document).ready(function($) {

    // Open media library.
    $('.access_media_library').click(function(e) {
        let key = $(this).data('key');

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
            let selection = image_frame.state().get('selection');
            let gallery_ids = new Array();
            let my_index = 0;

            selection.each(function(attachment) {
                gallery_ids[my_index] = attachment['id'];
                my_index++;
            });

            let ids = gallery_ids.join(",");
            $('input#' + key).val(ids);
            reloadPreviewImage(ids, key);
        });

        // On opening the media library.
        image_frame.on('open', function() {
            let selection = image_frame.state().get('selection');
            let ids = $('input#' + key).val().split(', ');

            ids.forEach(function(id) {
                let attachment = wp.media.attachment(id);
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
            pc_setting_page_scripts.mediaLibraryPreviewEndPoint + '/' + mediaId, {},
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
        let key = $(this).data('key');
        e.preventDefault();

        $('#media_upload_' + key + ' .media_upload__preview img').attr("src", pc_setting_page_scripts.mediaLibraryNoImagePlaceholder);
        $('#' + key).val('');
    });

    /** 
     * REPEATER
     */

    // Add new row
    $('body').on('click', '#repeater__add', function(e) {
        let fieldKey = $(this).attr('data-field-id');
        e.preventDefault();

        let rowTemplate = $('#' + fieldKey + '__template').text();
        let repeaterParent = '#' + fieldKey + '.repeater__columns';

        // Get the next index from existing rows.
        let rows = $(repeaterParent).children().map(function(i, row) {
            return parseInt($(row).attr("data-row"));
        });
        let nextIndex = rows.empty() ?
            rows[rows.length - 1] + 1 :
            0;

        // Prepend the current rows with an empty template.
        $(repeaterParent).append(rowTemplate.replaceAll('%i%', nextIndex))

        // Update the sort order.
        updateRepeaterSortOrder(fieldKey)
    });

    // Allow sorting.
    $(".repeater__columns").sortable({
        handle: ".group_handle",
        items: "> .repeater_group__column",
        stop: function(event, ui) {
            let fieldKey = $(this).attr('id');
            updateRepeaterSortOrder(fieldKey)
        }
    });

    function updateRepeaterSortOrder(fieldKey) {
        let rows = $('#' + fieldKey + '.repeater__columns').sortable('toArray', { attribute: 'data-row' });
        $('#' + fieldKey + '_sortorder').val(Array.from(rows).join());
    }






});
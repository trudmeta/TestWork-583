(function($) {
    $(function() {

        $('.js-add-media').on('click', function(e){
            e.preventDefault();

            const tw_uploader = wp.media({
                title: 'Download image',
                library: {
                    // uploadedTo: wp.media.view.settings.post.id,
                    type: 'image'
                },
                button: {
                    text: 'Select'
                },
                multiple: false
            }).on('select', function() {

                let attachment = tw_uploader.state().get('selection').first().toJSON();
                $('.js-hidden-src').val(attachment.url);
                $('.js-hidden-img-id').val(attachment.id);
                $('.js-product-img').attr('src', attachment.url);

            }).open();

        });

    });//document ready
})(jQuery);
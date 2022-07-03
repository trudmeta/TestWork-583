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


        $('.js-remove-media').on('click', function(e){

            if(confirm('Are you shure?')){

                let data = {
                    action: 'clear_custom_fields_callback',
                    post_ID: $('input[id="post_ID"]').val(),
                    type: 'media'
                };
                $.post( ajaxurl, data, function( response ){
                    let resp = JSON.parse(response);
                    if(resp && resp['status'] === 'ok'){

                        clear_custom_fields('img');

                    }
                } );
            }

        });


        //Очистка полей
        $('.js-clear-custom').on('click', function(e){
            e.preventDefault();

            let data = {
                action: 'clear_custom_fields_callback',
                post_ID: $('input[id="post_ID"]').val(),
                type: 'all'
            };
            $.post( ajaxurl, data, function( response ){
                let resp = JSON.parse(response);
                if(resp && resp['status'] === 'ok'){

                    clear_custom_fields();

                }
            } );

        });

        function clear_custom_fields(type='all'){

            if(type === 'all'){
                clear_img();
                clear_date();
                clear_type();
            }else if(type === 'img'){
                clear_img();
            }

            function clear_img(){
                const $product_img = $('.js-product-img');
                const default_img = $product_img.attr('data-src');
                $product_img.attr('src', default_img);
                $('.js-hidden-src').val("");
            }
            function clear_date(){
                $('.js-tw_time').val("");
            }
            function clear_type(){
                $('.js-product_type').val([]) ;
            }
        }

    });//document ready
})(jQuery);
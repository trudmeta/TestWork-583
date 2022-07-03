<?php

//testwork

add_action('admin_enqueue_scripts', function(){

    global $pagenow;
    if ($pagenow == 'post.php' && get_post_type() == 'product') {

        wp_enqueue_media();

        wp_enqueue_style( 'tw-admin-css', get_stylesheet_directory_uri() .'/assets/css/admin.css');
        wp_enqueue_script( 'tw-admin-js', get_stylesheet_directory_uri() .'/assets/js/admin.js', array('jquery'), null, true );

    }

}, 99);


add_action('wp_enqueue_scripts', function(){

    if (is_page('create-product')){
        wp_enqueue_style('bootstrap-css',  get_stylesheet_directory_uri() .'/assets/css/bootstrap.min.css');
        wp_enqueue_style('main-styles',  get_stylesheet_directory_uri() .'/assets/css/styles.css');

        wp_enqueue_media();
        wp_enqueue_script('frontend-script', get_stylesheet_directory_uri() . '/assets/js/script.js', array( 'jquery' ), null, true);
    }

}, 99);



add_action('add_meta_boxes', 'tw_meta_box_init');

// meta box functions for adding the meta box and saving the data
function tw_meta_box_init() {

    // create our custom meta box
    add_meta_box('tw-meta', 'Product Settings', 'tw_meta_box', 'product', 'side', 'high');

}


function tw_meta_box($post, $box) {

    // retrieve the custom meta box values
    $tw_img = get_post_meta( $post->ID, 'tw_product_img', true );
    $tw_time = get_post_meta( $post->ID, 'tw_time', true );
    $tw_type = get_post_meta( $post->ID, 'tw_product_type', true );

    $default = get_stylesheet_directory_uri() . '/assets/images/noimage.jpg';

    $image = !empty($tw_img)? $tw_img : $default;

    // custom meta box form elements
    echo '<p><a href="#" class="js-add-media"><img src="'.$image.'" data-src="'.$default.'" class="js-product-img" alt=""></a>';
    echo '<input type="hidden" class="js-hidden-src" name="tw_product_img" value="';if(!empty($tw_img)) echo $image; echo '">';
    echo '<input type="hidden" class="js-hidden-img-id" name="tw_product_img_id" value="">
          <input type="button" class="button js-remove-media" value="Delete">';
    echo '<p>Date: <input type="date" name="tw_time" class="js-tw_time" value="'.esc_attr( $tw_time ).'" /></p>';
    echo '<p>Type:
        <select name="tw_product_type" class="js-product_type">
            <option value="">Select option</option>
            <option value="rare" ' .selected( $tw_type, 'rare', false ). '>Rare</option>
            <option value="frequent" ' .selected( $tw_type, 'frequent', false ). '>Frequent</option>
            <option value="unusual" ' .selected( $tw_type, 'unusual', false ). '>Unusual</option>
        </select></p>';
    echo '<p><button class="js-clear-custom">Clear all</button></p>';
    echo '<p><input type="submit" name="save" class="button button-primary button-large" value="Обновить"></p>';

}


// hook to save our meta box data when the post is saved
add_action( 'save_post_product', 'tw_save_meta_box' );

function tw_save_meta_box($post_id) {

    // process form data if $_POST is set
    if (isset($_POST['tw_product_img']) || isset( $_POST['tw_time'] ) || isset( $_POST['tw_product_type'])) {

        // if auto saving skip saving our meta box data
        if (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)
            return;

        //Изменение изображения
        if (!empty($_POST['tw_product_img_id']) && $post_id){

            $imageID = sanitize_text_field($_POST['tw_product_img_id']);
            set_post_thumbnail( $post_id, $imageID );
            update_post_meta( $post_id, '_thumbnail_id', $imageID );

        }

        !empty($_POST['tw_product_img']) && update_post_meta( $post_id, 'tw_product_img', sanitize_text_field( $_POST['tw_product_img'] ) );
        !empty($_POST['tw_time']) && update_post_meta( $post_id, 'tw_time', sanitize_text_field( $_POST['tw_time'] ) );
        !empty($_POST['tw_product_type']) && update_post_meta( $post_id, 'tw_product_type', sanitize_text_field( $_POST['tw_product_type'] ) );

    }

}


//parent theme styles
add_action('wp_enqueue_scripts', 'my_child_theme_scripts');
function my_child_theme_scripts() {
    wp_enqueue_style( 'parent-style', get_stylesheet_directory_uri() . '/style.css' );
}


//ajax, clear all on product edit page
add_action('wp_ajax_clear_custom_fields_callback', 'clear_custom_fields_callback');
function clear_custom_fields_callback(){

    $response = [];
    $type = sanitize_text_field( $_POST['type'] );

    if ($post_ID = sanitize_text_field( $_POST['post_ID'] )){
        
        if ($type == 'all'){
            delete_post_meta($post_ID, 'tw_product_img');
            delete_post_meta($post_ID, 'tw_time');
            delete_post_meta($post_ID, 'tw_product_type');
        } elseif ($type == 'media'){
            delete_post_meta($post_ID, 'tw_product_img');
        }


        $response['status'] = 'ok';
        echo json_encode($response);

    }

    wp_die();
}


//product creation functionality
add_action('wp', 'create_product_methods', 10, 1);

function create_product_methods($wp){
    if (!is_admin() && is_page('create-product')){
        include_once 'inc/create-product.php';
    }
}


<?php
/**
 * Template Name: Create Product
 */

get_header(); ?>

	<div id="primary" class="content-area create-block">
		<main id="create_main" class="site-main" role="main">

			<?php

			do_action( 'create_product' );

            //create product
			if (isset($_POST['submit_create_product']) && wp_verify_nonce($_POST['create_product_nonce'], 'create_action')){

                $errors = [];

                $data = [];

			    if (!empty($_POST['product_name'])){
                    $data['name'] = sanitize_text_field($_POST['product_name']);
                } else {
			        $errors[] = 'Product name is required';
                }
			    if (!empty($_POST['product_type'])){
                    $data['type'] = sanitize_text_field($_POST['product_type']);
                } else {
                    $data['type'] = 'simple';
                }
			    if (!empty($_POST['product_cat'])){
                    $data['category_ids'] = array( sanitize_text_field($_POST['product_cat']) );
                }
			    if (!empty($_POST['product_price'])){
                    $data['regular_price'] = sanitize_text_field($_POST['product_price']);
                } else {
                    $data['regular_price'] = 0;
                }
			    if (!empty($_POST['product_sale_price'])){
                    $data['sale_price'] = sanitize_text_field($_POST['product_sale_price']);
                }
			    if (!empty($_POST['tw_product_img_id']) && current_user_can('upload_files')){
                    $data['image_id'] = sanitize_text_field($_POST['tw_product_img_id']);
                }
                $data['description'] = sanitize_textarea_field($_POST['description']);
                $data['short_description'] = sanitize_textarea_field($_POST['short_description']);


                if (!empty($_FILES['product_img'])){

                    require_once(ABSPATH.'wp-admin/includes/media.php');
                    require_once(ABSPATH.'wp-admin/includes/file.php');
                    require_once(ABSPATH.'wp-admin/includes/image.php');

                    $upload_dir = wp_upload_dir();

                    $file = $_FILES['product_img'];
                    $overrides = [ 'test_form' => false ];

                    $movefile = wp_handle_upload( $file, $overrides );

                    if ($movefile && empty($movefile['error'])) {
                        echo "Upload success.";

                        $filetype = wp_check_filetype( basename( $movefile['file'] ), null );
                        $attachment = array(
                            'guid'           => $movefile['url'],
                            'post_mime_type' => $filetype['type'],
                            'post_title'     => basename( $movefile['file'] ),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        );

                        $attach_id = wp_insert_attachment( $attachment, $movefile['file'] );

                        $attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
                        wp_update_attachment_metadata( $attach_id, $attach_data );

                        if ($attach_id){
                            $data['image_id'] = $attach_id;
                        }

                    } else {
                        echo "Upload error!";
                    }

                }

			    if (!empty($errors)){
			        echo 'Create errors:';
			        foreach($errors as $error){
			            echo $error . '<br>';
                    }
                } else {

                    $product_id = create_product($data);

                    if ($product_id){

                        !empty($movefile['url']) && update_post_meta( $product_id, 'tw_product_img', $movefile['url'] );
                        !empty($_POST['tw_time']) && update_post_meta( $product_id, 'tw_time', sanitize_text_field( $_POST['tw_time'] ) );
                        !empty($_POST['tw_product_type']) && update_post_meta( $product_id, 'tw_product_type', sanitize_text_field( $_POST['tw_product_type'] ) );

                        echo '<h3>Product created successfully!</h3>';

                    }
                }

            }
			?>

            <form class="row g-3" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('create_action','create_product_nonce'); ?>

                <div class="col-md-12">
                    <label for="inputName" class="form-label">Name</label>
                    <input type="text" name="product_name" class="form-control" id="inputName">
                </div>
                <div class="col-md-6">
                    <label for="inputType" class="form-label">Type</label>
                    <select id="inputType" class="form-select" name="product_type">
                        <option selected>Choose type</option>
                        <option value="simple">Simple</option>
                        <option value="grouped">Grouped</option>
                        <option value="variable">Variable</option>
                        <option value="external">External</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="inputCategories" class="form-label">Categories</label>
                    <?php
                        wc_product_dropdown_categories([
                                'class' => 'form-select',
                                'name'  => 'product_cat',
                                'value_field' => 'term_id'
                        ]);
                    ?>
                </div>
                <div class="col-md-6">
                    <label for="inputPrice" class="form-label">Regular price</label>
                    <input type="text" name="product_price" class="form-control" id="inputPrice">
                </div>
                <div class="col-md-6">
                    <label for="inputPrice" class="form-label">Sale price</label>
                    <input type="text" name="product_sale_price" class="form-control" id="inputSalePrice">
                </div>
                <div class="mb-3">
                    <?php
                    if (current_user_can('upload_files')) { ?>
                        <input type="hidden" class="js-hidden-img-id" name="tw_product_img_id" value="">
                        <input type="hidden" class="js-hidden-src" name="tw_product_img" value="">
                        <a href="#" class="js-add-media">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/noimage.jpg'; ?>" class="js-product-img" alt="">
                        </a>
                    <?php } else { ?>
                        <label for="inputImage" class="form-label">Image</label>
                        <input class="form-control" type="file" id="inputImage" name="product_img">
                    <?php } ?>
                </div>
                <div class="col-md-6">
                    <label for="inputDate" class="form-label">Date</label>
                    <input type="date" name="tw_time" class="form-control" id="inputDate">
                </div>
                <div class="col-md-6">
                    <label for="inputTWType" class="form-label">Type</label>
                    <select id="inputTWType" class="form-select" name="tw_product_type">
                        <option selected>Choose type</option>
                        <option value="rare">Rare</option>
                        <option value="frequent">Frequent</option>
                        <option value="unusual">Unusual</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="inputDesc" class="form-label">Description</label>
                    <textarea class="form-control" id="inputDesc" name="description" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="inputShortDesc" class="form-label">Short description</label>
                    <textarea class="form-control" id="inputShortDesc" name="short_description" rows="3"></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" name="submit_create_product" class="btn btn-primary">Create</button>
                </div>
            </form>

		</main><!-- #main -->
	</div><!-- #primary -->
<?php
get_footer();

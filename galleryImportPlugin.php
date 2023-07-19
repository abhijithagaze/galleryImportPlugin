    <?php
    /*
    Plugin Name: Image Uploader Plugin
    Plugin URI: https://your-plugin-url.com
    Description: Add images into product gallery from S3 bucket.
    Version: 1.0
    Author: Abhijith  Santhosh
    */
    // Step 1: Create the Settings Page
    function image_uploader_plugin_settings_page()
    {
        add_submenu_page(
            'options-general.php',
            'Image Uploader Plugin Settings',
            'Image Uploader',
            'manage_options',
            'image-uploader-settings',
            'image_uploader_plugin_settings_callback'
        );
    }
    function image_uploader_plugin_settings_callback()
    {
        // Check if the user has permissions to access the settings page
        if (!current_user_can('manage_options')) {
            return;
        }
        // Save the URL in the database when the form is submitted
        if (isset($_POST['image_uploader_url'])) {
            save_image_uploader_url();
        }
        // Handle the image import on button click
        if (isset($_POST['import_images'])) {
            fetch_and_attach_images();
        }
        // Display the settings page form
    ?>
        <div class="wrap">
            <h1>Set Your URL Here</h1>
            <form method="post" action="">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="image_uploader_url">Enter S3 Bucket URL:</label></th>
                        <td>
                            <input type="text" name="image_uploader_url" id="image_uploader_url" value="<?php echo esc_attr(get_option('image_uploader_url')); ?>">
                            <p class="description">Enter the URL of your S3 bucket where the images are stored.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save URL'); ?>
            </form>
            <!-- Add the Import button -->
            <form method="post" action="">
                <?php
                // Nonce field for security
                wp_nonce_field('import_images_action', 'import_images_nonce');
                ?>
                <input type="hidden" name="import_images" value="1">
                <?php submit_button('Import Images', 'primary', 'import_images_button'); ?>
            </form>
        </div>
    <?php
    }
    // Step 2: Save the URL in the Database
    function save_image_uploader_url()
    {
        // Handle the uploaded S3 bucket URL here
        $s3_bucket_url = sanitize_text_field($_POST['image_uploader_url']);
        update_option('image_uploader_url', $s3_bucket_url);
        echo '<div class="notice notice-success"><p>URL successfully saved!</p></div>';
    }
    // Hook the settings page function to the admin menu
    add_action('admin_menu', 'image_uploader_plugin_settings_page');
    // Step 3: Retrieve Product IDs
    function get_product_ids()
    {
        // Use get_posts() or any relevant WooCommerce function to get the product IDs
        $products = get_posts(array(
            'post_type' => 'product',
            'posts_per_page' => -1,
        ));
        $product_ids = array();
        foreach ($products as $product) {
            $product_ids[] = $product->ID;
        }
        return $product_ids;
    }
    // Step 4: Fetch Images and Attach to Products
    function fetch_and_attach_images()
    {
        $s3_bucket_url = get_option('image_uploader_url');
        // Check if the S3 bucket URL is set
        if (empty($s3_bucket_url)) {
            return;
        }
        $product_ids = get_product_ids();
        foreach ($product_ids as $product_id) {
            $image_filename = $product_id . '.jpg'; // Assuming the image filename is the same as the product ID and has a .jpg extension
            // Construct the URL of the image in the S3 bucket
            $image_url = trailingslashit($s3_bucket_url) . $image_filename;
           
           
            attach_images($image_url, $product_id);
            // exit;
        }
    }
    function attach_images($image_url, $product_id)
    {
        // Upload the image to the WordPress media library
        $attachment_id = media_sideload_image($image_url,null,null,'id');
         if (is_wp_error($attachment_id)) {
            // There was an error uploading the image
            echo 'Error uploading image: ' . $attachment_id->get_error_message();
            } else {
            // Image uploaded successfully, and $attachment_id contains the attachment ID
            echo 'Image uploaded successfully! Attachment ID: ' . $attachment_id;
            // Append the attachment ID to the product gallery
            $product_gallery = get_post_meta($product_id, '_product_image_gallery', true);
            $product_gallery .= ',' . $attachment_id;
            update_post_meta($product_id, '_product_image_gallery', $product_gallery);
        }
    }

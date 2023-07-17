<?php
/*
Plugin Name: Image Uploader Plugin
Plugin URI: https://your-plugin-url.com
Description: Add images into product gallery from S3 bucket.
Version: 1.0
Author: Abhijith  Santhosh

*/

// Step 1: Create the Settings Page
function image_uploader_plugin_settings_page() {
    add_submenu_page(
        'options-general.php',
        'Image Uploader Plugin Settings',
        'Image Uploader',
        'manage_options',
        'image-uploader-settings',
        'image_uploader_plugin_settings_callback'
    );
}

function image_uploader_plugin_settings_callback() {
    // Check if the user has permissions to access the settings page
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save the URL in the database when the form is submitted
    if (isset($_POST['image_uploader_url'])) {
        save_image_uploader_url();
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
    </div>
    <?php
}

// Step 2: Save the URL in the Database
function save_image_uploader_url() {
    // Handle the uploaded S3 bucket URL here
    $s3_bucket_url = sanitize_text_field($_POST['image_uploader_url']);
    update_option('image_uploader_url', $s3_bucket_url);
    print_r($s3_bucket_url);
    echo '<div class="notice notice-success"><p>URL successfully saved!</p></div>';
}

// Hook the settings page function to the admin menu
add_action('admin_menu', 'image_uploader_plugin_settings_page');

// Step 3: Retrieve Product IDs
function get_product_ids() {
    // Use get_posts() or any relevant WooCommerce function to get the product IDs
    $products = get_posts(array(
        'post_type' => 'product',
        'posts_per_page' => -1,
    ));

    $product_ids = array();
    foreach ($products as $product) {
        $product_ids[] = $product->ID;
    }
    print_r( $product_ids);
    return $product_ids;
    print_r( $product_ids);
}
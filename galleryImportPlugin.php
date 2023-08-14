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
                        <th scope="row"><label for="image_uploader_url">Enter cloud URL:</label></th>
                        <td>
                            <input type="text" name="image_uploader_url" id="image_uploader_url" value="<?php echo esc_attr(get_option('image_uploader_url')); ?>">
                            <p class="description">Enter the cloud  URL where the images are stored.</p>
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
        $cloud_url = sanitize_text_field($_POST['image_uploader_url']);
        update_option('image_uploader_url', $cloud_url);
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
  
  
    // Function to fetch and attach images from SharePoint
function fetch_and_attach_images() {
    $sharepoint_url = 'https://16sknv.sharepoint.com/';
    $access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsIng1dCI6Ii1LSTNROW5OUjdiUm9meG1lWm9YcWJIWkdldyIsImtpZCI6Ii1LSTNROW5OUjdiUm9meG1lWm9YcWJIWkdldyJ9.eyJhdWQiOiIwMDAwMDAwMy0wMDAwLTBmZjEtY2UwMC0wMDAwMDAwMDAwMDAvMTZza252LnNoYXJlcG9pbnQuY29tQDkxMTY1ZDQ0LTRjZDktNDJmYy05ZGRlLWIyYzgzNzdmMzRhZiIsImlzcyI6IjAwMDAwMDAxLTAwMDAtMDAwMC1jMDAwLTAwMDAwMDAwMDAwMEA5MTE2NWQ0NC00Y2Q5LTQyZmMtOWRkZS1iMmM4Mzc3ZjM0YWYiLCJpYXQiOjE2OTE5ODg2NjgsIm5iZiI6MTY5MTk4ODY2OCwiZXhwIjoxNjkyMDc1MzY4LCJpZGVudGl0eXByb3ZpZGVyIjoiMDAwMDAwMDEtMDAwMC0wMDAwLWMwMDAtMDAwMDAwMDAwMDAwQDkxMTY1ZDQ0LTRjZDktNDJmYy05ZGRlLWIyYzgzNzdmMzRhZiIsIm5hbWVpZCI6ImRmMjBjODg1LTM5OWYtNDg0YS05Mjk4LWYyNDE4ODI4YzI1YkA5MTE2NWQ0NC00Y2Q5LTQyZmMtOWRkZS1iMmM4Mzc3ZjM0YWYiLCJvaWQiOiJjM2MyZDMyZC04NzczLTRiMzMtODU1Yy1hNzI0ZmNkNzA3Y2EiLCJzdWIiOiJjM2MyZDMyZC04NzczLTRiMzMtODU1Yy1hNzI0ZmNkNzA3Y2EiLCJ0cnVzdGVkZm9yZGVsZWdhdGlvbiI6ImZhbHNlIn0.TL99KYr3nPD6LXgyXX1W1IfhROyNSlsPY3ZldHO7XGQxtTBZ4g__YXuG80fgM5NbE-KwDRtzcU7ssE3wxsjOLcV1NTHCL3A35zQ97Ps0YitvlnziOKGBn2B_hiJTa-YH1UJS31arnPIj69pOFXPZYwESvvd-6lLbE--AptkWWmM55-VkTN36wz12fjuLEEAKlPUvLHG6foOOqMl5aPI-IIR_qDgSjl69WmLPXPj-g9h64Ask4Ova_1x24i_-nRd4TCEzAvNIu1zmKGNV4D_ShxPvE_tS3Y93En1WkkbDnIan5SV9y2hCV3i1Hb8FTodnf2lUPTwOifF8KIMHDZSpkQ'; // Replace with your access token

    $product_ids = get_product_ids();

    foreach ($product_ids as $product_id) {
        // $image_filename = $product_id . '.jpg';
        $item_id = $product_id; // Assuming item IDs match product IDs

        $image_url = construct_sharepoint_image_url($sharepoint_url, $item_id, $access_token);

        if ($image_url) {
            attach_images($image_url, $product_id);
        }
    }
}

// Function to construct SharePoint image URL
function construct_sharepoint_image_url($sharepoint_url, $item_id, $access_token) {
    $api_endpoint = "https://16sknv.sharepoint.com/_api/web/GetFolderByServerRelativeUrl('/gallery_1')/Files";
    
    $headers = array(
        'Authorization' => 'Bearer ' . $access_token,
    );

    $response = wp_safe_remote_get($api_endpoint, array('headers' => $headers));

    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
        $data = wp_remote_retrieve_body($response);
        $json_data = json_decode($data);

        if (isset($json_data->ServerRelativeUrl)) {
            $image_url = "$sharepoint_url" . $json_data->ServerRelativeUrl;
            return $image_url;
        }
    }       

    // return null;
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
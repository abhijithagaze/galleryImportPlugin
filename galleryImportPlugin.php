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

        // // Save the URL in the database when the form is submitted
        // if (isset($_POST['image_uploader_url'])) {
        //     save_image_uploader_url();
        // }

        // Handle the image import on button click
        if (isset($_POST['sku_without_img_button'])) {
            fetch_and_attach_images();
        }
        if (isset($_POST['import_all_button'])) {
          
        }
        if (isset($_POST['view-button'])) {
            fetch_images_from_medialibrary();
        }

        // Display the settings page form
    ?>
       <div class='section section--import section--import--images' style="background-color: #f7f7f7; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); display: flex; justify-content: space-between; align-items: center;">
    <h2 style="margin-bottom: 10px;">Import Product Images</h2>
    <div class='section--content' style="position: relative;">
        <form method="post" action="">
            <?php
            wp_nonce_field('import_images_action2','import_images_nonce2');
            ?>
            <input type="hidden" name="sku_without_img_button" valu="1">
            <?php submit_button('CLICK HERE TO GET PRODUCTS SKU WITHOUT IMAGE GALLERY','Primary','import_images_button2'); ?>
        </form>
        <!-- <button id="sku_without_img" style="background-color: #4CAF50; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s, transform 0.2s;">
           CLICK HERE TO GET PRODUCTS SKU WITHOUT IMAGE GALLERY
        </button> -->
        <!-- Dropdown menu to display fetched content -->
        <select id="contentDropdown" style="position: absolute; top: 100%; left: 0; margin-top: 5px; display: none;">
            <option value="">Select content</option>
        </select>
    </div>
    <form method="post" action="">
        <?php
        wp_nonce_field('import_images_action','import_images_nonce');
        ?>
        <input type="hidden" name="import_all_button" valu="1">
        <?php submit_button('Import All','Primary','import_images_button'); ?>
    </form>
    <!-- <button id="import-all-button" style="background-color: #FF9800; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s, transform 0.2s;">
        IMPORT ALL
    </button>
     -->
</div>

<!-- New section for entering SKU to import image -->
<!-- <div class='section section--import' style="background-color: #f7f7f7; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
    <label for="skuInput" style="font-weight: bold;">ENTER SKU TO IMPORT IMAGE:</label>
    <div>
        <input type="text" id="skuInput" style="padding: 10px; border: 5px solid #ccc; border-radius: 3px; margin-right: 763px;">
        <button id="viewButton" style="background-color: #3498db; color: white; padding: 5px 10px; border: none; border-radius: 30px; cursor: pointer; transition: background-color 0.3s, transform 0.2s;">VIEW</button>
    </div>
</div> -->

<div class='section section--import' style="background-color: #f7f7f7; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
    <label for="skuInput" style="font-weight: bold;">ENTER SKU/Product ID TO VIEW GALLERY:</label>
    <div>
        <input type="text" id="product_id" style="padding: 10px; border: 5px solid #ccc; border-radius: 3px; margin-right: 763px;">
        <form method="post" action="">
            <?php
            wp_nonce_field('import_images_action2','import_images_nonce2');
            ?>
            <input type="hidden" name="view_button" valu="1">
            <?php submit_button('viewButton','Primary','import_images_button3'); ?>
        </form>
        <!-- <button id="viewButton" style="background-color: #3498db; color: white; padding: 5px 10px; border: none; border-radius: 30px; cursor: pointer; transition: background-color 0.3s, transform 0.2s;">VIEW</button> -->
    </div>
</div>

<!-- <script>
    // Function to fetch and populate the dropdown with content
    function fetchContent() {
        // Assuming you have a PHP function that fetches content
        // and returns it as a JSON response
        // Example PHP function: fetch_content_from_external_source()
        
        // You can use AJAX to fetch content from the server
        // Here, we're using a dummy response for demonstration
        const dummyResponse = [
            { value: 'item1', label: 'Item 1' },
            { value: 'item2', label: 'Item 2' },
            { value: 'item3', label: 'Item 3' }
        ];
        
        const contentDropdown = document.getElementById('contentDropdown');
        
        // Clear existing options
        contentDropdown.innerHTML = '<option value="">Select content</option>';
        
        // Populate dropdown with fetched content
        dummyResponse.forEach(item => {
            const option = document.createElement('option');
            option.value = item.value;
            option.textContent = item.label;
            contentDropdown.appendChild(option);
        });
        
        // Display the dropdown
        contentDropdown.style.display = 'block';
    }
    
    // Attach click event handler to the button
    const sku_without_img = document.getElementById('sku_without_img');
    sku_without_img.addEventListener('click', fetchContent);
    
    // Function to handle "View" button click
    // const viewButton = document.getElementById('viewButton');
    // viewButton.addEventListener('click', function () {
    //     const skuInput = document.getElementById('skuInput').value;
    //     console.log('SKU to import image:', skuInput);
    //     // You can perform the necessary action with the SKU here
    // });
</script> -->
    <?php
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
        // $s3_bucket_url = get_option('image_uploader_url');
        // // Check if the S3 bucket URL is set
        // if (empty($s3_bucket_url)) {
        //     return;
        // }
        // $product_ids = get_product_ids();
        // foreach ($product_ids as $product_id) {
        //     $image_filename = $product_id . '.jpg'; // Assuming the image filename is the same as the product ID and has a .jpg extension
        //     // Construct the URL of the image in the S3 bucket
        //     $image_url = trailingslashit($s3_bucket_url) . $image_filename;
        
        //    print_r("fetch attach function called")
            // attach_images($image_url, $product_id);
    }

    function fetch_images_from_medialibrary(){
        if (isset($_POST['product_id'])) {
            $product_id = intval($_POST['product_id']);
            print_r($product_id);
            
            // Fetch product by ID
            $product = wc_get_product($product_id);
            print_r($product);
            
            if ($product) {
                // Get product gallery images
                $gallery_ids = $product->get_gallery_image_ids();
                print_r($gallery_ids);
                if (!empty($gallery_ids)) {
                    foreach ($gallery_ids as $image_id) {
                        $image_url = wp_get_attachment_image_url($image_id, 'full');
                        echo '<img src="' . esc_url($image_url) . '" alt="Product Image">';
                    }
                } else {
                    echo 'No gallery images found for the specified product.';
                }
            } else {
                echo 'Product not found with the specified ID.';
            }
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

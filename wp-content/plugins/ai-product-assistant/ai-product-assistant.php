<?php
/*
Plugin Name: AI Product Assistant
Plugin URI: http://localhost/ecommerce-ai
Description: Automatically generates SEO-friendly WooCommerce product descriptions using a local LLM (e.g., LLaMA 3 via Ollama).
Author: Ioneasa Cristina
*/

// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Adds a "Generate AI Description" button to the WooCommerce product editor.
 * When clicked, it calls an AJAX endpoint that generates a description via Ollama.
 */
function ai_product_assistant_add_button() {
    global $post;

    // Only show this button on WooCommerce product edit screens
    if ('product' !== $post->post_type) {
        return;
    }

    echo '<div style="margin:15px 0;">';
    echo '<button type="button" class="button button-primary" id="generate-ai-description">🧠 Generate AI Description</button>';
    echo '</div>';

    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($){
        $('#generate-ai-description').on('click', function(e){
            e.preventDefault();

            // Get the product title from the title input
            const title = $('#title').val();
            
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'ai_generate_description',
                    product_title: title
                },
                beforeSend: function() {
                    alert('Generating AI description...');
                },
                success: function(response) {
                    if(response.success){
                        $('#content').val(response.data);
                        alert('✅ Description generated!');
                    } else {
                        alert('⚠️ Error: ' + response.data);
                    }
                }
            });
        });
    });
    </script>
    <?php
}
add_action('edit_form_after_title', 'ai_product_assistant_add_button');


/**
 * AJAX endpoint for generating a product description using Ollama.
 * Receives a product title, sends a prompt to Ollama, returns the generated text.
 */
function ai_product_assistant_generate_description() {
    if (!isset($_POST['product_title'])) {
        wp_send_json_error('Missing product title.');
    }

    $product_title = sanitize_text_field($_POST['product_title']);

    // Prompt for the LLM: ask for an SEO-friendly product description
    $prompt = "Creează o descriere SEO atractivă pentru produsul: {$product_title}. 
               Include caracteristici cheie, beneficii și un call-to-action convingător.";

    // Ollama local API endpoint
    $ollama_api = 'http://localhost:11434/api/generate';

    $body = json_encode([
        'model' => 'llama3',
        'prompt' => $prompt
    ]);

    $response = wp_remote_post($ollama_api, [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => $body
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error('Failed to connect to AI model.');
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($data['response'])) {
        wp_send_json_success($data['response']);
    } else {
        wp_send_json_error('Invalid response from AI model.');
    }
}
add_action('wp_ajax_ai_generate_description', 'ai_product_assistant_generate_description');


/**
 * Adds a top-level admin menu page: "AI Product Assistant"
 */
add_action('admin_menu', function() {
    add_menu_page(
        'AI Product Assistant',
        'AI Product Assistant',
        'manage_options',
        'ai-product-assistant',
        'ai_product_assistant_page',
        'dashicons-robot',
        56
    );
});


/**
 * Renders the admin page UI with a form for:
 * - Product name
 * - Product price
 * - Product image upload
 * Submits the data via AJAX (FormData) so image upload works.
 */
function ai_product_assistant_page() {
    ?>
    <div class="wrap">
        <h1>AI Product Assistant</h1>
        <p>Scrie numele si pretul unui dispozitiv și lasă AI-ul să creeze descrierea automat:</p>
        <form id="ai-product-form">
            <input type="text" id="product_name" name="product_name" style="width: 350px;" placeholder="ex: Samsung Galaxy S24 Ultra" required />
            <input type="number" id="product_price" name="product_price" style="width: 150px; margin-left:10px;" placeholder="Preț (RON)" min="0" step="0.01" />
            <input type="file" id="product_image" name="product_image" accept="image/*" style="margin-left:10px;" />
            <button type="submit" class="button button-primary">Generate & Add Product</button>
        </form>
        <div id="ai-result" style="margin-top:20px;"></div>
    </div>

  <script type="text/javascript">
    jQuery(function($){
        $('#ai-product-form').on('submit', function(e){
            e.preventDefault();

            // Use FormData so we can upload files via AJAX
            const formData = new FormData();
            formData.append('action', 'ai_add_product');
            formData.append('product_name', $('#product_name').val());
            formData.append('product_price', $('#product_price').val());

            const file = $('#product_image')[0].files[0];
            if (file) formData.append('product_image', file);

            $('#ai-result').html('<p>⏳ Generating product with AI...</p>');

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response){
                    if(response.success){
                        $('#ai-result').html('<p>✅ ' + response.data + '</p>');
                    } else {
                        $('#ai-result').html('<p>⚠️ Error: ' + response.data + '</p>');
                    }
                }
            });
        });
    });
</script>

    <?php
}

/**
 * Detects the most likely WooCommerce category based on product name keywords.
 * Returns the term_id (product_cat) or null if no match is found.
 */0423
function detect_category_id($product_name) {
    $name = strtolower($product_name);

    // Smartphones
    if (
        str_contains($name, 'iphone') ||
        str_contains($name, 'samsung') ||
        str_contains($name, 'galaxy') ||
        str_contains($name, 'pixel') ||
        str_contains($name, 'phone')
    ) {
        $term = get_term_by('slug', 'smartphones', 'product_cat');
        return $term ? $term->term_id : null;
    }

    // Cameras
    if (
        str_contains($name, 'canon') ||
        str_contains($name, 'nikon') ||
        str_contains($name, 'sony') ||
        str_contains($name, 'camera') ||
        str_contains($name, 'eos')
    ) {
        $term = get_term_by('slug', 'cameras', 'product_cat');
        return $term ? $term->term_id : null;
    }

    // Headphones
    if (
        str_contains($name, 'headphone') ||
        str_contains($name, 'headset') ||
        str_contains($name, 'airpods') ||
        str_contains($name, 'earbuds') ||
        str_contains($name, 'bose')
    ) {
        $term = get_term_by('slug', 'headphones', 'product_cat');
        return $term ? $term->term_id : null;
    }

    // Laptops
    if (
        str_contains($name, 'laptop') ||
        str_contains($name, 'macbook') ||
        str_contains($name, 'dell') ||
        str_contains($name, 'hp') ||
        str_contains($name, 'lenovo')
    ) {
        $term = get_term_by('slug', 'laptops', 'product_cat');
        return $term ? $term->term_id : null;
    }

    return null;
}

/**
 * AJAX handler: generates a description with AI, creates a WooCommerce product,
 * assigns a category (if detected), uploads an optional image, and returns a success message.
 */
add_action('wp_ajax_ai_add_product', function() {

    if (!isset($_POST['product_name'])) {
        wp_send_json_error('Missing product name.');
    }

    $product_name = sanitize_text_field($_POST['product_name']);
    $price = isset($_POST['product_price']) ? floatval($_POST['product_price']) : 0;

    // Prompt for AI: request an SEO-friendly description with bullet points + CTA
    $prompt = "Write a professional, clear, and persuasive SEO description for the product:
    {$product_name}

    Include:
    - a short general overview
    - 4–6 key benefits as bullet points
    - a final call-to-action

    Return ONLY the description text (no title, no extra explanations).";

    // Call the local Ollama API (LLaMA3)
   $response = wp_remote_post('http://127.0.0.1:11434/api/generate', [
    'timeout' => 60,
    'headers' => ['Content-Type' => 'application/json'],
    'body' => json_encode([
        'model' => 'llama3',
        'prompt' => $prompt,
        'stream' => false
    ])
]);


    if (is_wp_error($response)) {
        wp_send_json_error('Could not connect to the AI model (Ollama).');
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (!isset($body['response'])) {
        wp_send_json_error('Invalid response from the AI model.');
    }

    // Create the WooCommerce product
   $description = trim($body['response']);

    $product = new WC_Product_Simple();
    $product->set_name($product_name);
    $product->set_description($description);
    $product->set_regular_price((string)$price); 
    $product->save();

    $product_id = $product->get_id();

    // Assign category automatically (if detected)
    $category_id = detect_category_id($product_name);

    if ($category_id) {
        wp_set_object_terms($product_id, [(int)$category_id], 'product_cat');
    }

    // Upload and set featured image if provided
    if (!empty($_FILES['product_image']['name'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attachment_id = media_handle_upload('product_image', $product_id);

        if (is_wp_error($attachment_id)) {
            wp_send_json_error('Imaginea nu a putut fi încărcată: ' . $attachment_id->get_error_message());
        }

        set_post_thumbnail($product_id, $attachment_id);
}

    // Return success + link to edit the created product
    $edit_link = get_edit_post_link($product_id, '');
    wp_send_json_success('Produsul "' . $product_name . '" a fost adăugat cu succes! <a href="' . esc_url($edit_link) . '">Editează produsul</a>');


});
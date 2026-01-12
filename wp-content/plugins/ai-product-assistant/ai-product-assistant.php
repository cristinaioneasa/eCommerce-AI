<?php
/*
Plugin Name: AI Product Assistant
Plugin URI: http://localhost/ecommerce-ai
Description: Generează automat descrieri SEO pentru produse WooCommerce folosind un model LLM (ex: LLaMA 3 local via Ollama).
Version: 1.0
Author: Ioneasa Cristina
Author URI: https://github.com/<username>
License: GPL2
*/

// Prevenim accesul direct la fișier
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Adaugă un buton nou în editorul de produse WooCommerce
 * care va genera descrierea cu AI.
 */
function ai_product_assistant_add_button() {
    global $post;

    // Verificăm dacă e un produs
    if ('product' !== $post->post_type) {
        return;
    }

    echo '<div style="margin:15px 0;">';
    echo '<button type="button" class="button button-primary" id="generate-ai-description">🧠 Generate AI Description</button>';
    echo '</div>';

    // Scriptul care trimite cererea către server
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($){
        $('#generate-ai-description').on('click', function(e){
            e.preventDefault();
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
 * Endpoint AJAX care apelează AI-ul (via Ollama API local)
 */
function ai_product_assistant_generate_description() {
    if (!isset($_POST['product_title'])) {
        wp_send_json_error('Missing product title.');
    }

    $product_title = sanitize_text_field($_POST['product_title']);

    // Prompt pentru AI
    $prompt = "Creează o descriere SEO atractivă pentru produsul: {$product_title}. 
               Include caracteristici cheie, beneficii și un call-to-action convingător.";

    // URL către Ollama (model local)
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
 * Procesare cerere AJAX: generare + adăugare produs
 */
add_action('wp_ajax_ai_add_product', function() {

    if (!isset($_POST['product_name'])) {
        wp_send_json_error('Missing product name.');
    }

    $product_name = sanitize_text_field($_POST['product_name']);
    $price = isset($_POST['product_price']) ? floatval($_POST['product_price']) : 0;

    // 1. Generăm descrierea și detaliile produsului cu AI
    $prompt = "Scrie o descriere SEO profesională, clară și convingătoare pentru produsul:
    {$product_name}

    Include:
    - descriere generală
    - 4–6 beneficii cheie sub formă de bullet points
    - un call-to-action final
    Răspunde DOAR cu textul descrierii, fără titlu, fără explicații.";


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
        wp_send_json_error('Nu s-a putut conecta la AI (Ollama).');
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (!isset($body['response'])) {
        wp_send_json_error('Răspuns invalid de la AI.');
    }

    // 2. Adăugăm produsul în WooCommerce
   $description = trim($body['response']);

    $product = new WC_Product_Simple();
    $product->set_name($product_name);
    $product->set_description($description);
    $product->set_regular_price((string)$price); 
    $product->save();

    $product_id = $product->get_id();

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

    $edit_link = get_edit_post_link($product_id, '');
    wp_send_json_success('Produsul "' . $product_name . '" a fost adăugat cu succes! <a href="' . esc_url($edit_link) . '">Editează produsul</a>');


});
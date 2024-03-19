<?php

/*
Plugin Name: Really Simple Custom Popups
Description: Plugin to create custom popups with HTML content.
Version: 1.0
Author: Danilo P. da Silva
Website: https://www.dps.tec.br/
*/

// Register Custom Post Type
function custom_popup_post_type() {
    $labels = array(
        'name' => 'Custom Popups',
        'singular_name' => 'Custom Popup',
        'menu_name' => 'Custom Popups',
        'name_admin_bar' => 'Custom Popup',
        'archives' => 'Popup Archives',
        'attributes' => 'Popup Attributes',
        'parent_item_colon' => 'Parent Popup:',
        'all_items' => 'All Popups',
        'add_new_item' => 'Add New Popup',
        'add_new' => 'Add New',
        'new_item' => 'New Popup',
        'edit_item' => 'Edit Popup',
        'update_item' => 'Update Popup',
        'view_item' => 'View Popup',
        'view_items' => 'View Popups',
        'search_items' => 'Search Popup',
        'not_found' => 'Popup Not found',
        'not_found_in_trash' => 'Popup Not found in Trash',
        'featured_image' => 'Featured Image',
        'set_featured_image' => 'Set featured image',
        'remove_featured_image' => 'Remove featured image',
        'use_featured_image' => 'Use as featured image',
        'insert_into_item' => 'Insert into Popup',
        'uploaded_to_this_item' => 'Uploaded to this Popup',
        'items_list' => 'Popups list',
        'items_list_navigation' => 'Popups list navigation',
        'filter_items_list' => 'Filter Popups list',
    );
    $args = array(
        'label' => 'Custom Popup',
        'description' => 'Custom popups with HTML content.',
        'labels' => $labels,
        'supports' => array('title'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-megaphone',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
        'rewrite' => array('slug' => 'custom-popup'),
    );
    register_post_type('custom_popup', $args);
}

add_action('init', 'custom_popup_post_type', 0);

// Add Title Field Metabox
function add_title_field_metabox() {
    add_meta_box(
        'title-meta-box',
        'Popup Title',
        'title_meta_box_callback',
        'custom_popup',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_title_field_metabox');

// Title Field Metabox Callback
function title_meta_box_callback($post) {
    // Add nonce for security
    wp_nonce_field('title_meta_box', 'title_meta_box_nonce');
    
    // Retrieve the title value from post meta
    $popup_title = get_post_meta($post->ID, 'popup_title', true);
    ?>
    <label for="popup_title">Title:</label>
    <input type="text" id="popup_title" name="popup_title" value="<?php echo esc_attr($popup_title); ?>" style="width: 100%;"/>
    <?php
}

// Save Title Field
function save_title_field($post_id) {
    // Check if our nonce is set
    if (!isset($_POST['title_meta_box_nonce'])) {
        return;
    }

    // Verify that the nonce is valid
    if (!wp_verify_nonce($_POST['title_meta_box_nonce'], 'title_meta_box')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save the title field
    if (isset($_POST['popup_title'])) {
        update_post_meta($post_id, 'popup_title', sanitize_text_field($_POST['popup_title']));
    }
}
add_action('save_post', 'save_title_field');

// Add Custom Field for HTML Content
function add_html_content_field() {
    add_meta_box(
        'html-content-meta-box',
        'HTML Content',
        'html_content_meta_box_callback',
        'custom_popup',
        'normal',
        'default'
    );
}

add_action('add_meta_boxes', 'add_html_content_field');

function html_content_meta_box_callback($post) {
    // Add nonce for security
    wp_nonce_field('html_content_meta_box', 'html_content_meta_box_nonce');
    
    // Retrieve the HTML content value from post meta
    $html_content = get_post_meta($post->ID, 'popup_html_content', true);
    ?>
    <label for="popup_html_content">Insert the HTML code below:</label>
    <textarea id="popup_html_content" name="popup_html_content" style="width: 100%; height: 200px;"><?php echo esc_textarea($html_content); ?></textarea>
    <?php
}

// Save HTML Content Field
function save_html_content_field($post_id) {
    // Check if our nonce is set
    if (!isset($_POST['html_content_meta_box_nonce'])) {
        return;
    }

    // Verify that the nonce is valid
    if (!wp_verify_nonce($_POST['html_content_meta_box_nonce'], 'html_content_meta_box')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save the HTML content field without modifying it
    if (isset($_POST['popup_html_content'])) {
        update_post_meta($post_id, 'popup_html_content', $_POST['popup_html_content']);
    }
}

add_action('save_post', 'save_html_content_field');

// Add Shortcode Field to Custom Post Type
function add_shortcode_field() {
    add_meta_box(
        'shortcode-meta-box',
        'Popup Shortcode',
        'shortcode_meta_box_callback',
        'custom_popup',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_shortcode_field');

function shortcode_meta_box_callback($post) {
    // Add nonce for security
    wp_nonce_field('shortcode_meta_box', 'shortcode_meta_box_nonce');
    
    // Retrieve the post ID
    $post_id = $post->ID;
    
    // Generate the shortcode
    $shortcode = '[custom_popup post_id="' . $post_id . '"]';
    ?>
    <label for="popup_shortcode">Shortcode:</label>
    <input type="text" id="popup_shortcode" name="popup_shortcode" value="<?php echo esc_attr($shortcode); ?>" readonly style="width: 100%;"/>
    <?php
}

// Save Shortcode Field
function save_shortcode_field($post_id) {
    // Check if our nonce is set
    if (!isset($_POST['shortcode_meta_box_nonce'])) {
        return;
    }

    // Verify that the nonce is valid
    if (!wp_verify_nonce($_POST['shortcode_meta_box_nonce'], 'shortcode_meta_box')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save the shortcode field
    if (isset($_POST['popup_shortcode'])) {
        update_post_meta($post_id, 'popup_shortcode', sanitize_text_field($_POST['popup_shortcode']));
    }
}

add_action('save_post', 'save_shortcode_field');

// Função para adicionar CSS personalizado ao cabeçalho da página
function custom_popup_custom_css() {
    ?>
    <style type="text/css">
        /* Estilos para o botão do modal */
        .custom-popup-button {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }

        .custom-popup-button:hover {
            background-color: #0056b3;
        }

        /* Estilos para o modal */
        .modal {
            display: none; /* Ocultar modal por padrão */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <?php
}

add_action('wp_head', 'custom_popup_custom_css');

// Register Shortcode Rendering
function render_custom_popup_shortcode($atts) {
    // Extrair os atributos e definir valores padrão
    $atts = shortcode_atts(
        array(
            'post_id' => 0,
            'title' => 'Título do popup',
            'background' => '#FDF9D4',
            'button_text' => 'Saiba mais',
            'button_color' => '#0d2516',
            'border_radius' => '0px'
        ),
        $atts,
        'custom_popup'
    );

    // Verify if post ID was provided
    if ($atts['post_id']) {
        // Get the title from post meta
        $popup_title = get_post_meta($atts['post_id'], 'popup_title', true);
        
        // Get the HTML content from post meta
        $html_content = get_post_meta($atts['post_id'], 'popup_html_content', true);

        // Generate a unique ID for the modal
        $modal_id = 'custom_popup_modal_' . $atts['post_id'];

        // Generate the button HTML with new properties
        $button_html = '<button class="custom-popup-button" data-toggle="modal" data-target="#' . esc_attr($modal_id) . '" style="border-radius: ' . esc_attr($atts['border_radius']) . '; background: ' . esc_attr($atts['button_color']) . ';">' . esc_html($atts['button_text']) . '</button>';

        // Generate the modal HTML
        $modal_html = '<div class="modal" id="' . esc_attr($modal_id) . '">
                            <div class="modal-content" style="background: ' . esc_attr($atts['background']) . ';">
                                <span class="close">&times;</span>
                                <div class="modal-body"><h2 style="margin-left:20px;">' . esc_html($atts['title']) . '</h2>' . $html_content . '</div>
                            </div>
                        </div>';

        // Return the button HTML followed by the modal HTML
        return $button_html . $modal_html;
    } else {
        return ''; // Return empty if post ID was not provided
    }
}

add_shortcode('custom_popup', 'render_custom_popup_shortcode');

// Função para adicionar JavaScript personalizado ao final da página
function custom_popup_custom_js() {
    ?>
    <script>
        // Get the modal
        var modal = document.querySelector('.modal');

        // Get the button that opens the modal
        var btn = document.querySelector('.custom-popup-button');

        // Get the <span> element that closes the modal
        var span = document.querySelector('.close');

        // When the user clicks the button, open the modal 
        btn.addEventListener('click', function() {
          modal.style.display = "block";
        });

        // When the user clicks on <span> (x), close the modal
        span.addEventListener('click', function() {
          modal.style.display = "none";
        });

        // When the user clicks anywhere outside of the modal, close it
        window.addEventListener('click', function(event) {
          if (event.target == modal) {
            modal.style.display = "none";
          }
        });
    </script>
    <?php
}
add_action('wp_footer', 'custom_popup_custom_js');

<?php
function ocg_save_prompt($name, $content) {
    $saved_prompts = get_option('ocg_saved_prompts', array());
    $id = uniqid();
    $saved_prompts[$id] = array(
        'name' => sanitize_text_field($name),
        'content' => sanitize_textarea_field($content)
    );
    update_option('ocg_saved_prompts', $saved_prompts);
    return $id;
}

function ocg_delete_prompt($id) {
    $saved_prompts = get_option('ocg_saved_prompts', array());
    if (isset($saved_prompts[$id])) {
        unset($saved_prompts[$id]);
        update_option('ocg_saved_prompts', $saved_prompts);
        return true;
    }
    return false;
}

function ocg_get_saved_prompts() {
    return get_option('ocg_saved_prompts', array());
}

// AJAX handlers for prompt management
add_action('wp_ajax_ocg_save_prompt', function() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $name = $_POST['name'] ?? '';
    $content = $_POST['content'] ?? '';
    
    if (empty($name) || empty($content)) {
        wp_send_json_error('Name and content are required');
    }
    
    $id = ocg_save_prompt($name, $content);
    wp_send_json_success(['id' => $id]);
});

add_action('wp_ajax_ocg_delete_prompt', function() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $id = $_POST['id'] ?? '';
    if (empty($id)) {
        wp_send_json_error('Prompt ID is required');
    }
    
    if (ocg_delete_prompt($id)) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Prompt not found');
    }
});
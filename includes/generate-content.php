<?php
function ocg_generate_content_ajax() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }

    $prompt_id = $_POST['prompt_id'] ?? '';
    $custom_prompt = $_POST['custom_prompt'] ?? '';
    
    if (empty($prompt_id) && empty($custom_prompt)) {
        wp_send_json_error('Either select a prompt or enter custom text');
    }

    $openai = new OpenAI_API();
    
    // Use custom prompt if provided, otherwise use saved prompt
    $prompt_content = $custom_prompt;
    if (empty($custom_prompt)) {
        $prompts = ocg_get_saved_prompts();
        if (!isset($prompts[$prompt_id])) {
            wp_send_json_error('Invalid prompt selected');
        }
        $prompt_content = $prompts[$prompt_id]['content'];
    }

    $generated_content = $openai->generate_content($prompt_content);
    
    if (is_wp_error($generated_content)) {
        wp_send_json_error($generated_content->get_error_message());
    }

    wp_send_json_success([
        'content' => $generated_content,
        'prompt_used' => $prompt_content
    ]);
}
add_action('wp_ajax_ocg_generate_content', 'ocg_generate_content_ajax');
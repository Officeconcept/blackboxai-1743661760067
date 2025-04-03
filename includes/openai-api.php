<?php
class OpenAI_API {
    private $api_key;
    private $api_url = 'https://api.openai.com/v1/chat/completions';
    
    public function __construct($api_key = '') {
        $this->api_key = $api_key ?: get_option('ocg_openai_api_key');
    }
    
    public function generate_content($prompt, $model = 'gpt-3.5-turbo', $max_tokens = 1000) {
        if (empty($this->api_key)) {
            return new WP_Error('no_api_key', 'OpenAI API key is not set.');
        }
        
        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key
        );
        
        $body = array(
            'model' => $model,
            'messages' => array(
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            ),
            'max_tokens' => $max_tokens,
            'temperature' => 0.7
        );
        
        $args = array(
            'headers' => $headers,
            'body' => json_encode($body),
            'timeout' => 30
        );
        
        $response = wp_remote_post($this->api_url, $args);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = json_decode(wp_remote_retrieve_body($response), true);
        
        if ($response_code !== 200) {
            $error_message = $response_body['error']['message'] ?? 'Unknown error occurred';
            return new WP_Error('api_error', $error_message);
        }
        
        return $response_body['choices'][0]['message']['content'] ?? '';
    }
    
    public function validate_api_key() {
        if (empty($this->api_key)) {
            return false;
        }
        
        // Simple validation by checking the key format
        return preg_match('/^sk-[a-zA-Z0-9]+$/', $this->api_key);
    }
}
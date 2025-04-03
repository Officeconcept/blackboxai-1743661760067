<?php
// Admin interface functions for OpenAI Content Generator

function ocg_admin_page_content() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Get saved API key
    $api_key = get_option('ocg_openai_api_key');
    $saved_prompts = get_option('ocg_saved_prompts', array());
    
    ?>
    <div class="wrap">
        <h1 class="text-2xl font-bold mb-6">OpenAI Content Generator</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- API Key Settings -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">API Settings</h2>
                <form method="post" action="options.php">
                    <?php settings_fields('ocg_settings_group'); ?>
                    <?php do_settings_sections('ocg_settings_group'); ?>
                    
                    <div class="mb-4">
                        <label for="ocg_openai_api_key" class="block text-sm font-medium text-gray-700 mb-1">
                            OpenAI API Key
                        </label>
                        <input type="password" name="ocg_openai_api_key" id="ocg_openai_api_key"
                            value="<?php echo esc_attr($api_key); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-sm text-gray-500">
                            Your API key is stored securely and never shared.
                        </p>
                    </div>
                    
                    <?php submit_button('Save API Key', 'primary'); ?>
                </form>
            </div>
            
            <!-- Prompt Management -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Prompt Management</h2>
                
                <!-- Add New Prompt Form -->
                <div class="mb-6 p-4 bg-gray-50 rounded">
                    <h3 class="text-lg font-medium mb-2">Add New Prompt</h3>
                    <form id="ocg-add-prompt-form">
                        <div class="mb-3">
                            <label for="prompt-name" class="block text-sm font-medium text-gray-700 mb-1">
                                Prompt Name
                            </label>
                            <input type="text" id="prompt-name" name="prompt_name"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="prompt-content" class="block text-sm font-medium text-gray-700 mb-1">
                                Prompt Content
                            </label>
                            <textarea id="prompt-content" name="prompt_content" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                required></textarea>
                        </div>
                        
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Prompt
                        </button>
                    </form>
                </div>
                
                <!-- Saved Prompts List -->
                <div>
                    <h3 class="text-lg font-medium mb-2">Saved Prompts</h3>
                    <?php if (empty($saved_prompts)): ?>
                        <p class="text-gray-500">No prompts saved yet.</p>
                    <?php else: ?>
                        <ul class="divide-y divide-gray-200">
                            <?php foreach ($saved_prompts as $id => $prompt): ?>
                                <li class="py-3 flex justify-between items-center">
                                    <div>
                                        <h4 class="font-medium"><?php echo esc_html($prompt['name']); ?></h4>
                                        <p class="text-sm text-gray-500 truncate"><?php echo esc_html($prompt['content']); ?></p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="ocg-edit-prompt text-indigo-600 hover:text-indigo-900" data-id="<?php echo esc_attr($id); ?>">
                                            Edit
                                        </button>
                                        <button class="ocg-delete-prompt text-red-600 hover:text-red-900" data-id="<?php echo esc_attr($id); ?>">
                                            Delete
                                        </button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// Register settings
add_action('admin_init', 'ocg_register_settings');
function ocg_register_settings() {
    register_setting('ocg_settings_group', 'ocg_openai_api_key', array(
        'type' => 'string',
        'sanitize_callback' => 'ocg_sanitize_api_key',
        'default' => ''
    ));
}

function ocg_sanitize_api_key($value) {
    $value = trim($value);
    if (!empty($value) && !preg_match('/^sk-[a-zA-Z0-9]+$/', $value)) {
        add_settings_error('ocg_openai_api_key', 'invalid-api-key', 'Please enter a valid OpenAI API key.');
        return get_option('ocg_openai_api_key');
    }
    return $value;
}
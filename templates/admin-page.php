<div class="wrap">
    <h1 class="text-2xl font-bold mb-6">OpenAI Content Generator</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Settings Section -->
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
                        value="<?php echo esc_attr(get_option('ocg_openai_api_key')); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <?php submit_button('Save Settings', 'primary'); ?>
            </form>
        </div>

        <!-- Content Generation Section -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Generate Content</h2>
            
            <form id="ocg-generate-form" class="space-y-4">
                <div>
                    <label for="ocg-prompt-select" class="block text-sm font-medium text-gray-700 mb-1">
                        Select Saved Prompt
                    </label>
                    <select id="ocg-prompt-select" name="prompt_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select a prompt --</option>
                        <?php foreach (ocg_get_saved_prompts() as $id => $prompt): ?>
                            <option value="<?php echo esc_attr($id); ?>">
                                <?php echo esc_html($prompt['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="ocg-custom-prompt" class="block text-sm font-medium text-gray-700 mb-1">
                        Or Enter Custom Prompt
                    </label>
                    <textarea id="ocg-custom-prompt" name="custom_prompt" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Generate Content
                </button>
            </form>
            
            <div id="ocg-generated-content" class="mt-6 p-4 bg-gray-50 rounded hidden">
                <h3 class="font-medium mb-2">Generated Content</h3>
                <div id="ocg-content-output" class="whitespace-pre-wrap"></div>
                <button id="ocg-copy-content" class="mt-2 px-3 py-1 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
                    Copy to Clipboard
                </button>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Handle form submission
    $('#ocg-generate-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'ocg_generate_content',
            prompt_id: $('#ocg-prompt-select').val(),
            custom_prompt: $('#ocg-custom-prompt').val()
        };
        
        $.post(ajaxurl, formData, function(response) {
            if (response.success) {
                $('#ocg-content-output').text(response.data.content);
                $('#ocg-generated-content').removeClass('hidden');
            } else {
                alert('Error: ' + response.data);
            }
        }).fail(function() {
            alert('An error occurred while generating content');
        });
    });
    
    // Handle copy to clipboard
    $('#ocg-copy-content').on('click', function() {
        var content = $('#ocg-content-output').text();
        navigator.clipboard.writeText(content).then(function() {
            alert('Content copied to clipboard!');
        });
    });
});
</script>
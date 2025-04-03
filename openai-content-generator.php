<?php
/**
 * Plugin Name: OpenAI Content Generator
 * Description: Generate blog posts and pages using OpenAI with custom prompt management.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPLv2 or later
 * Text Domain: openai-content-generator
 */

// Security check
defined('ABSPATH') or die('No script kiddies please!');

// Define plugin constants
define('OCG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('OCG_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once(OCG_PLUGIN_DIR . 'includes/admin.php');
require_once(OCG_PLUGIN_DIR . 'includes/openai-api.php');
require_once(OCG_PLUGIN_DIR . 'includes/prompt-storage.php');
require_once(OCG_PLUGIN_DIR . 'includes/generate-content.php');

// Register activation/deactivation hooks
register_activation_hook(__FILE__, 'ocg_activate_plugin');
register_deactivation_hook(__FILE__, 'ocg_deactivate_plugin');

function ocg_activate_plugin() {
    // Initialize default options if needed
    if (!get_option('ocg_openai_api_key')) {
        update_option('ocg_openai_api_key', '');
    }
    if (!get_option('ocg_saved_prompts')) {
        update_option('ocg_saved_prompts', array());
    }
}

function ocg_deactivate_plugin() {
    // Clean up options if needed
    // delete_option('ocg_openai_api_key');
    // delete_option('ocg_saved_prompts');
}

// Enqueue admin scripts and styles
add_action('admin_enqueue_scripts', 'ocg_enqueue_admin_assets');
function ocg_enqueue_admin_assets($hook) {
    if (strpos($hook, 'openai-content-generator') !== false) {
        // Enqueue Tailwind CSS
        wp_enqueue_style(
            'ocg-tailwind',
            'https://cdn.tailwindcss.com',
            array(),
            '3.3.0'
        );
        
        // Enqueue custom admin CSS
        wp_enqueue_style(
            'ocg-admin-css',
            OCG_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            filemtime(OCG_PLUGIN_DIR . 'assets/css/admin.css')
        );
        
        // Enqueue custom admin JS
        wp_enqueue_script(
            'ocg-admin-js',
            OCG_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            filemtime(OCG_PLUGIN_DIR . 'assets/js/admin.js'),
            true
        );
    }
}

// Initialize admin menu
add_action('admin_menu', 'ocg_setup_admin_menu');
function ocg_setup_admin_menu() {
    add_management_page(
        'OpenAI Content Generator',
        'Content Generator',
        'manage_options',
        'openai-content-generator',
        'ocg_render_admin_page'
    );
}

function ocg_render_admin_page() {
    include(OCG_PLUGIN_DIR . 'templates/admin-page.php');
}
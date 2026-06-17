<?php
/**
 * Plugin Name: Yantram CRM - Lead Pipeline
 * Description: A comprehensive CRM system for managing leads, pipelines, and sales workflows
 * Version: 1.0.0
 * Author: Yantram CRM
 * License: GPL v2 or later
 * Text Domain: yantram-crm
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('YANTRAM_CRM_VERSION', '1.0.0');
define('YANTRAM_CRM_PATH', plugin_dir_path(__FILE__));
define('YANTRAM_CRM_URL', plugin_dir_url(__FILE__));
define('YANTRAM_CRM_BASENAME', plugin_basename(__FILE__));

// Load the main plugin class
require_once YANTRAM_CRM_PATH . 'includes/class-yantram-crm.php';

// Activate/Deactivate hooks
register_activation_hook(__FILE__, ['Yantram_CRM', 'activate']);
register_deactivation_hook(__FILE__, ['Yantram_CRM', 'deactivate']);

// Initialize the plugin
add_action('plugins_loaded', function() {
    Yantram_CRM::get_instance();
});

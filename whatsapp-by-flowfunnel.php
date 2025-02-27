<?php
/**
 * Plugin Name: WhatsApp by FlowFunnel.io
 * Plugin URI:  http://riverworksit.com/WhatsApp_Plugin_for_WordPress
 * Description: Customizable floating whatsapp chat icon for your entire website with options
 * Version:     1.0
 * Author:      RiverWork IT LLC
 * Author URI:  http://riverworksit.com
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: waflowfunnel
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

// Define plugin version constant
define('WAFLOWFUNNEL_VERSION', '1.0');

// Load Assets
function waflowfunnel_chat_enqueue_assets() {
    // Load Font Awesome from CDN to ensure it works
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
    
    wp_enqueue_style(
        'waflowfunnel-style', 
        plugin_dir_url(__FILE__) . 'assets/css/frontend.css',
        array('font-awesome'), // Make it dependent on Font Awesome
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/frontend.css')
    );
    wp_enqueue_script(
        'waflowfunnel-script', 
        plugin_dir_url(__FILE__) . 'assets/js/script.js', 
        array('jquery'),
        filemtime(plugin_dir_path(__FILE__) . 'assets/js/script.js'),
        true
    );

    wp_localize_script('waflowfunnel-script', 'waflowfunnelData', array(
        'phoneNumber' => get_option('waflowfunnel_chat_number', ''),
        'inquiryOptions' => get_option('waflowfunnel_chat_options', array()),
        'trackingEnabled' => get_option('waflowfunnel_chat_tracking', 'no')
    ));
}
add_action('wp_enqueue_scripts', 'waflowfunnel_chat_enqueue_assets');

// Load Admin Assets
function waflowfunnel_chat_enqueue_admin_assets() {
    wp_enqueue_style(
        'fontawesome', 
        plugin_dir_url(__FILE__) . 'assets/css/all.min.css',
        array(),
        '6.4.0'
    );
    wp_enqueue_style(
        'whatsapp-tailwind-style', 
        plugin_dir_url(__FILE__) . 'assets/css/output.css',
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/output.css')
    );
}
add_action('admin_enqueue_scripts', 'waflowfunnel_chat_enqueue_admin_assets');

// Include Admin Settings & Analytics
include_once plugin_dir_path(__FILE__) . 'admin-settings.php';
include_once plugin_dir_path(__FILE__) . 'includes/analytics.php';

// Display WhatsApp Button
function waflowfunnel_chat_button() {
    $number = get_option('whatsapp_chat_number'); // Changed from waflowfunnel_chat_number
    $position = get_option('whatsapp_chat_position', 'bottom-right');
    $icon_style = get_option('whatsapp_chat_icon_style', 'style1');
    $inquiry_options = get_option('whatsapp_chat_options', array());
    
    if (!empty($number)) {
        ?>
        <div class="whatsapp-chat-container <?php echo esc_attr($position); ?>">
            <div class="whatsapp-chat-button <?php echo esc_attr($icon_style); ?>" 
                 onclick="toggleWhatsAppPopup()">
                <i class="fab fa-whatsapp"></i>
            </div>
            <?php if (!empty($inquiry_options)): ?>
            <div class="whatsapp-popup" id="whatsapp-popup">
                <h3>How can we help?</h3>
                <?php foreach ($inquiry_options as $option): ?>
                    <a href="https://wa.me/<?php echo esc_attr($number); ?>?text=<?php echo urlencode($option); ?>" 
                       target="_blank" 
                       class="whatsapp-option" 
                       onclick="trackWhatsAppClick('<?php echo esc_js($option); ?>')">
                        <?php echo esc_html($option); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
add_action('wp_footer', 'waflowfunnel_chat_button');

// Add settings link to plugins page
function whatsapp_chat_add_settings_link($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=wa-chat-settings') . '">' . __('Settings') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'whatsapp_chat_add_settings_link');

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
    $font_awesome_path = plugin_dir_path(__FILE__) . 'assets/css/all.min.css';
    $font_awesome_url = plugin_dir_url(__FILE__) . 'assets/css/all.min.css';
    
    // Check if Font Awesome file exists before enqueuing
    if (file_exists($font_awesome_path)) {
        // Register Font Awesome first
        wp_register_style(
            'waflowfunnel-font-awesome', 
            $font_awesome_url,
            array(),
            '6.4.0',
            'all'
        );
        
        // Then enqueue it with higher priority (lower number)
        wp_enqueue_style('waflowfunnel-font-awesome');
    } else {
        // Log error if file doesn't exist
        error_log('WhatsApp by FlowFunnel: Font Awesome file not found at ' . $font_awesome_path);
        
        // Fallback to CDN version
        wp_enqueue_style(
            'waflowfunnel-font-awesome-cdn',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            array(),
            '6.4.0',
            'all'
        );
    }
    
    wp_enqueue_style(
        'whatsapp-tailwind-style', 
        plugin_dir_url(__FILE__) . 'assets/css/style.css',
        array('waflowfunnel-font-awesome'), // Updated dependency name
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/style.css')
    );

    wp_enqueue_style(
        'waflowfunnel-style', 
        plugin_dir_url(__FILE__) . 'assets/css/frontend.css',
        array('waflowfunnel-font-awesome', 'whatsapp-tailwind-style'), // Updated dependency name
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
function waflowfunnel_chat_enqueue_admin_assets($hook) {
    if ($hook !== 'settings_page_wa-chat-settings') {
        return;
    }
    
    $font_awesome_path = plugin_dir_path(__FILE__) . 'assets/css/all.min.css';
    $font_awesome_url = plugin_dir_url(__FILE__) . 'assets/css/all.min.css';
    
    // Check if Font Awesome file exists before enqueuing
    if (file_exists($font_awesome_path)) {
        wp_enqueue_style(
            'waflowfunnel-font-awesome', 
            $font_awesome_url,
            array(),
            '6.4.0',
            'all'
        );
    } else {
        // Fallback to CDN version
        wp_enqueue_style(
            'waflowfunnel-font-awesome-cdn',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            array(),
            '6.4.0',
            'all'
        );
    }
    
    wp_enqueue_style(
        'whatsapp-tailwind-style', 
        plugin_dir_url(__FILE__) . 'assets/css/style.css',
        array('waflowfunnel-font-awesome'), // Updated dependency
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/style.css')
    );
}
add_action('admin_enqueue_scripts', 'waflowfunnel_chat_enqueue_admin_assets');

// Include Admin Settings & Analytics
include_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';
include_once plugin_dir_path(__FILE__) . 'includes/analytics.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax-handlers.php';

// Display WhatsApp Button
function waflowfunnel_chat_button() {
    $number = get_option('whatsapp_chat_number');
    $is_verified = get_option('whatsapp_number_verified', false);
    
    if (!empty($number) && $is_verified) {
        $position = get_option('whatsapp_chat_position', 'bottom-right');
        $icon_style = get_option('whatsapp_chat_icon_style', 'style1');
        $inquiry_options = get_option('whatsapp_chat_options', array());
        ?>
        <div class="whatsapp-chat-container <?php echo esc_attr($position); ?>">
            <div class="whatsapp-chat-button <?php echo esc_attr($icon_style); ?>" onclick="toggleWhatsAppPopup()">
                <i class="fab fa-whatsapp"></i>
            </div>
            <?php if (!empty($inquiry_options)): ?>
            <div class="whatsapp-popup hidden" id="whatsapp-popup">
                <div class="popup-content">
                    <div class="popup-header">
                        <div class="header-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <h3>How can we help?</h3>
                    </div>
                    <div class="popup-options">
                        <?php foreach ($inquiry_options as $option): ?>
                            <a href="https://wa.me/<?php echo esc_attr($number); ?>?text=<?php echo urlencode($option); ?>" 
                               target="_blank" 
                               class="chat-option" 
                               onclick="trackWhatsAppClick('<?php echo esc_js($option); ?>')">
                                <span class="option-text"><?php echo esc_html($option); ?></span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="whatsapp-brand-name">
                        <a  href="https://flowfunnel.io" target="_blank">
                            by FlowFunnel.io
                        </a>
                    </div>
                </div>
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

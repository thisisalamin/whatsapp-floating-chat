<?php
/**
 * Plugin Name: Easy Chat Widget
 * Plugin URI:  https://crafely.com/easy-chat-widget/
 * Description: A fully customizable floating chat icon with inquiry options, animations, and analytics.
 * Version:     1.0
 * Author:      Mohamed Alamin
 * Author URI:  https://crafely.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: easy-chat
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

// Define plugin version constant
define('EASY_CHAT_VERSION', '1.0');

// Load Assets
function whatsapp_chat_enqueue_assets() {
    wp_enqueue_style('font-awesome', plugin_dir_url(__FILE__) . 'assets/css/all.min.css', array(), '6.4.0');
    wp_enqueue_style(
        'easy-chat-style', 
        plugin_dir_url(__FILE__) . 'assets/css/frontend.css',
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/frontend.css')
    );
    wp_enqueue_script(
        'easy-chat-script', 
        plugin_dir_url(__FILE__) . 'assets/js/script.js', 
        array('jquery'),
        filemtime(plugin_dir_path(__FILE__) . 'assets/js/script.js'),
        true
    );

    wp_localize_script('easy-chat-script', 'easyChatData', array(
        'phoneNumber' => get_option('whatsapp_chat_number', ''),
        'inquiryOptions' => get_option('whatsapp_chat_options', array()),
        'trackingEnabled' => get_option('whatsapp_chat_tracking', 'no')
    ));
}
add_action('wp_enqueue_scripts', 'whatsapp_chat_enqueue_assets');

// Load Admin Assets
function whatsapp_chat_enqueue_admin_assets() {
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
add_action('admin_enqueue_scripts', 'whatsapp_chat_enqueue_admin_assets');

// Include Admin Settings & Analytics
include_once plugin_dir_path(__FILE__) . 'admin-settings.php';
include_once plugin_dir_path(__FILE__) . 'includes/analytics.php';

// Display WhatsApp Button
function whatsapp_chat_button() {
    $number = get_option('whatsapp_chat_number');
    $position = get_option('whatsapp_chat_position', 'bottom-right');
    $icon_style = get_option('whatsapp_chat_icon_style', 'style1');
    $inquiry_options = get_option('whatsapp_chat_options', array());
    
    if (!empty($number)) {
        ?>
        <div class="whatsapp-chat-container <?php echo esc_attr($position); ?>">
            <div class="whatsapp-chat-button <?php echo esc_attr($icon_style); ?>" 
                 onclick="toggleWhatsAppPopup()">
                <?php
                switch ($icon_style) {
                    case 'style3':
                        echo '<i class="fab fa-whatsapp"></i>';
                        break;
                    case 'style4':
                        echo '<i class="fab fa-whatsapp"></i>';
                        break;
                    default: // style1
                        echo '<i class="fab fa-whatsapp"></i>';
                }
                ?>
            </div>
            <?php if (!empty($inquiry_options)): ?>
            <div class="whatsapp-popup">
                <h3>How can we help?</h3>
                <?php foreach ($inquiry_options as $option): ?>
                    <a href="https://wa.me/<?php echo esc_attr($number); ?>?text=<?php echo urlencode("Hello, I'm interested in $option."); ?>" 
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
add_action('wp_footer', 'whatsapp_chat_button');

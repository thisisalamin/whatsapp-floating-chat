<?php

// Admin Menu
function whatsapp_chat_menu() {
    add_options_page('WhatsApp by FlowFunel.io', 'WhatsApp By FlowFunnel', 'manage_options', 'wa-chat-settings', 'whatsapp_chat_settings_page');
}
add_action('admin_menu', 'whatsapp_chat_menu');

// Enqueue admin scripts
add_action('admin_enqueue_scripts', 'whatsapp_chat_admin_scripts');

function whatsapp_chat_admin_scripts($hook) {
    if ('settings_page_wa-chat-settings' !== $hook) {
        return;
    }

    wp_enqueue_script(
        'whatsapp-admin-settings',
        plugins_url('/assets/js/admin-settings.js', dirname(__FILE__)),
        array('jquery'),
        '1.0.0',
        true
    );

    // Pass nonce to JavaScript
    wp_localize_script(
        'whatsapp-admin-settings',
        'whatsappAdminSettings',
        array(
            'nonce' => wp_create_nonce('verify_whatsapp_number')
        )
    );
}

// Settings Page
function whatsapp_chat_settings_page() {
    if (!current_user_can('manage_options')) return;

    // Process form submission
    if (isset($_POST['whatsapp_chat_save'])) {
        // Verify nonce with proper sanitization
        if (!isset($_POST['whatsapp_chat_nonce']) || 
            !wp_verify_nonce(
                sanitize_text_field(
                    wp_unslash($_POST['whatsapp_chat_nonce'])
                ), 
                'whatsapp_chat_settings'
            )
        ) {
            wp_die('Invalid nonce specified', 'Error', array('response' => 403));
        }

        // Safely process and update options
        $settings_updated = false;

        // Process WhatsApp number
        if (isset($_POST['whatsapp_chat_number'])) {
            update_option('whatsapp_chat_number', sanitize_text_field(wp_unslash($_POST['whatsapp_chat_number'])));
            $settings_updated = true;
        }

        // Process inquiry options with proper sanitization
        if (isset($_POST['whatsapp_chat_options'])) {
            $raw_options = array_map('sanitize_text_field', wp_unslash($_POST['whatsapp_chat_options']));
            if (is_array($raw_options)) {
                $sanitized_options = array_map('sanitize_text_field', $raw_options);
                $sanitized_options = array_filter($sanitized_options); // Remove empty values
                update_option('whatsapp_chat_options', $sanitized_options);
                $settings_updated = true;
            }
        }

        // Process position
        if (isset($_POST['whatsapp_chat_position'])) {
            update_option('whatsapp_chat_position', sanitize_text_field(wp_unslash($_POST['whatsapp_chat_position'])));
            $settings_updated = true;
        }

        // Process icon style
        if (isset($_POST['whatsapp_chat_icon_style'])) {
            update_option('whatsapp_chat_icon_style', sanitize_text_field(wp_unslash($_POST['whatsapp_chat_icon_style'])));
            $settings_updated = true;
        }

        // Process tracking
        if (isset($_POST['whatsapp_chat_tracking'])) {
            update_option('whatsapp_chat_tracking', sanitize_text_field(wp_unslash($_POST['whatsapp_chat_tracking'])));
            $settings_updated = true;
        }

        // Show success message if any setting was updated
        if ($settings_updated) {
            echo '<div id="toast-success" class="fixed bottom-5 left-1/2 -translate-x-1/2 z-50 transform transition-all duration-300 opacity-0 translate-y-full">
                <div class="flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow-lg" role="alert">
                    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3 text-sm font-normal">Settings saved successfully!</div>
                </div>
            </div>';
            
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    const toast = document.getElementById("toast-success");
                    // Show toast
                    setTimeout(() => {
                        toast.classList.remove("opacity-0", "translate-y-full");
                    }, 100);
                    // Hide toast
                    setTimeout(() => {
                        toast.classList.add("opacity-0", "translate-y-full");
                        // Remove element after animation
                        setTimeout(() => {
                            toast.remove();
                        }, 3000);
                    }, 3000);
                });
            </script>';
        }
    }

    // Get saved options
    $whatsapp_number = get_option('whatsapp_chat_number', '');
    $inquiry_options = get_option('whatsapp_chat_options', array());
    $selected_icon = get_option('whatsapp_chat_icon_style', 'style1');

    ?>
<div class="wrap">
    <div class="py-6">
        <!-- Modern Header -->
        <div class="bg-gradient-to-r from-green-400 to-blue-500 -mt-6 -mx-4 px-8 py-8 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-2">WhatsApp by FlowFunel</h2>
                    <p class="text-green-50">Configure your WhatsApp chat widget settings</p>
                </div>
                <span class="px-4 py-2 bg-white/20 text-white rounded-full text-sm backdrop-blur-sm">Version 1.0</span>
            </div>
        </div>
        
        <form method="post" class="space-y-8">
            <?php wp_nonce_field('whatsapp_chat_settings', 'whatsapp_chat_nonce'); ?>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- WhatsApp Number Card -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-xl font-semibold mb-6 text-gray-700 pb-4 border-b">WhatsApp Account</h3>
                    <div class="space-y-4">
                        <div class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-4">
                            <div class="flex-grow">
                                <label class="text-sm font-medium text-gray-700 mb-2 block">WhatsApp Number</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-4 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm h-[35px]">+</span>
                                    <input type="text" 
                                           name="whatsapp_chat_number" 
                                           value="<?php echo esc_attr($whatsapp_number); ?>" 
                                           class="flex-1 min-w-0 rounded-none rounded-r-md border-gray-300 focus:ring-green-500 focus:border-green-500 h-[35px] text-sm"
                                           required>
                                </div>
                            </div>
                            <button type="submit" 
                                    name="whatsapp_chat_save" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                Save Number
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Original verification code commented out -->
                <?php /* 
                    Original WhatsApp Account verification section code here
                    ... 
                */ ?>

                <!-- Quick Stats Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-xl font-semibold mb-6 text-gray-700 pb-4 border-b">Quick Stats</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Total Clicks</span>
                            <span class="text-lg font-semibold text-gray-900">0</span>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Conversations</span>
                            <span class="text-lg font-semibold text-gray-900">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Container -->
            <div id="settings-container">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Basic Settings Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-xl font-semibold mb-6 text-gray-700 pb-4 border-b">Basic Settings</h3>
                        <div class="space-y-4">
                            <!-- Button Position -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">Button Position</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="relative flex rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                                        <input type="radio" name="whatsapp_chat_position" value="bottom-right" <?php checked(get_option('whatsapp_chat_position'), 'bottom-right'); ?> class="sr-only peer">
                                        <div class="flex items-center">
                                            <div class="text-sm">Bottom Right</div>
                                        </div>
                                        <div class="absolute inset-0 rounded-lg border-2 peer-checked:border-indigo-500 pointer-events-none"></div>
                                    </label>
                                    <label class="relative flex rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                                        <input type="radio" name="whatsapp_chat_position" value="bottom-left" <?php checked(get_option('whatsapp_chat_position'), 'bottom-left'); ?> class="sr-only peer">
                                        <div class="flex items-center">
                                            <div class="text-sm">Bottom Left</div>
                                        </div>
                                        <div class="absolute inset-0 rounded-lg border-2 peer-checked:border-indigo-500 pointer-events-none"></div>
                                    </label>
                                </div>
                            </div>

                            <!-- WhatsApp Icon Style -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">WhatsApp Icon Style</label>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="style-option flex flex-col items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 <?php echo $selected_icon === 'style1' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'; ?>" data-style="style1">
                                        <i class="fab fa-whatsapp text-4xl mb-2 text-[#25D366]"></i>
                                        <span class="text-sm">Classic</span>
                                    </div>
                                    <div class="style-option flex flex-col items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 <?php echo $selected_icon === 'style3' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'; ?>" data-style="style3">
                                        <div class="relative w-10 h-10 bg-[#25D366] rounded-full flex items-center justify-center mb-2">
                                            <i class="fab fa-whatsapp text-2xl text-white"></i>
                                        </div>
                                        <span class="text-sm">Circle</span>
                                    </div>
                                    <div class="style-option flex flex-col items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 <?php echo $selected_icon === 'style4' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'; ?>" data-style="style4">
                                        <div class="relative w-10 h-10 bg-[#25D366] rounded-lg flex items-center justify-center mb-2">
                                            <i class="fab fa-whatsapp text-2xl text-white"></i>
                                        </div>
                                        <span class="text-sm">Modern</span>
                                    </div>
                                </div>
                                <input type="hidden" name="whatsapp_chat_icon_style" id="selected_style" value="<?php echo esc_attr($selected_icon); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Inquiry Options Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b">
                            <h3 class="text-xl font-semibold text-gray-700">Inquiry Options</h3>
                            <button type="button" 
                                    onclick="addInquiryField()"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Add Option
                            </button>
                        </div>
                        <div id="inquiry-options" class="space-y-3">
                            <?php foreach ($inquiry_options as $option) : ?>
                                <div class="flex items-center space-x-2 p-2 rounded-lg bg-gray-50">
                                    <input type="text" 
                                           name="whatsapp_chat_options[]" 
                                           value="<?php echo esc_attr($option); ?>"
                                           class="flex-1 rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <button type="button" 
                                            onclick="removeInquiryField(this)"
                                            class="inline-flex items-center p-2 border border-transparent rounded-md text-red-600 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-6">
                <button type="submit" 
                        name="whatsapp_chat_save" 
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
<?php
}

// Add new AJAX handler for code verification
function handle_verify_code() {
    check_ajax_referer('verify_whatsapp_number', 'nonce');
    
    $code = sanitize_text_field($_POST['code']);
    
    // TODO: Implement your verification code validation logic here
    // For demo, we'll accept any 6-digit code
    if (strlen($code) === 6 && is_numeric($code)) {
        update_option('whatsapp_number_verified', true);
        wp_send_json_success([
            'message' => 'WhatsApp number verified successfully'
        ]);
    } else {
        wp_send_json_error([
            'message' => 'Invalid verification code'
        ]);
    }
}
add_action('wp_ajax_verify_whatsapp_code', 'handle_verify_code');

function handle_change_whatsapp_number() {
    check_ajax_referer('verify_whatsapp_number', 'nonce');
    
    $number = sanitize_text_field($_POST['number']);
    
    // Reset verification status
    update_option('whatsapp_number_verified', false);
    update_option('whatsapp_chat_number', $number);
    
    // TODO: Implement your WhatsApp number change logic here
    
    wp_send_json_success(array(
        'message' => 'WhatsApp number updated successfully'
    ));
}
add_action('wp_ajax_change_whatsapp_number', 'handle_change_whatsapp_number');

function handle_resend_verification_code() {
    check_ajax_referer('verify_whatsapp_number', 'nonce');
    
    $number = get_option('whatsapp_chat_number', '');
    
    if (empty($number)) {
        wp_send_json_error(array(
            'message' => 'No WhatsApp number found'
        ));
    }
    
    // TODO: Implement your verification code resend logic here
    
    wp_send_json_success(array(
        'message' => 'Verification code sent successfully'
    ));
}
add_action('wp_ajax_resend_verification_code', 'handle_resend_verification_code');

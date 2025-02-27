<?php

// Admin Menu
function whatsapp_chat_menu() {
    add_options_page('WhatsApp by FlowFunel.io', 'WhatsApp By FlowFunnel', 'manage_options', 'wa-chat-settings', 'whatsapp_chat_settings_page');
}
add_action('admin_menu', 'whatsapp_chat_menu');

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
            add_settings_error(
                'whatsapp_chat_messages',
                'whatsapp_chat_message',
                __('Settings Saved', 'easy-chat-widget'),
                'updated'
            );
        }
    }

    // Get saved options
    $whatsapp_number = get_option('whatsapp_chat_number', '');
    $inquiry_options = get_option('whatsapp_chat_options', array());
    $selected_icon = get_option('whatsapp_chat_icon_style', 'style1');

    // Show settings errors/messages
    settings_errors('whatsapp_chat_messages');
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
                <!-- WhatsApp Number Verification Card -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-xl font-semibold mb-6 text-gray-700 pb-4 border-b">WhatsApp Number Verification</h3>
                    <div class="space-y-4">
                        <div class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-4">
                            <div class="flex-grow">
                                <label class="text-sm font-medium text-gray-700 mb-2 block">WhatsApp Number</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-4 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">+</span>
                                    <input type="text" 
                                           name="whatsapp_chat_number" 
                                           value="<?php echo esc_attr($whatsapp_number); ?>" 
                                           class="flex-1 min-w-0 rounded-none rounded-r-md border-gray-300 focus:ring-green-500 focus:border-green-500"
                                           required>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button type="submit" 
                                        name="whatsapp_chat_update_number" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                    Update
                                </button>
                                <button type="button" 
                                        onclick="verifyWhatsAppNumber()"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    Verify
                                </button>
                            </div>
                        </div>
                        <?php 
                        $is_verified = get_option('whatsapp_number_verified', false);
                        $verification_status = $is_verified ? 
                            '<span class="text-green-600">✓ Verified</span>' : 
                            '<span class="text-red-600">✗ Not Verified</span>';
                        ?>
                        <div class="text-sm">
                            Status: <?php echo wp_kses_post($verification_status); ?>
                        </div>
                    </div>
                </div>

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
            <div id="settings-container" class="<?php echo !$is_verified ? 'filter blur-sm pointer-events-none' : ''; ?>">
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
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                        <?php echo !$is_verified ? 'disabled' : ''; ?>>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

    <script>
        function addInquiryField() {
            const container = document.getElementById('inquiry-options');
            const div = document.createElement('div');
            div.className = 'flex items-center space-x-2 p-2 rounded-lg bg-gray-50';
            div.innerHTML = `
                <input type="text" 
                       name="whatsapp_chat_options[]" 
                       class="flex-1 rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" 
                       placeholder="Enter inquiry option"
                       required>
                <button type="button" 
                        onclick="removeInquiryField(this)"
                        class="inline-flex items-center p-2 border-transparent rounded-md text-red-600 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            `;
            container.appendChild(div);
        }

        function removeInquiryField(button) {
            button.parentElement.remove();
        }

        // Replace the existing selectStyle function with this new code:
        document.addEventListener('DOMContentLoaded', function() {
            const styleOptions = document.querySelectorAll('.style-option');
            const hiddenInput = document.getElementById('selected_style');

            styleOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove active class from all options
                    styleOptions.forEach(opt => {
                        opt.classList.remove('border-indigo-500', 'bg-indigo-50');
                        opt.classList.add('border-gray-200');
                    });

                    // Add active class to clicked option
                    this.classList.remove('border-gray-200');
                    this.classList.add('border-indigo-500', 'bg-indigo-50');

                    // Update hidden input value
                    hiddenInput.value = this.dataset.style;
                });
            });
        });

        function verifyWhatsAppNumber() {
            const number = document.querySelector('input[name="whatsapp_chat_number"]').value;
            
            // Show loading state
            const verifyButton = event.target;
            const originalText = verifyButton.innerHTML;
            verifyButton.innerHTML = 'Verifying...';
            verifyButton.disabled = true;

            // Make AJAX call to verify number
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'verify_whatsapp_number',
                    number: number,
                    nonce: '<?php echo wp_create_nonce("verify_whatsapp_number"); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert('WhatsApp number verified successfully!');
                        location.reload(); // Reload to update UI
                    } else {
                        alert('Verification failed: ' + response.data.message);
                    }
                },
                error: function() {
                    alert('Verification failed. Please try again.');
                },
                complete: function() {
                    verifyButton.innerHTML = originalText;
                    verifyButton.disabled = false;
                }
            });
        }
    </script>
    <?php
}

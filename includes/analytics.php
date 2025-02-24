<?php
function track_whatsapp_click() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'whatsapp_track_nonce')) {
        wp_send_json_error('Invalid nonce');
        return;
    }

    if (isset($_POST['option'])) {
        $option = sanitize_text_field(wp_unslash($_POST['option']));
        $clicks = get_option("whatsapp_clicks_$option", 0);
        update_option("whatsapp_clicks_$option", $clicks + 1);
        wp_send_json_success('Click tracked successfully');
    } else {
        wp_send_json_error('Missing option parameter');
    }
}
add_action('wp_ajax_track_whatsapp_click', 'track_whatsapp_click');
add_action('wp_ajax_nopriv_track_whatsapp_click', 'track_whatsapp_click');

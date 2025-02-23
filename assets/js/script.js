function toggleWhatsAppPopup() {
    const button = document.querySelector(".whatsapp-chat-button");
    const popup = document.querySelector(".whatsapp-popup");
    
    if (popup) {
        button.classList.toggle('active');
        popup.classList.toggle('active');
        // Force display block when active
        popup.style.display = popup.classList.contains('active') ? 'block' : 'none';
    }

    // Close popup when clicking outside
    document.addEventListener('click', function(event) {
        if (!popup.contains(event.target) && !button.contains(event.target)) {
            popup.classList.remove('active');
            button.classList.remove('active');
        }
    });
}

document.addEventListener("click", function(event) {
    const container = document.querySelector(".whatsapp-chat-container");
    const popup = document.querySelector(".whatsapp-popup");
    const button = document.querySelector(".whatsapp-chat-button");
    
    if (container && popup && !container.contains(event.target)) {
        popup.classList.remove('active');
        button.classList.remove('active');
    }
});

function trackWhatsAppClick(option) {
    if (whatsappChatData.trackingEnabled === 'yes') {
        jQuery.post(ajaxurl, {
            action: 'track_whatsapp_click',
            option: option
        });
    }
}

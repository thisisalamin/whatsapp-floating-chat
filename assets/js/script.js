function toggleWhatsAppPopup() {
    const popup = document.getElementById('whatsapp-popup');
    
    if (popup.classList.contains('hidden')) {
        popup.classList.remove('hidden');
        // Force reflow
        void popup.offsetWidth;
        popup.classList.add('show');
    } else {
        popup.classList.remove('show');
        setTimeout(() => {
            popup.classList.add('hidden');
        }, 300); // Match the CSS transition duration
    }
}

// Close popup when clicking outside
document.addEventListener('click', function(event) {
    const popup = document.getElementById('whatsapp-popup');
    const button = document.querySelector('.whatsapp-chat-button');
    
    if (!popup.contains(event.target) && !button.contains(event.target) && !popup.classList.contains('hidden')) {
        toggleWhatsAppPopup();
    }
});

function trackWhatsAppClick(option) {
    // Add tracking logic here if needed
    console.log('Clicked option:', option);
}

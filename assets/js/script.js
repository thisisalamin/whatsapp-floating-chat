function toggleWhatsAppPopup() {
    const popup = document.getElementById('whatsapp-popup');
    popup.classList.toggle('active');
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
    // Add tracking logic here if needed
    console.log('Clicked option:', option);
}

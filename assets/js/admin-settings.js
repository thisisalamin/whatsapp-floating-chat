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

// Style options handler
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
            nonce: whatsappAdminSettings.nonce
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

function updateVerificationStatus(isVerified) {
    const statusElement = document.getElementById('verification-status');
    if (statusElement) {
        statusElement.innerHTML = `Status: ${
            isVerified 
            ? '<span class="text-green-600">✓ Verified</span>' 
            : '<span class="text-red-600">✗ Not Verified</span>'
        }`;
    }

    // Update settings container
    const settingsContainer = document.getElementById('settings-container');
    if (settingsContainer) {
        if (isVerified) {
            settingsContainer.classList.remove('filter', 'blur-sm', 'pointer-events-none');
        } else {
            settingsContainer.classList.add('filter', 'blur-sm', 'pointer-events-none');
        }
    }
}

function changeWhatsAppNumber() {
    const inputField = document.querySelector('input[name="whatsapp_chat_number"]');
    const changeButton = event.target.closest('button');
    
    if (changeButton.getAttribute('data-editing') === 'true') {
        // Save changes
        const number = inputField.value;
        if (!number) {
            alert('Please enter a WhatsApp number');
            return;
        }
        
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'change_whatsapp_number',
                number: number,
                nonce: whatsappAdminSettings.nonce
            },
            success: function(response) {
                if (response.success) {
                    inputField.setAttribute('readonly', true);
                    inputField.classList.remove('border-green-500');
                    changeButton.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Change WhatsApp`;
                    changeButton.setAttribute('data-editing', 'false');
                    updateVerificationStatus(false); // This will now update correctly
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('Error changing WhatsApp number');
            }
        });
    } else {
        // Enable editing
        inputField.removeAttribute('readonly');
        inputField.classList.add('border-green-500');
        inputField.focus();
        changeButton.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Save Changes`;
        changeButton.setAttribute('data-editing', 'true');
    }
}

function resendVerificationCode() {
    const inputField = document.querySelector('input[name="whatsapp_chat_number"]');
    const resendButton = event.target.closest('button');
    const changeButton = document.querySelector('.change-whatsapp-btn');
    
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'resend_verification_code',
            nonce: whatsappAdminSettings.nonce
        },
        success: function(response) {
            if (response.success) {
                // Store original number
                inputField.setAttribute('data-original-number', inputField.value);
                
                // Transform input field for verification code
                inputField.value = '';
                inputField.removeAttribute('readonly');
                inputField.placeholder = 'Enter 6-digit verification code';
                inputField.maxLength = 6;
                inputField.classList.add('border-green-500');
                inputField.focus();
                
                // Hide change button
                changeButton.style.display = 'none';
                
                // Transform resend button to verify button
                resendButton.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Verify Code`;
                resendButton.onclick = verifyCode;
            }
        },
        error: function() {
            alert('Error sending verification code');
        }
    });
}

function verifyCode() {
    const code = document.querySelector('input[name="whatsapp_chat_number"]').value;
    const inputField = document.querySelector('input[name="whatsapp_chat_number"]');
    const changeButton = document.querySelector('.change-whatsapp-btn');
    const verifyButton = document.querySelector('.verify-btn');
    
    if (code.length !== 6 || !/^\d+$/.test(code)) {
        alert('Please enter a valid 6-digit verification code');
        return;
    }
    
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'verify_whatsapp_code',
            code: code,
            nonce: whatsappAdminSettings.nonce
        },
        success: function(response) {
            if (response.success) {
                // Restore UI to original state
                inputField.value = document.querySelector('input[name="whatsapp_chat_number"]').getAttribute('data-original-number');
                inputField.setAttribute('readonly', true);
                inputField.classList.remove('border-green-500');
                inputField.placeholder = 'WhatsApp Number';
                
                // Show change button
                changeButton.style.display = 'inline-flex';
                
                // Reset verify button
                verifyButton.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Resend Code`;
                verifyButton.onclick = resendVerificationCode;
                
                updateVerificationStatus(true); // This will now update correctly
                alert(response.data.message);
            } else {
                alert(response.data.message);
            }
        },
        error: function() {
            alert('Error verifying code');
        }
    });
}

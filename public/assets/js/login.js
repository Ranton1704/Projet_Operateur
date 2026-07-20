document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('numero_telephone');
    const submitBtn = document.querySelector('.btn-submit');
    
    // Create validation message elements if they don't exist
    let validationMessage = document.getElementById('validation-message');
    if (!validationMessage) {
        validationMessage = document.createElement('div');
        validationMessage.id = 'validation-message';
        phoneInput.parentElement.parentElement.appendChild(validationMessage);
    }

    // Valid prefixes for Madagascar mobile numbers
    const validPrefixes = ['32', '33', '34', '38'];
    
    // Format phone number as user types
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, ''); // Remove all spaces
        
        // Remove +261 if user types it
        if (value.startsWith('261')) {
            value = value.substring(3);
        }
        
        // Ensure leading 0 if user starts with valid prefix
        if (value.length > 0 && !value.startsWith('0')) {
            const firstTwo = value.substring(0, 2);
            if (validPrefixes.includes(firstTwo)) {
                value = '0' + value;
            }
        }
        
        // Format with spaces: 0XX XX XXX XX
        let formatted = '';
        if (value.length > 0) {
            for (let i = 0; i < value.length; i++) {
                if (i === 3 || i === 5 || i === 8) {
                    formatted += ' ';
                }
                formatted += value[i];
            }
        }
        
        e.target.value = formatted;
        
        // Validate the number
        validatePhoneNumber(value);
    });

    function validatePhoneNumber(phoneNumber) {
        const cleanNumber = phoneNumber.replace(/\s/g, '');
        
        // Check if empty
        if (cleanNumber.length === 0) {
            showValidationMessage('', '');
            submitBtn.disabled = false;
            return;
        }
        
        // Check digit count (should be 10 digits with leading 0)
        if (cleanNumber.length < 10) {
            showValidationMessage('Le numéro doit contenir 10 chiffres', 'error');
            submitBtn.disabled = true;
            return;
        }
        
        if (cleanNumber.length > 10) {
            showValidationMessage('Le numéro ne doit pas dépasser 10 chiffres', 'error');
            submitBtn.disabled = true;
            return;
        }
        
        // Check if starts with 0
        if (!cleanNumber.startsWith('0')) {
            showValidationMessage('Le numéro doit commencer par 0', 'error');
            submitBtn.disabled = true;
            return;
        }
        
        // Check prefix (digits 1-2, after the leading 0)
        const prefix = cleanNumber.substring(1, 3);
        if (!validPrefixes.includes(prefix)) {
            showValidationMessage('Le préfixe doit commencer par 032, 033, 034 ou 038', 'error');
            submitBtn.disabled = true;
            return;
        }
        
        // All validations passed
        showValidationMessage('Numéro valide', 'success');
        submitBtn.disabled = false;
    }

    function showValidationMessage(message, type) {
        validationMessage.textContent = message;
        validationMessage.className = '';
        
        const phoneInputGroup = document.querySelector('.phone-input-group');
        phoneInputGroup.classList.remove('error', 'success');
        
        if (type === 'error') {
            validationMessage.classList.add('validation-error');
            phoneInputGroup.classList.add('error');
        } else if (type === 'success') {
            validationMessage.classList.add('validation-success');
            phoneInputGroup.classList.add('success');
        }
    }

    // Prevent form submission if validation fails
    document.querySelector('form').addEventListener('submit', function(e) {
        const phoneNumber = phoneInput.value.replace(/\s/g, '');
        if (phoneNumber.length !== 10) {
            e.preventDefault();
            showValidationMessage('Veuillez entrer un numéro valide', 'error');
        }
    });
});

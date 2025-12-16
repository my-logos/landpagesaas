// Phone number validation with country code
(function () {
  'use strict';

  function validateCountryCode(code) {
    // Country codes are 1-4 digits, starting with 1-9 (not 0)
    const codePattern = /^[1-9]\d{0,3}$/;
    return codePattern.test(code);
  }

  function validatePhoneNumber(phone, countryCode) {
    // Basic phone validation - digits only, 6-15 digits
    const phonePattern = /^\d{6,15}$/;
    if (!phonePattern.test(phone)) {
      return false;
    }

    // Additional validation based on country code
    const fullNumber = '+' + countryCode + phone;

    // Check for common invalid patterns
    if (phone.length < 6 || phone.length > 15) {
      return false;
    }

    // Check for repeated digits (likely invalid)
    if (/(\d)\1{9,}/.test(phone)) {
      return false;
    }

    return true;
  }

  function showValidationMessage(input, message, isError) {
    let validationEl = input.parentElement.querySelector('.auth-phone-validation');
    if (!validationEl) {
      validationEl = document.createElement('span');
      validationEl.className = 'auth-phone-validation';
      input.parentElement.appendChild(validationEl);
    }

    validationEl.textContent = message;
    validationEl.style.display = 'block';
    validationEl.className = 'auth-phone-validation' + (isError ? ' error' : '');

    if (isError) {
      input.style.borderColor = '#e74c3c';
    } else {
      input.style.borderColor = '#27ae60';
    }
  }

  function hideValidationMessage(input) {
    const validationEl = input.parentElement.querySelector('.auth-phone-validation');
    if (validationEl) {
      validationEl.style.display = 'none';
    }
    input.style.borderColor = '';
  }

  // Validate country code input
  document.addEventListener('DOMContentLoaded', function () {
    const countryCodeInputs = document.querySelectorAll('[data-validate="country-code"]');
    const phoneInputs = document.querySelectorAll('[data-validate="phone"]');

    countryCodeInputs.forEach(function (input) {
      // Only allow numbers
      input.addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, '');

        if (this.value.length > 0 && !validateCountryCode(this.value)) {
          showValidationMessage(this, 'Invalid country code', true);
        } else if (this.value.length > 0) {
          hideValidationMessage(this);
        }
      });

      input.addEventListener('blur', function () {
        if (this.value.length === 0) {
          this.value = '20'; // Default to Egypt
          this.setAttribute('value', '20');
        }
        if (!validateCountryCode(this.value)) {
          showValidationMessage(this, 'Invalid country code', true);
        } else {
          hideValidationMessage(this);
        }
      });
    });

    phoneInputs.forEach(function (input) {
      const countryCodeInput = input.closest('.auth-phone-wrapper').querySelector('[data-validate="country-code"]');

      // Only allow numbers
      input.addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, '');

        if (this.value.length > 0) {
          const countryCode = countryCodeInput ? countryCodeInput.value : '20';
          if (validatePhoneNumber(this.value, countryCode)) {
            hideValidationMessage(this);
          } else {
            showValidationMessage(this, 'Invalid phone number format', true);
          }
        } else {
          hideValidationMessage(this);
        }
      });

      input.addEventListener('blur', function () {
        if (this.value.length > 0) {
          const countryCode = countryCodeInput ? countryCodeInput.value : '20';
          if (!validatePhoneNumber(this.value, countryCode)) {
            showValidationMessage(this, 'Please enter a valid phone number (6-15 digits)', true);
          } else {
            showValidationMessage(this, 'Valid phone number', false);
            setTimeout(function () {
              hideValidationMessage(input);
            }, 2000);
          }
        }
      });
    });
  });
})();

/* =====================================================================
   Contact form validation: required fields, regex checks for email
   and phone, and a minimum quantity for custom print requests.
   Errors are shown under each field.
   ===================================================================== */

(function () {
    const form = document.getElementById('contactForm');
    if (!form) return;

    // Email: text@text.domain (domain of at least 2 letters)
    const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;
    // Phone: optional +, then 8-15 digits, spaces or dashes
    const PHONE_REGEX = /^\+?[0-9\s-]{8,15}$/;

    const typeSelect = form.elements.type;
    const quantityGroup = document.getElementById('quantityGroup');
    const isCustom = () => typeSelect.value === 'custom';

    // One rule per field: return an error message, or '' if the value is valid
    const rules = {
        name: (v) => (v.length < 2 ? 'Enter your name.' : ''),
        email: (v) => {
            if (v === '') return 'Enter your email.';
            if (!EMAIL_REGEX.test(v)) return 'Enter a valid email, like name@example.com.';
            return '';
        },
        phone: (v) => (v !== '' && !PHONE_REGEX.test(v) ? 'Enter 8 to 15 digits, or leave this empty.' : ''),
        type: (v) => (v === '' ? 'Choose what your message is about.' : ''),
        quantity: (v) => {
            if (!isCustom()) return ''; // only required for custom prints
            return !/^\d+$/.test(v) || Number(v) < 12 ? 'Custom prints start at 12 shirts.' : '';
        },
        message: (v) => (v.length < 20 ? 'Tell us a bit more (at least 20 characters).' : ''),
    };

    // Validate one field and show/hide its error; returns true if valid
    function validateField(input) {
        const error = rules[input.name](input.value.trim());
        const group = input.closest('.form-group');

        group.classList.toggle('invalid', error !== '');
        group.querySelector('.error-msg').textContent = error;
        input.setAttribute('aria-invalid', error !== '');
        return error === '';
    }

    const inputs = Object.keys(rules).map((name) => form.elements[name]);

    // Live feedback: re-check a field when the user leaves it or edits an invalid one
    inputs.forEach((input) => {
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => {
            if (input.closest('.form-group').classList.contains('invalid')) validateField(input);
        });
    });

    // Show the quantity field only for custom print requests
    typeSelect.addEventListener('change', () => {
        quantityGroup.hidden = !isCustom();
    });

    form.addEventListener('submit', (e) => {
        // map() runs every check (unlike every()), so all errors are shown at once
        const results = inputs.map(validateField);

        if (results.includes(false)) {
            e.preventDefault(); // stop the form from being sent
            inputs[results.indexOf(false)].focus(); // jump to the first invalid field
        }
    });
})();

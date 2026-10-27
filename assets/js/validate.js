/* =====================================================================
   Contact form validation: required fields, minimum lengths and
   email format (regex). Errors are shown under each field.
   ===================================================================== */

(function () {
    const form = document.getElementById('contactForm');
    if (!form) return;

    // Email pattern: text@text.domain (domain of at least 2 letters)
    const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;

    // One rule per field: return an error message, or '' if the value is valid
    const rules = {
        name: (v) => {
            if (v === '') return 'Name is required.';
            if (v.length < 3) return 'Name must be at least 3 characters.';
            return '';
        },
        email: (v) => {
            if (v === '') return 'Email is required.';
            if (!EMAIL_REGEX.test(v)) return 'Please enter a valid email (e.g. name@uqu.edu.sa).';
            return '';
        },
        subject: (v) => (v === '' ? 'Subject is required.' : ''),
        message: (v) => {
            if (v === '') return 'Message is required.';
            if (v.length < 10) return 'Message must be at least 10 characters.';
            return '';
        },
    };

    // Validate one field and show/hide its error; returns true if valid
    function validateField(input) {
        const error = rules[input.name](input.value.trim());
        const group = input.closest('.form-group');

        group.classList.toggle('invalid', error !== '');
        group.querySelector('.error-msg').textContent = error;
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

    form.addEventListener('submit', (e) => {
        // map() runs every check (unlike every()), so all errors are shown at once
        const results = inputs.map(validateField);

        if (results.includes(false)) {
            e.preventDefault(); // stop the form from being sent
            inputs[results.indexOf(false)].focus(); // jump to the first invalid field
        }
    });
})();

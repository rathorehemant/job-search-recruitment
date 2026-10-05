/**
 * Reusable Form Validation + AJAX Library
 *
 * Usage:
 *
 * <form
 *     id="loginForm"
 *     data-ajax-form
 *     action="/login"
 *     method="POST"
 * >
 *
 *     <input
 *         type="email"
 *         name="email"
 *         required
 *         data-label="Email"
 *     >
 *
 *     <input
 *         type="password"
 *         name="password"
 *         required
 *         minlength="8"
 *         data-label="Password"
 *     >
 *
 *     <button type="submit">Login</button>
 *
 * </form>
 */

(function (window, document) {

    'use strict';

    const FormHandler = {

        config: {
            formSelector: '[data-ajax-form]',
            errorClass: 'is-invalid',
            errorMessageClass: 'form-error-message',
            loadingClass: 'is-loading',
        },

        /**
         * Initialize library
         */
        init() {

            this.addStyles();

            const forms = document.querySelectorAll(
                this.config.formSelector
            );

            forms.forEach(form => {
                this.initForm(form);
            });
        },

        /**
         * Initialize individual form
         */
        initForm(form) {

            const fields = form.querySelectorAll(
                'input, select, textarea'
            );

            // Disable browser default validation
            form.setAttribute('novalidate', 'novalidate');

            fields.forEach(field => {

                // Validate when user leaves field
                field.addEventListener('blur', () => {
                    this.validateField(field);
                });

                /*
                 * Validate while typing only when
                 * the field already has an error.
                 *
                 * This removes the error immediately
                 * when the user enters a valid value.
                 */
                field.addEventListener('input', () => {

                    if (
                        field.classList.contains(
                            this.config.errorClass
                        )
                    ) {
                        this.validateField(field);
                    }
                });

                // Select, checkbox, radio etc.
                field.addEventListener('change', () => {
                    this.validateField(field);
                });
            });

            // AJAX submit
            form.addEventListener('submit', async (event) => {

                event.preventDefault();

                await this.submitForm(form);
            });
        },

        /**
         * Validate complete form
         */
        validateForm(form) {

            let isValid = true;

            const fields = form.querySelectorAll(
                'input, select, textarea'
            );

            fields.forEach(field => {

                if (field.disabled) {
                    return;
                }

                if (!this.validateField(field)) {
                    isValid = false;
                }
            });

            return isValid;
        },

        /**
         * Validate single field
         */
        validateField(field) {

            if (field.disabled) {

                this.clearError(field);

                return true;
            }

            const value = this.getFieldValue(field);

            /*
             * Required
             */
            if (field.hasAttribute('required')) {

                if (!value) {

                    this.showError(
                        field,
                        this.getRequiredMessage(field)
                    );

                    return false;
                }
            }

            /*
             * Optional empty fields are valid
             */
            if (!value) {

                this.clearError(field);

                return true;
            }

            /*
             * Email
             */
            if (
                field.type === 'email' &&
                !this.isValidEmail(value)
            ) {

                this.showError(
                    field,
                    field.dataset.emailMessage ||
                    'Please enter a valid email address.'
                );

                return false;
            }

            /*
             * Min length
             */
            if (field.hasAttribute('minlength')) {

                const minLength = parseInt(
                    field.getAttribute('minlength'),
                    10
                );

                if (value.length < minLength) {

                    this.showError(
                        field,
                        field.dataset.minlengthMessage ||
                        `Minimum ${minLength} characters required.`
                    );

                    return false;
                }
            }

            /*
             * Max length
             */
            if (field.hasAttribute('maxlength')) {

                const maxLength = parseInt(
                    field.getAttribute('maxlength'),
                    10
                );

                if (value.length > maxLength) {

                    this.showError(
                        field,
                        field.dataset.maxlengthMessage ||
                        `Maximum ${maxLength} characters allowed.`
                    );

                    return false;
                }
            }

            /*
             * Pattern
             */
            if (field.hasAttribute('pattern')) {

                const pattern =
                    field.getAttribute('pattern');

                try {

                    const regex =
                        new RegExp(`^(?:${pattern})$`);

                    if (!regex.test(value)) {

                        this.showError(
                            field,
                            field.dataset.patternMessage ||
                            'Please enter a valid value.'
                        );

                        return false;
                    }

                } catch (error) {

                    console.error(
                        'Invalid validation pattern:',
                        error
                    );
                }
            }

            /*
             * Number minimum
             */
            if (
                field.type === 'number' &&
                field.hasAttribute('min')
            ) {

                const min = parseFloat(
                    field.getAttribute('min')
                );

                if (parseFloat(value) < min) {

                    this.showError(
                        field,
                        field.dataset.minMessage ||
                        `Value must be at least ${min}.`
                    );

                    return false;
                }
            }

            /*
             * Number maximum
             */
            if (
                field.type === 'number' &&
                field.hasAttribute('max')
            ) {

                const max = parseFloat(
                    field.getAttribute('max')
                );

                if (parseFloat(value) > max) {

                    this.showError(
                        field,
                        field.dataset.maxMessage ||
                        `Value must not be greater than ${max}.`
                    );

                    return false;
                }
            }

            /*
             * Password confirmation
             *
             * Example:
             *
             * data-confirm="password"
             */
            if (field.dataset.confirm) {

                const originalField =
                    field.form
                        ? field.form.querySelector(
                            `[name="${CSS.escape(field.dataset.confirm)}"]`
                        )
                        : document.querySelector(
                            `[name="${CSS.escape(field.dataset.confirm)}"]`
                        );

                if (
                    originalField &&
                    value !== this.getFieldValue(originalField)
                ) {

                    this.showError(
                        field,
                        field.dataset.confirmMessage ||
                        'Passwords do not match.'
                    );

                    return false;
                }
            }

            /*
             * Custom validation
             *
             * data-validate="phone"
             * data-validate="name"
             */
            if (field.dataset.validate) {

                const result =
                    this.runCustomValidation(
                        field,
                        value
                    );

                if (!result.valid) {

                    this.showError(
                        field,
                        result.message
                    );

                    return false;
                }
            }

            /*
             * Valid
             */
            this.clearError(field);

            return true;
        },

        /**
         * Email validation
         */
        isValidEmail(value) {

            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(
                value
            );
        },

        /**
         * Custom validation
         */
        runCustomValidation(field, value) {

            const type =
                field.dataset.validate;

            switch (type) {

                case 'phone':

                    if (!/^[0-9]{10}$/.test(value)) {

                        return {
                            valid: false,
                            message:
                                'Please enter a valid 10 digit phone number.'
                        };
                    }

                    break;

                case 'name':

                    if (!/^[a-zA-Z\s]+$/.test(value)) {

                        return {
                            valid: false,
                            message:
                                'Name can contain only letters and spaces.'
                        };
                    }

                    break;

                default:
                    break;
            }

            return {
                valid: true,
                message: ''
            };
        },

        /**
         * Get field value
         */
        getFieldValue(field) {

            if (field.type === 'checkbox') {

                return field.checked
                    ? field.value
                    : '';
            }

            if (field.type === 'radio') {

                const checked =
                    field.form
                        ? field.form.querySelector(
                            `input[name="${CSS.escape(field.name)}"]:checked`
                        )
                        : document.querySelector(
                            `input[name="${CSS.escape(field.name)}"]:checked`
                        );

                return checked
                    ? checked.value
                    : '';
            }

            if (field.type === 'file') {

                return field.files.length
                    ? field.files
                    : '';
            }

            return field.value.trim();
        },

        /**
         * Required message
         */
        getRequiredMessage(field) {

            if (field.dataset.requiredMessage) {
                return field.dataset.requiredMessage;
            }

            const label =
                this.getFieldLabel(field);

            return `${label} is required.`;
        },

        /**
         * Get field label
         */
        getFieldLabel(field) {

            if (field.dataset.label) {
                return field.dataset.label;
            }

            if (field.id) {

                const label =
                    document.querySelector(
                        `label[for="${CSS.escape(field.id)}"]`
                    );

                if (label) {

                    return label.textContent
                        .replace('*', '')
                        .trim();
                }
            }

            if (field.name) {

                return field.name
                    .replace(/_/g, ' ')
                    .replace(
                        /\b\w/g,
                        char => char.toUpperCase()
                    );
            }

            return 'This field';
        },

        /**
         * Show error
         */
        showError(field, message) {

            this.clearError(field);

            field.classList.add(
                this.config.errorClass
            );

            const container =
                field.closest(
                    '.mb-3, .form-group, .form-field, .input-group-wrapper'
                ) ||
                field.parentElement;

            const error =
                document.createElement('div');

            error.className =
                this.config.errorMessageClass;

            error.setAttribute(
                'role',
                'alert'
            );

            error.textContent =
                message;

            container.appendChild(error);
        },

        /**
         * Clear error
         */
        clearError(field) {

            field.classList.remove(
                this.config.errorClass
            );

            const container =
                field.closest(
                    '.mb-3, .form-group, .form-field, .input-group-wrapper'
                ) ||
                field.parentElement;

            const error =
                container.querySelector(
                    `.${this.config.errorMessageClass}`
                );

            if (error) {
                error.remove();
            }
        },

        /**
         * AJAX form submission
         */
        async submitForm(form) {

            if (!this.validateForm(form)) {

                const firstError =
                    form.querySelector(
                        `.${this.config.errorClass}`
                    );

                if (firstError) {

                    firstError.focus();

                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }

                return;
            }

            const submitButton =
                form.querySelector(
                    '[type="submit"]'
                );

            const originalButtonText =
                submitButton
                    ? submitButton.innerHTML
                    : '';

            try {

                this.setLoading(
                    form,
                    true
                );

                const formData =
                    new FormData(form);

                const method =
                    (
                        form.getAttribute('method') ||
                        'POST'
                    ).toUpperCase();

                const action =
                    form.getAttribute('action') ||
                    window.location.href;

                const response =
                    await fetch(
                        action,
                        {
                            method: method,

                            headers: {
                                'X-CSRF-TOKEN':
                                    this.getCsrfToken(),

                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'
                            },

                            body: formData
                        }
                    );

                const data =
                    await this.parseResponse(response);

                /*
                 * Laravel validation error
                 */
                if (response.status === 422) {

                    this.handleServerValidationErrors(
                        form,
                        data.errors || {}
                    );

                    return;
                }

                /*
                 * Unauthorized
                 */
                if (response.status === 401) {

                    this.showFormMessage(
                        form,
                        'error',
                        data.message ||
                        'Invalid credentials.'
                    );

                    return;
                }

                /*
                 * Forbidden
                 */
                if (response.status === 403) {

                    this.showFormMessage(
                        form,
                        'error',
                        data.message ||
                        'You are not authorized to perform this action.'
                    );

                    return;
                }

                /*
                 * Other errors
                 */
                if (!response.ok) {

                    this.showFormMessage(
                        form,
                        'error',
                        data.message ||
                        'Something went wrong. Please try again.'
                    );

                    return;
                }

                /*
                 * Success
                 */
                this.showFormMessage(
                    form,
                    'success',
                    data.message ||
                    'Request completed successfully.'
                );

                /*
                 * Custom event
                 */
                form.dispatchEvent(
                    new CustomEvent(
                        'form:success',
                        {
                            detail: data
                        }
                    )
                );

                /*
                 * Redirect
                 */
                if (data.redirect) {

                    window.location.href =
                        data.redirect;

                    return;
                }

            } catch (error) {

                console.error(
                    'Form submission error:',
                    error
                );

                this.showFormMessage(
                    form,
                    'error',
                    'Unable to process your request. Please try again.'
                );

            } finally {

                this.setLoading(
                    form,
                    false,
                    submitButton,
                    originalButtonText
                );
            }
        },

        /**
         * Parse response
         */
        async parseResponse(response) {

            const contentType =
                response.headers.get(
                    'content-type'
                );

            if (
                contentType &&
                contentType.includes(
                    'application/json'
                )
            ) {

                return await response.json();
            }

            const text =
                await response.text();

            return {
                message: text
            };
        },

        /**
         * Handle Laravel validation errors
         */
        handleServerValidationErrors(
            form,
            errors
        ) {

            Object.keys(errors).forEach(
                fieldName => {

                    const field =
                        form.querySelector(
                            `[name="${CSS.escape(fieldName)}"]`
                        );

                    if (!field) {
                        return;
                    }

                    const message =
                        Array.isArray(
                            errors[fieldName]
                        )
                            ? errors[fieldName][0]
                            : errors[fieldName];

                    this.showError(
                        field,
                        message
                    );
                }
            );

            const firstError =
                form.querySelector(
                    `.${this.config.errorClass}`
                );

            if (firstError) {

                firstError.focus();

                firstError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        },

        /**
         * Loading state
         */
        setLoading(
            form,
            loading,
            submitButton = null,
            originalText = null
        ) {

            if (!submitButton) {

                submitButton =
                    form.querySelector(
                        '[type="submit"]'
                    );
            }

            if (!submitButton) {
                return;
            }

            if (loading) {

                submitButton.disabled = true;

                submitButton.dataset.originalText =
                    submitButton.innerHTML;

                submitButton.innerHTML = `
                    <span
                        class="spinner-border spinner-border-sm me-2"
                        role="status"
                        aria-hidden="true"
                    ></span>
                    Processing...
                `;

                form.classList.add(
                    this.config.loadingClass
                );

            } else {

                submitButton.disabled = false;

                submitButton.innerHTML =
                    originalText ||
                    submitButton.dataset.originalText ||
                    'Submit';

                form.classList.remove(
                    this.config.loadingClass
                );
            }
        },

        /**
         * Get Laravel CSRF token
         */
        getCsrfToken() {

            const meta =
                document.querySelector(
                    'meta[name="csrf-token"]'
                );

            return meta
                ? meta.getAttribute('content')
                : '';
        },

        /**
         * Show general response
         */
        showFormMessage(
            form,
            type,
            message
        ) {

            let messageBox =
                form.querySelector(
                    '.form-response-message'
                );

            if (!messageBox) {

                messageBox =
                    document.createElement('div');

                messageBox.className =
                    'form-response-message';

                form.prepend(
                    messageBox
                );
            }

            messageBox.className =
                `form-response-message alert alert-${
                    type === 'success'
                        ? 'success'
                        : 'danger'
                }`;

            messageBox.setAttribute(
                'role',
                'alert'
            );

            messageBox.textContent =
                message;
        },

        /**
         * Add required CSS
         */
        addStyles() {

            if (
                document.getElementById(
                    'form-handler-styles'
                )
            ) {
                return;
            }

            const style =
                document.createElement('style');

            style.id =
                'form-handler-styles';

            style.textContent = `

                .form-error-message {
                    color: #dc3545;
                    font-size: 0.875rem;
                    margin-top: 5px;
                    line-height: 1.4;
                }

                .form-control.is-invalid,
                .form-select.is-invalid {
                    border-color: #dc3545 !important;
                    background-image: none !important;
                }

                .form-control.is-invalid:focus,
                .form-select.is-invalid:focus {
                    border-color: #dc3545 !important;
                    box-shadow:
                        0 0 0 0.2rem
                        rgba(220, 53, 69, 0.15) !important;
                }

                .form-response-message {
                    margin-bottom: 1rem;
                }

                .is-loading {
                    opacity: 0.85;
                    pointer-events: none;
                }

            `;

            document.head.appendChild(
                style
            );
        }
    };

    /**
     * Initialize after DOM ready
     */
    document.addEventListener(
        'DOMContentLoaded',
        () => {
            FormHandler.init();
        }
    );

    /**
     * Make globally available
     */
    window.FormHandler =
        FormHandler;

})(window, document);
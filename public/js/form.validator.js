$(document).ready(function () {
    $('form').find('.card-body').children('div').addClass('form-group');
});
/**
 * function validateForm
 * @param form_element
 */
function validateForm(form_element) {
    form_element.validate({
        rules: {
            text: {
                required: true,
                minlength: 5
            },
            email: {
                required: true,
                email: true,
            },
            password: {
                required: true,
                minlength: 5
            }
        },
        messages: {
            text: {
                required: validator_text_message
            },
            email: {
                required: validator_email_message,
                email: validator_email_message_valid
            },
            password: {
                required: validator_password_message,
                minlength: validator_password_message_length
            },
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
}
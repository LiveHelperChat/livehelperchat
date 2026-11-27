$( document ).ready(function() {

    let validForm = false;
    let validRequiredGroups = false;
    let requiredGroups = [];

    if (!document.getElementById('group-required-holder') || !document.getElementById('label-validation-groups')){
        return;
    }

    requiredGroups = JSON.parse($('#group-required-holder').attr('data-required-groups'));

    let labelValidation = document.getElementById('label-validation-groups');
    let labelValidationIcon = document.getElementById('label-validation-icon');

    $('#group-required-holder input[type="checkbox"]').change(function(){
        $('#group-required-holder input[type="checkbox"]').each(function(){
            requiredGroups[$(this).val()] = $(this).is(':checked');
        })
        updateUI();
    })

    function updateUI() {
        validForm = validRequiredGroups = Object.keys(requiredGroups).filter(function(key) { return requiredGroups[key] !== false; }).length > 0;

        if (!validRequiredGroups) {
            labelValidation.classList.add('chat-closed');
            labelValidationIcon.style.display = 'inline';
        } else {
            labelValidation.classList.remove('chat-closed');
            labelValidationIcon.style.display = 'none';
        }

        if (validForm) {
            $('#save-button-action').prop("disabled",false);
            $('#update-button-action').prop("disabled",false);
        } else {
            $('#save-button-action').prop("disabled",true);
            $('#update-button-action').prop("disabled",true);
        }
    }

    updateUI();

});

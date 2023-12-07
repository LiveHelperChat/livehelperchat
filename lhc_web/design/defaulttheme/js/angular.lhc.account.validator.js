/*lhcAppControllers.controller('LHCAccountValidator',['$scope','$http','$location','$rootScope', function($scope, $http, $location, $rootScope) {

    var that = this;

    this.requiredGroups = [];
    this.validRequiredGroups = false;
    this.validForm = false;

    this.validateGroups = function() {
        this.validForm = this.validRequiredGroups = Object.keys(this.requiredGroups).filter(function(key) { return that.requiredGroups[key] !== false; }).length > 0;
    }

}]);*/

$( document ).ready(function() {

    let validForm = false;
    let validRequiredGroups = false;
    let requiredGroups = [];

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

    /*function validateGroups() {
        validForm = validRequiredGroups = Object.keys(requiredGroups).filter(function(key) { return requiredGroups[key] !== false; }).length > 0;
    }*/

    /*$('#buttons-submit-group').
    $('#save-button-action').
    $('#update-button-action').
    $('#label-validation-groups').
    $('#label-validation-icon').
    $('#group-required-holder').*/

});

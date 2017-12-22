lhcAppControllers.controller('AutoResponderCtrl',['$scope','$http','$location','$rootScope', '$log', function($scope, $http, $location, $rootScope, $log) {

    this.languages = [];

    var that = this;

    this.move = function(element, offset) {
        index = that.languages.indexOf(element);
        newIndex = index + offset;
        if (newIndex > -1 && newIndex < that.languages.length){
            removedElement = that.languages.splice(index, 1)[0];
            that.languages.splice(newIndex, 0, removedElement)
        }
    };

    this.addLanguage = function() {
        that.languages.push({
            'message' : '',
            'fallback_message' : '',
            'languages' : []});
        setTimeout(function () {
            $('#autoresponder-tabs li:eq(' + (that.languages.length+3) + ') a').tab('show');
        },250);
    };

    this.toggleSelection =  function toggleSelection(lang, language) {
        var idx = lang.languages.indexOf(language);
        // Is currently selected
        if (idx > -1) {
            lang.languages.splice(idx, 1);
        } else {
            lang.languages.push(language);
        }
    };

    this.deleteLanguage = function(field) {
        that.languages.splice(that.languages.indexOf(field),1);
        $('#autoresponder-tabs a:first').tab('show');
    };

    this.moveLeftField = function(field) {
        that.move(field,-1);
    }

    this.moveRightField = function(field) {
        that.move(field,1);
    }


}]);
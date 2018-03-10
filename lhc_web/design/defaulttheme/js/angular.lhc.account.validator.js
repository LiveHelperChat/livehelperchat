lhcAppControllers.controller('LHCAccountValidator',['$scope','$http','$location','$rootScope', function($scope, $http, $location, $rootScope) {

    var that = this;

    this.requiredGroups = [];
    this.validRequiredGroups = false;
    this.validForm = false;

    this.validateGroups = function() {
        this.validForm = this.validRequiredGroups = Object.keys(this.requiredGroups).filter(function(key) { return that.requiredGroups[key] !== false; }).length > 0;
    }

}]);
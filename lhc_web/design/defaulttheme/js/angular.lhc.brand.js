lhcAppControllers.controller('BrandCtrl',['$scope','$http','$location','$rootScope', '$log','$window', function($scope, $http, $location, $rootScope, $log, $window) {

    this.members = [];
    this.departments = [];
    this.departmentsRoles = {};
    var that = this;

    this.makeid = function(length) {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < length; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    this.setConditions = function() {
        this.members = $window['replaceConditions'];
        this.departments = $window['replaceDepartments'];
        this.departmentsRoles = $window['replaceDepartmentsRoles'];
    }

    this.deleteMember = function (element) {
        if (confirm('Are you sure?')){
            this.members.splice(this.members.indexOf(element), 1);

            if (typeof this.departmentsRoles[element] !== "undefined") {
                delete this.departmentsRoles[element]
            }
        }
    }

    this.addMember = function(element) {
        this.addDepartment(element)
    }

    this.addDepartment = function(combination) {
        if (this.members.indexOf(combination.dep_id) == -1) {
            this.members.push(combination.dep_id);
            this.departmentsRoles[combination.dep_id] = "";
        }
    }

    setTimeout(function(){
        $('.btn-block-department').makeDropdown();
    },1500);


}]);
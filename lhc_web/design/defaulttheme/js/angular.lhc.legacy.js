var phonecatApp = angular.module('lhcApp', [
    'lhcAppServices',
    'lhcAppControllers'
]);

var services = angular.module('lhcAppServices', []);
var lhcAppControllers = angular.module('lhcAppControllers', ["checklist-model"]);

lhcAppControllers.config(['$compileProvider', function ($compileProvider) {
    $compileProvider.debugInfoEnabled(false);
}]);

lhcAppControllers.run(['$http', function ($http) {
    $http.defaults.headers.common['X-CSRFToken'] = confLH.csrf_token;
}]);

angular.element(document).ready(function(){
    var element = angular.element(document.querySelector("form"));
    element.triggerHandler("$destroy");
});

services.factory('LiveHelperChatFactory', ['$http','$q',function ($http, $q) {
    return this;
}]);

lhcAppControllers.controller('LiveHelperChatCtrl',['$scope','$http','$location','$rootScope', '$log','$interval','LiveHelperChatFactory', function($scope, $http, $location, $rootScope, $log, $interval,LiveHelperChatFactory) {

}]);
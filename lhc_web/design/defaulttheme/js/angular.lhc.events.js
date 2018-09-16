

lhcAppControllers.controller('ProactiveEventsFormCtrl',['$scope','$http','$location','$rootScope', '$log', function($scope, $http, $location, $rootScope, $log) {
	this.events = [];	
	
	var that = this;
			    
	this.addEvent = function() {
		that.events.push({
			'event_type' : 0,
			'min_number' : 1,
			'during_seconds' : 0,
			'id' : 'temp'+new Date().getTime()
		});
	};
	
	this.deleteField = function(field) {								
		that.events.splice(that.events.indexOf(field),1);
	};
		
}]);

lhcAppControllers.requires.push('ngSanitize');

lhcAppControllers.controller('ProactiveDesignerCtrl',['$scope','$http','$location','$rootScope', '$log','$sce', function($scope, $http, $location, $rootScope, $log, $sce) {

    this.replaceArray = [];
	this.plainHtml = '';
	this.plainStyle = '';
    var that = this;


    $scope.$watch('ngModelAbstractInput_mobile_html', function(newVal,oldVal) {
        angular.forEach(that.replaceArray, function(item) {
            newVal = newVal.replace(item.id,item.val);
        });
        that.plainHtml = newVal;
    });

    $scope.$watch('ngModelAbstractInput_mobile_style', function(newVal,oldVal) {
        angular.forEach(that.replaceArray, function(item) {
            newVal = newVal.replace(item.id,item.val);
        });
        that.plainStyle = newVal.replace(/\n/g, "").replace(/\r/g, "");
    });

}]);
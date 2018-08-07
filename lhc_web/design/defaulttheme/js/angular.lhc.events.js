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
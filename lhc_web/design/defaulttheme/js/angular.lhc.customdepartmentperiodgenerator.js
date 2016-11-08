lhcAppControllers.controller('DepartmentCustomPeriodCtrl',['$scope', function($scope) {
		
		this.customPeriods = [];

		var that = this;
		    
		this.add = function() {
			that.customPeriods.push({
				'date_from' : that.custom_date_from,
				'date_to' 	: that.custom_date_to,
				'start_hour' : that.custom_start_hour,
				'start_hour_min' : that.custom_start_hour_min,
				'end_hour' 	: that.custom_end_hour,
				'end_hour_min' 	: that.custom_end_hour_min
			});
		};
		
		this.delete = function(period) {
			that.customPeriods.splice(that.customPeriods.indexOf(period),1);
		};
}]);
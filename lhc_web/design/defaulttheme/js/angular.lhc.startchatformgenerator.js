lhcAppControllers.controller('StartChatFormCtrl',['$scope','$http','$location','$rootScope', '$log', function($scope, $http, $location, $rootScope, $log) {
		
		this.startchatfields = [];	
		this.size = 6;
		this.fieldtype = 'text';
		this.visibility = 'all';
		this.showcondition = 'always';

		var that = this;
				
		this.move = function(element, offset) {
		  index = that.startchatfields.indexOf(element);
		  newIndex = index + offset;		  
		  if (newIndex > -1 && newIndex < that.startchatfields.length){		
		    removedElement = that.startchatfields.splice(index, 1)[0];		  		  
		    that.startchatfields.splice(newIndex, 0, removedElement)
		  }
		};
		    
		this.addField = function() {
			that.startchatfields.push({
				'fieldname' : that.fieldname,
				'defaultvalue' : that.defaultvalue,
				'fieldtype' : that.fieldtype,
				'visibility' : that.visibility,
				'fieldidentifier' : that.fieldidentifier,
				'size' : that.size,
				'isrequired' : that.isrequired,
				'options' : that.options,
				'showcondition' : that.showcondition
			});
		};
		
		this.deleteField = function(field) {								
			that.startchatfields.splice(that.startchatfields.indexOf(field),1);
		};	
		
		this.moveLeftField = function(field) {
			that.move(field,-1);
		}
		
		this.moveRightField = function(field) {
			that.move(field,1);
		}		
		
		
}]);
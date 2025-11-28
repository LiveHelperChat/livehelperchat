lhcAppControllers.controller('StartChatFormCtrl',['$scope','$http','$location','$rootScope', '$log','$window', function($scope, $http, $location, $rootScope, $log, $window) {
		
		this.startchatfields = [];
		this.size = 6;
		this.fieldtype = 'text';
		this.visibility = 'all';
		this.showcondition = 'always';
		this.priority = 50;

		var that = this;

        this.setStartChatFields = function() {
            that.startchatfields = $window['startChatFields'];
        }

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
				'hide_prefilled' : that.hide_prefilled,
				'options' : that.options,
				'showcondition' : that.showcondition,
				'priority' : that.priority,
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
lhcAppControllers.controller('StartChatFormURLCtrl',['$scope','$http','$location','$rootScope', '$log', '$window', function($scope, $http, $location, $rootScope, $log,  $window) {

		this.startchatfields = [];

		var that = this;
				
		this.move = function(element, offset) {
		  index = that.startchatfields.indexOf(element);
		  newIndex = index + offset;
		  if (newIndex > -1 && newIndex < that.startchatfields.length){
		    removedElement = that.startchatfields.splice(index, 1)[0];
		    that.startchatfields.splice(newIndex, 0, removedElement)
		  }
		};

        this.setStartFields = function() {
            that.startchatfields = $window['startChatFieldsURL'];
        }

		this.addField = function() {
			that.startchatfields.push({
				'fieldname' : that.fieldname,
				'fieldidentifier' : that.fieldidentifier
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
lhcAppControllers.controller('StartChatFormPreconditions',['$scope','$http','$location','$rootScope', '$log', '$window', function($scope, $http, $location, $rootScope, $log,  $window) {

		this.conditions = {
            'online': [],
            'offline': [],
            'disable': [],
            'maintenance': [],
            'offline_enabled' : false,
            'disable_enabled' : false,
            'disable_message' : ''
        };

        this.deleteElement = function (element,list) {
            if (confirm('Are you sure?')){
                list.splice(list.indexOf(element), 1);
            }
        }

        var that = this;

        this.move = function(element, list, offset) {
            index = list.indexOf(element);
            newIndex = index + offset;
            if (newIndex > -1 && newIndex < list.length){
                removedElement = list.splice(index, 1)[0];
                list.splice(newIndex, 0, removedElement)
            }
        };

        this.setStartFields = function() {
            that.conditions = $window['startChatFieldsConditions'];
        }

		this.addField = function(list) {
			if (!that.conditions[list]) {
				that.conditions[list] = [];
			}
			that.conditions[list].push({field:"", logic: "and", comparator : "eq"});
		};

        this.moveUp = function(field,list) {
            that.move(field,list,-1);
        }

        this.moveDown = function(field,list) {
            that.move(field,list,1);
        }
}]);
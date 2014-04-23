services.factory('OnlineUsersFactory', ['$http','$q',function ($http, $q) {
	
	this.loadOnlineUsers = function(params){
		var deferred = $q.defer();		
		$http.get(WWW_DIR_JAVASCRIPT + 'chat/onlineusers/(method)/ajax/(timeout)/'+params.timeout + (params.department > 0 ? '/(department)/' + params.department : '' )).success(function(data) {
			 deferred.resolve(data);		
		});		
		return deferred.promise;
	};
	
	this.deleteOnlineUser = function(params){
		var deferred = $q.defer();		
		$http.post(WWW_DIR_JAVASCRIPT +'chat/onlineusers/(deletevisitor)/'+params.user_id + '/(csfr)/'+confLH.csrf_token).success(function(data) {
			 deferred.resolve(data);		
		});		
		return deferred.promise;
	};
	
	return this;
}]);

lhcAppControllers.controller('OnlineCtrl',['$scope','$http','$location','$rootScope', '$log','$interval','OnlineUsersFactory', function($scope, $http, $location, $rootScope, $log, $interval, OnlineUsersFactory) {
	  	  		
		var timeoutId;		
		this.onlineusers = [];
		$scope.onlineusersGrouped = [];
		this.updateTimeout = 10;
		this.userTimeout = 3600;	
		this.department = 0;	
		this.predicate = 'last_visit';
		this.reverse = true;
		$scope.groupByField = 'none';
		
		var that = this;
				
		function sortOn( collection, name ) {			 
            collection.sort(
                function( a, b ) {
                    if ( a[ name ] <= b[ name ] ) {
                        return( -1 );
                    }
                    return( 1 );
                }
            );
        };
        
        // http://www.bennadel.com/blog/2456-grouping-nested-ngrepeat-lists-in-angularjs.htm
        // I group the friends list on the given property.
        $scope.groupBy = function( attribute ) {
            // First, reset the groups.
            $scope.onlineusersGrouped = [];
            
            // Now, sort the collection of friend on the
            // grouping-property. This just makes it easier
            // to split the collection.
            sortOn( that.onlineusers, attribute );

            // I determine which group we are currently in.
            var groupValue = "_INVALID_GROUP_VALUE_";

            // As we loop over each friend, add it to the
            // current group - we'll create a NEW group every
            // time we come across a new attribute value.
            for ( var i = 0 ; i < that.onlineusers.length ; i++ ) {
                var friend = that.onlineusers[ i ];
                // Should we create a new group?
                if ( friend[ attribute ] !== groupValue ) {

                    var group = {
                        label: friend[ attribute ],
                        id: i,
                        ou: []
                    };

                    groupValue = group.label;
                    $scope.onlineusersGrouped.push( group );
                }
                // Add the friend to the currently active
                // grouping.
                group.ou.push( friend );
            }
        };
        
		this.updateList = function(){
			OnlineUsersFactory.loadOnlineUsers({timeout: that.userTimeout,department : that.department}).then(function(data){
				that.onlineusers = data;
				if ($scope.groupByField != 'none') {
					$scope.groupBy($scope.groupByField);
				} else {
					$scope.onlineusersGrouped = [];
					$scope.onlineusersGrouped.push({label:'',id:0,ou:that.onlineusers});
				}				
			});
		};
								
		timeoutId = $interval(function() {		
			that.updateList();			
		},this.updateTimeout * 1000);
		
		$scope.$watch('online.updateTimeout', function(newVal,oldVal) {       
			if (newVal != oldVal) {			
				$interval.cancel(timeoutId);				
				timeoutId = $interval(function() {		
					that.updateList();			
				},newVal*1000);				
			}
		});
		
		$scope.$watch('online.userTimeout + online.department + groupByField', function(newVal,oldVal) { 
				that.updateList();			
		});
		
		this.showOnlineUserInfo = function(user_id) {
			$.colorbox({onComplete:function(){$(document).foundation('section', 'reflow');},width:'550px',href:WWW_DIR_JAVASCRIPT+'chat/getonlineuserinfo/'+user_id});
		};
		
		this.previewChat = function(ou) {
			if (ou.chat_id > 0 && ou.can_view_chat == 1) {
				$.colorbox({'iframe':true, height:'500px', width:'500px', href: WWW_DIR_JAVASCRIPT + 'chat/previewchat/'+ou.chat_id});
			}
		};
		
		this.sendMessage = function(user_id) {
			$.colorbox({'iframe':true,height:'500px',width:'500px', href:WWW_DIR_JAVASCRIPT+'chat/sendnotice/'+user_id});
		};
		
		this.deleteUser = function(user,q) {
			if (confirm(q)){
				 OnlineUsersFactory.deleteOnlineUser({user_id: user.id}).then(function(data){						
					that.onlineusers.splice(that.onlineusers.indexOf(user),1);	
				 });								 
			};
		};
		
		$scope.$on('$destroy', function disableController() {
			$interval.cancel(timeoutId);	
		});			  
}]);
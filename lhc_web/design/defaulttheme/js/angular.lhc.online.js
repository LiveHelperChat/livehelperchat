services.factory('OnlineUsersFactory', ['$http','$q',function ($http, $q) {
	
	this.loadOnlineUsers = function(params){
		var deferred = $q.defer();		
		$http.get(WWW_DIR_JAVASCRIPT + 'chat/onlineusers/(method)/ajax/(timeout)/'+params.timeout + (params.department > 0 ? '/(department)/' + params.department : '' ) + (params.max_rows > 0 ? '/(maxrows)/' + params.max_rows : '' )).success(function(data) {
			 deferred.resolve(data);		
		});		
		return deferred.promise;
	};
	
	this.deleteOnlineUser = function(params){
		var deferred = $q.defer();		
		$http.post(WWW_DIR_JAVASCRIPT +'chat/onlineusers/(deletevisitor)/'+params.user_id + '/(csfr)/'+confLH.csrf_token).success(function(data) {
			if (typeof data.error_url !== 'undefined') {
				document.location = data.error_url;
			} else {
				deferred.resolve(data);
			}		
		});		
		return deferred.promise;
	};
	
	return this;
}]);

lhcAppControllers.controller('OnlineCtrl',['$scope','$http','$location','$rootScope', '$log','$interval','OnlineUsersFactory', function($scope, $http, $location, $rootScope, $log, $interval, OnlineUsersFactory) {
	  	  		
		var timeoutId;		
		this.onlineusers = [];	
		this.onlineusersPreviousID = [];
		$scope.onlineusersGrouped = [];
		this.updateTimeout = 10;
		this.userTimeout = 3600;	
		this.maxRows = 50;	
		this.department = 0;	
		this.predicate = 'last_visit';
		this.reverse = true;
		this.wasInitiated = false;

    	this.forbiddenVisitors = false;
		this.soundEnabled = false;
		this.notificationEnabled = false;
		
		
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
			
			if (lhinst.disableSync == true || that.forbiddenVisitors == true ) {
				return;
			}
			
			OnlineUsersFactory.loadOnlineUsers({timeout: that.userTimeout,department : that.department, max_rows : that.maxRows}).then(function(data){
							
				that.onlineusers = data;
				if ($scope.groupByField != 'none') {
					$scope.groupBy($scope.groupByField);
				} else {
					$scope.onlineusersGrouped = [];
					$scope.onlineusersGrouped.push({label:'',id:0,ou:that.onlineusers});
				};	
					
				if (that.notificationEnabled || that.soundEnabled) {
					var hasNewVisitors = false;
					var newVisitors = [];				
					angular.forEach(that.onlineusers, function(value, key) {
								
						var hasValue = true;
						if (that.onlineusersPreviousID.indexOf(value.id) == -1){
							hasValue = false;
							that.onlineusersPreviousID.push(value.id);
						}
						
						if (that.wasInitiated == true && hasValue == false) {
							hasNewVisitors = true;	 
							newVisitors.push(value);						
						}
					});
					
					if (hasNewVisitors == true ) {
							if (that.soundEnabled && Modernizr.audio){
					    	    var audio = new Audio();
					            audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/new_visitor.ogg' :
					                        Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/new_visitor.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/new_visitor.wav';
					            audio.load();
					            setTimeout(function(){
					            	audio.play();
					            },500); 
					        };
					        
					        if (that.notificationEnabled && (window.webkitNotifications || window.Notification)) {
					        	
					        	angular.forEach(newVisitors, function(value, key) {
					        		if (window.webkitNotifications) {
								    	  var havePermission = window.webkitNotifications.checkPermission();
								    	  if (havePermission == 0) {
								    	    // 0 is PERMISSION_ALLOWED
								    	    var notification = window.webkitNotifications.createNotification(
								    	      WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION + '/notification.png',
								    	      value.ip+(value.user_country_name != '' ? ', '+value.user_country_name : ''),
								    	      (value.page_title != '' ? value.page_title+"\n-----\n" : '')+(value.referrer != '' ? value.referrer+"\n-----\n" : '')
								    	    );
								    	    notification.onclick = function () {							    	    	
								    	        notification.cancel();
								    	    };
								    	    notification.show();
								    	    
								    	    setTimeout(function(){
								    	    	 notification.cancel();
								    	    },15000);							    	    
								    	  }
							    	  } else if(window.Notification) {
							    		  if (window.Notification.permission == 'granted') {
								  				var notification = new Notification(value.ip+(value.user_country_name != '' ? ', '+value.user_country_name : ''), { icon: WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION + '/notification.png', body: (value.page_title != '' ? value.page_title+"\n-----\n" : '')+(value.referrer != '' ? value.referrer+"\n-----\n" : '') });
								  				notification.onclick = function () {								    	    	
									    	        notification.close();
									    	    };
									    	    setTimeout(function(){
									    	    	 notification.close();
									    	    },15000);								    	    
								    	   }
							    	  }
					        	});
					        	
							};
					};				
									
					that.wasInitiated = true;	
					
					if (that.onlineusersPreviousID.length > 100) {
						that.wasInitiated = false;
						that.onlineusersPreviousID = [];
					};
					
				};
			});
		};
								
		timeoutId = $interval(function() {
			if (that.forbiddenVisitors == false) {
				that.updateList();
			} else {
                $interval.cancel(timeoutId);
			}
		},this.updateTimeout * 1000);
		
		$scope.$watch('online.updateTimeout', function(newVal,oldVal) {       
			if (newVal != oldVal) {			
				$interval.cancel(timeoutId);				
				timeoutId = $interval(function() {		
					that.updateList();			
				},newVal*1000);	
				
				lhinst.changeUserSettingsIndifferent('oupdate_timeout',newVal);
			};
		});
		
		$scope.$watch('online.userTimeout',function(newVal,oldVal){
			if (newVal != oldVal) {	
				lhinst.changeUserSettingsIndifferent('ouser_timeout',newVal);
			}
		});
		
		$scope.$watch('online.department',function(newVal,oldVal){
			if (newVal != oldVal) {	
				lhinst.changeUserSettingsIndifferent('o_department',newVal);
			}
		});
		
		$scope.$watch('online.maxRows',function(newVal,oldVal){
			if (newVal != oldVal) {	
				lhinst.changeUserSettingsIndifferent('omax_rows',newVal);
			}
		});
		
		$scope.$watch('groupByField',function(newVal,oldVal){
			if (newVal != oldVal) {	
				lhinst.changeUserSettingsIndifferent('ogroup_by',newVal);
			}
		});
		
		$scope.$watch('online.userTimeout + online.department + online.maxRows + groupByField', function(newVal,oldVal) { 
				that.updateList();			
		});
				
		this.showOnlineUserInfo = function(user_id) {
			lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/getonlineuserinfo/'+user_id});
		};
		
		this.previewChat = function(ou) {
			if (ou.chat_id > 0 && ou.can_view_chat == 1) {	
				lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/previewchat/'+ou.chat_id});
			}
		};
		
		this.sendMessage = function(user_id) {
			lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/sendnotice/'+user_id});
		};
		
		this.deleteUser = function(user,q) {
			if (confirm(q)){
				 OnlineUsersFactory.deleteOnlineUser({user_id: user.id}).then(function(data){						
					that.onlineusers.splice(that.onlineusers.indexOf(user),1);	
				 });								 
			};
		};
		
		this.disableNewUserBNotif = function() {		
			that.notificationEnabled = !that.notificationEnabled;		
			lhinst.changeUserSettings('new_user_bn',that.notificationEnabled == true ? 1 : 0);
		};
		
		this.disableNewUserSound = function() {
			that.soundEnabled = !that.soundEnabled;
			lhinst.changeUserSettings('new_user_sound',that.soundEnabled == true ? 1 : 0);
		};
		
		$scope.$on('$destroy', function disableController() {
			$interval.cancel(timeoutId);	
		});
		
}]);
services.factory('OnlineUsersFactory', ['$http','$q',function ($http, $q) {
	
	this.loadOnlineUsers = function(params){
		var deferred = $q.defer();
		$http.get(WWW_DIR_JAVASCRIPT + 'chat/onlineusers/(method)/ajax/(timeout)/'+params.timeout +  (params.department_dpgroups.length > 0 ? '/(department_dpgroups)/' + params.department_dpgroups.join('/') : '' ) + (params.department.length > 0 ? '/(department)/' + params.department.join('/') : '' ) + (params.max_rows > 0 ? '/(maxrows)/' + params.max_rows : '' ) + (params.country != '' ? '/(country)/' + params.country : '' ) + (params.time_on_site != '' ? '/(timeonsite)/' + encodeURIComponent(params.time_on_site) : '') ).then(function(data) {
			 deferred.resolve(data.data);
		});		
		return deferred.promise;
	};

    this.setLocalSettings = function(attr,val) {
        var deferred = $q.defer();
        $http.post(WWW_DIR_JAVASCRIPT + 'front/settings',{"attr":attr,"val":val}).then(function(data) {
            deferred.resolve(data.data);
        },function(internalError){
            deferred.reject(typeof internalError.status !== 'undefined' ? '['+internalError.status+']' : '[0]');
        });
        return deferred.promise;
    };

	this.deleteOnlineUser = function(params){
		var deferred = $q.defer();		
		$http.post(WWW_DIR_JAVASCRIPT +'chat/onlineusers/(deletevisitor)/'+params.user_id + '/(csfr)/'+confLH.csrf_token).then(function(data) {
			if (typeof data.error_url !== 'undefined') {
				document.location = data.data.error_url;
			} else {
				deferred.resolve(data.data);
			}		
		});		
		return deferred.promise;
	};
	
	return this;
}]);

lhcAppControllers.controller('OnlineCtrl',['$scope','$http','$location','$rootScope', '$log','$interval', '$window', 'OnlineUsersFactory', function($scope, $http, $location, $rootScope, $log, $interval, $window, OnlineUsersFactory) {
	  	  		
		var timeoutId;		
		this.onlineusers = [];
		this.onlineusers_tt = 0;
		this.onlineusersPreviousID = [];
		$scope.onlineusersGrouped = [];
		this.updateTimeout = '10';
		this.userTimeout = '3600';
		this.maxRows = '50';
		this.department = [];
		this.department_dpgroups = [];
		this.country = 'none';
		this.predicate = 'last_visit';
		this.time_on_site = '';
		this.reverse = true;
		this.wasInitiated = false;
		this.online_connected = false;

        // Attributes filters
        this.attrf_key_1 = '';
        this.attrf_val_1 = '';

        this.attrf_key_2 = '';
        this.attrf_val_2 = '';

        this.attrf_key_3 = '';
        this.attrf_val_3 = '';

        this.attrf_key_4 = '';
        this.attrf_val_4 = '';

        this.attrf_key_5 = '';
        this.attrf_val_5 = '';

    	this.forbiddenVisitors = false;
		this.soundEnabled = false;
		this.notificationEnabled = false;
		this.lastSyncSkipped = false;

		var that = this;

        if (that.forbiddenVisitors !== true) {
            ['onlineusers','widget-onvisitors','map','dashboard'].forEach(function(item){
                var itemTab = document.getElementById(item);
                if (itemTab !== null) {
                    var observer = new MutationObserver(function (event) {
                        if (itemTab.classList.contains('active') && that.lastSyncSkipped == true) {
                            that.updateList();
                        }
                    })
                    observer.observe(itemTab, {
                        attributes: true,
                        attributeFilter: ['class'],
                        childList: false,
                        characterData: false
                    })
                }
            });
        }

		$scope.groupByField = 'none';

				
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

			// Check is online visitors tab is active or widget is expanded
            // otherwise also do not sync and save resources
            var activeList = false;

            var itemTab = document.getElementById('onlineusers');
			if (itemTab !== null) {
                activeList = itemTab.classList.contains('active');
            }

			if (activeList == false){
                var mapItem = document.getElementById('map');
                if (mapItem !== null) {
                    activeList = mapItem.classList.contains('active');
                }
            }

			if (activeList == false) {
                var widgetItem = document.getElementById('widget-onvisitors-body');
                if (widgetItem !== null) {
                    var dashboardTab = document.getElementById('dashboard');
                    if (dashboardTab !== null && dashboardTab.classList.contains('active')) {
                        activeList = true;
                    }
                }
            }

			if (activeList === false) {
                that.lastSyncSkipped = true;
			    return;
            }

			that.lastSyncSkipped = false;

			OnlineUsersFactory.loadOnlineUsers({department_dpgroups: that.department_dpgroups,timeout: that.userTimeout, time_on_site : that.time_on_site, department : that.department, country: that.country, max_rows : that.maxRows}).then(function(data){
							
				that.onlineusers = data.list;
				that.onlineusers_tt = data.tt;
				if ($scope.groupByField != 'none') {
					$scope.groupBy($scope.groupByField);
				} else {
					$scope.onlineusersGrouped = [];
					$scope.onlineusersGrouped.push({label:'',id:0,ou:that.onlineusers});
				};

                ee.emitEvent('chatAdminSyncOnlineVisitors', [data.list]);

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
		
		$scope.$watch('online.country',function(newVal,oldVal){
			if (newVal != oldVal) {	
				lhinst.changeUserSettingsIndifferent('ocountry',newVal);
			}
		});

		$scope.$watch('online.time_on_site',function(newVal,oldVal){
			if (newVal != oldVal) {
				lhinst.changeUserSettingsIndifferent('otime_on_site',newVal == '' ? 'none' : newVal);
			}
		});
		
		$scope.$watch('online.maxRows',function(newVal,oldVal){
			if (newVal != oldVal) {	
				lhinst.changeUserSettingsIndifferent('omax_rows',newVal);
			}
		});

        $scope.$watch('online.attrf_key_1',function(newVal,oldVal){
			if (newVal != oldVal) {
				lhinst.changeUserSettingsIndifferent('oattrf_key_1',newVal);
			}
		});

        $scope.$watch('online.attrf_val_1',function(newVal,oldVal){
			if (newVal != oldVal) {
				lhinst.changeUserSettingsIndifferent('oattrf_val_1',newVal);
			}
		});

        $scope.$watch('online.attrf_key_2',function(newVal,oldVal){
			if (newVal != oldVal) {
				lhinst.changeUserSettingsIndifferent('oattrf_key_2',newVal);
			}
		});

        $scope.$watch('online.attrf_val_2',function(newVal,oldVal){
			if (newVal != oldVal) {
				lhinst.changeUserSettingsIndifferent('oattrf_val_2',newVal);
			}
		});

        $scope.$watch('online.attrf_key_3',function(newVal,oldVal){
			if (newVal != oldVal) {
				lhinst.changeUserSettingsIndifferent('oattrf_key_3',newVal);
			}
		});

        $scope.$watch('online.attrf_val_3',function(newVal,oldVal){
			if (newVal != oldVal) {
				lhinst.changeUserSettingsIndifferent('oattrf_val_3',newVal);
			}
		});

        $scope.$watch('online.attrf_key_4',function(newVal,oldVal){
			if (newVal != oldVal) {
				lhinst.changeUserSettingsIndifferent('oattrf_key_4',newVal);
			}
		});

        $scope.$watch('online.attrf_val_4',function(newVal,oldVal){
			if (newVal != oldVal) {
				lhinst.changeUserSettingsIndifferent('oattrf_val_4',newVal);
			}
		});

        $scope.$watch('online.attrf_key_5',function(newVal,oldVal){
			if (newVal != oldVal) {
				lhinst.changeUserSettingsIndifferent('oattrf_key_5',newVal);
			}
		});

        $scope.$watch('online.attrf_val_5',function(newVal,oldVal){
			if (newVal != oldVal) {
				lhinst.changeUserSettingsIndifferent('oattrf_val_5',newVal);
			}
		});

        this.departmentChanged = function(listId) {
            OnlineUsersFactory.setLocalSettings(listId+'_online', this[listId]);
        };

        this.productChanged = function(listId) {
            OnlineUsersFactory.setLocalSettings(listId+'_online', this[listId]);
        };

		$scope.$watch('groupByField',function(newVal,oldVal){
			if (newVal != oldVal) {	
				lhinst.changeUserSettingsIndifferent('ogroup_by',newVal);
			}
		});
		
		$scope.$watch('online.userTimeout + online.department + online.department_dpgroups + online.maxRows + groupByField + online.country + online.time_on_site + online.attrf_key_1 + online.attrf_val_1 + online.attrf_key_2 + online.attrf_val_2 + online.attrf_key_3 + online.attrf_val_3 + online.attrf_key_4 + online.attrf_val_4 + online.attrf_key_5 + online.attrf_val_5', function(newVal,oldVal) {
			setTimeout(function(){
				that.updateList();
			},500);						
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

		this.showConnected = function() {
			that.online_connected = !that.online_connected;
            lhinst.changeUserSettings('online_connected',that.online_connected == true ? 1 : 0);
		};
		
		this.disableNewUserSound = function() {
			that.soundEnabled = !that.soundEnabled;
			lhinst.changeUserSettings('new_user_sound',that.soundEnabled == true ? 1 : 0);
		};

        this.initController = function() {
            if ($window['onlineAttributeFilter']) {
                this.attrf_key_1 = $window['onlineAttributeFilter']['attrf_key_1'];
                this.attrf_val_1 = $window['onlineAttributeFilter']['attrf_val_1'];
                this.attrf_key_2 = $window['onlineAttributeFilter']['attrf_key_2'];
                this.attrf_val_2 = $window['onlineAttributeFilter']['attrf_val_2'];
                this.attrf_key_3 = $window['onlineAttributeFilter']['attrf_key_3'];
                this.attrf_val_3 = $window['onlineAttributeFilter']['attrf_val_3'];
                this.attrf_key_4 = $window['onlineAttributeFilter']['attrf_key_4'];
                this.attrf_val_4 = $window['onlineAttributeFilter']['attrf_val_4'];
                this.attrf_key_5 = $window['onlineAttributeFilter']['attrf_key_5'];
                this.attrf_val_5 = $window['onlineAttributeFilter']['attrf_val_5'];
            }
        }

        $scope.$on('$destroy', function disableController() {
            $interval.cancel(timeoutId);
        });

}]);
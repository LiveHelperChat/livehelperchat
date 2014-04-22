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
		this.updateTimeout = 10;
		this.userTimeout = 3600;	
		this.department = 0;	
		this.predicate = 'last_visit';
		this.reverse = true;
		
		var that = this;
			
		this.updateList = function(){
			OnlineUsersFactory.loadOnlineUsers({timeout: that.userTimeout,department : that.department}).then(function(data){
				that.onlineusers = data;				
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
		
		$scope.$watch('online.userTimeout + online.department', function(newVal,oldVal) { 
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
		
		this.updateList();	  
}]);
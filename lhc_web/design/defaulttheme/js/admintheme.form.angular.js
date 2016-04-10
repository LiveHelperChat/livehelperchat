services.factory('IClickToCallFormFactory', ['$http','$q',function ($http, $q) {
	
	this.getHash = function(){
		var deferred = $q.defer();		
		$http.get(WWW_DIR_JAVASCRIPT + 'theme/gethash').success(function(data) {			
			deferred.resolve(data);			 		 
		}).error(function(){
			deferred.reject('error');
		});
		
		return deferred.promise;
	};
	
	this.deleteResource = function(id, resource, itemId){
		var deferred = $q.defer();		
		$http.get(WWW_DIR_JAVASCRIPT + 'theme/deleteresource/'+id+'/'+resource+'/'+itemId).success(function(data) {			
			 deferred.resolve(data);			 		 
		}).error(function(){
			deferred.reject('error');
		});
		
		return deferred.promise;
	};
	
	return this;
}]);

lhcAppControllers.controller('IClickToCallFormGenerator',['$scope','$http','$location','$rootScope', '$log','IClickToCallFormFactory', function($scope, $http, $location, $rootScope, $log,IClickToCallFormFactory) {
		
		this.staticResources = {};			
		this.staticJSResources = {};			
		this.staticCSSResources = {};			
		
		// Resource name in static resource
		this.static_content_name = '';
		this.static_js_content_name = '';
		this.static_css_content_name = '';
		
		var that = this;
		
		this.addStaticResource = function() {
			IClickToCallFormFactory.getHash().then(function(data){	
				that.staticResources[data.hash] = {
						'name' : that.static_content_name,
						'hash' : data.hash
				};
				that.static_content_name = '';
			});
		}
		
		this.addStaticJSResource = function() {
			IClickToCallFormFactory.getHash().then(function(data){	
				that.staticJSResources[data.hash] = {
						'name' : that.static_js_content_name,
						'hash' : data.hash
				};
				that.static_js_content_name = '';
			});
		}
									
		this.addStaticCSSResource = function() {
			IClickToCallFormFactory.getHash().then(function(data){	
				that.staticCSSResources[data.hash] = {
					'name' : that.static_css_content_name,
					'hash' : data.hash
				};
				that.static_css_content_name = '';
			});
		}
		
		this.deleteStaticResource = function(id,field) {								
			var removedItem = that.staticResources[field.hash];			
			IClickToCallFormFactory.deleteResource(id,'static_content',removedItem.hash).then(function(data){	
				
			});			
			delete that.staticResources[field.hash];
		};
		
		this.deleteStaticJSResource = function(id,field) {								
			var removedItem = that.staticJSResources[field.hash];			
			IClickToCallFormFactory.deleteResource(id,'static_js_content',removedItem.hash).then(function(data){	
				
			});			
			delete that.staticJSResources[field.hash];
		};

		this.deleteStaticCSSResource = function(id,field) {								
			var removedItem = that.staticCSSResources[field.hash];			
			IClickToCallFormFactory.deleteResource(id,'static_css_content',removedItem.hash).then(function(data){	
				
			});			
			delete that.staticCSSResources[field.hash];
		};
		
}]);
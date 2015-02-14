var revealM = {
		cancelcolorbox : function() {
			$('#myModal').foundation('reveal', 'close');
		},
		
		initializeModal : function(selector) {		
			var modelSelector = selector != undefined ? selector : 'myModal';
			if ($('#'+modelSelector).size() == 0) {
				$('body').prepend('<div id="'+modelSelector+'" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"></div>');		
			};	
		},
		
		revealModal : function(params) {
			/*if (closePrevious !== undefined && closePrevious === true){
				$('#myModal').remove(); 	
				$('.reveal-modal-bg').remove();	
			}*/
			
			revealM.initializeModal('myModal');
			if (typeof params['iframe'] === 'undefined') {				
				jQuery.get(params['url'], function(data){				
					$('#myModal').html(data).modal('show')	
				});
			} else {
				$('#myModal').html('<div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-info-sign"></span></h4></div><div class="modal-body"><iframe src="'+params['url']+'" frameborder="0" style="width:100%" height="'+params['height']+'" /></div></div></div>').modal('show')	;
			}
		}		
};

module.exports = revealM;
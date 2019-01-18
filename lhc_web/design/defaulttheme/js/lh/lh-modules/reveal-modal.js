var revealM = {
		cancelcolorbox : function() {
			$('#myModal').foundation('reveal', 'close');
		},
		
		initializeModal : function(selector) {		
			var modelSelector = selector != undefined ? selector : 'myModal';
			if ($('#'+modelSelector).size() == 0) {
				
				var prependTo = null;
				if ($('#widget-layout').size() == 0) {
					prependTo = $('body');
				} else {
					prependTo = $('#widget-layout');
				};
				prependTo.prepend('<div id="'+modelSelector+'" style="padding-right:0px !important;" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"></div>');		
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
					
					if (typeof params['showcallback'] !== 'undefined') {
						$('#myModal').on('shown.bs.modal',params['showcallback']);
					}
					
					if (typeof params['hidecallback'] !== 'undefined') {
						$('#myModal').on('hide.bs.modal',params['hidecallback']);
					}
					
					$('#myModal').html(data).modal('show')	
				});
			} else {
				var header = '';
				var prependeBody = '';
				if (typeof params['hideheader'] === 'undefined') {	
					header = '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="myModalLabel"><span class="material-icons">info</span>'+(typeof params['title'] === 'undefined' ? '' : params['title'])+'</h4></div>';
				} else {
					prependeBody = (typeof params['title'] === 'undefined' ? '' : '<b>'+params['title']+'</b>') + '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
				}
				var additionalModalBody = typeof params['modalbodyclass'] === 'undefined' ? '' : ' '+params['modalbodyclass'];
				
				$('#myModal').html('<div class="modal-dialog modal-lg"><div class="modal-content">'+header+'<div class="modal-body'+additionalModalBody+'">'+prependeBody+'<iframe src="'+params['url']+'" frameborder="0" style="width:100%" height="'+params['height']+'" /></div></div></div>').modal('show')	;
			}
		}		
};

module.exports = revealM;
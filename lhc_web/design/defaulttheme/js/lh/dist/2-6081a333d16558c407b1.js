(window.webpackJsonp=window.webpackJsonp||[]).push([[2],{2:function(a,l){var o={cancelcolorbox:function(){$("#myModal").foundation("reveal","close")},initializeModal:function(a){var l=null!=a?a:"myModal";if(0==$("#"+l).length){(0==$("#widget-layout").length?$("body"):$("#widget-layout")).prepend('<div id="'+l+'" style="padding-right:0px !important;" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"></div>')}},revealModal:function(a){var l=!1;$("body").hasClass("modal-open")?(l=!0,$("#myModal").modal("hide")):$("#myModal").modal("dispose"),o.initializeModal("myModal");var d={show:!0};if(a.mparams,Object.assign(d,a.mparams),void 0===a.iframe)void 0!==a.loadmethod&&"post"==a.loadmethod?jQuery.post(a.url,a.datapost,(function(o){!1===l?(void 0!==a.showcallback&&$("#myModal").on("shown.bs.modal",a.showcallback),void 0!==a.hidecallback&&$("#myModal").on("hide.bs.modal",a.hidecallback),$("#myModal").html(o).modal("show")):setTimeout((function(){$("#myModal").modal("dispose"),void 0!==a.showcallback&&$("#myModal").on("shown.bs.modal",a.showcallback),void 0!==a.hidecallback&&$("#myModal").on("hide.bs.modal",a.hidecallback),$("#myModal").html(o).modal(d)}),500)})):jQuery.get(a.url,(function(o){!1===l?(void 0!==a.showcallback&&$("#myModal").on("shown.bs.modal",a.showcallback),void 0!==a.hidecallback&&$("#myModal").on("hide.bs.modal",a.hidecallback),$("#myModal").html(o).modal(d)):setTimeout((function(){$("#myModal").modal("dispose"),void 0!==a.showcallback&&$("#myModal").on("shown.bs.modal",a.showcallback),void 0!==a.hidecallback&&$("#myModal").on("hide.bs.modal",a.hidecallback),$("#myModal").html(o).modal(d)}),500)}));else{var i="",e="";void 0===a.hideheader?i='<div class="modal-header"><h4 class="modal-title" id="myModalLabel"><span class="material-icons">info</span>'+(void 0===a.title?"":a.title)+'</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>':e=(void 0===a.title?"":"<b>"+a.title+"</b>")+'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';var s=void 0===a.modalbodyclass?"":" "+a.modalbodyclass;$("#myModal").html('<div class="modal-dialog modal-xl"><div class="modal-content">'+i+'<div class="modal-body'+s+'">'+e+'<iframe src="'+a.url+'" frameborder="0" style="width:100%" height="'+a.height+'" /></div></div></div>').modal(d),void 0!==a.showcallback&&$("#myModal").on("shown.bs.modal",a.showcallback),void 0!==a.hidecallback&&$("#myModal").on("hide.bs.modal",a.hidecallback)}$("#myModal").draggabilly({handle:".modal-header"})}};a.exports=o}}]);
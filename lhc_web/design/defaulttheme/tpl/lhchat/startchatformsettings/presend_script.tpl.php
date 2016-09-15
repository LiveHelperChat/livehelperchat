<script>
  var visitorTitle =  <?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Me'),ENT_QUOTES))?>;
  $( "<?php echo $formIdentifier?>" ).submit(function() {         	
     if ($('#messagesBlock > .message-row.response').size() == 0 && $(this).attr('key-up-started') != 1) {
     	jQuery('<div/>', {
		    'class': 'message-row response',					   
		    text: $('#id_Question').val()
		}).appendTo('#messagesBlock').prepend('<span class="usr-tit vis-tit">'+<?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Me'),ENT_QUOTES))?>+'</span>');
     	$('#messagesBlock').animate({ scrollTop: $('#messagesBlock').prop('scrollHeight') }, 1000);
	}
 });

  <?php if($fullheight) : ?>
      var fullHeightFunction = function() {
          var bodyHeight = $(document.body).outerHeight();
          var messageBlockHeight = $('#messages').outerHeight();
          var widgetLayoutHeight = $('#widget-layout').outerHeight();

          var messageBlockFullHeight = bodyHeight - (widgetLayoutHeight - messageBlockHeight) - 10;

          $('#messagesBlockWrap').height(messageBlockFullHeight);
          $('#messagesBlock').css('max-height',messageBlockFullHeight);
          setTimeout(fullHeightFunction, 200);
      };
      setTimeout(fullHeightFunction, 200);
    <?php endif; ?>
</script>
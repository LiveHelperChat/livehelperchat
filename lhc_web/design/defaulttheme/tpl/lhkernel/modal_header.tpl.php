<div class="modal-dialog modal-<?php isset($modalSize) ? print $modalSize : print 'lg'?>">
    <div class="modal-content">
      <div class="modal-header<?php (isset($modalHeaderClass)) ? print ' '.$modalHeaderClass : ''?>">
        <?php if (!isset($hideModalClose)) : ?><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php endif;?>
        <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span><?php isset($modalHeaderTitle) ? print $modalHeaderTitle : ''?></h4>
      </div>
      <div class="modal-body<?php (isset($modalBodyClass)) ? print ' '.$modalBodyClass : ''?>">
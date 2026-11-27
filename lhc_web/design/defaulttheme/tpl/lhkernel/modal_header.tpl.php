<div class="modal-dialog modal-dialog-scrollable modal-<?php isset($modalSize) ? print $modalSize : print 'lg'?>">
    <div class="modal-content">
      <div ng-non-bindable class="modal-header<?php (isset($modalHeaderClass)) ? print ' '.$modalHeaderClass : ''?>">
        <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span><?php isset($modalHeaderTitle) ? print $modalHeaderTitle : ''?></h4>
        <?php if (!isset($hideModalClose)) : ?><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button><?php endif;?>
      </div>
      <div class="modal-body<?php (isset($modalBodyClass)) ? print ' '.$modalBodyClass : ''?>">
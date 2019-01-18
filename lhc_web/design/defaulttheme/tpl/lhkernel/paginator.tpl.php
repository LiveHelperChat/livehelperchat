<?php if (isset($pages) && $pages->num_pages > 1) : ?>


<nav>
    
    <div class="found-total float-right"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','Page')?> <?php echo $pages->current_page?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','of')?> <?php echo $pages->num_pages?>, <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','Found')?> - <?php echo $pages->items_total?></div>

    <ul class="pagination paginator-lhc">

    <?php if ($pages->current_page != 1) : ?>
        <li class="arrow page-item"><a class="page-link previous" href="<?php echo $pages->serverURL,$pages->prev_page,$pages->querystring?>">&laquo;</a></li>
    <?php endif;?>

    <?php if ($pages->num_pages > 10) :
    $needNoBolder = false;

    if ($pages->range[0] > 1) :
    $i = 1;
    $pageURL = $i > 1 ? '/(page)/'.$i : '';
    $needNoBolder = true;
    	if ($i == $pages->current_page) : ?>
    	   <li class="active no-b page-item"><a class="page-link" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','Go to page')?> <?php echo $i?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','of')?> <?php echo $pages->num_pages?>" href="#"><?php echo $i?></a></li>
    	<?php else : ?>
           <li class="page-item"><a class="page-link no-b" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','Go to page')?> <?php echo $i?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','of')?> <?php echo $pages->num_pages?>" href="<?php echo $pages->serverURL,$pageURL,$pages->querystring?>"><?php echo $i?></a></li>
        <?php endif;
        ?> <li class="page-item"><a class="page-link" href="#">...</a></li> <?php
        endif;

        for($i=$pages->range[0];$i<=$pages->lastArrayNumber;$i++) :
        if ($i > 0) :
        $pageURL = $i > 1 ? '/(page)/'.$i : '';
        $noBolderClass = ($i == 1 || $needNoBolder == true) ? ' no-b' : '';
        $needNoBolder = false;

				if ($i == $pages->current_page): ?>

				<li class="page-item active<?php echo $noBolderClass?>"><a class="page-link" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','Go to page')?> <?php echo $i?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','of')?> <?php echo $pages->num_pages?>"  href="#"><?php echo $i?></a></li>

			    <?php else : ?>

			    <li class="page-item"><a class="page-link <?php echo $noBolderClass?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','Go to page')?> <?php echo $i?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','of')?> <?php echo $pages->num_pages?>" href="<?php echo $pages->serverURL,$pageURL,$pages->querystring?>"><?php echo $i?></a></li>

    <?php endif;endif;endfor;
    if ($pages->lastArrayNumber < $pages->num_pages) :
    $i = $pages->num_pages;
    $pageURL = $i > 1 ? '/(page)/'.$i : '';

    ?> <li class="page-item"><a class="page-link" href="#">...</a></li> <?php if ($i == $pages->current_page) : ?>

			<li class="active page-item"><a class="page-link" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','Go to page')?> <?php echo $i?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','of')?> <?php echo $pages->num_pages?>" href="#"><?php echo $i?></a></li>

    <?php  else : ?>

            <li class="page-item"><a class="page-link no-b" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','Go to page')?> <?php echo $i?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','of')?> <?php echo $pages->num_pages?>" href="<?php echo $pages->serverURL,$pageURL,$pages->querystring?>"><?php echo $i?></a></li>

   <?php endif; endif;

   else : for ($i=1;$i<=$pages->num_pages;$i++) :
   $noBolderClass = ($i == 1) ? ' no-b' : '';
   $pageURL = $i > 1 ? '/(page)/'.$i : '';
		if ($i == $pages->current_page) :?>
            <li class="active page-item<?php echo $noBolderClass?>"><a class="page-link" href="#"><?php echo $i?></a></li>
		<?php else : ?>
		    <li class="page-item"><a class="paginate page-link" href="<?php echo $pages->serverURL,$pageURL,$pages->querystring;?>"><?php echo $i?></a></li>
    <?php endif; endfor; endif;

    if ($pages->current_page != $pages->num_pages): ?>

    <li class="arrow page-item"><a class="next page-link" href="<?php echo $pages->serverURL,'/(page)/',$pages->next_page,$pages->querystring?>">&raquo;</a></li>

    <?php endif;?>

    </ul>
    

    </nav>
<?php endif;?>
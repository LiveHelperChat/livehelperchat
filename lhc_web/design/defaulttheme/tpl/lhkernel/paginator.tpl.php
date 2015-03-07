<?php if (isset($pages) && $pages->num_pages > 1) : ?>


<nav>
<ul class="pagination paginator-lhc">

    <?php if ($pages->current_page != 1) : ?>
        <li class="arrow"><a class="previous" href="<?php echo $pages->serverURL,$pages->prev_page,$pages->querystring?>">&laquo;</a></li>
    <?php endif;?>

    <?php if ($pages->num_pages > 10) :
    $needNoBolder = false;

    if ($pages->range[0] > 1) :
    $i = 1;
    $pageURL = $i > 1 ? '/(page)/'.$i : '';
    $needNoBolder = true;
    	if ($i == $pages->current_page) : ?>
    	   <li class="current no-b"><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','Go to page')?> <?php echo $i?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','of')?> <?php echo $pages->num_pages?>" href="#"><?php echo $i?></a></li>
    	<?php else : ?>
           <li><a class="no-b" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','Go to page')?> <?php echo $i?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','of')?> <?php echo $pages->num_pages?>" href="<?php echo $pages->serverURL,$pageURL,$pages->querystring?>"><?php echo $i?></a></li>
        <?php endif;
        ?> <li><a href="#">...</a></li> <?php
        endif;

        for($i=$pages->range[0];$i<=$pages->lastArrayNumber;$i++) :
        if ($i > 0) :
        $pageURL = $i > 1 ? '/(page)/'.$i : '';
        $noBolderClass = ($i == 1 || $needNoBolder == true) ? ' no-b' : '';
        $needNoBolder = false;

				if ($i == $pages->current_page): ?>

				<li class="active<?php echo $noBolderClass?>"><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','Go to page')?> <?php echo $i?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','of')?> <?php echo $pages->num_pages?>"  href="#"><?php echo $i?></a></li>

			    <?php else : ?>

			    <li><a class="<?php echo $noBolderClass?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','Go to page')?> <?php echo $i?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','of')?> <?php echo $pages->num_pages?>" href="<?php echo $pages->serverURL,$pageURL,$pages->querystring?>"><?php echo $i?></a></li>

    <?php endif;endif;endfor;
    if ($pages->lastArrayNumber < $pages->num_pages) :
    $i = $pages->num_pages;
    $pageURL = $i > 1 ? '/(page)/'.$i : '';

    ?> <li><a href="#">...</a></li> <?php if ($i == $pages->current_page) : ?>

			<li class="active"><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','Go to page')?> <?php echo $i?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','of')?> <?php echo $pages->num_pages?>" href="#"><?php echo $i?></a></li>

    <?php  else : ?>

            <li><a class="no-b" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','Go to page')?> <?php echo $i?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','of')?> <?php echo $pages->num_pages?>" href="<?php echo $pages->serverURL,$pageURL,$pages->querystring?>"><?php echo $i?></a></li>

   <?php endif; endif;

   else : for ($i=1;$i<=$pages->num_pages;$i++) :
   $noBolderClass = ($i == 1) ? ' no-b' : '';
   $pageURL = $i > 1 ? '/(page)/'.$i : '';
		if ($i == $pages->current_page) :?>
            <li class="active<?php echo $noBolderClass?>"><a href="#"><?php echo $i?></a></li>
		<?php else : ?>
		    <li><a class="paginate" href="<?php echo $pages->serverURL,$pageURL,$pages->querystring;?>"><?php echo $i?></a></li>
    <?php endif; endfor; endif;

    if ($pages->current_page != $pages->num_pages): ?>

    <li class="arrow"><a class="next" href="<?php echo $pages->serverURL,'/(page)/',$pages->next_page,$pages->querystring?>">&raquo;</a></li>

    <?php endif;?>

    </ul>
    
    <div class="found-total pull-right"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','Page')?> <?php echo $pages->current_page?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','of')?> <?php echo $pages->num_pages?>, <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('core/paginator','Found')?> - <?php echo $pages->items_total?></div>
    
    </nav>
<?php endif;?>
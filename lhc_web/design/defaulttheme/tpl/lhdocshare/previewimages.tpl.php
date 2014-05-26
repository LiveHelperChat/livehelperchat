<h2><?php echo htmlspecialchars($docshare)?></h2>
    
<div class="row" style="max-height:400px;overflow:hidden;overflow-y:auto;">
	<?php foreach ($docshare->pages_pdf_url as $counter => $img) : ?>
		<div class="columns small-4">
		<img src="<?php echo $img?>" alt="">
			<div class="text-center fs12">Page - <?php echo $counter+1;?></div>
		</div>
	<?php endforeach;?>
</div>

<a class="close-reveal-modal">&#215;</a>
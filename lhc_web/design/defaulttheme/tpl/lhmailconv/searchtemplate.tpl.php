<?php foreach ($items as $item) : ?>
<div>
    <h5 class="pb-0 mb-0"><a class="use-template" data-id="<?php echo $item->id?>" href="#"><?php echo htmlspecialchars($item->name)?></a></h5>
    <p class="pt-1 mt-0"><?php echo htmlspecialchars(mb_substr($item->template_plain,0,250))?>...</p>
    <input type="hidden" id="use-template-value-<?php echo $item->id?>" value="<?php echo htmlspecialchars($item->template_html)?>" /></input>
</div>
<?php endforeach; ?>

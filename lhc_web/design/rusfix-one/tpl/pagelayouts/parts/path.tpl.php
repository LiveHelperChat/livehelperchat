<?php if (isset($Result['path'])) :
$pathElementCount = count($Result['path'])-1;
if ($pathElementCount >= 0):
?>
<ul  class="breadcrumbs" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
<li><a rel="home" itemprop="url" href="<?php echo erLhcoreClassDesign::baseurl()?>"><span itemprop="title"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Home')?></span></a></li>
<?php foreach ($Result['path'] as $key => $pathItem) : if (isset($pathItem['url']) && $pathElementCount != $key) { ?><li itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="<?php echo $pathItem['url']?>" itemprop="url"><span itemprop="title"><?php echo htmlspecialchars($pathItem['title'])?></span></a></li><?php } else { ?><li itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><?php echo htmlspecialchars($pathItem['title'])?></span></li><?php }; ?><?php endforeach; ?>
</ul><?php endif; ?>
<?php endif;?>
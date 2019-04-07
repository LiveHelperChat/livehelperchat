<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>

<?php $menuItems = array(); ?>
<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_chat_actions.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_online_users.tpl.php'));?>
       
<?php if (!empty($menuItems)) : ?>
<li class="nav-item">
        <a href="#" class="nav-link"><i class="material-icons">chat</i><span class="nav-link-text"><?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/chat/chat_title.tpl.php'));?></span><i class="material-icons arrow">&#xf142</i></a>
        <ul class="nav nav-second-level">       
            <?php foreach ($menuItems as $menuItem) : ?>
                <li class="nav-item"><a class="nav-link" href="<?php echo $menuItem['href']?>" <?php if (isset($menuItem['onclick'])) : ?>onclick="<?php echo $menuItem['onclick']?>"<?php endif;?>><?php if (isset($menuItem['iclass'])) : ?><i class="material-icons"><?php echo $menuItem['iclass']?></i><?php endif;?><span class="nav-link-text"><?php echo $menuItem['text']?></span></a></li>
            <?php endforeach;?>
	   </ul>
</li>
<?php endif; ?>

<?php endif;?>
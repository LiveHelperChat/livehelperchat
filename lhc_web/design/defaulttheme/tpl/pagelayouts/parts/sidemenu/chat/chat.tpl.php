<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>

<?php $menuItems = array(); ?>
<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_chat_actions.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_online_users.tpl.php'));?>
       
<?php if (!empty($menuItems)) : ?>
<li>
        <a href="#"><i class="material-icons">chat</i><?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/chat/chat_title.tpl.php'));?><i class="material-icons arrow">chevron_right</i></a>
        <ul class="nav nav-second-level">       
            <?php foreach ($menuItems as $menuItem) : ?>
                <li><a href="<?php echo $menuItem['href']?>" <?php if (isset($menuItem['onclick'])) : ?>onclick="<?php echo $menuItem['onclick']?>"<?php endif;?>><?php if (isset($menuItem['iclass'])) : ?><i class="material-icons"><?php echo $menuItem['iclass']?></i><?php endif;?><?php echo $menuItem['text']?></a></li>
            <?php endforeach;?>
	   </ul>
</li>
<?php endif; ?>

<?php endif;?>
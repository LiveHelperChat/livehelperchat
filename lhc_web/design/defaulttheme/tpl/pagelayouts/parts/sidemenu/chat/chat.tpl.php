<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>

<?php $menuItems = array(); ?>
<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_chat_actions.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_online_users.tpl.php'));?>
       
<?php if (!empty($menuItems)) : ?>
<li>
        <a href="#"><i class="icon-chat"></i><?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/chat/chat_title.tpl.php'));?><span class="glyphicon arrow"></span></a>
        <ul class="nav nav-second-level">       
            <?php foreach ($menuItems as $menuItem) : ?>
                <li class="li-icon"><a href="<?php echo $menuItem['href']?>" <?php if (isset($menuItem['onclick'])) : ?>onclick="<?php echo $menuItem['onclick']?>"<?php endif;?>><?php if (isset($menuItem['iclass'])) : ?><i class="<?php echo $menuItem['iclass']?>"></i><?php endif;?><?php echo $menuItem['text']?></a></li>
            <?php endforeach;?>
	   </ul>
</li>
<?php endif; ?>

<?php endif;?>
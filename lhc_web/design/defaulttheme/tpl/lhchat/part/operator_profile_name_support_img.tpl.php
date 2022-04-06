<?php if ($user->has_photo) : ?>
<img width="48" height="48" src="<?php echo $user->photo_path?>" alt="<?php echo htmlspecialchars($user->name_support)?>" />
<?php else : ?>
<img width="48" height="48" src="<?php echo erLhcoreClassSystem::getHost()?><?php echo erLhcoreClassDesign::baseurldirect('widgetrestapi/avatar')?>/<?php echo htmlspecialchars($user->avatar)?>" alt="<?php echo htmlspecialchars($user->name_support)?>" />
<?php endif; ?>

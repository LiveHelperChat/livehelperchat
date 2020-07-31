<div><strong><?php echo htmlspecialchars($user->name_support)?></strong><?php if (isset($extraMessage)) : ?><i><?php echo $extraMessage;?></i><?php elseif ($user->job_title != '') : ?>,<i class="pl-1"><?php echo htmlspecialchars($user->job_title);?></i>
    <?php endif;?>
</div>
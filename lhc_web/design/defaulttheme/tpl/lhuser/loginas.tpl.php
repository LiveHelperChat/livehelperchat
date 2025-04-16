<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','You will be redirected and logged in as a different user in a few moments...')?></p>

<script>
setTimeout(function(){
    document.location = <?php echo json_encode(erLhcoreClassDesign::baseurl('user/loginasuser') . $login_as_link);?>;
},3000);
</script>
var lh_inst  = {

    urlopen : "http://<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/startchat')?>",
    
    windowname : "startchatwindow",
    
    lh_openchatWindow : function() {
        <?php if ($click == 'internal') : ?>
        alert('internal');
        <?php else : ?>
        window.open(this.urlopen+'?URLReferer='+escape(document.location),this.windowname,"menubar=1,resizable=1,width=500,height=520");	      
        <?php endif; ?>
        return false;
    }
};

<?php if (erLhcoreClassChat::isOnline() === true) { ?>
document.write('<p><a href="javascript:void()" onclick="lh_inst.lh_openchatWindow()"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus',"Live help is online...")?></a></p>');
<?php } else { ?>
document.write('<p><a href="javascript:void()" onclick="lh_inst.lh_openchatWindow()"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus',"Live help is offline...")?></a></p>');
<?php }  exit; ?>
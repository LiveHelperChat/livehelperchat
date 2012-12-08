function lh_openchatWindow(url,windowname)
{
    window.open(url+'?URLReferer='+escape(document.location),windowname,"menubar=1,resizable=1,width=450,height=420");	      
    return false;
}
var urlopen= "http://<?=$_SERVER['HTTP_HOST'].erLhcoreClassDesign::baseurl('chat/startchat')?>";
var windowname = "startchatwindow";

<?php if (erLhcoreClassChat::isOnline() === true) { ?>
document.write('<p><a href="javascript:void()" onclick="lh_openchatWindow(urlopen,windowname)"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus',"Live help is online...")?></a></p>');
<? } else { ?>
document.write('<p><a href="javascript:void()" onclick="lh_openchatWindow(urlopen,windowname)"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus',"Live help is offline...")?></a></p>');
<? }  exit; ?>
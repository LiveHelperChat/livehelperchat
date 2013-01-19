<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>
<link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('css/chat.css');?>" /> 

<script type="text/javascript">
/** Reset pointer from parent window **/
function ChatUnload()
{
                window.opener.lhinst.deleteChatNewWindow();
}
</script>
</head>
<body onunload="ChatUnload()">
<div id="container" class="no-left-column no-right-column">

	<div id="bodcont" class="float-break">			
		<div id="middcont">
			<div id="mainartcont">
			<?php echo $Result['content']; ?>			
			</div>
		</div>		
	</div>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer.tpl.php'));?>
</div>

</body>

</html>
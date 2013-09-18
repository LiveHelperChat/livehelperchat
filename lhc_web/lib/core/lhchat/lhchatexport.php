<?php

class erLhcoreClassChatExport {

	public function chatExportXML(erLhcoreClassModelChat $chat) {
		$tpl = new erLhcoreClassTemplate('lhexport/xml.tpl.php');
		$tpl->set('chat', $chat);
		return $tpl->fetch();
	}

	public function chatExportJSON(erLhcoreClassModelChat $chat) {
		$tpl = new erLhcoreClassTemplate('lhexport/json.tpl.php');
		$tpl->set('chat', $chat);
		return $tpl->fetch();
	}
}

?>
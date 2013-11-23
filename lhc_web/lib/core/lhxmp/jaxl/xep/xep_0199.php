<?php 

require_once JAXL_CWD.'/xmpp/xmpp_xep.php';

define('NS_XMPP_PING', 'urn:xmpp:ping');

class XEP_0199 extends XMPPXep {
	
	//
	// abstract method
	//
	
	public function init() {
		return array(
			'on_auth_success' => 'on_auth_success',
			'on_get_iq' => 'on_xmpp_ping'
		);
	}
	
	//
	// api methods
	//
	
	public function get_ping_pkt() {
		$attrs = array(
			'type'=>'get',
			'from'=>$this->jaxl->full_jid->to_string(),
			'to'=>$this->jaxl->full_jid->domain
		);
		
		return $this->jaxl->get_iq_pkt(
			$attrs,
			new JAXLXml('ping', NS_XMPP_PING)
		);
	}
	
	public function ping() {
		$this->jaxl->send($this->get_ping_pkt());
	}
	
	//
	// event callbacks
	//
	
	public function on_auth_success() {
		JAXLLoop::$clock->call_fun_periodic(30 * pow(10,6), array(&$this, 'ping'));
	}
	
	public function on_xmpp_ping($stanza) {
		if($stanza->exists('ping', NS_XMPP_PING)) {
			$stanza->type = "result";
			$stanza->to = $stanza->from;
			$stanza->from = $this->jaxl->full_jid->to_string();
			$this->jaxl->send($stanza);
		}
	}
	
}

?>

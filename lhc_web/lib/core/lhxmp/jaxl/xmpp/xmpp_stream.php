<?php
/**
* Jaxl (Jabber XMPP Library)
*
* Copyright (c) 2009-2012, Abhinav Singh <me@abhinavsingh.com>.
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions
* are met:
*
* * Redistributions of source code must retain the above copyright
* notice, this list of conditions and the following disclaimer.
*
* * Redistributions in binary form must reproduce the above copyright
* notice, this list of conditions and the following disclaimer in
* the documentation and/or other materials provided with the
* distribution.
*
* * Neither the name of Abhinav Singh nor the names of his
* contributors may be used to endorse or promote products derived
* from this software without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
* COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRIC
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.
*
*/

require_once JAXL_CWD.'/core/jaxl_fsm.php';
require_once JAXL_CWD.'/core/jaxl_xml.php';
require_once JAXL_CWD.'/core/jaxl_xml_stream.php';
require_once JAXL_CWD.'/core/jaxl_util.php';
require_once JAXL_CWD.'/core/jaxl_socket_client.php';

require_once JAXL_CWD.'/xmpp/xmpp_nss.php';
require_once JAXL_CWD.'/xmpp/xmpp_jid.php';
require_once JAXL_CWD.'/xmpp/xmpp_msg.php';
require_once JAXL_CWD.'/xmpp/xmpp_pres.php';
require_once JAXL_CWD.'/xmpp/xmpp_iq.php';

/**
 * 
 * Enter description here ...
 * @author abhinavsingh
 *
 */
abstract class XMPPStream extends JAXLFsm {
	
	// jid with binding resource value
	public $full_jid = null;
	
	// input parameters
	public $jid = null;
	public $pass = null;
	public $resource = null;
	public $force_tls = false;
	
	// underlying socket/bosh and xml stream ref
	protected $trans = null;
	protected $xml = null;
	
	// stanza id
	protected $last_id = 0;
	
	//
	// abstract methods
	//
	
	abstract public function handle_stream_start($stanza);
	abstract public function handle_auth_mechs($stanza, $mechs);
	abstract public function handle_auth_success();
	abstract public function handle_auth_failure($reason);
	abstract public function handle_iq($stanza);
	abstract public function handle_presence($stanza);
	abstract public function handle_message($stanza);
	abstract public function handle_other($event, $args);
	
	//
	// public api
	// 
	
	public function __construct($transport, $jid, $pass=null, $resource=null, $force_tls=false) {
		$this->jid = $jid;
		$this->pass = $pass;
		$this->resource = $resource ? $resource : md5(time());
		$this->force_tls = $force_tls;
		
		$this->trans = $transport;
		$this->xml = new JAXLXmlStream();
		
		$this->trans->set_callback(array(&$this->xml, "parse"));
		$this->xml->set_callback(array(&$this, "start_cb"), array(&$this, "end_cb"), array(&$this, "stanza_cb"));
		
		parent::__construct("setup");
	}
	
	public function __destruct() {
		//_debug("cleaning up xmpp stream...");
	}
	
	public function handle_invalid_state($r) {
		_error("got invalid return value from state handler '".$this->state."', sending end stream...");
		$this->send_end_stream();
		$this->state = "logged_out";
		_notice("state handler '".$this->state."' returned ".serialize($r).", kindly report this to developers");
	}
	
	public function send($stanza) {
		$this->trans->send($stanza->to_string());
	}
	
	public function send_raw($data) {
		$this->trans->send($data);
	}
	
	//
	// pkt creation utilities
	//
	
	public function get_start_stream($jid) {
		$xml = '<stream:stream xmlns:stream="'.NS_XMPP.'" version="1.0" ';
		//if(isset($jid->bare)) $xml .= 'from="'.$jid->bare.'" ';
		if(isset($jid->domain)) $xml .= 'to="'.$jid->domain.'" ';
		$xml .= 'xmlns="'.NS_JABBER_CLIENT.'" xml:lang="en" xmlns:xml="'.NS_XML.'">';
		return $xml;
	}
	
	public function get_end_stream() {
		return '</stream:stream>';
	}
	
	public function get_starttls_pkt() {
		$stanza = new JAXLXml('starttls', NS_TLS);
		return $stanza;
	}
	
	public function get_compress_pkt($method) {
		$stanza = new JAXLXml('compress', NS_COMPRESSION_PROTOCOL);
		$stanza->c('method')->t($method);
		return $stanza;
	}
	
	// someday this all needs to go inside jaxl_sasl_auth
	public function get_auth_pkt($mechanism, $user, $pass) {
		$stanza = new JAXLXml('auth', NS_SASL, array('mechanism'=>$mechanism));
		
		switch($mechanism) {
			case 'PLAIN':
			case 'X-OAUTH2':
				$stanza->t(base64_encode("\x00".$user."\x00".$pass));
				break;
			case 'DIGEST-MD5':
				break;
			case 'CRAM-MD5':
				break;
			case 'SCRAM-SHA-1':
				// client first message always starts with n, y or p for GS2 extensibility
				$stanza->t(base64_encode("n,,n=".$user.",r=".JAXLUtil::get_nonce(false)));
				break;
			case 'ANONYMOUS':
				break;
			case 'EXTERNAL':
				// If no password, then we are probably doing certificate auth, so follow RFC 6120 form and pass '='.
				if(strlen($pass) == 0)
					$stanza->t('=');
				break;
			default:
				break;
		}
		
		return $stanza;
	}
	
	public function get_challenge_response_pkt($challenge) {
		$stanza = new JAXLXml('response', NS_SASL);
		$decoded = $this->explode_data(base64_decode($challenge));
		
		if(!isset($decoded['rspauth'])) {
			_debug("calculating response to challenge");
			$stanza->t($this->get_challenge_response($decoded));
		}
		
		return $stanza;
	}
	
	public function get_challenge_response($decoded) {
		$response = array();
		$nc = '00000001';
		
		if(!isset($decoded['digest-uri']))
			$decoded['digest-uri'] = 'xmpp/'.$this->jid->domain;
		
		$decoded['cnonce'] = base64_encode(JAXLUtil::get_nonce());
		
		if(isset($decoded['qop']) && $decoded['qop'] != 'auth' && strpos($decoded['qop'], 'auth') !== false)
			$decoded['qop'] = 'auth';
		
		$data = array_merge($decoded, array('nc'=>$nc));
			
		$response = array(
			'username'=> $this->jid->node,
			'response' => $this->encrypt_password($data, $this->jid->node, $this->pass),
			'charset' => 'utf-8',
			'nc' => $nc,
			'qop' => 'auth'
		);
			
		foreach(array('nonce', 'digest-uri', 'realm', 'cnonce') as $key)
			if(isset($decoded[$key]))
				$response[$key] = $decoded[$key];
		
		return base64_encode($this->implode_data($response));
	}
	
	public function get_bind_pkt($resource) {
		$stanza = new JAXLXml('bind', NS_BIND);
		$stanza->c('resource')->t($resource);
		return $this->get_iq_pkt(array(
			'type' => 'set'
		), $stanza);
	}
	
	public function get_session_pkt() {
		$stanza = new JAXLXml('session', NS_SESSION);
		return $this->get_iq_pkt(array(
			'type' => 'set'
		), $stanza);
	}
	
	public function get_msg_pkt($attrs, $body=null, $thread=null, $subject=null, $payload=null) {
		$msg = new XMPPMsg($attrs, $body, $thread, $subject);
		if(!$msg->id) $msg->id = $this->get_id();
		if($payload) $msg->cnode($payload);
		return $msg;
	}
	
	public function get_pres_pkt($attrs, $status=null, $show=null, $priority=null, $payload=null) {
		$pres = new XMPPPres($attrs, $status, $show, $priority);
		if(!$pres->id) $pres->id = $this->get_id();
		if($payload) $pres->cnode($payload);
		return $pres;
	}
	
	public function get_iq_pkt($attrs, $payload) {
		$iq = new XMPPIq($attrs);
		if(!$iq->id) $iq->id = $this->get_id();
		if($payload) $iq->cnode($payload);
		return $iq;
	}
	
	public function get_id() {
		++$this->last_id;
		return dechex($this->last_id);
	}
	
	public function explode_data($data) {
		$data = explode(',', $data);
		$pairs = array();
		$key = false;
		
		foreach($data as $pair) {
			$dd = strpos($pair, '=');
			if($dd) {
				$key = trim(substr($pair, 0, $dd));
				$pairs[$key] = trim(trim(substr($pair, $dd + 1)), '"');
			}
			else if(strpos(strrev(trim($pair)), '"') === 0 && $key) {
				$pairs[$key] .= ',' . trim(trim($pair), '"');
				continue;
			}
		}
		
		return $pairs;
	}
	
	public function implode_data($data) {
		$return = array();
		foreach($data as $key => $value) $return[] = $key . '="' . $value . '"';
		return implode(',', $return);
	}
	
	public function encrypt_password($data, $user, $pass) {
		foreach(array('realm', 'cnonce', 'digest-uri') as $key)
			if(!isset($data[$key])) 
				$data[$key] = '';
	
		$pack = md5($user.':'.$data['realm'].':'.$pass);
		
		if(isset($data['authzid'])) 
			$a1 = pack('H32',$pack).sprintf(':%s:%s:%s',$data['nonce'],$data['cnonce'],$data['authzid']);
		else 
			$a1 = pack('H32',$pack).sprintf(':%s:%s',$data['nonce'],$data['cnonce']);
		
		$a2 = 'AUTHENTICATE:'.$data['digest-uri'];
		return md5(sprintf('%s:%s:%s:%s:%s:%s', md5($a1), $data['nonce'], $data['nc'], $data['cnonce'], $data['qop'], md5($a2)));
	}
	
	//
	// socket senders
	//
	
	protected function send_start_stream($jid) {
		$this->send_raw($this->get_start_stream($jid));
	}
	
	public function send_end_stream() {
		$this->send_raw($this->get_end_stream());
	}
	
	protected function send_auth_pkt($type, $user, $pass) {
		$this->send($this->get_auth_pkt($type, $user, $pass));
	}
	
	protected function send_starttls_pkt() {
		$this->send($this->get_starttls_pkt());
	}
	
	protected function send_compress_pkt($method) {
		$this->send($this->get_compress_pkt($method));
	}
	
	protected function send_challenge_response($challenge) {
		$this->send($this->get_challenge_response_pkt($challenge));
	}
	
	protected function send_bind_pkt($resource) {
		$this->send($this->get_bind_pkt($resource));
	}
	
	protected function send_session_pkt() {
		$this->send($this->get_session_pkt());
	}
	
	private function do_connect($args) {
		$socket_path = @$args[0];
		if($this->trans->connect($socket_path)) {
			return array("connected", 1);
		}
		else {
			return array("disconnected", 0);
		}
	}
	
	//
	// fsm States
	// 
	
	public function setup($event, $args) {
		switch($event) {
			case "connect":
				return $this->do_connect($args);
				break;
			// someone else already started the stream
			// even before "connect" was called must be bosh
			case "start_cb":
				$stanza = $args[0];
				return $this->handle_stream_start($stanza);
				break;
			default:
				_debug("uncatched $event");
				//print_r($args);
				return $this->handle_other($event, $args);
				//return array("setup", 0);
				break;
		}
	}
	
	public function connected($event, $args) {
		switch($event) {
			case "start_stream":
				$this->send_start_stream($this->jid);
				return array("wait_for_stream_start", 1);
				break;
			// someone else already started the stream before us
			// even before "start_stream" was called
			// must be component
			case "start_cb":
				$stanza = $args[0];
				return $this->handle_stream_start($stanza);
				break;
			default:
				_debug("uncatched $event");
				return $this->handle_other($event, $args);
				//return array("connected", 0);
				break;
		}
	}
	
	public function disconnected($event, $args) {
		switch($event) {
			case "connect":
				return $this->do_connect($args);
				break;
			case "end_stream":
				$this->send_end_stream();
				return "logged_out";
				break;
			default:
				_debug("uncatched $event");
				return $this->handle_other($event, $args);
				//return array("disconnected", 0);
				break;
		}
	}
	
	public function wait_for_stream_start($event, $args) {
		switch($event) {
			case "start_cb":
				// TODO: save stream id and other meta info
				//_debug("stream started");
				return "wait_for_stream_features";
				break;
			default:
				_debug("uncatched $event");
				return $this->handle_other($event, $args);
				//return array("wait_for_stream_start", 0);
				break;
		}
	}
	
	// XEP-0170: Recommended Order of Stream Feature Negotiation
	public function wait_for_stream_features($event, $args) {
		switch($event) {
			case "stanza_cb":
				$stanza = $args[0];
				
				// get starttls requirements
				$starttls = $stanza->exists('starttls', NS_TLS);
				$required = $starttls ? ($this->force_tls ? true : ($starttls->exists('required') ? true : false)) : false;
				
				if($starttls && $required) {
					$this->send_starttls_pkt();
					return "wait_for_tls_result";
				}
				
				// handle auth mech
				$mechs = $stanza->exists('mechanisms', NS_SASL);
				if($mechs) {
					$new_state = $this->handle_auth_mechs($stanza, $mechs);
					return $new_state ? $new_state : "wait_for_sasl_response";
				}
				
				// post auth
				$bind = $stanza->exists('bind', NS_BIND) ? true : false;
				$sess = $stanza->exists('session', NS_SESSION) ? true : false;
				$comp = $stanza->exists('compression', NS_COMPRESSION_FEATURE) ? true : false;
				
				if($bind) {
					$this->send_bind_pkt($this->resource);
					return "wait_for_bind_response";
				}
				/*// compression not supported due to bug in php stream filters
				else if($comp) {
					$this->send_compress_pkt("zlib");
					return "wait_for_compression_result";
				}*/
				else {
					_debug("no catch");
				}
				
				break;
			default:
				_debug("uncatched $event");
				return $this->handle_other($event, $args);
				//return array("wait_for_stream_features", 0);
				break;
		}
	}
	
	public function wait_for_tls_result($event, $args) {
		switch($event) {
			case "stanza_cb":
				$stanza = $args[0];
				
				if($stanza->name == 'proceed' && $stanza->ns == NS_TLS) {
					if($this->trans->crypt()) {
						$this->xml->reset_parser();
						$this->send_start_stream($this->jid);
						return "wait_for_stream_start";
					}
					else {
						$this->handle_auth_failure("tls-negotiation-failed");
						return "logged_out";
					}
				}
				else {
					// FIXME: here
				}
				
				break;
			default:
				_debug("uncatched $event");
				return $this->handle_other($event, $args);
				//return array("wait_for_tls_result", 0);
				break;
		}
	}

	public function wait_for_compression_result($event, $args) {
		switch($event) {
			case "stanza_cb":
				$stanza = $args[0];
				
				if($stanza->name == 'compressed' && $stanza->ns == NS_COMPRESSION_PROTOCOL) {
					$this->xml->reset_parser();
					$this->trans->compress();
					$this->send_start_stream($this->jid);
					return "wait_for_stream_start";
				}
				
				break;
			default:
				_debug("uncatched $event");
				return $this->handle_other($event, $args);
				//return array("wait_for_compression_result", 0);
				break;
		}
	}
	
	public function wait_for_sasl_response($event, $args) {
		switch($event) {
			case "stanza_cb":
				$stanza = $args[0];
				
				if($stanza->name == 'failure' && $stanza->ns == NS_SASL) {
					$reason = $stanza->childrens[0]->name;
					//_debug("sasl failed with reason ".$reason."");
					$this->handle_auth_failure($reason);
					return "logged_out";
				}
				else if($stanza->name == 'challenge' && $stanza->ns == NS_SASL) {
					$challenge = $stanza->text;
					$this->send_challenge_response($challenge);
					return "wait_for_sasl_response";
				}
				else if($stanza->name == 'success' && $stanza->ns == NS_SASL) {
					$this->xml->reset_parser();
					$this->send_start_stream(@$this->jid);
					return "wait_for_stream_start";
				}
				else {
					_debug("got unhandled sasl response");
				}
				
				return "wait_for_sasl_response";
				break;
			default:
				_debug("uncatched $event");
				return $this->handle_other($event, $args);
				//return array("wait_for_sasl_response", 0);
				break;
		}
	}
	
	public function wait_for_bind_response($event, $args) {
		switch($event) {
			case "stanza_cb":
				$stanza = $args[0];
				
				// TODO: chk on id
				if($stanza->name == 'iq' && $stanza->attrs['type'] == 'result'
				&& ($jid = $stanza->exists('bind', NS_BIND)->exists('jid'))) {
					$this->full_jid = new XMPPJid($jid->text);
					$this->send_session_pkt();
					return "wait_for_session_response";
				}
				else {
					// FIXME: 
				}
				break;
			default:
				_debug("uncatched $event");
				return $this->handle_other($event, $args);
				//return array("wait_for_bind_response", 0);
				break;
		}
	}
	
	public function wait_for_session_response($event, $args) {
		switch($event) {
			case "stanza_cb":
				$this->handle_auth_success();
				return "logged_in";
				break;
			default:
				_debug("uncatched $event");
				return $this->handle_other($event, $args);
				//return array("wait_for_session_response", 0);
				break;
		}
	}
	
	public function logged_in($event, $args) {
		switch($event) {
			case "stanza_cb":
				$stanza = $args[0];
				
				// call abstract
				if($stanza->name == 'message') {
					$this->handle_message($stanza);
				}
				else if($stanza->name == 'presence') {
					$this->handle_presence($stanza);
				}
				else if($stanza->name == 'iq') {
					$this->handle_iq($stanza);
				}
				else {
					$this->handle_other($event, $args);
				}
				
				return "logged_in";
				break;
			case "end_cb":
				$this->send_end_stream();
				return "logged_out";
				break;
			case "end_stream":
				$this->send_end_stream();
				return "logged_out";
				break;
			case "disconnect":
				$this->trans->disconnect();
				return "disconnected";
				break;
			default:
				_debug("uncatched $event");
				return $this->handle_other($event, $args);
				//return array("logged_in", 0);
				break;
		}
	}
	
	public function logged_out($event, $args) {
		switch($event) {
			case "end_cb":
				$this->trans->disconnect();
				return "disconnected";
				break;
			case "end_stream":
				return "disconnected";
				break;
			case "disconnect":
				$this->trans->disconnect();
				return "disconnected";
				break;
			case "connect":
				return $this->do_connect($args);
				break;
			default:
				// exit for any other event in logged_out state
				_debug("uncatched $event");
				return $this->handle_other($event, $args);
				//return array("logged_out", 0);
				break;
		}
	}
	
}

?>

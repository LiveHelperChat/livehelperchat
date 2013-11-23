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

require_once JAXL_CWD.'/xmpp/xmpp_xep.php';

define('NS_JABBER_COMPONENT_ACCEPT', 'jabber:component:accept');

class XEP_0114 extends XMPPXep {
	
	//
	// abstract method
	//
	
	public function init() {
		return array(
			'on_connect' => 'start_stream',
			'on_stream_start' => 'start_handshake',
			'on_handshake_stanza' => 'logged_in',
			'on_error_stanza' => 'logged_out'
		);
	}
	
	//
	// event callbacks
	//
	
	public function start_stream() {
		$xml = '<stream:stream xmlns:stream="'.NS_XMPP.'" to="'.$this->jaxl->jid->to_string().'" xmlns="'.NS_JABBER_COMPONENT_ACCEPT.'">';
		$this->jaxl->send_raw($xml);
	}
	
	public function start_handshake($stanza) {
		_debug("starting component handshake");
		$id = $stanza->id;
		$hash = strtolower(sha1($id.$this->jaxl->pass));
		$stanza = new JAXLXml('handshake', null, $hash);
		$this->jaxl->send($stanza);
	}
	
	public function logged_in($stanza) {
		_debug("component handshake complete");
		$this->jaxl->handle_auth_success();
		return array("logged_in", 1);
	}
	
	public function logged_out($stanza) {
		if($stanza->name == "error" && $stanza->ns == NS_XMPP) {
			$reason = $stanza->childrens[0]->name;
			$this->jaxl->handle_auth_failure($reason);
			$this->jaxl->send_end_stream();
			return array("logged_out", 0);
		}
		else {
			_debug("uncatched stanza received in logged_out");
		}
	}
	
}

?>

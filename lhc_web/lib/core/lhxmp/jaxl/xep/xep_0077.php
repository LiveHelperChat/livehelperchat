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

define('NS_FEATURE_REGISTER', 'http://jabber.org/features/iq-register');
define('NS_INBAND_REGISTER', 'jabber:iq:register');

class XEP_0077 extends XMPPXep {
	
	//
	// abstract method
	//
	
	public function init() {
		return array(
					
		);
	}
	
	//
	// api methods
	//
	
	public function get_form_pkt($domain) {
		return $this->jaxl->get_iq_pkt(
			array('to'=>$domain, 'type'=>'get'), 
			new JAXLXml('query', NS_INBAND_REGISTER)
		);
	}
	
	public function get_form($domain) {
		$this->jaxl->send($this->get_form_pkt($domain));
	}
	
	public function set_form($domain, $form) {
		$query = new JAXLXml('query', NS_INBAND_REGISTER);
		foreach($form as $k=>$v) $query->c($k, null, array(), $v)->up();
		$pkt = $this->jaxl->get_iq_pkt(
			array('to'=>$domain, 'type'=>'set'),
			$query
		);
		$this->jaxl->send($pkt);
	}
	
}

?>

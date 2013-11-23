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

define('NS_PUBSUB', 'http://jabber.org/protocol/pubsub');

class XEP_0060 extends XMPPXep {

	//
	// abstract method
	//

	public function init() {
		return array();
	}

	//
	// api methods (entity use case)
	//
	
	//
	// api methods (subscriber use case)
	//
	
	public function get_subscribe_pkt($service, $node, $jid=null) {
		$child = new JAXLXml('pubsub', NS_PUBSUB);
		$child->c('subscribe', null, array('node'=>$node, 'jid'=>($jid ? $jid : $this->jaxl->full_jid->to_string())));
		return $this->get_iq_pkt($service, $child);
	}
	
	public function subscribe($service, $node, $jid=null) {
		$this->jaxl->send($this->get_subscribe_pkt($service, $node, $jid));
	}
	
	public function unsubscribe() {
		
	}
	
	public function get_subscription_options() {
		
	}
	
	public function set_subscription_options() {
		
	}
	
	public function get_node_items() {
		
	}
	
	//
	// api methods (publisher use case)
	//
	
	public function get_publish_item_pkt($service, $node, $item) {
		$child = new JAXLXml('pubsub', NS_PUBSUB);
		$child->c('publish', null, array('node'=>$node));
		$child->cnode($item);
		return $this->get_iq_pkt($service, $child);
	}
	
	public function publish_item($service, $node, $item) {
		$this->jaxl->send($this->get_publish_item_pkt($service, $node, $item));
	}
	
	public function delete_item() {
		
	}
	
	//
	// api methods (owner use case)
	//
	
	public function get_create_node_pkt($service, $node) {
		$child = new JAXLXml('pubsub', NS_PUBSUB);
		$child->c('create', null, array('node'=>$node));
		return $this->get_iq_pkt($service, $child);
	}
	
	public function create_node($service, $node) {
		$this->jaxl->send($this->get_create_node_pkt($service, $node));
	}
	
	//
	// event callbacks
	//
	
	
	//
	// local methods
	//
	
	// this always add attrs
	protected function get_iq_pkt($service, $child, $type='set') {
		return $this->jaxl->get_iq_pkt(
			array('type'=>$type, 'from'=>$this->jaxl->full_jid->to_string(), 'to'=>$service),
			$child
		);
	}
	
}

?>

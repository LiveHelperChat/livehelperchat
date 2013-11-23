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

define('NS_MUC', 'http://jabber.org/protocol/muc');

class XEP_0045 extends XMPPXep {
	
	//
	// abstract method
	//
	
	public function init() {
		return array();
	}
	
	public function send_groupchat($room_jid, $body, $thread=null, $subject=null) {
		$msg = new XMPPMsg(
			array(
				'type'=>'groupchat', 
				'to'=>(($room_jid instanceof XMPPJid) ? $room_jid->to_string() : $room_jid), 
				'from'=>$this->jaxl->full_jid->to_string()
			),
			$body,
			$thread,
			$subject
		);
		$this->jaxl->send($msg);
	}
	
	//
	// api methods (occupant use case)
	//
	
	// room_full_jid simply means room jid with nick name as resource
	public function get_join_room_pkt($room_full_jid, $options) {
		$pkt = $this->jaxl->get_pres_pkt(
			array(
				'from'=>$this->jaxl->full_jid->to_string(), 
				'to'=>(($room_full_jid instanceof XMPPJid) ? $room_full_jid->to_string() : $room_full_jid)
			)
		);
		$x = $pkt->c('x', NS_MUC);
		if (isset($options['no_history'])) {
			$x->c('history')->attrs(array('maxstanzas' => 0, 'seconds' => 0));
		}
		return $x;
	}
	
	public function join_room($room_full_jid, $options = array()) {
		$pkt = $this->get_join_room_pkt($room_full_jid, $options);
		$this->jaxl->send($pkt);
	}
	
	public function get_leave_room_pkt($room_full_jid) {
		return $this->jaxl->get_pres_pkt(
			array('type'=>'unavailable', 'from'=>$this->jaxl->full_jid->to_string(), 'to'=>(($room_full_jid instanceof XMPPJid) ? $room_full_jid->to_string() : $room_full_jid))
		);
	}
	
	public function leave_room($room_full_jid) {
		$pkt = $this->get_leave_room_pkt($room_full_jid);
		$this->jaxl->send($pkt);
	}
	
	//
	// api methods (moderator use case)
	//
	
	
	
	//
	// event callbacks
	//

}

?>

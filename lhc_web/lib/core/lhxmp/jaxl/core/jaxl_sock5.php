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

require_once JAXL_CWD.'/core/jaxl_socket_client.php';

/**
 * TODO: convert into a finite state machine
 * 
 * @author abhinavsingh
 *
 */
class JAXLSock5 {
	
	private $client = null;
	
	protected $transport = null;
	protected $ip = null;
	protected $port = null;
	
	public function __construct($transport='tcp') {
		$this->transport = $transport;
		$this->client = new JAXLSocketClient();
		$this->client->set_callback(array(&$this, 'on_response'));
	}
	
	public function __destruct() {
		
	}
	
	public function connect($ip, $port=1080) {
		$this->ip = $ip;
		$this->port = $port;
		$sock_path = $this->_sock_path();
		
		if($this->client->connect($sock_path)) {
			_debug("established connection to $sock_path");
			return true;
		}
		else {
			_error("unable to connect $sock_path");
			return false;
		}
	}
	
	//
	// Three phases of SOCK5
	//
	
	// Negotiation pkt consists of 3 part:
	// 0x05 => Sock protocol version
	// 0x01 => Number of method identifier octet
	//
	// Following auth methods are defined:
	// 0x00 => No authentication required
	// 0x01 => GSSAPI
	// 0x02 => USERNAME/PASSWORD
	// 0x03 to 0x7F => IANA ASSIGNED
	// 0x80 to 0xFE => RESERVED FOR PRIVATE METHODS
	// 0xFF => NO ACCEPTABLE METHODS
	public function negotiate() {
		$pkt = pack("C3", 0x05, 0x01, 0x00);
		$this->client->send($pkt);
		
		// enter sub-negotiation state
	}
	
	public function relay_request() {
		// enter wait for reply state
	}
	
	public function send_data() {
		
	}
	
	//
	// Socket client callback
	//
	
	public function on_response($raw) {
		_debug($raw);
	}
	
	//
	// Private
	//
	
	protected function _sock_path() {
		return $this->transport.'://'.$this->ip.':'.$this->port;
	}
	
}

?>

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
require_once JAXL_CWD.'/http/http_multipart.php';

//
// These methods are available only once
// $request FSM has reached 'headers_received' state
// 
// following shortcuts are available
// on received $request object:
//
//	   $request->{status_code_name}($headers, $body)
//     $request->{status_code_name}($body, $headers)
//
//     $request->{status_code_name}($headers)
//     $request->{status_code_name}($body)
//
// following specific methods are also available:
//
//     $request->send_line($code)
//     $request->send_header($key, $value)
//     $request->send_headers($headers)
//     $request->send_message($string)
//
// all the above methods can also be directly performed using:
//
//     $request->send_response($code, $headers=array(), $body=null)
//
// Note: All the send_* methods do not manage connection close/keep-alive logic,
// it is upto you to do that, by default connection will usually be dropped
// on client disconnect if not handled by you.
//
class HTTPRequest extends JAXLFsm {
	
	// peer identifier
	public $sock = null;
	public $ip = null;
	public $port = null;
	
	// request line
	public $version = null;
	public $method = null;
	public $resource = null;
	public $path = null;
	public $query = array();
	
	// headers and body
	public $headers = array();
	public $body = null;
	public $recvd_body_len = 0;
	
	// is true if 'Expect: 100-Continue' 
	// request header has been seen
	public $expect = false;
	
	// set if there is Content-Type: multipart/form-data; boundary=...
	// header already seen
	public $multipart = null;
	
	// callback for send/read/close actions on accepted sock
	private $_send_cb = null;
	private $_read_cb = null;
	private $_close_cb = null;
	
	private $shortcuts = array(
		'ok' => 200, // 2xx
		'redirect' => 302, 'not_modified' => 304, // 3xx
		'not_found' => 404, 'bad_request' => 400, // 4xx
		'internal_error' => 500, // 5xx
		'recv_body' => true, 'close' => true // others
	);
	
	public function __construct($sock, $addr) {
		$this->sock = $sock;
		
		$addr = explode(":", $addr);
		$this->ip = $addr[0];
		if(sizeof($addr) == 2) {
			$this->port = $addr[1];
		}
		
		parent::__construct("setup");
	}
	
	public function __destruct() {
		_debug("http request going down in ".$this->state." state");
	}
	
	public function state() {
		return $this->state;
	}
	
	//
	// abstract method implementation
	//
	
	public function handle_invalid_state($r) {
		_debug("handle invalid state called with");
		var_dump($r);
	}
	
	//
	// fsm States
	//
	
	public function setup($event, $args) {
		switch($event) {
			case 'set_sock_cb':
				$this->_send_cb = $args[0];
				$this->_read_cb = $args[1];
				$this->_close_cb = $args[2];
				return 'wait_for_request_line';
				break;
			default:
				_warning("uncatched $event");
				return 'setup';
		}
	}
	
	public function wait_for_request_line($event, $args) {
		switch($event) {
			case 'line':
				$this->_line($args[0], $args[1], $args[2]);
				return 'wait_for_headers';
				break;
			default:
				_warning("uncatched $event");
				return 'wait_for_request_line';
		}
	}
	
	public function wait_for_headers($event, $args) {
		switch($event) {
			case 'set_header':
				$this->set_header($args[0], $args[1]);
				return 'wait_for_headers';
				break;
			case 'empty_line':
				return 'maybe_headers_received';
			default:
				_warning("uncatched $event");
				return 'wait_for_headers';
		}
	}
	
	public function maybe_headers_received($event, $args) {
		switch($event) {
			case 'set_header':
				$this->set_header($args[0], $args[1]);
				return 'wait_for_headers';
				break;
			case 'empty_line':
				return 'headers_received';
				break;
			case 'body':
				return $this->wait_for_body($event, $args);
				break;
			default:
				_warning("uncatched $event");
				return 'maybe_headers_received';
		}
	}
	
	public function wait_for_body($event, $args) {
		switch($event) {
			case 'body':
				$content_length = $this->headers['Content-Length'];
				$rcvd = $args[0];
				$rcvd_len = strlen($rcvd);
				$this->recvd_body_len += $rcvd_len;
				
				if($this->body === null) $this->body = $rcvd;
				else $this->body .= $rcvd;
				
				if($this->multipart) {
					// boundary start, content_disposition, form data, boundary start, ....., boundary end
					// these define various states of a multipart/form-data
					$form_data = explode("\r\n", $rcvd);
					foreach($form_data as $data) {
						//_debug("passing $data to multipart fsm");
						if(!$this->multipart->process($data)) {
							_debug("multipart fsm returned false");
							$this->_close();
							return array('closed', false);
						}
					}
				}
				
				if($this->recvd_body_len < $content_length && $this->multipart->state() != 'done') {
					_debug("rcvd body len: $this->recvd_body_len/$content_length");
					return 'wait_for_body';
				}
				else {
					_debug("all data received, switching state for dispatch");
					return 'headers_received';
				}
				break;
			case 'set_header':
				$content_length = $this->headers['Content-Length'];
				$body = implode(":", $args);
				$rcvd_len = strlen($body);
				$this->recvd_body_len += $rcvd_len;
				
				if(!$this->multipart->process($body)) {
					_debug("multipart fsm returned false");
					$this->_close();
					return array('closed', false);
				}
				
				if($this->recvd_body_len < $content_length) {
					_debug("rcvd body len: $this->recvd_body_len/$content_length");
					return 'wait_for_body';
				}
				else {
					_debug("all data received, switching state for dispatch");
					return 'headers_received';
				}
				break;
			case 'empty_line':
				$content_length = $this->headers['Content-Length'];
				
				if(!$this->multipart->process('')) {
					_debug("multipart fsm returned false");
					$this->_close();
					return array('closed', false);
				}
				
				if($this->recvd_body_len < $content_length) {
					_debug("rcvd body len: $this->recvd_body_len/$content_length");
					return 'wait_for_body';
				}
				else {
					_debug("all data received, switching state for dispatch");
					return 'headers_received';
				}
				break;
			default:
				_warning("uncatched $event");
				return 'wait_for_body';
		}
	}
	
	// headers and may be body received
	public function headers_received($event, $args) {
		switch($event) {
			case 'empty_line':
				return 'headers_received';
				break;
			default:
				if(substr($event, 0, 5) == 'send_') {
					$protected = '_'.$event;
					if(method_exists($this, $protected)) {
						call_user_func_array(array(&$this, $protected), $args);
						return 'headers_received';
					}
					else {
						_debug("non-existant method $event called");
						return 'headers_received';
					}
				}
				else if(@isset($this->shortcuts[$event])) {
					return $this->handle_shortcut($event, $args);
				}
				else {
					_warning("uncatched $event ".$args[0]);
					return 'headers_received';
				}
		}
	}
	
	public function closed($event, $args) {
		_warning("uncatched $event");
	}
	
	// sets input headers
	// called internally for every header received
	protected function set_header($k, $v) {
		$k = trim($k); $v = ltrim($v);
		
		// is expect header?
		if(strtolower($k) == 'expect' && strtolower($v) == '100-continue') {
			$this->expect = true;
		}
		
		// is multipart form data?
		if(strtolower($k) == 'content-type') {
			$ctype = explode(';', $v);
			if(sizeof($ctype) == 2 && strtolower(trim($ctype[0])) == 'multipart/form-data') {
				$boundary = explode('=', trim($ctype[1]));
				if(strtolower(trim($boundary[0])) == 'boundary') {
					_debug("multipart with boundary $boundary[1] detected");
					$this->multipart = new HTTPMultiPart(ltrim($boundary[1]));
				}
			}
		}
		
		$this->headers[trim($k)] = trim($v);
	}
	
	// shortcut handler
	protected function handle_shortcut($event, $args) {
		_debug("executing shortcut '$event'");
		switch($event) {
			// http status code shortcuts
			case 'ok':
			case 'redirect':
			case 'not_modified':
			case 'bad_request':
			case 'not_found':
			case 'internal_error':
				list($headers, $body) = $this->parse_shortcut_args($args);
				
				$code = $this->shortcuts[$event];
				$this->_send_response($code, $headers, $body);
				
				$this->_close();
				return 'closed';
				break;
			// others
			case 'recv_body':
				// send expect header if required
				if($this->expect) {
					$this->expect = false;
					$this->_send_line(100);
				}
				
				// read data
				$this->_read();
				
				return 'wait_for_body';
				break;
			case 'close':
				$this->_close();
				return 'closed';
				break;
		}
	}
	
	private function parse_shortcut_args($args) {
		if(sizeof($args) == 0) {
			$body = null;
			$headers = array();
		}
		if(sizeof($args) == 1) {
			// http headers or body only received
			if(is_array($args[0])) {
				// http headers only
				$headers = $args[0];
				$body = null;
			}
			else {
				// body only
				$body = $args[0];
				$headers = array();
			}
		}
		else if(sizeof($args) == 2) {
			// body and http headers both received
			if(is_array($args[0])) {
				// header first
				$body = $args[1];
				$headers = $args[0];
			}
			else {
				// body first
				$body = $args[0];
				$headers = $args[1];
			}
		}
		
		return array($headers, $body);
	}
	
	// 
	// send methods
	// available only on 'headers_received' state
	//
	
	protected function _send_line($code) {
		$raw = $this->version." ".$code." ".constant('HTTP_'.$code).HTTP_CRLF;
		$this->_send($raw);
	}
	
	protected function _send_header($k, $v) {
		$raw = $k.': '.$v.HTTP_CRLF;
		$this->_send($raw);
	}
	
	protected function _send_headers($code, $headers) {
		foreach($headers as $k=>$v) 
			$this->_send_header($k, $v);
	}
	
	protected function _send_body($body) {
		$this->_send($body);
	}
	
	protected function _send_response($code, $headers=array(), $body=null) {
		// send out response line
		$this->_send_line($code);
		
		// set content length of body exists and is not already set
		if($body && !isset($headers['Content-Length']))
			$headers['Content-Length'] = strlen($body);
		
		// send out headers
		$this->_send_headers($code, $headers);
	
		// send body
		// prefixed with an empty line
		_debug("sending out HTTP_CRLF prefixed body");
		if($body)
			$this->_send_body(HTTP_CRLF.$body);
	}
	
	//
	// internal methods
	//
	
	// initializes status line elements
	private function _line($method, $resource, $version) {
		$this->method = $method;
		$this->resource = $resource;
	
		$resource = explode("?", $resource);
		$this->path = $resource[0];
		if(sizeof($resource) == 2) {
			$query = $resource[1];
			$query = explode("&", $query);
			foreach($query as $q) {
				$q = explode("=", $q);
				if(sizeof($q) == 1) $q[1] = "";
				$this->query[$q[0]] = $q[1];
			}
		}
	
		$this->version = $version;
	}
	
	private function _send($raw) {
		call_user_func($this->_send_cb, $this->sock, $raw);
	}
	
	private function _read() {
		call_user_func($this->_read_cb, $this->sock);
	}
	
	private function _close() {
		call_user_func($this->_close_cb, $this->sock);
	}
	
	
}

?>

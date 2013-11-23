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

require_once JAXL_CWD.'/core/jaxl_loop.php';

class JAXLSocketServer {
	
	public $fd = null;
	
	private $clients = array();
	private $recv_chunk_size = 1024;
	private $send_chunk_size = 8092;
	private $accept_cb = null;
	private $request_cb = null;
	private $blocking = false;
	private $backlog = 200;
	
	public function __construct($path, $accept_cb, $request_cb) {
		$this->accept_cb = $accept_cb;
		$this->request_cb = $request_cb;
		
		$ctx = stream_context_create(array('socket'=>array('backlog'=>$this->backlog)));
		if(($this->fd = @stream_socket_server($path, $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $ctx)) !== false) {
			if(@stream_set_blocking($this->fd, $this->blocking)) {
				JAXLLoop::watch($this->fd, array(
					'read' => array(&$this, 'on_server_accept_ready')
				));
				_info("socket ready to accept on path ".$path);
			}
			else {
				_error("unable to set non block flag");
			}
		}
		else {
			_error("unable to establish socket server, errno: ".$errno.", errstr: ".$errstr);
		}
	}
	
	public function __destruct() {
		_info("shutting down socket server");
	}
	
	public function read($client_id) {
		//_debug("reactivating read on client sock");
		$this->add_read_cb($client_id);
	}
	
	public function send($client_id, $data) {
		$this->clients[$client_id]['obuffer'] .= $data;
		$this->add_write_cb($client_id);
	}
	
	public function close($client_id) {
		$this->clients[$client_id]['close'] = true;
		$this->add_write_cb($client_id);
	}
	
	public function on_server_accept_ready($server) {
		//_debug("got server accept");
		$client = @stream_socket_accept($server, 0, $addr);
		if(!$client) {
			_error("unable to accept new client conn");
			return;
		}
		
		if(@stream_set_blocking($client, $this->blocking)) {
			$client_id = (int) $client;
			$this->clients[$client_id] = array(
				'fd' => $client,
				'ibuffer' => '',
				'obuffer' => '',
				'addr' => trim($addr),
				'close' => false,
				'closed' => false,
				'reading' => false,
				'writing' => false
			);
			
			_debug("accepted connection from client#".$client_id.", addr:".$addr);
			
			// if accept callback is registered
			// callback and let user land take further control of what to do
			if($this->accept_cb) {
				call_user_func($this->accept_cb, $client_id, $this->clients[$client_id]['addr']);
			}
			// if no accept callback is registered
			// close the accepted connection
			else {
				@fclose($client);
				$this->clients[$client_id]['closed'] = true;
				unset($this->clients[$client_id]);
			}
		}
		else {
			_error("unable to set non block flag");
		}
	}
	
	public function on_client_read_ready($client) {
		// deactive socket for read
		$client_id = (int) $client;
		//_debug("client#$client_id is read ready");
		$this->del_read_cb($client_id);
		
		$raw = fread($client, $this->recv_chunk_size);
		$bytes = strlen($raw);
		
		_debug("recv $bytes bytes from client#$client_id");
		//_debug($raw);
		
		if($bytes === 0) {
			$meta = stream_get_meta_data($client);
			if($meta['eof'] === TRUE) {
				_debug("socket eof client#".$client_id.", closing");
				$this->del_read_cb($client_id);
				
				@fclose($client);
				unset($this->clients[$client_id]);
				return;
			}
		}
		
		$total = $this->clients[$client_id]['ibuffer'] . $raw;
		if($this->request_cb) 
			call_user_func($this->request_cb, $client_id, $total);
		$this->clients[$client_id]['ibuffer'] = '';
	}
	
	public function on_client_write_ready($client) {
		$client_id = (int) $client;
		_debug("client#$client_id is write ready");
		
		try {
			// send in chunks
			$total = $this->clients[$client_id]['obuffer'];
			$written = @fwrite($client, substr($total, 0, $this->send_chunk_size));
			
			if($written === false) {
				// fwrite failed
				_warning("====> fwrite failed");
				$this->clients[$client_id]['obuffer'] = $total;
			}
			else if($written == strlen($total) || $written == $this->send_chunk_size) {
				// full chunk written
				//_debug("full chunk written");
				$this->clients[$client_id]['obuffer'] = substr($total, $this->send_chunk_size);
			}
			else {
				// partial chunk written
				//_debug("partial chunk $written written");
				$this->clients[$client_id]['obuffer'] = substr($total, $written);
			}
			
			// if no more stuff to write, remove write handler
			if(strlen($this->clients[$client_id]['obuffer']) === 0) {
				$this->del_write_cb($client_id);
				
				// if scheduled for close and not closed do it and clean up
				if($this->clients[$client_id]['close'] && !$this->clients[$client_id]['closed']) {
					@fclose($client);
					$this->clients[$client_id]['closed'] = true;
					unset($this->clients[$client_id]);
					
					_debug("closed client#".$client_id);
				}
			}
		}
		catch(JAXLException $e) {
			_debug("====> got fwrite exception");
		}
	}
	
	//
	// client sock event register utils
	//
	
	protected function add_read_cb($client_id) {
		if($this->clients[$client_id]['reading'])
			return;
		
		JAXLLoop::watch($this->clients[$client_id]['fd'], array(
			'read' => array(&$this, 'on_client_read_ready')
		));
		
		$this->clients[$client_id]['reading'] = true;
	}
	
	protected function add_write_cb($client_id) {
		if($this->clients[$client_id]['writing'])
			return;
		
		JAXLLoop::watch($this->clients[$client_id]['fd'], array(
			'write' => array(&$this, 'on_client_write_ready')
		));
		
		$this->clients[$client_id]['writing'] = true;
	}
	
	protected function del_read_cb($client_id) {
		if(!$this->clients[$client_id]['reading'])
			return;
		
		JAXLLoop::unwatch($this->clients[$client_id]['fd'], array(
			'read' => true
		));
		
		$this->clients[$client_id]['reading'] = false;
	}
	
	protected function del_write_cb($client_id) {
		if(!$this->clients[$client_id]['writing'])
			return;
		
		JAXLLoop::unwatch($this->clients[$client_id]['fd'], array(
			'write' => true
		));
		
		$this->clients[$client_id]['writing'] = false;
	}
	
}

?>

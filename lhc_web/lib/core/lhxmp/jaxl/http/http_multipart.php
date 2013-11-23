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

class HTTPMultiPart extends JAXLFsm {

	public $boundary = null;
	public $form_data = array();
	public $index = -1;

	public function handle_invalid_state($r) {
		_error("got invalid event $r");
	}

	public function __construct($boundary) {
		$this->boundary = $boundary;
		parent::__construct('wait_for_boundary_start');
	}

	public function state() {
		return $this->state;
	}

	public function wait_for_boundary_start($event, $data) {
		if($event == 'process') {
			if($data[0] == '--'.$this->boundary) {
				$this->index += 1;
				$this->form_data[$this->index] = array(
					'meta' => array(),
					'headers' => array(),
					'body' => ''
				);
				return array('wait_for_content_disposition', true);
			}
			else {
				_warning("invalid boundary start $data[0] while expecting $this->boundary");
				return array('wait_for_boundary_start', false);
			}
		}
		else {
			_warning("invalid $event rcvd");
			return array('wait_for_boundary_start', false);
		}
	}

	public function wait_for_content_disposition($event, $data) {
		if($event == 'process') {
			$disposition = explode(":", $data[0]);
				
			if(strtolower(trim($disposition[0])) == 'content-disposition') {
				$this->form_data[$this->index]['headers'][$disposition[0]] = trim($disposition[1]);
				$meta = explode(";", $disposition[1]);
				if(trim(array_shift($meta)) == 'form-data') {
					foreach($meta as $k) {
						list($k, $v) = explode("=", $k);
						$this->form_data[$this->index]['meta'][$k] = $v;
					}
						
					return array('wait_for_content_type', true);
				}
				else {
					_warning("first part of meta is not form-data");
					return array('wait_for_content_disposition', false);
				}
			}
			else {
				_warning("not a valid content-disposition line");
				return array('wait_for_content_disposition', false);
			}
		}
		else {
			_warning("invalid $event rcvd");
			return array('wait_for_content_disposition', false);
		}
	}

	public function wait_for_content_type($event, $data) {
		if($event == 'process') {
			$type = explode(":", $data[0]);
			if(strtolower(trim($type[0])) == 'content-type') {
				$this->form_data[$this->index]['headers'][$type[0]] = trim($type[1]);
				$this->form_data[$this->index]['meta']['type'] = $type[1];
				return array('wait_for_content_body', true);
			}
			else {
				_debug("not a valid content-type line");
				return array('wait_for_content_type', false);
			}
		}
		else {
			_warning("invalid $event rcvd");
			return array('wait_for_content_type', false);
		}
	}

	public function wait_for_content_body($event, $data) {
		if($event == 'process') {
			if($data[0] == '--'.$this->boundary) {
				_debug("start of new multipart/form-data detected");
				return array('wait_for_content_disposition', true);
			}
			else if($data[0] == '--'.$this->boundary.'--') {
				_debug("end of multipart form data detected");
				return array('wait_for_empty_line', true);
			}
			else {
				$this->form_data[$this->index]['body'] .= $data[0];
				return array('wait_for_content_body', true);
			}
		}
		else {
			_warning("invalid $event rcvd");
			return array('wait_for_content_body', false);
		}
	}

	public function wait_for_empty_line($event, $data) {
		if($event == 'process') {
			if($data[0] == '') {
				return array('done', true);
			}
			else {
				_warning("invalid empty line $data[0] received");
				return array('wait_for_empty_line', false);
			}
		}
		else {
			_warning("got $event in done state with data $data[0]");
			return array('wait_for_empty_line', false);
		}
	}

	public function done($event, $data) {
		_warning("got unhandled event $event with data $data[0]");
		return array('done', false);
	}

}

?>

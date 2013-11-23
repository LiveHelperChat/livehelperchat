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

/**
 * following kind of events are possible:
 * 1) hook i.e. if a callback for such an event is registered, calling function is responsible for the workflow from their on
 * 2) filter i.e. calling function will manipulate passed arguments and modified arguments will be passed to next chain of filter
 * 
 * As a rule of thumb, only 1 hook can be registered for an event, while more than 1 filter is allowed for an event
 * hook and filter both cannot be applied on an event
 * 
 * @author abhinavsingh
 *
 */
class JAXLEvent {
	
	protected $common = array();
	public $reg = array();
	
	public function __construct($common) {
		$this->common = $common;
	}
	
	public function __destruct() {
		
	}
	
	// add callback on a event
	// returns a reference to be used while deleting callback
	// callback'd method must return TRUE to be persistent
	// if none returned or FALSE, callback will be removed automatically
	public function add($ev, $cb, $pri) {
		if(!isset($this->reg[$ev]))
			$this->reg[$ev] = array();
		
		$ref = sizeof($this->reg[$ev]);
		$this->reg[$ev][] = array($pri, $cb);
		return $ev."-".$ref;
	}
	
	// emit event to notify registered callbacks
	// is a pqueue required here for performance enhancement
	// in case we have too many cbs on a specific event?
	public function emit($ev, $data=array()) {
		$data = array_merge($this->common, $data);
		$cbs = array();
		
		if(!isset($this->reg[$ev])) return $data;
		
		foreach($this->reg[$ev] as $cb) {
			if(!isset($cbs[$cb[0]]))
				$cbs[$cb[0]] = array();
			$cbs[$cb[0]][] = $cb[1];
		}
		
		foreach($cbs as $pri => $cb) {
			foreach($cb as $c) {
				$ret = call_user_func_array($c, $data);
				// this line is for fixing situation where callback function doesn't return an array type
				// in such cases next call of call_user_func_array will report error since $data is not an array type as expected
				// things will change in future, atleast put the callback inside a try/catch block
				// here we only check if there was a return, if yes we update $data with return value
				// this is bad design, need more thoughts, should work as of now
				if($ret) $data = $ret;
			}
		}
		
		unset($cbs);
		return $data;
	}
	
	// remove previous registered callback
	public function del($ref) {
		$ref = explode("-", $ref);
		unset($this->reg[$ref[0]][$ref[1]]);
	}
	
	public function exists($ev) {
		$ret = isset($this->reg[$ev]);
		//_debug("event ".$ev." callback ".($ret ? "exists" : "do not exists"));
		return $ret;
	}
	
}

?>

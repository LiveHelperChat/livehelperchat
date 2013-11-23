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

//
// (optionally) set an array of url dispatch rules
// each dispatch rule is defined by an array of size 4 like:
// array($callback, $pattern, $methods, $extra), where
//     $callback    the method which will be called if this rule matches
//     $pattern     regular expression to match request path
//     $methods     list of allowed methods for this rule
//                  pass boolean true to allow all http methods
//                  if omitted or an empty array() is passed (which actually doesnt make sense)
//                  by default 'GET' will be the method allowed on this rule
//     $extra       reserved for future (you can totally omit this as of now)
//
class HTTPDispatchRule {
	
	// match callback
	public $cb = null;
	
	// regexp to match on request path
	public $pattern = null;
	
	// methods to match upon
	// add atleast 1 method for this rule to work
	public $methods = null;
	
	// other matching rules
	public $extra = array();
	
	public function __construct($cb, $pattern, $methods=array('GET'), $extra=array()) {
		$this->cb = $cb;
		$this->pattern = $pattern;
		$this->methods = $methods;
		$this->extra = $extra;
	}
	
	public function match($path, $method) {
		if(preg_match("/".str_replace("/", "\/", $this->pattern)."/", $path, $matches)) {
			if(in_array($method, $this->methods)) {
				return $matches;
			}
		}
		return false;
	}
	
}

class HTTPDispatcher {
	
	protected $rules = array();
	
	public function __construct() {
		$this->rules = array();
	}
	
	public function add_rule($rule) {
		$s = sizeof($rule);
		if($s > 4) { _debug("invalid rule"); return; }
		
		// fill up defaults
		if($s == 3) { $rule[] = array(); }
		else if($s == 2) { $rule[] = array('GET'); $rule[] = array(); }
		else { _debug("invalid rule"); return; }
		
		$this->rules[] = new HTTPDispatchRule($rule[0], $rule[1], $rule[2], $rule[3]);
	}
	
	public function dispatch($request) {
		foreach($this->rules as $rule) {
			//_debug("matching $request->path with pattern $rule->pattern");
			if(($matches = $rule->match($request->path, $request->method)) !== false) {
				_debug("matching rule found, dispatching");
				$params = array($request);
				// TODO: a bad way to restrict on 'pk', fix me for generalization
				if(@isset($matches['pk'])) $params[] = $matches['pk'];
				call_user_func_array($rule->cb, $params);
				return true;
			}
		}
		return false;
	}
	
}

?>

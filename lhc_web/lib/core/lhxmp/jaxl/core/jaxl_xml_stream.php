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
 * 
 * Enter description here ...
 * @author abhinavsingh
 *
 */
class JAXLXmlStream {
	
	private $delimiter = '\\';
	private $ns;
	private $parser;
	private $stanza;
	private $depth = -1;
	
	private $start_cb;
	private $stanza_cb;
	private $end_cb;
	
	public function __construct() {
		$this->init_parser();
	}
	
	private function init_parser() {
		$this->depth = -1;
		$this->parser = xml_parser_create_ns("UTF-8", $this->delimiter);
		
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
		
		xml_set_character_data_handler($this->parser, array(&$this, "handle_character"));
		xml_set_element_handler($this->parser, array(&$this, "handle_start_tag"), array(&$this, "handle_end_tag"));
	}
	
	public function __destruct() {
		//_debug("cleaning up xml parser...");
		@xml_parser_free($this->parser);
	}
	
	public function reset_parser() {
		$this->parse_final(null);
		@xml_parser_free($this->parser);
		$this->parser = null;
		$this->init_parser();
	}
	
	public function set_callback($start_cb, $end_cb, $stanza_cb) {
		$this->start_cb = $start_cb;
		$this->end_cb = $end_cb;
		$this->stanza_cb = $stanza_cb;
	}
	
	public function parse($str) {
		xml_parse($this->parser, $str, false);
	}
	
	public function parse_final($str) {
		xml_parse($this->parser, $str, true);
	}
	
	protected function handle_start_tag($parser, $name, $attrs) {
		$name = $this->explode($name);
		//echo "start of tag ".$name[1]." with ns ".$name[0].PHP_EOL;
		
		// replace ns with prefix
		foreach($attrs as $key=>$v) {
			$k = $this->explode($key);
			
			// no ns specified
			if($k[0] == null) {
				$attrs[$k[1]] = $v;
			}
			// xml ns
			else if($k[0] == NS_XML) {
				unset($attrs[$key]);
				$attrs['xml:'.$k[1]] = $v;
			}
			else {
				_error("==================> unhandled ns prefix on attribute");
				// remove attribute else will cause error with bad stanza format
				// report to developer if above error message is ever encountered
				unset($attrs[$key]);
			}
		}
		
		if($this->depth <= 0) {
			$this->depth = 0;
			$this->ns = $name[1];
			
			if($this->start_cb) {
				$stanza = new JAXLXml($name[1], $name[0], $attrs);
				call_user_func($this->start_cb, $stanza);
			}
		}
		else {
			if(!$this->stanza) {
				$stanza = new JAXLXml($name[1], $name[0], $attrs);
				$this->stanza = &$stanza;
			}
			else {
				$this->stanza->c($name[1], $name[0], $attrs);
			}
		}
		
		++$this->depth;
	}
	
	protected function handle_end_tag($parser, $name) {
		$name = explode($this->delimiter, $name);
		$name = sizeof($name) == 1 ? array('', $name[0]) : $name;
		
		//echo "depth ".$this->depth.", $name[1] tag ended".PHP_EOL.PHP_EOL;
		
		if($this->depth == 1) {
			if($this->end_cb) {
				$stanza = new JAXLXml($name[1], $this->ns);
				call_user_func($this->end_cb, $stanza);
			}
		}
		else if($this->depth > 1) {
			if($this->stanza) $this->stanza->up();
			
			if($this->depth == 2) {
				if($this->stanza_cb) {
					call_user_func($this->stanza_cb, $this->stanza);
					$this->stanza = null;
				}
			}
		}
		
		--$this->depth;
	}
	
	protected function handle_character($parser, $data) {
		//echo "depth ".$this->depth.", character ".$data." for stanza ".$this->stanza->name.PHP_EOL;
		if($this->stanza) {
			$this->stanza->t(htmlentities($data, ENT_COMPAT, "UTF-8"), TRUE);
		}
	}
	
	private function implode($data) {
		return implode($this->delimiter, $data);
	}
	
	private function explode($data) {
		$data = explode($this->delimiter, $data);
		$data = sizeof($data) == 1 ? array(null, $data[0]) : $data;
		return $data;
	}
	
}

?>

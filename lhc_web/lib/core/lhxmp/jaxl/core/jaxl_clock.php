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

class JAXLClock {
	
	// current clock time in microseconds
	private $tick = 0;
	
	// current Unix timestamp with microseconds
	public $time = null;
	
	// scheduled jobs
	public $jobs = array();
	
	public function __construct() {
		$this->time = microtime(true);
	}
	
	public function __destruct() {
		_info("shutting down clock server...");
	}
	
	public function tick($by=null) {
		// update clock
		if($by) {
			$this->tick += $by;
			$this->time += $by / pow(10,6);
		}
		else {
			$time = microtime(true);
			$by = $time - $this->time;
			$this->tick += $by * pow(10, 6);
			$this->time = $time;
		}
		
		// run scheduled jobs
		foreach($this->jobs as $ref=>$job) {
			if($this->tick >= $job['scheduled_on'] + $job['after']) {
				//_debug("running job#".($ref+1)." at tick ".$this->tick.", scheduled on ".$job['scheduled_on']." after ".$job['after'].", periodic ".$job['is_periodic']);
				call_user_func($job['cb'], $job['args']);
				if(!$job['is_periodic']) {
					unset($this->jobs[$ref]);
				}
				else {
					$job['scheduled_on'] = $this->tick;
					$job['runs']++;
					$this->jobs[$ref] = $job;
				}
			}
		}
	}
	
	// calculate execution time of callback
	public function tc($callback, $args=null) {
		
	}
	
	// callback after $time microseconds
	public function call_fun_after($time, $callback, $args=null) {
		$this->jobs[] = array(
			'scheduled_on' => $this->tick,
			'after' => $time,
			'cb' => $callback,
			'args' => $args,
			'is_periodic' => false,
			'runs' => 0
		);
		return sizeof($this->jobs);
	}
	
	// callback periodically after $time microseconds
	public function call_fun_periodic($time, $callback, $args=null) {
		$this->jobs[] = array(
			'scheduled_on' => $this->tick,
			'after' => $time,
			'cb' => $callback,
			'args' => $args,
			'is_periodic' => true,
			'runs' => 0
		);
		return sizeof($this->jobs);
	}
	
	// cancel a previously scheduled callback
	public function cancel_fun_call($ref) {
		unset($this->jobs[$ref-1]);
	}
	
}

?>

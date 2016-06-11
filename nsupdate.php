<?php
/*
auth.php

Util functions & classes for nsupdate

Copyright 2016 Oliver Kahrmann

Licensed under the Apache License, Version 2.0 (the "License"); 
you may not use this file except in compliance with the License. 
You may obtain a copy of the License at 

   http://www.apache.org/licenses/LICENSE-2.0 

Unless required by applicable law or agreed to in writing, software 
distributed under the License is distributed on an "AS IS" BASIS, 
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. 
See the License for the specific language governing permissions and 
limitations under the License. 
*/

class DomainInfo {
	public $domain;
	public $zone;
	public $nameserver;
	public $keyfile;
	public $ttl;

	function __construct($domain, $zone, $nameserver, $keyfile, $ttl)
	{
		$this->domain = $domain;
		$this->zone = $zone;
		$this->nameserver = $nameserver;
		$this->keyfile = $keyfile;
		$this->ttl = $ttl;
	}
}

class NsupdateInfo {
	public $domain;
	public $zone;
	public $nameserver;
	public $keyfile;
	public $ttl;

	public $ip;

	function __construct($info, $ip)
	{
		$this->domain = escapeshellcmd($info->domain);
		$this->zone = escapeshellcmd($info->zone);
		$this->nameserver = escapeshellcmd($info->nameserver);
		$this->keyfile = escapeshellcmd($info->keyfile);
		$this->ttl = escapeshellcmd($info->ttl);

		$this->ip = escapeshellcmd($ip);
	}
}

?>
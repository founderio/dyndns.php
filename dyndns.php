<?php
/*
dyndns.php

PHP script to call nsupdate, intended for Dynamic DNS Setups.
Designed for use with automatic update from an internet gateway ("router"), e.g. a Fritz!Box.

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

// Prepare default config, will be overridden/adjusted in dyndns.conf.php
$config = array(
	'users' => array(),
	'domains' => array(),
	'realm' => 'Dynamic DNS Update',
	'nsupdate_template' => function($update_info) {
		$file = "<<EOD
server $update_info->nameserver
zone $update_info->zone
update delete $update_info->domain A
update add $update_info->domain $update_info->ttl A $update_info->ip
send
EOD";
		return $file;
	},
	'nsupdate_call' => function($update_info, $file) {
		return "/usr/bin/nsupdate -k $update_info->keyfile 2>&1 $file";
	}
	);

header('Content-Type: text/plain');

require 'vendor/autoload.php';

// Set up log4php
Logger::configure('config.xml');
$log = Logger::getLogger('dyndns');

require 'auth.php';
require 'nsupdate.php';

require 'dyndns.conf.php';

function get_option($name) {
	if(isset($_GET[$name])) {
		return $_GET[$name];
	} else if(isset($_POST[$name])) {
		return $_POST[$name];
	} else {
		return false;
	}
}

function isIPv4($ip) {
	return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
}

function isIPv6($ip) {
	return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
}

function get_nsupdate_options(&$error) {
	global $log;
	global $config;

	$domain = get_option('domain');

	if($domain === false || !isset($config['domains'][$domain])) {
		$error = 'No domain specified or domain not configured';
		$log->error("No domain specified or domain not configured: $domain");
		return false;
	}

	$ipv4 = get_option('ipv4');
	if($ipv4 === false || !isIPv4($ipv4)) {
		$error = 'No IPv4 specified or invalid IPv4';
		$log->error("No IPv4 specified or invalid IPv4: $ipv4");
		return false;
	}

	$domain_info = $config['domains'][$domain];

	return new NsupdateInfo($domain_info, $ipv4);
}

function run_nsupdate($update_info, &$error) {
	global $log;
	global $config;

	$file = $config['nsupdate_template']($update_info);
	$call = $config['nsupdate_call']($update_info, $file);

	$log->info("Running $call");

	exec($call, $out, $ret);
	$error = implode("\n", $out)."\nReturn Value: $ret";
	return $ret == 0;
}

// Authentication, exits if unsuccessful
// Return value check as failsave
if(authenticate()) {
	$update_info = get_nsupdate_options($error);

	if($update_info === false) {
		header('HTTP/1.0 400 Bad Request');
		echo $error;
		exit;
	}

	$log->info("Running nsupdate...");
	$result = run_nsupdate($update_info, $error);

	if($result) {
		header('HTTP/1.0 202 Accepted');
		$log->info("Domain change successfully requested. TTL: $update_info->ttl Output:\n$error");
		echo "Domain change successfully requested. TTL: $update_info->ttl Output:\n$error";
		exit;
	} else {
		header('HTTP/1.0 500 Internal Server Error');
		$log->error("Error running nsupdate:\n$error");
		echo "Error running nsupdate:\n$error";
		exit;
	}
}

?>
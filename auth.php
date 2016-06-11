<?php
/*
auth.php

Util functions for user authentication

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

function auth_header() {
	global $log;
	global $config;

	$realm = $config['realm'];
	header("WWW-Authenticate: Basic realm=\"$realm\"");
	header('HTTP/1.0 401 Unauthorized');
	echo 'Authentication Required';
	$log->info("Sending auth header...");
	exit;
}

function authenticate() {
	global $log;
	global $config;
	
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
		auth_header();
	} else {
		$log->info('Authentication start');
		$user = $_SERVER['PHP_AUTH_USER'];
		$pass = $_SERVER['PHP_AUTH_PW'];

		// Check User
		if(!isset($config['users'][$user])) {
			$log->warn("Authentication attempt with unknown user $user");
			auth_header();
		} else {
			$user_info = $config['users'][$user];

			// Check Password
			if(password_verify($pass, $user_info['password_hash'])) {
				$log->info("Authentication successful for user $user");
				return true;
			} else {
				$log->warn("Authentication attempt with incorrect password for user $user");
				auth_header();
			}
		}
	}
	// Failsave
	return false;
}
?>
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

Generate password hashes like this:

php -r "print password_hash('password-here', PASSWORD_BCRYPT).\"\n\";"


*/

// For testing: Password: password-here
$config['users']['nsuser'] = array('password_hash' => '$2y$10$vpnKpfWEEEWna3N25c.3ceOUiIHz2S7nEzWVtlMcGb1TFbDgxMK8S');

$config['domains']['domain.sub.example.com'] = new DomainInfo(
	'domain.sub.example.com',				// Domain
	'sub.example.com',						// Zone
	'ns1.sub.example.com',					// Nameserver
	'Ksub.example.com.private',				// Keyfile
	30										// TTL
	);

?>
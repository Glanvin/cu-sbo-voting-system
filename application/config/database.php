<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// DATABASE CONNECTIVITY SETTINGS Replaces the legacy `config.php` / `connection.php` pair, which hard coded SERVER_NAME, USERNAME, PASSWORD
// Replaces the legacy config.php / connection.php pair, which hard coded SERVER_NAME, USERNAME, PASSWORD and DB_NAME and opened a global

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => '127.0.0.1',
	'username' => 'root',
	'password' => '',
	'database' => 'db_voting_system',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,

	// Lets the dev/prod split in config.php decide how loudly a broken query announces itself instead of hard coding a dev default.
	'db_debug' => (ENVIRONMENT !== 'production'),

	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8mb4',
	'dbcollat' => 'utf8mb4_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

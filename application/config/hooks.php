<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// The legacy connection.php ran SET time_zone = '+08:00' on every request so MySQL's NOW() agreed with the open_at and close_at columns.
// CI3 has no config slot for a per connection session variable, so the statement is replayed here, before any controller runs.

$config['hooks'] = array();

$config['hooks']['pre_controller'][] = array(
	'class' => 'Db_timezone',
	'hook' => 'pin_timezone',
	'filename' => 'db_timezone.php',
	'filepath' => 'hooks/'
);

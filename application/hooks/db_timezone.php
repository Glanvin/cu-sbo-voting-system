<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Pins the MySQL session to +08:00 so NOW() agrees with the open_at and close_at values, which are naive Manila wall clock.
// Without it the two clocks can disagree and an open ballot reads as closed. See README.
class Db_timezone
{
	// Runs before the requested controller's constructor. Deliberately swallows failures: a MySQL build without the timezone tables loaded rejects
	// SET time_zone, and that must not take down every page in the app over a cosmetic offset.
	public function pin_timezone()
	{
		$CI =& get_instance();

		if ( ! isset($CI->db) OR ! is_object($CI->db))
		{
			return;
		}

		$CI->db->query("SET time_zone = '+08:00'");
	}
}

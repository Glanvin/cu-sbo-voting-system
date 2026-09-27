<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// A small CLI-only wrapper around the Migration library.
// Rationale: see README.md "Conversion notes and gotchas".
class Migrate extends CI_Controller
{
	// Loads the Migration library and refuses every non CLI request.
	public function __construct()
	{
		parent::__construct();

		if ( ! is_cli())
		{
			show_error('The migrator is only reachable from the command line.', 403);
		}

		// migration.php is not an autoload config, so the flag has to be read before the library will run. CI_Loader also finds config/migration.php
		// by itself when $params is left NULL, but the guard below has to come first, so it is loaded explicitly.
		$this->load->config('migration');

		if ($this->config->item('migration_enabled') !== TRUE)
		{
			show_error('Migrations are disabled in application/config/migration.php.', 500);
		}

		$this->load->library('migration');
	}

	// Migrates to the version configured in application/config/migration.php.
	public function index()
	{
		$this->report($this->migration->current(), 'current');
	}

	// The documented "migrate current" verb. Separate from index() only so the verb works; both call the same library method.
	public function current()
	{
		$this->index();
	}

	// Migrates to the newest migration file on disk.
	public function latest()
	{
		$this->report($this->migration->latest(), 'latest');
	}

	// Migrates to an explicit version, up or down.
	public function to($version = NULL)
	{
		if ($version === NULL OR ! ctype_digit($version))
		{
			$this->line('to needs a numeric version, e.g. migrate to 003');
			$this->fail();
			return;
		}

		$this->report($this->migration->version($version), 'to '.$version);
	}

	// Prints the current schema version without migrating anything. Migration::_get_version() is protected, so the migrations table is read with
	// the query builder instead.
	public function status()
	{
		$table = $this->config->item('migration_table');
		$version = 0;

		if ($this->db->table_exists($table))
		{
			$row = $this->db->select('version')
				->get($table)
				->row();

			if ($row)
			{
				$version = $row->version;
			}
		}

		$this->line('Current version:   ' . $version);
		$this->line('Configured target: ' . $this->config->item('migration_version'));
		$this->line('Files on disk:');

		foreach ($this->migration->find_migrations() as $number => $file)
		{
			$this->line('  ' . $number . '  ' . basename($file));
		}
	}

	// Reports the result of a migrator call and sets the exit status. The library writes its own progress to the CLI, so this only adds the result
	// line and the exit code a shell needs.
	protected function report($result, $command)
	{
		// version() returns TRUE when there was nothing to do, and the version string on success. Only FALSE is a failure, and error_string() carries
		// the reason.
		if ($result === FALSE)
		{
			$error = $this->migration->error_string();

			$this->line($command . ' failed: ' . ($error === '' ? 'no reason given' : $error));
			$this->line('Any steps that completed before the failure were committed.');
			$this->fail();
			return;
		}

		if ($result === TRUE)
		{
			$this->line($command . ': already at the target version, nothing to do.');
			return;
		}

		$this->line($command . ' ok. Schema is now at version ' . $result . '.');
	}

	// Writes one line to standard output.
	protected function line($message)
	{
		if (is_cli() && defined('STDOUT'))
		{
			fwrite(STDOUT, $message . PHP_EOL);
		}
	}

	// Ends the process with a failing status.
	// Rationale: see README.md "Conversion notes and gotchas".
	protected function fail()
	{
		if (is_cli() && defined('STDERR'))
		{
			fwrite(STDERR, PHP_EOL);
		}

		exit(1);
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Creates the `elections` table.
// Rationale: see README.md "Conversion notes and gotchas".
class Migration_Create_elections_table extends CI_Migration
{
	// Creates elections with its scheduling and manual close flag.
	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'name' => array('type' => 'VARCHAR', 'constraint' => 150),
			'department' => array('type' => 'VARCHAR', 'constraint' => 100),
			'open_at' => array('type' => 'DATETIME'),
			'close_at' => array('type' => 'DATETIME'),
			'is_closed' => array('type' => 'INT', 'constraint' => 1, 'default' => 0)
		));

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('elections', TRUE);
	}

	// Drops the elections table.
	public function down()
	{
		$this->dbforge->drop_table('elections', TRUE);
	}
}

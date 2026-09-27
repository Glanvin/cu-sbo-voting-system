<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Creates the `users` table.
// Rationale: see README.md "Conversion notes and gotchas".
class Migration_Create_users_table extends CI_Migration
{
	// Creates users with the identity, role and presence columns.
	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'school_id' => array('type' => 'VARCHAR', 'constraint' => 50, 'UNIQUE' => TRUE),
			'firstname' => array('type' => 'VARCHAR', 'constraint' => 50),
			'lastname' => array('type' => 'VARCHAR', 'constraint' => 50),
			'email' => array('type' => 'VARCHAR', 'constraint' => 100, 'UNIQUE' => TRUE),
			'password' => array('type' => 'VARCHAR', 'constraint' => 255),
			'role' => array('type' => 'ENUM', 'constraint' => array('student', 'moderator', 'admin'), 'default' => 'student'),
			'is_new' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 0),
			'is_online' => array('type' => 'INT', 'constraint' => 1, 'default' => 0),
			'session_id' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'last_activity' => array('type' => 'DATETIME', 'null' => TRUE),
			'department' => array('type' => 'VARCHAR', 'constraint' => 50)
		));

		// One add_key() call, and only one. Forge accumulates every add_key(..., TRUE) and implodes them into a single key
		// (DB_forge::_process_primary_keys), so 'id', 'school_id' and 'email' in turn would give PRIMARY KEY (id, school_id, email).
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('users', TRUE);
	}

	// Drops the users table.
	public function down()
	{
		$this->dbforge->drop_table('users', TRUE);
	}
}

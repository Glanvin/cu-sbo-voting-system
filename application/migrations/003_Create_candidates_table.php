<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Creates the `candidates` table.
// Rationale: see README.md "Conversion notes and gotchas".
class Migration_Create_candidates_table extends CI_Migration
{
	// Creates candidates, cascading deletions from their election.
	public function up()
	{
		// Forge has no foreign key support: a 'foreign_key' key inside add_field() is accepted silently and then discarded, because _attr_*() only
		// recognises a fixed set of attributes. The constraint is therefore added as an ALTER after the table exists.
		$this->load->helper('schema');

		$this->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'election_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE),
			'fullname' => array('type' => 'VARCHAR', 'constraint' => 150),
			'position' => array('type' => 'VARCHAR', 'constraint' => 100),
			'bio' => array('type' => 'TEXT'),
			'school_id' => array('type' => 'VARCHAR', 'constraint' => 50),
			'email' => array('type' => 'VARCHAR', 'constraint' => 100),
			'contact_number' => array('type' => 'VARCHAR', 'constraint' => 20)
		));

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('election_id');

		$this->dbforge->create_table('candidates', TRUE);

		// Deleting an election removes its whole slate in one statement.
		forge_add_foreign_key('candidates', array('election_id'), 'elections', array('id'), 'CASCADE', 'CASCADE');
	}

	// Drops the candidates table.
	public function down()
	{
		$this->dbforge->drop_table('candidates', TRUE);
	}
}

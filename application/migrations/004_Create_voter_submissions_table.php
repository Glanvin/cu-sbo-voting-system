<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Creates the `voter_submissions` table. One row per voter per election.
class Migration_Create_voter_submissions_table extends CI_Migration
{
	// Creates voter_submissions with its composite unique key.
	public function up()
	{
		// See application/helpers/schema_helper.php: Forge builds neither unique indexes nor foreign keys, so both are added as ALTER statements once
		// the table exists.
		$this->load->helper('schema');

		$this->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'election_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE),
			'voter_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE),
			'submitted_at' => array('type' => 'DATETIME', 'null' => TRUE)
		));

		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('voter_submissions', TRUE);

		// Across both columns, not on election_id alone, so one election can collect ballots from many different voters. This is the database half of
		// the "one ballot per voter" rule.
		forge_add_unique('voter_submissions', array('election_id', 'voter_id'));

		forge_add_foreign_key('voter_submissions', array('election_id'), 'elections', array('id'), 'CASCADE', 'CASCADE');
		forge_add_foreign_key('voter_submissions', array('voter_id'), 'users', array('id'), 'CASCADE', 'CASCADE');
	}

	// Drops the voter_submissions table.
	public function down()
	{
		$this->dbforge->drop_table('voter_submissions', TRUE);
	}
}

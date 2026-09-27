<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Creates the `votes` table.
// Rationale: see README.md "Conversion notes and gotchas".
class Migration_Create_votes_table extends CI_Migration
{
	// Creates votes with its composite unique key and nullable candidate.
	public function up()
	{
		// See application/helpers/schema_helper.php: Forge builds neither unique indexes nor foreign keys, so both are added as ALTER statements once
		// the table exists.
		$this->load->helper('schema');

		$this->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'election_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE),
			'voter_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE),
			'position' => array('type' => 'VARCHAR', 'constraint' => 100),

			// ON DELETE SET NULL: removing a candidate must not delete the votes cast for them, or a single admin action would silently rewrite a past
			// election's result. The row survives as an abstention and the tally reports the missing name.
			'candidate_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => TRUE),
			'voted_at' => array('type' => 'DATETIME', 'null' => TRUE)
		));

		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('votes', TRUE);

		// The database half of the "one vote per position" rule. The legacy app relied on has_submitted_ballot() being checked first, which two
		// concurrent submissions could both pass.
		forge_add_unique('votes', array('election_id', 'voter_id', 'position'));

		// candidate_id indexes the join in Vote_model::get_tally().
		$this->db->query('ALTER TABLE `votes` ADD INDEX `votes_candidate_id` (`candidate_id`)');

		forge_add_foreign_key('votes', array('election_id'), 'elections', array('id'), 'CASCADE', 'CASCADE');
		forge_add_foreign_key('votes', array('voter_id'), 'users', array('id'), 'CASCADE', 'CASCADE');
		forge_add_foreign_key('votes', array('candidate_id'), 'candidates', array('id'), 'SET NULL', 'CASCADE');
	}

	// Drops the votes table.
	public function down()
	{
		$this->dbforge->drop_table('votes', TRUE);
	}
}

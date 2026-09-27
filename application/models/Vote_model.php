<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Ballot recording, submission tracking and vote tallies.
// Rationale: see README.md "Conversion notes and gotchas".
class Vote_model extends CI_Model
{
	// Value posted for a position the voter deliberately left unselected. The ballot form renders an "Abstain" radio per position, so this is a
	// real choice the voter makes rather than a fallback for a missed one.
	const ABSTAIN = 'abstain';

	// Loads the database handle shared by every query in this model. The query builder is the only dependency; no external libraries.
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	// Records one position's choice. A numeric value is a candidate id and anything else is an abstention, which is stored as NULL.
	public function cast($election_id, $voter_id, $position, $choice)
	{
		$is_abstain = ! is_numeric($choice) OR (int) $choice < 1;

		return $this->db->insert('votes', array(
			'election_id' => (int) $election_id,
			'voter_id' => (int) $voter_id,
			'position' => $position,
			'candidate_id' => $is_abstain ? NULL : (int) $choice,
			'voted_at' => date('Y-m-d H:i:s')
		));
	}

	// Flags a whole ballot as submitted. Separate from the individual vote rows because this is the row the dashboard counts and the receipt looks
	// up.
	public function mark_submitted($election_id, $voter_id)
	{
		return $this->db->insert('voter_submissions', array(
			'election_id' => (int) $election_id,
			'voter_id' => (int) $voter_id,
			'submitted_at' => date('Y-m-d H:i:s')
		));
	}

	// Whether this voter has already submitted a ballot for this election.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function has_submitted($election_id, $voter_id)
	{
		$this->db
			->where('election_id', (int) $election_id)
			->where('voter_id', (int) $voter_id);

		return $this->db->count_all_results('voter_submissions') > 0;
	}

	// One voter's recorded choices for an election, for their receipt. The candidate join is a left join so an abstention, or a vote whose
	// candidate has since been deleted, still returns a row with a NULL candidate_name rather than dropping out of the receipt.
	public function get_ballot_of($election_id, $voter_id)
	{
		return $this->db
			->select('v.position, v.candidate_id, v.voted_at, c.fullname AS candidate_name')
			->from('votes v')
			->join('candidates c', 'v.candidate_id = c.id', 'left')
			->where('v.election_id', (int) $election_id)
			->where('v.voter_id', (int) $voter_id)
			->order_by('v.position', 'ASC')
			->get()
			->result_array();
	}

	// When this voter submitted their ballot for an election.
	public function get_submitted_at($election_id, $voter_id)
	{
		$this->db
			->select('submitted_at')
			->where('election_id', (int) $election_id)
			->where('voter_id', (int) $voter_id);

		return $this->db->get('voter_submissions')->row_array();
	}

	// Votes per candidate for an election, grouped into positions. The left join with the candidate id and election id in the ON clause is what
	// makes a candidate with no votes appear with a count of zero.
	public function get_tally($election_id)
	{
		$rows = $this->db
			->select('c.position, c.fullname, c.id AS candidate_id, COUNT(v.id) AS votes')
			->from('candidates c')
			->join('votes v', 'v.candidate_id = c.id AND v.election_id = ' . (int) $election_id, 'left')
			->where('c.election_id', (int) $election_id)
			->group_by(array('c.id', 'c.position', 'c.fullname'))
			->order_by('c.position', 'ASC')
			->order_by('votes', 'DESC')
			->get()
			->result_array();

		$tally = array();

		foreach ($rows as $row)
		{
			$tally[$row['position']][] = $row;
		}

		return $tally;
	}

	// How many positions in this election were left blank.
	public function count_abstains($election_id, $position)
	{
		return (int) $this->db
			->where('election_id', (int) $election_id)
			->where('position', $position)
			->where('candidate_id IS NULL', NULL, FALSE)
			->count_all_results('votes');
	}

	// How many ballots have been submitted for an election.
	public function count_submissions($election_id)
	{
		return (int) $this->db
			->where('election_id', (int) $election_id)
			->count_all_results('voter_submissions');
	}

	// How many students are entitled to vote in an election's department. A University Wide election is open to every student regardless of
	// college, so it takes the first branch.
	public function count_eligible_voters($election_department)
	{
		$this->db->where('role', 'student');

		if ($election_department !== \Election_model::UNIVERSITY_WIDE)
		{
			$this->db->where('department', $election_department);
		}

		return (int) $this->db->count_all_results('users');
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Candidate records for an election, and the position grouping the ballot uses.
// Rationale: see README.md "Conversion notes and gotchas".
class Candidate_model extends CI_Model
{
	// Loads the database handle shared by every query in this model. The query builder is the only dependency; no external libraries.
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	// Every candidate with its election name, for the admin table.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function get_all($election_id = NULL)
	{
		$this->db->select('c.*, e.name AS election_name, e.is_closed, e.open_at, e.close_at');
		$this->db->from('candidates c');
		$this->db->join('elections e', 'c.election_id = e.id');

		if ( ! empty($election_id) AND (int) $election_id > 0)
		{
			$this->db->where('c.election_id', (int) $election_id);
		}

		return $this->db
			->order_by('e.open_at', 'DESC')
			->order_by('c.position', 'ASC')
			->order_by('c.fullname', 'ASC')
			->get()
			->result_array();
	}

	// Candidates for one election grouped into their positions.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function get_grouped_by_position($election_id)
	{
		$rows = $this->db
			->where('election_id', (int) $election_id)
			->order_by('position', 'ASC')
			->order_by('fullname', 'ASC')
			->get('candidates')
			->result_array();

		$grouped = array();

		foreach ($rows as $row)
		{
			$grouped[$row['position']][] = $row;
		}

		return $grouped;
	}

	// Fetches a single candidate by id.
	public function get_by_id($id)
	{
		return $this->db->where('id', (int) $id)->get('candidates')->row_array();
	}

	// Counts candidates, optionally scoped to one election.
	public function count($election_id = NULL)
	{
		if ( ! empty($election_id))
		{
			$this->db->where('election_id', (int) $election_id);
		}

		return (int) $this->db->count_all_results('candidates');
	}

	// Creates a candidate.
	public function create($data)
	{
		return $this->db->insert('candidates', $data);
	}

	// Updates a candidate, including which election they belong to.
	public function update($id, $data)
	{
		return $this->db->where('id', (int) $id)->update('candidates', $data);
	}

	// Permanently deletes a candidate. The votes FK is ON DELETE SET NULL, so any votes already cast for this candidate survive as abstentions
	// rather than vanishing from the tally.
	public function delete($id)
	{
		return $this->db->where('id', (int) $id)->delete('candidates');
	}
}

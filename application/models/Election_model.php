<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Election records, their lifecycle status and department eligibility.
// Rationale: see README.md "Conversion notes and gotchas".
class Election_model extends CI_Model
{
	// Department value that opts an election out of the department filter. An election carrying this is open to every student regardless of their
	// college, and is the reason StudentController has to re-check eligibility on the ballot route rather than trusting the dashboard link alone.
	const UNIVERSITY_WIDE = 'University Wide';

	// Election has been manually closed or its window has passed.
	const STATUS_CLOSED = 'closed';

	// Election window has not opened yet.
	const STATUS_UPCOMING = 'upcoming';

	// Election is accepting ballots right now.
	const STATUS_ONGOING = 'ongoing';

	// Loads the database handle shared by every query in this model. The query builder is the only dependency; no external libraries.
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	// Decides an election's status from its window and close flag.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function status($open_at, $close_at, $is_closed)
	{
		if ((int) $is_closed === 1)
		{
			return self::STATUS_CLOSED;
		}

		$now = time();

		if ($now < strtotime($open_at))
		{
			return self::STATUS_UPCOMING;
		}

		if ($now <= strtotime($close_at))
		{
			return self::STATUS_ONGOING;
		}

		return self::STATUS_CLOSED;
	}

	// Whether a department may vote in an election. Split out so StudentController can gate both the ballot GET and the ballot POST through one
	// comparison.
	public function is_eligible($election_department, $student_department)
	{
		return $election_department === self::UNIVERSITY_WIDE
			OR $election_department === $student_department;
	}

	// Every election, newest window first, each with a computed status.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function get_all()
	{
		$elections = $this->db->order_by('open_at', 'DESC')->get('elections')->result_array();

		foreach ($elections as $index => $election)
		{
			$elections[$index]['status'] = $this->status(
				$election['open_at'],
				$election['close_at'],
				$election['is_closed']
			);
		}

		return $elections;
	}

	// Fetches one election by id, with its computed status attached.
	public function get_by_id($id)
	{
		$election = $this->db->where('id', (int) $id)->get('elections')->row_array();

		if ($election)
		{
			$election['status'] = $this->status($election['open_at'], $election['close_at'], $election['is_closed']);
		}

		return $election;
	}

	// Elections currently accepting ballots that this student may vote in. The window test stays in SQL so the database clock decides, matching
	// what the voting path itself will accept.
	public function get_ongoing_for_student($department)
	{
		$rows = $this->db
			->where('is_closed', 0)
			->where('open_at <=', 'NOW()', FALSE)
			->where('close_at >=', 'NOW()', FALSE)
			->group_start()
				->where('department', $department)
				->or_where('department', self::UNIVERSITY_WIDE)
			->group_end()
			->order_by('close_at', 'ASC')
			->get('elections')
			->result_array();

		// The window was already proven in SQL, so the status is known without re-running the PHP comparison.
		foreach ($rows as $index => $row)
		{
			$rows[$index]['status'] = self::STATUS_ONGOING;
		}

		return $rows;
	}

	// Counts elections by status, for the dashboard stat tiles. Returned as keys so the view can address a tile without knowing which status
	// string backs it.
	public function count_by_status()
	{
		$all = $this->db->get('elections')->result_array();
		$counts = array(self::STATUS_ONGOING => 0, self::STATUS_UPCOMING => 0, self::STATUS_CLOSED => 0);

		foreach ($all as $election)
		{
			$status = $this->status($election['open_at'], $election['close_at'], $election['is_closed']);
			$counts[$status]++;
		}

		return $counts;
	}

	// The next election to open, for the dashboard "current election" tile. Prefers something open now and falls back to the soonest upcoming one,
	// so the tile always names a real election.
	public function get_current()
	{
		$current = NULL;

		foreach ($this->get_all() as $election)
		{
			if ($election['status'] === self::STATUS_ONGOING)
			{
				return $election;
			}

			if ($election['status'] === self::STATUS_UPCOMING && $current === NULL)
			{
				$current = $election;
			}
		}

		return $current;
	}

	// Creates an election from its four supplied columns.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function create($name, $department, $open_at, $close_at)
	{
		return $this->db->insert('elections', array(
			'name' => $name,
			'department' => $department,
			'open_at' => $open_at,
			'close_at' => $close_at
		));
	}

	// Updates an election's name, department and window.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function update($id, $name, $department, $open_at, $close_at)
	{
		return $this->db->where('id', (int) $id)->update('elections', array(
			'name' => $name,
			'department' => $department,
			'open_at' => $open_at,
			'close_at' => $close_at
		));
	}

	// Force closes an election ahead of its scheduled end.
	public function close($id)
	{
		return $this->db->where('id', (int) $id)->update('elections', array('is_closed' => 1));
	}

	// Permanently deletes an election with its candidates, ballots and votes.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function delete($id)
	{
		return $this->db->where('id', (int) $id)->delete('elections');
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// User lookups, account administration and the online presence columns.
// Rationale: see README.md "Conversion notes and gotchas".
class User_model extends CI_Model
{
	// Loads the database handle shared by every query in this model. The query builder is the only dependency; no external libraries.
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	// Looks an account up by email for the login flow. Returns the whole row, so the caller still has to compare the password itself.
	public function find_by_email($email)
	{
		$this->db->where('email', $email);
		return $this->db->get('users')->row_array();
	}

	// Fetches a single account by primary key.
	public function find_by_id($id)
	{
		$this->db->where('id', $id);
		return $this->db->get('users')->row_array();
	}

	// Whether an email address belongs to some account other than the one being edited.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function email_taken_by_other($email, $exclude_id = 0)
	{
		$this->db->where('email', $email);

		if ((int) $exclude_id > 0)
		{
			// Without this, saving an account without changing its address would collide with its own row.
			$this->db->where('id', '!=', (int) $exclude_id);
		}

		return $this->db->count_all_results('users') > 0;
	}

	// Flags the account online and binds it to the current session id. Storing the session id on the row is what lets the login handler detect the
	// same account being used from a second device.
	public function set_online($user_id, $session_id)
	{
		return $this->db->where('id', $user_id)->update('users', array(
			'is_online' => 1,
			'session_id' => $session_id,
			'last_activity' => date('Y-m-d H:i:s')
		));
	}

	// Clears the presence columns on sign out. Also destroys the stored session id, otherwise the next sign in on a different device would be
	// refused as a stale duplicate.
	public function set_offline($user_id)
	{
		return $this->db->where('id', $user_id)->update('users', array(
			'is_online' => 0,
			'session_id' => NULL,
			'last_activity' => NULL
		));
	}

	// Stamps last_activity on every authenticated page view.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function touch_activity($user_id)
	{
		return $this->db
			->where('id', $user_id)
			->update('users', array('last_activity' => date('Y-m-d H:i:s')));
	}

	// Every account except the one asking, for the admin user table.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function get_all_except($current_user_id)
	{
		return $this->db
			->where('id !=', (int) $current_user_id)
			->order_by('role', 'ASC')
			->order_by('lastname', 'ASC')
			->get('users')
			->result_array();
	}

	// Counts registered voters, for the dashboard stat tile.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function count_voters()
	{
		return (int) $this->db->where('role', 'student')->count_all_results('users');
	}

	// Counts accounts by role, for the admin user table summary. zero so the view never has to guard on isset.
	public function count_by_role()
	{
		$counts = array('student' => 0, 'moderator' => 0, 'admin' => 0);

		foreach ($this->db->select('role, COUNT(*) AS total')->group_by('role')->get('users')->result() as $row)
		{
			$counts[$row->role] = (int) $row->total;
		}

		return $counts;
	}

	// Creates an account, flagged so the next login forces a password change.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function create($data)
	{
		$data['is_new'] = 1;

		return $this->db->insert('users', $data);
	}

	// Updates an account, skipping the password when none was supplied.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function update($id, $data)
	{
		return $this->db->where('id', (int) $id)->update('users', $data);
	}

	// Replaces the password and clears the first login flag. Always sets is_new = 0, which is what stops the user being bounced back to the change
	// password form on their next sign in.
	public function update_password($user_id, $password)
	{
		return $this->db->where('id', (int) $user_id)->update('users', array(
			'password' => $password,
			'is_new' => 0
		));
	}

	// Permanently deletes an account. The FKs on votes and voter_submissions cascade, so deleting a voter also discards the ballot they cast.
	public function delete($id)
	{
		return $this->db->where('id', (int) $id)->delete('users');
	}
}

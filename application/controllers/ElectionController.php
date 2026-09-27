<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Admin only election scheduling and lifecycle.
// Rationale: see README.md "Conversion notes and gotchas".
class ElectionController extends CI_Controller
{
	// Requires an admin, then loads the election model and the shared rule set. The guard is inline rather than inherited.
	public function __construct()
	{
		parent::__construct();
		$this->guard_admin();

		$this->load->model('Election_model');
		$this->load->model('Candidate_model');
		$this->load->model('User_model');
		$this->config->load('form_rules');
	}

	// Redirects unless the session holds an admin.
	private function guard_admin()
	{
		if ( ! $this->session->userdata('user_id'))
		{
			redirect('auth/login');
		}

		if ($this->session->userdata('role') !== 'admin')
		{
			redirect('dashboard');
		}
	}

	// Renders the election table and the create/edit form.
	public function index()
	{
		$this->User_model->touch_activity((int) $this->session->userdata('user_id'));

		$edit_id = (int) $this->input->get('edit', TRUE);

		$data = array(
			'title' => 'Elections - CU SBO Voting System',
			'page_heading' => 'Elections',
			'active' => 'elections',
			'user_name' => $this->display_name(),
			'elections' => $this->elections_with_counts(),
			'departments' => $this->config->item('departments'),
			'message' => $this->session->flashdata('message'),
			'edit_election' => $edit_id ? $this->edit_row($edit_id) : NULL
		);

		$this->load->view('templates/staff_header', $data);
		$this->load->view('elections/index', $data);
		$this->load->view('templates/footer', $data);
	}

	// Creates or updates an election from one shared form. The window is checked before the model is touched, so an election that closes before it
	// opens never reaches the database.
	public function save()
	{
		$this->form_validation->set_rules($this->config->item('voting_election'));
		$this->form_validation->set_error_delimiters('', '');

		$election_id = (int) $this->input->post('election_id', TRUE);

		if ($this->form_validation->run() !== TRUE)
		{
			$this->render_with_errors($election_id);
			return;
		}

		$name = $this->input->post('election_name', TRUE);
		$department = $this->input->post('department', TRUE);
		$open_at = $this->to_mysql($this->input->post('open_at', TRUE));
		$close_at = $this->to_mysql($this->input->post('close_at', TRUE));

		// Re-checked here as well as in the callback: the callback only guards the open_at field, and this is the point where both normalised values
		// are actually known.
		if (strtotime($close_at) <= strtotime($open_at))
		{
			$this->session->set_flashdata('message', 'Error: the closing time must be after the opening time.');
			redirect('elections');
		}

		if ($election_id)
		{
			$existing = $this->Election_model->get_by_id($election_id);

			if ( ! $existing)
			{
				$this->session->set_flashdata('message', 'That election no longer exists.');
				redirect('elections');
			}

			// The legacy updateElection() documented that it "should not be called on ongoing elections" but never checked, so an admin could move a
			// window out from under voters who had the ballot open.
			if ($existing['status'] === Election_model::STATUS_ONGOING)
			{
				$this->session->set_flashdata('message', 'Error: an election that is already open cannot be edited. Close it first.');
				redirect('elections');
			}

			$ok = $this->Election_model->update($election_id, $name, $department, $open_at, $close_at);
			$message = $ok
				? "Election '$name' has been successfully updated!"
				: 'Error updating election.';
		}
		else
		{
			$ok = $this->Election_model->create($name, $department, $open_at, $close_at);
			$message = $ok
				? "Election '$name' has been successfully created!"
				: 'Error creating election.';
		}

		$this->session->set_flashdata('message', $message);
		redirect('elections');
	}

	// Force closes an election ahead of its scheduled end.
	public function close()
	{
		$election_id = (int) $this->input->post('election_id', TRUE);

		if ( ! $election_id OR ! $this->Election_model->get_by_id($election_id))
		{
			$this->session->set_flashdata('message', 'That election no longer exists.');
			redirect('elections');
		}

		if ($this->Election_model->close($election_id))
		{
			$this->session->set_flashdata('message', 'The election has been closed early.');
		}
		else
		{
			$this->session->set_flashdata('message', 'Error closing election.');
		}

		redirect('elections');
	}

	// Permanently deletes an election and everything under it. Deleting an election that is still open would strand voters holding the ballot
	// link, so only closed elections are removable.
	public function delete()
	{
		$election_id = (int) $this->input->post('election_id', TRUE);
		$election = $election_id ? $this->Election_model->get_by_id($election_id) : NULL;

		if ( ! $election)
		{
			$this->session->set_flashdata('message', 'That election no longer exists.');
			redirect('elections');
		}

		if ($election['status'] !== Election_model::STATUS_CLOSED)
		{
			$this->session->set_flashdata('message', 'Error: only a closed election can be deleted. Close it first.');
			redirect('elections');
		}

		if ($this->Election_model->delete($election_id))
		{
			$this->session->set_flashdata('message', 'Election record has been permanently deleted.');
		}
		else
		{
			$this->session->set_flashdata('message', 'Error deleting election.');
		}

		redirect('elections');
	}

	// Converts a datetime-local value to MySQL DATETIME.
	// Rationale: see README.md "Conversion notes and gotchas".
	private function to_mysql($value)
	{
		$value = str_replace('T', ' ', trim($value));

		if ($value === '')
		{
			return NULL;
		}

		// datetime-local omits the seconds, so supply them before parsing.
		if (strlen($value) === 16)
		{
			$value .= ':00';
		}

		$timestamp = strtotime($value);

		return $timestamp === FALSE ? NULL : date('Y-m-d H:i:s', $timestamp);
	}

	// Form callback: an election must close after it opens. Replaces the JavaScript setCustomValidity() on the close_at input, which was the only
	// place this rule existed and could be skipped with a direct POST.
	public function _valid_window($value)
	{
		$open_at = $this->to_mysql($value);
		$close_at = $this->to_mysql((string) $this->input->post('close_at', TRUE));

		// close_at has its own required rule and is checked separately, so a blank value here is not this callback's error to report.
		if ( ! $open_at OR ! $close_at)
		{
			return TRUE;
		}

		if (strtotime($close_at) <= strtotime($open_at))
		{
			$this->form_validation->set_message('_valid_window', 'The closing time must be after the opening time.');
			return FALSE;
		}

		return TRUE;
	}

	// Re-renders the election page with validation errors and posted values.
	private function render_with_errors($election_id)
	{
		$posted = array(
			'election_id' => $election_id,
			'name' => (string) $this->input->post('election_name', TRUE),
			'department' => (string) $this->input->post('department', TRUE),
			'open_at' => (string) $this->input->post('open_at', TRUE),
			'close_at' => (string) $this->input->post('close_at', TRUE)
		);

		$data = array(
			'title' => 'Elections - CU SBO Voting System',
			'page_heading' => 'Elections',
			'active' => 'elections',
			'user_name' => $this->display_name(),
			'elections' => $this->elections_with_counts(),
			'departments' => $this->config->item('departments'),
			'message' => validation_errors() ?: 'Please correct the highlighted fields.',
			'edit_election' => $election_id ? $posted : NULL
		);

		$this->load->view('templates/staff_header', $data);
		$this->load->view('elections/index', $data);
		$this->load->view('templates/footer', $data);
	}

	// Every election with its candidate count attached.
	// Rationale: see README.md "Conversion notes and gotchas".
	private function elections_with_counts()
	{
		$elections = $this->Election_model->get_all();

		$counts = array();

		foreach ($this->Candidate_model->get_all() as $candidate)
		{
			$key = (int) $candidate['election_id'];
			$counts[$key] = isset($counts[$key]) ? $counts[$key] + 1 : 1;
		}

		foreach ($elections as $index => $election)
		{
			$id = (int) $election['id'];
			$elections[$index]['candidate_count'] = isset($counts[$id]) ? $counts[$id] : 0;
		}

		return $elections;
	}

	// Fetches an election for the edit form, under the key the form uses. The model's row names its primary key 'id', but the form and the POST
	// both call it 'election_id', so the alias is added here.
	private function edit_row($edit_id)
	{
		$row = $this->Election_model->get_by_id($edit_id);

		if ($row)
		{
			$row['election_id'] = $row['id'];
		}

		return $row;
	}

	// The signed in user's name for the topbar.
	private function display_name()
	{
		return trim($this->session->userdata('firstname') . ' ' . $this->session->userdata('lastname'));
	}
}

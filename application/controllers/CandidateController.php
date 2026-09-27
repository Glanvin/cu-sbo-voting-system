<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Candidate administration, open to admins and moderators.
// Rationale: see README.md "Conversion notes and gotchas".
class CandidateController extends CI_Controller
{
	// Requires an admin or moderator, then loads the models the page needs. The guard is inline rather than inherited.
	public function __construct()
	{
		parent::__construct();
		$this->guard_staff();

		$this->load->model('Candidate_model');
		$this->load->model('Election_model');
		$this->load->model('User_model');
		$this->config->load('form_rules');
	}

	// Redirects unless the session holds an admin or moderator.
	private function guard_staff()
	{
		if ( ! $this->session->userdata('user_id'))
		{
			redirect('auth/login');
		}

		if ( ! in_array($this->session->userdata('role'), array('admin', 'moderator'), TRUE))
		{
			redirect('student');
		}
	}

	// Renders the candidate table and the add/edit form.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function index()
	{
		$this->User_model->touch_activity((int) $this->session->userdata('user_id'));

		$election_id = (int) $this->input->get('election', TRUE);
		$search = trim((string) $this->input->get('q', TRUE));
		$edit_id = (int) $this->input->get('edit', TRUE);

		$candidates = $this->Candidate_model->get_all($election_id);

		if ($search !== '')
		{
			$candidates = array_values(array_filter($candidates, function ($candidate) use ($search) {
				return stripos($candidate['fullname'], $search) !== FALSE
					OR stripos($candidate['election_name'], $search) !== FALSE;
			}));
		}

		$data = array(
			'title' => 'Candidates - CU SBO Voting System',
			'page_heading' => 'Candidates',
			'active' => 'candidates',
			'user_name' => $this->display_name(),
			'can_delete' => $this->session->userdata('role') === 'admin',
			'candidates' => $candidates,
			'elections' => $this->Election_model->get_all(),
			'positions' => $this->config->item('positions'),
			'filter_election' => $election_id,
			'search' => $search,
			'message' => $this->session->flashdata('message'),
			'edit_candidate' => $edit_id ? $this->edit_row($edit_id) : NULL
		);

		$this->load->view('templates/staff_header', $data);
		$this->load->view('candidates/index', $data);
		$this->load->view('templates/footer', $data);
	}

	// Creates or updates a candidate from one shared form.
	public function save()
	{
		$this->form_validation->set_rules($this->config->item('voting_candidate'));
		$this->form_validation->set_error_delimiters('', '');

		$candidate_id = (int) $this->input->post('candidate_id', TRUE);

		if ($this->form_validation->run() !== TRUE)
		{
			$this->render_with_errors($candidate_id);
			return;
		}

		$election_id = (int) $this->input->post('election_id', TRUE);

		// The rule set proves the id is numeric but not that it exists, and a candidate pointing at a missing election would be an orphan.
		if ( ! $this->Election_model->get_by_id($election_id))
		{
			$this->session->set_flashdata('message', 'Error: the selected election no longer exists.');
			redirect('candidates');
		}

		$data = array(
			'election_id' => $election_id,
			'fullname' => $this->input->post('fullname', TRUE),
			'position' => $this->input->post('position', TRUE),
			'bio' => $this->input->post('bio', TRUE),
			'school_id' => $this->input->post('school_id', TRUE),
			'email' => $this->input->post('email', TRUE),
			'contact_number' => $this->input->post('contact_number', TRUE)
		);

		$fullname = $data['fullname'];

		if ($candidate_id)
		{
			$ok = $this->Candidate_model->update($candidate_id, $data);
			$message = $ok
				? "Candidate '$fullname' has been successfully updated."
				: 'Error updating candidate.';
		}
		else
		{
			$ok = $this->Candidate_model->create($data);
			$message = $ok
				? "Candidate '$fullname' has been successfully added."
				: 'Error adding candidate.';
		}

		$this->session->set_flashdata('message', $message);
		redirect('candidates');
	}

	// Permanently deletes a candidate.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function delete()
	{
		if ($this->session->userdata('role') !== 'admin')
		{
			$this->session->set_flashdata('message', 'Access denied: moderators cannot delete candidates.');
			redirect('candidates');
		}

		$candidate_id = (int) $this->input->post('candidate_id', TRUE);
		$candidate = $candidate_id ? $this->Candidate_model->get_by_id($candidate_id) : NULL;

		if ( ! $candidate)
		{
			$this->session->set_flashdata('message', 'That candidate no longer exists.');
			redirect('candidates');
		}

		if ($this->Candidate_model->delete($candidate_id))
		{
			$this->session->set_flashdata('message', "Candidate '{$candidate['fullname']}' has been permanently deleted.");
		}
		else
		{
			$this->session->set_flashdata('message', 'Error deleting candidate.');
		}

		redirect('candidates');
	}

	// Re-renders the candidate page with errors and posted values.
	private function render_with_errors($candidate_id)
	{
		$posted = array(
			'candidate_id' => $candidate_id,
			'election_id' => (int) $this->input->post('election_id', TRUE),
			'fullname' => (string) $this->input->post('fullname', TRUE),
			'position' => (string) $this->input->post('position', TRUE),
			'bio' => (string) $this->input->post('bio', TRUE),
			'school_id' => (string) $this->input->post('school_id', TRUE),
			'email' => (string) $this->input->post('email', TRUE),
			'contact_number' => (string) $this->input->post('contact_number', TRUE)
		);

		$data = array(
			'title' => 'Candidates - CU SBO Voting System',
			'page_heading' => 'Candidates',
			'active' => 'candidates',
			'user_name' => $this->display_name(),
			'can_delete' => $this->session->userdata('role') === 'admin',
			'candidates' => $this->Candidate_model->get_all(),
			'elections' => $this->Election_model->get_all(),
			'positions' => $this->config->item('positions'),
			'filter_election' => 0,
			'search' => '',
			'message' => validation_errors() ?: 'Please correct the highlighted fields.',
			'edit_candidate' => $candidate_id ? $posted : NULL
		);

		$this->load->view('templates/staff_header', $data);
		$this->load->view('candidates/index', $data);
		$this->load->view('templates/footer', $data);
	}

	// Fetches a candidate for the edit form, under the key the form uses. The model's row names its primary key 'id', but the form and the POST
	// both call it 'candidate_id', so the alias is added here.
	private function edit_row($edit_id)
	{
		$row = $this->Candidate_model->get_by_id($edit_id);

		if ($row)
		{
			$row['candidate_id'] = $row['id'];
		}

		return $row;
	}

	// The signed in user's name for the topbar.
	private function display_name()
	{
		return trim($this->session->userdata('firstname') . ' ' . $this->session->userdata('lastname'));
	}
}

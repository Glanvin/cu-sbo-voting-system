<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// The student area: election list, ballot and receipt.
// Rationale: see README.md "Conversion notes and gotchas".
class StudentController extends CI_Controller
{
	// Requires a signed in student, then loads the four models the three actions need. Inline guard, matching the rest of the app.
	public function __construct()
	{
		parent::__construct();
		$this->guard_student();

		$this->load->model('Election_model');
		$this->load->model('Candidate_model');
		$this->load->model('Vote_model');
		$this->load->model('User_model');
	}

	// Redirects unless the session holds a student. Staff are sent to their own dashboard rather than to sign in, since they are authenticated and
	// simply in the wrong area.
	private function guard_student()
	{
		if ( ! $this->session->userdata('user_id'))
		{
			redirect('auth/login');
		}

		if ($this->session->userdata('role') !== 'student')
		{
			redirect('dashboard');
		}
	}

	// Lists the elections this student can currently vote in.
	public function index()
	{
		$this->User_model->touch_activity((int) $this->session->userdata('user_id'));

		$voter_id = (int) $this->session->userdata('user_id');
		$elections = $this->Election_model->get_ongoing_for_student($this->session->userdata('department'));

		// Each card says whether the student has already voted, so a finished election links to the receipt instead of the ballot.
		foreach ($elections as $index => $election)
		{
			$elections[$index]['has_voted'] = $this->Vote_model->has_submitted($election['id'], $election_id);
			$elections[$index]['positions'] = $this->Candidate_model->count($election['id']);
		}

		$data = array(
			'title' => 'My Elections - CU SBO Voting System',
			'page_heading' => 'My Elections',
			'active' => 'home',
			'user_name' => $this->display_name(),
			'elections' => $elections
		);

		$this->load->view('templates/student_header', $data);
		$this->load->view('student/dashboard', $data);
		$this->load->view('templates/footer', $data);
	}

	// Renders the ballot for one election.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function ballot($election_id)
	{
		$this->User_model->touch_activity((int) $this->session->userdata('user_id'));

		$voter_id = (int) $this->session->userdata('user_id');
		$election = $this->election_for_ballot($election_id, $voter_id);

		// Reached only when the voter has not already submitted.
		$data = array(
			'title' => 'Ballot - CU SBO Voting System',
			'page_heading' => 'Cast your ballot',
			'active' => 'ballot',
			'user_name' => $this->display_name(),
			'election' => $election,
			'grouped' => $this->Candidate_model->get_grouped_by_position($election['id'])
		);

		$this->load->view('templates/student_header', $data);
		$this->load->view('student/ballot', $data);
		$this->load->view('templates/footer', $data);
	}

	// Records a submitted ballot and sends the voter to their receipt.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function submit_ballot()
	{
		$voter_id = (int) $this->session->userdata('user_id');

		// election_id arrives in the POST body rather than the URI because the form posts to its own action.
		$election_id = (int) $this->input->post('election_id', TRUE);
		$election = $this->election_for_ballot($election_id, $voter_id);
		$grouped = $this->Candidate_model->get_grouped_by_position($election['id']);

		// vote[position] => candidate id, or the abstain sentinel.
		$choices = (array) $this->input->post('vote', TRUE);

		$this->db->trans_start();

		foreach ($grouped as $position => $candidates)
		{
			$choice = isset($choices[$position]) ? $choices[$position] : Vote_model::ABSTAIN;

			// A position only accepts a candidate that is actually running in this election, so a crafted id cannot vote for someone off the ballot, nor
			// for a candidate in a different election.
			if (is_numeric($choice) && ! $this->is_on_ballot($candidates, (int) $choice))
			{
				$choice = Vote_model::ABSTAIN;
			}

			$this->Vote_model->cast($election['id'], $voter_id, $position, $choice);
		}

		$this->Vote_model->mark_submitted($election['id'], $voter_id);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			// Most likely a concurrent submission won the unique key race.
			$this->session->set_flashdata('message', 'Your ballot could not be recorded. It may already have been submitted.');
			redirect('student');
		}

		// Lets the receipt tell a first view from a later revisit.
		$this->session->set_flashdata('receipt_fresh', TRUE);

		redirect('student/receipt/' . $election['id']);
	}

	// Shows the receipt for a ballot this student already submitted. The readme specifies the receipt confirms submission without revealing the
	// Scoped to the signed in voter: the query filters on voter_id as well as election_id, so another voter's receipt is refused, not displayed.
	public function receipt($election_id)
	{
		$this->User_model->touch_activity((int) $this->session->userdata('user_id'));

		$voter_id = (int) $this->session->userdata('user_id');
		$election = $this->Election_model->get_by_id($election_id);

		// Unlike the ballot, the receipt is still reachable after the election closes, so a missing election is the only 404 here.
		if ( ! $election)
		{
			show_error('Election not found.');
		}

		if ( ! $this->Vote_model->has_submitted($election['id'], $voter_id))
		{
			$this->session->set_flashdata('message', 'You have not submitted a ballot for this election.');
			redirect('student');
		}

		$ballot = $this->Vote_model->get_ballot_of($election['id'], $voter_id);
		$submission = $this->Vote_model->get_submitted_at($election['id'], $voter_id);
		$voter = $this->User_model->find_by_id($voter_id);

		$abstained = 0;

		foreach ($ballot as $row)
		{
			if ($row['candidate_id'] === NULL)
			{
				$abstained++;
			}
		}

		$data = array(
			'title' => 'Receipt - CU SBO Voting System',
			'page_heading' => 'Ballot Receipt',
			'active' => 'receipt',
			'user_name' => $this->display_name(),
			'election' => $election,
			'ballot' => $ballot,
			'submitted_at' => isset($submission['submitted_at']) ? $submission['submitted_at'] : NULL,
			'fresh' => (bool) $this->session->flashdata('receipt_fresh'),
			'school_id' => $voter ? $voter['school_id'] : '—',
			'department' => $voter ? $voter['department'] : '—',
			'total' => count($ballot),
			'voted' => count($ballot) - $abstained,
			'abstained' => $abstained,
			'message' => $this->session->flashdata('message')
		);

		$this->load->view('templates/student_header', $data);
		$this->load->view('student/receipt', $data);
		$this->load->view('templates/footer', $data);
	}

	// Loads an election the voter is currently allowed to fill in a ballot for.
	// Rationale: see README.md "Conversion notes and gotchas".
	private function election_for_ballot($election_id, $voter_id)
	{
		$election = $election_id ? $this->Election_model->get_by_id($election_id) : NULL;

		if ( ! $election OR $election['status'] !== Election_model::STATUS_ONGOING)
		{
			redirect('student');
		}

		// University Wide is handled inside is_eligible().
		if ( ! $this->Election_model->is_eligible($election['department'], $this->session->userdata('department')))
		{
			redirect('student');
		}

		// Already voted: send them to the receipt instead of a second ballot, rather than letting the unique key fail later.
		if ($this->Vote_model->has_submitted($election['id'], $voter_id))
		{
			redirect('student/receipt/' . $election['id']);
		}

		return $election;
	}

	// Whether a candidate id is one of the options on this ballot.
	private function is_on_ballot($candidates, $candidate_id)
	{
		foreach ($candidates as $candidate)
		{
			if ((int) $candidate['id'] === $candidate_id)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	// The signed in user's name for the topbar.
	private function display_name()
	{
		return trim($this->session->userdata('firstname') . ' ' . $this->session->userdata('lastname'));
	}
}

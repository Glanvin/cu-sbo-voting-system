<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Staff overview for admins and moderators.
// Rationale: see README.md "Conversion notes and gotchas".
class DashboardController extends CI_Controller
{
	// Requires a signed in admin or moderator, then loads the three models behind the tiles. The guard is inline rather than inherited, matching
	// the rest of the app.
	public function __construct()
	{
		parent::__construct();
		$this->guard_staff();

		$this->load->model('User_model');
		$this->load->model('Election_model');
		$this->load->model('Candidate_model');
	}

	// Redirects unless the session holds an admin or moderator. Students are sent to their own dashboard rather than to sign in, since they are
	// authenticated and simply in the wrong area.
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

	// Renders the overview tiles and the most recent elections.
	public function index()
	{
		$this->User_model->touch_activity((int) $this->session->userdata('user_id'));

		$election_counts = $this->Election_model->count_by_status();

		$data = array(
			'title' => 'Dashboard - CU SBO Voting System',
			'page_heading' => 'Dashboard Overview',
			'active' => 'home',
			'user_name' => trim($this->session->userdata('firstname') . ' ' . $this->session->userdata('lastname')),
			'total_voters' => $this->User_model->count_voters(),
			'ongoing' => $election_counts[Election_model::STATUS_ONGOING],
			'upcoming' => $election_counts[Election_model::STATUS_UPCOMING],
			'closed' => $election_counts[Election_model::STATUS_CLOSED],
			'total_elections' => array_sum($election_counts),
			'total_candidates' => $this->Candidate_model->count(),
			'current' => $this->Election_model->get_current(),
			'elections' => array_slice($this->Election_model->get_all(), 0, 5)
		);

		$this->load->view('templates/staff_header', $data);
		$this->load->view('dashboard/index', $data);
		$this->load->view('templates/footer', $data);
	}
}

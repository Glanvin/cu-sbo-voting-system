<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Vote tallies and turnout for admins and moderators.
// Rationale: see README.md "Conversion notes and gotchas".
class ReportController extends CI_Controller
{
	// Requires an admin or moderator, then loads the models behind the turnout figures and the per position tallies. Inline guard.
	public function __construct()
	{
		parent::__construct();
		$this->guard_staff();

		$this->load->model('Election_model');
		$this->load->model('Vote_model');
		$this->load->model('User_model');
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

	// Renders turnout and the candidate tally for one election.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function index($election_id = NULL)
	{
		$this->User_model->touch_activity((int) $this->session->userdata('user_id'));

		$elections = $this->Election_model->get_all();

		if (empty($elections))
		{
			$data = array(
				'title' => 'Reports - CU SBO Voting System',
				'active' => 'reports',
				'user_name' => $this->display_name(),
				'elections' => array(),
				'selected' => NULL,
				'positions' => array(),
				'stats' => array('eligible' => 0, 'submitted' => 0, 'abstained' => 0, 'participation' => 0),
				'message' => $this->session->flashdata('message')
			);

			$this->load->view('templates/staff_header', $data);
			$this->load->view('reports/index', $data);
			$this->load->view('templates/footer', $data);
			return;
		}

		$selected = $this->resolve_election($election_id, $elections);

		$eligible = $this->Vote_model->count_eligible_voters($selected['department']);
		$submitted = $this->Vote_model->count_submissions($selected['id']);

		$positions = array();
		$is_first = TRUE;

		foreach ($this->Vote_model->get_tally($selected['id']) as $position => $candidates)
		{
			$rows = array();

			foreach ($candidates as $candidate)
			{
				$rows[] = array(
					'name' => $candidate['fullname'],
					'votes' => (int) $candidate['votes'],
					'pct' => $submitted > 0 ? round(((int) $candidate['votes'] / $submitted) * 100, 1) : 0.0
				);
			}

			$abstain = $this->Vote_model->count_abstains($selected['id'], $position);

			$positions[] = array(
				'key' => preg_replace('/[^a-zA-Z0-9]+/', '_', strtolower($position)),
				'title' => $position,
				'candidates' => $rows,
				'abstain' => $abstain,

				// Cast for this position, which excludes the abstentions counted separately below.
				'cast' => $submitted - $abstain,

				// The legacy code reused votes_cast here, which made the position total and the turnout figure the same number.
				'submitted' => $submitted,

				// Accordion behaviour: first group open, the rest closed.
				'open' => $is_first
			);

			$is_first = FALSE;
		}

		$data = array(
			'title' => 'Reports - CU SBO Voting System',
			'page_heading' => 'Vote Reports',
			'active' => 'reports',
			'user_name' => $this->display_name(),
			'elections' => $elections,
			'selected' => $selected,
			'positions' => $positions,
			'stats' => array(
				'eligible' => $eligible,
				'submitted' => $submitted,
				'participation' => $eligible > 0 ? round(($submitted / $eligible) * 100, 1) : 0.0
			),
			'message' => $this->session->flashdata('message')
		);

		$this->load->view('templates/staff_header', $data);
		$this->load->view('reports/index', $data);
		$this->load->view('templates/footer', $data);
	}

	// Picks the election to report on. An unknown or missing id falls back to the first row, which get_all() orders by open_at descending.
	private function resolve_election($election_id, $elections)
	{
		if ($election_id)
		{
			foreach ($elections as $election)
			{
				if ((int) $election['id'] === (int) $election_id)
				{
					return $election;
				}
			}
		}

		return $elections[0];
	}

	// The signed in user's name for the topbar.
	private function display_name()
	{
		return trim($this->session->userdata('firstname') . ' ' . $this->session->userdata('lastname'));
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// The site root, which dispatches each signed in user to their own area.
// Rationale: see README.md "Conversion notes and gotchas".
class HomeController extends CI_Controller
{
	// Loads the user model for the presence stamp. No session guard: this controller is the thing that decides where a guest is sent.
	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
	}

	// Sends the visitor to sign in, or to the dashboard for their role. Admin and moderator share the staff dashboard; students get their own area
	// with the election list.
	public function index()
	{
		$user_id = (int) $this->session->userdata('user_id');

		if ( ! $user_id)
		{
			redirect('auth/login');
		}

		$this->User_model->touch_activity($user_id);

		$role = $this->session->userdata('role');
		redirect($role === 'student' ? 'student' : 'dashboard');
	}
}

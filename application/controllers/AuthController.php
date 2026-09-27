<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Sign in, sign out and the forced first-login password change.
// Rationale: see README.md "Conversion notes and gotchas".
class AuthController extends CI_Controller
{
	// Loads the user model and the shared rule sets. No session guard: login and change_password must stay reachable while signed out.
	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		$this->config->load('form_rules');
	}

	// Entry point for `/auth`: signed in users go to their own area, everyone else falls through to the sign in form.
	public function index()
	{
		$this->login();
	}

	// Renders the sign in form and processes the credentials POST. The email format check is now a `valid_email` form rule rather than the four
	// string returns of utils.php validateEmail().
	public function login()
	{
		// Already signed in: no reason to show the form again.
		if ($this->session->userdata('user_id'))
		{
			redirect($this->landing_for($this->session->userdata('role')));
		}

		$this->form_validation->set_rules($this->config->item('voting_signin'));
		$this->form_validation->set_error_delimiters('', '');

		$data = array(
			'title' => 'Sign In - CU SBO Voting System',
			'login_error' => NULL,
			'email' => $this->input->post('email', TRUE)
		);

		if ($this->form_validation->run() === TRUE)
		{
			$email = $this->input->post('email', TRUE);
			$password = (string) $this->input->post('password', TRUE);

			$user = $this->User_model->find_by_email($email);

			// Wrong email and wrong password produce the same message, so the form cannot be used to enumerate registered addresses.
			if ( ! $user OR (string) $user['password'] !== $password)
			{
				$data['login_error'] = 'Invalid university email or password.';
			}
			elseif ($this->is_signed_in_elsewhere($user))
			{
				$data['login_error'] = 'Access denied: your account is currently logged in on another device. Please sign out there first.';
			}
			else
			{
				$this->sign_in($user);
			}
		}

		$this->load->view('templates/auth_header', $data);
		$this->load->view('auth/login', $data);
		$this->load->view('templates/auth_footer');
	}

	// Stores the identity in the session and routes by role. Keeps the session write and the two possible redirects together so no caller can sign
	// a user in without sending them somewhere.
	private function sign_in($user)
	{
		$this->session->set_userdata(array(
			'user_id' => $user['id'],
			'role' => $user['role'],
			'firstname' => $user['firstname'],
			'lastname' => $user['lastname'],
			'email' => $user['email'],
			'department' => $user['department'],

			// Carried into the session because change_password() reads it to decide whether to keep nagging after a reload. Without it the flag exists
			// only in the POST, so navigating away and back would quietly drop the requirement.
			'is_new' => (int) $user['is_new']
		));

		$this->User_model->set_online($user['id'], session_id());

		// A first login must change the admin supplied password before reaching any other page.
		if ((int) $user['is_new'] === 1)
		{
			redirect('auth/change_password');
		}

		redirect($this->landing_for($user['role']));
	}

	// Whether this account holds a live session in another browser. The stored session id is compared against this one, so reloading a page in the
	// same browser is never mistaken for a duplicate sign in.
	private function is_signed_in_elsewhere($user)
	{
		return (int) $user['is_online'] === 1
			AND $user['session_id'] !== NULL
			AND $user['session_id'] !== session_id();
	}

	// The landing path for a role.
	// Rationale: see README.md "Conversion notes and gotchas".
	private function landing_for($role)
	{
		return $role === 'student' ? 'student' : 'dashboard';
	}

	// Renders the change password form and processes the POST.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function change_password()
	{
		if ( ! $this->session->userdata('user_id'))
		{
			redirect('auth/login');
		}

		$this->form_validation->set_rules($this->config->item('voting_password'));
		$this->form_validation->set_error_delimiters('', '');

		$data = array(
			'title' => 'Change Password - CU SBO Voting System',
			'is_new' => (int) $this->input->post('is_new') === 1
				OR (int) $this->session->userdata('is_new') === 1,
			'firstname' => $this->session->userdata('firstname')
		);

		if ($this->form_validation->run() === TRUE)
		{
			$user_id = (int) $this->session->userdata('user_id');

			$this->User_model->update_password($user_id, (string) $this->input->post('new_password', TRUE));

			// update_password() clears is_new, so the session copy has to go with it or the user would be forced through this form again.
			$this->session->set_userdata('is_new', 0);

			$this->session->set_flashdata('message', 'Your password has been changed successfully.');
			redirect($this->landing_for($this->session->userdata('role')));
		}

		$this->load->view('templates/auth_header', $data);
		$this->load->view('auth/change_password', $data);
		$this->load->view('templates/auth_footer');
	}

	// Clears presence and destroys the session, then returns to sign in. Presence is cleared before the session is destroyed because the database
	// write needs the user id that lives in the session.
	public function logout()
	{
		$user_id = (int) $this->session->userdata('user_id');

		if ($user_id)
		{
			$this->User_model->set_offline($user_id);
		}

		$this->session->sess_destroy();
		redirect('auth/login');
	}
}

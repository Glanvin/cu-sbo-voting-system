<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Admin only user administration.
// Rationale: see README.md "Conversion notes and gotchas".
class UserController extends CI_Controller
{
	// Requires an admin. Moderators are refused even though they may use the dashboard, candidates and reports.
	public function __construct()
	{
		parent::__construct();
		$this->guard_admin();

		$this->load->model('User_model');
		$this->load->model('Election_model');
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

	// Renders the user table, the add form and the department/role lists.
	public function index()
	{
		$this->User_model->touch_activity((int) $this->session->userdata('user_id'));

		$data = array(
			'title' => 'View Users - CU SBO Voting System',
			'page_heading' => 'View Users',
			'active' => 'users',
			'user_name' => $this->display_name(),
			'users' => $this->User_model->get_all_except((int) $this->session->userdata('user_id')),
			'role_counts' => $this->User_model->count_by_role(),
			'departments' => $this->config->item('departments'),
			'roles' => $this->config->item('roles'),
			'message' => $this->session->flashdata('message'),
			'edit_user' => NULL
		);

		// Deep link from the table's edit button, so the form arrives populated instead of empty.
		$edit_id = (int) $this->input->get('edit', TRUE);

		if ($edit_id)
		{
			$row = $this->User_model->find_by_id($edit_id);

			// find_by_id() hands back a database row, whose primary key is called 'id', but the form and the POST both call it 'user_id'. Alias it so the
			// view can read one key on both paths: this deep link, and render_with_errors() below, which passes the posted array through unchanged.
			if ($row)
			{
				$row['user_id'] = $row['id'];
			}

			$data['edit_user'] = $row;
		}

		$this->load->view('templates/staff_header', $data);
		$this->load->view('users/index', $data);
		$this->load->view('templates/footer', $data);
	}

	// Creates or updates an account from one shared form.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function save()
	{
		$this->form_validation->set_rules($this->config->item('voting_user'));
		$this->form_validation->set_error_delimiters('', '');

		$this->form_validation->set_message(
			'_password_rule',
			'Password is required when adding an account, and must be at least 6 characters.'
		);

		$this->form_validation->set_message(
			'_unique_email',
			'That email address is already used by another account.'
		);

		$user_id = (int) $this->input->post('user_id', TRUE);

		if ($this->form_validation->run() !== TRUE)
		{
			// Repopulating the form needs the posted values back, so the view is rendered with them rather than redirected to.
			$this->render_with_errors($user_id);
			return;
		}

		$data = array(
			'school_id' => $this->input->post('school_id', TRUE),
			'firstname' => $this->input->post('firstname', TRUE),
			'lastname' => $this->input->post('lastname', TRUE),
			'email' => $this->input->post('email', TRUE),
			'role' => $this->input->post('role', TRUE),
			'department' => $this->input->post('department', TRUE)
		);

		$password = (string) $this->input->post('password', TRUE);

		if ($user_id)
		{
			// Blank password on the edit form means "keep the current one".
			if ($password !== '')
			{
				$data['password'] = $password;
			}

			$name = $data['firstname'] . ' ' . $data['lastname'];

			if ($this->User_model->update($user_id, $data))
			{
				$this->session->set_flashdata('message', "User '$name' has been successfully updated.");
			}
			else
			{
				$this->session->set_flashdata('message', 'Error updating user.');
			}
		}
		else
		{
			// _password_rule() has already rejected a blank password on the create path, so it is present here.
			$data['password'] = $password;
			$name = $data['firstname'] . ' ' . $data['lastname'];

			if ($this->User_model->create($data))
			{
				$this->session->set_flashdata('message', "User '$name' has been successfully added.");
			}
			else
			{
				$this->session->set_flashdata('message', 'Error adding user.');
			}
		}

		redirect('users');
	}

	// Rejects an email address that another account already holds.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function _unique_email($str)
	{
		return ! $this->User_model->email_taken_by_other(
			$str,
			(int) $this->input->post('user_id', TRUE)
		);
	}

	// Validates the password field, which has two different contracts.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function _password_rule($str)
	{
		// A non-zero user_id means the form was in edit mode, which is the same signal save() uses to decide between insert and update.
		$editing = (int) $this->input->post('user_id', TRUE) > 0;

		if ($str === '')
		{
			// Blank is fine when editing: save() omits the column and the stored password stays as it was.
			return $editing;
		}

		if (mb_strlen($str) < 6)
		{
			return FALSE;
		}

		// Matches the column width. Anything longer would be silently truncated by the database, which under MySQL's default strict mode is an error
		// rather than a warning.
		return mb_strlen($str) <= 255;
	}

	// Permanently deletes an account.
	// Rationale: see README.md "Conversion notes and gotchas".
	public function delete()
	{
		$user_id = (int) $this->input->post('user_id', TRUE);
		$current = (int) $this->session->userdata('user_id');

		if ( ! $user_id OR $user_id === $current)
		{
			$this->session->set_flashdata('message', 'You cannot delete your own account.');
			redirect('users');
		}

		$target = $this->User_model->find_by_id($user_id);

		if ( ! $target)
		{
			$this->session->set_flashdata('message', 'That user no longer exists.');
			redirect('users');
		}

		if ($this->User_model->delete($user_id))
		{
			$this->session->set_flashdata('message', "User {$target['firstname']} {$target['lastname']} has been permanently deleted.");
		}
		else
		{
			$this->session->set_flashdata('message', 'Error deleting user.');
		}

		redirect('users');
	}

	// Re-renders the user page with validation errors and posted values.
	// Rationale: see README.md "Conversion notes and gotchas".
	private function render_with_errors($user_id)
	{
		$posted = array(
			'user_id' => $user_id,
			'school_id' => (string) $this->input->post('school_id', TRUE),
			'firstname' => (string) $this->input->post('firstname', TRUE),
			'lastname' => (string) $this->input->post('lastname', TRUE),
			'email' => (string) $this->input->post('email', TRUE),
			'role' => (string) $this->input->post('role', TRUE),
			'department' => (string) $this->input->post('department', TRUE)
		);

		// Never echo the posted password back into the markup.
		$posted['password'] = '';

		$data = array(
			'title' => 'View Users - CU SBO Voting System',
			'page_heading' => 'View Users',
			'active' => 'users',
			'user_name' => $this->display_name(),
			'users' => $this->User_model->get_all_except((int) $this->session->userdata('user_id')),
			'role_counts' => $this->User_model->count_by_role(),
			'departments' => $this->config->item('departments'),
			'roles' => $this->config->item('roles'),
			'message' => validation_errors() ?: 'Please correct the highlighted fields.',
			'edit_user' => $user_id ? $posted : NULL
		);

		$this->load->view('templates/staff_header', $data);
		$this->load->view('users/index', $data);
		$this->load->view('templates/footer', $data);
	}

	// The signed in user's name for the topbar.
	private function display_name()
	{
		return trim($this->session->userdata('firstname') . ' ' . $this->session->userdata('lastname'));
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// FORM VALIDATION RULE SETS Every hand rolled check in the legacy viewmodels now lives here as a CodeIgniter rule array. Controllers call
// `$this->form_validation->set_rules($this->config->item('voting_*'))`, which keeps the rules declarative and out of the request handlers.

// The legacy forms posted short department codes ("CCS") while the database stored full names, so the two could never match.
// The full name is the value here and the short code is only the label, which is what makes get_ongoing_for_student() match.
$config['departments'] = array(
	'College of Computer Studies' => 'College of Computer Studies (CCS)',
	'College of Engineering' => 'College of Engineering (COE)',
	'College of Business Administration' => 'College of Business Administration (CBA)',
	'College of Arts and Sciences' => 'College of Arts and Sciences (CAS)',
	'College of Education' => 'College of Education (CED)',
	'College of Criminology' => 'College of Criminology (CRIM)',
	'College of Health and Sciences' => 'College of Health and Sciences (COHS)',
	'University Wide' => 'University Wide (all colleges)',
	'Student Affairs' => 'Student Affairs (moderator)',
	'IT Department' => 'IT Department (admin)'
);

// Positions offered on the candidate form. Kept in the same order the ballot renders them so the two read as one list.
$config['positions'] = array(
	'President',
	'Vice President',
	'Secretary',
	'Treasurer',
	'Auditor',
	'Public Information Officer',
	'Representative'
);

$config['roles'] = array('student', 'moderator', 'admin');

// `in_list` takes a comma separated string, so each whitelist is built from the arrays above with implode rather than repeated by hand.
$department_values = implode(',', array_keys($config['departments']));
$position_values = implode(',', $config['positions']);
$role_values = implode(',', $config['roles']);

// Replaces utils.php validateEmail(), which returned result strings that login_viewmodel.php then switched on.
// Rationale: see README.md "Conversion notes and gotchas".
$config['voting_signin'] = array(
	array(
		'field' => 'email',
		'label' => 'Email',
		'rules' => 'required|trim|valid_email|max_length[100]'
	),
	array(
		'field' => 'password',
		'label' => 'Password',
		'rules' => 'required'
	)
);

// First login password change

// The legacy check was a single `$new_password === $confirm_password` comparison with no length floor. `matches` covers the comparison and
// min_length adds the floor that was missing.
$config['voting_password'] = array(
	array(
		'field' => 'new_password',
		'label' => 'New password',
		'rules' => 'required|min_length[6]|max_length[255]'
	),
	array(
		'field' => 'confirm_password',
		'label' => 'Confirm password',
		'rules' => 'required|matches[new_password]'
	)
);

// view_users_viewmodel.php applied no validation at all: it passed raw $_POST straight into the interpolated INSERT and UPDATE. The password
// and email rules are callbacks on UserController because both have to know whether this is an add or an edit.
$config['voting_user'] = array(
	array(
		'field' => 'user_id',
		'label' => 'User id',
		// is_natural, not is_natural_no: 0 is the meaningful value here. The add form posts a hidden user_id of 0 and is_natural_no rejects zero, so
		// creating any account always failed validation on this field before reaching the insert.
		'rules' => 'trim|is_natural'
	),
	array(
		'field' => 'school_id',
		'label' => 'School ID',
		'rules' => 'required|trim|max_length[50]'
	),
	array(
		'field' => 'firstname',
		'label' => 'First name',
		'rules' => 'required|trim|max_length[50]'
	),
	array(
		'field' => 'lastname',
		'label' => 'Last name',
		'rules' => 'required|trim|max_length[50]'
	),
	array(
		'field' => 'email',
		'label' => 'Email',
		// A callback rather than is_unique.
		// Rationale: see README.md "Conversion notes and gotchas".
		'rules' => 'required|trim|valid_email|max_length[100]|callback__unique_email'
	),
	array(
		'field' => 'password',
		'label' => 'Password',
		// A single callback rather than min_length[6], which cannot express the two cases: add needs 6+ characters, edit treats blank as keep current.
		// Rationale: see README.md "Conversion notes and gotchas".
		'rules' => 'trim|callback__password_rule'
	),
	array(
		'field' => 'role',
		'label' => 'Role',
		'rules' => 'required|in_list[' . $role_values . ']'
	),
	array(
		'field' => 'department',
		'label' => 'Department',
		'rules' => 'required|in_list[' . $department_values . ']'
	)
);

// open_at and close_at arrive as "YYYY-MM-DDTHH:MM" and ElectionController normalises them to MySQL DATETIME before validating. The rule lives
// on the controller because it was previously a JavaScript setCustomValidity call, which a direct POST bypassed entirely.
$config['voting_election'] = array(
	array(
		'field' => 'election_id',
		'label' => 'Election id',
		// 0 means "add a new election". See the user_id note above.
		'rules' => 'trim|is_natural'
	),
	array(
		'field' => 'election_name',
		'label' => 'Election name',
		'rules' => 'required|trim|max_length[150]'
	),
	array(
		'field' => 'department',
		'label' => 'Department',
		'rules' => 'required|in_list[' . $department_values . ']'
	),
	array(
		'field' => 'open_at',
		'label' => 'Opens at',
		'rules' => 'required|callback[_valid_window]'
	),
	array(
		'field' => 'close_at',
		'label' => 'Closes at',
		'rules' => 'required'
	)
);

// Contact numbers are validated as an 11 digit Philippine mobile so the column's VARCHAR(20) holds one real format rather than free text.
$config['voting_candidate'] = array(
	array(
		'field' => 'candidate_id',
		'label' => 'Candidate id',
		// 0 means "add a new candidate". See the user_id note above.
		'rules' => 'trim|is_natural'
	),
	array(
		'field' => 'election_id',
		'label' => 'Election',
		'rules' => 'required|is_natural_no'
	),
	array(
		'field' => 'fullname',
		'label' => 'Full name',
		'rules' => 'required|trim|max_length[150]'
	),
	array(
		'field' => 'position',
		'label' => 'Position',
		'rules' => 'required|in_list[' . $position_values . ']'
	),
	array(
		'field' => 'bio',
		'label' => 'Bio',
		'rules' => 'required|trim|max_length[2000]'
	),
	array(
		'field' => 'school_id',
		'label' => 'School ID',
		'rules' => 'required|trim|max_length[50]'
	),
	array(
		'field' => 'email',
		'label' => 'Email',
		'rules' => 'required|trim|valid_email|max_length[100]'
	),
	array(
		'field' => 'contact_number',
		'label' => 'Contact number',
		'rules' => 'required|regex_match[/^09\d{9}$/]'
	)
);

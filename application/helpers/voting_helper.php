<?php defined('BASEPATH') OR exit('No direct script access allowed');

// View helpers shared by the staff and student templates. Autoloaded via config/autoload.php, so these are available in any view without a per
// controller load.

if ( ! function_exists('status_badge_class'))
{
	// Maps an election status onto a Bootstrap contextual colour. Centralised because the status string appears in the dashboard table, the
	// elections table, the reports header and the student election cards, and each of those previously picked its own colour inline.
	function status_badge_class($status)
	{
		switch ($status)
		{
			case 'ongoing':
				return 'success';

			case 'upcoming':
				return 'warning';

			default:
				return 'secondary';
		}
	}
}

if ( ! function_exists('status_badge_icon'))
{
	// The icon that pairs with status_badge_class().
	function status_badge_icon($status)
	{
		switch ($status)
		{
			case 'ongoing':
				return 'play-circle-fill';

			case 'upcoming':
				return 'clock-fill';

			default:
				return 'lock-fill';
		}
	}
}

if ( ! function_exists('department_label'))
{
	// The short code to show after a department's full name.
	// Rationale: see README.md "Conversion notes and gotchas".
	function department_label($department)
	{
		$abbreviations = array(
			'College of Computer Studies' => 'CCS',
			'College of Engineering' => 'COE',
			'College of Business Administration' => 'CBA',
			'College of Arts and Sciences' => 'CAS',
			'College of Education' => 'CED',
			'College of Criminology' => 'CRIM',
			'College of Health and Sciences' => 'COHS',
		);

		return isset($abbreviations[$department])
			? $department . ' (' . $abbreviations[$department] . ')'
			: $department;
	}
}

if ( ! function_exists('to_datetime_local'))
{
	// Formats a MySQL DATETIME for an <input type="datetime-local">.
	// Rationale: see README.md "Conversion notes and gotchas".
	function to_datetime_local($value)
	{
		if (empty($value))
		{
			return '';
		}

		$timestamp = strtotime($value);

		return $timestamp === FALSE ? '' : date('Y-m-d\TH:i', $timestamp);
	}
}

if ( ! function_exists('field_error_class'))
{
	// Whether a form field should be marked invalid.
	// Rationale: see README.md "Conversion notes and gotchas".
	function field_error_class($field)
	{
		$CI =& get_instance();

		return $CI->form_validation->error($field) ? ' is-invalid' : '';
	}
}

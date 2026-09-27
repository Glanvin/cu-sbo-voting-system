<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Only the lines CI3 3.1.13 does not already ship go here. Everything else is inherited from system/language/english/form_validation_lang.php,
// and application/language/english/ is searched first, so adding a key here overrides the system copy without editing system/.

$lang['form_validation_error_message_not_set'] = 'Unable to access an error message corresponding to your field name {field}.';

// CI3 validates is_natural_no but ships no lang line for it, so a failure renders "Unable to access an error message ...".
// Only the missing key is added here; every other line is inherited from system/language/.
$lang['form_validation_is_natural_no'] = 'The {field} field must be a real id (1 or more).';

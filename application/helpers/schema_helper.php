<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Schema helpers for the migrations.
// Rationale: see README.md "Conversion notes and gotchas".

if ( ! function_exists('forge_add_unique'))
{
	// Adds a composite unique key to an existing table.
	function forge_add_unique($table, array $columns)
	{
		$CI =& get_instance();

		if (count($columns) < 2)
		{
			// One column is not composite, and a lone add_key() would only produce a non unique index. Use the UNIQUE field attribute.
			log_message('error', 'forge_add_unique() needs at least two columns.');
			return FALSE;
		}

		// The generated name stays inside MySQL's 64 character limit.
		$name = substr($table.'_'.implode('_', $columns).'_unique', 0, 64);

		$sql = 'ALTER TABLE '.$CI->db->escape_identifiers($table)
			.' ADD UNIQUE '.$CI->db->escape_identifiers($name)
			.' ('.forge_escape_columns($CI->db, $columns).')';

		return $CI->db->query($sql) !== FALSE;
	}
}

if ( ! function_exists('forge_add_foreign_key'))
{
	// Adds a foreign key constraint to an existing table.
	function forge_add_foreign_key($table, array $columns, $ref_table, array $ref_columns, $on_delete = 'CASCADE', $on_update = 'CASCADE')
	{
		$CI =& get_instance();

		if (count($columns) !== count($ref_columns))
		{
			log_message('error', 'forge_add_foreign_key() column counts do not match.');
			return FALSE;
		}

		$name = substr('fk_'.$table.'_'.implode('_', $columns), 0, 64);

		// The referential action is a fixed vocabulary rather than free text, so anything else is rejected instead of interpolated.
		$actions = array('CASCADE', 'SET NULL', 'RESTRICT', 'NO ACTION', 'SET DEFAULT');
		$on_delete = strtoupper($on_delete);
		$on_update = strtoupper($on_update);

		if ( ! in_array($on_delete, $actions, TRUE) OR ! in_array($on_update, $actions, TRUE))
		{
			log_message('error', 'forge_add_foreign_key() got an unknown referential action.');
			return FALSE;
		}

		$sql = 'ALTER TABLE '.$CI->db->escape_identifiers($table)
			.' ADD CONSTRAINT '.$CI->db->escape_identifiers($name)
			.' FOREIGN KEY ('.forge_escape_columns($CI->db, $columns).')'
			.' REFERENCES '.$CI->db->escape_identifiers($ref_table)
			.' ('.forge_escape_columns($CI->db, $ref_columns).')'
			.' ON DELETE '.$on_delete
			.' ON UPDATE '.$on_update;

		return $CI->db->query($sql) !== FALSE;
	}
}

if ( ! function_exists('forge_escape_columns'))
{
	// Escapes a list of column names and joins them with commas.
	function forge_escape_columns($db, array $columns)
	{
		return implode(', ', array_map(array($db, 'escape_identifiers'), $columns));
	}
}

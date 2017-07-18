<?php
function loadSaveData($table_name, $field, $key = null) {
	global	$save_registerID,
					$save_registration,
					$save_child,
					$save_guardian,
					$save_guardians,
					$save_emergency_contacts,
					$save_authorized_contacts,
					$save_restricted_contacts,
					$save_users,
					$save_location,
					$save_program,
					$save_activity,
					$save_course;
					$save_course_activity;
					$save_course_activity_options;

	$table_name = "save_".$table_name;
	$table = $$table_name;

	$value = null;
	if (is_array($table)) {
		if (isset($key) && isset($table[$key])) {
			if (isset($table[$key][$field])) {
				$value = $table[$key][$field];
			}
		} elseif (isset($key) && isset($table[0][$key])) {
			if (isset($table[0][$key][$field])) {
				$value = $table[0][$key][$field];
			}
		} elseif (isset($table[$field])) {
			$value = $table[$field];
		}
	}
	return $value;
}
?>
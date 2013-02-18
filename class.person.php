<?php
	class person {
		
		function __construct($name) {
			global $db;
			$q = $db->rquery('SELECT * FROM staff WHERE name = "' . $name . '";');
			$q = $q[0];
			$this->name = $q['name'];
			$this->email = $q['email'];
			$this->phone = $q['phone'];
			$this->password = $q['password'];
			$this->active = $q['active'];
			$this->position = $q['position'];
			$this->schedule = array(
				'monday' => array(
					'in' => strtotime($q['monday_in']),
					'out' => strtotime($q['monday_out']),
				),
				'tuesday' => array(
					'in' => strtotime($q['tuesday_in']),
					'out' => strtotime($q['tuesday_out']),
				),
				'wednesday' => array(
					'in' => strtotime($q['wednesday_in']),
					'out' => strtotime($q['wednesday_out']),
				),
				'thursday' => array(
					'in' => strtotime($q['thursday_in']),
					'out' => strtotime($q['thursday_out']),
				),
				'friday' => array(
					'in' => strtotime($q['friday_in']),
					'out' => strtotime($q['friday_out']),
				),
				'saturday' => array(
					'in' => strtotime($q['saturday_in']),
					'out' => strtotime($q['saturday_out']),
				),
			);
		}
	}
?>
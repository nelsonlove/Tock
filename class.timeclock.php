<?php
	class timeclock {
		
		# Does a MySQL query and returns the result as an array

		function __construct() {
			
			global $db;
			
			# get events
			$this->events = $db->rquery("SELECT * FROM timeclock ORDER BY timestamp ASC;");
		}
		
		public function dates() {
			$rows = array();
			foreach ($this->events as $event) {
				$ts = new timestamp($event);
				$rows[] = $ts->unixdate;
			}
			return $rows;	
		}
		
		# Returns the Unix time of the first record
		public function first() {
			$r = $this->dates();
			return reset($r);
		}
		
		# Returns the Unix time of the last record
		public function last() {
			$r = $this->dates();
			return end($r);
		}
		
		# return an array of the week numbers with records
		public function weeks($year) {
			$rows = array();
			foreach ($this->events as $event) {
				$ts = new timestamp($event);
				if (($ts->year == $year) && !in_array($ts->week_number,$rows)) { $rows[] = $ts->week_number; }
			}
			return array_reverse($rows);
		}
		
		# return an array of the years with records
		public function years() {
			$rows = array();
			foreach ($this->events as $event) {
				$ts = new timestamp($event);
				if (!in_array($ts->year,$rows)) { $rows[] = $ts->year; }
			}
			return array_reverse($rows);
		}
		
		private function day_of_week($a) {
			return date("l",strtotime);
		}
		
		public function events_in_range($start_time, $end_time) {
			$rows = array();
			foreach ($this->events as $event) {
				$ts = new timestamp($event);
				$time = $ts->unixdate;
				if (($start_time <= $time) && ($time <= $end_time)) { $rows[] = $event; }
			}
			if ($rows) { return $rows; }
		}
		
		public function events_by_week($date) {
			$rows = array();
			$year = date("Y",$date);
			$week_number = date("W",$date);
			$start_time = strtotime($year."W".$week_number."1");
			$end_time = strtotime($year."W".$week_number."6");
			return $this->events_in_range($start_time, $end_time);
		}
		
		public function events_by_day($date) {
			$rows = array();
			$start_time = mktime(0, 0, 0, date("n",$date), date("j",$date), date("Y",$date) );
			$end_time = mktime(23, 59, 59, date("n",$date), date("j",$date), date("Y",$date) );
			$rows = $this->events_in_range($start_time, $end_time);
			if ($rows) { return $rows; }
		}

		public function events_by_person($name) {
			$rows = array();
			if ($event['name'] == $name) { $rows[] = $event; }
			if ($rows) { return $rows; }
		}
	}
?>
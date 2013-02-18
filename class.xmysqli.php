<?php
	class xmysqli extends mysqli {
		public function rquery($query) {
			$rows = array();
			$result = $this->query($query);
			if (is_object($result)) {
				while ($row = $result->fetch_assoc()) {
					$rows[] = $row;
				}
				if ($this->field_count == 1) {
					$rows = array();
					$result = $this->query($query);
					while ($row = $result->fetch_assoc()) {
						$rows[] = reset($row);
					}
				}
			}
			return $rows;
		}
	}
?>
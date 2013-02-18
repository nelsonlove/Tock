<html>
<head>
<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>

<img src="logo.gif" width=191 height=54><br>
<h2>Employee time clock report</h2>
<?php

	# Include all the goodies
	include("header.php");
	
	# Create a timeclock object
	$timeclock = new timeclock();

	# Populate the staff list with the names of all staff
	$staff = $db->rquery("SELECT name FROM staff;");
	
	# Let's make a table of contents based on the weeks that wil have records
	echo '<ul class="toc">' . "\n";
	foreach (array_reverse($timeclock->years()) as $year) {
		foreach (array_reverse($timeclock->weeks($year)) as $week_number) {
			$week = $year."W".$week_number;
			echo '<li><a href="#' . strtotime($week."1") . '">';
			echo "Week of " . date("F j, Y",strtotime($week."1"));
			echo "</a></li>" . "\n" . "<br>" . "\n";
		}
	}
	echo '</ul>';
			
	# For every year with records:
	foreach ($timeclock->years() as $year) {
		
		# For every week in the active year with records:
		foreach ($timeclock->weeks($year) as $week_number) {
			
			# Set the specific week as a string to derive days from
			$week = $year."W".$week_number;
			
			# Open a div for the week
			echo '<div id="' . strtotime($week."1") . '">' . "\n" . "<hr>" . "\n";
			
			# Make a heading with the monday of the active week
			echo "<h2>Week of " . date("F j, Y",strtotime($week."1")) . "</h2>" . "\n";
			
			# Provide a link to the top of the page
			echo '<a href="#">Top of page</a>' . "\n";
			
			# For each staff member:
			foreach ($staff as $name) {
				
				# Make a person object
				$person = new person($name);
				
				# Make counters for time clock events and scheduled days
				$event_counter = 0;
				$scheduled_days = 6;
				
				# Create an array that will later be used to generate a table
				$table = array();
				
				# Put the side headers into the table
				$table['headings'][0] = "&nbsp;";
				$table['times'][0] = "Scheduled Hours";
				$table['in1'][0] = "In";
				$table['out1'][0] = "Out to Lunch";
				$table['in2'][0] = "Back from Lunch";
				$table['out2'][0] = "Out";	
				
				# For each day, Monday through Saturday, of the active week
				for ($i = 1; $i <= 6; $i++) {
					
					# Here's our specific day
					$day = strtotime($week.$i);
					
					# Here's our day names, capitalized and lowercase
					$day_name = date("l",$day);
					$lc_day_name = strtolower($day_name);
					
					# Here's our day headings
					$table['headings'][$i] = $day_name;
					
					# Here's our scheduled times, only put them in if a person is actually scheduled
					$start_time = date("g:ia", $person->schedule[$lc_day_name]['in']);
					$end_time = date("g:ia", $person->schedule[$lc_day_name]['out']);
					$table['times'][$i] = $start_time . " &ndash; " . $end_time;
					if ($table['times'][$i] == $start_time . " &ndash; " . $start_time) {
						$table['times'][$i] = " &mdash; ";
						# We're also going to increment our scheduled days Counter
						$scheduled_days = $scheduled_days - 1;
					}
					
					# Now we get the person's events for the active day
					$events = array();
					if ($dailyevents = $timeclock->events_by_day($day)) {
						foreach ($dailyevents as $event) { if ($event['name'] == $person->name) { $events[] = $event; } }
					}
					
					# Add the number of events for the active day to our counter
					$event_counter = $event_counter + count($events);

					# Parse and format the events into an array with keys in1, out1, in2, out2 and a null value
					$nulltext = "";
					$parsedEvents = popEventArray($events,$nulltext);
					foreach ($parsedEvents as &$row) {
						if ($row != $nulltext) {
							$row = date("h:ia",$row);
						}
					}
					
					# Let's put the parsed events in the table array
					$a = array("in1", "out1", "in2", "out2");
					foreach ($a as $b) { $table[$b][$i] = $parsedEvents[$b]; }
					
				}
				
				# Now we echo the person's table if there have been events this week
				if (($event_counter > 0) or ($scheduled_days > 0)) {
					echo "<h3>" . $person->name . " / " . $person->phone . " / " . $person->email . "</h3>" . "\n";
					echo "<table>";
					$tr = array("headings", "times", "in1", "out1", "in2", "out2");
					foreach ($tr as $row) {
						echo "<tr>" . "\n";
						for ($i = 0; $i <= 6; $i++) { echo "<td>" . $table[$row][$i] . "</td>" . "\n"; }
						echo "<tr>" . "\n";
					}
					echo "</table>";
				}
			}
			
			# Close the div and provide a link to the top of the page
			echo "</div>" . "\n" . "<br>" . "\n" . '<a href="#">Top of page</a>' . "\n";
		}
	}
	
?>
</body>
</html>
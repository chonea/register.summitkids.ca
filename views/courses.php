<?php
include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

//set taxonomy
include("config/register.config.php");

// boot the non-admins
if ($_SESSION['user_role'] != "admin") {
	header("Location: /");
	exit();
}

include('header.php');
//include("sidebar.php");
?>

<div class="row-fluid" style="margin: 0 auto 30px;">
	<div class="page-header">
		<h2>Courses</h2>
		<h3>Manage Courses</h3>
	</div>
	<div class="span12" style="float: right; height: 40px; padding: 0 0 15px; margin: -10px 0 0; text-align: right;">
		<form class="form-search" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<div class="input-append span12">
				<select type="search" name="order" class="span2 search-query">
					<?php
					$options = array(
						"program" => "Program → Location",
						"location" => "Location → Program"
					);
					foreach ($options as $key => $value) {
						echo '<option value="'.$key.'"';
						if (isset($_REQUEST['order']) && $_REQUEST['order'] != '') {
							if ($_REQUEST['order'] == $key) {
								echo "selected";
							}
						}
						echo '>'.$value.'</option>';
					}
					?>
				</select>
				<button class="btn" style="background:transparent url('images/glyphicons-halflings.png') -44px 6px no-repeat; text-indent: -9999px; margin-right: 15px;">Search</button>
				<select type="search" name="range" class="span2 search-query">
					<?php
					$options = array(
						"currentupcoming" => "Current and Upcoming Courses",
						"current" => "Current Courses",
						"upcoming" => "Upcoming Courses",
						"past" => "Past Courses",
						"all" => "All Courses"
					);
					foreach ($options as $key => $value) {
						echo '<option value="'.$key.'"';
						if (isset($_REQUEST['range']) && $_REQUEST['range'] != '') {
							if ($_REQUEST['range'] == $key) {
								echo "selected";
							}
						}
						echo '>'.$value.'</option>';
					}
					?>
				</select>
				<button class="btn" style="background:transparent url('images/glyphicons-halflings.png') -44px 6px no-repeat; text-indent: -9999px; margin-right: 15px;">Search</button>
				<select type="search" name="programID" class="span2 search-query">
					<option value="">All Programs</option>
					<?php
					$fields = "p.*";
					$from = "program AS p";
					$where = "1 = 1";
					$order = " ORDER BY p.name ASC";
					if ($programs = $db->select($from, $where." ".$order, "", $fields)) {
						foreach ($programs as $program) {
							echo '<option value="'.$program['id'].'"';
							if (isset($_REQUEST['programID']) && $_REQUEST['programID'] != '') {
								if ($_REQUEST['programID'] == $program['id']) {
									echo "selected";
								}
							}
							echo '>'.$program['name'].'</option>';
						}
					}
					?>
				</select>
				<button class="btn" style="background:transparent url('images/glyphicons-halflings.png') -44px 6px no-repeat; text-indent: -9999px; margin-right: 15px;">Search</button>
				<select type="search" name="locationID" class="span2 search-query">
					<option value="">All Locations</option>
					<?php
					$fields = "l.id, l.name, l.disabled, l.deleted";
					$from = "location AS l";
					$where = "(l.disabled IS NULL OR l.disabled = '0000-00-00 00:00:00')";
					$where .= " AND (l.deleted IS NULL OR l.deleted = '0000-00-00 00:00:00')";
					$order = " ORDER BY l.name ASC";
					if ($locations = $db->select($from, $where." ".$order, "", $fields)) {
						foreach ($locations as $location) {
							echo '<option value="'.$location['id'].'"';
							if (isset($_REQUEST['locationID']) && $_REQUEST['locationID'] != '') {
								if ($_REQUEST['locationID'] == $location['id']) {
									echo "selected";
								}
							}
							echo '>'.$location['name'].'</option>';
						}
					}
					?>
				</select>
				<button class="btn" style="background:transparent url('images/glyphicons-halflings.png') -44px 6px no-repeat; text-indent: -9999px; margin-right: 15px;">Search</button>
				<input type="search" name="search" class="span2 search-query" placeholder="Course Name or ID" value="<?php if (isset($_REQUEST['search']) && $_REQUEST['search'] != '') echo $_REQUEST['search']; ?>">
				<button class="btn" style="background:transparent url('images/glyphicons-halflings.png') -44px 6px no-repeat; text-indent: -9999px;">Search</button>
			</div>
		</form>
	</div>

<form id="dashboard-courses" name="dashboard-courses" method="post" action="courses.php">
<input id="dashboard-courses-course-id" type="hidden" name="courseID" value="" />
<?php
echo '<h3>';

if (isset($_REQUEST['range'])) {
	if ($_REQUEST['range'] == 'currentupcoming' || $_REQUEST['range'] == '') {
		$range_str = "Current and Upcoming";
	} elseif ($_REQUEST['range'] == 'current') {
		$range_str = "Current";
	} elseif ($_REQUEST['range'] == 'upcoming') {
		$range_str = "Upcoming";
	} elseif ($_REQUEST['range'] == 'past') {
		$range_str = "Past";
	} else {
		$range_str = "All";
	}
} else {
	$range_str = "Current and Upcoming";
}

if (isset($_REQUEST['search']) && $_REQUEST['search'] != '') {
	echo 'Search '.$range_str.' Course IDs/Names Like <em>"'.$_REQUEST['search'].'"</em>';
} elseif ((isset($_REQUEST['locationID']) && $_REQUEST['locationID'] != '') || (isset($_REQUEST['programID']) && $_REQUEST['programID'] != '')) {
	echo 'Search '.$range_str.' Courses';
	if (isset($_REQUEST['programID']) && $_REQUEST['programID'] != '') {
		$results = $db->select("program", "id = '".$_REQUEST['programID']."' LIMIT 0,1");
		$program = $results[0];
		echo ' For <em>"'.$program['name'].'"</em>';
	}
	if (isset($_REQUEST['locationID']) && $_REQUEST['locationID'] != '') {
		$results = $db->select("location", "id = '".$_REQUEST['locationID']."' LIMIT 0,1");
		$location = $results[0];
		echo ' At <em>"'.$location['name'].'"</em>';
	}
} else {
	echo  $range_str.' Courses';
}
echo '</h3>';
?>

<div class="application-list row-fluid">
	<div id="dashboard-applications-message" class="application-row ajax-response"></div>
</div>
<div class="application-list row-fluid">
	<div id="applications-nav" class="application-row"></div>
</div>
<?php
$fields = "*";
$fields .= ", c.id AS id, c.name AS name";
$fields .= ", l.name AS location";
$fields .= ", p.name AS program";
$tables = "course as c, location as l, program as p";
$where = "l.id = c.location_id";
$where .= " AND p.id = c.program_id";
if (isset($_REQUEST['search']) && $_REQUEST['search'] != '') {
	$search_array = explode(" ", $_REQUEST['search']);
	$where .= " AND (";
	foreach ($search_array as $index => $search) {
		if ($index > 0) $where .= " OR ";
		$where .= "c.name LIKE '%".$search."%'";
		$where .= " OR c.id = '".$search."'";
	}
	$where .= ")";
}
if (isset($_REQUEST['locationID']) && $_REQUEST['locationID'] != '') {
	$where .= " AND c.location_id = '".$_REQUEST['locationID']."'";
}

if (isset($_REQUEST['programID']) && $_REQUEST['programID'] != '') {
	$where .= " AND c.program_id = '".$_REQUEST['programID']."'";
}
if (isset($_REQUEST['range'])) {
	if ($_REQUEST['range'] == 'currentupcoming' || $_REQUEST['range'] == '') {
		$where .= " AND c.end_date > NOW()";
	} elseif ($_REQUEST['range'] == 'current') {
		$where .= " AND NOW() BETWEEN c.start_date AND c.end_date";
	} elseif ($_REQUEST['range'] == 'upcoming') {
		$where .= " AND c.start_date > NOW()";
	} elseif ($_REQUEST['range'] == 'past') {
		$where .= " AND c.end_date < NOW()";
	} // else all
} else {
	// else default to current and upcoming
	$where .= " AND c.end_date > NOW()";
}
if (isset($_REQUEST['order']) && $_REQUEST['order'] == 'location') {
	$order = "ORDER BY location ASC, program ASC, c.name ASC, c.start_date DESC";
} else {
	$order = "ORDER BY program ASC, location ASC, c.name ASC, c.start_date DESC";
}
$bind = "";
$results = $db->select($tables, $where." ".$order, $bind, $fields);

if (!$courses = $results) {
	echo '<div class="application-row no-applications"><div class="application-column">Returned 0 results.</div></div>';
	if (isset($_REQUEST['search']) && $_REQUEST['search'] != '') {
		echo '<a href="'.basename($_SERVER['PHP_SELF']).'">Clear Search</a>';
	}
	echo '<h4><a href="course.php">Add Course</a></h4>';
} else {
	echo '<div>Returned '.count($courses).' results.</div>';
	if (isset($_REQUEST['search']) && $_REQUEST['search'] != '') {
		echo '<a href="'.basename($_SERVER['PHP_SELF']).'">Clear Search</a>';
	}
	echo '<h4><a href="course.php">Add Course</a></h4>';
?>
<div class="application-list row-fluid">
<?php
	$current_order_1 = "";
	$current_order_2 = "";
	$section = 0;
	foreach ($courses as $course) {

		if (isset($_REQUEST['order']) && $_REQUEST['order'] == 'location') {
			$new_order_1 = strtoupper($course['location']);
			$new_order_2 = strtoupper($course['program']);
		} else {
			$new_order_1 = strtoupper($course['program']);
			$new_order_2 = strtoupper($course['location']);
		}
		if ($new_order_1 != $current_order_1) {
			$section++;
			echo '<script>$(\'#applications-nav\').append(\'<a href="#sect-'.$section.'" class="scroll-to">'.$new_order_1.'</a>\');</script>';
			echo '<h3 id="sect-'.$section.'" class="application-title"><span class="block-letter">'.$new_order_1.'</span><span class="scroll-top">top</span></h3>';
			$current_order_1 = $new_order_1; 
		}
		if ($new_order_1 != $current_order_1 || $new_order_2 != $current_order_2) {
			echo '<h4 class="" style="padding-top: 10px;"><span class="">'.$new_order_2.'</span></h4>';
			echo '<div class="application-header">';
			echo '<div class="application-column span2">ID/Course</div>';
			echo '<div class="application-column span1">Start</div>';
			echo '<div class="application-column span1">End</div>';
			echo '<div class="application-column span1">Registration Start</div>';
			echo '<div class="application-column span1">Registration End</div>';
			echo '<div class="application-column span1" style="text-align: center;">Applications Accepted</div>';
			echo '<div class="application-column span1" style="text-align: center;">Applications Waiting</div>';
			echo '<div class="application-column span1" style="text-align: center;">Current Availability</div>';
			echo '<div class="application-column span1" style="text-align: center;">Reserve Availability</div>';
			echo '<div class="application-column span1" style="text-align: center;">Maximum Availability</div>';
			echo '<div class="application-column span1" style="text-align: right;"></div>';
			echo '</div>';
			$current_order_2 = $new_order_2; 
		}

		echo '<div class="application-row">';

		echo '<div class="application-column span2">';
		echo "#".str_pad($course['id'], 5, '0', STR_PAD_LEFT)." ".$course['name'];
		echo '</div>';
		echo '<div class="application-column span1">';
		echo strftime('%m/%d/%Y %l:%M %p', strtotime($course['start_date']));
		echo '</div>';
		echo '<div class="application-column span1">';
		echo strftime('%m/%d/%Y %l:%M %p', strtotime($course['end_date']));
		echo '</div>';
		echo '<div class="application-column span1">';
		echo strftime('%m/%d/%Y %l:%M %p', strtotime($course['registration_start_date']));
		echo '</div>';
		echo '<div class="application-column span1">';
		echo strftime('%m/%d/%Y %l:%M %p', strtotime($course['registration_end_date']));
		echo '</div>';
		echo '<div class="application-column span1" style="text-align: center;">';
		$where = "course_id = '".$course['id']."'";
		$where .= " AND (accepted IS NOT NULL AND accepted <> '0000-00-00 00:00:00')";
		$where .= " AND (deleted IS NULL OR deleted = '0000-00-00 00:00:00')";
		if ($result = $db->select("register", $where)) {
			echo '<a href="applications.php?status=accepted&courseID='.$course['id'].'">'.count($result).'</a>'; 
		} else {
			echo "0";
		}
		echo '</div>';
		echo '<div class="application-column span1" style="text-align: center;">';
		$where = "course_id = '".$course['id']."'";
		$where .= " AND (submitted IS NOT NULL AND submitted <> '0000-00-00 00:00:00')";
		$where .= " AND (accepted IS NULL OR accepted = '0000-00-00 00:00:00')";
		$where .= " AND (deleted IS NULL OR deleted = '0000-00-00 00:00:00')";
		if ($result = $db->select("register", $where)) {
			echo '<a href="applications.php?status=submitted&courseID='.$course['id'].'">'.count($result).'</a>'; 
		} else {
			echo "0";
		}
		echo '</div>';
		echo '<div class="application-column span1" style="text-align: center;">';
		echo $course['current_available'];
		echo '</div>';
		echo '<div class="application-column span1" style="text-align: center;">';
		echo $course['reserve_available'];
		echo '</div>';
		echo '<div class="application-column span1" style="text-align: center;">';
		echo $course['max_available'];
		echo '</div>';

		echo '<div class="application-actions span1">';
		echo '<ul class="pull-right">';
		echo '<li class="dropdown">';
		echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars fa-lg fa-fw"></i></a>';
		echo '<ul class="dropdown-menu">';
		echo '<li><a href="course.php?courseID='.$course['id'].'" class=""><i class="fa fa-edit fa-lg fa-fw"></i> Edit</a></li>';
		echo '<li><a href="applications.php?courseID='.$course['id'].'" class=""><i class="fa icon-search fa-lg fa-fw"></i> Find Applications</a></li>';
//		echo '<li><a href="" data-delete-course-id='.$course['id'].'" class="delete"><i class="fa fa-trash fa-lg fa-fw"></i> Delete</a></li>';
		echo '<li><a href="print.php?courseID='.$course['id'].'&reportType=summary" class="" target="_blank"><i class="fa fa-file-pdf-o fa-lg fa-fw"></i> Print Attendance Summary</a></li>';
		echo '</ul>';
		echo '</li>';
		echo '</ul>';
		echo '</div>';

		echo '</div>';
	}
}
?>
</div>
</form>
</div>
<?php include('footer.php'); ?>
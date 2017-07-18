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
				<input type="search" name="name" class="span2 search-query" placeholder="Course Name" value="<?php if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') echo $_REQUEST['name']; ?>">
				<button class="btn" style="background:transparent url('images/glyphicons-halflings.png') -44px 6px no-repeat; text-indent: -9999px;">Search</button>
			</div>
		</form>
	</div>

<form id="dashboard-courses" name="dashboard-courses" method="post" action="courses.php">
<input id="dashboard-courses-course-id" type="hidden" name="courseID" value="" />
<?php
if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
	echo '<h3>Search Course Name Like <em>"'.$_REQUEST['name'].'"</em></h3>';
} else {
	echo '<h3>All Courses</h3>';
}
?>
<div class="application-list row-fluid">
	<div id="dashboard-applications-message" class="application-row ajax-response"></div>
</div>
<div class="application-list row-fluid">
	<div id="applications-nav" class="application-row"></div>
</div>
<?php
$tables = "course as c, location as l, program as p";
$where = "l.id = c.location_id";
$where .= " AND p.id = c.program_id";
if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
	$search_array = explode(" ", $_REQUEST['name']);
	$where .= " AND (";
	foreach ($search_array as $index => $search) {
		if ($index > 0) $where .= " OR ";
		$where .= "c.name LIKE '%".$search."%'";
	}
	$where .= ")";
}
$order = "ORDER BY c.name ASC, c.start_date DESC";
$bind = "";
$fields = "*";
$fields .= ", c.id AS id, c.name AS name";
$fields .= ", l.name AS location";
$fields .= ", p.name AS program";
$results = $db->select($tables, $where." ".$order, $bind, $fields);

if (!$courses = $results) {
	echo '<div class="application-row no-applications"><div class="application-column">Returned 0 results.</div></div>';
	if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
		echo '<a href="courses.php">Clear Search</a>';
	}
	echo '<h4><a href="course.php">Add Course</a></h4>';
} else {
	echo '<div>Returned '.count($courses).' results.</div>';
	if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
		echo '<a href="courses.php">Clear Search</a>';
	}
	echo '<h4><a href="course.php">Add Course</a></h4>';
?>
<div class="application-list row-fluid">
<?php
	$current_char = "";
	$section = 0;
	foreach ($courses as $course) {

		$new_char = strtoupper(substr($course['name'], 0, 1));
		if ($new_char != $current_char) {
			$section++;
			echo '<script>$(\'#applications-nav\').append(\'<a href="#sect-'.$section.'" class="scroll-to">'.$new_char.'</a>\');</script>';
			echo '<h3 id="sect-'.$section.'" class="application-title"><span class="block-letter">'.$new_char.'</span><span class="scroll-top">top</span></h3>';
			echo '<div class="application-header">';
			echo '<div class="application-column span2">Course Name</div>';
			echo '<div class="application-column span2">Location</div>';
			echo '<div class="application-column span2">Program</div>';
			echo '<div class="application-column span1">Start</div>';
			echo '<div class="application-column span1">End</div>';
			echo '<div class="application-column span1">Registration Start</div>';
			echo '<div class="application-column span1">Registration End</div>';
			echo '<div class="application-column span1">Availability</div>';
			echo '<div class="application-column span1" style="text-align: right;"></div>';
			echo '</div>';
			$current_char = $new_char; 
		}

		echo '<div class="application-row">';

		echo '<div class="application-column span2">';
		echo $course['name'];
		echo '</div>';
		echo '<div class="application-column span2">';
		echo $course['location'];
		echo '</div>';
		echo '<div class="application-column span2">';
		echo $course['program'];
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
		echo '<div class="application-column span1">';
		echo '('.$course['current_available'].'+'.$course['reserve_available'].') of '.$course['max_available'];
		echo '</div>';

		echo '<div class="application-actions span1">';
		echo '<ul class="pull-right">';
		echo '<li class="dropdown">';
		echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars fa-lg fa-fw"></i></a>';
		echo '<ul class="dropdown-menu">';
		echo '<li><a href="course.php?courseID='.$course['id'].'" class=""><i class="fa fa-edit fa-lg fa-fw"></i> Edit</a></li>';
		echo '<li><a href="" data-delete-course-id='.$course['id'].'" class="delete"><i class="fa fa-trash fa-lg fa-fw"></i> Delete</a></li>';
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

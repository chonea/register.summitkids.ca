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
		<h2>Activities</h2>
		<h3>Manage Activities</h3>
	</div>
	<div class="span12" style="float: right; height: 40px; padding: 0 0 15px; margin: -10px 0 0; text-align: right;">
		<form class="form-search" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<div class="input-append span12">
				<input type="search" name="name" class="span2 search-query" placeholder="Activity Name" value="<?php if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') echo $_REQUEST['name']; ?>">
				<button class="btn" style="background:transparent url('images/glyphicons-halflings.png') -44px 6px no-repeat; text-indent: -9999px;">Search</button>
			</div>
		</form>
	</div>

<form id="dashboard-activities" name="dashboard-activities" method="post" action="activities.php">
<input id="dashboard-activities-activity-id" type="hidden" name="activityID" value="" />
<?php
if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
	echo '<h3>Search Activity Name Like <em>"'.$_REQUEST['name'].'"</em></h3>';
} else {
	echo '<h3>All Activities</h3>';
}
?>
<div class="application-list row-fluid">
	<div id="dashboard-applications-message" class="application-row ajax-response"></div>
</div>
<div class="application-list row-fluid">
	<div id="applications-nav" class="application-row"></div>
</div>
<?php
$tables = "activity as a, program as p";
$where = "p.id = a.program_id";
if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
	$search_array = explode(" ", $_REQUEST['name']);
	$where .= " AND (";
	foreach ($search_array as $index => $search) {
		if ($index > 0) $where .= " OR ";
		$where .= "a.name LIKE '%".$search."%'";
	}
	$where .= ")";
}
$order = "ORDER BY a.name ASC, p.name ASC";
$bind = "";
$fields = "*";
$fields .= ", a.id AS id, a.name AS name, a.description AS description";
$fields .= ", p.name AS program";
$results = $db->select($tables, $where." ".$order, $bind, $fields);

if (!$activities = $results) {
	echo '<div class="application-row no-applications"><div class="application-column">Returned 0 results.</div></div>';
	if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
		echo '<a href="activities.php">Clear Search</a>';
	}
	echo '<h4><a href="activity.php">Add Activity</a></h4>';
} else {
	echo '<div>Returned '.count($activities).' results.</div>';
	if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
		echo '<a href="activities.php">Clear Search</a>';
	}
	echo '<h4><a href="activity.php">Add Activity</a></h4>';
?>
<div class="application-list row-fluid">
<?php
	$current_char = "";
	$section = 0;
	foreach ($activities as $activity) {

		$new_char = strtoupper(substr($activity['name'], 0, 1));
		if ($new_char != $current_char) {
			$section++;
			echo '<script>$(\'#applications-nav\').append(\'<a href="#sect-'.$section.'" class="scroll-to">'.$new_char.'</a>\');</script>';
			echo '<h3 id="sect-'.$section.'" class="application-title"><span class="block-letter">'.$new_char.'</span><span class="scroll-top">top</span></h3>';
			echo '<div class="application-header">';
			echo '<div class="application-column span3">Activity Name</div>';
			echo '<div class="application-column span3">Program</div>';
			echo '<div class="application-column span5">Description</div>';
			echo '<div class="application-column span1"></div>';
			echo '</div>';
			$current_char = $new_char; 
		}

		echo '<div class="application-row">';

		echo '<div class="application-column span3">';
		echo $activity['name'];
		echo '</div>';
		echo '<div class="application-column span3">';
		echo $activity['program'];
		echo '</div>';
		echo '<div class="application-column span5">';
		echo $activity['description'];
		echo '</div>';

		echo '<div class="application-actions span1">';
		echo '<ul class="pull-right">';
		echo '<li class="dropdown">';
		echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars fa-lg fa-fw"></i></a>';
		echo '<ul class="dropdown-menu">';
		echo '<li><a href="activity.php?activityID='.$activity['id'].'" class=""><i class="fa fa-edit fa-lg fa-fw"></i> Edit</a></li>';
//		echo '<li><a href="" data-delete-activity-id='.$activity['id'].'" class="delete"><i class="fa fa-trash fa-lg fa-fw"></i> Delete</a></li>';
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

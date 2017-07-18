<?php
include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

//set taxonomy
include("config/register.config.php");

// boot the non-admins
if ($_SESSION['user_role'] != "admin" && $_SESSION['user_role'] != "manager" && $_SESSION['user_role'] != "staff") {
	header("Location: /");
	exit();
}

include('header.php');
//include("sidebar.php");
?>
<div class="row-fluid" style="margin: 0 auto 30px;">
	<div class="page-header">
		<h2>Reports</h2>
		<h3>Application Summary by Location/Program</h3>
	</div>
	<div>
		<form id="dashboard-applications" name="dashboard-applications" method="post" action="admin-dashboard.php">
			<input id="dashboard-applications-register-id" type="hidden" name="registerID" value="" />
			<div class="application-list row-fluid">
				<div class="application-header">
<?php
$query = "1 = 1";
$query .= " ORDER BY name ASC";
$programs = $db->select("program", $query);
$total_programs = count($programs);
if ($total_programs > 11) {
	$location_cols = 1;
} else {
	$location_cols = 12 - $total_programs;
}
?>
					<div class="application-column span<?php echo $location_cols; ?>">Location</div>
<?php //			<div class="application-column span9"> ?>
<?php
	foreach ($programs as $program) {
		echo '<div class="span1" style="word-wrap: break-word;">'.$program['name'].'</div>';
	}
?>
<?php //			</div> ?>
				</div>
				<div id="dashboard-applications-message" class="application-row ajax-response"></div>
<?php
$fields = "l.id, l.name, l.disabled, l.deleted";
$from = "location AS l";
$where = "(l.disabled IS NULL OR l.disabled = '0000-00-00 00:00:00')";
$where .= " AND (l.deleted IS NULL OR l.deleted = '0000-00-00 00:00:00')";
$order = " ORDER BY l.name ASC";
if ($locations = $db->select($from, $where." ".$order, "", $fields)) {
	foreach ($locations as $location) {
		$location_name = $location['name'];

		echo '<div class="application-row">';
		echo '<div class="application-column span'.$location_cols.'">'.$location['name'].'</div>';
//		echo '<div class="application-column span9">';
		foreach ($programs as $program) {
			echo '<div class="application-actions span1">';
				if ($location_programs = $db->select("location_program", "location_id = '".$location['id']."' AND program_id = '".$program['id']."'")) {

					$where = "submitted != '' AND location_id = '".$location['id']."' AND program_id = '".$program['id']."'";
					if ($register_results = $db->select("register", $where)) {
						echo '<a title="'.count($register_results).' Application(s)" class="action-view" href="print.php?locationID='.$location['id'].'&programID='.$program['id'].'&reportType=summary" target="_blank">PDF</a>';
					} else {
						echo '<span title="0 Application(s)" class="action-view disabled">PDF</span>';
					}
				} else {
					echo '<span title="Not Offered" class="action-disabled">--</span>';
				}
				echo '</div>';
			}
	//		echo '</div>';
			echo '</div>';
		}
}
?>
			</div>
		</form>
	</div>
</div>
<?php include('footer.php'); ?>

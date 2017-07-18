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
?>

<div class="row-fluid" style="margin: 0 auto 30px;">
	<div class="page-header">
		<h2><?php
$CFG['application_statuses']['all'] = "All";
if (isset($_REQUEST['status']) && $_REQUEST['status'] != '') {
	echo $CFG['application_statuses'][$_REQUEST['status']];
} else {
	echo $CFG['application_statuses']['accepted'];
}
?> Applications</h2>
		<h3>Search for an Application</h3>
	</div>
<?php //<div class="row-fluid"> ?>
<?php //include("sidebar.php"); ?>
<?php //<div class="span10"> ?>
	<div class="span12" style="float: right; height: 40px; padding: 0 0 15px; margin: -10px 0 0; text-align: right;">
		<form class="form-search" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<div class="input-append span12">
				<select type="search" name="status" class="span2 search-query">
					<?php
					foreach ($CFG['application_statuses'] as $key => $value) {
						echo '<option value="'.$key.'"';
						if (isset($_REQUEST['status']) && $_REQUEST['status'] != '') {
							if ($_REQUEST['status'] == $key) {
								echo " selected";
							}
						}
						echo '>'.$value.'</option>';
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
									echo " selected";
								}
							}
							echo '>'.$location['name'].'</option>';
						}
					}
					?>
				</select>
				<button class="btn" style="background:transparent url('images/glyphicons-halflings.png') -44px 6px no-repeat; text-indent: -9999px; margin-right: 15px;">Search</button>
				<select type="search" name="courseID" class="span2 search-query">
					<option value="">All Courses</option>
					<?php
					$fields = "c.*";
					$from = "course AS c";
					$where = "(c.disabled IS NULL OR c.disabled = '0000-00-00 00:00:00')";
					$where .= " AND (c.canceled IS NULL OR c.canceled = '0000-00-00 00:00:00')";
					$where .= " AND (c.deleted IS NULL OR c.deleted = '0000-00-00 00:00:00')";
					$order = " ORDER BY c.id DESC";
					if ($courses = $db->select($from, $where." ".$order, "", $fields)) {
						foreach ($courses as $course) {
							echo '<option value="'.$course['id'].'"';
							if (isset($_REQUEST['courseID']) && $_REQUEST['courseID'] != '') {
								if ($_REQUEST['courseID'] == $course['id']) {
									echo " selected";
								}
							}
							echo '>#'.str_pad($course['id'], 5, '0', STR_PAD_LEFT).' '.$course['name'].'</option>';
						}
					}
					?>
				</select>
				<button class="btn" style="background:transparent url('images/glyphicons-halflings.png') -44px 6px no-repeat; text-indent: -9999px; margin-right: 15px;">Search</button>
				<select type="search" name="userID" class="span2 search-query">
					<option value="">All Users</option>
					<option value="0">Unassigned (not connected to an account)</option>
					<?php
					$users = $db->select("users", "1=1 ORDER BY user_first_name ASC, user_last_name ASC", "", "user_id, user_first_name, user_last_name, user_email");
					foreach ($users as $user) {
						echo '<option value="'.$user['user_id'].'"';
						if (isset($_REQUEST['userID']) && $_REQUEST['userID'] != '') {
							if ($_REQUEST['userID'] == $user['user_id']) {
								echo " selected";
							}
						}
						echo '>'.$user['user_first_name'].' '.$user['user_last_name'].' ('.$user['user_email'].')</option>';
					}
					?>
				</select>
				<button class="btn" style="background:transparent url('images/glyphicons-halflings.png') -44px 6px no-repeat; text-indent: -9999px; margin-right: 15px;">Search</button>
				<input type="search" name="name" class="span2 search-query" placeholder="Name" value="<?php if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') echo $_REQUEST['name']; ?>">
				<button class="btn" style="background:transparent url('images/glyphicons-halflings.png') -44px 6px no-repeat; text-indent: -9999px;">Search</button>
			</div>
		</form>
	</div>

<form id="dashboard-applications" name="dashboard-applications" method="post" action="applications.php">
<input id="dashboard-applications-register-id" type="hidden" name="registerID" value="" />
<h3>By Child's Name<?php
if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') { echo ' Like <em>"'.$_REQUEST['name'].'"</em>'; }
?>
</h3>
<div class="application-list row-fluid">
	<div id="dashboard-applications-message" class="application-row ajax-response"></div>
</div>
<div class="application-list row-fluid">
	<div id="applications-nav" class="application-row"></div>
</div>
<?php
$tables = "register as r";
//$tables = "register as r, child as c";
$fields = "r.*";
$fields .= ", (SELECT c.first_name FROM child AS c WHERE c.id = r.child_id) AS child_first_name";
$fields .= ", (SELECT c.last_name FROM child AS c WHERE c.id = r.child_id) AS child_last_name";
$where = "1 = 1";
if (isset($_REQUEST['status']) && $_REQUEST['status'] != '') {
	switch ($_REQUEST['status']) {
		case "accepted":
			$where .= " AND r.accepted IS NOT NULL AND r.deleted IS NULL"; // where the application has been accepted
			break;
		case "submitted":
			$where .= " AND r.submitted IS NOT NULL AND r.accepted IS NULL AND r.deleted IS NULL"; // where the application has been submitted 
			break;
		case "incomplete":
			$where .= " AND r.submitted IS NULL AND r.accepted IS NULL AND r.deleted IS NULL"; // where the application has not been submitted
			break;
		case "deleted":
			$where .= " AND r.deleted IS NOT NULL"; // where the application has been deleted 
			break;
		case "all":
			break;
		default:
			$where .= " AND r.accepted IS NOT NULL AND r.deleted IS NULL"; // where the application has been accepted 
			break;
	}
} else {
	$where .= " AND r.accepted IS NOT NULL AND r.deleted IS NULL"; // where the application has been accepted
}
//die($where);
if (isset($_REQUEST['userID']) && $_REQUEST['userID'] != '') {
	$where .= " AND r.user_id = '".$_REQUEST['userID']."'"; // where there is an assigned user id
} else {
	$where .= " AND r.user_id <> '0'"; // where there is an assigned user id
}
if (isset($_REQUEST['locationID']) && $_REQUEST['locationID'] != '') {
	$where .= " AND r.location_id = '".$_REQUEST['locationID']."'";
}
if (isset($_REQUEST['courseID']) && $_REQUEST['courseID'] != '') {
	$where .= " AND r.course_id = '".$_REQUEST['courseID']."'";
}
if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
	$search_array = explode(" ", $_REQUEST['name']);
	$where .= " AND (";
	foreach ($search_array as $index => $search) {
		if ($index > 0) $where .= " OR ";
		$where .= "child_first_name LIKE '%".$search."%' OR child_last_name LIKE '%".$search."%'";
	}
	$where .= ")";
}
$order = "ORDER BY child_first_name ASC, child_last_name ASC, r.updated DESC";
$bind = "";
//die($where);
$results = $db->select($tables, $where." ".$order, $bind, $fields);
if (!$register = $results) {
	echo '<div class="application-row no-applications"><div class="application-column">Returned 0 results.</div></div>';
} else {
	echo '<div>Returned '.count($register).' results.</div>';
?>
<div class="application-list row-fluid">
<?php
	$current_char = "";
	$section = 0;
	foreach ($register as $registration) {

		if ($registration['child_id']) {
			$results = $db->select("child", "id = '".$registration['child_id']."'");
			$child = $results[0];
		} else {
			$child = array();
			$child['first_name'] = "";
			$child['last_name'] = "";
		}

		if ($registration['child_id']) {
			$new_char = strtoupper(substr($child['first_name'], 0, 1));
		} else {
			$new_char = "?";
		}
		if ($new_char != $current_char) {
			$section++;
			echo '<script>$(\'#applications-nav\').append(\'<a href="#sect-'.$section.'" class="scroll-to">'.$new_char.'</a>\');</script>';
			echo '<h3 id="sect-'.$section.'" class="application-title"><span class="block-letter">'.$new_char.'</span><span class="scroll-top">top</span></h3>';
			echo '<div class="application-header">';
			echo '<div class="application-column span2">Child Name</div>';
			echo '<div class="application-column span1">Summit ID</div>';
			echo '<div class="application-column span2">Guardian(s)</div>';
			echo '<div class="application-column span1">Location</div>';
			echo '<div class="application-column span1">Program</div>';
			echo '<div class="application-column span2">Course</div>';
			echo '<div class="application-column span2">Status</div>';
			echo '<div class="application-column span1"></div>';
			echo '</div>';
			$current_char = $new_char; 
		}

		echo '<div class="application-row">';

		if ($registration['child_id']) {
			echo '<div class="application-column span2">';
			echo '<a href="child.php?childID='.$registration['child_id'].'">'.$child['first_name'].' '.$child['last_name'].'</a>';
			echo '</div>';
			if ($child['summit_id']) {
				echo '<div class="application-column span1 summit-id">'.$child['summit_id'].'</div>';
			} else {
				echo '<div class="application-column span1">Unassigned</div>';
			}
		} else {
			echo '<div class="application-column span3">';
			echo "No child assigned";
			echo '</div>';
		}

		echo '<div class="application-column span2">';
		if ($registration['child_id']) {
			$where = "1=1";
			$where .= " AND child_id = '".$registration['child_id']."'";
			$order = "ORDER BY guardian_first_name ASC, guardian_last_name ASC";
			$fields = "guardian_id, child_id, relationship";
			$fields .= ", (SELECT first_name FROM guardian AS g WHERE g.id = guardian_id) AS guardian_first_name";
			$fields .= ", (SELECT last_name FROM guardian AS g WHERE g.id = guardian_id) AS guardian_last_name";
			if ($guardians = $db->select("relationship", $where." ".$order, "", $fields)) {
				foreach ($guardians as $key => $guardian) {
					if ($key > 0) echo ', ';
					echo '<a href="guardian.php?guardianID='.$guardian['guardian_id'].'">'.$guardian['guardian_first_name'].' '.$guardian['guardian_last_name'].'</a>';
				}
			} else {
				echo 'No guardians assigned';
			}
		} else {
			echo 'No guardians assigned';
		}
		echo '</div>';

		if ($registration['location_id']) {
			$query = "id = '".$registration['location_id']."'";
			$query .= " LIMIT 0,1";
			if ($result = $db->select("location", $query)) {
				$location = $result[0];
				echo '<div class="application-column span1">'.$location['name'].'</div>';
			} else {
				echo '<div class="application-column span1">Invalid Location '.$registration['location_id'].'</div>';
			}
		} else {
			echo '<div class="application-column span1">Unassigned</div>';
		}

		if ($registration['program_id']) {
			$query = "id = '".$registration['program_id']."'";
			$query .= " LIMIT 0,1";
			if ($result = $db->select("program", $query)) {
				$program = $result[0];
				echo '<div class="application-column span1">'.$program['name'].'</div>';
			} else {
				echo '<div class="application-column span1">Invalid Program '.$registration['program_id'].'</div>';
			}
		} else {
			echo '<div class="application-column span1">Unassigned</div>';
		}

		if ($registration['course_id']) {
			$query = "id = '".$registration['course_id']."'";
			$query .= " LIMIT 0,1";
			if ($result = $db->select("course", $query)) {
				$course = $result[0];
				echo '<div class="application-column span2">'.$course['name'].'</div>';
			} else {
				echo '<div class="application-column span2">Invalid Course '.$registration['course_id'].'</div>';
			}
		} else {
			echo '<div class="application-column span2">Unassigned</div>';
		}

/*
		if ($registration['payment_type']) {
			echo '<div class="application-column span1">'.$registration['payment_type'].'</div>';
		} else {
			echo '<div class="application-column span1">?</div>';
		}

		echo '<div class="application-column span1">';
		if ($registration['waivers'] == 'yes') {
			echo 'Yes';
		} else {
			echo 'No';
		}
		echo '</div>';
*/

		echo '<div class="application-column span2">';
			if ($registration['accepted'] != '' && $registration['accepted'] != '0000-00-00 00:00:00') {
				echo 'Accepted ';
				$date = new DateTime(strftime('%Y-%m-%d %H:%M:%S', strtotime($registration['accepted'])));
				echo $date->format('m/d/Y g:i a');
			} elseif ($registration['submitted'] != '' && $registration['submitted'] != '0000-00-00 00:00:00') {
				echo 'Submitted ';
				$date = new DateTime(strftime('%Y-%m-%d %H:%M:%S', strtotime($registration['submitted'])));
				echo $date->format('m/d/Y g:i a');
			} else{
				echo 'Updated ';
				$date = new DateTime(strftime('%Y-%m-%d %H:%M:%S', strtotime($registration['updated'])));
				echo $date->format('m/d/Y g:i a');
			}
		echo '</div>';

		echo '<div class="application-actions span1">';
		echo '<ul class="pull-right">';
		echo '<li class="dropdown">';
		echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars fa-lg fa-fw"></i></a>';
		echo '<ul class="dropdown-menu">';
		if ($registration['submitted'] != '') {
			echo '<li><a href="print.php?registerID='.$registration['id'].'" class="" target="_blank"><i class="fa fa-file-pdf-o fa-lg fa-fw"></i> View PDF</a></li>';
			echo '<li><a href="register.php?registerID='.$registration['id'].'" class=""><i class="fa fa-edit fa-lg fa-fw"></i> Edit</a></li>';
			echo '<li><a href="" data-delete-registration-id='.$registration['id'].'" class="delete"><i class="fa fa-trash fa-lg fa-fw"></i> Delete</a></li>';
			if ($registration['accepted'] == '') {
				echo '<li><a href="" data-accept-registration-id='.$registration['id'].'" class="accept"><i class="fa fa-check fa-lg fa-fw"></i> Accept</a></li>';
			}
		} else {
			echo '<li><a href="register.php?registerID='.$registration['id'].'" class=""><i class="fa fa-edit fa-lg fa-fw"></i> Edit</a></li>';
			echo '<li><a href="" data-delete-registration-id='.$registration['id'].'" class="delete"><i class="fa fa-trash fa-lg fa-fw"></i> Discard</a></li>';
		}
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
<?php //</div> ?>
<?php //</div> ?>
<?php include('footer.php'); ?>

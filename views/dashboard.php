<?php
include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

//set taxonomy
include("config/register.config.php");

include('header.php');
//include("sidebar.php");
?>
<?php
/*
// if you need the user's information, just put them into the $_SESSION variable and output them here
echo '<h2>' . WORDING_WELCOME . ', ' . $_SESSION['user_first_name'] . ' ' . $_SESSION['user_last_name']  . ".</h2>";
echo WORDING_YOU_ARE_LOGGED_IN_AS . $_SESSION['user_name'] . "<br />";
//echo WORDING_PROFILE_PICTURE . '<br/><img src="' . $login->user_gravatar_image_url . '" />;
echo WORDING_PROFILE_PICTURE . '<br/>' . $login->user_gravatar_image_tag;
?>

<div>
    <a href="index.php?logout"><?php echo WORDING_LOGOUT; ?></a>
    <a href="edit.php"><?php echo WORDING_EDIT_USER_DATA; ?></a>
    <a href="register.php"><?php echo WORDING_ENROLL; ?></a>
</div>
<? */ ?>

<div class="row-fluid" style="margin: 0 auto 30px;">
	<div class="page-header">
		<h2>Dashboard</h2>
		<h3><?php echo date('l, jS \of F, Y, h:i A'); ?></h3>
	</div>
<?php
// check system status
$result = $db->select("system", "id = '1'");
$system = $result[0];
if ($system['message'] != "" && $system['message'] != NULL) {
	echo '<div class="system-message">'.$system['message'].'</div>';
}
?>
<form id="dashboard-applications" name="dashboard-applications" method="post" action="dashboard.php">
<input id="dashboard-applications-register-id" type="hidden" name="registerID" value="" />
<div class="application-list row-fluid">
<?php
if ($_SESSION['user_role'] != "admin") {
?>
	<h3>Applications</h3>
<?php
} else {
?>
	<h3>Completed Applications</h3>
<?php
}
?>
	<div class="application-header">
		<div class="application-column span2">Child Name</div>
		<div class="application-column span2">Program</div>
		<div class="application-column span2">Location</div>
		<div class="application-column span2">Course</div>
		<div class="application-column span2">Activity</div>
		<div class="application-column span1">Status</div>
		<div class="application-column span1"></div>
	</div>
	<div id="dashboard-applications-message" class="application-row ajax-response"></div>
<?php
$query = "1 = 1";
if ($_SESSION['user_role'] != "admin") {
	$query .= " AND user_id = '".$_SESSION['user_id']."'";
} else {
	$query .= " AND submitted <> ''";
}
$query .= " ORDER BY updated DESC";
$results = $db->select("register", $query);
if (!$register = $results) {
	echo '<div class="application-row"><div class="application-column span12"><p>No applications for registration have been started.</p></div></div>';
} else {
	foreach ($register as $registration) {
		echo '<div class="application-row">';
		echo '<div class="application-column span2">';
		if ($registration['child_id']) {
			$results = $db->select("child", "id = '".$registration['child_id']."'");
			$child = $results[0];
			echo $child['first_name'].' '.$child['last_name'];
		} else {
			echo "No child assigned";
		}
		echo '</div>';

		$results = $db->select("program", "id = '".$registration['program_id']."'");
		$program = $results[0];
		if ($registration['program_id']) {
			echo '<div class="application-column span2">'.$program['name'].'</div>';
		} else {
			echo '<div class="application-column span2">Unassigned</div>';
		}

		$results = $db->select("location", "id = '".$registration['location_id']."'");
		$location = $results[0];
		if ($registration['location_id']) {
			echo '<div class="application-column span2">'.$location['name'].'</div>';
		} else {
			echo '<div class="application-column span2">Unassigned</div>';
		}

		$results = $db->select("course", "id = '".$registration['course_id']."'");
		$course = $results[0];
		if ($registration['course_id']) {
			echo '<div class="application-column span2">'.$course['name'].'</div>';
		} else {
			echo '<div class="application-column span2">Unassigned</div>';
		}

		$results = $db->select("activity", "id = '".$registration['activity_id']."'");
		$activity = $results[0];
		if ($registration['activity_id']) {
			echo '<div class="application-column span2">'.$activity['name'].'</div>';
		} else {
			echo '<div class="application-column span2">Unassigned</div>';
		}

		echo '<div class="application-column span1">';
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
				echo '<li><a href="print.php?registerID='.$registration['id'].'" class="" target="_blank"><i class="fa fa-file-pdf-o fa-lg fa-fw"></i> Print Application</a></li>';
				if ($program['form_waivers'] != '') {
					echo '<li><a href="/'.$program['form_waivers'].'" class="" target="_blank"><i class="fa fa-file-pdf-o fa-lg fa-fw"></i> Print Waivers</a></li>';
				}
				if ($program['form_cc_authorization'] != '') {
					echo '<li><a href="/'.$program['form_cc_authorization'].'" class="" target="_blank"><i class="fa fa-file-pdf-o fa-lg fa-fw"></i> Print CC Auhorization</a></li>';
				}
				if ($program['form_eft_authorization'] != '') {
					echo '<li><a href="/'.$program['form_eft_authorization'].'" class="" target="_blank"><i class="fa fa-file-pdf-o fa-lg fa-fw"></i> Print EFT Auhorization</a></li>';
				}
				if ($registration['accepted'] == '') {
					echo '<li><a href="register.php?registerID='.$registration['id'].'" class=""><i class="fa fa-edit fa-lg fa-fw"></i> Edit</a></li>';
					echo '<li><a href="" data-delete-registration-id='.$registration['id'].'" class="delete"><i class="fa fa-trash fa-lg fa-fw"></i> Delete</a></li>';
				}
			} else {
				echo '<li><a href="register.php?registerID='.$registration['id'].'" class=""><i class="fa fa-edit fa-lg fa-fw"></i> Continue</a></li>';
				echo '<li><a href="" data-delete-registration-id='.$registration['id'].'" class="delete"><i class="fa fa-trash fa-lg fa-fw"></i> Discard</a></li>';
			}
			echo '</ul>';
			echo '</li>';
			echo '</ul>';
		echo '</div>';

		echo '</div>';
	}
}
echo '<div class="application-row"><div class="application-column span12"><a title="New Application" href="register.php">New Application</a></div></div>';
?>
</div>
</form>

<?php
if ($_SESSION['user_role'] != "admin") {
?>

<form id="dashboard-child-profiles" name="dashboard-child-profiles" method="post" action="dashboard.php">
<input id="dashboard-child-profiles-child-id" type="hidden" name="childID" value="" />
<div class="application-list row-fluid">
	<h3>Child Profiles</h3>
	<div class="application-header">
		<div class="application-column span2">Child Name</div>
		<div class="application-column span2">Birth Date</div>
		<div class="application-column span2">Gender</div>
		<div class="application-column span2">Summit ID</div>
		<div class="application-column span2">Updated</div>
		<div class="application-column span2"></div>
	</div>
	<div id="dashboard-applications-message" class="application-row ajax-response"></div>
<?php
$results = $db->select("child", "user_id = '".$_SESSION['user_id']."' ORDER BY first_name ASC, last_name ASC, updated DESC");
if (!$children = $results) {
	echo '<div class="application-row"><div class="application-column span12"><p>No children have been added.</p></div></div>';
} else {
	foreach ($children as $child) {
		echo '<div class="application-row">';
		echo '<div class="application-column span2">';
		if ($child['photo']) {
			echo '<img title="'.$child['first_name'].'\'s Photo" src="'.$child['photo'].'" style="height: 20px;" class="child-avatar fancybox" /> &nbsp; ';
		}
		echo $child['first_name'].' '.$child['last_name'].'</div>';
		$date = new DateTime($child['birth_date']);
		echo '<div class="application-column span2">'.$date->format('jS F Y').'</div>';
		echo '<div class="application-column span2">'.$child['gender'].'</div>';
		if ($child['summit_id']) {
			echo '<div class="application-column span2">'.$child['summit_id'].'</div>';
		} else {
			echo '<div class="application-column span2">Unassigned</div>';
		}
		$date = new DateTime(strftime('%Y-%m-%d %H:%M:%S', strtotime($child['updated'])));
		echo '<div class="application-column span2">'.$date->format('m/d/Y g:i a').'</div>';

		echo '<div class="application-actions span2">';
		echo '<ul class="pull-right">';
		echo '<li class="dropdown">';
		echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars fa-lg fa-fw"></i></a>';
		echo '<ul class="dropdown-menu">';
		echo '<li><a href="child.php?childID='.$child['id'].'""><i class="fa fa-edit fa-lg fa-fw"></i> Edit</a></li>';
		echo '</ul>';
		echo '</li>';
		echo '</ul>';
		echo '</div>';
		echo '</div>';
	}
}
echo '<div class="application-row"><div class="application-column span12"><a title="Add Child" href="child.php">Add Child</a></div></div>';
?>
</div>
</form>

<form id="dashboard-guardian-profiles" name="dashboard-guardian-profiles" method="post" action="dashboard.php">
<input id="dashboard-guardian-profiles-guardian-id" type="hidden" name="guardianID" value="" />
<div class="application-list row-fluid">
	<h3>Guardian Profiles</h3>
	<div class="application-header">
		<div class="application-column span2">Guardian Name</div>
		<div class="application-column span2">Email</div>
		<div class="application-column span2">Day Phone</div>
		<div class="application-column span2">Day Address</div>
		<div class="application-column span2">Updated</div>
		<div class="application-column span2"></div>
	</div>
<?php
$results = $db->select("guardian", "user_id = '".$_SESSION['user_id']."' ORDER BY first_name ASC, last_name ASC, updated DESC");
if (!$guardians = $results) {
	echo '<div class="application-row"><div class="application-column span12"><p>No guardians have been added.</p></div></div>';
} else {
	foreach ($guardians as $guardian) {
		echo '<div class="application-row">';
		echo '<div class="application-column span2">'.$guardian['first_name'].' '.$guardian['last_name'].'</div>';
		echo '<div class="application-column span2">'.$guardian['email'].'</div>';
		echo '<div class="application-column span2">'.$guardian['daytime_phone'].'</div>';
		echo '<div class="application-column span2">'.$guardian['daytime_address'].', '.$guardian['daytime_city'].', '.$guardian['daytime_province'].'</div>';
		$date = new DateTime(strftime('%Y-%m-%d %H:%M:%S', strtotime($guardian['updated'])));
		echo '<div class="application-column span2">'.$date->format('m/d/Y g:i a').'</div>';

		echo '<div class="application-actions span2">';
		echo '<ul class="pull-right">';
		echo '<li class="dropdown">';
		echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars fa-lg fa-fw"></i></a>';
		echo '<ul class="dropdown-menu">';
		echo '<li><a href="guardian.php?guardianID='.$guardian['id'].'""><i class="fa fa-edit fa-lg fa-fw"></i> Edit</a></li>';
		echo '<li><a href="" data-delete-guardian-id='.$guardian['id'].'" class="delete"><i class="fa fa-trash fa-lg fa-fw"></i> Delete</a></li>';
		echo '</ul>';
		echo '</li>';
		echo '</ul>';
		echo '</div>';
		echo '</div>';
	}
}
echo '<div class="application-row"><div class="application-column span12"><a title="Add Guardian" href="guardian.php">Add Guardian</a></div></div>';
?>
</div>
</form>

<?php
}
?>
</div>
<?php include('footer.php'); ?>

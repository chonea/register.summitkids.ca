<?php
include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

//set taxonomy
include("config/register.config.php");

// boot the non-admins
if ($_SESSION['user_role'] != "admin" && $_SESSION['user_role'] != "manager") {
	header("Location: /");
	exit();
}

include('header.php');
//include("sidebar.php");
?>

<div class="row-fluid" style="margin: 0 auto 30px;">
	<div class="page-header">
		<h2>Users</h2>
		<h3>Search for a User</h3>
	</div>
	<div class="span12" style="float: right; height: 40px; padding: 0 0 15px; margin: -10px 0 0; text-align: right;">
		<form class="form-search" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<div class="input-append span12">
				<select type="search" name="role" class="span2 search-query">
					<option value="">All User Roles</option>
					<?php
					foreach ($CFG['user_roles'] as $key => $value) {
						echo '<option value="'.$key.'"';
						if (isset($_REQUEST['role'])) {
							if ($_REQUEST['role'] == $key) {
								echo "selected";
							}
						}
						echo '>'.$value.'</option>';
					}
					?>
				</select>
				<button class="btn" style="background:transparent url('images/glyphicons-halflings.png') -44px 6px no-repeat; text-indent: -9999px; margin-right: 15px;">Search</button>
				<input type="search" name="name" class="span2 search-query" placeholder="Search by user first or last name" value="<?php if (isset($_REQUEST['name'])) echo $_REQUEST['name']; ?>">
				<button class="btn" style="background:transparent url('images/glyphicons-halflings.png') -44px 6px no-repeat; text-indent: -9999px;">Search</button>
			</div>
		</form>
	</div>

<form id="dashboard-applications" name="dashboard-applications" method="post" action="users.php">
<input id="dashboard-applications-register-id" type="hidden" name="registerID" value="" />
<h3>By User's Name<?php if (isset($_REQUEST['name']) && $_REQUEST['name']) { echo ' Like <em>"'.$_REQUEST['name'].'"</em>'; } ?></h3>
<div class="application-list row-fluid">
	<div id="dashboard-applications-message" class="application-row ajax-response"></div>
</div>
<div class="application-list row-fluid">
	<div id="applications-nav" class="application-row"></div>
</div>
<?php
$tables = "users";
$where = "1 = 1";
if (isset($_REQUEST['role']) && $_REQUEST['role'] != '') {
	$where .= " AND role = '".$_REQUEST['role']."'";
}
if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
	$search_array = explode(" ", $_REQUEST['name']);
	$where .= " AND (";
	foreach ($search_array as $index => $search) {
		if ($index > 0) $where .= " OR ";
		$where .= "user_first_name LIKE '%".$search."%' OR user_last_name LIKE '%".$search."%'";
	}
	$where .= ")";
}
$order = "ORDER BY user_first_name ASC, user_last_name ASC, user_registration_datetime DESC";
$bind = "";
$fields = "*";
$results = $db->select($tables, $where." ".$order, $bind, $fields);

if (!$users = $results) {
	echo '<div class="application-row no-applications"><div class="application-column">Returned 0 results.</div></div>';
	if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
		echo '<a href="users.php">Clear Search</a>';
	}
	echo '<h4><a href="user.php">Add User</a></h4>';
} else {
	echo '<div>Returned '.count($users).' results.</div>';
	if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
		echo '<a href="users.php">Clear Search</a>';
	}
	echo '<h4><a href="user.php">Add User</a></h4>';
?>
<div class="application-list row-fluid">
<?php
	$current_char = "";
	$section = 0;
	foreach ($users as $user) {

		$new_char = strtoupper(substr($user['user_first_name'], 0, 1));
		if ($new_char != $current_char) {
			$section++;
			echo '<script>$(\'#applications-nav\').append(\'<a href="#sect-'.$section.'" class="scroll-to">'.$new_char.'</a>\');</script>';
			echo '<h3 id="sect-'.$section.'" class="application-title"><span class="block-letter">'.$new_char.'</span><span class="scroll-top">top</span></h3>';
			echo '<div class="application-header">';
			echo '<div class="application-column span1">First Name</div>';
			echo '<div class="application-column span2">Last Name</div>';
			echo '<div class="application-column span2">Email</div>';
			echo '<div class="application-column span1">Status</div>';
			echo '<div class="application-column span1">Role</div>';
			echo '<div class="application-column span1">Location</div>';
			echo '<div class="application-column span3">Created</div>';
			echo '<div class="application-column span1"></div>';
			echo '</div>';
			$current_char = $new_char; 
		}

		echo '<div class="application-row">';

		echo '<div class="application-column span1">';
		echo $user['user_first_name'];
		echo '</div>';

		echo '<div class="application-column span2">';
		echo $user['user_last_name'];
		echo '</div>';

		echo '<div class="application-column span2">';
		echo $user['user_email'];
		echo '</div>';

		if ($user['user_active'] == 1) {
			echo '<div class="application-column span1">Active</div>';
		} else {
			echo '<div class="application-column span1">Needs Verification</div>';
		}

		if ($user['role']) {
			if (isset($CFG['user_roles'][$user['role']])) {
				echo '<div class="application-column span1">'.$CFG['user_roles'][$user['role']].'</div>';
			} else {
				echo '<div class="application-column span1">Invalid "'.$user['role'].'"</div>';
			}
		} else {
			echo '<div class="application-column span1">Unassigned</div>';
		}

		echo '<div class="application-column span1">';
		if ($result = $db->select("location", "id = '".$user['location_id']."'")) {
			$location = $result[0];
			echo $location['name'];
		} else {
			echo "Unassigned";
		}
		echo '</div>';

		$date = new DateTime($user['user_registration_datetime']);
		echo '<div class="application-column span3">'.$date->format('l jS F Y \a\t g:i a').'</div>';

		echo '<div class="application-actions span1">';
		echo '<ul class="pull-right">';
		echo '<li class="dropdown">';
		echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars fa-lg fa-fw"></i></a>';
		echo '<ul class="dropdown-menu">';
		echo '<li><a href="user.php?userID='.$user['user_id'].'" class=""><i class="fa fa-edit fa-lg fa-fw"></i> Edit</a></li>';
//		echo '<li><a href="" data-delete-user-id='.$user['user_id'].'" class="delete"><i class="fa fa-trash fa-lg fa-fw"></i> Delete</a></li>';
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

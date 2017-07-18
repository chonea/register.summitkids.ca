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
		<h2>Locations</h2>
		<h3>Manage Locations</h3>
	</div>
	<div class="span12" style="float: right; height: 40px; padding: 0 0 15px; margin: -10px 0 0; text-align: right;">
		<form class="form-search" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<div class="input-append span12">
				<input type="search" name="name" class="span2 search-query" placeholder="Location Name" value="<?php if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') echo $_REQUEST['name']; ?>">
				<button class="btn" style="background:transparent url('images/glyphicons-halflings.png') -44px 6px no-repeat; text-indent: -9999px;">Search</button>
			</div>
		</form>
	</div>

<form id="dashboard-locations" name="dashboard-locations" method="post" action="locations.php">
<input id="dashboard-locations-location-id" type="hidden" name="locationID" value="" />
<?php
if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
	echo '<h3>Search Location Name Like <em>"'.$_REQUEST['name'].'"</em></h3>';
} else {
	echo '<h3>All Locations</h3>';
}
?>
<div class="application-list row-fluid">
	<div id="dashboard-applications-message" class="application-row ajax-response"></div>
</div>
<div class="application-list row-fluid">
	<div id="applications-nav" class="application-row"></div>
</div>
<?php
$tables = "location";
$where = "1 = 1";
if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
	$search_array = explode(" ", $_REQUEST['name']);
	$where .= " AND (";
	foreach ($search_array as $index => $search) {
		if ($index > 0) $where .= " OR ";
		$where .= "name LIKE '%".$search."%'";
	}
	$where .= ")";
}
$order = "ORDER BY name ASC";
$bind = "";
$fields = "*";
$results = $db->select($tables, $where." ".$order, $bind, $fields);
if (!$locations = $results) {
	echo '<div class="application-row no-applications"><div class="application-column">Returned 0 results.</div></div>';
	if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
		echo '<a href="locations.php">Clear Search</a>';
	}
	echo '<h4><a href="location.php">Add Location</a></h4>';
} else {
	echo '<div>Returned '.count($locations).' results.</div>';
	if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
		echo '<a href="locations.php">Clear Search</a>';
	}
	echo '<h4><a href="location.php">Add Location</a></h4>';
?>
<div class="application-list row-fluid">
<?php
	$current_char = "";
	$section = 0;
	foreach ($locations as $location) {

		$new_char = strtoupper(substr($location['name'], 0, 1));
		if ($new_char != $current_char) {
			$section++;
			echo '<script>$(\'#applications-nav\').append(\'<a href="#sect-'.$section.'" class="scroll-to">'.$new_char.'</a>\');</script>';
			echo '<h3 id="sect-'.$section.'" class="application-title"><span class="block-letter">'.$new_char.'</span><span class="scroll-top">top</span></h3>';
			echo '<div class="application-header">';
			echo '<div class="application-column span2">Location Name</div>';
			echo '<div class="application-column span2">Address</div>';
			echo '<div class="application-column span1">Phone</div>';
			echo '<div class="application-column span1">Fax</div>';
			echo '<div class="application-column span2">Email</div>';
			echo '<div class="application-column span3">URL</div>';
			echo '<div class="application-column span1"></div>';
			echo '</div>';
			$current_char = $new_char; 
		}

		echo '<div class="application-row">';

		echo '<div class="application-column span2">';
		echo $location['name'];
		echo '</div>';
		echo '<div class="application-column span2">';
		echo $location['address']."<br>";
		echo $location['city'].", ";
		echo $location['province'].", ";
		echo $location['country']." ";
		echo $location['postal_code'];
		echo '</div>';
		echo '<div class="application-column span1">';
		echo $location['phone'];
		echo '</div>';
		echo '<div class="application-column span1">';
		echo $location['fax'];
		echo '</div>';
		echo '<div class="application-column span2">';
		echo $location['email'];
		echo '</div>';
		echo '<div class="application-column span3">';
		echo '<a target="_blank" href="'.$location['web_url'].'">'.$location['web_url'].'</a>';
		echo '</div>';

		echo '<div class="application-actions span1">';
		echo '<ul class="pull-right">';
		echo '<li class="dropdown">';
		echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars fa-lg fa-fw"></i></a>';
		echo '<ul class="dropdown-menu">';
		echo '<li><a href="location.php?locationID='.$location['id'].'" class=""><i class="fa fa-edit fa-lg fa-fw"></i> Edit</a></li>';
//		echo '<li><a href="" data-delete-location-id='.$location['id'].'" class="delete"><i class="fa fa-trash fa-lg fa-fw"></i> Delete</a></li>';
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

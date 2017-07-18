<?php
//ini_set('display_errors',1);
//error_reporting(E_ALL);
//session_start();

// only logged in users allowed
if (!$_SESSION['user_id']) {
	header("Location: /");
	exit();
}

include("config/db.config.php");
include("classes/php-pdo-wrapper-class/class.db.php");
$db = new db("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass);
$db->setErrorCallbackFunction("echo");

// printing an individual registration form
if (isset($_REQUEST["registerID"])) {
	// if passed in header
	if (isset($_GET["registerID"]) && $_GET["registerID"] != '') {
		$registerID = $_GET["registerID"];
	// if passed from registration form
	} elseif (isset($_POST["form"]) && isset($_POST["registerID"]) && $_POST["registerID"] != '') {
		$registerID = $_POST["registerID"];
		// update register to show this application has been printed
		$update = array(
			"updated" => date("Y-m-d H:i:s"),
			"printed" => date("Y-m-d H:i:s"),
		);
		$where = "id = '".$registerID."'";
		$db->update("register", $update, $where);
	// either came from a cookie or is not set, exit
	} else {
		header("Location: /");
		exit();
	}
// printing a listing from admin, if not, exit
} elseif ((
		$_SESSION['user_role'] == "admin" || $_SESSION['user_role'] == "manager" || $_SESSION['user_role'] == "staff"
	)	&& (
		(isset($_REQUEST["locationID"]) && $_REQUEST["locationID"] != '') || 
		(isset($_REQUEST["programID"]) && $_REQUEST["programID"] != '') || 
		(isset($_REQUEST["courseID"]) && $_REQUEST["courseID"] != '') || 
		(isset($_REQUEST["activityID"]) && $_REQUEST["activityID"] != ''))
	) {
	$registerID = '';
// no idea why we're here, exit
} else {
	header("Location: /");
	exit();
}

if (isset($_REQUEST["reportType"])) {
	$reportType = $_REQUEST["reportType"];
} else {
	$reportType = "all";
}

//set taxonomy
include("config/register.config.php");

require('libraries/fpdf/fpdf.php');

class PDF extends FPDF {

	// Page header
	function Header() {
		global $title, $subject, $author, $register;
		// Logo
		$this->Image('images/summitkids_logofinal.png',10,10,52);
		// Arial bold 8
		$this->SetFont('Arial','',7);
		// Move to the right
		$this->Cell(80);
		// Title
		if ($register['submitted'] != NULL && $register['submitted'] != "0000-00-00 00:00:00") {
			$this->Cell(0,6,'Summit Kids App. No. '.$register['id'].' Submitted '.$register['submitted'],0,1,'R');
		} else {
			$this->Cell(0,6,'Summit Kids App. No. '.$register['id'].' Printed '.$register['printed'],0,1,'R');
		}
		// Arial bold 12
		$this->SetFont('Arial','B',12);
		// Move to the right
		$this->Cell(80);
		// Title
		$this->Cell(0,6,$title,0,1,'R');
		// Program
		$this->SetFont('Arial','B',9);
		$this->Cell(0,5,$subject,0,1,'R');
		// Location
		$this->SetFont('Arial','B',9);
		$this->Cell(0,5,$author,0,0,'R');
		// Line break
		$this->Ln(25);
	}

	// Page footer
	function Footer() {
		global $footer_image;
		// Footer image
		// Position at 5 cm from bottom
		if ($footer_image) {
			$this->SetY(-50);
			$this->Image($footer_image,0.025,252.40,209.85);
		}
/*  REMOVED
		// Position at 4.2 cm from bottom
		$this->SetY(-42);
		$this->SetFont('Arial','',6);
		// Date
//		$this->Cell(0,10,date('Y-m-d H:i:s'),0,0,'L');
		// Page number
//		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}',0,0,'R');
		$this->SetTextColor(200,200,200);
		if (isset($register['id'])) $this->Cell(0,3,'Registration No. '.$register['id'],0,1,'R');
		if (isset($register['printed']) && $register['printed'] != '') $this->Cell(0,3,'Printed '.$register['printed'],0,1,'R');
		if (isset($register['submitted']) && $register['submitted'] != '') $this->Cell(0,3,'Submitted '.$register['submitted'],0,1,'R');
		if (isset($register['accepted']) && $register['accepted'] != '') $this->Cell(0,3,'Accepted '.$register['accepted'],0,1,'R');
		$this->SetTextColor(0,0,0);
*/
	}

}  // end class PDF
// set the loop
$where = "1 = 1";
if ($registerID) {
	$where .= " AND id = '".$registerID."'";
} else {
	$where .= " AND submitted != ''";
	if (isset($_REQUEST["locationID"]) && $_REQUEST["locationID"] != '') {
		$where .= " AND location_id = '".$_REQUEST["locationID"]."'";
	}
	if (isset($_REQUEST["programID"]) && $_REQUEST["programID"] != '') {
		$where .= " AND program_id = '".$_REQUEST["programID"]."'";
	}
	if (isset($_REQUEST["courseID"]) && $_REQUEST["courseID"] != '') {
		$where .= " AND course_id = '".$_REQUEST["courseID"]."'";
	}
	if (isset($_REQUEST["activityID"]) && $_REQUEST["activityID"] != '') {
		$where .= " AND activity_id = '".$_REQUEST["activityID"]."'";
	}
}
if (!$register_results = $db->select("register", $where)) {
//	die("Error: missing record for ".$registerID.".");
	die("Returned 0 results.");
}

/*
echo "<pre>";
print_r($register_results);
echo "<pre>";
die();
*/
//die("count: ".count($register_results));

// start the loop
$pdf = new PDF();
foreach ($register_results as $register) {

	$guardians = array();

//	if (isset($register['id'])) $registerID = $register['id']; else $registerID = '';
//	if (isset($register['updated'])) $updated = $register['updated']; else $updated = '';

	if ($register['user_id'] == $_SESSION['user_id'] || $_SESSION['user_role'] == "admin" || $_SESSION['user_role'] == "manager" || $_SESSION['user_role'] == "staff") {
	
		$results = $db->select("child", "id = '".$register['child_id']."'");
		$child = $results[0];
		
		$relationships = $db->select("relationship", "child_id = '".$child['id']."'");
		foreach ($relationships as $relationship) {
			$results = $db->select("guardian", "id = '".$relationship['guardian_id']."'");
			$guardian = $results[0];
			$guardian['relationship'] = $relationship['relationship'];		// Mother, Father, etc.
			$guardian['lives_with'] = $relationship['lives_with'];				// yes/no from relationship lives_with
			$guardians[] = $guardian;
		}
	/*
	echo "<pre>";
	print_r($guardians);
	echo "</pre>";
	die();
	*/
		
		$emergency_contacts = $db->select("contact", "child_id = '".$child['id']."' AND type = 'emergency'");
		$authorized_contacts = $db->select("contact", "child_id = '".$child['id']."' AND type = 'authorized'");
		$restricted_contacts = $db->select("contact", "child_id = '".$child['id']."' AND type = 'restricted'");
		
		$register_id = $register['program_id'];
		$program_id = $register['program_id'];
		$location_id = $register['location_id'];
		$course_id = $register['course_id'];
		$activity_id = $register['activity_id'];

		// Instanciation of inherited class
		$pdf->SetTopMargin(10); // 1cm from top
		$pdf->SetAutoPageBreak(1,45); // 4.5cm from bottom
		$title = "Application for Registration";
		$pdf->SetTitle($title);
		if ($program_id) {
			$results = $db->select("program", "id = '".$program_id."'");
			$program = $results[0];
			$subject = strtoupper($program['name']);
			$footer_image = $program['footer_image'];
		} else {
			$subject = '';
			$footer_image = '';
		}
		$pdf->SetSubject($subject);
		if ($location_id) {
			$results = $db->select("location", "id = '".$location_id."'");
			$location = $results[0];
			$author = strtoupper($location['name']);
		} else {
			$author = '';
		}
		$pdf->SetAuthor($author);
		$pdf->AliasNbPages();
		
		$pdf->AddPage();
		
		if ($reportType == "all" || $reportType == "summary") {
			$pdf->SetFont('Arial','',10);
			
			//for($i=1;$i<=40;$i++)
			//    $pdf->Cell(0,10,'Printing line number '.$i,0,1);
			
			$pdf->SetY(40);
			
			if ($child['photo']) {
				if (file_exists($child['photo'])) {
					$pdf->Image($child['photo'],158,38,40);
				} else {
					$pdf->SetX(160);
					$pdf->Rect(156,36,44,44,'D',array('all' => 'style'));
					$pdf->SetX(10);
				}
			} else {
				$pdf->SetX(160);
				$pdf->Rect(156,36,44,44,'D',array('all' => 'style'));
				$pdf->SetX(10);
			}
			
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(0,5.5,'Child Name:',0,0);
			$pdf->SetX(60);
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(0,5.5, $child['first_name'].' '.$child['last_name'],0,1);
			
			if ($register['grade']) {
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(0,5.5,'Grade:',0,0);
				$pdf->SetX(60);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(0,5.5, $CFG['grades'][$register['grade']],0,1);
			}
			
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(0,5.5,'Summit Kids ID:',0,0);
			$pdf->SetX(60);
			$pdf->SetFont('Arial','B',10);
			if ($child['summit_id'] != '') {
				$pdf->MultiCell(80,6, $child['summit_id'],0,1);
			} else {
				$pdf->MultiCell(80,6, "Unassigned",0,1);
			}
			
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(0,5.5,'Gender:',0,0);
			$pdf->SetX(60);
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(0,5.5, $CFG['genders'][$child['gender']],0,1);
			
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(0,5.5,'Birthdate:',0,0);
			$pdf->SetX(60);
			$pdf->SetFont('Arial','B',10);
			$datetime = strtotime($child['birth_date']);
			$mysqldate = date("F j, Y", $datetime);
			$pdf->Cell(0,5.5, $mysqldate,0,1);
			
			$pdf->SetFont('Arial','B',12);
			
			$pdf->Ln(8);
			$pdf->Cell(0,14,'Guardian Info',0,1);
			
			$ynow = ($pdf->GetY())-2;
			$pdf->SetDrawColor(0,0,0);
			$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge
			
			$count = 0;
			$sety = 0;
			$setyh = $pdf->GetY();
			$setx = 10;
			$rules = array();
			foreach ($guardians as $guardian) {
			
				$count++;
			
				$pdf->SetY($setyh);
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','',11);
				$pdf->Cell(0,10,'Guardian '.$CFG['numbers'][$count],0,1);
			
				if (!isset($rules['header'])) {
					$ynow = ($pdf->GetY())-2;
					$pdf->SetDrawColor(123,164,62);
					$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge
					$rules['header'] = true;
				}
			
				if (!$sety) {
					$sety = ($pdf->GetY());
				}
				$pdf->SetY($sety);
				
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(0,5.5,'Name:',0,0);
				$pdf->SetX($setx + 35);
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(0,5.5, stripslashes($guardian['first_name']).' '.stripslashes($guardian['last_name']),0,1);
			
				if ($guardian['relationship']) {
					$pdf->SetX($setx);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(0,5.5,'Relation:',0,0);
					$pdf->SetX($setx + 35);
					$pdf->SetFont('Arial','B',8);
					$pdf->Cell(0,5.5, $guardian['relationship'],0,1);
				}
			
				if ($guardian['lives_with']) {
					$pdf->SetX($setx);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(0,5.5,'Lives With?',0,0);
					$pdf->SetX($setx + 35);
					$pdf->SetFont('Arial','B',8);
					$pdf->Cell(0,5.5, ucfirst($guardian['lives_with']),0,1);
				}
			
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(0,5.5,'Email:',0,0);
				$pdf->SetX($setx + 35);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(0,5.5, $guardian['email'],0,1);
			
				if ($guardian['home_phone']) {
					$pdf->SetX($setx);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(0,5.5,'Home Phone:',0,0);
					$pdf->SetX($setx + 35);
					$pdf->SetFont('Arial','B',8);
					$pdf->Cell(0,5.5, $guardian['home_phone'],0,1);
				}
			
				if ($guardian['cell_phone']) {
					$pdf->SetX($setx);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(0,5.5,'Cell Phone:',0,0);
					$pdf->SetX($setx + 35);
					$pdf->SetFont('Arial','B',8);
					$pdf->Cell(0,5.5, $guardian['cell_phone'],0,1);
				}
			
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(0,5.5,'Daytime Phone:',0,0);
				$pdf->SetX($setx + 35);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(0,5.5, $guardian['daytime_phone'],0,1);
			
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(0,5.5,'Address:',0,0);
				$pdf->SetX($setx + 35);
				$pdf->SetFont('Arial','B',8);
				$pdf->MultiCell(60,6, mb_strimwidth(stripslashes($guardian['address']),0,64,"..."),0,1);
				$pdf->SetX($setx + 35);
				$pdf->SetFont('Arial','B',8);
				$pdf->MultiCell(60,6, stripslashes($guardian['city'])."   ".stripslashes($guardian['province'])."   ".$guardian['postal_code'],0,1);
			
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(0,5.5,'Daytime Address:',0,0);
				$pdf->SetX($setx + 35);
				$pdf->SetFont('Arial','B',8);
				$pdf->MultiCell(60,6, mb_strimwidth(stripslashes($guardian['daytime_address']),0,64,"..."),0,1);
				$pdf->SetX($setx + 35);
				$pdf->SetFont('Arial','B',8);
				$pdf->MultiCell(60,6, stripslashes($guardian['daytime_city'])."   ".stripslashes($guardian['daytime_province'])."   ".$guardian['daytime_postal_code'],0,1);
			
				$setx = $setx + 100;
				$pdf->SetX($setx);
			}
		
			$pdf->SetFont('Arial','B',12);
			
			$pdf->Ln(4);
			$pdf->Cell(0,14,'Emergency Contacts',0,1);
			
			$ynow = ($pdf->GetY())-2;
			$pdf->SetDrawColor(0,0,0);
			$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge
			
			$count = 0;
			$sety = 0;
			$setyh = $pdf->GetY();
			$setyf = 0;
			$setx = 45;
			$rules = array();
			$labels = array();
			foreach ($emergency_contacts as $key => $contact) {
			
				$count++;
			
				$pdf->SetY($setyh);
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','',11);
				$pdf->Cell(0,10,$CFG['numbers'][$count],0,1);
			
				if (!isset($rules['header'])) {
					$ynow = ($pdf->GetY()) - 2;
					$pdf->SetDrawColor(123,164,62);
					$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge
					$rules['header'] = true;
				}
			
				if (!$sety) {
					$sety = ($pdf->GetY());
				}
				$pdf->SetY($sety);
			
				if (!isset($labels['name'])) {
					$pdf->SetX($setx - 35);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(0,5.5,'Name:',0,0);
					$labels['name'] = true;
				}
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(0,5.5, stripslashes($contact['first_name']).' '.stripslashes($contact['last_name']),0,1);
			
				if (!isset($labels['relationship'])) {
					$pdf->SetX($setx - 35);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(0,5.5,'Relation:',0,0);
					$labels['relationship'] = true;
				}
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(0,5.5, stripslashes($contact['relationship']),0,1);
			
				if (!isset($labels['email'])) {
					$pdf->SetX($setx - 35);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(0,5.5,'Email:',0,0);
					$labels['email'] = true;
				}
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(0,5.5, $contact['email'],0,1);
			
				if (!isset($labels['phone_1'])) {
					$pdf->SetX($setx - 35);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(0,5.5,'Phone:',0,0);
					$labels['phone_1'] = true;
				}
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(0,5.5, $contact['phone_1'],0,1);
			
				if (!isset($labels['phone_2'])) {
					$pdf->SetX($setx - 35);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(0,5.5,'Phone:',0,0);
					$labels['phone_2'] = true;
				}
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(0,5.5, $contact['phone_2'],0,1);
			
				if (!isset($labels['phone_3'])) {
					$pdf->SetX($setx - 35);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(0,5.5,'Phone:',0,0);
					$labels['phone_3'] = true;
				}
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(0,5.5, $contact['phone_3'],0,1);
			
				if (!isset($labels['daytime_address'])) {
					$pdf->SetX($setx - 35);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(0,5.5,'Daytime Address:',0,0);
					$labels['daytime_address'] = true;
				}
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','B',8);
				$pdf->MultiCell(50,5, mb_strimwidth(stripslashes($contact['daytime_address']),0,64,"..."),0,1);
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','B',8);
				$pdf->MultiCell(50,6, stripslashes($contact['daytime_city'])."   ".stripslashes($contact['daytime_province'])."   ".$contact['daytime_postal_code'],0,1);
			
				$newy = $pdf->GetY();
				if ($newy > $setyf) {
					$setyf = $newy;
					$pdf->SetY($setyf);
				}
			
				$setx = $setx + 55;
				$pdf->SetX($setx);
			}
		
		} // endif reporttype all or summary
		
		if ($reportType == "all") {
			
			$pdf->AddPage();
			
			$pdf->SetFont('Arial','B',12);
			
			$pdf->Ln(4);
			$pdf->Cell(0,14,'Authorized Contacts',0,1);
			
			$ynow = ($pdf->GetY()) - 2;
			$pdf->SetDrawColor(0,0,0);
			$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge
			
			$count = 0;
			foreach ($authorized_contacts as $key => $contact) {
			
				$count++;
				$details = '';
			
				$details = $contact['first_name'].' '.$contact['last_name'];
				if ($contact['relationship']) {
					$details .= " - ".$contact['relationship'];
				}
				$details .= ": ";
			
				$pdf->SetFont('Arial','B',9);
				$pdf->Write(6,$details);
			
				$comma = ", ";
				$details = '';
			
				if ($contact['email']) {
					if ($details) $details .= $comma;
					$details .= $contact['email'];
				}
			
				if ($contact['phone_1']) {
					if ($details) $details .= $comma;
					$details .= $contact['phone_1'];
				}
			
				if ($contact['phone_2']) {
					if ($details) $details .= $comma;
					$details .= $contact['phone_2'];
				}
			
				if ($contact['phone_3']) {
					if ($details) $details .= $comma;
					$details .= $contact['phone_3'];
				}
			
				$pdf->SetFont('Arial','',8);	
				$pdf->Write(6,stripslashes($details));
			
				$pdf->Ln();
			}
			
			$pdf->SetFont('Arial','B',12);
			
			$pdf->Ln(4);
			$pdf->Cell(0,14,'Restricted Contacts',0,1);
			
			$ynow = ($pdf->GetY()) - 2;
			$pdf->SetDrawColor(0,0,0);
			$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge
			
			$count = 0;
			foreach ($restricted_contacts as $key => $contact) {
			
				$count++;
			
				$pdf->SetFont('Arial','B',9);
				$pdf->Write(6, stripslashes($contact['first_name']).' '.stripslashes($contact['last_name']));
			
				if ($contact['detail']) {
					$pdf->SetFont('Arial','B',9);
					$pdf->Write(6, ": ");
					$pdf->SetFont('Arial','',8);
					$pdf->Write(6, stripslashes($contact['detail']));
				}
			
				$pdf->Ln();
			}
			
			$pdf->SetFont('Arial','B',12);
			
			$pdf->Ln(4);
			$pdf->Cell(0,14,'Medical Info',0,1);
			
			$ynow = ($pdf->GetY()) - 2;
			$pdf->SetDrawColor(0,0,0);
			$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge
			
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(0,5.5,'Alberta Health Care Number:',0,0);
			$pdf->SetX(60);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(0,5.5, $child['alberta_health_care_number'],0,1);
			
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(0,5.5,'Doctor/Clinic Name:',0,0);
			$pdf->SetX(60);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(0,5.5, stripslashes($child['doctor_name']),0,1);
			
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(0,5.5,'Phone:',0,0);
			$pdf->SetX(60);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(0,5.5, $child['doctor_phone'],0,1);
			
			
			$yprojected = ($pdf->GetY()) + 80;
			if ($yprojected > 250) {
				$pdf->AddPage();
			}
			
			$pdf -> Ln(4);
			$pdf->SetFont('Arial','',11);
			$pdf->Cell(0,10,'Medical Conditions',0,1);
			
			$ynow = ($pdf->GetY()) - 2;
			$pdf->SetDrawColor(123,164,62);
			$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge
			
			$setx = 10;
			
			foreach ($CFG['medical_conditions'] as $key => $condition) {
				if ($setx > 110) {
					$pdf -> Ln();
					$setx = 10;
				}
			
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(0,5.5,$condition.':',0,0);
				$pdf->SetX($setx + 60);
				$pdf->SetFont('Arial','B',8);
				if ($child[$key] == 'yes' && isset($child[$key.'_detail'])) {
					$pdf->Cell(0,5.5,ucfirst($child[$key]).' *',0,0);
				} else {
					$pdf->Cell(0,5.5,ucfirst($child[$key]),0,0);
				}
			
				$setx += 100;
			}
			$pdf -> Ln();
			
			$pdf -> Ln(4);
			$pdf->SetFont('Arial','',11);
			$pdf->Cell(0,10,'Medical Conditions Notes *',0,1);
			
			$ynow = ($pdf->GetY()) - 2;
			$pdf->SetDrawColor(123,164,62);
			$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge
			
			$separator = '';
			foreach ($CFG['medical_conditions'] as $key => $condition) {
				if ($child[$key] == 'yes' && isset($child[$key.'_detail'])) {
					$pdf->Write(6,$separator);
					$pdf->SetFont('Arial','B',8);
					$pdf->Write(6,$condition.': ');
					$pdf->SetFont('Arial','',8);
					$pdf->Write(6, stripslashes($child[$key.'_detail']));
					$separator = "; ";
				}
			}
			$pdf -> Ln();
			
			$yprojected = ($pdf->GetY()) + 32;
			if ($yprojected > 250) {
				$pdf->AddPage();
			}
			$pdf -> Ln(4);
			$pdf->SetFont('Arial','',11);
			$pdf->Cell(0,10,'Medical Difficulties',0,1);
			
			$ynow = ($pdf->GetY()) - 2;
			$pdf->SetDrawColor(123,164,62);
			$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge
			
			$setx = 10;
			foreach ($CFG['medical_difficulties'] as $key => $difficulty) {
				if ($setx > 110) {
					$pdf -> Ln();
					$setx = 10;
				}
			
				$pdf->SetX($setx);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(0,5.5,$difficulty.':',0,0);
				$pdf->SetX($setx + 60);
				$pdf->SetFont('Arial','B',8);
				if ($child[$key] == 'yes' && isset($child[$key.'_detail'])) {
					$pdf->Cell(0,5.5,ucfirst($child[$key]).' *',0,0);
				} else {
					$pdf->Cell(0,5.5,ucfirst($child[$key]),0,0);
				}
				$setx += 100;
			}
			$pdf -> Ln();
			
			$pdf -> Ln(4);
			$pdf->SetFont('Arial','',11);
			$pdf->Cell(0,10,'Medical Difficulties Notes *',0,1);
			
			$ynow = ($pdf->GetY()) - 2;
			$pdf->SetDrawColor(123,164,62);
			$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge
			
			$separator = '';
			foreach ($CFG['medical_difficulties'] as $key => $difficulty) {
				if ($child[$key] == 'yes' && isset($child[$key.'_detail'])) {
					$pdf->Write(6,$separator);
					$pdf->SetFont('Arial','B',8);
					$pdf->Write(6,$difficulty.': ');
					$pdf->SetFont('Arial','',8);
					$pdf->Write(6, stripslashes($child[$key.'_detail']));
					$separator = "; ";
				}
			}
			$pdf -> Ln();
			/*
				if ($child[$key] == 'yes' && isset($child[$key.'_detail'])) {
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(0,5.5,$difficulty.' Detail:',0,0);
					$pdf->SetX(60);
					$pdf->SetFont('Arial','',8);
					$pdf->MultiCell(140,7, $child[$key.'_detail'],0,1);
				}
			*/
			$pdf -> Ln(4);
			$pdf->SetFont('Arial','',11);
			$pdf->Cell(0,10,'Special Needs and Personality',0,1);
			
			$ynow = ($pdf->GetY()) - 2;
			$pdf->SetDrawColor(123,164,62);
			$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge
			
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(0,5.5,'Favourite activities:',0,0);
			$pdf->SetX(60);
			$pdf->SetFont('Arial','',8);
			$pdf->MultiCell(140,7, stripslashes($child['behavior_favourite_activities']),0,1);
			
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(0,5.5,'Fears or challenges:',0,0);
			$pdf->SetX(60);
			$pdf->SetFont('Arial','',8);
			$pdf->MultiCell(140,7, stripslashes($child['behavior_fears']),0,1);
			
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(0,5.5,'Behavioral challenges:',0,0);
			$pdf->SetX(60);
			$pdf->SetFont('Arial','',8);
			$pdf->MultiCell(140,7, stripslashes($child['behavior_challenges']),0,1);
			
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(0,5.5,'Anything else:',0,0);
			$pdf->SetX(60);
			$pdf->SetFont('Arial','',8);
			$pdf->MultiCell(140,7, stripslashes($child['behavior_other']),0,1);
			
			$pdf->Ln(8);
			
			//$pdf->SetFont('Arial','B',11);
			//$pdf->Cell(0,10,'Disclaimer',0,1);
			
			//$ynow = ($pdf->GetY()) - 2;
			//$pdf->SetDrawColor(123,164,62);
			//$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge
			
			
			//$pdf->Ln(4);
			$pdf->SetFont('Arial','B',12);
			$pdf->Cell(0,14,'Disclaimer',0,1);
			
			$ynow = ($pdf->GetY()) - 2;
			$pdf->SetDrawColor(0,0,0);
			$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge
			
			$disclaimer = "All personal and medical information collected by Summit Kids in the Registration package becomes part of the child's record.  It is considered to be confidential and is protected by our Confidentiality Policy.  Email addresses provided by parents will be added to our confidential email list and will be used to enhance communication with parents in our program.";
			
			$pdf->SetFont('Arial','',7.75);
			$pdf->MultiCell(190,6, $disclaimer,0,1);
			
			$pdf->Ln(24);
			$ynow = ($pdf->GetY())-2;
			$pdf->SetDrawColor(0,0,0);
			$pdf->Line(10,$ynow,130,$ynow); // 10mm from each edge
			$pdf->Line(140,$ynow,200,$ynow); // 10mm from each edge
			
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(0,5.5,"Guardian's signature",0,0);
			$pdf->SetX(140);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(0,5.5,"Date",0,1);


			// Course summary
			if ($course_id && $activity_id) {

				setlocale(LC_MONETARY, 'en_US');
				$pdf->AddPage();

				$results = $db->select("course", "id = '".$course_id."'");
				$course = $results[0];

			//	$pdf->Ln(4);
				$pdf->SetFont('Arial','B',12);
				$pdf->Cell(0,14,'Course Information',0,1);
				
				$ynow = ($pdf->GetY())-2;
				$pdf->SetDrawColor(0,0,0);
				$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge
				
				$setx = 10;
				
				$pdf -> Ln(4);
				$pdf->SetFont('Arial','',11);
				$pdf->Cell(0,10,$program['name'],0,1);
				
				$ynow = ($pdf->GetY()) - 2;
				$pdf->SetDrawColor(123,164,62);
				$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge

				if ($program['description']) {
					$pdf->SetFont('Arial','',6);
					$pdf->MultiCell(190,3, stripslashes($program['description']),0,1);
				}

				if ($program['web_url']) {
					$pdf -> Ln(4);
					$pdf->SetFont('Arial','B',7);
					$pdf->Cell(0,3,'For More Information:',0,0);
					$pdf->SetX($setx + 35);
					$pdf->SetFont('Arial','',6);
					$pdf->MultiCell(140,3, $program['web_url'],0,1);
				}

				$pdf -> Ln(4);
				$pdf->SetFont('Arial','',11);
				$pdf->Cell(0,10,'Location',0,1);
				
				$ynow = ($pdf->GetY()) - 2;
				$pdf->SetDrawColor(123,164,62);
				$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge

				$sety = 0;
				$setyh = $pdf->GetY();
				$setx = 10;

				$pdf->SetFont('Arial','',7);
				$pdf->Cell(0,6,'Name:',0,0);
				$pdf->SetX($setx + 35);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(0,6, stripslashes($location['name']),0,1);

				$pdf->SetX($setx);
				$pdf->SetFont('Arial','',7);
				$pdf->Cell(0,4,'Address:',0,0);
				$pdf->SetX($setx + 35);
				$pdf->SetFont('Arial','',8);
				$pdf->MultiCell((125 - $setx - 35),4, stripslashes($location['address']),0,1);
				$pdf->SetX($setx + 35);
				$pdf->SetFont('Arial','',8);
				$pdf->MultiCell((125 - $setx - 35),4, stripslashes($location['city'])."   ".stripslashes($location['province'])."   ".$location['postal_code'],0,1);

				if ($location['phone']) {
					$pdf->SetFont('Arial','',7);
					$pdf->Cell(0,5,'Phone:',0,0);
					$pdf->SetX($setx + 35);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(0,5, $location['phone'],0,1);
				}

				if ($location['fax']) {
					$pdf->SetFont('Arial','',7);
					$pdf->Cell(0,5,'Fax:',0,0);
					$pdf->SetX($setx + 35);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(0,5, $location['fax'],0,1);
				}

				if ($location['email']) {
					$pdf->SetFont('Arial','',7);
					$pdf->Cell(0,5,'Email:',0,0);
					$pdf->SetX($setx + 35);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(0,5, $location['email'],0,1);
				}

				if ($location['web_url']) {
					$pdf->SetFont('Arial','',7);
					$pdf->Cell(0,5,'Website:',0,0);
					$pdf->SetX($setx + 35);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(0,5, $location['web_url'],0,1);
				}

				$setyf = ($pdf->GetY());
				$pdf->SetY($setyh + .5);
				$pdf->SetX($setx + 115);

				$pdf->SetFont('Arial','',7);
				$pdf->MultiCell(75,5, stripslashes($location['description']),0,'J');

				$pdf->SetX($setx);
				$sety = ($pdf->GetY());
				if ($sety > $setyf) {
					$setyf = $sety;
				}
				$pdf->SetY($setyf);

				$pdf -> Ln(4);
				$pdf->SetFont('Arial','',11);
				$pdf->Cell(0,10,stripslashes($course['name']),0,1);

				$ynow = ($pdf->GetY()) - 2;
				$pdf->SetDrawColor(123,164,62);
				$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge

				$pdf->SetFont('Arial','',6);
				$pdf->MultiCell(190,3, stripslashes($course['description']),0,1);
				$pdf -> Ln(4);

				$date = new DateTime(strftime('%Y-%m-%d %H:%M:%S', strtotime($course['start_date'])));
				$datetime = $date->format('m/d/Y g:i a');
				$pdf->SetFont('Arial','',7);
				$pdf->Cell(0,5,'Course Begins:',0,0);
				$pdf->SetX($setx + 35);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(0,5, $datetime,0,1);

				$date = new DateTime(strftime('%Y-%m-%d %H:%M:%S', strtotime($course['end_date'])));
				$datetime = $date->format('m/d/Y g:i a');
				$pdf->SetFont('Arial','',7);
				$pdf->Cell(0,5,'Course Ends:',0,0);
				$pdf->SetX($setx + 35);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(0,5, $datetime,0,1);

				$pdf -> Ln(4);
				$pdf->SetFont('Arial','B',7);
				$pdf->Cell(0,3,'Special Instructions:',0,0);
				$pdf->SetX($setx + 35);
				$pdf->SetFont('Arial','',6);
				$pdf->MultiCell(140,3, stripslashes($course['special_instructions']),0,1);

				$pdf -> Ln(4);

				$pdf->SetX($setx);
				$pdf->SetFont('Arial','B',6);
				$pdf->Write(6, "Activity");
			
				$pdf->SetX($setx + 50);
				$pdf->SetFont('Arial','B',6);
				$pdf->Write(6, "Additional Instructions");
			
				$pdf->SetX($setx + 175);
				$pdf->SetFont('Arial','B',6);
				$pdf->Write(6, "Cost");

				$pdf->Ln();

				$ynow = ($pdf->GetY());
				$pdf->SetDrawColor(123,164,62);
				$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge

				$results = $db->select("course_activity", "course_id = '".$course_id."' AND activity_id = '".$register['activity_id']."'");
				$course_activity = $results[0];
				$results = $db->select("activity", "id = '".$register['activity_id']."'");
				$activity = $results[0];

				$pdf -> Ln(1);
				$pdf->SetFont('Arial','',6);

				$setyh = $pdf->GetY();
				$pdf->SetY($setyh + 1);
				$pdf->SetX($setx);
				$pdf->MultiCell(45,3, stripslashes($activity['name']),0,1);
				$setyf = $pdf->GetY();

				$pdf->SetY($setyh + 1);
				$pdf->SetX($setx + 50);
				$pdf->MultiCell(120,3, stripslashes($course_activity['special_instructions']),0,1);
				$sety = $pdf->GetY();

				if ($sety > $setyf) {
					$setyf = $sety;
				}

				$pdf->SetY($setyh);
				$pdf->SetX($setx + 175);
				if ($register['apply_discounts'] == "yes" && $course_activity['discount_cost'] > 0) {
					$cost = $course_activity['discount_cost'];
				} else {
					$cost = $course_activity['cost'];
				}
				$pdf->Cell(0,5, money_format('%(#6n', $cost),0,1,'R');
				$sety = $pdf->GetY();

				if ($sety > $setyf) {
					$setyf = $sety;
				}

				$pdf->SetY($setyf);
				$pdf->SetX($setx);
				
				$pdf->Ln(8);
				$ynow = ($pdf->GetY()) - 2;
				$pdf->SetDrawColor(123,164,62);
				$pdf->Line(10,$ynow,210 - 10,$ynow); // 10mm from each edge
			
				$pdf->SetFont('Arial','B',6);
				$pdf->Write(6, "Total Cost");
			
				$pdf->SetX(179);
				$pdf->SetFont('Arial','B',6);
				$pdf->Cell(0,5.5, money_format('%(#10n', $cost),1,0,'R');

			
				$pdf->Ln(8);
				$pdf->SetTextColor(123,164,62);
				$pdf->SetFont('Arial','',6);
				$pdf->Cell(0,5.5, "All terms, costs, and discounts are subject to change at time of submission.",0,0,'C');
				$pdf->SetTextColor(0,0,0);
			} // end course summary
		} // endif reporttype all

	} else {
		// if not my child or I am not an admin, exit this script
		header("Location: /");
		exit();
	}
}
//$hashbase = md5($register['id'].$child['first_name'].$child['last_name']);
//$filebase = "files/tmp/".$hashbase;
//$pdf->Output($filebase.".pdf",'F');
$pdf->Output();
?>
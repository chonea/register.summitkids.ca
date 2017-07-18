<?php

$CFG = array();

$CFG['user_roles'] = array(
	"user" => "User",
	"staff" => "Staff",
	"manager" => "Manager",
	"admin" => "Administrator"
);

$CFG['programs'] = array(
	"summit-kids-k" => "Summit Kids Kindergarten",
	"summit-kids-1-6" => "Summit Kids Grades 1-6",
	"summit-steps" => "Summit Steps",
	"summit-summer" => "Summit Summer",
	"summit-spring" => "Summit Spring",
	"summit-u" => "Summit U"
);

$CFG['programs_abbreviated'] = array(
	"summit-kids-k" => "SK K",
	"summit-kids-1-6" => "SK 1-6",
	"summit-steps" => "Steps",
	"summit-summer" => "Summer",
	"summit-spring" => "Spring",
	"summit-u" => "U"
);

$CFG['locations'] = array(
	"doyle" => "Doyle Campus",
	"nellie" => "Nellie Campus",
	"north-haven" => "North Haven Campus",
	"pbp" => "PBP Campus",
	"rideau" => "Rideau Campus",
	"st-cecilia" => "St. Cecilia Campus",
	"st-gerard" => "St. Gerard Campus",
	"st-vincent" => "St. Vincent Campus",
	"terrace-road" => "Terrace Road Campus",
	"ues" => "UES Campus",
	"wom" => "WOM Campus"
);

$CFG['program_by_location'] = array (
	"doyle" => array("summit-kids-k", "summit-kids-1-6"),
	"nellie" => array("summit-kids-k", "summit-kids-1-6"),
	"north-haven" => array("summit-kids-k", "summit-kids-1-6"),
	"pbp" => array("summit-kids-k", "summit-kids-1-6"),
	"rideau" => array("summit-kids-k", "summit-kids-1-6"),
	"st-cecilia" => array("summit-kids-k", "summit-kids-1-6"),
	"st-gerard" => array("summit-kids-k", "summit-kids-1-6"),
	"st-vincent" => array("summit-kids-k", "summit-kids-1-6"),
	"terrace-road" => array("summit-kids-k", "summit-kids-1-6"),
	"ues" => array("summit-kids-k", "summit-kids-1-6"),
	"wom" => array("summit-kids-k", "summit-kids-1-6"),
	"special" => array("summit-steps","summit-summer","summit-spring","summit-u")
);

$CFG['grades'] = array(
	"kindergarten" => "Kindergarten", 
	"grade-1" => "Grade 1", 
	"grade-2" => "Grade 2", 
	"grade-3" => "Grade 3", 
	"grade-4" => "Grade 4", 
	"grade-5" => "Grade 5", 
	"grade-6" => "Grade 6"
);

$CFG['grades_abbreviated'] = array(
	"kindergarten" => "K", 
	"grade-1" => "1", 
	"grade-2" => "2", 
	"grade-3" => "3", 
	"grade-4" => "4", 
	"grade-5" => "5", 
	"grade-6" => "6"
);

$CFG['types'] = array(
	"part-time-am" => "Part-time (mornings)",
	"part-time-pm" => "Part-time (afternoons)",
	"full-time" => "Full-time (mornings & afternoons)",
	"kg-1" => "KG - 1 Component (school day only)",
	"kg-2" => "KG - 2 Components (school day + morning or afternoon)",
	"kg-3" => "KG - 3 Components (school day + morning + afternoon)",
	"sessions" => "Sessions",
	"tu-th-am" => "Tuesday/Thursday AM (8:30AM - 11:30AM) - $220/month",
	"tu-th-pm" => "Tuesday/Thursday PM (12:30PM - 3:30PM) - $220/month",
	"m-w-f-am" => "Monday/Wednesday/Friday AM (8:30AM - 11:30AM) - $262/month",
	"m-w-f-pm" => "Monday/Wednesday/Friday PM (12:30PM - 3:30PM) - $262/month",
	"week-am" => "5 Days Per Week AM (8:30AM - 11:30AM) - $400/month",
	"week-pm" => "5 Days Per Week PM (12:30PM - 3:30PM) - $400/month"
);

$CFG['sessions'] = array(
	"summer-session-1" => "Week One: June 30 - July 4 (closed July 1)",
	"summer-session-2" => "Week Two: July 7 - July 11",
	"summer-session-3" => "Week Three: July 14 - July 18",
	"summer-session-4" => "Week Four: July 21 - July 25",
	"summer-session-5" => "Week Five: July 28 - Aug 1",
	"summer-session-6" => "Week Six: Aug 5 - Aug 8 (closed Aug 4)",
	"summer-session-7" => "Week Seven: Aug 11 - Aug 15",
	"summer-session-8" => "Week Eight: Aug 18 - Aug 22",
	"summer-session-9" => "Week Nine: Aug 25 - Aug 29",
	"summer-extended-session-1" => "Weeks Two/Three: July 7 - July 18",
	"summer-extended-session-2" => "Weeks Four/Five: July 21 - Aug 1"
);

$CFG['activities'] = array(
	"summit-studio" => "Summit Studio",
	"summit-sports" => "Summit Sports",
	"summit-science" => "Summit Science",
	"summit-sing-strum-stomp" => "Summit Sing, Strum, & Stomp",
	"summit-citizens" => "Summit Citizens"
);

$CFG['genders'] = array(
	"male" => "Boy",
	"female" => "Girl"
);

$CFG['numbers'] = array(
	0 => "Zero",
	1 => "One",
	2 => "Two",
	3 => "Three",
	4 => "Four",
	5 => "Five",
	6 => "Six",
	7 => "Seven",
	8 => "Eight",
	9 => "Nine"
);

$CFG['medical_conditions'] = array(
	"condition_immunizations_up_to_date" => "Immunizations up-to-date",
	"condition_ear_infections" => "Ear infections",
	"condition_no_immunization" => "Chose NOT to immunize",
	"condition_tubes_in_ears" => "Has tubes in ears",
	"condition_allergy_life_threatening" => "Allergy - Life threatening",
	"condition_headaches" => "Headaches",
	"condition_allergy_food" => "Allergy - Food",
	"condition_sore_throats" => "Sore throats",
	"condition_allergy_environmental" => "Allergy - Environmental",
	"condition_colds" => "Colds",
	"condition_diabetes" => "Diabetes",
	"condition_uti" => "UTI",
	"condition_asthma_with_puffer" => "Asthma with puffer",
	"condition_stomach_upset" => "Stomach upset",
	"condition_add_adhd" => "ADD/ADHD",
	"condition_epilepsy" => "Epilepsy",
	"condition_heart_condition" => "Heart condition",
	"condition_autism_spectrum" => "Autism spectrum",
	"condition_medications" => "Medications"
);

$CFG['medical_difficulties'] = array(
	"difficulty_hearing" => "Hearing",
	"difficulty_speech" => "Speech",
	"difficulty_eating" => "Eating",
	"difficulty_vision" => "Vision",
	"difficulty_bowels" => "Bowels",
	"difficulty_urinary" => "Urinary accidents",
	"difficulty_social" => "Making friends",
	"difficulty_other" => "Other"
);

$CFG['application_statuses'] = array(
	"accepted" => "Accepted",
	"submitted" => "Submitted",
	"incomplete" => "Incomplete",
	"deleted" => "Deleted"
);
?>
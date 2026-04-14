<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/vendor/autoload.php';

$con    = mysqli_connect("localhost","root","","test");
$id     = (int)$_GET['id'];
$result = mysqli_query($con, "SELECT * FROM tgbiodata WHERE id='$id'");
$row    = mysqli_fetch_assoc($result);

if(!$row){
    die("Student not found.");
}

// Build photo tag
if(!empty($row['PHOTO']) && !empty($row['PHOTO_MIME'])){
    $photo_html = '<img src="data:'.htmlspecialchars($row['PHOTO_MIME']).';base64,'.$row['PHOTO'].'"
        style="width:110px;height:140px;object-fit:cover;border:1px solid #ccc;border-radius:6px;">';
} else {
    $photo_html = '<div style="width:110px;height:140px;background:#eee;border:1px solid #ccc;
        border-radius:6px;text-align:center;padding-top:55px;color:#999;font-size:12px;">No Photo</div>';
}

// Build SGPA section based on year
$sgpa_html = '';

if(!empty($row['SEM1_SGPA'])){
    $sgpa_html .= '
    <tr><td colspan="2" class="sec-head" style="background:#1a7f37;color:white;">First Year — Semester Results</td></tr>
    <tr><td class="label">Sem 1 SGPA</td><td>'.htmlspecialchars($row['SEM1_SGPA']).'</td></tr>
    <tr><td class="label">Sem 2 SGPA</td><td>'.htmlspecialchars($row['SEM2_SGPA']).'</td></tr>
    <tr><td class="label">First Year CGPA</td><td class="cgpa-val">'.htmlspecialchars($row['FIRST_YEAR_CGPA']).'</td></tr>';
}
if(!empty($row['SEM3_SGPA'])){
    $sgpa_html .= '
    <tr><td colspan="2" class="sec-head" style="background:#0969da;color:white;">Second Year — Semester Results</td></tr>
    <tr><td class="label">Sem 3 SGPA</td><td>'.htmlspecialchars($row['SEM3_SGPA']).'</td></tr>
    <tr><td class="label">Sem 4 SGPA</td><td>'.htmlspecialchars($row['SEM4_SGPA']).'</td></tr>
    <tr><td class="label">Second Year CGPA</td><td class="cgpa-val">'.htmlspecialchars($row['SECOND_YEAR_CGPA']).'</td></tr>';
}
if(!empty($row['SEM5_SGPA'])){
    $sgpa_html .= '
    <tr><td colspan="2" class="sec-head" style="background:#8250df;color:white;">Third Year — Semester Results</td></tr>
    <tr><td class="label">Sem 5 SGPA</td><td>'.htmlspecialchars($row['SEM5_SGPA']).'</td></tr>
    <tr><td class="label">Sem 6 SGPA</td><td>'.htmlspecialchars($row['SEM6_SGPA']).'</td></tr>
    <tr><td class="label">Third Year CGPA</td><td class="cgpa-val">'.htmlspecialchars($row['THIRD_YEAR_CGPA']).'</td></tr>';
}
if(!empty($row['SEM7_SGPA'])){
    $sgpa_html .= '
    <tr><td colspan="2" class="sec-head" style="background:#cf222e;color:white;">Final Year — Semester Results</td></tr>
    <tr><td class="label">Sem 7 SGPA</td><td>'.htmlspecialchars($row['SEM7_SGPA']).'</td></tr>
    <tr><td class="label">Sem 8 SGPA</td><td>'.htmlspecialchars($row['SEM8_SGPA']).'</td></tr>
    <tr><td class="label">Final Year CGPA</td><td class="cgpa-val">'.htmlspecialchars($row['FINAL_YEAR_CGPA']).'</td></tr>';
}

$html = '
<html>
<head>
<style>
    body { font-family: Arial, sans-serif; font-size:13px; color:#222; }

    /* HEADER */
    .header-table { width:100%; border:none; margin-bottom:15px; border-collapse:collapse; }
    .header-table td { border:none; padding:0; vertical-align:top; }
    .college-name { font-size:17px; font-weight:bold; color:#1a5c2a; margin:0 0 5px 0; }
    .college-sub  { font-size:12px; color:#555; margin:0; }
    .header-line  { border-bottom:3px solid #5b3ea4; margin-bottom:15px; }

    /* DATA TABLE */
    table { width:100%; border-collapse:collapse; margin-top:10px; }
    th {
        background:#4CAF50; color:white;
        padding:8px 10px; text-align:left;
        font-size:13px; font-weight:bold;
    }
    td { border:1px solid #ccc; padding:8px 10px; }
    .label    { background:#f0f0f0; font-weight:bold; width:42%; }
    .cgpa-val { font-weight:bold; color:#c0392b; }
    .sec-head { font-weight:bold; }
</style>
</head>
<body>

<!-- HEADER using real HTML table for mPDF compatibility -->
<table class="header-table">
    <tr>
        <!-- LEFT: College name -->
        <td style="width:75%; padding-right:10px;">
            <p class="college-name">G H Raisoni College of Engineering &amp; Management</p>
            <p class="college-sub">Student Biodata &mdash; '.htmlspecialchars($row['YEAR']).'</p>
        </td>

        <!-- RIGHT: Passport photo -->
        <td style="width:25%; text-align:right; vertical-align:top;">
            '.$photo_html.'
        </td>
    </tr>
</table>
<div class="header-line"></div>

<!-- PERSONAL -->
<table>
<tr><th colspan="2">Personal Details</th></tr>
<tr><td class="label">Name</td>              <td>'.htmlspecialchars($row['NAME']).'</td></tr>
<tr><td class="label">Section &amp; Roll</td><td>'.htmlspecialchars($row['SECTION']).'</td></tr>
<tr><td class="label">Branch</td>            <td>'.htmlspecialchars($row['BRANCH']).'</td></tr>
<tr><td class="label">Year</td>              <td>'.htmlspecialchars($row['YEAR']).'</td></tr>
<tr><td class="label">Date of Birth</td>     <td>'.htmlspecialchars($row['DOB']).'</td></tr>
<tr><td class="label">Blood Group</td>       <td>'.htmlspecialchars($row['BLOOD_GROUP']).'</td></tr>
<tr><td class="label">Father Name</td>       <td>'.htmlspecialchars($row['FATHER_NAME']).'</td></tr>
<tr><td class="label">Father Occupation</td> <td>'.htmlspecialchars($row['FATHER_OCCUPATION']).'</td></tr>

<!-- CONTACT -->
<tr><th colspan="2">Contact Details</th></tr>
<tr><td class="label">Student Mobile</td>        <td>'.htmlspecialchars($row['PHONE_NO']).'</td></tr>
<tr><td class="label">Guardian Number</td>       <td>'.htmlspecialchars($row['GUARDIAN_NO']).'</td></tr>
<tr><td class="label">Email</td>                 <td>'.htmlspecialchars($row['EMAIL']).'</td></tr>
<tr><td class="label">Hostel</td>                <td>'.htmlspecialchars($row['HOSTEL_NAME']).'</td></tr>
<tr><td class="label">Permanent Address</td>     <td>'.htmlspecialchars($row['ADDRESS']).'</td></tr>
<tr><td class="label">Residential Address</td>   <td>'.htmlspecialchars($row['RESIDENTIAL_ADDRESS']).'</td></tr>

<!-- QUALIFICATION -->
<tr><th colspan="2">Qualification Details</th></tr>
<tr><td class="label">10th Marks</td>  <td>'.htmlspecialchars($row['TENTH_MARKS']).'</td></tr>
<tr><td class="label">12th Marks</td>  <td>'.htmlspecialchars($row['TEVELTH_MARKS']).'</td></tr>
<tr><td class="label">Achievement</td> <td>'.htmlspecialchars($row['ACHIEVEMENT']).'</td></tr>

'.$sgpa_html.'

</table>
</body>
</html>';

// Generate PDF
$mpdf = new \Mpdf\Mpdf([
    'margin_left'   => 10,
    'margin_right'  => 10,
    'margin_top'    => 10,
    'margin_bottom' => 10,
    'format'        => 'A4',
]);

$mpdf->SetTitle('Student Biodata - ' . $row['NAME']);
$mpdf->WriteHTML($html);

$filename = 'biodata_' . preg_replace('/[^a-zA-Z0-9]/', '_', $row['NAME']) . '.pdf';
$mpdf->Output($filename, 'D'); // D = force download
exit();
?>
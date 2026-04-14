<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

$con = mysqli_connect("localhost","root","","test");

$allowed_years = ['First Year','Second Year','Third Year','Final Year'];
$year = isset($_GET['year']) ? $_GET['year'] : 'First Year';
if(!in_array($year, $allowed_years)) $year = 'First Year';

$safe_year = mysqli_real_escape_string($con, $year);
$result = mysqli_query($con, "SELECT * FROM tgbiodata WHERE YEAR='$safe_year' ORDER BY SECTION ASC");
$filename  = str_replace(' ','_',$year) . "_students.xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$filename");

echo "Name\tSection\tBranch\tYear\tDOB\tBlood Group\tFather Name\tFather Occupation\t" .
     "Email\tPhone\tGuardian No\tHostel\tCorrespondence Address\tResidential Address\t" .
     "10th Marks\t12th Marks\tAchievement\t" .
     "Sem1 SGPA\tSem2 SGPA\tFirst Year CGPA\t" .
     "Sem3 SGPA\tSem4 SGPA\tSecond Year CGPA\t" .
     "Sem5 SGPA\tSem6 SGPA\tThird Year CGPA\t" .
     "Sem7 SGPA\tSem8 SGPA\tFinal Year CGPA\n";

while($row = mysqli_fetch_assoc($result)){
    echo $row['NAME']                . "\t" .
         $row['SECTION']             . "\t" .
         $row['BRANCH']              . "\t" .
         $row['YEAR']                . "\t" .
         $row['DOB']                 . "\t" .
         $row['BLOOD_GROUP']         . "\t" .
         $row['FATHER_NAME']         . "\t" .
         $row['FATHER_OCCUPATION']   . "\t" .
         $row['EMAIL']               . "\t" .
         $row['PHONE_NO']            . "\t" .
         $row['GUARDIAN_NO']         . "\t" .
         $row['HOSTEL_NAME']         . "\t" .
         $row['ADDRESS']             . "\t" .
         $row['RESIDENTIAL_ADDRESS'] . "\t" .
         $row['TENTH_MARKS']         . "\t" .
         $row['TEVELTH_MARKS']       . "\t" .
         $row['ACHIEVEMENT']         . "\t" .
         $row['SEM1_SGPA']           . "\t" .
         $row['SEM2_SGPA']           . "\t" .
         $row['FIRST_YEAR_CGPA']     . "\t" .
         $row['SEM3_SGPA']           . "\t" .
         $row['SEM4_SGPA']           . "\t" .
         $row['SECOND_YEAR_CGPA']    . "\t" .
         $row['SEM5_SGPA']           . "\t" .
         $row['SEM6_SGPA']           . "\t" .
         $row['THIRD_YEAR_CGPA']     . "\t" .
         $row['SEM7_SGPA']           . "\t" .
         $row['SEM8_SGPA']           . "\t" .
         $row['FINAL_YEAR_CGPA']     . "\n";
}
?>
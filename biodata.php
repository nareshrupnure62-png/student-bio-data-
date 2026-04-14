<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'student'){
    header("Location: login.php");
    exit();
}

$insert = false;
$error  = "";
$con    = mysqli_connect("localhost","root","","test");
if(!$con) die("Connection Failed: " . mysqli_connect_error());

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $name         = mysqli_real_escape_string($con, $_POST['NAME']);
    $section      = mysqli_real_escape_string($con, $_POST['SECTION']);
    $branch       = mysqli_real_escape_string($con, $_POST['BRANCH']);
    $year         = mysqli_real_escape_string($con, $_POST['YEAR']);
    $dob          = mysqli_real_escape_string($con, $_POST['DOB']);
    $father       = mysqli_real_escape_string($con, $_POST['FATHER_NAME']);
    $email        = mysqli_real_escape_string($con, $_POST['EMAIL']);
    $address      = mysqli_real_escape_string($con, $_POST['ADDRESS']);
    $res_address  = mysqli_real_escape_string($con, $_POST['RESIDENTIAL_ADDRESS']);
    $phone        = mysqli_real_escape_string($con, $_POST['PHONE_NO']);
    $guardian_no  = mysqli_real_escape_string($con, $_POST['GUARDIAN_NO']);
    $occupation   = mysqli_real_escape_string($con, $_POST['FATHER_OCCUPATION']);
    $hostel       = mysqli_real_escape_string($con, $_POST['HOSTEL_NAME']);
    $blood        = mysqli_real_escape_string($con, $_POST['BLOOD_GROUP']);
    $tenthmarks   = mysqli_real_escape_string($con, $_POST['TENTH_MARKS']);
    $tevelthmarks = mysqli_real_escape_string($con, $_POST['TEVELTH_MARKS']);
    $achievement  = mysqli_real_escape_string($con, $_POST['ACHIEVEMENT']);

    $sem1_sgpa   = mysqli_real_escape_string($con, $_POST['SEM1_SGPA']        ?? '');
    $sem2_sgpa   = mysqli_real_escape_string($con, $_POST['SEM2_SGPA']        ?? '');
    $first_cgpa  = mysqli_real_escape_string($con, $_POST['FIRST_YEAR_CGPA']  ?? '');
    $sem3_sgpa   = mysqli_real_escape_string($con, $_POST['SEM3_SGPA']        ?? '');
    $sem4_sgpa   = mysqli_real_escape_string($con, $_POST['SEM4_SGPA']        ?? '');
    $second_cgpa = mysqli_real_escape_string($con, $_POST['SECOND_YEAR_CGPA'] ?? '');
    $sem5_sgpa   = mysqli_real_escape_string($con, $_POST['SEM5_SGPA']        ?? '');
    $sem6_sgpa   = mysqli_real_escape_string($con, $_POST['SEM6_SGPA']        ?? '');
    $third_cgpa  = mysqli_real_escape_string($con, $_POST['THIRD_YEAR_CGPA']  ?? '');
    $sem7_sgpa   = mysqli_real_escape_string($con, $_POST['SEM7_SGPA']        ?? '');
    $sem8_sgpa   = mysqli_real_escape_string($con, $_POST['SEM8_SGPA']        ?? '');
    $final_cgpa  = mysqli_real_escape_string($con, $_POST['FINAL_YEAR_CGPA']  ?? '');

    $allowed_years = ['First Year','Second Year','Third Year','Final Year'];
    if(!in_array($year, $allowed_years)){
        $error = "Please select a valid year.";
    }

    $photo_data = "";
    $photo_mime = "";
    if($error === ""){
        if(isset($_FILES['PHOTO']) && $_FILES['PHOTO']['error'] === UPLOAD_ERR_OK){
            $allowed = ['image/jpeg','image/jpg','image/png','image/gif'];
            $mime    = mime_content_type($_FILES['PHOTO']['tmp_name']);
            if(in_array($mime, $allowed)){
                $photo_data = base64_encode(file_get_contents($_FILES['PHOTO']['tmp_name']));
                $photo_mime = mysqli_real_escape_string($con, $mime);
            } else {
                $error = "Only JPG, PNG, or GIF allowed for photo.";
            }
        } else {
            $error = "Please upload a passport size photo.";
        }
    }

    if($error === ""){
        $sql = "INSERT INTO tgbiodata
            (NAME, SECTION, BRANCH, YEAR, DOB, FATHER_NAME, EMAIL, ADDRESS,
             RESIDENTIAL_ADDRESS, PHONE_NO, GUARDIAN_NO, FATHER_OCCUPATION,
             HOSTEL_NAME, BLOOD_GROUP, TENTH_MARKS, TEVELTH_MARKS, ACHIEVEMENT,
             PHOTO, PHOTO_MIME,
             SEM1_SGPA, SEM2_SGPA, FIRST_YEAR_CGPA,
             SEM3_SGPA, SEM4_SGPA, SECOND_YEAR_CGPA,
             SEM5_SGPA, SEM6_SGPA, THIRD_YEAR_CGPA,
             SEM7_SGPA, SEM8_SGPA, FINAL_YEAR_CGPA)
            VALUES
            ('$name','$section','$branch','$year','$dob','$father','$email','$address',
             '$res_address','$phone','$guardian_no','$occupation',
             '$hostel','$blood','$tenthmarks','$tevelthmarks','$achievement',
             '$photo_data','$photo_mime',
             '$sem1_sgpa','$sem2_sgpa','$first_cgpa',
             '$sem3_sgpa','$sem4_sgpa','$second_cgpa',
             '$sem5_sgpa','$sem6_sgpa','$third_cgpa',
             '$sem7_sgpa','$sem8_sgpa','$final_cgpa')";

        if(mysqli_query($con,$sql)) $insert = true;
        else $error = mysqli_error($con);
    }
}
mysqli_close($con);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Student Bio-Data</title>
<style>
body { margin:0; font-family:'Segoe UI',sans-serif; }
.gh  { width:100%; position:absolute; z-index:-1; opacity:0.4; }

.container {
    width:850px; margin:40px auto; background:#fff;
    padding:30px; border-radius:10px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
}
h2 { text-align:center; margin-bottom:5px; }
h3 { text-align:center; color:#555; margin-top:0; }

.photo-section {
    text-align:center; margin-bottom:25px;
    padding-bottom:20px; border-bottom:2px dashed #ddd;
}
.photo-section label { display:block; font-size:15px; font-weight:600; margin-bottom:10px; color:#333; }
.photo-box {
    display:inline-block; width:130px; height:160px;
    border:2px dashed #667eea; border-radius:8px;
    overflow:hidden; background:#f0f4ff;
    cursor:pointer; position:relative;
}
.photo-box img { width:100%; height:100%; object-fit:cover; display:none; }
.photo-placeholder {
    position:absolute; top:50%; left:50%;
    transform:translate(-50%,-50%);
    text-align:center; color:#667eea; pointer-events:none;
}
.photo-placeholder span { font-size:12px; display:block; margin-top:5px; }
input[type=file] { display:none; }
.photo-hint { font-size:12px; color:#888; margin-top:6px; }

table { width:100%; border-spacing:15px; }
td { font-weight:500; }

input[type=text],input[type=email],input[type=tel],
input[type=date],textarea,select {
    width:100%; padding:8px; border:1px solid #ccc;
    border-radius:5px; font-size:14px; box-sizing:border-box; background:white;
}
textarea { resize:none; height:60px; }
input:focus,textarea:focus,select:focus {
    border-color:#667eea; outline:none;
    box-shadow:0 0 5px rgba(102,126,234,0.3);
}

.section-heading td {
    background:#667eea; color:white;
    font-weight:700; font-size:14px;
    padding:8px 15px; border-radius:5px;
}

.sgpa-row { display:none; }

.sgpa-input {
    border-color:#667eea !important;
    background:#f0f4ff !important;
}
.cgpa-input {
    border-color:#e67e22 !important;
    background:#fff8f0 !important;
    font-weight:700; color:#c0392b;
}

.submit-btn { text-align:center; margin-top:20px; }
.submit-btn input {
    background:#667eea; color:white; border:none;
    padding:10px 30px; font-size:16px; border-radius:5px; cursor:pointer;
}
.submit-btn input:hover { background:#5a67d8; }

.logout {
    float:right; background:#ff4d4d; color:white;
    padding:6px 12px; text-decoration:none; border-radius:4px; font-size:14px;
}
.success { text-align:center; color:green; font-weight:bold; }
.error   { text-align:center; color:red;   font-weight:bold; }
</style>
</head>
<body>
<img class="gh" src="gh.jpg" alt="college">
<div class="container">

<a class="logout" href="logout.php">Logout</a>
<h2>G H Raisoni College of Engineering &amp; Management</h2>
<h3>Student Bio-Data Form</h3>

<?php
if($insert) echo "<p class='success'>Bio-Data Submitted Successfully!</p>";
if($error)  echo "<p class='error'>" . htmlspecialchars($error) . "</p>";
?>

<form method="post" enctype="multipart/form-data">

    <!-- PASSPORT PHOTO -->
    <div class="photo-section">
        <label>Passport Size Photo <span style="color:red">*</span></label>
        <div class="photo-box" onclick="document.getElementById('PHOTO').click()">
            <img id="photoPreview" src="" alt="Preview">
            <div class="photo-placeholder" id="photoPlaceholder">
                <svg width="40" height="40" fill="none" stroke="#667eea" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <span>Click to upload</span>
            </div>
        </div>
        <input type="file" id="PHOTO" name="PHOTO" accept="image/jpeg,image/png,image/gif" required>
        <p class="photo-hint">JPG / PNG / GIF &nbsp;|&nbsp; Max 2MB</p>
    </div>

    <table>

    <!-- PERSONAL DETAILS -->
    <tr class="section-heading"><td colspan="4">Personal Details</td></tr>
    <tr>
        <td>Name</td>
        <td><input type="text" name="NAME" required></td>
        <td>Section &amp; Roll No</td>
        <td><input type="text" name="SECTION" required></td>
    </tr>
    <tr>
        <td>Branch</td>
        <td><input type="text" name="BRANCH" required></td>
        <td>Year <span style="color:red">*</span></td>
        <td>
            <select name="YEAR" id="yearSelect" required onchange="showSgpaRows(this.value)">
                <option value="">-- Select Year --</option>
                <option value="First Year">First Year</option>
                <option value="Second Year">Second Year</option>
                <option value="Third Year">Third Year</option>
                <option value="Final Year">Final Year</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>Date of Birth</td>
        <td><input type="date" name="DOB" required></td>
        <td>Blood Group</td>
        <td><input type="text" name="BLOOD_GROUP" required></td>
    </tr>
    <tr>
        <td>Father Name</td>
        <td><input type="text" name="FATHER_NAME" required></td>
        <td>Father Occupation</td>
        <td><input type="text" name="FATHER_OCCUPATION"></td>
    </tr>

    <!-- CONTACT DETAILS -->
    <tr class="section-heading"><td colspan="4">Contact Details</td></tr>
    <tr>
        <td>Student Mobile</td>
        <td><input type="tel" name="PHONE_NO" required></td>
        <td>Email</td>
        <td><input type="email" name="EMAIL" required></td>
    </tr>
    <tr>
        <td>Guardian Number</td>
        <td><input type="tel" name="GUARDIAN_NO" required></td>
        <td>Hostel Name</td>
        <td><input type="text" name="HOSTEL_NAME"></td>
    </tr>
    <tr>
        <td>permanent Address</td>
        <td colspan="3"><textarea name="ADDRESS"></textarea></td>
    </tr>
    <tr>
        <td>Residential Address</td>
        <td colspan="3"><textarea name="RESIDENTIAL_ADDRESS"></textarea></td>
    </tr>

    <!-- QUALIFICATION DETAILS -->
    <tr class="section-heading"><td colspan="4">Qualification Details</td></tr>
    <tr>
        <td>10th Marks</td>
        <td><input type="text" name="TENTH_MARKS" required></td>
        <td>12th Marks</td>
        <td><input type="text" name="TEVELTH_MARKS" required></td>
    </tr>
    <tr>
        <td>Achievement</td>
        <td colspan="3"><input type="text" name="ACHIEVEMENT"></td>
    </tr>

   <!-- FIRST YEAR SGPA — shown for all years -->
    <tr class="sgpa-row first-year-row">
        <td colspan="4" style="background:#e8f5e9;color:#1a7f37;font-weight:700;padding:8px 15px;border-radius:5px;">
            First Year — Semester Results
        </td>
    </tr>
    <tr class="sgpa-row first-year-row">
        <td>Sem I SGPA</td>
        <td><input type="text" name="SEM1_SGPA" class="sgpa-input" placeholder="e.g. 8.5"></td>
        <td>Sem II SGPA</td>
        <td><input type="text" name="SEM2_SGPA" class="sgpa-input" placeholder="e.g. 8.5"></td>
    </tr>
    <tr class="sgpa-row first-year-row">
        <td>First Year CGPA</td>
        <td colspan="3"><input type="text" name="FIRST_YEAR_CGPA" class="cgpa-input" placeholder="e.g. 8.5"></td>
    </tr>

    <!-- SECOND YEAR SGPA — shown for second, third, final year -->
    <tr class="sgpa-row second-year-row">
        <td colspan="4" style="background:#e3f2fd;color:#0969da;font-weight:700;padding:8px 15px;border-radius:5px;">
            Second Year — Semester Results
        </td>
    </tr>
    <tr class="sgpa-row second-year-row">
        <td>Sem III SGPA</td>
        <td><input type="text" name="SEM3_SGPA" class="sgpa-input" placeholder="e.g. 8.5"></td>
        <td>Sem IV SGPA</td>
        <td><input type="text" name="SEM4_SGPA" class="sgpa-input" placeholder="e.g. 8.5"></td>
    </tr>
    <tr class="sgpa-row second-year-row">
        <td>Second Year CGPA</td>
        <td colspan="3"><input type="text" name="SECOND_YEAR_CGPA" class="cgpa-input" placeholder="e.g. 8.5"></td>
    </tr>

    <!-- THIRD YEAR SGPA — shown for third, final year -->
    <tr class="sgpa-row third-year-row">
        <td colspan="4" style="background:#f3e8ff;color:#8250df;font-weight:700;padding:8px 15px;border-radius:5px;">
            Third Year — Semester Results
        </td>
    </tr>
    <tr class="sgpa-row third-year-row">
        <td>Sem V SGPA</td>
        <td><input type="text" name="SEM5_SGPA" class="sgpa-input" placeholder="e.g. 8.5"></td>
        <td>Sem VI SGPA</td>
        <td><input type="text" name="SEM6_SGPA" class="sgpa-input" placeholder="e.g. 8.5"></td>
    </tr>
    <tr class="sgpa-row third-year-row">
        <td>Third Year CGPA</td>
        <td colspan="3"><input type="text" name="THIRD_YEAR_CGPA" class="cgpa-input" placeholder="e.g. 8.5"></td>
    </tr>

    <!-- FINAL YEAR SGPA — shown only for final year -->
    <tr class="sgpa-row final-year-row">
        <td colspan="4" style="background:#fdecea;color:#cf222e;font-weight:700;padding:8px 15px;border-radius:5px;">
            Final Year — Semester Results
        </td>
    </tr>
    <tr class="sgpa-row final-year-row">
        <td>Sem VII SGPA</td>
        <td><input type="text" name="SEM7_SGPA" class="sgpa-input" placeholder="e.g. 8.5"></td>
        <td>Sem VIII SGPA</td>
        <td><input type="text" name="SEM8_SGPA" class="sgpa-input" placeholder="e.g. 8.5"></td>
    </tr>
    <tr class="sgpa-row final-year-row">
        <td>Final Year CGPA</td>
        <td colspan="3"><input type="text" name="FINAL_YEAR_CGPA" class="cgpa-input" placeholder="e.g. 8.5"></td>
    </tr>
    </table>

    <div class="submit-btn">
        <input type="submit" value="Submit Bio-Data">
    </div>
</form>
</div>

<script>
function showSgpaRows(year) {
    // Hide all sgpa rows first
    document.querySelectorAll('.sgpa-row').forEach(row => {
        row.style.display = 'none';
    });

    // Each year shows its own rows PLUS all previous years
    // First Year  → show: first
    // Second Year → show: first + second
    // Third Year  → show: first + second + third
    // Final Year  → show: first + second + third + final

    if(year === 'First Year' || year === 'Second Year' ||
       year === 'Third Year' || year === 'Final Year'){
        document.querySelectorAll('.first-year-row').forEach(r => r.style.display = 'table-row');
    }
    if(year === 'Second Year' || year === 'Third Year' || year === 'Final Year'){
        document.querySelectorAll('.second-year-row').forEach(r => r.style.display = 'table-row');
    }
    if(year === 'Third Year' || year === 'Final Year'){
        document.querySelectorAll('.third-year-row').forEach(r => r.style.display = 'table-row');
    }
    if(year === 'Final Year'){
        document.querySelectorAll('.final-year-row').forEach(r => r.style.display = 'table-row');
    }
}

document.getElementById('PHOTO').addEventListener('change', function(){
    const file = this.files[0];
    if(!file) return;
    const reader = new FileReader();
    reader.onload = function(e){
        const preview = document.getElementById('photoPreview');
        preview.src = e.target.result;
        preview.style.display = 'block';
        document.getElementById('photoPlaceholder').style.display = 'none';
    };
    reader.readAsDataURL(file);
});
</script>
</body>
</html>
<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

$con = mysqli_connect("localhost","root","","test");

$active_year   = isset($_GET['year']) ? $_GET['year'] : 'First Year';
$allowed_years = ['First Year','Second Year','Third Year','Final Year'];
if(!in_array($active_year, $allowed_years)) $active_year = 'First Year';

$safe_year = mysqli_real_escape_string($con, $active_year);
$result = mysqli_query($con, "SELECT * FROM tgbiodata WHERE YEAR='$safe_year' ORDER BY SECTION ASC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Panel</title>
<style>
body { font-family:'Segoe UI',sans-serif; margin:0; padding:20px; background:#f4f4f4; }
h2   { text-align:center; color:#333; }

.tabs { display:flex; gap:10px; margin:20px 0 0; justify-content:center; }
.tab-btn {
    padding:10px 28px; font-size:15px; font-weight:600;
    border:2px solid transparent; border-radius:8px 8px 0 0;
    cursor:pointer; text-decoration:none; color:#555;
    background:#ddd; transition:0.2s;
}
.tab-btn:hover { background:#bbb; color:#222; }
.tab-btn.first  { border-color:#1a7f37; }
.tab-btn.second { border-color:#0969da; }
.tab-btn.third  { border-color:#8250df; }
.tab-btn.final  { border-color:#cf222e; }
.tab-btn.active.first  { background:#1a7f37; color:white; }
.tab-btn.active.second { background:#0969da; color:white; }
.tab-btn.active.third  { background:#8250df; color:white; }
.tab-btn.active.final  { background:#cf222e; color:white; }

.tab-content {
    background:white; border-radius:0 0 10px 10px;
    box-shadow:0 4px 15px rgba(0,0,0,0.1);
    padding:20px; overflow-x:auto;
}
.top-bar {
    display:flex; justify-content:space-between;
    align-items:center; margin-bottom:15px;
}
.logout {
    background:#ff4d4d; color:white; padding:6px 12px;
    text-decoration:none; border-radius:4px; font-size:14px;
}
.download-btn {
    background:green; color:white; padding:8px 15px;
    border:none; border-radius:5px; font-size:14px; text-decoration:none;
}
table { border-collapse:collapse; width:100%; font-size:12px; }
th {
    background:#444; color:white; padding:10px;
    text-align:left; white-space:nowrap;
}
td { border:1px solid #ddd; padding:7px; vertical-align:middle; }
tr:nth-child(even) { background:#f9f9f9; }

.photo-thumb {
    width:40px; height:50px; object-fit:cover;
    border-radius:4px; border:1px solid #ccc; display:block;
}
.no-photo {
    width:40px; height:50px; background:#eee;
    border:1px solid #ccc; border-radius:4px;
    display:flex; align-items:center; justify-content:center;
    font-size:9px; color:#999; text-align:center;
}
.dl-btn {
    background:orange; color:#333; padding:5px 10px;
    border:none; border-radius:4px; font-size:11px;
    text-decoration:none; white-space:nowrap;
}
.empty { text-align:center; color:#888; padding:40px; font-size:16px; }
</style>
</head>
<body>

<h2>Students Information — Teachers Panel</h2>

<div class="tabs">
    <a href="admin.php?year=First+Year"
       class="tab-btn first <?php echo $active_year=='First Year' ? 'active':''; ?>">
       🟢 First Year
    </a>
    <a href="admin.php?year=Second+Year"
       class="tab-btn second <?php echo $active_year=='Second Year' ? 'active':''; ?>">
       🔵 Second Year
    </a>
    <a href="admin.php?year=Third+Year"
       class="tab-btn third <?php echo $active_year=='Third Year' ? 'active':''; ?>">
       🟣 Third Year
    </a>
    <a href="admin.php?year=Final+Year"
       class="tab-btn final <?php echo $active_year=='Final Year' ? 'active':''; ?>">
       🔴 Final Year
    </a>
</div>

<div class="tab-content">
    <div class="top-bar">
        <span style="font-weight:600;font-size:15px;">
            Showing: <strong><?php echo htmlspecialchars($active_year); ?></strong>
            (<?php echo mysqli_num_rows($result); ?> records)
        </span>
        <div style="display:flex;gap:10px;align-items:center;">
            <a class="download-btn"
               href="download_all.php?year=<?php echo urlencode($active_year); ?>">
               ⬇ Download <?php echo htmlspecialchars($active_year); ?> Excel
            </a>
            <a class="logout" href="logout.php">Logout</a>
            <a class="delete-link"
            href="delete.php"
             style="background:#c0392b;color:white;padding:8px 15px;
             border-radius:5px;text-decoration:none;font-size:14px;font-weight:600;">
           🗑️ Delete Records
           </a>
        </div>
    </div>
</div>

    <?php if(mysqli_num_rows($result) == 0): ?>
        <p class="empty">No students found for <?php echo htmlspecialchars($active_year); ?>.</p>
    <?php else: ?>

    <table>
    <tr>
        <th>Photo</th>
        <th>Name</th>
        <th>Section &amp; Roll</th>
        <th>Branch</th>
        <th>Year</th>
        <th>DOB</th>
        <th>Blood Group</th>
        <th>Father Name</th>
        <th>Father Occupation</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Guardian No</th>
        <th>Hostel</th>
        <th>Correspondence Address</th>
        <th>Residential Address</th>
        <th>10th Marks</th>
        <th>12th Marks</th>
        <th>Sem1 SGPA</th>
        <th>Sem2 SGPA</th>
        <th>Y1 CGPA</th>
        <th>Sem3 SGPA</th>
        <th>Sem4 SGPA</th>
        <th>Y2 CGPA</th>
        <th>Sem5 SGPA</th>
        <th>Sem6 SGPA</th>
        <th>Y3 CGPA</th>
        <th>Sem7 SGPA</th>
        <th>Sem8 SGPA</th>
        <th>Final CGPA</th>
        <th>Achievement</th>
        <th>Download</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td>
            <?php if(!empty($row['PHOTO']) && !empty($row['PHOTO_MIME'])): ?>
                <img class="photo-thumb"
                     src="data:<?php echo htmlspecialchars($row['PHOTO_MIME']); ?>;base64,<?php echo $row['PHOTO']; ?>"
                     alt="photo">
            <?php else: ?>
                <div class="no-photo">No<br>Photo</div>
            <?php endif; ?>
        </td>
        <td><?php echo htmlspecialchars($row['NAME']); ?></td>
        <td><?php echo htmlspecialchars($row['SECTION']); ?></td>
        <td><?php echo htmlspecialchars($row['BRANCH']); ?></td>
        <td><?php echo htmlspecialchars($row['YEAR']); ?></td>
        <td><?php echo htmlspecialchars($row['DOB']); ?></td>
        <td><?php echo htmlspecialchars($row['BLOOD_GROUP']); ?></td>
        <td><?php echo htmlspecialchars($row['FATHER_NAME']); ?></td>
        <td><?php echo htmlspecialchars($row['FATHER_OCCUPATION']); ?></td>
        <td><?php echo htmlspecialchars($row['EMAIL']); ?></td>
        <td><?php echo htmlspecialchars($row['PHONE_NO']); ?></td>
        <td><?php echo htmlspecialchars($row['GUARDIAN_NO']); ?></td>
        <td><?php echo htmlspecialchars($row['HOSTEL_NAME']); ?></td>
        <td><?php echo htmlspecialchars($row['ADDRESS']); ?></td>
        <td><?php echo htmlspecialchars($row['RESIDENTIAL_ADDRESS']); ?></td>
        <td><?php echo htmlspecialchars($row['TENTH_MARKS']); ?></td>
        <td><?php echo htmlspecialchars($row['TEVELTH_MARKS']); ?></td>
        <td><?php echo htmlspecialchars($row['SEM1_SGPA']); ?></td>
        <td><?php echo htmlspecialchars($row['SEM2_SGPA']); ?></td>
        <td><?php echo htmlspecialchars($row['FIRST_YEAR_CGPA']); ?></td>
        <td><?php echo htmlspecialchars($row['SEM3_SGPA']); ?></td>
        <td><?php echo htmlspecialchars($row['SEM4_SGPA']); ?></td>
        <td><?php echo htmlspecialchars($row['SECOND_YEAR_CGPA']); ?></td>
        <td><?php echo htmlspecialchars($row['SEM5_SGPA']); ?></td>
        <td><?php echo htmlspecialchars($row['SEM6_SGPA']); ?></td>
        <td><?php echo htmlspecialchars($row['THIRD_YEAR_CGPA']); ?></td>
        <td><?php echo htmlspecialchars($row['SEM7_SGPA']); ?></td>
        <td><?php echo htmlspecialchars($row['SEM8_SGPA']); ?></td>
        <td><?php echo htmlspecialchars($row['FINAL_YEAR_CGPA']); ?></td>
        <td><?php echo htmlspecialchars($row['ACHIEVEMENT']); ?></td>
        <td>
            <a class="dl-btn" href="download.php?id=<?php echo $row['id']; ?>">
                ⬇ Download
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
    </table>

    <?php endif; ?>
</div>

</body>
</html>
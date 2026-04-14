<?php
session_start();

// ─── CONFIGURE ADMIN CREDENTIALS HERE ───────────────────────────
// These are separate delete-page credentials (extra security layer)
// Change these to whatever you want
define('DELETE_ADMIN_USER', 'naresh');
define('DELETE_ADMIN_PASS', '1234');
// ────────────────────────────────────────────────────────────────

$con = mysqli_connect("localhost","root","","test");

$verified      = false;  // login verified this request
$login_error   = "";
$delete_msg    = "";
$delete_error  = "";

$allowed_years = ['First Year','Second Year','Third Year','Final Year'];

// ── STEP 1: Verify admin credentials ────────────────────────────
if(isset($_POST['verify'])){
    $u = $_POST['del_username'] ?? '';
    $p = $_POST['del_password'] ?? '';
    if($u === DELETE_ADMIN_USER && $p === DELETE_ADMIN_PASS){
        $_SESSION['delete_verified'] = true;
    } else {
        $login_error = "Invalid username or password.";
    }
}

// ── STEP 2: Delete records ───────────────────────────────────────
if(isset($_POST['delete_year']) && isset($_SESSION['delete_verified'])){
    $year = $_POST['year_to_delete'] ?? '';
    if(in_array($year, $allowed_years)){
        $safe_year = mysqli_real_escape_string($con, $year);

        // Count first so we can report how many were deleted
        $count_res = mysqli_query($con,"SELECT COUNT(*) as cnt FROM tgbiodata WHERE YEAR='$safe_year'");
        $count_row = mysqli_fetch_assoc($count_res);
        $count     = $count_row['cnt'];

        if($count == 0){
            $delete_error = "No records found for $year. Nothing deleted.";
        } else {
            mysqli_query($con, "DELETE FROM tgbiodata WHERE YEAR='$safe_year'");
            $delete_msg = "Successfully deleted $count student record(s) from $year.";
        }
    } else {
        $delete_error = "Please select a valid year.";
    }
}

// ── STEP 3: Logout from delete page ─────────────────────────────
if(isset($_POST['delete_logout'])){
    unset($_SESSION['delete_verified']);
    header("Location: delete.php");
    exit();
}

// Get record counts per year for display
$counts = [];
foreach($allowed_years as $y){
    $safe = mysqli_real_escape_string($con, $y);
    $r    = mysqli_query($con,"SELECT COUNT(*) as cnt FROM tgbiodata WHERE YEAR='$safe'");
    $rw   = mysqli_fetch_assoc($r);
    $counts[$y] = $rw['cnt'];
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Delete Records — Admin</title>
<style>
* { box-sizing:border-box; }
body {
    margin:0; font-family:'Segoe UI',sans-serif;
    background:#1a1a2e; min-height:100vh;
    display:flex; align-items:center; justify-content:center;
}

.wrapper { width:100%; max-width:520px; padding:20px; }

/* ── Login card ── */
.login-card {
    background:white; border-radius:12px;
    padding:40px; box-shadow:0 20px 60px rgba(0,0,0,0.4);
    text-align:center;
}
.login-card .lock-icon {
    font-size:48px; margin-bottom:10px;
}
.login-card h2 {
    margin:0 0 5px; color:#c0392b; font-size:22px;
}
.login-card p {
    color:#888; font-size:13px; margin:0 0 25px;
}
.login-card input[type=text],
.login-card input[type=password]{
    width:100%; padding:11px 14px; margin-bottom:14px;
    border:1px solid #ddd; border-radius:6px;
    font-size:14px; outline:none;
}
.login-card input:focus { border-color:#c0392b; }
.login-card button {
    width:100%; padding:12px; background:#c0392b;
    color:white; border:none; border-radius:6px;
    font-size:15px; font-weight:600; cursor:pointer;
}
.login-card button:hover { background:#a93226; }
.login-error {
    background:#fdecea; color:#c0392b; padding:10px;
    border-radius:6px; margin-bottom:15px; font-size:13px;
}

/* ── Delete card ── */
.delete-card {
    background:white; border-radius:12px;
    padding:35px; box-shadow:0 20px 60px rgba(0,0,0,0.4);
}
.delete-card h2 {
    margin:0 0 5px; color:#333; font-size:20px; text-align:center;
}
.delete-card .subtitle {
    text-align:center; color:#888; font-size:13px; margin-bottom:25px;
}

/* Year cards grid */
.year-grid {
    display:grid; grid-template-columns:1fr 1fr;
    gap:12px; margin-bottom:25px;
}
.year-card {
    border:2px solid #ddd; border-radius:10px;
    padding:15px; text-align:center; cursor:pointer;
    transition:0.2s; position:relative;
}
.year-card:hover { transform:translateY(-2px); }
.year-card input[type=radio] {
    position:absolute; opacity:0; width:0; height:0;
}
.year-card .year-label { font-size:15px; font-weight:700; }
.year-card .year-count {
    font-size:12px; color:#888; margin-top:4px;
}
.year-card .year-count span { font-weight:700; font-size:18px; display:block; }

/* Colored selected states */
.year-card.first  { border-color:#1a7f37; }
.year-card.second { border-color:#0969da; }
.year-card.third  { border-color:#8250df; }
.year-card.final  { border-color:#cf222e; }

.year-card.first:has(input:checked)  { background:#e8f5e9; }
.year-card.second:has(input:checked) { background:#e3f2fd; }
.year-card.third:has(input:checked)  { background:#f3e8ff; }
.year-card.final:has(input:checked)  { background:#fdecea; }

/* Warning box */
.warning-box {
    background:#fff8e1; border:1px solid #f9a825;
    border-radius:8px; padding:14px 16px;
    font-size:13px; color:#795548; margin-bottom:20px;
    display:flex; gap:10px; align-items:flex-start;
}
.warning-box .warn-icon { font-size:20px; line-height:1; }

/* Delete button */
.delete-btn {
    width:100%; padding:13px; background:#c0392b;
    color:white; border:none; border-radius:8px;
    font-size:15px; font-weight:700; cursor:pointer;
    letter-spacing:0.5px;
}
.delete-btn:hover { background:#a93226; }

/* Messages */
.success-msg {
    background:#e8f5e9; border:1px solid #4CAF50;
    color:#1a5c2a; padding:12px 16px; border-radius:8px;
    margin-bottom:18px; font-size:14px; text-align:center;
}
.error-msg {
    background:#fdecea; border:1px solid #ef9a9a;
    color:#c0392b; padding:12px 16px; border-radius:8px;
    margin-bottom:18px; font-size:14px; text-align:center;
}

/* Top bar */
.top-bar {
    display:flex; justify-content:space-between;
    align-items:center; margin-bottom:20px;
}
.logout-btn {
    background:#555; color:white; border:none;
    padding:7px 14px; border-radius:6px;
    font-size:13px; cursor:pointer;
}
.logout-btn:hover { background:#333; }
.back-link {
    color:#667eea; text-decoration:none; font-size:13px;
}
.back-link:hover { text-decoration:underline; }

/* Confirm overlay */
.confirm-overlay {
    display:none; position:fixed; inset:0;
    background:rgba(0,0,0,0.6); z-index:100;
    align-items:center; justify-content:center;
}
.confirm-overlay.show { display:flex; }
.confirm-box {
    background:white; border-radius:12px;
    padding:35px; max-width:400px; width:90%;
    text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.4);
}
.confirm-box h3 { margin:0 0 10px; color:#c0392b; font-size:20px; }
.confirm-box p  { color:#555; margin:0 0 25px; font-size:14px; }
.confirm-btns   { display:flex; gap:12px; }
.confirm-yes {
    flex:1; padding:11px; background:#c0392b; color:white;
    border:none; border-radius:7px; font-size:15px;
    font-weight:700; cursor:pointer;
}
.confirm-no {
    flex:1; padding:11px; background:#eee; color:#333;
    border:none; border-radius:7px; font-size:15px; cursor:pointer;
}
</style>
</head>
<body>
<div class="wrapper">

<?php if(!isset($_SESSION['delete_verified'])): ?>

    <!-- ── LOGIN FORM ── -->
    <div class="login-card">
        <div class="lock-icon">🔐</div>
        <h2>Admin Verification</h2>
        <p>Enter admin credentials to access the delete panel</p>

        <?php if($login_error): ?>
            <div class="login-error">⚠️ <?php echo htmlspecialchars($login_error); ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="text"     name="del_username" placeholder="Username" required autofocus>
            <input type="password" name="del_password" placeholder="Password" required>
            <button type="submit" name="verify">Verify &amp; Continue →</button>
        </form>
    </div>

<?php else: ?>

    <!-- ── DELETE PANEL ── -->
    <div class="delete-card">

        <div class="top-bar">
            <a class="back-link" href="admin.php">← Back to Admin</a>
            <form method="post" style="margin:0;">
                <button class="logout-btn" name="delete_logout">Logout</button>
            </form>
        </div>

        <h2>🗑️ Delete Student Records</h2>
        <p class="subtitle">Select a year and permanently delete all its student data</p>

        <?php if($delete_msg): ?>
            <div class="success-msg">✅ <?php echo htmlspecialchars($delete_msg); ?></div>
        <?php endif; ?>

        <?php if($delete_error): ?>
            <div class="error-msg">❌ <?php echo htmlspecialchars($delete_error); ?></div>
        <?php endif; ?>

        <div class="warning-box">
            <span class="warn-icon">⚠️</span>
            <span>This action is <strong>permanent and cannot be undone.</strong>
            All student records including photos for the selected year will be deleted.</span>
        </div>

        <form method="post" id="deleteForm">

            <!-- YEAR SELECTION CARDS -->
            <div class="year-grid">
                <label class="year-card first">
                    <input type="radio" name="year_to_delete" value="First Year" required>
                    <div class="year-label">🟢 First Year</div>
                    <div class="year-count">
                        <span><?php echo $counts['First Year']; ?></span>
                        students
                    </div>
                </label>

                <label class="year-card second">
                    <input type="radio" name="year_to_delete" value="Second Year">
                    <div class="year-label">🔵 Second Year</div>
                    <div class="year-count">
                        <span><?php echo $counts['Second Year']; ?></span>
                        students
                    </div>
                </label>

                <label class="year-card third">
                    <input type="radio" name="year_to_delete" value="Third Year">
                    <div class="year-label">🟣 Third Year</div>
                    <div class="year-count">
                        <span><?php echo $counts['Third Year']; ?></span>
                        students
                    </div>
                </label>

                <label class="year-card final">
                    <input type="radio" name="year_to_delete" value="Final Year">
                    <div class="year-label">🔴 Final Year</div>
                    <div class="year-count">
                        <span><?php echo $counts['Final Year']; ?></span>
                        students
                    </div>
                </label>
            </div>

            <button type="button" class="delete-btn" onclick="showConfirm()">
                🗑️ Delete Selected Year Records
            </button>

            <!-- hidden submit — triggered by confirm dialog -->
            <button type="submit" name="delete_year" id="realSubmit" style="display:none;"></button>
        </form>
    </div>

    <!-- CONFIRM DIALOG -->
    <div class="confirm-overlay" id="confirmOverlay">
        <div class="confirm-box">
            <h3>Are you sure?</h3>
            <p id="confirmText">This will permanently delete all student records for the selected year.</p>
            <div class="confirm-btns">
                <button class="confirm-no"  onclick="hideConfirm()">Cancel</button>
                <button class="confirm-yes" onclick="doDelete()">Yes, Delete</button>
            </div>
        </div>
    </div>

<?php endif; ?>

</div>

<script>
function showConfirm(){
    const radios = document.querySelectorAll('input[name="year_to_delete"]');
    let selected = '';
    radios.forEach(r => { if(r.checked) selected = r.value; });

    if(!selected){
        alert('Please select a year to delete.');
        return;
    }

    document.getElementById('confirmText').textContent =
        'This will permanently delete ALL student records for "' + selected + '". This cannot be undone!';
    document.getElementById('confirmOverlay').classList.add('show');
}

function hideConfirm(){
    document.getElementById('confirmOverlay').classList.remove('show');
}

function doDelete(){
    document.getElementById('realSubmit').click();
}
</script>
</body>
</html>
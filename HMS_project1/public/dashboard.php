<?php
session_start();
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../config/db.php";
require_once "../class/user.php";

$database = new Database();
$db = $database->connect();
$user = new User($db);

$userList = $user->viewAll();

// Get counts
$doctor_count = $db->query("SELECT COUNT(*) AS total FROM doctor")->fetch(PDO::FETCH_ASSOC)['total'];
$patient_count = $db->query("SELECT COUNT(*) AS total FROM patient")->fetch(PDO::FETCH_ASSOC)['total'];
$staff_count   = $db->query("SELECT COUNT(*) AS total FROM staff")->fetch(PDO::FETCH_ASSOC)['total'];
$appointment_count = $db->query("SELECT COUNT(*) AS total FROM appointment")->fetch(PDO::FETCH_ASSOC)['total'];

// Appointments per month (for Bar Chart)
$bar_stmt = $db->query("
  SELECT DATE_FORMAT(APPT_DATE, '%b') AS month, COUNT(*) AS total
  FROM appointment
  GROUP BY MONTH(APPT_DATE)
  ORDER BY MONTH(APPT_DATE)
");
$bar_data = $bar_stmt->fetchAll(PDO::FETCH_ASSOC);

// Appointments per day (for Area Chart - recent 7 days)
$area_stmt = $db->query("
  SELECT DATE_FORMAT(APPT_DATE, '%b %d') AS date, COUNT(*) AS total
  FROM appointment
  WHERE APPT_DATE >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
  GROUP BY DATE(APPT_DATE)
  ORDER BY DATE(APPT_DATE)
");
$area_data = $area_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4">Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item active">Super Admin</li>
    </ol>

    <!-- ====== COUNT CARDS ====== -->
    <div class="row">
      <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white mb-4">
          <div class="card-body"><h5>Doctors</h5><h2><?= $doctor_count ?></h2></div>
          <div class="card-footer d-flex align-items-center justify-content-between">
            <a class="small text-white stretched-link" href="../public/doctor_dashboard.php">View Details</a>
            <div class="small text-white"><i class="fas fa-user-md"></i></div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white mb-4">
          <div class="card-body"><h5>Patients</h5><h2><?= $patient_count ?></h2></div>
          <div class="card-footer d-flex align-items-center justify-content-between">
            <a class="small text-white stretched-link" href="../public/patient.php">View Details</a>
            <div class="small text-white"><i class="fas fa-user-injured"></i></div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6">
        <div class="card bg-warning text-white mb-4">
          <div class="card-body"><h5>Staff</h5><h2><?= $staff_count ?></h2></div>
          <div class="card-footer d-flex align-items-center justify-content-between">
            <a class="small text-white stretched-link" href="../public/staff.php">View Details</a>
            <div class="small text-white"><i class="fas fa-users"></i></div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6">
        <div class="card bg-danger text-white mb-4">
          <div class="card-body"><h5>Appointments</h5><h2><?= $appointment_count ?></h2></div>
          <div class="card-footer d-flex align-items-center justify-content-between">
            <a class="small text-white stretched-link" href="../public/appointment_dashboard.php">View Details</a>
            <div class="small text-white"><i class="fas fa-calendar-check"></i></div>
          </div>
        </div>
      </div>
    </div>

    <!-- CHARTS -->
    <div class="row">
      <div class="col-xl-6">
        <div class="card mb-4">
          <div class="card-header">
            <i class="fas fa-chart-area me-1"></i>
            Area Chart Example
          </div>
          <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
        </div>
      </div>
      <div class="col-xl-6">
        <div class="card mb-4">
          <div class="card-header">
            <i class="fas fa-chart-bar me-1"></i>
            Bar Chart Example
          </div>
          <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
        </div>
      </div>
    </div>

    <!-- USER TABLE -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                User Table
            </div>
            <div class="card-body">
                <table id="datatablesSimple" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Is superAdmin</th>
                            <th>Patient</th>
                            <th>Staff</th>
                            <th>Doctor</th>
                            <th>Last Login</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userList as $userRow): ?>
                            <tr>
                                <td><?= htmlspecialchars($userRow['user_id']) ?></td>
                                <td><?= htmlspecialchars($userRow['user_name']) ?></td>
                                <td><?= htmlspecialchars($userRow["user_password"]) ?></td>
                                <td><?= htmlspecialchars($userRow["user_is_superadmin"]) ?></td>
                                <td><?= htmlspecialchars($userRow['pat_id'] ? $userRow['pat_id'] : '-') ?></td>
                                <td><?= htmlspecialchars($userRow['staff_id'] ? $userRow['staff_id'] : '-') ?></td>
                                <td><?= htmlspecialchars($userRow['doc_id'] ? $userRow['doc_id'] : '-') ?></td>
                                <td><?= htmlspecialchars($userRow['user_last_login'] ? $userRow['user_last_login'] : '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>


</main>
<?php require_once "../includes/footer.php";?>
</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
<!-- Consistent Bootstrap version -->
 <script>
  const barData = <?= json_encode($bar_data) ?>;
  const areaData = <?= json_encode($area_data) ?>;
</script>
<script src="../public/js/chart-area.js"></script>
<script src="../public/js/chart_bar.js"></script>

</body>
</html>
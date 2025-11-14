<?php
require_once "../../includes/header.php";
require_once "../../includes/sidebar.php";
require_once "../../config/db.php";
require_once "../../class/doctor.php";


$database = new Database();
$db = $database->connect();
$doctor = new Doctor($db);



// Search logic
$searchKeyword = $_GET['search'] ?? '';
$searchKeyword = trim($searchKeyword);

// Fetch future appointments

// Filter by patient name if search is used
if (!empty($searchKeyword)) {
  $appointments = array_filter($appointments, function ($appt) use ($searchKeyword) {
    $fullName = strtolower($appt['pat_first_name'] . ' ' . $appt['pat_last_name']);
    return strpos($fullName, strtolower($searchKeyword)) !== false;
  });
}
?>
<script src="../../includes/js/scripts.js"></script>
<script src="../../includes/js/datatables.js"></script>
<link href="../../includes/css/style.css" rel="stylesheet" />

<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4">Future Appointments</h1>

    <!-- ✅ Search Bar -->
    <form method="GET" class="mb-3">
      <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Search patient by name..." value="<?= htmlspecialchars($searchKeyword) ?>">
        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Search</button>
      </div>
    </form>

    <!-- ✅ Appointments Table -->
    <div class="card mb-4">
      <div class="card-header bg-light">
        <i class="fas fa-calendar-alt me-1"></i> Future Appointments List
      </div>
      <div class="card-body">
        <table id="datatablesSimple" class="table table-bordered table-hover align-middle">
          <thead class="table-primary">
            <tr>
              <th>Appointment ID</th>
              <th>Date</th>
              <th>Time</th>
              <th>Patient Name</th>
              <th>Contact</th>
              <th>Service</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($appointments)): ?>
              <?php foreach ($appointments as $appt): ?>
                <tr>
                  <td><?= htmlspecialchars($appt['appt_id']) ?></td>
                  <td><?= htmlspecialchars($appt['appt_date']) ?></td>
                  <td><?= htmlspecialchars($appt['appt_time']) ?></td>
                  <td><?= htmlspecialchars($appt['pat_first_name'] . ' ' . $appt['pat_last_name']) ?></td>
                  <td><?= htmlspecialchars($appt['pat_contact_num']) ?></td>
                  <td><?= htmlspecialchars($appt['serv_name']) ?></td>
                  <td><?= htmlspecialchars($appt['stat_name']) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center text-danger">No future appointments found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<?php require_once "../../includes/footer.php"; ?>
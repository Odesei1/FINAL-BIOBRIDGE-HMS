<?php
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../config/db.php";
require_once "../class/schedule.php";
require_once "../class/doctor.php";

$database = new Database();
$db = $database->connect();
$schedule = new Schedule($db);
$doctor = new Doctor($db);

$rows = $schedule->all();
$doctors = $doctor->getAllDoctors();


// UPDATE SCHEDULE
if (isset($_POST['update_schedule'])) {
    $SCHED_ID = $_POST['sched_id'];
    $DOC_ID = $_POST['doc_id'];
    $SCHED_DAYS = $_POST['sched_days'];
    $SCHED_START_TIME = $_POST['sched_start_time'];
    $SCHED_END_TIME = $_POST['sched_end_time'];

    if ($schedule->update($SCHED_ID, $DOC_ID, $SCHED_DAYS, $SCHED_START_TIME, $SCHED_END_TIME)) {
        $rows = $schedule->all();
    } else {
        echo "<script>alert('❌ Error updating schedule.'); window.location='../public/schedule.php';</script>";
    }
}

// DELETE SCHEDULE
if (isset($_GET['delete'])) {
    $SCHED_ID = $_GET['delete'];
    if ($schedule->delete($SCHED_ID)) {
        $rows = $schedule->all();
    } else {
        echo "<script>alert('❌ Error deleting schedule.'); window.location='../public/schedule.php';</script>";
    }
}
?>

<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4">Schedule Management</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item active">Schedule Dashboard</li>
    </ol>

    <?php require_once "../public/schedule_add.php"?>

    <!-- SCHEDULE TABLE -->
    <div class="card mb-4">
      <div class="card-header bg-light">
        <i class="fas fa-table me-1"></i> Schedule List
      </div>
      <div class="card-body">
        <table id="datatablesSimple" class="table table-bordered table-hover align-middle">
          <thead class="table-primary">
            <tr>
              <th>ID</th>
              <th>Doctor</th>
              <th>Day</th>
              <th>Start Time</th>
              <th>End Time</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($rows)): ?>
              <?php foreach ($rows as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['SCHED_ID']) ?></td>
                  <td><?= htmlspecialchars($row['DOC_FIRST_NAME'] . ' ' . $row['DOC_LAST_NAME']) ?></td>
                  <td><?= htmlspecialchars($row['SCHED_DAYS']) ?></td>
                  <td><?= htmlspecialchars($row['SCHED_START_TIME']) ?></td>
                  <td><?= htmlspecialchars($row['SCHED_END_TIME']) ?></td>
                  <td>
                    <!-- EDIT BUTTON -->
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['SCHED_ID'] ?>">
                      <i class="fas fa-edit"></i>
                    </button>

                    <!-- DELETE BUTTON -->
                    <a href="?delete=<?= $row['SCHED_ID'] ?>" class="btn btn-sm btn-danger"
                      onclick="return confirm('Are you sure you want to delete this schedule?');">
                      <i class="fas fa-trash-alt"></i>
                    </a>
                  </td>
                </tr>

                <!-- EDIT MODAL -->
                <div class="modal fade" id="editModal<?= $row['SCHED_ID'] ?>" tabindex="-1">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <form method="POST">
                        <div class="modal-header bg-warning text-white">
                          <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Schedule</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="sched_id" value="<?= $row['SCHED_ID'] ?>">
                          <div class="row g-3">
                            <div class="col-md-6">
                              <label class="form-label">Doctor</label>
                              <select class="form-select" name="doc_id" required>
                                <?php foreach ($doctors as $doc): ?>
                                  <option value="<?= $doc['doc_id'] ?>" <?= ($doc['doc_first_name'] . ' ' . $doc['doc_last_name']) == ($row['DOC_FIRST_NAME'] . ' ' . $row['DOC_LAST_NAME']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($doc['doc_first_name'] . ' ' . $doc['doc_last_name']) ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Day</label>
                              <select class="form-select" name="sched_days" required>
                                <?php 
                                  $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                                  foreach ($days as $day): ?>
                                  <option <?= ($row['SCHED_DAYS'] == $day) ? 'selected' : '' ?>><?= $day ?></option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Start Time</label>
                              <input type="time" class="form-control" name="sched_start_time" value="<?= $row['SCHED_START_TIME'] ?>" required>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">End Time</label>
                              <input type="time" class="form-control" name="sched_end_time" value="<?= $row['SCHED_END_TIME'] ?>" required>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" name="update_schedule" class="btn btn-success">Save Changes</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-center text-danger">No schedules found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<?php require_once "../includes/footer.php"; ?>

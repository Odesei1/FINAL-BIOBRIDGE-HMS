  <?php 
require_once "../config/db.php";
require_once "../class/schedule.php";
require_once "../class/doctor.php";

$database = new Database();
$db = $database->connect();
$schedule = new Schedule($db);
$doctor = new Doctor($db);

$rows = $schedule->all();
$doctors = $doctor->getAllDoctors();
  if (isset($_POST['add_schedule'])) {
    $DOC_ID = $_POST['doc_id'];
    $SCHED_DAYS = $_POST['sched_days'];
    $SCHED_START_TIME = $_POST['sched_start_time'];
    $SCHED_END_TIME = $_POST['sched_end_time'];

    if ($schedule->add($DOC_ID, $SCHED_DAYS, $SCHED_START_TIME, $SCHED_END_TIME)) {
        $rows = $schedule->all();
    } else {
        echo "<script>alert('❌ Error adding schedule.'); window.location='../public/schedule.php';</script>";
    }
}
  ?>
  <!-- ADD SCHEDULE BUTTON -->
    <div class="mb-3">
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
        <i class="fas fa-plus"></i> Add New Schedule
      </button>
    </div>

    <!-- ADD SCHEDULE MODAL -->
    <div class="modal fade" id="addScheduleModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="POST">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Add Schedule</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Doctor</label>
                  <select class="form-select" name="doc_id" required>
                    <option value="">-- Select Doctor --</option>
                    <?php foreach ($doctors as $doc): ?>
                      <option value="<?= $doc['doc_id'] ?>">
                        <?= htmlspecialchars($doc['doc_first_name'] . ' ' . $doc['doc_last_name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Day</label>
                  <select class="form-select" name="sched_days" required>
                    <option value="">-- Select Day --</option>
                    <option>Monday</option>
                    <option>Tuesday</option>
                    <option>Wednesday</option>
                    <option>Thursday</option>
                    <option>Friday</option>
                    <option>Saturday</option>
                    <option>Sunday</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Start Time</label>
                  <input type="time" class="form-control" name="sched_start_time" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">End Time</label>
                  <input type="time" class="form-control" name="sched_end_time" required>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" name="add_schedule" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Save
              </button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
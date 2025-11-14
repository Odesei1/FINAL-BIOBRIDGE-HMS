<?php
require_once "../config/db.php";
require_once "../class/appointment.php";
require_once "../class/patient.php";
require_once "../class/doctor.php";
require_once "../class/service.php";
require_once "../class/status.php";

$database = new Database();
$db = $database->connect();

$appointment = new Appointment($db);
$patient = new Patient($db);
$doctor = new Doctor($db);
$service = new Service($db);
$status = new Status($db);

// Fetch dropdown data
$patients = $patient->viewAll();
$doctors = $doctor->getAllDoctors();
$services = $service->all();
$statuses = $status->all();

// ✅ Add Appointment
if (isset($_POST['add_appointment'])) {
    $PAT_ID      = trim($_POST['pat_id']);
    $DOC_ID      = trim($_POST['doc_id']);
    $SERV_ID     = trim($_POST['serv_id']);
    $STAT_ID     = trim($_POST['stat_id']);
    $APPT_DATE   = trim($_POST['appt_date']);
    $APPT_TIME   = trim($_POST['appt_time']);

    // Call the create() method, which automatically generates the ID
    $appt_id = $appointment->create($PAT_ID, $DOC_ID, $SERV_ID, $STAT_ID, $APPT_DATE, $APPT_TIME);

    if ($appt_id) {
     $rows = $appointment->getAllAppointments();
    } else {
        echo "<script>alert('❌ Error creating appointment.'); window.location='../public/appointment_dashboard.php';</script>";
    }
}
?>

<!-- ✅ ADD APPOINTMENT BUTTON -->
<div class="mb-3">
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
    <i class="fa-solid fa-calendar-plus"></i> Create New Appointment
  </button>
</div>

<!-- ✅ ADD APPOINTMENT MODAL -->
<div class="modal fade" id="addAppointmentModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Add Appointment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Patient</label>
              <select class="form-select" name="pat_id" required>
                <option value="">-- Select Patient --</option>
                <?php foreach ($patients as $p): ?>
                  <option value="<?= $p['pat_id'] ?>"><?= $p['pat_first_name'] . ' ' . $p['pat_last_name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Doctor</label>
              <select class="form-select" name="doc_id" required>
                <option value="">-- Select Doctor --</option>
                <?php foreach ($doctors as $d): ?>
                  <option value="<?= $d['doc_id'] ?>"><?= $d['doc_first_name'] . ' ' . $d['doc_last_name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Service</label>
              <select class="form-select" name="serv_id" required>
                <option value="">-- Select Service --</option>
                <?php foreach ($services as $s): ?>
                  <option value="<?= $s['serv_id'] ?>"><?= $s['serv_name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select class="form-select" name="stat_id" required>
                <option value="">-- Select Status --</option>
                <?php foreach ($statuses as $st): ?>
                  <option value="<?= $st['stat_id'] ?>"><?= $st['stat_name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" name="appt_date" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Time</label>
              <input type="time" class="form-control" name="appt_time" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="add_appointment" class="btn btn-success">
            <i class="fas fa-save me-1"></i> Save
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
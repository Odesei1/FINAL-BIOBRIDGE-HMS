<?php
require_once "../config/db.php";
require_once "../class/medical_records.php";
require_once "../class/appointment.php";

$database = new Database();
$db = $database->connect();

$medicalRecord = new MedicalRecords($db);
$appointment = new Appointment($db);

$rows = $medicalRecord->all();
$appointments = $appointment->getAllAppointments();

// ADD MEDICAL RECORD
if (isset($_POST['add_medical_record'])) {
    $DIAGNOSIS = $_POST['med_rec_diagnosis'];
    $PRESCRIPTION = $_POST['med_rec_prescription'];
    $VISIT_DATE = $_POST['med_rec_visit_date'];
    $APPT_ID = $_POST['appt_id'];

    if ($medicalRecord->add($DIAGNOSIS, $PRESCRIPTION, $VISIT_DATE, $APPT_ID)) {
        $rows = $medicalRecord->all();
    } else {
        echo "<script>alert('❌ Error adding medical record.');</script>";
    }
}
?>
<?php
require_once "../config/db.php";
require_once "../class/medical_records.php";
require_once "../class/appointment.php";

$database = new Database();
$db = $database->connect();

$medicalRecord = new MedicalRecords($db);
$appointment = new Appointment($db);

$rows = $medicalRecord->all();
$appointments = $appointment->getAllAppointments();

// ADD MEDICAL RECORD
if (isset($_POST['add_medical_record'])) {
    $DIAGNOSIS = trim($_POST['med_rec_diagnosis']);
    $PRESCRIPTION = trim($_POST['med_rec_prescription']);
    $VISIT_DATE = $_POST['med_rec_visit_date'];
    $APPT_ID = $_POST['appt_id'];

    if ($medicalRecord->add($DIAGNOSIS, $PRESCRIPTION, $VISIT_DATE, $APPT_ID)) {
        echo "<script>alert('✅ Medical record added successfully!'); window.location='../public/medicalRecord_dashboard.php';</script>";
    } else {
        echo "<script>alert('❌ Error adding medical record.'); window.location='../public/medicalRecord_dashboard.php';</script>";
    }
}
?>

<!-- ADD MEDICAL RECORD BUTTON -->
<div class="mb-3">
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
    <i class="fas fa-notes-medical"></i> Add New Record
  </button>
</div>

<!-- ADD MEDICAL RECORD MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="fas fa-notes-medical me-2"></i>Add Medical Record</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Diagnosis</label>
              <input type="text" class="form-control" name="med_rec_diagnosis" placeholder="Enter diagnosis" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Prescription</label>
              <input type="text" class="form-control" name="med_rec_prescription" placeholder="Enter prescription" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Visit Date</label>
              <input type="date" class="form-control" name="med_rec_visit_date" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Appointment</label>
              <select class="form-select" name="appt_id" required>
                <option value="" disabled selected>Select Appointment</option>
                <?php foreach ($appointments as $appt): ?>
                  <option value="<?= htmlspecialchars($appt['appt_id']) ?>">
                    <?= htmlspecialchars('Appt #' . $appt['appt_id'] . ' — ' . $appt['appt_date']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" name="add_medical_record" class="btn btn-success">
            <i class="fas fa-save me-1"></i> Save
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

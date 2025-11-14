<?php
require_once "../config/db.php";
require_once "../class/patient.php";

$database = new Database();
$db = $database->connect();
$patient = new Patient($db);

$rows = $patient->viewAll();

// ADD PATIENT
if (isset($_POST['add_patient'])) {
    $PAT_FNAME     = trim($_POST['patient_first_name']);
    $PAT_MID_INIT  = trim($_POST['patient_middle_init']);
    $PAT_LNAME     = trim($_POST['patient_last_name']);
    $PAT_GENDER    = trim($_POST['patient_gender']);
    $PAT_BIRTHDATE = trim($_POST['patient_birthdate']);
    $PAT_CONTACT   = trim($_POST['patient_contact_num']);
    $PAT_EMAIL     = trim($_POST['patient_email']);
    $PAT_ADDRESS   = trim($_POST['patient_address']);

    if ($patient->add($PAT_FNAME, $PAT_MID_INIT, $PAT_LNAME, $PAT_GENDER, $PAT_BIRTHDATE, $PAT_CONTACT, $PAT_EMAIL, $PAT_ADDRESS)) {
        $rows = $patient->viewAll();
    } else {
        echo "<script>alert('❌ Error adding patient.'); window.location='../public/patient.php';</script>";
    }
}

?>

<!-- ADD PATIENT BUTTON -->
<div class="mb-3">
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
    <i class="fa-solid fa-user-plus"></i> Add New Patient
  </button>
</div>

<!-- ADD PATIENT MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add Patient</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">First Name</label>
              <input type="text" class="form-control" name="patient_first_name" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Initial</label>
              <input type="text" class="form-control" name="patient_middle_init">
            </div>
            <div class="col-md-4">
              <label class="form-label">Last Name</label>
              <input type="text" class="form-control" name="patient_last_name" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Gender</label>
              <select class="form-select" name="patient_gender" required>
                <option value="">-- Select Gender --</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Birthdate</label>
              <input type="date" class="form-control" name="patient_birthdate" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Contact Number</label>
              <input type="text" class="form-control" name="patient_contact_num" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="patient_email" required>
            </div>

            <div class="col-12">
              <label class="form-label">Address</label>
              <textarea class="form-control" name="patient_address" rows="2" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="add_patient" class="btn btn-success"><i class="fas fa-save me-1"></i>Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

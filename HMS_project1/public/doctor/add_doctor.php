<?php
require_once "../config/db.php";
require_once "../class/doctor.php";

$database = new Database();
$db = $database->connect();
$doctor = new Doctor($db);

// VIEW ALL DOCTORS
$rows = $doctor->getAllDoctors();

// ADD DOCTOR
if (isset($_POST['add_doctor'])) {
    $fname    = trim($_POST['doc_first_name']);
    $mname    = trim($_POST['doc_middle_init']);
    $lname    = trim($_POST['doc_last_name']);
    $contact  = trim($_POST['doc_contact_num']);
    $email    = trim($_POST['doc_email']);
    $spec_id  = trim($_POST['spec_id']); // optional, but safe

    if ($doctor->addDoctor($fname, $mname, $lname, $contact, $email, $spec_id)) {
        $rows = $doctor->getAllDoctors();
        exit;
    } else {
        echo "<script>alert('❌ Error adding doctor.'); window.location='../public/doctor_dashboard.php';</script>";
    }
}
?>

<!-- ADD DOCTOR BUTTON -->
<div class="mb-3">
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
    <i class="fa-solid fa-user-md"></i> Add New Doctor
  </button>
</div>

<!-- ADD DOCTOR MODAL -->
<div class="modal fade" id="addDoctorModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="fas fa-user-md me-2"></i>Add Doctor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">First Name</label>
              <input type="text" class="form-control" name="doc_first_name" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Initial</label>
              <input type="text" class="form-control" name="doc_middle_init">
            </div>
            <div class="col-md-4">
              <label class="form-label">Last Name</label>
              <input type="text" class="form-control" name="doc_last_name" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Contact Number</label>
              <input type="text" class="form-control" name="doc_contact_num" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="doc_email" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Specialization</label>
              <select class="form-select" name="spec_id" required>
                <option value="">-- Select Specialization --</option>
                <?php
                // Load specialization options
                $spec_stmt = $db->query("SELECT spec_id, spec_name FROM specialization ORDER BY spec_name ASC");
                while ($spec = $spec_stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$spec['spec_id']}'>{$spec['spec_name']}</option>";
                }
                ?>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" name="add_doctor" class="btn btn-success"><i class="fas fa-save me-1"></i>Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

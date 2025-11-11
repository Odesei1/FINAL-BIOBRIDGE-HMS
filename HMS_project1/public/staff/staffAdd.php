<?php
require_once "../config/db.php";
require_once "../class/staff.php";

$database = new Database();
$db = $database->connect();
$staff = new Staff($db);

$rows = $staff->all();

// ADD STAFF
if (isset($_POST['add_staff'])) {
    $STAFF_FNAME     = trim($_POST['staff_first_name']);
    $STAFF_MID_INIT  = trim($_POST['staff_middle_init']);
    $STAFF_LNAME     = trim($_POST['staff_last_name']);
    $STAFF_CONTACT   = trim($_POST['staff_contact_num']);
    $STAFF_EMAIL     = trim($_POST['staff_email']);

    if ($staff->add($STAFF_FNAME, $STAFF_LNAME, $STAFF_MID_INIT, $STAFF_CONTACT, $STAFF_EMAIL)) {
        $rows = $staff->all();
    } else {
        echo "<script>alert('❌ Error adding staff.'); window.location='../public/staff.php';</script>";
    }
}
?>

    <!-- ADD STAFF BUTTON -->
    <div class="mb-3">
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fa-solid fa-user-plus"></i> Add New Staff
      </button>
    </div>

    <!-- ADD STAFF MODAL -->
    <div class="modal fade" id="addModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="POST">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add Staff</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">First Name</label>
                  <input type="text" class="form-control" name="staff_first_name" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Middle Initial</label>
                  <input type="text" class="form-control" name="staff_middle_init">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Last Name</label>
                  <input type="text" class="form-control" name="staff_last_name" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Contact Number</label>
                  <input type="text" class="form-control" name="staff_contact_num" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" name="staff_email" required>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" name="add_staff" class="btn btn-success"><i class="fas fa-save me-1"></i>Save</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
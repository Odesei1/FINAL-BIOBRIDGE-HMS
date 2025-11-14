<?php
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../config/db.php";
require_once "../class/patient.php";

$database = new Database();
$db = $database->connect();
$patient = new Patient($db);

// Fetch all patients
$rows = $patient->viewAll();

// UPDATE PATIENT
if (isset($_POST['update_patient'])) {
    $PAT_ID = $_POST['pat_id'];
    $PAT_FNAME = $_POST['pat_first_name'];
    $PAT_MID_INIT = $_POST['pat_middle_init'];
    $PAT_LNAME = $_POST['pat_last_name'];
    $PAT_DOB = $_POST['pat_dob'];
    $PAT_GENDER = $_POST['pat_gender'];
    $PAT_CONTACT = $_POST['pat_contact_num'];
    $PAT_EMAIL = $_POST['pat_email'];
    $PAT_ADDRESS = $_POST['pat_address'];

    if ($patient->update($PAT_ID, $PAT_FNAME, $PAT_MID_INIT, $PAT_LNAME, $PAT_DOB, $PAT_GENDER, $PAT_CONTACT, $PAT_EMAIL, $PAT_ADDRESS)) {
        $rows = $patient->viewAll();
    } else {
        echo "<script>alert('❌ Error updating patient.'); window.location='../public/patient.php';</script>";
    }
}

// DELETE PATIENT
if (isset($_GET['delete'])) {
    $PAT_ID = $_GET['delete'];
    if ($patient->delete($PAT_ID)) {
        $rows = $patient->viewAll();
    } else {
        echo "<script>alert('❌ Error deleting patient.'); window.location='../public/patient.php';</script>";
    }
}
?>
<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4">Patient Management</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item active">Patient CRUD</li>
    </ol>

    <?php require_once "../public/patient/patientAdd.php"?>

    <div class="card mb-4">
      <div class="card-header bg-light">
        <i class="fas fa-table me-1"></i> Patient List
      </div>
      <div class="card-body">
        <table id="datatablesSimple" class="table table-bordered table-hover align-middle">
          <thead class="table-primary">
            <tr>
              <th>ID</th>
              <th>First Name</th>
              <th>M.I.</th>
              <th>Last Name</th>
              <th>Gender</th>
              <th>Contact</th>
              <th>Email</th>
              <th>Address</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($rows)): ?>
              <?php foreach ($rows as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['pat_id']) ?></td>
                  <td><?= htmlspecialchars($row['pat_first_name']) ?></td>
                  <td><?= htmlspecialchars($row['pat_middle_init']) ?></td>
                  <td><?= htmlspecialchars($row['pat_last_name']) ?></td>
                  <td><?= htmlspecialchars($row['pat_gender']) ?></td>
                  <td><?= htmlspecialchars($row['pat_contact_num']) ?></td>
                  <td><?= htmlspecialchars($row['pat_email']) ?></td>
                  <td><?= htmlspecialchars($row['pat_address']) ?></td>
                  <td>
                    <!-- EDIT BUTTON -->
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['pat_id'] ?>">
                      <i class="fas fa-edit"></i>
                    </button>

                    <!-- DELETE BUTTON -->
                    <a href="?delete=<?= $row['pat_id'] ?>" class="btn btn-sm btn-danger"
                      onclick="return confirm('Are you sure you want to delete this patient?');">
                      <i class="fas fa-trash-alt"></i>
                    </a>
                  </td>
                </tr>

                <!-- EDIT MODAL -->
                <div class="modal fade" id="editModal<?= $row['pat_id'] ?>" tabindex="-1">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <form method="POST" >
                        <div class="modal-header bg-warning text-white">
                          <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Patient</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="pat_id" value="<?= $row['pat_id'] ?>">
                          <div class="row g-3">
                            <div class="col-md-4">
                              <label class="form-label">First Name</label>
                              <input type="text" class="form-control" name="pat_first_name" value="<?= $row['pat_first_name'] ?>" required>
                            </div>
                            <div class="col-md-4">
                              <label class="form-label">Middle Initial</label>
                              <input type="text" class="form-control" name="pat_middle_init" value="<?= $row['pat_middle_init'] ?>">
                            </div>
                            <div class="col-md-4">
                              <label class="form-label">Last Name</label>
                              <input type="text" class="form-control" name="pat_last_name" value="<?= $row['pat_last_name'] ?>" required>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Date of Birth</label>
                              <input type="date" class="form-control" name="pat_dob" value="<?= $row['pat_dob'] ?>">
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Gender</label>
                              <select class="form-select" name="pat_gender">
                                <option value="Male" <?= $row['pat_gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= $row['pat_gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                              </select>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Contact Number</label>
                              <input type="text" class="form-control" name="pat_contact_num" value="<?= $row['pat_contact_num'] ?>">
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Email</label>
                              <input type="email" class="form-control" name="pat_email" value="<?= $row['pat_email'] ?>">
                            </div>
                            <div class="col-12">
                              <label class="form-label">Address</label>
                              <textarea class="form-control" name="pat_address"><?= $row['pat_address'] ?></textarea>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" name="update_patient" class="btn btn-success">Save Changes</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="8" class="text-center text-danger">No patients found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<?php
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
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

// UPDATE MEDICAL RECORD
if (isset($_POST['update_medical_record'])) {
    $ID = $_POST['med_rec_id'];
    $DIAGNOSIS = $_POST['med_rec_diagnosis'];
    $PRESCRIPTION = $_POST['med_rec_prescription'];
    $VISIT_DATE = $_POST['med_rec_visit_date'];
    $APPT_ID = $_POST['appt_id'];

    if ($medicalRecord->update($ID, $DIAGNOSIS, $PRESCRIPTION, $VISIT_DATE, $APPT_ID)) {
        $rows = $medicalRecord->all();
    } else {
        echo "<script>alert('❌ Error updating medical record.');</script>";
    }
}

// DELETE MEDICAL RECORD
if (isset($_GET['delete'])) {
    $ID = $_GET['delete'];
    if ($medicalRecord->delete($ID)) {
        $rows = $medicalRecord->all();
    } else {
        echo "<script>alert('❌ Error deleting medical record.');</script>";
    }
}
?>
<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4">Medical Records Management</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item active">Medical Records Dashboard</li>
    </ol>

<?php require_once "../public/medicalRecord_add.php"?>

    <!-- MEDICAL RECORDS TABLE -->
    <div class="card mb-4">
      <div class="card-header bg-light">
        <i class="fas fa-table me-1"></i> Medical Records List
      </div>
      <div class="card-body">
        <table id="datatablesSimple" class="table table-bordered table-hover align-middle">
          <thead class="table-primary">
            <tr>
              <th>ID</th>
              <th>Diagnosis</th>
              <th>Prescription</th>
              <th>Visit Date</th>
              <th>Appointment</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($rows)): ?>
              <?php foreach ($rows as $rec): ?>
                <tr>
                  <td><?= htmlspecialchars($rec['med_rec_id']) ?></td>
                  <td><?= htmlspecialchars($rec['med_rec_diagnosis']) ?></td>
                  <td><?= htmlspecialchars($rec['med_rec_prescription']) ?></td>
                  <td><?= htmlspecialchars($rec['med_rec_visit_date']) ?></td>
                  <td><?= htmlspecialchars($rec['appt_id'] . ' - ' . $rec['appt_date']) ?></td>
                  <td>
                    <!-- EDIT BUTTON -->
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $rec['med_rec_id'] ?>">
                      <i class="fas fa-edit"></i>
                    </button>

                    <!-- DELETE BUTTON -->
                    <a href="?delete=<?= $rec['med_rec_id'] ?>" class="btn btn-sm btn-danger"
                      onclick="return confirm('Are you sure you want to delete this record?');">
                      <i class="fas fa-trash-alt"></i>
                    </a>
                  </td>
                </tr>

                <!-- EDIT MODAL -->
                <div class="modal fade" id="editModal<?= $rec['med_rec_id'] ?>" tabindex="-1">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <form method="POST">
                        <div class="modal-header bg-warning text-white">
                          <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Medical Record</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="med_rec_id" value="<?= $rec['med_rec_id'] ?>">
                          <div class="row g-3">
                            <div class="col-md-6">
                              <label class="form-label">Diagnosis</label>
                              <input type="text" class="form-control" name="med_rec_diagnosis" value="<?= $rec['med_rec_diagnosis'] ?>" required>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Prescription</label>
                              <input type="text" class="form-control" name="med_rec_prescription" value="<?= $rec['med_rec_prescription'] ?>" required>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Visit Date</label>
                              <input type="date" class="form-control" name="med_rec_visit_date" value="<?= $rec['med_rec_visit_date'] ?>" required>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Appointment</label>
                              <select class="form-select" name="appt_id" required>
                                <option value="">Select Appointment</option>
                                <?php foreach ($appointments as $appt): ?>
                                  <option value="<?= $appt['appt_id'] ?>" <?= ($appt['appt_id'] == $rec['appt_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($appt['appt_id'] . ' - ' . $appt['appt_date']) ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" name="update_medical_record" class="btn btn-success">Save Changes</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-center text-danger">No medical records found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<?php require_once "../includes/footer.php"; ?>

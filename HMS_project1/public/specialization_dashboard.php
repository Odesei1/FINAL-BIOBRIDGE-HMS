<?php
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../config/db.php";
require_once "../class/specialization.php";
require_once "../class/doctor.php";

$database = new Database();
$db = $database->connect();
$specialization = new Specialization($db);
$doctor = new Doctor($db);

// Fetch all specializations
$rows = $specialization->all();

// ✅ UPDATE SPECIALIZATION
if (isset($_POST['update_specialization'])) {
    $SPEC_ID = $_POST['spec_id'];
    $SPEC_NAME = trim($_POST['spec_name']);

    if ($specialization->update($SPEC_ID, $SPEC_NAME)) {
         $rows = $specialization->all();
        exit;
    } else {
        echo "<script>alert('❌ Error updating specialization.'); window.location='../public/specialization_dashboard.php';</script>";
    }
}

// ✅ DELETE SPECIALIZATION
if (isset($_GET['delete'])) {
    $SPEC_ID = $_GET['delete'];
    if ($specialization->delete($SPEC_ID)) {
         $rows = $specialization->all();
        exit;
    } else {
        echo "<script>alert('❌ Error deleting specialization.');  window.location='../public/specialization_dashboard.php';</script>";
    }
}
?>

<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4">Specialization Management</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item active">Manage Specializations</li>
    </ol>

<div class="d-flex gap-3 mb-4 flex-wrap">
  <?php require_once "../public/specialization/specialization_add.php"; ?>
  <?php require_once "../public/specialization/browseInternalMeds.php"; ?>
</div>

    <!-- ✅ SPECIALIZATION TABLE -->
    <div class="card mb-4">
      <div class="card-header bg-light">
        <i class="fas fa-table me-1"></i> Specialization List
      </div>
      <div class="card-body">
        <table id="datatablesSimple" class="table table-bordered table-hover align-middle">
          <thead class="table-primary">
            <tr>
              <th>ID</th>
              <th>Specialization Name</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($rows)): ?>
              <?php foreach ($rows as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['spec_id']) ?></td>
                  <td><?= htmlspecialchars($row['spec_name']) ?></td>
                  <td class="text-center">
                    <!-- EDIT BUTTON -->
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['spec_id'] ?>">
                      <i class="fas fa-edit"></i>
                    </button>

                    <!-- DELETE BUTTON -->
                    <a href="?delete=<?= $row['spec_id'] ?>" class="btn btn-sm btn-danger"
                      onclick="return confirm('Are you sure you want to delete this specialization?');">
                      <i class="fas fa-trash-alt"></i>
                    </a>
                  </td>
                </tr>

                <!-- ✅ EDIT MODAL -->
                <div class="modal fade" id="editModal<?= $row['spec_id'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="POST">
                        <div class="modal-header bg-warning text-dark">
                          <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Specialization</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="spec_id" value="<?= $row['spec_id'] ?>">
                          <label class="form-label">Specialization Name</label>
                          <input type="text" class="form-control" name="spec_name"
                            value="<?= htmlspecialchars($row['spec_name']) ?>" required>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" name="update_specialization" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>Update
                          </button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" class="text-center text-muted">No specializations found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<?php require_once "../includes/footer.php"; ?>

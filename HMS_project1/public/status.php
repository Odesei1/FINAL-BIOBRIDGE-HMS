<?php
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../config/db.php";
require_once "../class/status.php";

$database = new Database();
$db = $database->connect();
$status = new Status($db);

// Fetch all status records
$rows = $status->all();

// ✅ ADD STATUS
if (isset($_POST['add_status'])) {
    $STAT_NAME = $_POST['STAT_NAME'];
    if ($status->add($STAT_NAME)) {
       $rows = $status->all();
    } else {
        echo "<script>alert('❌ Error adding status.'); window.location='../public/status.php';</script>";
    }
}

// ✅ UPDATE STATUS
if (isset($_POST['update_status'])) {
    $STAT_ID = $_POST['STAT_ID'];
    $STAT_NAME = $_POST['STAT_NAME'];

    if ($status->update($STAT_ID, $STAT_NAME)) {
       $rows = $status->all();
    } else {
        echo "<script>alert('❌ Error updating status.'); window.location='../public/status.php';</script>";
    }
}

// ✅ DELETE STATUS
if (isset($_GET['delete'])) {
    $STAT_ID = $_GET['delete'];
    if ($status->delete($STAT_ID)) {
       $rows = $status->all();
    } else {
        echo "<script>alert('❌ Error deleting status.'); window.location='../public/status.php';</script>";
    }
}
?>

<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4">Status Management</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item active">Manage Appointment Status</li>
    </ol>

    <!-- ADD STATUS FORM -->
    <div class="card mb-4">
      <div class="card-header bg-light">
        <i class="fas fa-plus-circle me-1"></i> Add New Status
      </div>
      <div class="card-body">
        <form method="POST" class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Status Name</label>
            <input type="text" class="form-control" name="STAT_NAME" placeholder="e.g. Scheduled" required>
          </div>
          <div class="col-md-6 align-self-end">
            <button type="submit" name="add_status" class="btn btn-primary">
              <i class="fas fa-save me-1"></i> Add Status
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- STATUS TABLE -->
    <div class="card mb-4">
      <div class="card-header bg-light">
        <i class="fas fa-list me-1"></i> Status List
      </div>
      <div class="card-body">
        <table id="datatablesSimple" class="table table-bordered table-hover align-middle">
          <thead class="table-primary">
            <tr>
              <th>ID</th>
              <th>Status Name</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($rows)): ?>
              <?php foreach ($rows as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['stat_id']) ?></td>
                  <td><?= htmlspecialchars($row['stat_name']) ?></td>
                  <td>
                    <!-- EDIT BUTTON -->
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['stat_id'] ?>">
                      <i class="fas fa-edit"></i>
                    </button>

                    <!-- DELETE BUTTON -->
                    <a href="?delete=<?= $row['stat_id'] ?>" class="btn btn-sm btn-danger"
                      onclick="return confirm('Are you sure you want to delete this status?');">
                      <i class="fas fa-trash-alt"></i>
                    </a>
                  </td>
                </tr>

                <!-- EDIT MODAL -->
                <div class="modal fade" id="editModal<?= $row['STAT_ID'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="POST">
                        <div class="modal-header bg-warning text-white">
                          <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Status</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="STAT_ID" value="<?= $row['STAT_ID'] ?>">
                          <div class="mb-3">
                            <label class="form-label">Status Name</label>
                            <input type="text" class="form-control" name="STAT_NAME" value="<?= htmlspecialchars($row['STAT_NAME']) ?>" required>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" name="update_status" class="btn btn-success">Save Changes</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3" class="text-center text-danger">No status found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<?php require_once "../includes/footer.php"; ?>

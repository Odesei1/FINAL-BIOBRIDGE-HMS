<?php
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../config/db.php";
require_once "../class/service.php";

$database = new Database();
$db = $database->connect();
$service = new Service($db);

$rows = $service->all();

// ✅ UPDATE SERVICE
if (isset($_POST['update_service'])) {
  $serv_id = $_POST['serv_id'];
  $serv_name = $_POST['serv_name'];
  $serv_description = $_POST['serv_description'];
  $serv_price = $_POST['serv_price'];

  if ($service->update($serv_id, $serv_name, $serv_description, $serv_price)) {
    $rows = $service->all();
  } else {
    echo "<script>alert('❌ Error updating service.'); window.location='../public/service.php';</script>";
  }
}

// ✅ DELETE SERVICE
if (isset($_GET['delete'])) {
  $serv_id = $_GET['delete'];
  if ($service->delete($serv_id)) {
    $rows = $service->all();
  } else {
    echo "<script>alert('❌ Error deleting service.'); window.location='../public/service.php';</script>";
  }
}
?>
<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4">Service Management</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item active">Manage Hospital Services</li>
    </ol>

    <?php require_once "../public/service.add.php" ?>

    <!-- SERVICE TABLE -->
    <div class="card mb-4">
      <div class="card-header bg-light">
        <i class="fas fa-list me-1"></i> Service List
      </div>
      <div class="card-body">
        <table id="datatablesSimple" class="table table-bordered table-hover align-middle">
          <thead class="table-primary text-center">
            <tr>
              <th>ID</th>
              <th>Service Name</th>
              <th>Description</th>
              <th>Price (₱)</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($rows)): ?>
              <?php foreach ($rows as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['serv_id']) ?></td>
                  <td><?= htmlspecialchars($row['serv_name']) ?></td>
                  <td><?= htmlspecialchars($row['serv_description']) ?></td>
                  <td class="text-end"><?= number_format($row['serv_price'], 2) ?></td>
                  <td class="text-center">
                    <!-- EDIT BUTTON -->
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['serv_id'] ?>">
                      <i class="fas fa-edit"></i>
                    </button>

                    <!-- VIEW APPOINTMENTS BUTTON -->
                    <a href="../public/viewAppointmentsByService.php?id=<?= $row['serv_id'] ?>" class="btn btn-sm btn-info">
                      <i class="fas fa-calendar-alt"></i>
                    </a>

                    <!-- DELETE BUTTON -->
                    <a href="?delete=<?= $row['serv_id'] ?>" class="btn btn-sm btn-danger"
                      onclick="return confirm('Are you sure you want to delete this service?');">
                      <i class="fas fa-trash-alt"></i>
                    </a>
                  </td>
                </tr>

                <!-- EDIT MODAL -->
                <div class="modal fade" id="editModal<?= $row['serv_id'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="POST">
                        <div class="modal-header bg-warning text-white">
                          <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Service</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="serv_id" value="<?= $row['serv_id'] ?>">
                          <div class="mb-3">
                            <label class="form-label">Service Name</label>
                            <input type="text" class="form-control" name="serv_name" value="<?= htmlspecialchars($row['serv_name']) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="serv_description" value="<?= htmlspecialchars($row['serv_description']) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Price (₱)</label>
                            <input type="number" step="0.01" class="form-control" name="serv_price" value="<?= htmlspecialchars($row['serv_price']) ?>" required>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" name="update_service" class="btn btn-success">Save Changes</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center text-danger">No services found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<?php require_once "../includes/footer.php"; ?>
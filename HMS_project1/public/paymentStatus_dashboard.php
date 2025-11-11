<?php
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../config/db.php";
require_once "../class/payment_status.php";

$database = new Database();
$db = $database->connect();
$paymentStatus = new payment_status($db);

// VIEW ALL
$rows = $paymentStatus->all();

// UPDATE PAYMENT STATUS
if (isset($_POST['update_status'])) {
    $PYMT_STAT_ID = $_POST['PYMT_STAT_ID'];
    $PYMT_STAT_NAME = $_POST['PYMT_STAT_NAME'];

    if ($paymentStatus->update($PYMT_STAT_ID, $PYMT_STAT_NAME)) {
        echo "<script>alert('✅ Payment status updated successfully!'); window.location='paymentStatus.php';</script>";
    } else {
        echo "<script>alert('❌ Error updating payment status!');</script>";
    }
}

// DELETE PAYMENT STATUS
if (isset($_GET['delete'])) {
    $PYMT_STAT_ID = $_GET['delete'];
    if ($paymentStatus->delete($PYMT_STAT_ID)) {
        echo "<script>alert('🗑️ Payment status deleted successfully!'); window.location='paymentStatus_dashboard.php';</script>";
    } else {
        echo "<script>alert('❌ Error deleting payment status!');</script>";
    }
}
?>

<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4">Payment Status</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item active">Manage Payment Status</li>
    </ol>

    <?php require_once "../public/paymentStatus_add.php"; ?>

    <!-- TABLE -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <i class="fas fa-list me-1"></i> Payment Status List
      </div>
      <div class="card-body">
        <table id="datatablesSimple" class="table table-bordered table-hover text-center align-middle">
          <thead class="table-primary">
            <tr>
              <th>ID</th>
              <th>Payment Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['pymt_stat_id']) ?></td>
                <td><?= htmlspecialchars($row['pymt_stat_name']) ?></td>
                <td>
                  <!-- EDIT BUTTON -->
                  <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['pymt_stat_id'] ?>">
                    <i class="fas fa-edit"></i>
                  </button>

                  <!-- DELETE BUTTON -->
                  <a href="?delete=<?= $row['pymt_stat_id'] ?>" class="btn btn-sm btn-danger"
                    onclick="return confirm('Are you sure you want to delete this payment status?');">
                    <i class="fas fa-trash-alt"></i>
                  </a>
                </td>
              </tr>

              <!-- EDIT MODAL -->
              <div class="modal fade" id="editModal<?= $row['pymt_stat_id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form method="POST">
                      <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Payment Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="PYMT_STAT_ID" value="<?= $row['pymt_stat_id'] ?>">
                        <div class="form-group">
                          <label>Payment Status</label>
                          <input type="text" name="PYMT_STAT_NAME" class="form-control"
                                 value="<?= htmlspecialchars($row['pymt_stat_name']) ?>"
                                 placeholder="Enter payment status (e.g. Paid, Pending, Cancelled)" required>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" name="update_status" class="btn btn-success">
                          <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<?php require_once "../includes/footer.php"; ?>

<?php
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../config/db.php";
require_once "../class/payment_method.php";

$database = new Database();
$db = $database->connect();
$payment = new payment_method($db);

// VIEW ALL
$rows = $payment->viewAll();

// UPDATE PAYMENT METHOD
if (isset($_POST['update_payment'])) {
    $pymt_meth_id = $_POST['pymt_meth_id'];
    $pymt_meth_name = $_POST['pymt_meth_name'];

    if ($payment->update($pymt_meth_id, $pymt_meth_name)) {
        $rows = $payment->viewAll();
    } else {
        echo "<script>alert('❌ Error updating payment method!');</script>";
    }
}

// DELETE PAYMENT METHOD
if (isset($_GET['delete'])) {
    $pymt_meth_id = $_GET['delete'];
    if ($payment->delete($pymt_meth_id)) {
        $rows = $payment->viewAll();
    } else {
        echo "<script>alert('❌ Error deleting payment method!');</script>";
    }
}
?>

<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4">Payment Methods</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item active">Manage Payment Methods</li>
    </ol>

    <?php require_once "../public/paymentMethod_add.php"?>

    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <i class="fas fa-list me-1"></i> Payment Method List
      </div>
      <div class="card-body">
        <table id="datatablesSimple" class="table table-bordered table-hover text-center align-middle">
          <thead class="table-primary">
            <tr>
              <th>ID</th>
              <th>Payment Method</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['pymt_meth_id']) ?></td>
                <td><?= htmlspecialchars($row['pymt_meth_name']) ?></td>
                <td>
                  <!-- EDIT BUTTON -->
                  <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['pymt_meth_id'] ?>">
                    <i class="fas fa-edit"></i>
                  </button>

                  <!-- DELETE BUTTON -->
                  <a href="?delete=<?= $row['pymt_meth_id'] ?>" class="btn btn-sm btn-danger"
                    onclick="return confirm('Are you sure you want to delete this payment method?');">
                    <i class="fas fa-trash-alt"></i>
                  </a>
                </td>
              </tr>

              <!-- EDIT MODAL -->
              <div class="modal fade" id="editModal<?= $row['pymt_meth_id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form method="POST">
                      <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Payment Method</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="pymt_meth_id" value="<?= $row['pymt_meth_id'] ?>">
                        <div class="form-group">
                          <label>Payment Method Name</label>
                          <input type="text" name="pymt_meth_name" value="<?= htmlspecialchars($row['pymt_meth_name']) ?>" class="form-control" required>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" name="update_payment" class="btn btn-success">
                          <i class="fas fa-save me-1"></i>Save Changes
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

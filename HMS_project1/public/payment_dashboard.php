<?php
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../config/db.php";
require_once "../class/payment.php";
require_once "../class/payment_method.php";
require_once "../class/payment_status.php";

$database = new Database();
$db = $database->connect();

$payment = new Payment($db);
$methodObj = new payment_method($db);
$statusObj = new payment_status($db);

$rows = $payment->all(); // includes joins
$methods = $methodObj->viewAll();
$statuses = $statusObj->all();

// UPDATE PAYMENT
if (isset($_POST['update_payment'])) {
    $PYMT_ID = $_POST['pymt_id'];
    $AMOUNT = $_POST['pymt_amount_paid'];
    $DATE = $_POST['pymt_date'];
    $METHOD = $_POST['pymt_meth_id'];
    $STATUS = $_POST['pymt_stat_id'];
    $APPT_ID = $_POST['appt_id'];

    if ($payment->update($PYMT_ID, $AMOUNT, $DATE, $METHOD, $STATUS, $APPT_ID)) {
        echo "<script>alert('✅ Payment updated successfully!'); window.location='payment.php';</script>";
    } else {
        echo "<script>alert('❌ Error updating payment.');</script>";
    }
}

// DELETE PAYMENT
if (isset($_GET['delete'])) {
    $PYMT_ID = $_GET['delete'];
    if ($payment->delete($PYMT_ID)) {
        echo "<script>alert('🗑️ Payment deleted successfully!'); window.location='payment.php';</script>";
    } else {
        echo "<script>alert('❌ Error deleting payment.');</script>";
    }
}
?>

<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4">Payment Management</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item active">Payment Dashboard</li>
    </ol>

    <?php require_once "../public/payment_Add.php"; ?>

    <!-- PAYMENT TABLE -->
    <div class="card mb-4">
      <div class="card-header bg-light">
        <i class="fas fa-credit-card me-1"></i> Payment List
      </div>
      <div class="card-body">
        <table id="datatablesSimple" class="table table-bordered table-hover align-middle text-center">
          <thead class="table-primary">
            <tr>
              <th>ID</th>
              <th>Amount Paid</th>
              <th>Date</th>
              <th>Payment Method</th>
              <th>Status</th>
              <th>Appointment ID</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($rows)): ?>
              <?php foreach ($rows as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['pymt_id']) ?></td>
                  <td><?= htmlspecialchars($row['pymt_amount_paid']) ?></td>
                  <td><?= htmlspecialchars($row['pymt_date']) ?></td>
                  <td><?= htmlspecialchars($row['pymt_meth_name']) ?></td>
                  <td><?= htmlspecialchars($row['pymt_stat_name']) ?></td>
                  <td><?= htmlspecialchars($row['appt_id']) ?></td>
                  <td>
                    <!-- EDIT BUTTON -->
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['pymt_id'] ?>">
                      <i class="fas fa-edit"></i>
                    </button>

                    <!-- DELETE BUTTON -->
                    <a href="?delete=<?= $row['pymt_id'] ?>" class="btn btn-sm btn-danger"
                      onclick="return confirm('Are you sure you want to delete this payment?');">
                      <i class="fas fa-trash-alt"></i>
                    </a>
                  </td>
                </tr>

                <!-- EDIT MODAL -->
                <div class="modal fade" id="editModal<?= $row['pymt_id'] ?>" tabindex="-1">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <form method="POST">
                        <div class="modal-header bg-warning text-white">
                          <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Payment</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="pymt_id" value="<?= $row['pymt_id'] ?>">
                          <div class="row g-3">
                            <div class="col-md-4">
                              <label class="form-label">Amount Paid</label>
                              <input type="number" step="0.01" name="pymt_amount_paid" class="form-control"
                                     value="<?= $row['pymt_amount_paid'] ?>" required>
                            </div>
                            <div class="col-md-4">
                              <label class="form-label">Date</label>
                              <input type="date" name="pymt_date" class="form-control"
                                     value="<?= $row['pymt_date'] ?>" required>
                            </div>
                            <div class="col-md-4">
                              <label class="form-label">Appointment ID</label>
                              <input type="number" name="appt_id" class="form-control"
                                     value="<?= $row['appt_id'] ?>" required>
                            </div>

                            <div class="col-md-6">
                              <label class="form-label">Payment Method</label>
                              <select name="pymt_meth_id" class="form-select" required>
                                <option value="">-- Select Method --</option>
                                <?php foreach ($methods as $method): ?>
                                  <option value="<?= $method['pymt_meth_id'] ?>"
                                    <?= ($row['pymt_meth_id'] == $method['pymt_meth_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($method['pymt_meth_name']) ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>

                            <div class="col-md-6">
                              <label class="form-label">Payment Status</label>
                              <select name="pymt_stat_id" class="form-select" required>
                                <option value="">-- Select Status --</option>
                                <?php foreach ($statuses as $status): ?>
                                  <option value="<?= $status['pymt_stat_id'] ?>"
                                    <?= ($row['pymt_stat_id'] == $status['pymt_stat_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($status['pymt_stat_name']) ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" name="update_payment" class="btn btn-success">Save Changes</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center text-danger">No payments found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<?php require_once "../includes/footer.php"; ?>

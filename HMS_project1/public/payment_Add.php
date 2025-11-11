<?php
require_once "../config/db.php";
require_once "../class/payment.php";
require_once "../class/payment_method.php";
require_once "../class/payment_status.php";

$database = new Database();
$db = $database->connect();

$payment = new Payment($db);
$methodObj = new payment_method($db);
$statusObj = new payment_status($db);

$methods = $methodObj->viewAll();
$statuses = $statusObj->all();

// ADD PAYMENT
if (isset($_POST['add_payment'])) {
    $amount = $_POST['pymt_amount_paid'];
    $date = $_POST['pymt_date'];
    $method = $_POST['pymt_meth_id'];
    $status = $_POST['pymt_stat_id'];
    $appt_id = $_POST['appt_id'];

    if ($payment->add($amount, $date, $method, $status, $appt_id)) {
        echo "<script>alert('✅ Payment added successfully!'); window.location='payment.php';</script>";
    } else {
        echo "<script>alert('❌ Error adding payment!');</script>";
    }
}
?>

<!-- ADD PAYMENT BUTTON -->
<div class="mb-3">
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
    <i class="fas fa-plus-circle"></i> Add Payment
  </button>
</div>

<!-- ADD PAYMENT MODAL -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="fas fa-credit-card me-2"></i>Add New Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Amount Paid</label>
              <input type="number" step="0.01" name="pymt_amount_paid" class="form-control" placeholder="Enter amount" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Date</label>
              <input type="date" name="pymt_date" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Appointment ID</label>
              <input type="number" name="appt_id" class="form-control" placeholder="Enter appointment ID" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Payment Method</label>
              <select name="pymt_meth_id" class="form-select" required>
                <option value="">-- Select Method --</option>
                <?php foreach ($methods as $method): ?>
                  <option value="<?= $method['pymt_meth_id'] ?>">
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
                  <option value="<?= $status['pymt_stat_id'] ?>">
                    <?= htmlspecialchars($status['pymt_stat_name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" name="add_payment" class="btn btn-success">
            <i class="fas fa-save me-1"></i> Save
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

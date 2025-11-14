<?php
require_once "../config/db.php";
require_once "../class/payment_status.php";

$database = new Database();
$db = $database->connect();
$paymentStatus = new payment_status($db);

// Fetch all payment statuses
$rows = $paymentStatus->all();

// ADD PAYMENT STATUS
if (isset($_POST['add_payment_status'])) {
    $pymt_stat_name = trim($_POST['pymt_stat_name']);

    if ($paymentStatus->add($pymt_stat_name)) {
        echo "<script>alert('✅ Payment status added successfully!'); window.location.reload();</script>";
    } else {
        echo "<script>alert('❌ Error adding payment status!');</script>";
    }
}
?>

<!-- ADD BUTTON -->
<div class="mb-3">
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
    <i class="fas fa-credit-card"></i> Add Payment Status
  </button>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Payment Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label class="form-label">Payment Status Name</label>
          <input type="text" name="pymt_stat_name" class="form-control" placeholder="Enter payment status (e.g. Paid, Pending, Cancelled)" required>
        </div>
        <div class="modal-footer">
          <button type="submit" name="add_payment_status" class="btn btn-success">
            <i class="fas fa-save me-1"></i> Save
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

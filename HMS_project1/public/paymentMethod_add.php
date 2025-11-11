<?php
require_once "../config/db.php";
require_once "../class/payment_method.php";

$database = new Database();
$db = $database->connect();
$payment = new payment_method($db);

$rows = $payment->viewAll();

// ADD PAYMENT METHOD
if (isset($_POST['add_payment'])) {
    $pymt_meth_name = $_POST['pymt_meth_name'];

    if ($payment->add($pymt_meth_name)) {
        $rows = $payment->viewAll();
    } else {
        echo "<script>alert('❌ Error adding payment method!');</script>";
    }
}
?>
<!-- ADD BUTTON -->
<div class="mb-3">
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
    <i class="fas fa-credit-card"></i> Add Payment Method
  </button>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Payment Method</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label class="form-label">Payment Method Name</label>
          <input type="text" name="pymt_meth_name" class="form-control" placeholder="Enter payment method (e.g. Cash, Credit Card)" required>
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

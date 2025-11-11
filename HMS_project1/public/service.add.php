<?php
require_once "../config/db.php";
require_once "../class/service.php";

$database = new Database();
$db = $database->connect();
$service = new Service($db);

$rows = $service->all();

// ✅ ADD SERVICE
if (isset($_POST['add_service'])) {
    $serv_name = $_POST['serv_name'];
    $serv_description = $_POST['serv_description'];
    $serv_price = $_POST['serv_price'];

    if ($service->add($serv_name, $serv_description, $serv_price)) {
      $rows = $service->all();
    } else {
        echo "<script>alert('❌ Error adding service.'); window.location='../public/service.php';</script>";
    }
}
?>

    <!-- ADD SERVICE -->
    <div class="card mb-4">
      <div class="card-header bg-light">
        <i class="fas fa-plus-circle me-1"></i> Add New Service
      </div>
      <div class="card-body">
        <form method="POST" class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Service Name</label>
            <input type="text" class="form-control" name="serv_name" placeholder="e.g. Consultation" required>
          </div>
          <div class="col-md-5">
            <label class="form-label">Description</label>
            <input type="text" class="form-control" name="serv_description" placeholder="Brief description" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Price (₱)</label>
            <input type="number" step="0.01" class="form-control" name="serv_price" placeholder="e.g. 500.00" required>
          </div>
          <div class="col-12 text-end">
            <button type="submit" name="add_service" class="btn btn-primary">
              <i class="fas fa-save me-1"></i> Add Service
            </button>
          </div>
        </form>
      </div>
    </div>
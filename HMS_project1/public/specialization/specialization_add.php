<?php
if (isset($_POST['add_specialization'])) {
    $SPEC_NAME = trim($_POST['spec_name']);
    
    if ($specialization->add($SPEC_NAME)) {
        $rows = $specialization->all();
        exit;
    } else {
        echo "<script>alert('❌ Error adding specialization.'); window.location='../public/specialization_dashboard.php';</script>";
    }
}
?>
<div class="mb-3">
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSpecModal">
    <i class="fa-solid fa-plus"></i> Add New Specialization
  </button>
</div>

<!-- ADD SPECIALIZATION MODAL -->
<div class="modal fade" id="addSpecModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="fas fa-stethoscope me-2"></i>Add Specialization</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label class="form-label">Specialization Name</label>
          <input type="text" class="form-control" name="spec_name" placeholder="e.g., Internal Medicine" required>
        </div>
        <div class="modal-footer">
          <button type="submit" name="add_specialization" class="btn btn-success">
            <i class="fas fa-save me-1"></i>Save
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>


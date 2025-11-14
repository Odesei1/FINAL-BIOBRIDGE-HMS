<?php
require_once __DIR__ ."/../../config/db.php";
require_once __DIR__ ."/../../class/doctor.php";
require_once __DIR__ ."/../../class/specialization.php";

$database = new Database();
$db = $database->connect();
$doctor = new Doctor($db);
$specialization = new Specialization($db);

// Fetch all specializations
$specializations = $specialization->all();

// Handle form submission
$spec_id = $_POST['spec_id'] ?? null;
$spec_row = $spec_id ? $specialization->findID($spec_id) : null;
$spec_name = $spec_row ? $spec_row['spec_name'] : 'Unknown';
$doctors = $spec_id ? $doctor->getBySpecialization($spec_id) : [];
?>

<main>
<div class="mb-3">
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#browseDoctorsModal">
    <i class="fas fa-search"></i> Browse Doctors
  </button>
</div>


<!-- BROWSE DOCTORS MODAL -->
<div class="modal fade" id="browseDoctorsModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form method="POST">
        <!-- Modal Header -->
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">
            <i class="fas fa-user-md me-2"></i>Browse Doctors by Specialization
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
          <!-- Dropdown to select specialization -->
          <div class="row mb-3">
            <div class="col-md-8">
              <label class="form-label">Select Specialization</label>
              <select class="form-select" name="spec_id" required>
                <option value="">-- Select Specialization --</option>
                <?php foreach ($specializations as $spec): ?>
                  <option value="<?= $spec['spec_id'] ?>" <?= ($spec_id == $spec['spec_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($spec['spec_name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
              <button type="submit" class="btn btn-success w-100">
                <i class="fas fa-search me-1"></i> Browse
              </button>
            </div>
          </div>

          <!-- Results Section -->
          <?php if ($spec_id): ?>
            <h5 class="mb-3">Doctors in <strong><?= htmlspecialchars($spec_name) ?></strong></h5>
            <?php if (!empty($doctors)): ?>
              <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                  <thead class="table-primary">
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Contact</th>
                      <th>Email</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($doctors as $doc): ?>
                      <tr>
                        <td><?= htmlspecialchars($doc['doc_id']) ?></td>
                        <td><?= htmlspecialchars($doc['doc_first_name'] . ' ' . $doc['doc_last_name']) ?></td>
                        <td><?= htmlspecialchars($doc['doc_contact_num']) ?></td>
                        <td><?= htmlspecialchars($doc['doc_email']) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted mb-0">No doctors found for this specialization.</p>
            <?php endif; ?>
          <?php endif; ?>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Close
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
</main>

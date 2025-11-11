<?php
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../config/db.php";
require_once "../class/doctor.php";
require_once "../class/specialization.php";

$database = new Database();
$db = $database->connect();

$specialization = new specialization($db);
$doctor = new Doctor($db);
$rows = $doctor->getAllDoctors();

// DELETE FUNCTIONALITY
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  if ($doctor->deleteDoctor($id)) {
    $rows = $doctor->getAllDoctors();
  } else {
    echo "<script>alert('Failed to delete doctor.'); window.location='../public/doctor_dashboard.php';</script>";
  }
}


if (isset($_POST["update_doctor"])) {
  $doc_id = ($_POST["doc_id"]);
  $fname = trim($_POST['doc_first_name']);
  $mname = trim($_POST['doc_middle_init']);
  $lname = trim($_POST['doc_last_name']);
  $contact = trim($_POST['doc_contact_num']);
  $email = trim($_POST['doc_email']);
  $spec_id = $_POST['spec_id'];
  if ($doctor->updateDoctor($doc_id, $fname, $mname, $lname, $contact, $email, $spec_id)) {
    $rows = $doctor->getAllDoctors();
  } else {
    echo "<script>alert('❌ Error updating Doctor.'); window.location='../public/doctor_dashboard.php';</script>";
  }
}


?>
<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4">Doctor Management</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item active">Doctor CRUD</li>
    </ol>

    <?php require_once "../public/doctor/add_doctor.php"; ?>

    <!-- STAT CARDS -->
      <div class="col-xl-3 col-md-6">
        <div class="card text-bg-secondary mb-3">
          <div class="card-header">Specializations</div>
          <div class="card-footer d-flex align-items-center justify-content-between">
            <a class="small text-white stretched-link" href="../public/specialization_dashboard.php">View Details</a>
            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
          </div>
        </div>
      </div>
    </div>

    <!-- ✅ Search Bar -->
    <form method="GET" class="mb-3">
      <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Search doctor by name " value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Search</button>
      </div>
    </form>


    <!-- ✅ Doctor Carousel -->
    <div class="card mb-4">
      <div class="card-header bg-light">
        <i class="fas fa-table me-1"></i> Doctor List
      </div>
      <div class="card-body position-relative">
        <?php if (!empty($rows)): ?>
          <div id="doctorCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">

              <?php
              // Sort doctors by ID 
              usort($rows, function ($a, $b) {
                return $a['doc_id'] <=> $b['doc_id'];
              });

              $chunks = array_chunk($rows, 3); // Group doctors into sets of 3
              $isActive = true;
              foreach ($chunks as $group):
              ?>

                <div class="carousel-item <?= $isActive ? 'active' : '' ?>">
                  <div class="container">
                    <div class="row justify-content-center">
                      <?php foreach ($group as $row): ?>
                        <div class="col-md-4 mb-4 d-flex justify-content-center">
                          <div class="card shadow-sm border-0" style="width: 22rem;">
                            <img src="../assets/images/dafultimage.jpg" class="card-img-top" alt="Doctor Image" style="height: 250px; object-fit: cover;">
                            <div class="card-body text-center">
                              <h5 class="card-title text-primary">
                                Dr. <?= htmlspecialchars($row['doc_first_name'] . ' ' . $row['doc_last_name']) ?>
                              </h5>
                              <p class="card-text">
                                <strong>ID:</strong> <?= htmlspecialchars($row['doc_id']) ?><br>
                                <strong>Contact:</strong> <?= htmlspecialchars($row['doc_contact_num']) ?><br>
                                <strong>Email:</strong> <?= htmlspecialchars($row['doc_email']) ?><br>
                                <strong>Specialization:</strong> <?= htmlspecialchars($row['spec_name'] ?? 'N/A') ?>
                              </p>
                              <div class="d-flex justify-content-around">
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['doc_id'] ?>">
                                  <i class="fas fa-edit"></i> Edit
                                </button>

                                <a href="../public/doctors_page.php?id=<?= $row['doc_id'] ?>" class="btn btn-sm btn-info">
                                  <i class="fas fa-calendar-alt"></i> Schedule
                                </a>

                                <a href="?delete=<?= $row['doc_id'] ?>" class="btn btn-sm btn-danger"
                                  onclick="return confirm('Are you sure you want to delete this doctor?');">
                                  <i class="fas fa-trash-alt"></i> Delete
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- ✅ EDIT MODAL -->
                        <div class="modal fade" id="editModal<?= $row['doc_id'] ?>" tabindex="-1">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                              <form method="POST">
                                <div class="modal-header bg-warning text-dark">
                                  <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Doctor</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                  <input type="hidden" name="doc_id" value="<?= $row['doc_id'] ?>">
                                  <div class="row g-3">
                                    <div class="col-md-4">
                                      <label class="form-label">First Name</label>
                                      <input type="text" class="form-control" name="doc_first_name" value="<?= htmlspecialchars($row['doc_first_name']) ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                      <label class="form-label">Middle Initial</label>
                                      <input type="text" class="form-control" name="doc_middle_init" value="<?= htmlspecialchars($row['doc_middle_init']) ?>">
                                    </div>
                                    <div class="col-md-4">
                                      <label class="form-label">Last Name</label>
                                      <input type="text" class="form-control" name="doc_last_name" value="<?= htmlspecialchars($row['doc_last_name']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Contact Number</label>
                                      <input type="text" class="form-control" name="doc_contact_num" value="<?= htmlspecialchars($row['doc_contact_num']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Email</label>
                                      <input type="email" class="form-control" name="doc_email" value="<?= htmlspecialchars($row['doc_email']) ?>" required>
                                    </div>
                                    <div class="col-md-12">
                                      <select class="form-select" name="spec_id" required>
                                        <option value="">-- Select Specialization --</option>
                                        <?php
                                        $spec_stmt = $db->query("SELECT spec_id, spec_name FROM specialization ORDER BY spec_name ASC");
                                        while ($spec = $spec_stmt->fetch(PDO::FETCH_ASSOC)) {
                                          $selected = ($spec['spec_name'] == $row['spec_name']) ? 'selected' : '';
                                          echo "<option value='{$spec['spec_id']}' $selected>{$spec['spec_name']}</option>";
                                        }
                                        ?>
                                      </select>

                                    </div>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="submit" name="update_doctor" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Update
                                  </button>
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
                <?php $isActive = false; ?>
              <?php endforeach; ?>
            </div>

            <script src="../public/js/autoScroll_doctor.js"></script>

            <!-- ✅ Carousel Controls (inside carousel) -->
            <button class="carousel-control-prev" type="button" data-bs-target="#doctorCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#doctorCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
            </button>
          </div>
        <?php else: ?>
          <p class="text-center text-muted">No doctors found.</p>
        <?php endif; ?>
      </div>
    </div>
</main>
<style>
  .carousel-control-prev,
  .carousel-control-next {
    top: 50%;
    width: 40px;
    height: 40px;
    background-color: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
  }

  .carousel-control-prev {
    left: -30px;
  }

  .carousel-control-next {
    right: -30px;
  }
</style>

<?php require_once "../includes/footer.php"; ?>
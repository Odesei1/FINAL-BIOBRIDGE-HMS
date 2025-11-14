<?php
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../config/db.php";
require_once "../class/appointment.php";
require_once "../class/doctor.php";
require_once "../class/service.php";
require_once "../class/status.php";

$database = new Database();
$db = $database->connect();
$appointment = new Appointment($db);
$doctor = new Doctor($db);
$service = new Service($db);
$status = new Status($db);

// ✅ Fetch all appointments
$rows = $appointment->getAllAppointments();

// ✅ Cancel Appointment
if (isset($_GET['cancel'])) {
    $appt_id = $_GET['cancel'];
    if ($appointment->cancel($appt_id)) {
     $rows = $appointment->getAllAppointments();
    } else {
        echo "<script>alert('Failed to cancel appointment.');window.location='../public/appointment_dashboard.php';</script>";
    }
}

// ✅ Update Appointment
if (isset($_POST['update_appointment'])) {
    $appt_id = $_POST['appt_id'];
    $doc_id = $_POST['doc_id'];
    $serv_id = $_POST['serv_id'];
    $appt_date = $_POST['appt_date'];
    $appt_time = $_POST['appt_time'];

    if ($appointment->update($appt_id, $doc_id, $serv_id, $appt_date, $appt_time, $appt_notes)) {
     $rows = $appointment->getAllAppointments();
    } else {
        echo "<script>alert('Failed to update appointment.');window.location='../public/appointment_dashboard.php';</script>";
    }
}

// ✅ Update Appointment Status
if (isset($_POST['update_status'])) {
    $appt_id = $_POST['appt_id'];
    $stat_id = $_POST['stat_id'];

    if ($appointment->updateStatus($appt_id, $stat_id)) {
     $rows = $appointment->getAllAppointments();
    } else {
        echo "<script>alert('Failed to update appointment status.'); window.location='../public/appointment_dashboard.php';</script>";
    }
}

?>

<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Appointment Management</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Appointment Dashboard</li>
        </ol>

        <?php require_once "../public/appointment_Add.php"; ?>

        <!-- APPOINTMENT TABLE -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <i class="fas fa-calendar-alt me-1"></i> Appointment List
            </div>
            <div class="card-body">
                <table id="datatablesSimple" class="table table-bordered table-hover align-middle">
                    <thead class="table-primary">
                    <tr>
                        <th>Reference ID</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($rows)): ?>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['appt_id']) ?></td>
                                <td><?= htmlspecialchars($row['appt_date']) ?></td>
                                <td><?= htmlspecialchars($row['appt_time']) ?></td>
                                <td><?= htmlspecialchars($row['pat_fname'] . ' ' . $row['pat_lname']) ?></td>
                                <td><?= htmlspecialchars($row['doc_fname'] . ' ' . $row['doc_lname']) ?></td>
                                <td><?= htmlspecialchars($row['serv_name']) ?></td>
                                <td><?= htmlspecialchars($row['stat_name']) ?></td>
                                <td>
                                    <!-- EDIT BUTTON -->
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editModal<?= htmlspecialchars($row['appt_id']) ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- CANCEL BUTTON -->
                                    <a href="?cancel=<?= htmlspecialchars($row['appt_id']) ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to cancel this appointment?');">
                                        <i class="fas fa-ban"></i>
                                    </a>

                                    <!-- STATUS BUTTON -->
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                            data-bs-target="#statusModal<?= htmlspecialchars($row['appt_id']) ?>">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- EDIT MODAL -->
                            <div class="modal fade" id="editModal<?= htmlspecialchars($row['appt_id']) ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit
                                                    Appointment</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="appt_id" value="<?= htmlspecialchars($row['appt_id']) ?>">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Date</label>
                                                        <input type="date" class="form-control" name="appt_date"
                                                               value="<?= htmlspecialchars($row['appt_date']) ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Time</label>
                                                        <input type="time" class="form-control" name="appt_time"
                                                               value="<?= htmlspecialchars($row['appt_time']) ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Doctor</label>
                                                        <select class="form-select" name="doc_id" required>
                                                            <?php
                                                            $doctors = $doctor->getAllDoctors();
                                                            foreach ($doctors as $doc) {
                                                                $selected = ($doc['doc_id'] == $row['doc_id']) ? 'selected' : '';
                                                                echo "<option value='" . htmlspecialchars($doc['doc_id']) . "' $selected>" . htmlspecialchars($doc['doc_first_name'] . ' ' . $doc['doc_last_name']) . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Service</label>
                                                        <select class="form-select" name="serv_id" required>
                                                            <?php
                                                            $services = $service->all();
                                                            foreach ($services as $serv) {
                                                                $selected = ($serv['serv_id'] == $row['serv_id']) ? 'selected' : '';
                                                                echo "<option value='" . htmlspecialchars($serv['serv_id']) . "' $selected>" . htmlspecialchars($serv['serv_name']) . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="update_appointment" class="btn btn-success">
                                                    Save Changes
                                                </button>
                                                <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- STATUS MODAL -->
                            <div class="modal fade" id="statusModal<?= htmlspecialchars($row['appt_id']) ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title"><i class="fas fa-sync-alt me-2"></i>Update Status</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="appt_id" value="<?= htmlspecialchars($row['appt_id']) ?>">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" name="stat_id" required>
                                                    <?php
                                                    $statuses = $status->all();
                                                    foreach ($statuses as $status_item) {
                                                        $selected = ($status_item['stat_id'] == $row['stat_id']) ? 'selected' : '';
                                                        echo "<option value='" . htmlspecialchars($status_item['stat_id']) . "' $selected>" . htmlspecialchars($status_item['stat_name']) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="update_status" class="btn btn-success">
                                                    Save Changes
                                                </button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-danger">No appointments found.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>
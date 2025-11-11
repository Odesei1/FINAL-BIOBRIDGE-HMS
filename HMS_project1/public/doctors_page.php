<?php
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../config/db.php";
require_once "../class/doctor.php";
require_once "../class/specialization.php";
require_once "../class/schedule.php";

$database = new Database();
$db = $database->connect();

$doctor = new Doctor($db);
$specialization = new Specialization($db);

// ✅ Get doctor ID from URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $doc_id = $_GET['id'];
} else {
    echo "<script>alert('No doctor selected.'); window.location='../public/doctor_dashboard.php';</script>";
    exit;
}

// ✅ Fetch doctor info
$stmt = $db->prepare("
    SELECT d.*, s.spec_name 
    FROM doctor d 
    LEFT JOIN specialization s ON d.spec_id = s.spec_id 
    WHERE d.doc_id = :doc_id
");
$stmt->execute([':doc_id' => $doc_id]);
$doctor_info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor_info) {
    echo "<script>alert('Doctor not found.'); window.location='../public/doctor_dashboard.php';</script>";
    exit;
}

// ✅ Fetch schedule of this doctor
$schedQuery = $db->prepare("
    SELECT * FROM schedule 
    WHERE doc_id = :doc_id 
    ORDER BY FIELD(SCHED_DAYS, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')
");
$schedQuery->execute([':doc_id' => $doc_id]);
$schedules = $schedQuery->fetchAll(PDO::FETCH_ASSOC);

// ✅ Get appointments
$todayAppointments = $doctor->viewTodayAppointments($doc_id);
$futureAppointments = $doctor->viewFutureAppointments($doc_id);
$pastAppointments = $doctor->viewPreviousAppointments($doc_id);
?>

<main>
    <div class="container-fluid px-4 mt-4">

        <!-- Doctor Profile -->
        <div class="card p-3 shadow-sm">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <img src="../assets/images/dafultimage.jpg" class="card-img-top" alt="Doctor Image" style="height: 250px; object-fit: cover;">
                </div>
                <div class="col-md-9">
                    <h3 class="text-primary mb-0">
                        Dr. <?= htmlspecialchars($doctor_info['doc_first_name'] . " " . $doctor_info['doc_last_name']) ?>
                    </h3>
                    <p class="text-muted mb-1"><?= htmlspecialchars($doctor_info['spec_name'] ?? "No Specialization") ?></p>
                    <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($doctor_info['doc_email']) ?></p>
                    <p class="mb-1"><strong>Contact:</strong> <?= htmlspecialchars($doctor_info['doc_contact_num']) ?></p>
                </div>
            </div>
        </div>

        <!-- Clinic Schedule -->
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-calendar-alt me-2"></i>Clinic Schedule
            </div>

            <div class="card-body">
                <?php if (!empty($schedules)): ?>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Day</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $sched): ?>
                                <tr>
                                    <td><?= htmlspecialchars($sched['sched_days']) ?></td>
                                    <td><?= date("h:i A", strtotime($sched['sched_start_time'])) ?></td>
                                    <td><?= date("h:i A", strtotime($sched['sched_end_time'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning">No schedule found for this doctor.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Appointments -->
        <div class="row">

            <!-- Today -->
            <div class="col-md-4 mb-4">
                <div class="card border-success">
                    <div class="card-header bg-success text-white"><i class="fas fa-calendar-day me-2"></i>Today's Appointments</div>
                    <div class="card-body">
                        <?php if (empty($todayAppointments)): ?>
                            <p class="text-muted text-center">No appointments today.</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($todayAppointments as $appt): ?>
                                    <li class="list-group-item">
                                        <strong><?= htmlspecialchars($appt['pat_first_name'] . ' ' . $appt['pat_last_name']) ?></strong><br>
                                        <small><?= htmlspecialchars($appt['serv_name']) ?> — <?= date("h:i A", strtotime($appt['appt_time'])) ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Future -->
            <div class="col-md-4 mb-4">
                <div class="card border-info">
                    <div class="card-header bg-info text-white"><i class="fas fa-calendar-plus me-2"></i>Future Appointments</div>
                    <div class="card-body">
                        <?php if (empty($futureAppointments)): ?>
                            <p class="text-muted text-center">No future appointments.</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($futureAppointments as $appt): ?>
                                    <li class="list-group-item">
                                        <strong><?= htmlspecialchars($appt['pat_first_name'] . ' ' . $appt['pat_last_name']) ?></strong><br>
                                        <small><?= htmlspecialchars($appt['serv_name']) ?> — <?= date("M j, Y h:i A", strtotime($appt['appt_date'] . " " . $appt['appt_time'])) ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Previous -->
            <div class="col-md-4 mb-4">
                <div class="card border-secondary">
                    <div class="card-header bg-secondary text-white"><i class="fas fa-calendar-minus me-2"></i>Previous Appointments</div>
                    <div class="card-body">
                        <?php if (empty($pastAppointments)): ?>
                            <p class="text-muted text-center">No previous appointments.</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($pastAppointments as $appt): ?>
                                    <li class="list-group-item">
                                        <strong><?= htmlspecialchars($appt['pat_first_name'] . ' ' . $appt['pat_last_name']) ?></strong><br>
                                        <small><?= htmlspecialchars($appt['serv_name']) ?> — <?= date("M j, Y", strtotime($appt['appt_date'])) ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

    </div>
</main>

<?php require_once "../includes/footer.php"; ?>
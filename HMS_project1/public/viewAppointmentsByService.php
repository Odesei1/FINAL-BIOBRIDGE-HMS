<?php
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../config/db.php";
require_once "../class/appointment.php";
require_once "../class/service.php";

$database = new Database();
$db = $database->connect();
$appointment = new Appointment($db);
$service = new Service($db);

// Check if service ID is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $serv_id = $_GET['id'];

    // Fetch the service details
    $service_details = $service->findId($serv_id);

    if ($service_details) {
        // Fetch appointments for the given service ID
        $appointments = $service->getAppointmentsByService($serv_id);
    } else {
        echo "<script>alert('Service not found.'); window.location='../publlic/service.php';</script>";
        exit;
    }
} else {
        
        exit;
}
?>

<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Appointments for Service: <?= htmlspecialchars($service_details['serv_name']) ?></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="service.php">Service Management</a></li>
            <li class="breadcrumb-item active">Appointments</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header bg-light">
                <i class="fas fa-calendar-alt me-1"></i> Appointment List
            </div>
            <div class="card-body">
                <?php if (!empty($appointments)): ?>
                    <table id="datatablesSimple" class="table table-bordered table-hover align-middle">
                        <thead class="table-primary text-center">
                        <tr>
                            <th>Reference ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?= htmlspecialchars($appointment['appt_id']) ?></td>
                                <td><?= htmlspecialchars($appointment['appt_date']) ?></td>
                                <td><?= htmlspecialchars($appointment['appt_time']) ?></td>
                                <td><?= htmlspecialchars($appointment['pat_first_name'] . ' ' . $appointment['pat_last_name']) ?></td>
                                <td><?= htmlspecialchars($appointment['doc_first_name'] . ' ' . $appointment['doc_last_name']) ?></td>
                                <td><?= htmlspecialchars($appointment['stat_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center text-muted">No appointments found for this service.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>
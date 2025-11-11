<?php
require_once "../class/service.php";
require_once "../config/db.php";
require_once "../class/doctor.php";
require_once "../class/payment_status.php";
$database = new Database();
$db = $database->connect();

//Service
$service = new Service($db);

$doctor = new Doctor($db);

    // $result = $doctor->viewPreviousAppointments(1);
    // echo "<pre>";
    //  print_r($result);
    // echo "<pre>";

//payment_status
$paymentStatus = new payment_status($db);

$result = $paymentStatus->all();
  echo "<pre>";
     print_r($result);
 echo "<pre>";
?>
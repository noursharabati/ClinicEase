<?php
include 'db.php';

$errorMsg   = "";
$successMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_name     = trim($_POST['patient_name'] ?? '');
    $phone            = trim($_POST['phone'] ?? '');
    $doctor           = trim($_POST['doctor'] ?? '');
    $appointment_date = trim($_POST['appointment_date'] ?? '');
    $appointment_time = trim($_POST['appointment_time'] ?? '');

    $allowed_doctors = ["Dr. Smith", "Dr. Jane", "Dr. Mike"];

    if (empty($patient_name) || empty($phone) || empty($doctor) || empty($appointment_date) || empty($appointment_time)) {
        $errorMsg = "Please fill in all fields!";
    } elseif (!in_array($doctor, $allowed_doctors)) {
        $errorMsg = "Invalid doctor selected.";
    } elseif (!preg_match('/^\d{7,15}$/', $phone)) {
        $errorMsg = "Please enter a valid phone number (digits only, 7–15 digits).";
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO appointments (patient_name, phone, doctor, appointment_date, appointment_time)
             VALUES (:name, :phone, :doctor, :date, :time)"
        );
        $stmt->execute([
            ':name'   => $patient_name,
            ':phone'  => $phone,
            ':doctor' => $doctor,
            ':date'   => $appointment_date,
            ':time'   => $appointment_time,
        ]);
        $successMsg = "Appointment booked successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - ClinicEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">ClinicEase</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="book.php">Book Appointment</a></li>
        <li class="nav-item"><a class="nav-link" href="view.php">View Appointments</a></li>
        <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center mb-4">Book an Appointment</h2>

    <?php if ($errorMsg)   echo "<div class='alert alert-danger'>" . htmlspecialchars($errorMsg) . "</div>"; ?>
    <?php if ($successMsg) echo "<div class='alert alert-success'>" . htmlspecialchars($successMsg) . "</div>"; ?>

    <form method="POST" class="mx-auto" style="max-width: 500px;">
        <div class="mb-3">
            <label class="form-label">Patient Name</label>
            <input type="text" name="patient_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="tel" name="phone" class="form-control" pattern="\d{7,15}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Doctor</label>
            <select name="doctor" class="form-select" required>
                <option value="">-- Select Doctor --</option>
                <option value="Dr. Smith">Dr. Smith – General Checkup</option>
                <option value="Dr. Jane">Dr. Jane – Pediatrics</option>
                <option value="Dr. Mike">Dr. Mike – Dental Care</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="appointment_date" class="form-control"
                   min="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Time</label>
            <input type="time" name="appointment_time" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Book Appointment</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

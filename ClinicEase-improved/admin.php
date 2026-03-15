<?php
include 'db.php';

// Delete Appointment
if (isset($_GET['delete_id'])) {
    $id   = (int) $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: admin.php");
    exit;
}

// Update Appointment
$successMsg = "";
if (isset($_POST['edit_id'])) {
    $allowed_doctors = ["Dr. Smith", "Dr. Jane", "Dr. Mike"];
    $id     = (int) $_POST['edit_id'];
    $name   = trim($_POST['patient_name'] ?? '');
    $phone  = trim($_POST['phone'] ?? '');
    $doctor = trim($_POST['doctor'] ?? '');
    $date   = trim($_POST['appointment_date'] ?? '');
    $time   = trim($_POST['appointment_time'] ?? '');

    if (in_array($doctor, $allowed_doctors) && !empty($name) && !empty($phone)) {
        $stmt = $pdo->prepare(
            "UPDATE appointments
             SET patient_name=:name, phone=:phone, doctor=:doctor,
                 appointment_date=:date, appointment_time=:time
             WHERE id=:id"
        );
        $stmt->execute([
            ':name'   => $name,
            ':phone'  => $phone,
            ':doctor' => $doctor,
            ':date'   => $date,
            ':time'   => $time,
            ':id'     => $id,
        ]);
        $successMsg = "Appointment updated successfully!";
    } else {
        $successMsg = "Error: Invalid data provided.";
    }
}

// Dashboard stats
$totalAppointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$todayAppointments = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE()");
$todayAppointments->execute();
$todayAppointments = $todayAppointments->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – ClinicEase</title>
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
        <li class="nav-item"><a class="nav-link" href="book.php">Book Appointment</a></li>
        <li class="nav-item"><a class="nav-link" href="view.php">View Appointments</a></li>
        <li class="nav-item"><a class="nav-link active" href="admin.php">Admin</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center mb-4">Admin – Manage Appointments</h2>
    <?php if ($successMsg) echo "<div class='alert alert-success'>" . htmlspecialchars($successMsg) . "</div>"; ?>

    <!-- Dashboard -->
    <div class="row mb-4 text-center">
        <div class="col-md-6 mb-2">
            <div class="card bg-info text-white p-3 shadow-sm">
                <strong>Total Appointments</strong>
                <span class="fs-3"><?php echo $totalAppointments; ?></span>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <div class="card bg-success text-white p-3 shadow-sm">
                <strong>Today's Appointments</strong>
                <span class="fs-3"><?php echo $todayAppointments; ?></span>
            </div>
        </div>
    </div>

    <table class="table table-striped table-bordered table-hover">
        <thead class="table-primary text-center">
            <tr>
                <th>ID</th><th>Patient Name</th><th>Phone</th>
                <th>Doctor</th><th>Date</th><th>Time</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $rows = $pdo->query("SELECT * FROM appointments ORDER BY appointment_date, appointment_time")->fetchAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) { ?>
                <tr class="text-center">
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['patient_name']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['doctor']) ?></td>
                    <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                    <td><?= htmlspecialchars($row['appointment_time']) ?></td>
                    <td>
                        <a href="#" class="btn btn-warning btn-sm"
                           data-bs-toggle="modal"
                           data-bs-target="#editModal<?= $row['id'] ?>">Edit</a>
                        <a href="admin.php?delete_id=<?= $row['id'] ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirmDelete(event)">Delete</a>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="POST">
                        <div class="modal-header">
                          <h5 class="modal-title">Edit Appointment</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="edit_id" value="<?= $row['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Patient Name</label>
                                <input type="text" name="patient_name" class="form-control"
                                       value="<?= htmlspecialchars($row['patient_name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control"
                                       value="<?= htmlspecialchars($row['phone']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Doctor</label>
                                <select name="doctor" class="form-select" required>
                                    <?php foreach (["Dr. Smith", "Dr. Jane", "Dr. Mike"] as $d): ?>
                                    <option <?= $row['doctor'] === $d ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($d) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="appointment_date" class="form-control"
                                       value="<?= htmlspecialchars($row['appointment_date']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Time</label>
                                <input type="time" name="appointment_time" class="form-control"
                                       value="<?= htmlspecialchars($row['appointment_time']) ?>" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
            <?php }
        } else {
            echo "<tr><td colspan='7' class='text-center'>No appointments found</td></tr>";
        } ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(e) {
    e.preventDefault();
    const link = e.currentTarget.href;
    Swal.fire({
        title: 'Are you sure?',
        text: "This appointment will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) window.location.href = link;
    });
}
</script>
</body>
</html>

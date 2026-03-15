<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments – ClinicEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bg-morning   { background-color: #fff3cd !important; color: #856404; }
        .bg-afternoon { background-color: #5dade2 !important; color: white; }
        .bg-evening   { background-color: #9b59b6 !important; color: white; }
    </style>
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
        <li class="nav-item"><a class="nav-link active" href="view.php">View Appointments</a></li>
        <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <h2 class="mb-4 text-center">All Appointments</h2>

    <input type="text" id="search" class="form-control mb-3"
           placeholder="🔍 Search by patient name or doctor…">

    <?php if (isset($_GET['rated'])): ?>
    <div class="alert alert-success text-center">⭐ Doctor rated successfully!</div>
    <?php endif; ?>

    <!-- Legend -->
    <div class="d-flex gap-3 mb-3 flex-wrap">
        <span class="badge bg-warning text-dark p-2">🌅 Morning (06:00–11:59)</span>
        <span class="badge p-2" style="background:#5dade2">🌤 Afternoon (12:00–17:59)</span>
        <span class="badge p-2" style="background:#9b59b6">🌙 Evening (18:00–21:59)</span>
    </div>

    <table class="table table-striped table-bordered table-hover">
        <thead class="table-primary text-center">
            <tr>
                <th>#</th><th>Patient Name</th><th>Phone</th>
                <th>Doctor</th><th>Date</th><th>Time</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $rows    = $pdo->query("SELECT * FROM appointments ORDER BY appointment_date, appointment_time")->fetchAll();
        $counter = 1;
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $hour      = (int) explode(":", $row['appointment_time'])[0];
                $timeClass = '';
                if ($hour >= 6  && $hour < 12) $timeClass = 'bg-morning';
                elseif ($hour >= 12 && $hour < 18) $timeClass = 'bg-afternoon';
                elseif ($hour >= 18 && $hour < 22) $timeClass = 'bg-evening';
                echo "<tr class='text-center'>
                        <td>{$counter}</td>
                        <td>" . htmlspecialchars($row['patient_name']) . "</td>
                        <td>" . htmlspecialchars($row['phone']) . "</td>
                        <td>" . htmlspecialchars($row['doctor']) . "</td>
                        <td>" . htmlspecialchars($row['appointment_date']) . "</td>
                        <td class='{$timeClass}'>" . htmlspecialchars($row['appointment_time']) . "</td>
                      </tr>";
                $counter++;
            }
        } else {
            echo "<tr><td colspan='6' class='text-center'>No appointments found</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('search').addEventListener('keyup', function () {
    const filter = this.value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
    });
});
</script>
</body>
</html>

<?php
include 'db.php';

$successMsg = "";

// Handle rating submission via modal (AJAX or POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'], $_POST['doctor'])) {
    $allowed_doctors = ["Dr. Smith", "Dr. Jane", "Dr. Mike"];
    $doctor = trim($_POST['doctor']);
    $rating = (int) $_POST['rating'];

    if (in_array($doctor, $allowed_doctors) && $rating >= 1 && $rating <= 5) {
        $stmt = $pdo->prepare("INSERT INTO doctor_ratings (doctor, rating) VALUES (:doctor, :rating)");
        $stmt->execute([':doctor' => $doctor, ':rating' => $rating]);
        $successMsg = "⭐ Thank you! Your rating for " . htmlspecialchars($doctor) . " has been submitted.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClinicEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .star {
            font-size: 40px;
            cursor: pointer;
            color: #ccc;
            transition: color 0.15s, transform 0.1s;
            display: inline-block;
        }
        .star:hover { transform: scale(1.2); }
        .star.selected, .star.hovered { color: gold; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">ClinicEase</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="book.php">Book Appointment</a></li>
        <li class="nav-item"><a class="nav-link" href="view.php">View Appointments</a></li>
        <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<div class="bg-primary text-white text-center py-5">
    <h1 class="display-4">Welcome to ClinicEase</h1>
    <p class="lead">Book your clinic appointment easily!</p>
    <div class="mt-4">
        <a href="book.php" class="btn btn-light btn-lg">Book Appointment</a>
    </div>
</div>

<!-- Success Toast -->
<?php if ($successMsg): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index:9999">
  <div class="toast show align-items-center text-bg-success border-0 shadow" role="alert">
    <div class="d-flex">
      <div class="toast-body fs-6"><?= $successMsg ?></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Services / Doctors Section -->
<div class="container my-5">
    <h2 class="text-center mb-4">Our Services</h2>
    <div class="row">

        <?php
        $services = [
            ["title" => "General Checkup",  "desc" => "Routine health examinations for all ages.",      "doctor" => "Dr. Smith", "icon" => "🩺"],
            ["title" => "Pediatrics",        "desc" => "Healthcare for children with specialized doctors.", "doctor" => "Dr. Jane",  "icon" => "👶"],
            ["title" => "Dental Care",       "desc" => "Professional dental checkups and treatments.",   "doctor" => "Dr. Mike",  "icon" => "🦷"],
        ];
        foreach ($services as $s):
            $docId = str_replace([" ", "."], "", $s['doctor']); // e.g. DrSmith
        ?>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="display-4 mb-2"><?= $s['icon'] ?></div>
                        <h5 class="card-title"><?= htmlspecialchars($s['title']) ?></h5>
                        <p class="card-text text-muted"><?= htmlspecialchars($s['desc']) ?></p>
                        <p class="fw-semibold"><?= htmlspecialchars($s['doctor']) ?></p>
                    </div>
                    <!-- Trigger Rating Modal -->
                    <button class="btn btn-outline-primary mt-2"
                            onclick="openRateModal('<?= htmlspecialchars($s['doctor']) ?>', '<?= $docId ?>')">
                        ⭐ Rate Doctor
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</div>

<!-- ===== Rating Modal ===== -->
<div class="modal fade" id="rateModal" tabindex="-1" aria-labelledby="rateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="rateForm">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="rateModalLabel">⭐ Rate Doctor</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center py-4">
          <p class="fs-5 mb-1">How would you rate</p>
          <p class="fs-4 fw-bold mb-4" id="modalDoctorName"></p>

          <!-- Stars -->
          <div id="starContainer" class="mb-3">
            <span class="star" data-val="1">★</span>
            <span class="star" data-val="2">★</span>
            <span class="star" data-val="3">★</span>
            <span class="star" data-val="4">★</span>
            <span class="star" data-val="5">★</span>
          </div>
          <p class="text-muted" id="starLabel">Click a star to rate</p>

          <input type="hidden" name="rating" id="ratingInput">
          <input type="hidden" name="doctor" id="doctorInput">
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary px-4" id="submitRateBtn" disabled>Submit Rating</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const labels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
const modal  = new bootstrap.Modal(document.getElementById('rateModal'));
const stars  = document.querySelectorAll('.star[data-val]');

function openRateModal(doctor, docId) {
    document.getElementById('modalDoctorName').textContent = doctor;
    document.getElementById('doctorInput').value           = doctor;
    document.getElementById('ratingInput').value           = '';
    document.getElementById('submitRateBtn').disabled      = true;
    document.getElementById('starLabel').textContent       = 'Click a star to rate';
    stars.forEach(s => s.classList.remove('selected', 'hovered'));
    modal.show();
}

// Hover effect
stars.forEach(star => {
    star.addEventListener('mouseenter', () => {
        const val = +star.dataset.val;
        stars.forEach(s => s.classList.toggle('hovered', +s.dataset.val <= val));
    });
    star.addEventListener('mouseleave', () => {
        stars.forEach(s => s.classList.remove('hovered'));
    });
    star.addEventListener('click', () => {
        const val = +star.dataset.val;
        document.getElementById('ratingInput').value      = val;
        document.getElementById('submitRateBtn').disabled = false;
        document.getElementById('starLabel').textContent  = labels[val];
        stars.forEach(s => s.classList.toggle('selected', +s.dataset.val <= val));
    });
});
</script>
</body>
</html>

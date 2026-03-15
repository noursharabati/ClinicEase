<?php
include 'db.php';

$allowed_doctors = ["Dr. Smith", "Dr. Jane", "Dr. Mike"];

if (!isset($_GET['doctor']) || !in_array($_GET['doctor'], $allowed_doctors)) {
    header("Location: index.php");
    exit;
}

$doctor = $_GET['doctor'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = (int) ($_POST['rating'] ?? 0);
    if ($rating >= 1 && $rating <= 5) {
        $stmt = $pdo->prepare("INSERT INTO doctor_ratings (doctor, rating) VALUES (:doctor, :rating)");
        $stmt->execute([':doctor' => $doctor, ':rating' => $rating]);
    }
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Doctor – ClinicEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .star { font-size: 50px; cursor: pointer; color: #ccc; transition: color 0.2s; }
        .star:hover, .star.selected { color: gold; }
    </style>
</head>
<body class="text-center mt-5">
<div class="container" style="max-width:400px;">
    <h2 class="mb-4">Rate <?= htmlspecialchars($doctor) ?></h2>
    <form method="POST">
        <div class="mb-3">
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <span class="star" onclick="rate(<?= $i ?>)">★</span>
            <?php endfor; ?>
        </div>
        <input type="hidden" name="rating" id="rating">
        <button type="submit" class="btn btn-primary btn-lg w-100"
                id="submitBtn" disabled>Submit Rating</button>
    </form>
    <a href="index.php" class="btn btn-link mt-2">← Back to Home</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function rate(stars) {
    document.getElementById('rating').value = stars;
    document.getElementById('submitBtn').disabled = false;
    document.querySelectorAll('.star').forEach((el, i) => {
        el.classList.toggle('selected', i < stars);
    });
}
</script>
</body>
</html>

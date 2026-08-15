<?php
require 'db.php';

$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';
$guests = (int)($_GET['guests'] ?? 1);

$sql = "SELECT * FROM rooms WHERE status='available' AND capacity >= $guests ORDER BY price ASC";
$result = $conn->query($sql);
$rooms = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Search Results | Kaizer B&B</title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <div class="container" style="padding:40px 0;">
    <h2>Available Rooms</h2>
    <p>Search for <?= (int)$guests ?> guest(s) from <?= htmlspecialchars($check_in) ?> to <?= htmlspecialchars($check_out) ?>.</p>
    <div class="cards">
      <?php foreach ($rooms as $room): ?>
        <article class="card">
          <img src="<?= htmlspecialchars($room['image']) ?>" alt="<?= htmlspecialchars($room['room_name']) ?>" />
          <div class="card-body">
            <h4><?= htmlspecialchars($room['room_name']) ?></h4>
            <p><?= htmlspecialchars($room['description']) ?></p>
            <div class="price-row">
              <strong>R <?= number_format((float)$room['price'], 2) ?></strong>
              <a class="btn btn-small" href="booking.php?room_id=<?= (int)$room['id'] ?>">Book</a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>

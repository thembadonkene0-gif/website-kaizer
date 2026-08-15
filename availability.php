<?php
require 'db.php';

$month = (int)($_GET['month'] ?? date('n'));
$year = (int)($_GET['year'] ?? date('Y'));
$room_id = (int)($_GET['room_id'] ?? 0);

$rooms = $conn->query('SELECT id, room_name, price FROM rooms WHERE status = "available" ORDER BY room_name ASC')->fetch_all(MYSQLI_ASSOC);

if ($room_id === 0 && $rooms) {
    $room_id = (int)$rooms[0]['id'];
}

$first_day = mktime(0, 0, 0, $month, 1, $year);
$days_in_month = date('t', $first_day);
$start_weekday = date('w', $first_day);

$bookings = [];
if ($room_id) {
    $stmt = $conn->prepare('SELECT check_in, check_out FROM bookings WHERE room_id = ? AND status != "cancelled"');
    $stmt->bind_param('i', $room_id);
    $stmt->execute();
    $bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function is_booked($day, $month, $year, $bookings) {
    $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
    foreach ($bookings as $booking) {
        if ($date >= $booking['check_in'] && $date <= $booking['check_out']) {
            return true;
        }
    }
    return false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Availability Calendar | Kaizer B&B</title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <div class="container" style="padding:40px 0;">
    <div class="panel">
      <div class="topbar-admin">
        <div>
          <p class="eyebrow">AVAILABILITY</p>
          <h2>Room Calendar</h2>
        </div>
        <form method="GET" class="inline-form">
          <select name="room_id" onchange="this.form.submit()">
            <?php foreach ($rooms as $room): ?>
              <option value="<?= (int)$room['id'] ?>" <?= $room_id === (int)$room['id'] ? 'selected' : '' ?>><?= htmlspecialchars($room['room_name']) ?></option>
            <?php endforeach; ?>
          </select>
          <input type="hidden" name="month" value="<?= (int)$month ?>" />
          <input type="hidden" name="year" value="<?= (int)$year ?>" />
        </form>
      </div>

      <div class="calendar-nav">
        <a class="btn btn-small" href="availability.php?room_id=<?= (int)$room_id ?>&month=<?= $month == 1 ? 12 : $month - 1 ?>&year=<?= $month == 1 ? $year - 1 : $year ?>">Previous</a>
        <strong><?= date('F Y', $first_day) ?></strong>
        <a class="btn btn-small" href="availability.php?room_id=<?= (int)$room_id ?>&month=<?= $month == 12 ? 1 : $month + 1 ?>&year=<?= $month == 12 ? $year + 1 : $year ?>">Next</a>
      </div>

      <div class="calendar-grid">
        <?php for ($i = 0; $i < 7; $i++): ?>
          <div class="calendar-head"><?= date('D', strtotime('Sunday +' . $i . ' days')) ?></div>
        <?php endfor; ?>
        <?php for ($i = 0; $i < $start_weekday; $i++): ?><div class="calendar-cell muted-cell"></div><?php endfor; ?>
        <?php for ($day = 1; $day <= $days_in_month; $day++): ?>
          <?php $booked = is_booked($day, $month, $year, $bookings); ?>
          <div class="calendar-cell <?= $booked ? 'booked' : 'free' ?>">
            <strong><?= $day ?></strong>
            <span><?= $booked ? 'Booked' : 'Open' ?></span>
          </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>
</body>
</html>

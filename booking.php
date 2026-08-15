<?php
require 'db.php';
$room_id = (int)($_GET['room_id'] ?? 0);
$room = $conn->query("SELECT * FROM rooms WHERE id=$room_id")->fetch_assoc();
if (!$room) {
    die('Room not found.');
}

$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $check_in = trim($_POST['check_in'] ?? '');
    $check_out = trim($_POST['check_out'] ?? '');
    $guests = (int)($_POST['guests'] ?? 1);

    if ($full_name === '') { $errors[] = 'Full name is required.'; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Please enter a valid email address.'; }
    if ($phone === '') { $errors[] = 'Phone number is required.'; }
    if ($check_in === '' || $check_out === '') { $errors[] = 'Check-in and check-out dates are required.'; }
    if ($check_in >= $check_out) { $errors[] = 'Check-out must be after check-in.'; }
    if ($guests < 1 || $guests > (int)$room['capacity']) { $errors[] = 'Guest count exceeds the room capacity.'; }

    if (!$errors) {
        $stmt = $conn->prepare('INSERT INTO customers (full_name, email, phone, address) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $full_name, $email, $phone, $address);
        $stmt->execute();
        $customer_id = $stmt->insert_id;

        $total_amount = (float)$room['price'];
        $stmt2 = $conn->prepare('INSERT INTO bookings (customer_id, room_id, check_in, check_out, guests, total_amount, status, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt2->bind_param('iisssdss', $customer_id, $room_id, $check_in, $check_out, $guests, $total_amount, $status, $payment_status);
        $status = 'pending';
        $payment_status = 'unpaid';
        $stmt2->execute();
        $booking_id = $stmt2->insert_id;

        $success = 'Reservation created. Continue to checkout.';
        header('Location: checkout.php?booking_id=' . (int)$booking_id);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Book Room | Kaizer B&B</title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <div class="container" style="padding:40px 0;">
    <div class="panel" style="max-width:700px; margin:0 auto;">
      <h2>Book <?= htmlspecialchars($room['room_name']) ?></h2>
      <p>Starting from R <?= number_format((float)$room['price'], 2) ?> per night.</p>
      <?php if ($errors): ?>
        <div class="error-banner">
          <?php foreach ($errors as $error): ?><div><?= htmlspecialchars($error) ?></div><?php endforeach; ?>
        </div>
      <?php endif; ?>
      <form method="POST" class="form-grid">
        <div><label>Full Name</label><input name="full_name" required /></div>
        <div><label>Email</label><input type="email" name="email" required /></div>
        <div><label>Phone</label><input name="phone" required /></div>
        <div><label>Guests</label><input type="number" name="guests" value="2" min="1" max="<?= (int)$room['capacity'] ?>" /></div>
        <div><label>Check-in</label><input type="date" name="check_in" required /></div>
        <div><label>Check-out</label><input type="date" name="check_out" required /></div>
        <div style="grid-column:1 / -1;"><label>Address</label><textarea name="address" rows="3"></textarea></div>
        <div style="grid-column:1 / -1;"><button class="btn btn-primary" type="submit">Continue to Checkout</button></div>
      </form>
    </div>
  </div>
</body>
</html>

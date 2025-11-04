<?php
require_once 'db.php';

// Get hotel and booking details
$hotel_id = $_GET['hotel_id'] ?? null;
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$nights = $_GET['nights'] ?? 1;

if (!$hotel_id) {
    die("Hotel ID is required");
}

// Fetch hotel details
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$hotel_id]);
$hotel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hotel) {
    die("Hotel not found");
}

// Calculate total price
$totalPrice = $hotel['price_per_night'] * $nights;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guest_name = $_POST['guest_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $room_type = $_POST['room_type'] ?? 'Standard';
    
    if (empty($guest_name) || empty($email)) {
        $error = "Please fill in all required fields";
    } else {
        // Insert booking into database
        $stmt = $pdo->prepare("INSERT INTO bookings (hotel_id, guest_name, email, check_in, check_out, room_type, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$hotel_id, $guest_name, $email, $checkin, $checkout, $room_type, $totalPrice]);
        
        // Redirect to confirmation page
        header("Location: confirmation.php?booking_id=" . $pdo->lastInsertId());
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Stay - Hilton Hotels</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
        }
        
        header {
            background: linear-gradient(135deg, #005792 0%, #0077be 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
        }
        
        .logo span {
            color: #ffd700;
        }
        
        .booking-container {
            display: flex;
            gap: 2rem;
            margin: 2rem 0;
        }
        
        .hotel-details {
            flex: 1;
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .booking-form {
            flex: 1;
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .hotel-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .hotel-name {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            color: #005792;
        }
        
        .hotel-location {
            color: #666;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .rating {
            color: #ffd700;
            margin-right: 0.5rem;
        }
        
        .booking-dates {
            background: #e9f7fe;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .date-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .price-summary {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1.5rem;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .total-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #005792;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #555;
        }
        
        input, select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .book-btn {
            background: #005792;
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
            font-weight: 600;
            width: 100%;
            transition: background 0.3s;
        }
        
        .book-btn:hover {
            background: #003d66;
        }
        
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        @media (max-width: 768px) {
            .booking-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">Hilton<span>.</span></div>
        </div>
    </header>
    
    <main class="container">
        <h1>Book Your Stay</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="booking-container">
            <div class="hotel-details">
                <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>" class="hotel-image">
                <h2 class="hotel-name"><?php echo htmlspecialchars($hotel['name']); ?></h2>
                <div class="hotel-location">
                    <span class="rating">★ <?php echo $hotel['rating']; ?></span>
                    <?php echo htmlspecialchars($hotel['location']); ?>
                </div>
                <p><?php echo htmlspecialchars($hotel['description']); ?></p>
                
                <div class="booking-dates">
                    <h3>Your Stay</h3>
                    <div class="date-item">
                        <span>Check-in:</span>
                        <strong><?php echo htmlspecialchars($checkin); ?></strong>
                    </div>
                    <div class="date-item">
                        <span>Check-out:</span>
                        <strong><?php echo htmlspecialchars($checkout); ?></strong>
                    </div>
                    <div class="date-item">
                        <span>Nights:</span>
                        <strong><?php echo $nights; ?></strong>
                    </div>
                </div>
                
                <div class="price-summary">
                    <div class="price-row">
                        <span>Price per night:</span>
                        <strong>$<?php echo number_format($hotel['price_per_night'], 2); ?></strong>
                    </div>
                    <div class="price-row">
                        <span>Nights:</span>
                        <strong><?php echo $nights; ?></strong>
                    </div>
                    <div class="total-price">
                        <span>Total:</span>
                        <strong>$<?php echo number_format($totalPrice, 2); ?></strong>
                    </div>
                </div>
            </div>
            
            <div class="booking-form">
                <h2>Guest Details</h2>
                <form method="POST">
                    <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
                    <input type="hidden" name="checkin" value="<?php echo $checkin; ?>">
                    <input type="hidden" name="checkout" value="<?php echo $checkout; ?>">
                    <input type="hidden" name="nights" value="<?php echo $nights; ?>">
                    <input type="hidden" name="total_price" value="<?php echo $totalPrice; ?>">
                    
                    <div class="form-group">
                        <label for="guest_name">Full Name *</label>
                        <input type="text" id="guest_name" name="guest_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="room_type">Room Type</label>
                        <select id="room_type" name="room_type">
                            <option value="Standard">Standard Room</option>
                            <option value="Deluxe">Deluxe Room</option>
                            <option value="Suite">Suite</option>
                            <option value="Executive">Executive Suite</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="book-btn">Confirm Booking</button>
                </form>
            </div>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; 2023 Hilton Hotels. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

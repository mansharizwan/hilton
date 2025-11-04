<?php
require_once 'db.php';

// Get booking ID
$booking_id = $_GET['booking_id'] ?? null;

if (!$booking_id) {
    die("Booking ID is required");
}

// Fetch booking details
$stmt = $pdo->prepare("SELECT b.*, h.name as hotel_name, h.location as hotel_location FROM bookings b JOIN hotels h ON b.hotel_id = h.id WHERE b.id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die("Booking not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - Hilton Hotels</title>
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
            max-width: 800px;
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
        
        .confirmation-container {
            background: white;
            border-radius: 12px;
            padding: 3rem;
            margin: 3rem auto;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .confirmation-icon {
            width: 100px;
            height: 100px;
            background: #e8f5e9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
        }
        
        .confirmation-icon svg {
            width: 60px;
            height: 60px;
            fill: #4caf50;
        }
        
        .confirmation-title {
            font-size: 2.2rem;
            color: #005792;
            margin-bottom: 1rem;
        }
        
        .confirmation-message {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: #555;
        }
        
        .booking-details {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 12px;
            margin: 2rem 0;
            text-align: left;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #555;
        }
        
        .detail-value {
            color: #333;
        }
        
        .back-home {
            display: inline-block;
            background: #005792;
            color: white;
            text-decoration: none;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            margin-top: 2rem;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        .back-home:hover {
            background: #003d66;
        }
        
        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
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
        <div class="confirmation-container">
            <div class="confirmation-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
            </div>
            
            <h1 class="confirmation-title">Booking Confirmed!</h1>
            <p class="confirmation-message">Thank you for choosing Hilton Hotels. Your reservation has been successfully booked.</p>
            
            <div class="booking-details">
                <div class="detail-row">
                    <span class="detail-label">Booking ID:</span>
                    <span class="detail-value"><?php echo $booking['id']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Hotel:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['hotel_name']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Location:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['hotel_location']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Guest Name:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['guest_name']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Check-in:</span>
                    <span class="detail-value"><?php echo $booking['check_in']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Check-out:</span>
                    <span class="detail-value"><?php echo $booking['check_out']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Room Type:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['room_type']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Price:</span>
                    <span class="detail-value">$<?php echo number_format($booking['total_price'], 2); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Booking Date:</span>
                    <span class="detail-value"><?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></span>
                </div>
            </div>
            
            <a href="index.php" class="back-home">Back to Homepage</a>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; 2023 Hilton Hotels. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

<?php
require_once 'db.php';

// Fetch featured hotels (top 3 by rating)
$stmt = $pdo->query("SELECT * FROM hotels ORDER BY rating DESC LIMIT 3");
$featuredHotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hilton Hotels - Find Your Perfect Stay</title>
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
        
        .search-container {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 900px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .search-title {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #005792;
            font-size: 1.8rem;
        }
        
        .search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
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
        
        .search-btn {
            background: #005792;
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            transition: background 0.3s;
            margin-top: 1.8rem;
        }
        
        .search-btn:hover {
            background: #003d66;
        }
        
        .section-title {
            text-align: center;
            margin: 3rem 0 2rem;
            color: #005792;
            font-size: 2rem;
        }
        
        .hotels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .hotel-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .hotel-card:hover {
            transform: translateY(-5px);
        }
        
        .hotel-image {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }
        
        .hotel-info {
            padding: 1.5rem;
        }
        
        .hotel-name {
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
            color: #005792;
        }
        
        .hotel-location {
            color: #666;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .rating {
            color: #ffd700;
            margin-right: 0.5rem;
        }
        
        .hotel-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #005792;
            margin-top: 1rem;
        }
        
        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
            }
            
            .search-btn {
                margin-top: 0;
                width: 100%;
            }
            
            .hotels-grid {
                grid-template-columns: 1fr;
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
    
    <main>
        <div class="container">
            <div class="search-container">
                <h1 class="search-title">Find Your Perfect Stay</h1>
                <form action="search.php" method="GET" class="search-form">
                    <div class="form-group">
                        <label for="destination">Destination</label>
                        <input type="text" id="destination" name="destination" placeholder="Where are you going?" required>
                    </div>
                    <div class="form-group">
                        <label for="checkin">Check-in</label>
                        <input type="date" id="checkin" name="checkin" required>
                    </div>
                    <div class="form-group">
                        <label for="checkout">Check-out</label>
                        <input type="date" id="checkout" name="checkout" required>
                    </div>
                    <button type="submit" class="search-btn">Search Hotels</button>
                </form>
            </div>
            
            <h2 class="section-title">Featured Hotels</h2>
            <div class="hotels-grid">
                <?php foreach ($featuredHotels as $hotel): ?>
                <div class="hotel-card">
                    <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>" class="hotel-image">
                    <div class="hotel-info">
                        <h3 class="hotel-name"><?php echo htmlspecialchars($hotel['name']); ?></h3>
                        <div class="hotel-location">
                            <span class="rating">★ <?php echo $hotel['rating']; ?></span>
                            <?php echo htmlspecialchars($hotel['location']); ?>
                        </div>
                        <p><?php echo substr(htmlspecialchars($hotel['description']), 0, 100); ?>...</p>
                        <div class="hotel-price">$<?php echo number_format($hotel['price_per_night'], 2); ?> per night</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; 2023 Hilton Hotels. All rights reserved.</p>
        </div>
    </footer>
    
    <script>
        // Set min date for check-in to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('checkin').min = today;
        document.getElementById('checkout').min = today;
        
        // Set check-out min date to day after check-in
        document.getElementById('checkin').addEventListener('change', function() {
            const checkinDate = new Date(this.value);
            const checkoutDate = new Date(checkinDate);
            checkoutDate.setDate(checkoutDate.getDate() + 1);
            document.getElementById('checkout').min = checkoutDate.toISOString().split('T')[0];
        });
    </script>
</body>
</html>

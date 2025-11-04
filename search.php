<?php
require_once 'db.php';

// Get search parameters
$destination = $_GET['destination'] ?? '';
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$sort = $_GET['sort'] ?? 'default';
$minPrice = $_GET['min_price'] ?? 0;
$maxPrice = $_GET['max_price'] ?? 10000;
$rating = $_GET['rating'] ?? 0;
$amenities = $_GET['amenities'] ?? [];

// Build SQL query
$sql = "SELECT * FROM hotels WHERE 1=1";
$params = [];

// Add destination filter
if (!empty($destination)) {
    $sql .= " AND (name LIKE :destination OR location LIKE :destination)";
    $params[':destination'] = "%$destination%";
}

// Add price filter
if ($minPrice > 0 || $maxPrice < 10000) {
    $sql .= " AND price_per_night BETWEEN :min_price AND :max_price";
    $params[':min_price'] = $minPrice;
    $params[':max_price'] = $maxPrice;
}

// Add rating filter
if ($rating > 0) {
    $sql .= " AND rating >= :rating";
    $params[':rating'] = $rating;
}

// Add sorting
switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY price_per_night ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY price_per_night DESC";
        break;
    case 'rating':
        $sql .= " ORDER BY rating DESC";
        break;
    default:
        $sql .= " ORDER BY id ASC";
}

// Execute query
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate number of nights
$nights = 1;
if (!empty($checkin) && !empty($checkout)) {
    $checkinDate = new DateTime($checkin);
    $checkoutDate = new DateTime($checkout);
    $interval = $checkinDate->diff($checkoutDate);
    $nights = $interval->days;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Hilton Hotels</title>
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
        
        .search-summary {
            background: white;
            padding: 1.5rem;
            margin: 2rem 0;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .filters {
            background: white;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .filter-title {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: #005792;
        }
        
        .filter-group {
            margin-bottom: 1.5rem;
        }
        
        .filter-options {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .filter-option {
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
        
        .apply-filters {
            background: #005792;
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            transition: background 0.3s;
            margin-top: 1rem;
        }
        
        .apply-filters:hover {
            background: #003d66;
        }
        
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .results-count {
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .sort-by {
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
        
        .amenities {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 1rem 0;
        }
        
        .amenity {
            background: #e9f7fe;
            color: #005792;
            padding: 0.3rem 0.6rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        
        .hotel-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #005792;
            margin: 1rem 0;
        }
        
        .book-btn {
            display: block;
            width: 100%;
            background: #005792;
            color: white;
            border: none;
            padding: 0.8rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            transition: background 0.3s;
            text-decoration: none;
            text-align: center;
        }
        
        .book-btn:hover {
            background: #003d66;
        }
        
        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        @media (max-width: 768px) {
            .results-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .filter-options {
                flex-direction: column;
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
    
    <main class="container">
        <div class="search-summary">
            <h2>Search Results</h2>
            <p>Stays in <strong><?php echo htmlspecialchars($destination); ?></strong> from 
                <strong><?php echo htmlspecialchars($checkin); ?></strong> to 
                <strong><?php echo htmlspecialchars($checkout); ?></strong> 
                (<?php echo $nights; ?> nights)
            </p>
        </div>
        
        <div class="filters">
            <h3 class="filter-title">Refine Your Search</h3>
            <form method="GET" class="filter-form">
                <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination); ?>">
                <input type="hidden" name="checkin" value="<?php echo htmlspecialchars($checkin); ?>">
                <input type="hidden" name="checkout" value="<?php echo htmlspecialchars($checkout); ?>">
                
                <div class="filter-group">
                    <label for="sort">Sort by</label>
                    <select name="sort" id="sort">
                        <option value="default" <?php echo $sort == 'default' ? 'selected' : ''; ?>>Recommended</option>
                        <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price (Low to High)</option>
                        <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price (High to Low)</option>
                        <option value="rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Top Rated</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Price Range</label>
                    <div class="filter-options">
                        <div class="filter-option">
                            <label for="min_price">Min Price ($)</label>
                            <input type="number" id="min_price" name="min_price" value="<?php echo $minPrice; ?>" min="0">
                        </div>
                        <div class="filter-option">
                            <label for="max_price">Max Price ($)</label>
                            <input type="number" id="max_price" name="max_price" value="<?php echo $maxPrice; ?>" min="0">
                        </div>
                    </div>
                </div>
                
                <div class="filter-group">
                    <label for="rating">Minimum Rating</label>
                    <select name="rating" id="rating">
                        <option value="0">Any Rating</option>
                        <option value="4" <?php echo $rating == 4 ? 'selected' : ''; ?>>4+ Stars</option>
                        <option value="4.5" <?php echo $rating == 4.5 ? 'selected' : ''; ?>>4.5+ Stars</option>
                    </select>
                </div>
                
                <button type="submit" class="apply-filters">Apply Filters</button>
            </form>
        </div>
        
        <div class="results-header">
            <div class="results-count"><?php echo count($hotels); ?> properties found</div>
            <div class="sort-by">
                <label for="sort-mobile">Sort by:</label>
                <select id="sort-mobile" onchange="this.form.submit()">
                    <option value="default" <?php echo $sort == 'default' ? 'selected' : ''; ?>>Recommended</option>
                    <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price (Low to High)</option>
                    <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price (High to Low)</option>
                    <option value="rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Top Rated</option>
                </select>
            </div>
        </div>
        
        <?php if (empty($hotels)): ?>
            <div class="no-results">
                <h3>No hotels found matching your criteria.</h3>
                <p>Try adjusting your search filters.</p>
            </div>
        <?php else: ?>
            <div class="hotels-grid">
                <?php foreach ($hotels as $hotel): ?>
                <div class="hotel-card">
                    <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>" class="hotel-image">
                    <div class="hotel-info">
                        <h3 class="hotel-name"><?php echo htmlspecialchars($hotel['name']); ?></h3>
                        <div class="hotel-location">
                            <span class="rating">★ <?php echo $hotel['rating']; ?></span>
                            <?php echo htmlspecialchars($hotel['location']); ?>
                        </div>
                        <p><?php echo substr(htmlspecialchars($hotel['description']), 0, 120); ?>...</p>
                        
                        <div class="amenities">
                            <?php 
                            $amenityList = explode(', ', $hotel['amenities']);
                            foreach ($amenityList as $amenity): 
                            ?>
                                <span class="amenity"><?php echo htmlspecialchars($amenity); ?></span>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="hotel-price">
                            $<?php echo number_format($hotel['price_per_night'], 2); ?> per night<br>
                            <small>Total: $<?php echo number_format($hotel['price_per_night'] * $nights, 2); ?> for <?php echo $nights; ?> nights</small>
                        </div>
                        
                        <a href="booking.php?hotel_id=<?php echo $hotel['id']; ?>&checkin=<?php echo $checkin; ?>&checkout=<?php echo $checkout; ?>&nights=<?php echo $nights; ?>" class="book-btn">Book Now</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; 2023 Hilton Hotels. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

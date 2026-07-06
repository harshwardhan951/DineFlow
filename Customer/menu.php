<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /DineFlow/login.php");
    exit();
}

include '../db.php';

/* ===== Hotel ID Check ===== */
if (!isset($_GET['hotel_id'])) {
    header("Location: hotels.php");
    exit();
}

$hotel_id = intval($_GET['hotel_id']);

/* ===== Fetch Hotel Name ===== */
$stmt = $conn->prepare("SELECT hotel_name FROM hotels WHERE id = ?");
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();
$hotel = $result->fetch_assoc();
$stmt->close();

if (!$hotel) {
    header("Location: hotels.php");
    exit();
}

/* ===== Fetch Menu Items ===== */
$stmt = $conn->prepare("
    SELECT id, item_name, description, category, price, image 
    FROM menu 
    WHERE hotel_id = ?
");
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$menu = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>DineFlow | <?= htmlspecialchars($hotel['hotel_name']); ?> Menu</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background:#0f172a; color:#e5e7eb;
    margin:0; padding:0;
}
.sidebar {
    width:220px; background:#020617; padding:25px 15px;
    position:fixed; height:100%;
}
.sidebar h2 {color:#38bdf8;text-align:center;margin-bottom:30px;}
.sidebar ul {list-style:none;padding:0;}
.sidebar ul li {margin-bottom:18px;}
.sidebar ul li a {
    display:block;color:#cbd5f5;text-decoration:none;padding:10px 12px;
    border-radius:8px;transition:0.3s;
}
.sidebar ul li a:hover, .sidebar ul li a.active {background:#1e293b;color:#38bdf8;}
.main-content {margin-left:220px;padding:40px;}
header h1 {color:#38bdf8;font-size:2rem;}
header p {color:#94a3b8;margin-top:5px;}
.menu-grid {
    display:grid; 
    grid-template-columns: repeat(auto-fill, minmax(260px, 260px));
    gap:25px; 
    margin-top:30px;
    justify-content: start;
}

.menu-card {
    background:#020617;
    padding:15px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,0.3);
    width:260px;
    transition:0.3s;
}

.menu-card:hover {
    transform: translateY(-5px);
}

.menu-card img {
    width:100%;
    height:170px;
    object-fit:cover;
    border-radius:10px;
}

.menu-info h3 {
    font-size:1rem;
    margin:8px 0 5px;
}

.menu-info p {
    font-size:0.85rem;
    line-height:1.3;
    height:45px;
    overflow:hidden;
}
.menu-info .tag {padding:2px 6px;background:#1e293b;border-radius:4px;font-size:0.7rem;color:#38bdf8;margin-bottom:6px;display:inline-block;}
.price-cart {margin-top:8px; display:flex; align-items:center;}
.price-cart .price {font-weight:600; margin-right:10px;}
.price-cart input[type="number"] {width:50px;padding:4px;border-radius:4px;border:1px solid #ccc;margin-right:8px;}
.price-cart button {padding:6px 12px;border:none;background:#38bdf8;color:#020617;border-radius:6px;cursor:pointer;transition:0.3s;}
.price-cart button:hover {background:#0ea5e9;}
#cart-popup {position:fixed;bottom:20px;right:20px;background:#16a34a;color:#fff;padding:12px 20px;border-radius:8px;display:none;font-weight:600;box-shadow:0 4px 10px rgba(0,0,0,0.3);z-index:9999;}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>DineFlow</h2>
    <ul>
        <li><a href="dashboard.php">🏠 Dashboard</a></li>
        <li><a href="hotels.php">🏨 Hotels</a></li>
        <li><a class="active" href="#">🍽 View Menu</a></li>
        <li><a href="orders.php">📦 My Orders</a></li>
        <li><a href="profile.php">👤 Profile</a></li>
        <li><a href="/DineFlow/logout.php">🔒 Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
<header>
    <h1><?= htmlspecialchars($hotel['hotel_name']); ?> Menu</h1>
    <p>Select dishes and place your order</p>
</header>

<section class="menu-grid">
<?php if ($menu->num_rows > 0): ?>
    <?php while ($row = $menu->fetch_assoc()): ?>
        <div class="menu-card">
            <img src="../uploads/<?= htmlspecialchars($row['image']); ?>" alt="<?= htmlspecialchars($row['item_name']); ?>">
            <div class="menu-info">
                <h3><?= htmlspecialchars($row['item_name']); ?></h3>
                <p><?= htmlspecialchars($row['description']); ?></p>
                <span class="tag"><?= strtoupper($row['category']); ?></span>

                <div class="price-cart">
                    <span class="price" data-base-price="<?= $row['price']; ?>">₹<?= number_format($row['price'], 2); ?></span>
                    <form method="POST" class="add-to-cart-form">
                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                        <input type="hidden" name="name" value="<?= $row['item_name']; ?>">
                        <input type="hidden" name="price" value="<?= $row['price']; ?>">
                        <input type="hidden" name="hotel_id" value="<?= $hotel_id; ?>">
                        <input type="number" name="quantity" value="1" min="1">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
<p style="color:#94a3b8;">No menu items available for this hotel.</p>
<?php endif; ?>
</section>
</div>

<div id="cart-popup"></div>

<script>
// Update price dynamically
document.querySelectorAll('.menu-card').forEach(card => {
    const qtyInput = card.querySelector('input[name="quantity"]');
    const priceSpan = card.querySelector('.price');
    const basePrice = parseFloat(priceSpan.dataset.basePrice);

    qtyInput.addEventListener('input', () => {
        let qty = parseInt(qtyInput.value);
        if (isNaN(qty) || qty < 1) qty = 1;
        priceSpan.textContent = '₹' + (basePrice * qty).toFixed(2);
    });
});

// Add to Cart AJAX
document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(form);

        fetch('add_to_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            const qty = form.querySelector('input[name="quantity"]').value;
            const name = form.querySelector('input[name="name"]').value;
            const price = form.querySelector('input[name="price"]').value;
            const total = (qty * price).toFixed(2);

            const popup = document.getElementById('cart-popup');
            popup.textContent = `Added ${qty} x ${name} (₹${total}) to cart`;
            popup.style.display = 'block';

            setTimeout(() => { popup.style.display = 'none'; }, 2500);
        })
        .catch(err => console.error(err));
    });
});
</script>

</body>
</html>

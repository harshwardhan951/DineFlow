<div class="admin-sidebar">
    <div class="logo">
        <h2>DineFlow</h2>
        <span>Admin Panel</span>
    </div>

    <ul class="menu">
        <li>
            <a href="admin_dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])=='admin_dashboard.php'?'active':'' ?>">
                🏠 Dashboard
            </a>
        </li>

        <li>
            <a href="add_hotel.php" class="<?= basename($_SERVER['PHP_SELF'])=='add_hotel.php'?'active':'' ?>">
                ➕ Add Hotel
            </a>
        </li>

        <li>
            <a href="view_hotels.php" class="<?= basename($_SERVER['PHP_SELF'])=='view_hotels.php'?'active':'' ?>">
                🏨 View Hotels
            </a>
        </li>

        <li>
            <a href="view_orders.php" class="<?= basename($_SERVER['PHP_SELF'])=='view_orders.php'?'active':'' ?>">
                📦 View Orders
            </a>
        </li>

        <li class="logout">
            <a href="logout.php">
                🔒 Logout
            </a>
        </li>
    </ul>
</div>

<header>
    <nav>
        <div class="logo-block">
            <img src="images/food-delivery.png" alt="logo" class="logo">
            <h1><a href="homepage.php">Chewtopia</a></h1>
        </div>
        <ul>
            <li><a href="staff-add-item.php">ITEMS</a></li>
            <li><a href="staff-add-restaurant.php">BRANCHES</a></li>
            <li><a href="offer.php">OFFER</a></li>
            <li><a href="staff-add-driver.php">DRIVERS</a></li>
            <li><a href="staff-profile.php">PROFILE</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="destroy_session.php">SIGN OUT</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
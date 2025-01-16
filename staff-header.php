<header>
    <nav>
        <div class="logo-block">
            <img src="images/delicious.png" alt="logo" class="logo">
            <h1><a href="staff-homepage.php">Chewtopia</a></h1>
        </div>
        <ul>
            <li><a href="staff-items.php">ITEMS</a></li>
            <li><a href="staff-restaurants.php">BRANCHES</a></li>
            <li><a href="staff-offers.php">OFFER</a></li>
            <li><a href="staff-add-driver.php">DRIVERS</a></li>
            <li><a href="staff-profile.php">PROFILE</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="destroy_session.php">SIGN OUT</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
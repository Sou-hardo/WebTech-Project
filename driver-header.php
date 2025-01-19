<header>
    <nav>
        <div class="logo-block">
            <img src="images/delicious.png" alt="logo" class="logo">
            <h1><a href="driver-homepage.php">Chewtopia</a></h1>
        </div>
        <ul>
            <?php if (isset($_SESSION['driver_id'])): ?>
                <li><a href="destroy_session.php">SIGN OUT</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
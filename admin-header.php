<header>
    <nav>
        <div class="logo-block">
            <img src="images/delicious.png" alt="logo" class="logo">
            <h1><a href="admin-homepage.php">Chewtopia</a></h1>
        </div>
        <ul>
            <li><a href="admin-homepage.php">STATS</a></li>
            <li><a href="admin-adress.php">LOCATIONS</a></li>
            <li><a href="add-admin.php">ADMINS</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="destroy_session.php">SIGN OUT</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
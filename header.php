<header>
    <nav>
        <div class="logo-block">
            <img src="images/delicious.png" alt="logo" class="logo">
            <h1><a href="homepage.php">Chewtopia</a></h1>
        </div>
        <ul>
            <li><a href="menu-items-all.php">ITEMS</a></li>
            <li><a href="restaurants-all.php">RESTAURANTS</a></li>
            
            
            <!-- <li><a href="about.php">ABOUT</a></li> -->
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="cart.php">CART</a></li>
                <li><a href="online-payment.php">PAYMENT</a></li>
                <li><a href="orders.php">ORDERS</a></li>
                <li><a href="profile.php">PROFILE</a></li>
                <li><a href="destroy_session.php">SIGN OUT</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
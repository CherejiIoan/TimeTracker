<?php if (
    isset($_SESSION['email']) && !empty($_SESSION['email'])
    && isset($_SESSION['name']) && !empty($_SESSION['name'])
): ?>
    <p>Bine ai venit:
        <?php echo $_SESSION['name'] ?>
    </p>
    <a href="index.php">Dashboard</a>
    <br />
    <a href="includes/logout.inc.php">Log Out</a>
    <br>
    <br>
<?php else: ?>
    <a href="login.php">Login</a>
    <br />
    <a href="register.php">Register</a>
<?php endif ?>
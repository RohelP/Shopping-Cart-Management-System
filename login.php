<?php
	session_start();

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		require_once('./db.php');
	
		$db = getDB();
		$user = $_POST['user'];
		$pass = $_POST['pass'];

		$u_stmt = $db->prepare("SELECT id FROM users WHERE username = ? AND password = ?");
		$u_stmt->bind_param("ss", $user, $pass);
		$u_stmt->execute();
		$u_stmt->bind_result($id);
		if ($u_stmt->fetch()) {
			$_SESSION['uid'] = $id;
			header("Location: home.php", true, 303);
			exit;
		} else {
			$_SESSION['error'] = "Invalid Username or Password.";
		}
		$u_stmt->close();
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Shopping Cart</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="container">
        <div class="login-box">
            <h2>User Login</h2>
            
            <?php if (!empty($_SESSION['error'])): ?>
                <p class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
            <?php endif; ?>
            
            <form method="post">
                <div class="input-group">
                    <label for="user">Username</label>
                    <input type="text" name="user" required>
                </div>

                <div class="input-group">
                    <label for="pass">Password</label>
                    <input type="password" name="pass" required>
                </div>

                <button type="submit" class="login-btn">Login</button>

                <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
            </form>
        </div>
    </div>

</body>
</html>

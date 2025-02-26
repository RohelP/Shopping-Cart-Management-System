<?php
	//login handling
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
			header("Location: ".PROJECT_ROOT."home", true, 303);
		} else {
			$_SESSION['error'] = "Username/Password not recognized.";
			header("Location: ".PROJECT_ROOT."login", true, 303);
		}
		$u_stmt->close();
		exit;
		//login handling end
	}
	if (@$_SESSION['error']) {
		echo "<p style='color:red;'>{$_SESSION['error']}</p>";
		unset($_SESSION['error']);
	}

?>
<h1>Login</h1>
<form method="post" id="login">
	<label for="user">USERNAME</label><br />
	<input type="text" name="user" /><br />
	<label for="pass">PASSWORD</label><br />
	<input type="password" name="pass" /><br />
</form>
	<input style="margin-top: 5px;" form="login" type="submit" value="Login" />
	<a style='margin-left: 5px;' href='<?php PROJECT_ROOT ?>register'><button>Register</button></a>
<?php
	//registration handling
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		require_once('./db.php');
	
		$db = getDB();

		$user = $_POST['user'];
		$pass = $_POST['pass'];

		$u_stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?,?)");
		$u_stmt->bind_param("ss", $user, $pass);
		$u_stmt->execute();
		$u_stmt->store_result();
		$match = $u_stmt->affected_rows;
		$u_stmt->close();

		if ($match == 1){
			$_SESSION['created'] = 1;
		}
		header("Location: ".PROJECT_ROOT."register", true, 303);
		exit;
	}
if (@$_SESSION['created']) {
	echo "<h2>Account created successfully.</h2><a href='".PROJECT_ROOT."login'><button>Login Now</button></a>";
	unset($_SESSION['created']);
} else {
	echo <<<RegisterForm
	<h1>Register</h1>
	<form method="post">
		<label for="user">USERNAME</label><br />
		<input type="text" name="user" /><br />
		<label for="pass">PASSWORD</label><br />
		<input type="password" name="pass" /><br />
		<input style="margin-top: 5px;" type="submit" value="Register" />
	</form>
RegisterForm;
}
?>
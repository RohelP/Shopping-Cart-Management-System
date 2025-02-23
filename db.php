<?php

	function getDB() {
		$dbhost="localhost";
		$dbuser="cp476";
		$dbpass="cp476";
		$dbname="my_database";
		try {
			// Create a DB connection
			$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
			if ($conn->connect_error) {
				$message = $conn->connect_error;
				if (str_starts_with($message, "Access denied for user '{$dbuser}'@'{$dbhost}'")) {
					echo "<h3>Error connecting to db</h3>";
					echo "Use the following commands to set up the db: ";
					echo "<pre>";
					echo "CREATE USER IF NOT EXISTS '{$dbuser}'@'{$dbhost}' IDENTIFIED BY '{$dbpass}';";
					echo "CREATE DATABASE IF NOT EXISTS '{$dbname}';";
					echo "GRANT ALL ON '{$dbname}'.* TO '{$dbuser}'@'{$dbhost}';";
					echo "</pre>";
					exit;
				} else {
					die("Database connection failed: " . $conn->connect_error . "\n");
				}
			}
			return $conn;
		} catch (mysqli_sql_exception $e) {
			$message = $e->getMessage();
			if (str_starts_with($message, "Access denied for user '{$dbuser}'@'{$dbhost}'")) {
				echo "<h3>Error connecting to db</h3>";
				echo "Use the following commands to set up the db: ";
				echo "<pre>";
				echo "CREATE USER IF NOT EXISTS '{$dbuser}'@'{$dbhost}' IDENTIFIED BY '{$dbpass}';";
				echo "CREATE DATABASE IF NOT EXISTS {$dbname};";
				echo "GRANT ALL ON {$dbname}.* TO '{$dbuser}'@'{$dbhost}';";
				echo "</pre>";
				exit;
			} else if (str_starts_with($message, "Unknown database 'cp467'")) {
				echo "<h3>Error connecting to db</h3>";
				echo "Use the following commands to set up the db: ";
				echo "<pre>";
				echo "CREATE DATABASE IF NOT EXISTS {$dbname};";
				echo "GRANT ALL ON {$dbname}.* TO '{$dbuser}'@'{$dbhost}';";
				echo "</pre>";
				exit;
			} else {
				die("Database connection failed: " . $message . "\n");
			}
		}
	}
	
	$db = getDB();
	$stmt = $db->prepare("CREATE TABLE IF NOT EXISTS migrations ( migration VARCHAR(200) PRIMARY KEY )");
	$stmt->execute();
	if (!$stmt->get_warnings()) {
		echo "<pre style='color: grey'>Created migrations table</pre>";
	}
	$stmt->close();
	
	$stmt = $db->prepare("SELECT migration from migrations");
	$stmt->execute();
	$result = $stmt->get_result();
	$runMigrations = $result ? array_column($result->fetch_all(MYSQLI_ASSOC), 'migration') : [];
	
	$migrationsDir = new DirectoryIterator('./migrations');
	$migrations = array();
	foreach ($migrationsDir as $migrationFile){
		if($migrationFile->isDot() || $migrationFile->getExtension() !== 'sql'){
			continue;
		}
		$migrationName = $migrationFile->getBasename('.sql');
		$migrations[] = $migrationName;
	}
	sort($migrations);
	foreach ($migrations as $migrationName) {
		$migration = file_get_contents("./migrations/{$migrationName}.sql");
		if ($migration && !in_array($migrationName, $runMigrations)) {
			$stmt = $db->prepare($migration);
			$stmt->execute();
			if (!$stmt->errno) {
				echo "<pre style='color: grey'>Ran {$migrationName}</pre>";
				$stmt2 = $db->prepare("INSERT INTO migrations VALUES (?)");
				$stmt2->bind_param("s", $migrationName);
				$stmt2->execute();
				$stmt2->close();
			}
			$stmt->close();
		}
	}
?>
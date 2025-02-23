<?php

	require_once('./db.php');

	$uid = @$_SESSION['uid'] ?: 0;
	$db = getDB();

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$cmd = $_REQUEST['command'];
		switch ($cmd) {
			case 'add':
				$item = $_POST['item'];
				$stmt = $db->prepare("INSERT INTO cart (uid,fk_product,quantity) VALUES (?,?,1);");
				$stmt->bind_param("ss", $uid, $item);
				$stmt->execute();
				$stmt->close();
				$stmt = $db->prepare("UPDATE product SET quantity = quantity - 1 WHERE _key = ?;");
				$stmt->bind_param("i", $item);
				$stmt->execute();
				$stmt->close();
				break;
			case 'addAnother':
				$item = $_POST['item'];
				try {
				$stmt = $db->prepare("UPDATE product SET quantity = quantity - 1 WHERE _key = ?;");
				$stmt->bind_param("i", $item);
				$stmt->execute();
				$stmt->close();
				$stmt = $db->prepare("UPDATE cart SET quantity = quantity + 1 WHERE uid = ? AND fk_product = ?;");
				$stmt->bind_param("si", $uid, $item);
				$stmt->execute();
				$stmt->close();
				} catch (Exception $e) {}
				break;
			case 'remove':
				$cart_item = $_POST['cart_item'];
				$stmt = $db->prepare("SELECT fk_product, quantity FROM cart WHERE uid = ? AND _key = ?;");
				$stmt->bind_param("si", $uid, $cart_item);
				$stmt->execute();
				if (!$result = $stmt->get_result()) {
					die('There was an error running the query [' . $stmt->error . ']\n');
				}
				$row = $result->fetch_assoc();
				$stmt->close();
				if ($row) {
					$stmt = $db->prepare("UPDATE product SET quantity = quantity + ? WHERE _key = ?;");
					$stmt->bind_param("ii", $row['quantity'], $row['fk_product']);
					$stmt->execute();
					$stmt->close();
					$stmt = $db->prepare("DELETE FROM cart WHERE uid = ? AND _key = ?;");
					$stmt->bind_param("si", $uid, $cart_item);
					$stmt->execute();
					$stmt->close();
				}
				break;
			case 'increment':
				try {
					$cart_item = $_POST['cart_item'];
					$stmt = $db->prepare("UPDATE product SET quantity = quantity - 1 WHERE _key IN (SELECT fk_product FROM cart WHERE uid = ? AND _key = ?);");
					$stmt->bind_param("si", $uid, $cart_item);
					$stmt->execute();
					$stmt->close();
					$stmt = $db->prepare("UPDATE cart SET quantity = quantity + 1 WHERE uid = ? AND _key = ?;");
					$stmt->bind_param("si", $uid, $cart_item);
					$stmt->execute();
					$stmt->close();
				} catch (Exception $e) {}
				break;
			case 'decrement':
				try {
					$cart_item = $_POST['cart_item'];
					$stmt = $db->prepare("UPDATE product SET quantity = quantity + 1 WHERE _key IN (SELECT fk_product FROM cart WHERE uid = ? AND _key = ?);");
					$stmt->bind_param("si", $uid, $cart_item);
					$stmt->execute();
					$stmt->close();
					$stmt = $db->prepare("UPDATE cart SET quantity = quantity - 1 WHERE uid = ? AND _key = ?;");
					$stmt->bind_param("si", $uid, $cart_item);
					$stmt->execute();
					$stmt->close();
				} catch (Exception $e) {}
				break;
		}
		header("HTTP/1.1 303 SEE OTHER");
		header("Location: #");
		exit;
	}
	
	function drawRemoveForm(array $row): String {
		$s = "<form method='post'>";
		$s .= "<input type='hidden' name='cart_item' value='{$row['_key']}' />";
		$s .= "<input type='hidden' name='command' value='remove' />";
		$s .= "<input type='submit' value='Remove' />";
		$s .= "</form>";
		return $s;
	}
	
	function drawQuntityForm(array $row): String {
		$s = "<form method='post' style='display: inline;'>";
		$s .= "<input type='hidden' name='cart_item' value='{$row['_key']}' />";
		$s .= "<input type='hidden' name='command' value='decrement' />";
		$s .= "<input type='submit' value='-' ".($row['quantity'] > 1 ? '' : 'disabled')." />";
		$s .= "</form>";
		$s .= "<form method='post' style='display: inline;'>";
		$s .= "<input type='hidden' name='cart_item' value='{$row['_key']}' />";
		$s .= "<input type='hidden' name='command' value='increment' />";
		$s .= "<input type='submit' value='+' ".($row['available'] > 0 ? '' : 'disabled')." />";
		$s .= "</form>";
		return $s;
	}
	
	function displayTable(array $rows) {
		echo "<table><tr><th>Product ID</th><th>Product Name</th><th>Quantity</th><th>Price</th><th>Status</th><th>Supplier Name</th></tr>";
		foreach ($rows as $row) {
			echo "<tr>";
			echo "<td>{$row['productId']}</td>";
			echo "<td>{$row['productName']}</td>";
			echo "<td>{$row['quantity']}</td>";
			echo "<td>{$row['price']}</td>";
			echo "<td>{$row['status']}</td>";
			echo "<td>{$row['supplierName']}</td>";
			echo "<td>".drawQuntityForm($row)."</td>";
			echo "<td>".drawRemoveForm($row)."</td>";
			echo "</tr>";
		}
		echo "</table>";
	}

	$arr = array();
	$stmt = $db->prepare("SELECT cart._key, product.`id` as `productId`, product.`name` as `productName`, cart.quantity, price, status, supplier.`name` as `supplierName`, product.`quantity` as `available` FROM cart INNER JOIN product ON fk_product = product._key INNER JOIN supplier ON product.`fk_supplier` = supplier.`id` WHERE uid = ?;");
	$stmt->bind_param("s", $uid);
	$stmt->execute();
	if (!$result = $stmt->get_result()) {
		die('There was an error running the query [' . $stmt->error . ']\n');
	}
	while($row = $result->fetch_assoc()){
		array_push($arr, $row);
	}
	$stmt->close();
	displayTable($arr);
	if (count($arr) == 0) {
		echo "<h3>No items in cart</h3>";
		echo "<a href='".PROJECT_ROOT."home'><button>Add some now</button></a>";
	} else {
		
		echo "<a href='".PROJECT_ROOT."home'><button>Continue Shopping</button></a>";
	}
?>
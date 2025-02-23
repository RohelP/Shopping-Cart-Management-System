<?php
	require_once('./db.php');
	
	$uid = @$_SESSION['uid'] ?: 0;
	
	function drawForm(array $row, array $items): String {
		if (in_array($row['_key'], $items) && $row['quantity'] > 0) {
			$s = "In Cart<form method='post' action='".PROJECT_ROOT."cart' style='display:inline;margin-left:10px;'>";
			$s .= "<input type='hidden' name='item' value='{$row['_key']}' />";
			$s .= "<input type='hidden' name='command' value='addAnother' />";
			$s .= "<input type='submit' value='+' />";
			$s .= "</form>";
			return $s;
		}
		if (in_array($row['_key'], $items)) {
			return "In Cart";
		}
		if ($row['quantity'] == 0) {
			return "Out of Stock";
		}
		$s = "<form method='post' action='".PROJECT_ROOT."cart'>";
		$s .= "<input type='hidden' name='item' value='{$row['_key']}' />";
		$s .= "<input type='hidden' name='command' value='add' />";
		$s .= "<input type='submit' value='Add to cart' />";
		$s .= "</form>";
		return $s;
	}
	
	function displayTable(array $rows, array $items) {
		echo "<table><tr><th>Product ID</th><th>Product Name</th><th>Quantity</th><th>Price</th><th>Status</th><th>Supplier Name</th></tr>";
		foreach ($rows as $row) {
			echo "<tr>";
			echo "<td>{$row['productId']}</td>";
			echo "<td>{$row['productName']}</td>";
			echo "<td>{$row['quantity']}</td>";
			echo "<td>{$row['price']}</td>";
			echo "<td>{$row['status']}</td>";
			echo "<td>{$row['supplierName']}</td>";
			echo "<td>".drawForm($row, $items)."</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	$arr = array();
	$items = array();
	$db = getDB();
	$stmt = $db->prepare("SELECT _key, product.`id` as `productId`, product.`name` as `productName`, quantity, price, status, supplier.`name` as `supplierName` FROM product INNER JOIN supplier ON product.`fk_supplier` = supplier.`id`;");
	$stmt->execute();
	if (!$result = $stmt->get_result()) {
		die('There was an error running the query [' . $stmt->error . ']\n');
	}
	while($row = $result->fetch_assoc()){
		array_push($arr, $row);
	}
	$stmt->close();
	$stmt = $db->prepare("SELECT product.`_key` FROM cart INNER JOIN product ON fk_product = product._key WHERE uid = ?;");
	$stmt->bind_param("s", $uid);
	$stmt->execute();
	if (!$result = $stmt->get_result()) {
		die('There was an error running the query [' . $stmt->error . ']\n');
	}
	while($row = $result->fetch_assoc()){
		array_push($items, $row['_key']);
	}
	if (count($items) > 0) {
		echo "<a href='".PROJECT_ROOT."cart'><button>View Cart</button></a>";
	}
	displayTable($arr, $items);
?>
<?php
include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

$query = "SELECT o.*, u.name as user_name 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id";

if ($user_id) {
    $query .= " WHERE o.user_id = :user_id";
}

$query .= " ORDER BY o.created_at DESC";

$stmt = $db->prepare($query);

if ($user_id) {
    $stmt->bindParam(':user_id', $user_id);
}

$stmt->execute();

$orders = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($row);

    // Fetch items for this order
    $query_items = "SELECT oi.*, p.name as product_name, p.image as product_image 
                    FROM order_items oi 
                    LEFT JOIN products p ON oi.product_id = p.id 
                    WHERE oi.order_id = :order_id";
    $stmt_items = $db->prepare($query_items);
    $stmt_items->bindParam(':order_id', $id);
    $stmt_items->execute();
    $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

    $order_item = array(
        "id" => $id,
        "user_id" => $user_id,
        "user_name" => $user_name,
        "total_amount" => $total_amount,
        "status" => $status,
        "shipping_address" => $shipping_address,
        "created_at" => $created_at,
        "items" => $items
    );
    array_push($orders, $order_item);
}

echo json_encode($orders);
?>
<?php
include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

if (!$product_id) {
    http_response_code(400);
    echo json_encode(array("message" => "Product ID is required."));
    exit;
}

$query = "SELECT r.*, u.name as user_name 
          FROM reviews r 
          LEFT JOIN users u ON r.user_id = u.id 
          WHERE r.product_id = :product_id 
          ORDER BY r.created_at DESC";

$stmt = $db->prepare($query);
$stmt->bindParam(':product_id', $product_id);
$stmt->execute();

$reviews = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    $review_item = array(
        "id" => $id,
        "user_id" => $user_id,
        "user_name" => $user_name,
        "rating" => $rating,
        "comment" => $comment,
        "created_at" => $created_at
    );
    array_push($reviews, $review_item);
}

echo json_encode($reviews);
?>
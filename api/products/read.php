<?php
include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;

$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id";

if ($category_id) {
    $query .= " WHERE p.category_id = :category_id";
}

$query .= " ORDER BY p.created_at DESC";

$stmt = $db->prepare($query);

if ($category_id) {
    $stmt->bindParam(':category_id', $category_id);
}

$stmt->execute();

$products = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    $product_item = array(
        "id" => $id,
        "name" => $name,
        "slug" => $slug,
        "description" => $description,
        "price" => $price,
        "image" => $image,
        "category_id" => $category_id,
        "category_name" => $category_name,
        "stock" => $stock
    );
    array_push($products, $product_item);
}

echo json_encode($products);
?>
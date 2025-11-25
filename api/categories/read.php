<?php
include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM categories ORDER BY name";
$stmt = $db->prepare($query);
$stmt->execute();

$categories = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    $category_item = array(
        "id" => $id,
        "name" => $name,
        "slug" => $slug,
        "image" => $image,
        "description" => $description
    );
    array_push($categories, $category_item);
}

echo json_encode($categories);
?>
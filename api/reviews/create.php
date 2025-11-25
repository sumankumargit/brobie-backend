<?php
include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->product_id) &&
    !empty($data->user_id) &&
    !empty($data->rating)
) {
    $query = "INSERT INTO reviews SET product_id=:product_id, user_id=:user_id, rating=:rating, comment=:comment";
    $stmt = $db->prepare($query);

    $data->comment = !empty($data->comment) ? htmlspecialchars(strip_tags($data->comment)) : null;

    $stmt->bindParam(":product_id", $data->product_id);
    $stmt->bindParam(":user_id", $data->user_id);
    $stmt->bindParam(":rating", $data->rating);
    $stmt->bindParam(":comment", $data->comment);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(array("message" => "Review created."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create review."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
}
?>
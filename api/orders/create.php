<?php
include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->user_id) &&
    !empty($data->total_amount) &&
    !empty($data->shipping_address) &&
    !empty($data->items)
) {
    try {
        $db->beginTransaction();

        // Create Order
        $query = "INSERT INTO orders SET user_id=:user_id, total_amount=:total_amount, shipping_address=:shipping_address, status='pending'";
        $stmt = $db->prepare($query);

        $stmt->bindParam(":user_id", $data->user_id);
        $stmt->bindParam(":total_amount", $data->total_amount);
        $stmt->bindParam(":shipping_address", $data->shipping_address);

        $stmt->execute();
        $order_id = $db->lastInsertId();

        // Create Order Items
        foreach ($data->items as $item) {
            $query_item = "INSERT INTO order_items SET order_id=:order_id, product_id=:product_id, quantity=:quantity, price=:price, variant_info=:variant_info";
            $stmt_item = $db->prepare($query_item);

            $stmt_item->bindParam(":order_id", $order_id);
            $stmt_item->bindParam(":product_id", $item->product_id);
            $stmt_item->bindParam(":quantity", $item->quantity);
            $stmt_item->bindParam(":price", $item->price);
            $stmt_item->bindParam(":variant_info", $item->variant_info);

            $stmt_item->execute();
        }

        $db->commit();
        http_response_code(201);
        echo json_encode(array("message" => "Order created successfully."));

    } catch (Exception $e) {
        $db->rollBack();
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create order. " . $e->getMessage()));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
}
?>
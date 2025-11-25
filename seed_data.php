<?php
include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Seed Categories
$categories = [
    ['Men', 'men', 'https://images.unsplash.com/photo-1490578474895-699cd4e2cf59?q=80&w=2071&auto=format&fit=crop', 'Men\'s Collection'],
    ['Women', 'women', 'https://images.unsplash.com/photo-1503342217505-b0815a046baf?q=80&w=2070&auto=format&fit=crop', 'Women\'s Collection'],
    ['Accessories', 'accessories', 'https://images.unsplash.com/photo-1523293182086-7651a899d60f?q=80&w=2068&auto=format&fit=crop', 'Accessories Collection']
];

foreach ($categories as $cat) {
    $query = "INSERT IGNORE INTO categories (name, slug, image, description) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute($cat);
}

// Seed Products
$products = [
    ['Classic T-Shirt', 'classic-t-shirt', 'Men', 'A classic cotton t-shirt.', 29.99, 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=1780&auto=format&fit=crop'],
    ['Denim Jacket', 'denim-jacket', 'Men', 'Stylish denim jacket.', 89.99, 'https://images.unsplash.com/photo-1576871337622-98d48d1cf531?q=80&w=1887&auto=format&fit=crop'],
    ['Summer Dress', 'summer-dress', 'Women', 'Light and breezy summer dress.', 49.99, 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?q=80&w=1946&auto=format&fit=crop'],
    ['Leather Bag', 'leather-bag', 'Accessories', 'Premium leather bag.', 129.99, 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?q=80&w=1938&auto=format&fit=crop']
];

foreach ($products as $prod) {
    // Get Category ID
    $cat_query = "SELECT id FROM categories WHERE name = ?";
    $cat_stmt = $db->prepare($cat_query);
    $cat_stmt->execute([$prod[2]]);
    $cat_row = $cat_stmt->fetch(PDO::FETCH_ASSOC);

    if ($cat_row) {
        $query = "INSERT IGNORE INTO products (name, slug, category_id, description, price, image, stock) VALUES (?, ?, ?, ?, ?, ?, 100)";
        $stmt = $db->prepare($query);
        $stmt->execute([$prod[0], $prod[1], $cat_row['id'], $prod[3], $prod[4], $prod[5]]);
    }
}

// Seed Admin User
$password = password_hash('admin123', PASSWORD_DEFAULT);
$query = "INSERT IGNORE INTO users (name, email, password, role) VALUES ('Admin', 'admin@example.com', '$password', 'admin')";
$db->exec($query);

echo "Database seeded successfully!";
?>
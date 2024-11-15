<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'junessa';  // Your database name
$user = 'root';  // Your database username
$pass = '';  // Your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Add product
if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $stmt = $pdo->prepare("INSERT INTO products (product_name, description, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->execute([$product_name, $description, $quantity, $price]);

    $message = "Product added successfully!";
}

// Update product quantity
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $new_quantity = $_POST['quantity'];

    $stmt = $pdo->prepare("UPDATE products SET quantity = ? WHERE id = ?");
    $stmt->execute([$new_quantity, $product_id]);

    $message = "Product quantity updated successfully!";
}

// Fetch products for dashboard
$stmt = $pdo->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bahian Bakery</title>
    <link rel="stylesheet" href="styles.css">  <!-- Link to your CSS file -->
</head>
<body>
    <div class="dashboard">
        <h1>Bahian Bakery</h1>
        
        <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>

        <!-- Add Product Form -->
        <div class="add-product-form">
            <h2>Bread</h2>
            <form method="POST" action="">
                <input type="text" name="Star Bread" placeholder="Product Name" required>
                <textarea name="description" placeholder="Description" required></textarea>
                <input type="number" name="quantity" placeholder="Quantity" required>
                <input type="number" name="price" placeholder="Price" step="0.01" required>
                <button type="submit" name="add_product">Add Product</button>
            </form>
        </div>

            <form method="POST" action="">
                <input type="text" name="Pande Coco" placeholder="Product Name" required>
                <textarea name="description" placeholder="Description" required></textarea>
                <input type="number" name="quantity" placeholder="Quantity" required>
                <input type="number" name="price" placeholder="Price" step="0.01" required>
                <button type="submit" name="add_product">Add Product</button>
            </form>
        </div>

        <!-- Product List Table -->
        <div class="product-list">
            <h2>Product List</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product) : ?>
                        <tr>
                            <td><?= $product['id']; ?></td>
                            <td><?= $product['product_name']; ?></td>
                            <td><?= $product['description']; ?></td>
                            <td><?= $product['quantity']; ?></td>
                            <td>$<?= number_format($product['price'], 2); ?></td>
                            <td>
                                <!-- Update Quantity Form -->
                                <form method="POST" action="" style="display: inline;">
                                    <input type="number" name="quantity" placeholder="New Quantity" required>
                                    <input type="hid
                                    den" name="product_id" value="<?= $product['id']; ?>">
                                    <button type="submit" name="update_quantity">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
session_start();

// Database connection details
$host = 'localhost';
$db = 'junessa'; // Replace with your database name
$user = 'root';  // Replace with your database username
$pass = '';      // Replace with your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Bread choices
$bread_choices = [
    "Sourdough Bread",
    "Whole Wheat Bread",
    "Brioche Bread",
    "Rye Bread",
    "Multigrain Bread",
    "French Baguette",
    "Ciabatta",
    "Focaccia",
    "Pita Bread",
    "Banana Bread"
];

// Add product logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $product_name = htmlspecialchars($_POST['product_name']);
    $description = htmlspecialchars($_POST['description']);
    $quantity = (int) $_POST['quantity'];
    $price = (float) $_POST['price'];

    if ($product_name && $description && $quantity > 0 && $price > 0) {
        $stmt = $pdo->prepare("INSERT INTO products (product_name, description, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$product_name, $description, $quantity, $price]);
        $message = "Product added successfully!";
    } else {
        $message = "Please provide valid inputs!";
    }
}

// Fetch products
$stmt = $pdo->prepare("SELECT * FROM products ORDER BY id DESC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baked Babe Bakery Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffe4e6;
            color: #333;
        }

        .dashboard {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            color: #ff639a;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .add-product-form {
            margin-bottom: 30px;
        }

        .add-product-form h2 {
            font-size: 1.8em;
            color: #ff639a;
        }

        .add-product-form form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .add-product-form input,
        .add-product-form select,
        .add-product-form textarea,
        .add-product-form button {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
        }

        .add-product-form button {
            background-color: #ff639a;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .add-product-form button:hover {
            background-color: #ff3d81;
        }

        .product-list {
            margin-top: 20px;
        }

        .product-list h2 {
            text-align: center;
            font-size: 1.8em;
            color: #ff639a;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: auto;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #ff639a;
            color: white;
        }

        td {
            background-color: #ffe4e6;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        @media (max-width: 768px) {
            table {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>Baked Babe Bakery</h1>

        <!-- Display Message -->
        <?php if (isset($message)): ?>
            <p style="text-align:center; color:#28a745;"><?= $message; ?></p>
        <?php endif; ?>

        <!-- Add Product Form -->
        <div class="add-product-form">
            <h2>Add Product</h2>
            <form method="POST" action="">
                <select name="product_name" required>
                    <option value="">Select Bread</option>
                    <?php foreach ($bread_choices as $bread): ?>
                        <option value="<?= $bread; ?>"><?= $bread; ?></option>
                    <?php endforeach; ?>
                </select>
                <textarea name="description" placeholder="Description" required></textarea>
                <input type="number" name="quantity" placeholder="Quantity" min="1" required>
                <input type="number" name="price" placeholder="Price" step="0.01" min="0.01" required>
                <button type="submit" name="add_product">Add Product</button>
            </form>
        </div>

        <!-- Product List -->
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
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= $product['id']; ?></td>
                                <td><?= $product['product_name']; ?></td>
                                <td><?= $product['description']; ?></td>
                                <td><?= $product['quantity']; ?></td>
                                <td>$<?= number_format($product['price'], 2); ?></td>
                                <td>
                                    <button>Buy</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No products available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

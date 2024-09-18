
<?php
session_start();
include "connection.php";

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $delete_query = "DELETE FROM users WHERE id='$user_id'";
    mysqli_query($conn, $delete_query);
    header("Location: admin.php");
    exit();
}

// Handle product addition
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $add_query = "INSERT INTO products (name, price) VALUES ('$name', '$price')";
    mysqli_query($conn, $add_query);
}

// Handle product update
if (isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $update_query = "UPDATE products SET name='$name', price='$price' WHERE id='$product_id'";
    mysqli_query($conn, $update_query);
}

// Handle product deletion
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    $delete_query = "DELETE FROM products WHERE id='$product_id'";
    mysqli_query($conn, $delete_query);
}

// Fetch users for display
$users_query = "SELECT id, username, email FROM users";
$users_result = mysqli_query($conn, $users_query);

// Fetch products for display
$products_query = "SELECT * FROM products";
$products_result = mysqli_query($conn, $products_query);

// Fetch contact messages for display
$messages_query = "SELECT * FROM contact";
$messages_result = mysqli_query($conn, $messages_query);

// Get the count of new users registered
$new_users_query = "SELECT COUNT(*) as count FROM users WHERE created_at > DATE_SUB(NOW(), INTERVAL 1 MONTH)";
$new_users_result = mysqli_query($conn, $new_users_query);
$new_users_count = mysqli_fetch_assoc($new_users_result)['count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body style="background: url('./photos/forest bg.webp'); background-repeat: no-repeat; background-position: center; background-size: cover;">
    <div class="container">
        <div class="form-box box">
            <header>Admin Dashboard</header>
            <hr>

            <!-- Contact Messages Section -->
            <h2>Contact Messages</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($message = mysqli_fetch_assoc($messages_result)) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($message['id']); ?></td>
                            <td><?php echo htmlspecialchars($message['name']); ?></td>
                            <td><?php echo htmlspecialchars($message['email']); ?></td>
                            <td><?php echo htmlspecialchars($message['message']); ?></td>
                            <td>
                                <form action="admin.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="message_id" value="<?php echo htmlspecialchars($message['id']); ?>">
                                    <button type="submit" name="delete_message" onclick="return confirm('Are you sure you want to delete this message?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- New Users Section -->
            <h2>New Users (Last 30 Days): <?php echo htmlspecialchars($new_users_count); ?></h2>

            <!-- Products Section -->
            <h2>Manage Products</h2>
            <form action="admin.php" method="POST">
                <input type="text" name="name" placeholder="Product Name" required>
                <input type="number" name="price" placeholder="Price" required step="0.01">
                <input type="submit" name="add_product" value="Add Product" class="btn">
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = mysqli_fetch_assoc($products_result)) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['price']); ?></td>
                            <td>
                                <form action="admin.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                    <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required step="0.01">
                                    <button type="submit" name="update_product">Update</button>
                                </form>
                                <form action="admin.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                                    <button type="submit" name="delete_product" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- User Management Section -->
            <h2>User Management</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($users_result)) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <form action="admin.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                    <button type="submit" name="delete_user" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <a href="logout.php" class="btn">Logout</a>
        </div>
    </div>
</body>
</html>

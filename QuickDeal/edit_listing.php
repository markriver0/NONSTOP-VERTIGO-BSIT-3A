<?php
include './auth/auth.php';
include './db/db_conn.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ./signin/signin.php");
    exit;
}

$loggedInUserId = $_SESSION['user_id'];

// Validate product ID
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Invalid or missing product ID.");
}
$itemId = (int) $_GET['id'];

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM items WHERE item_id = ? AND user_id = ?");
$stmt->bind_param("ii", $itemId, $loggedInUserId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Product not found or you do not have permission to edit this product.");
}

$product = $result->fetch_assoc();
$stmt->close();

// Fetch categories
$categories = [];
$categoryStmt = $conn->prepare("SELECT category_id, name FROM categories");
$categoryStmt->execute();
$categoryResult = $categoryStmt->get_result();
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row;
}
$categoryStmt->close();

// Fetch subcategories
$subcategories = [];
$subcategoryStmt = $conn->prepare("SELECT subcategory_id, category_id, name FROM subcategory");
$subcategoryStmt->execute();
$subcategoryResult = $subcategoryStmt->get_result();
while ($row = $subcategoryResult->fetch_assoc()) {
    $subcategories[] = $row;
}
$subcategoryStmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $categoryId = (int) $_POST['category_id'];
    $subcategoryId = (int) $_POST['subcategory_id'];
    $price = (float) $_POST['price'];
    $condition = trim($_POST['condition']);
    $location = trim($_POST['location']);
    $status = trim($_POST['status']);

    // Handle image upload
    $imageUrl = $product['image_url']; // Default to existing image
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . uniqid() . '_' . basename($_FILES['image_url']['name']); // Ensure unique file name
        if (move_uploaded_file($_FILES['image_url']['tmp_name'], $uploadFile)) {
            $imageUrl = $uploadFile;
        } else {
            echo "<p style='color: red;'>Failed to upload image. Please try again.</p>";
        }
    }

    // Update product details
    $updateStmt = $conn->prepare("UPDATE items SET title = ?, description = ?, category_id = ?, subcategory_id = ?, price = ?, `condition` = ?, location = ?, status = ?, image_url = ?, updated_at = NOW() WHERE item_id = ? AND user_id = ?");
    $updateStmt->bind_param("ssiidsssiis", $title, $description, $categoryId, $subcategoryId, $price, $condition, $location, $status, $imageUrl, $itemId, $loggedInUserId);
    $updateStmt->execute();

    if ($updateStmt->affected_rows > 0) {
        echo "<p style='color: green;'>Product updated successfully!</p>";
    } else {
        echo "<p style='color: red;'>Failed to update product. Please try again.</p>";
    }

    $updateStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="edit_listing.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<button id="dark-mode-toggle" class="dark-mode-toggle">
    <span>🌙</span> 
</button></main>

<main>

<section>
    <h1>Edit Product</h1>
    <form method="POST" enctype="multipart/form-data"> 
      <div class="form-w">      
<div>
    <label for="image_url">Product Image:</label>
        <input type="file" id="image_url" name="image_url" accept="image/*">
        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="Current Image" style="max-width: 200px; display: block; margin-top: 10px;">
</div>

<div>
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>       
        
        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
</div>

<div>
        <label for="category_id">Category:</label>
        <select id="category_id" name="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['category_id'] ?>" <?= $product['category_id'] == $category['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
</div>

<div>
        <label for="subcategory_id">Subcategory:</label>
        <select id="subcategory_id" name="subcategory_id" required>
            <?php foreach ($subcategories as $subcategory): ?>
                <?php if ($subcategory['category_id'] == $product['category_id']): ?>
                    <option value="<?= $subcategory['subcategory_id'] ?>" <?= $product['subcategory_id'] == $subcategory['subcategory_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($subcategory['name']) ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>

        </div>

        <div>
        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>
        </div>

        <div>
        <label for="condition">Condition:</label>
        <input type="text" id="condition" name="condition" value="<?= htmlspecialchars($product['condition']) ?>" required>
        </div>

        <div>
        <label for="location">Location:</label>
        <input type="text" id="location" name="location" value="<?= htmlspecialchars($product['location']) ?>" required>
        </div>

        <div>
        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Available" <?= $product['status'] === 'Available' ? 'selected' : '' ?>>Available</option>
            <option value="Sold" <?= $product['status'] === 'Sold' ? 'selected' : '' ?>>Sold</option>
        </select>
        </div>
        </div>


        <button name="button" type="submit">Update Product</button>
        </div>
    </form>

    </section>
</main>

<footer><?php include 'footer.php'; ?></footer>
  <script src="dark-mode.js"></script>
</body>
</html>
<?php
session_start();

// Our product list. This is like our mini-database.
$products = array(
    array('id' => 1, 'name' => 'Wireless Mouse', 'description' => 'Ergonomic wireless mouse', 'price' => 29.99, 'category' => 'Electronics'),
    array('id' => 2, 'name' => 'Desk Lamp', 'description' => 'LED desk lamp with adjustable brightness', 'price' => 24.95, 'category' => 'Home')
);

// Let's make some empty variables to start with
$errors = array();
$submittedData = array();
$successMessage = '';

// I'll make a function to create a new ID number
function makeNewId($productList) {
    // If the list is empty, start at 1
    if (empty($productList)) {
        return 1;
    }
    
    // Otherwise, find the highest ID and add 1 to it
    $highestId = 0;
    foreach ($productList as $item) {
        if ($item['id'] > $highestId) {
            $highestId = $item['id'];
        }
    }
    return $highestId + 1;
}

// This function will check if the form data is good
function checkFormData($data, &$errorList) {
    $isGood = true; // Let's assume it's good until we find a problem
    
    // Check Name
    if (empty($data['name'])) {
        $errorList['name'] = "You need to enter a product name.";
        $isGood = false;
    }
    
    // Check Description
    if (empty($data['description'])) {
        $errorList['description'] = "Please describe the product.";
        $isGood = false;
    }
    
    // Check Price
    if (empty($data['price'])) {
        $errorList['price'] = "How much does it cost?";
        $isGood = false;
    } else if (!is_numeric($data['price'])) {
        $errorList['price'] = "Price needs to be a number.";
        $isGood = false;
    } else if ($data['price'] < 0) {
        $errorList['price'] = "Price can't be less than zero!";
        $isGood = false;
    }
    
    // Check Category
    if (empty($data['category'])) {
        $errorList['category'] = "Please choose a category.";
        $isGood = false;
    }
    
    return $isGood;
}

// Now check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from the form
    $submittedData['name'] = trim($_POST['name'] ?? '');
    $submittedData['description'] = trim($_POST['description'] ?? '');
    $submittedData['price'] = trim($_POST['price'] ?? '');
    $submittedData['category'] = trim($_POST['category'] ?? '');
    
    // Use our function to check the data
    $isValid = checkFormData($submittedData, $errors);
    
    if ($isValid) {
        // If everything is good, add the new product
        $newId = makeNewId($products);
        
        $newProduct = array(
            'id' => $newId,
            'name' => $submittedData['name'],
            'description' => $submittedData['description'],
            'price' => (float)$submittedData['price'],
            'category' => $submittedData['category']
        );
        
        // Add it to our list
        $products[] = $newProduct;
        
        // Show success message
        $successMessage = "Nice! '" . $newProduct['name'] . "' was added successfully!";
        
        // Clear the form
        $submittedData = array();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Product Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f8ff; padding: 20px; }
        .card { border-radius: 10px; margin-bottom: 20px; }
        .btn { border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-3">🛍️ Product Manager</h1>
        <p class="text-center text-muted mb-4">Add and view your products</p>

        <!-- Success Message -->
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Yay!</strong> <?php echo $successMessage; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Wait!</strong> Please check the form below.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Form Side -->
            <div class="col-lg-5">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">➕ Add New Product</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <!-- Name -->
                            <div class="mb-2">
                                <label class="form-label">Name *</label>
                                <input type="text" name="name" class="form-control <?php if(isset($errors['name'])) echo 'is-invalid'; ?>" value="<?php echo htmlspecialchars($submittedData['name'] ?? ''); ?>">
                                <?php if(isset($errors['name'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Description -->
                            <div class="mb-2">
                                <label class="form-label">Description *</label>
                                <textarea name="description" class="form-control <?php if(isset($errors['description'])) echo 'is-invalid'; ?>" rows="2"><?php echo htmlspecialchars($submittedData['description'] ?? ''); ?></textarea>
                                <?php if(isset($errors['description'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['description']; ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Price -->
                            <div class="mb-2">
                                <label class="form-label">Price ($) *</label>
                                <input type="number" step="0.01" name="price" class="form-control <?php if(isset($errors['price'])) echo 'is-invalid'; ?>" value="<?php echo htmlspecialchars($submittedData['price'] ?? ''); ?>">
                                <?php if(isset($errors['price'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['price']; ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Category -->
                            <div class="mb-3">
                                <label class="form-label">Category *</label>
                                <select name="category" class="form-select <?php if(isset($errors['category'])) echo 'is-invalid'; ?>">
                                    <option value="">-- Choose --</option>
                                    <option value="Electronics" <?php if (isset($submittedData['category']) && $submittedData['category'] == 'Electronics') echo 'selected'; ?>>Electronics</option>
                                    <option value="Home" <?php if (isset($submittedData['category']) && $submittedData['category'] == 'Home') echo 'selected'; ?>>Home</option>
                                    <option value="Clothing" <?php if (isset($submittedData['category']) && $submittedData['category'] == 'Clothing') echo 'selected'; ?>>Clothing</option>
                                    <option value="Books" <?php if (isset($submittedData['category']) && $submittedData['category'] == 'Books') echo 'selected'; ?>>Books</option>
                                </select>
                                <?php if(isset($errors['category'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['category']; ?></div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-info text-white w-100">Add Product</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Table Side -->
            <div class="col-lg-7">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">📋 Product List</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($products)): ?>
                            <p class="text-muted text-center">No products to show yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Category</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td><?php echo $product['id']; ?></td>
                                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($product['category']); ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
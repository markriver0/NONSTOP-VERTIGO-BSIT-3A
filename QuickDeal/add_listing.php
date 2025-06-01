<?php
include './db/db_conn.php';
session_start();

// Fetch categories and subcategories from the database
$categories = [];
$subcategories = [];

$result = $conn->query("SELECT * FROM categories");
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

$result = $conn->query("SELECT * FROM subcategory");
while ($row = $result->fetch_assoc()) {
    $subcategories[$row['category_id']][] = $row;
}

// Pass categories and subcategories to JavaScript
echo "<script>const categories = " . json_encode($categories) . ";</script>";
echo "<script>const subcategories = " . json_encode($subcategories) . ";</script>";

if (isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $price = $_POST['price'];
    $category = (int)$_POST['category'];
    $subcategory = (int)$_POST['subcategory'];
    $condition = $_POST['condition'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $status = "Pending";
    $created_at = date('Y-m-d H:i:s');
    $updated_at = $created_at;

    // Handle image upload
    $image_url = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '_' . basename($_FILES['photo']['name']);
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
            $image_url = $uploadPath;
        } else {
            die("Image upload failed.");
        }
    } else {
        die("No photo uploaded.");
    }

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO items (user_id, title, description, category_id, subcategory_id, price, `condition`, location, status, created_at, updated_at, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssisssssss", $user_id, $title, $description, $category, $subcategory, $price, $condition, $location, $status, $created_at, $updated_at, $image_url);

    if ($stmt->execute()) {
        header("Location: listing.php?success=Item added");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Marketplace - Item for sale</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="add_listing.css">
</head>
<body>

<button id="dark-mode-toggle" class="dark-mode-toggle">
    <span>🌙</span> 
</button>


<main>



  <!-- Main content with the two sections -->
  <section class="main-content">
    <!-- Add Listing Section -->
    <div class="add-listing-section">
      <div class="page-title">Item for sale</div>
      
<!-- User Info -->
<div class="user-info">
  <div class="user-avatar"></div>
  <div>
    <div class="user-name"><?php echo htmlspecialchars($_SESSION['fullname'] ?? 'Guest'); ?></div>
    <div class="listing-meta">Listing to QuickDeal · Public</div>
  </div>
</div>


      <!-- Photos Section -->

  <form action="add_listing.php" method="POST" enctype="multipart/form-data">
    
      <div class="photos-container">
  <div class="photos-info" id="photo-count">Photos: 0 / 1 - You can add up to 1 photos.</div>
  <div class="add-photos" id="photo-uploader">
<input type="file" id="photo-input" name="photo" accept="image/*" required style="display: none;" />
    <div class="add-photos-icon">+</div>
    <div class="add-photos-text">Add photos</div>
    <div class="add-photos-subtext">or drag and drop</div>
  </div>
  <div id="photo-preview" class="photo-preview"></div>
</div>

      <!-- Required Fields -->
      <div class="section-title">Required</div>
      <div class="hint-text">Be as descriptive as possible.</div>


<input type="text" class="input-field" name="title" placeholder="Title" required>
<input type="number" class="input-field" name="price" placeholder="Price" required>
      

<div class="dropdown" id="category-dropdown">
  <div class="dropdown-header">
    <span class="dropdown-text">Category</span>
    <span class="dropdown-icon">▼</span>
  </div>
  <div class="dropdown-menu">
    <?php foreach ($categories as $category): ?>
      <div class="dropdown-item" data-value="<?php echo $category['category_id']; ?>">
        <?php echo htmlspecialchars($category['name']); ?>
      </div>
    <?php endforeach; ?>
  </div>
  <input type="hidden" name="category" id="selected-category" required>
</div>

<div class="dropdown" id="subcategory-dropdown">
  <div class="dropdown-header">
    <span class="dropdown-text">Subcategory</span>
    <span class="dropdown-icon">▼</span>
  </div>
  <div class="dropdown-menu" id="subcategory-menu">
    <!-- Subcategories will be dynamically populated based on the selected category -->
  </div>
  <input type="hidden" name="subcategory" id="selected-subcategory" required>
</div>


<div class="dropdown" id="condition-dropdown">
  <div class="dropdown-header">
    <span class="dropdown-text">Condition</span>
    <span class="dropdown-icon">▼</span>
  </div>
  <div class="dropdown-menu">
    <div class="dropdown-item" data-value="new">New</div>
    <div class="dropdown-item" data-value="good">Good</div>
    <div class="dropdown-item" data-value="fair">Fair</div>
    <div class="dropdown-item" data-value="poor">Poor</div>
  </div>
<input type="hidden" name="condition" id="selected-condition" required>
</div>


      <div class="more-details">
        <div class="details-header">
          <h1 class="section-title">More details</h1>
        </div>
        <p class="details-hint">Attract more interest by including more details.</p>
      </div>

<input type="text" class="brand-input" name="description" id="brand-input" placeholder="More Details" required>
<input type="text" class="location-input" name="location" id="location-input" placeholder="Location" required>

      <button type="submit" name="submit" class="action-button next-btn" onclick="window.location.href='listing.php'">Next</button>
      </form>
    </div>


    <div class="listing-preview">
      <div class="preview-header">Preview</div>
      <div class="preview-container">
        <div class="preview-content">
          <div class="preview-message">Your listing preview</div>
          <div class="preview-submessage">As you create your listing, you can preview how it will appear to others on Marketplace.</div>
        </div>
        
        <div id="filled-preview" style="display: none; padding: 20px;">
          <div class="preview-item">
            <div class="preview-image">
              <div style="color: #65676b;">Your Image is Here</div>
            </div>


            <div class="preview-details">
              <h1 class="preview-title">Title</h1>
              <h2 class="preview-price">Price</h2>
              <p class="preview-category">Category</p>
              <p class="preview-condition">Condition</p>

              <div class="preview-meta">
                Listed a few seconds ago<br>
                in Lisga
              </div>
              
              <h1 class="preview-section-title">Details</h1>
              <p class="preview-brand">Description</p>
              <p class="preview-location">Location</p>
              
              <div class="preview-section-title">Seller information</div>
              <div class="seller-info">
                <div class="user-avatar" style="width: 32px; height: 32px;"></div>
                <div class="seller-details" style="margin-left: 10px;">
                  <div class="seller-name">Jeremy Rellama</div>
                  <div class="seller-link">Seller details</div>
                </div>
              </div>
              
              <button class="message-button">Message</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  </main>

  <script src="dark-mode.js"></script>


<script>
  const photoInput = document.getElementById('photo-input');
  const photoUploader = document.getElementById('photo-uploader');
  const photoPreview = document.getElementById('photo-preview');
  const photoCount = document.getElementById('photo-count');

  let photos = [];

  function updatePhotoCount() {
    photoCount.textContent = `Photos: ${photos.length} / 1 - You can add up to 1 photos.`;
  }

  function handleFiles(files) {
    for (const file of files) {
      if (photos.length >= 1) break;
      if (!file.type.startsWith('image/')) continue;

      const reader = new FileReader();
      reader.onload = function (e) {
        const img = document.createElement('img');
        img.src = e.target.result;
        photoPreview.appendChild(img);
      };
      reader.readAsDataURL(file);
      photos.push(file);
    }
    updatePhotoCount();
  }

  photoUploader.addEventListener('click', () => {
    photoInput.click();
  });

  photoInput.addEventListener('change', () => {
    handleFiles(photoInput.files);
  });

  photoUploader.addEventListener('dragover', (e) => {
    e.preventDefault();
    photoUploader.style.borderColor = '#1877f2';
  });

  photoUploader.addEventListener('dragleave', () => {
    photoUploader.style.borderColor = '#dadde1';
  });

  photoUploader.addEventListener('drop', (e) => {
    e.preventDefault();
    photoUploader.style.borderColor = '#dadde1';
    handleFiles(e.dataTransfer.files);
  });

  updatePhotoCount();
</script>


<!-- preview -->
<script>
  const titleInput = document.querySelector('input[placeholder="Title"]');
  const priceInput = document.querySelector('input[placeholder="Price"]');
  const brandInput = document.querySelector('#brand-input');
  const locationInput = document.querySelector('#location-input');

  const previewContent = document.querySelector('.preview-content');
  const filledPreview = document.getElementById('filled-preview');

  const previewTitle = document.querySelector('.preview-title');
  const previewPrice = document.querySelector('.preview-price');
  const previewCategory = document.querySelector('.preview-category');
  const previewCondition = document.querySelector('.preview-condition');
  const previewBrand = document.querySelector('.preview-brand');
  const previewLocation = document.querySelector('.preview-location');

  const nextButton = document.querySelector('.next-btn');

  const categoryDropdown = document.getElementById('category-dropdown');
  const conditionDropdown = document.getElementById('condition-dropdown');
  const subcategoryDropdown = document.getElementById('subcategory-dropdown');
  const subcategoryMenu = document.getElementById('subcategory-menu');

  let selectedCategory = '';
  let selectedCondition = '';
  let selectedSubcategory = '';

  function updatePreview() {
    const title = titleInput.value.trim();
    const price = priceInput.value.trim();
    const brand = brandInput.value.trim();
    const location = locationInput.value.trim();

    if (title || price || selectedCategory || selectedCondition || selectedSubcategory || brand || location) {
      previewContent.style.display = 'none';
      filledPreview.style.display = 'block';

      if (title) previewTitle.textContent = title;
      if (price) previewPrice.textContent = `₱${price}`;
      if (selectedCategory) previewCategory.textContent = selectedCategory;
      if (selectedCondition) previewCondition.textContent = selectedCondition;
      if (selectedSubcategory) previewCategory.textContent += ` - ${selectedSubcategory}`;
      if (brand) previewBrand.textContent = brand;
      if (location) previewLocation.textContent = location;

      if (title && price) {
        nextButton.classList.add('active');
      }
    } else {
      previewContent.style.display = 'flex';
      filledPreview.style.display = 'none';
      nextButton.classList.remove('active');
    }
  }

  function setupDropdown(dropdownElement, previewElement, setValueCallback) {
    const header = dropdownElement.querySelector('.dropdown-header');
    const items = dropdownElement.querySelectorAll('.dropdown-item');

    header.addEventListener('click', () => {
      dropdownElement.classList.toggle('open');
    });

    items.forEach(item => {
      item.addEventListener('click', () => {
        const displayText = item.textContent;
        header.querySelector('.dropdown-text').textContent = displayText;
        setValueCallback(displayText);
        dropdownElement.classList.remove('open');
        updatePreview();
      });
    });

    document.addEventListener('click', (e) => {
      if (!dropdownElement.contains(e.target)) {
        dropdownElement.classList.remove('open');
      }
    });
  }

  function populateSubcategories(categoryId) {
    subcategoryMenu.innerHTML = '';
    if (subcategories[categoryId]) {
      subcategories[categoryId].forEach(subcat => {
        const item = document.createElement('div');
        item.classList.add('dropdown-item');
        item.textContent = subcat.name;
        item.dataset.value = subcat.subcategory_id;
        item.addEventListener('click', () => {
          const displayText = item.textContent;
          subcategoryDropdown.querySelector('.dropdown-text').textContent = displayText;
          document.getElementById('selected-subcategory').value = subcat.subcategory_id;
          subcategoryDropdown.classList.remove('open');
        });
        subcategoryMenu.appendChild(item);
      });
    }
  }

  setupDropdown(categoryDropdown, previewCategory, value => selectedCategory = value);
  setupDropdown(conditionDropdown, previewCondition, value => selectedCondition = value);
  setupDropdown(subcategoryDropdown, previewCategory, value => selectedSubcategory = value);

  // Update subcategories when category changes
  categoryDropdown.querySelectorAll('.dropdown-item').forEach(item => {
    item.addEventListener('click', () => {
      const categoryId = item.dataset.value;
      document.getElementById('selected-category').value = categoryId;
      populateSubcategories(categoryId);
    });
  });

  // JavaScript to ensure the selected condition is set in the hidden input field
  const selectedConditionInput = document.getElementById('selected-condition');

  conditionDropdown.querySelectorAll('.dropdown-item').forEach(item => {
      item.addEventListener('click', () => {
          const conditionValue = item.getAttribute('data-value');
          selectedConditionInput.value = conditionValue; // Set the value in the hidden input field
      });
  });

  // Ensure the selected condition value is set in the hidden input field before form submission
  const form = document.querySelector('form');
  form.addEventListener('submit', () => {
      const selectedConditionInput = document.getElementById('selected-condition');
      const conditionDropdown = document.getElementById('condition-dropdown');
      const selectedConditionText = conditionDropdown.querySelector('.dropdown-text').textContent;

      // Map condition text to corresponding values
      const conditionMap = {
          'New': 'new',
          'Good': 'good',
          'Fair': 'fair',
          'Poor': 'poor'
      };

      selectedConditionInput.value = conditionMap[selectedConditionText] || '';
  });

  titleInput.addEventListener('input', updatePreview);
  priceInput.addEventListener('input', updatePreview);
  brandInput.addEventListener('input', updatePreview);
  locationInput.addEventListener('input', updatePreview);
</script>


</body>
</html>
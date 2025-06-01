<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include './db/db_conn.php';
    include './auth/auth.php';

    $businessName = $_POST['business_name'] ?? '';
    $linkUrl = $_POST['link_url'] ?? '';
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $userId = $_SESSION['user_id'] ?? null; // Assuming user_id is stored in the session

    if (!$userId) {
        echo "<p>User not logged in. Please log in to add an advertisement.</p>";
        exit;
    }

    // Calculate the number of days and the cost
    $startDateTime = new DateTime($startDate);
    $endDateTime = new DateTime($endDate);
    $interval = $startDateTime->diff($endDateTime);
    $days = $interval->days;

    // Define cost per day
    $costPerDay = 100; // Example: ₱100 per day
    $totalCost = $days * $costPerDay;

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "ads/";
        $fileName = basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow only certain file formats
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($fileType), $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                // Begin transaction
                $conn->begin_transaction();

                try {
                    // Insert ad details into the database
                    $stmt = $conn->prepare("INSERT INTO sponsored_ads (business_name, image_url, link_url, start_date, end_date, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                    $stmt->bind_param("sssssi", $businessName, $targetFilePath, $linkUrl, $startDate, $endDate, $isActive);

                    if (!$stmt->execute()) {
                        throw new Exception("Failed to add advertisement.");
                    }

                    // Insert transaction details into the database
                    $adId = $conn->insert_id; // Get the last inserted ad ID
                    $stmt = $conn->prepare("INSERT INTO transactions (user_id, subscription_id, type, amount, transaction_date) VALUES (?, NULL, 'advertisement', ?, NOW())");
                    $stmt->bind_param("id", $userId, $totalCost);

                    if (!$stmt->execute()) {
                        throw new Exception("Failed to record transaction.");
                    }

                    // Commit transaction
                    $conn->commit();
                    echo "<p>Advertisement added successfully! Total cost: ₱" . number_format($totalCost, 2) . "</p>";
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $conn->rollback();
                    echo "<p>" . $e->getMessage() . "</p>";
                }

                $stmt->close();
            } else {
                echo "<p>Failed to upload image. Please try again.</p>";
            }
        } else {
            echo "<p>Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.</p>";
        }
    } else {
        echo "<p>Please upload an image.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Advertisement</title>
    <link rel="stylesheet" href="add_advertisement.css">
      <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <link href="https://fonts.cdnfonts.com/css/futura-std-4" rel="stylesheet">

<script>
        function calculateTotalCost() {
            const costPerDay = 100; // Example: ₱100 per day
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);

            if (startDate && endDate && startDate <= endDate) {
                const timeDiff = endDate - startDate;
                const days = Math.ceil(timeDiff / (1000 * 60 * 60 * 24)) + 1; // Include both start and end dates
                const totalCost = days * costPerDay;
                document.getElementById('total_cost').textContent = `Total Cost: ₱${totalCost.toLocaleString()}`;
            } else {
                document.getElementById('total_cost').textContent = 'Total Cost: ₱0';
            }
        }
    </script>
</head>
<body>

<?php include 'navbar.php'; ?>

<button id="dark-mode-toggle" class="dark-mode-toggle">
    <span>🌙</span> 
</button>



  <main>
    <section>
     <div class="form-container">
  <div class="form-sidebar">
    <h2>Advertisement Info</h2>
    <p>Fill in your ad details and image for submission.</p>

    <h2 style="margin-top: 3rem;">Preview</h2>
    <div id="imagePreviewContainer">
      <img id="imagePreview" src="#" alt="Image Preview">
    </div>
  </div>

  <div class="form-main">
    <form id="adForm" method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="business_name">Business Name *</label>
        <input type="text" id="business_name" name="business_name" required>
      </div>

      <div class="form-group">
        <label for="image">Image *</label>
        <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)" required>
      </div>

      <div class="form-group">
        <label for="link_url">Link URL *</label>
        <input type="url" id="link_url" name="link_url" required>
      </div>

      <div class="form-group">
        <label for="start_date">Start Date *</label>
        <input type="date" id="start_date" name="start_date" onchange="calculateTotalCost()" required>
      </div>

      <div class="form-group">
        <label for="end_date">End Date *</label>
        <input type="date" id="end_date" name="end_date" onchange="calculateTotalCost()" required>
      </div>

      <div class="form-group">
        <label><input type="checkbox" id="is_active" name="is_active"> Active</label>
      </div>

      <div class="form-group">
        <p id="total_cost" style="font-size: 1.4rem; font-weight: bold;">Total Cost: ₱0</p>
      </div>

      <div class="form-footer">
        <button type="button" class="btn btn-secondary" onclick="reset()">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="openPaymentPanel()">Submit</button>
      </div>
    </form>
  </div>
</div>

<!-- Payment Panel Modal -->
<div id="paymentPanelModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
  <div class="modal-content" style="background:#fff; padding:2rem; border-radius:10px; max-width:500px; margin:auto;">
    <h2 style="margin-bottom:1rem; text-align:center;">Payment Method</h2>
    <div style="display:flex; justify-content:space-around; margin-bottom:1.5rem;">
      <label>
        <input type="radio" name="payment_method" value="bdo" checked onchange="showBankDetails('bdo')">
        <img src="icons/bdo.png" alt="BDO" style="height:40px;">
      </label>
      <label>
        <input type="radio" name="payment_method" value="bpi" onchange="showBankDetails('bpi')">
        <img src="icons/bpi.png" alt="BPI" style="height:40px;">
      </label>
      <label>
        <input type="radio" name="payment_method" value="other" onchange="showBankDetails('other')">
        <img src="icons/bank.png" alt="Other Bank" style="height:40px;">
      </label>
    </div>
    <div id="bankDetails_bdo" class="bank-details">
      <strong>Bank Name:</strong> BDO<br>
      <strong>Account Name:</strong> QuickDeal Marketplace<br>
      <strong>Account Number:</strong> 1234-5678-9012
      <div class="card-fields" style="margin-top:1rem;">
        <label>Card Number *</label>
        <input type="text" id="card_number_bdo" maxlength="19" placeholder="Card Number" style="width:100%;margin-bottom:8px;">
        <label>Cardholder *</label>
        <input type="text" id="card_holder_bdo" placeholder="Cardholder Name" style="width:100%;margin-bottom:8px;">
        <div style="display:flex; gap:8px;">
          <div style="flex:1;">
            <label>Expiry</label>
            <input type="text" id="expiry_bdo" maxlength="5" placeholder="MM/YY" style="width:100%;">
          </div>
          <div style="flex:1;">
            <label>CVC</label>
            <input type="text" id="cvc_bdo" maxlength="4" placeholder="CVC" style="width:100%;">
          </div>
        </div>
      </div>
    </div>
    <div id="bankDetails_bpi" class="bank-details" style="display:none;">
      <strong>Bank Name:</strong> BPI<br>
      <strong>Account Name:</strong> QuickDeal Marketplace<br>
      <strong>Account Number:</strong> 9876-5432-1098
      <div class="card-fields" style="margin-top:1rem;">
        <label>Card Number *</label>
        <input type="text" id="card_number_bpi" maxlength="19" placeholder="Card Number" style="width:100%;margin-bottom:8px;">
        <label>Cardholder *</label>
        <input type="text" id="card_holder_bpi" placeholder="Cardholder Name" style="width:100%;margin-bottom:8px;">
        <div style="display:flex; gap:8px;">
          <div style="flex:1;">
            <label>Expiry</label>
            <input type="text" id="expiry_bpi" maxlength="5" placeholder="MM/YY" style="width:100%;">
          </div>
          <div style="flex:1;">
            <label>CVC</label>
            <input type="text" id="cvc_bpi" maxlength="4" placeholder="CVC" style="width:100%;">
          </div>
        </div>
      </div>
    </div>
    <div id="bankDetails_other" class="bank-details" style="display:none;">
      <strong>Bank Name:</strong> <input type="text" id="other_bank_name" placeholder="Enter bank name" style="width:60%;"><br>
      <strong>Account Name:</strong> <input type="text" id="other_account_name" placeholder="Enter account name" style="width:60%;"><br>
      <strong>Account Number:</strong> <input type="text" id="other_account_number" placeholder="Enter account number" style="width:60%;">
      <div class="card-fields" style="margin-top:1rem;">
        <label>Card Number *</label>
        <input type="text" id="card_number_other" maxlength="19" placeholder="Card Number" style="width:100%;margin-bottom:8px;">
        <label>Cardholder *</label>
        <input type="text" id="card_holder_other" placeholder="Cardholder Name" style="width:100%;margin-bottom:8px;">
        <div style="display:flex; gap:8px;">
          <div style="flex:1;">
            <label>Expiry</label>
            <input type="text" id="expiry_other" maxlength="5" placeholder="MM/YY" style="width:100%;">
          </div>
          <div style="flex:1;">
            <label>CVC</label>
            <input type="text" id="cvc_other" maxlength="4" placeholder="CVC" style="width:100%;">
          </div>
        </div>
      </div>
    </div>
    <p id="modal_total_cost" style="font-weight:bold; margin:1rem 0;">Total Cost: ₱0</p>
    <div style="text-align:right;">
      <button class="btn btn-primary" onclick="submitPayment()">Confirm Payment</button>
      <button class="btn btn-secondary" onclick="closePaymentPanel()">Cancel</button>
    </div>
  </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal">
  <div class="modal-content">
    <p style="font-size: 1.5rem; margin-bottom: 1.5rem;">Are you sure you want to submit?</p>
    <button class="btn btn-primary" onclick="proceedToPayment()">Yes</button>
    <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
  </div>
</div>

    </section>
  </main>

 <script>
  function calculateTotalCost() {
    const costPerDay = 100;
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    if (startDate && endDate && startDate <= endDate) {
      const timeDiff = endDate - startDate;
      const days = Math.ceil(timeDiff / (1000 * 60 * 60 * 24)) + 1;
      const totalCost = days * costPerDay;
      document.getElementById('total_cost').textContent = `Total Cost: ₱${totalCost.toLocaleString()}`;
      document.getElementById('modal_total_cost').textContent = `Total Cost: ₱${totalCost.toLocaleString()}`;
    } else {
      document.getElementById('total_cost').textContent = 'Total Cost: ₱0';
      document.getElementById('modal_total_cost').textContent = 'Total Cost: ₱0';
    }
  }

  function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
      const output = document.getElementById('imagePreview');
      output.src = reader.result;
      document.getElementById('imagePreviewContainer').style.display = 'block';
      output.style.opacity = 0;
      setTimeout(() => output.style.opacity = 1, 100);
    };
    reader.readAsDataURL(event.target.files[0]);
  }

  function openModal() {
    document.getElementById('confirmationModal').style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('confirmationModal').style.display = 'none';
  }

  function proceedToPayment() {
    document.getElementById('confirmationModal').style.display = 'none';
    openPaymentPanel();
  }

  function openPaymentPanel() {
    calculateTotalCost();
    document.getElementById('paymentPanelModal').style.display = 'flex';
  }

  function closePaymentPanel() {
    document.getElementById('paymentPanelModal').style.display = 'none';
  }

  function submitPayment() {
    const method = document.querySelector('input[name="payment_method"]:checked').value;
    let cardNumber = '', cardHolder = '', expiry = '', cvc = '';
    if (method === 'bdo') {
      cardNumber = document.getElementById('card_number_bdo').value.trim();
      cardHolder = document.getElementById('card_holder_bdo').value.trim();
      expiry = document.getElementById('expiry_bdo').value.trim();
      cvc = document.getElementById('cvc_bdo').value.trim();
    } else if (method === 'bpi') {
      cardNumber = document.getElementById('card_number_bpi').value.trim();
      cardHolder = document.getElementById('card_holder_bpi').value.trim();
      expiry = document.getElementById('expiry_bpi').value.trim();
      cvc = document.getElementById('cvc_bpi').value.trim();
    } else if (method === 'other') {
      const bankName = document.getElementById('other_bank_name').value.trim();
      const accountName = document.getElementById('other_account_name').value.trim();
      const accountNumber = document.getElementById('other_account_number').value.trim();
      if (!bankName || !accountName || !accountNumber) {
        alert('Please fill in all other bank details.');
        return;
      }
      cardNumber = document.getElementById('card_number_other').value.trim();
      cardHolder = document.getElementById('card_holder_other').value.trim();
      expiry = document.getElementById('expiry_other').value.trim();
      cvc = document.getElementById('cvc_other').value.trim();
      addOrUpdateHidden('other_bank_name', bankName);
      addOrUpdateHidden('other_account_name', accountName);
      addOrUpdateHidden('other_account_number', accountNumber);
    }
    if (!cardNumber || !cardHolder || !expiry || !cvc) {
      alert('Please fill in all card details.');
      return;
    }
    addOrUpdateHidden('payment_method', method);
    addOrUpdateHidden('card_number', cardNumber);
    addOrUpdateHidden('card_holder', cardHolder);
    addOrUpdateHidden('expiry', expiry);
    addOrUpdateHidden('cvc', cvc);
    document.getElementById('paymentPanelModal').style.display = 'none';
    document.getElementById('adForm').submit();
  }

  function addOrUpdateHidden(name, value) {
    let input = document.getElementById(name);
    if (!input) {
      input = document.createElement('input');
      input.type = 'hidden';
      input.id = name;
      input.name = name;
      document.getElementById('adForm').appendChild(input);
    }
    input.value = value;
  }

  function reset() {
    document.getElementById('adForm').reset();
    document.getElementById('imagePreviewContainer').style.display = 'none';
    document.getElementById('total_cost').textContent = 'Total Cost: ₱0';
    document.getElementById('modal_total_cost').textContent = 'Total Cost: ₱0';
  }

  function showBankDetails(bank) {
    document.getElementById('bankDetails_bdo').style.display = (bank === 'bdo') ? 'block' : 'none';
    document.getElementById('bankDetails_bpi').style.display = (bank === 'bpi') ? 'block' : 'none';
    document.getElementById('bankDetails_other').style.display = (bank === 'other') ? 'block' : 'none';
  }
</script>

<style>
.bank-details input[type="text"] {
  margin: 2px 0 8px 0;
  padding: 4px;
  font-size: 1rem;
}
.bank-details {
  margin-bottom: 1rem;
}
</style>

<footer><?php include 'footer.php'; ?>
</footer>  

<script src="bubble.js"></script>
<script src="dark-mode.js"></script>
</body>
</html>

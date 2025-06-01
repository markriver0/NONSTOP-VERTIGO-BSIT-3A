<?php
include './db/db_conn.php';
include './auth/auth.php';

$loggedInUserId = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $loggedInUserId) {
    $paymentMethod = $_POST['payment_method'] ?? '';
    $accountName = $_POST['account_name'] ?? '';
    $reference = $_POST['reference'] ?? '';
    $mobileNumber = $_POST['mobile_number'] ?? '';
    $networkProvider = $_POST['network_provider'] ?? '';
    $bankOption = $_POST['bank_option'] ?? '';
    $bankName = $_POST['bank_name'] ?? '';

    $amount = 299.00;

    // Insert subscription
    $stmt = $conn->prepare("INSERT INTO subscriptions (user_id, plan, start_date, end_date) VALUES (?, 'premium', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY))");
    $stmt->bind_param("i", $loggedInUserId);

    if ($stmt->execute()) {
        $subscriptionId = $conn->insert_id;

        // Record transaction
        $transactionStmt = $conn->prepare("INSERT INTO transactions (user_id, subscription_id, type, amount, transaction_date) VALUES (?, ?, 'subscription', ?, NOW())");
        $transactionStmt->bind_param("iid", $loggedInUserId, $subscriptionId, $amount);

        if ($transactionStmt->execute()) {
            echo "<script>alert('Payment Successful! Premium Activated.'); window.location.href='listing.php';</script>";
            exit;
        } else {
            echo "<script>alert('Failed to record transaction.');</script>";
        }
    } else {
        echo "<script>alert('Failed to activate subscription.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cashier</title>
    <style>
        * {
  box-sizing: border-box;
  padding: 0;
  margin: 0;

  font-family: var(--ff--primary)
}

*::before,
*::after {
  box-sizing: border-box;
}

html {
  font-size: 62.5%;
  font-family: var(--ff--primary);
}

img, svg {
  display: block;
  max-width: 100%;
}

:root {
  --color--primary--100: #fafafa;
  --color--primary--200: rgb(255, 216, 77);
  --color--primary--300: rgba(251, 105, 27);
  --color--primary--400: rgb(2, 87, 184);
  --color--primary--500: #252525;

  --ff--primary: "Poppins", sans-serif;
  --ff--futura: 'Futura Std',sans-serif;    
  --ff--secondary: ;

  --fz--header: 5rem;
}

/* Dark Mode Variables */
[data-theme="dark"] {
  --color--primary--100: #252525; 
  --color--primary--200: #1a1a1a; 
  --color--primary--500: #fafafa;
  --color--primary--400: rgba(251, 105, 27);
}

/* Dark Mode Toggle Button */
.dark-mode-toggle {
  position: fixed;
  top: 20px;
  right: 20px;
  background-color: var(--color--primary--300);
  color: var(--color--primary--100);
  border: none;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.dark-mode-toggle:hover {
  background-color: var(--color--primary--500);
  color: var(--color--primary--100);
}



body {
  background-color: var(--color--primary--100);
  color: var(--color--primary--500);

  transition: background-color 0.5s ease, color 0.5s ease;
}

h1{
  font-family: var(--ff--futura);
  font-weight: 700;
}

main{
  background-color: var(--color--primary--100);
  width: 90%;
  margin-inline: auto;

  height: 80dvh;

  display: flex;
  align-items: center;
  justify-content: center;
}

section{
  margin-bottom: 4em;

  transition: background-color 0.5s ease, color 0.5s ease;
}

        .cashier-box {
            background: white;
            margin: 4rem auto; 
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .cashier-box h2 { 
            text-align: center;
             margin-bottom: 1rem; 
             font-size: 3rem !important;
        }
        label { display: block;
             margin-top: 1rem;
            font-size: 1.6rem;
            }
        input[type="text"], select {
            width: 100%; 
            padding: 0.7rem; 
            margin-top: 0.3rem;
            border-radius: 8px;
             border: 1px solid #ccc;
        }
        .submit-btn {
            background: #5e4ce6; color: white; border: none;
            padding: 0.8rem 2rem; margin-top: 2rem; width: 100%;
            border-radius: 30px; cursor: pointer;
        }
        .radio-group {
            margin-top: 1rem;
        }
    </style>
    <script>
        function showFields() {
            const method = document.querySelector('input[name="payment_method"]:checked')?.value;
            const fields = document.getElementById("fields");
            fields.innerHTML = "";

            if (method === "gcash") {
                fields.innerHTML = `
                    <label>GCash Mobile Number</label>
                    <input type="text" name="mobile_number" required>
                    <label>Reference Number</label>
                    <input type="text" name="reference" required>
                    <div style="margin-top:1rem;">
                        <strong>Or Scan QR to Pay:</strong>
                        <div style="margin-top:0.5rem;">
                            <img src="./uploads/gcash-payment-1024.jpeg" alt="GCash QR" style="width:200px;">
                        </div>
                    </div>
                `;
            } else if (method === "network") {
                fields.innerHTML = `
                    <label>Mobile Number</label>
                    <input type="text" name="mobile_number" required>
                    <label>Network Provider</label>
                    <select name="network_provider" required>
                        <option value="">-- Select Provider --</option>
                        <option value="Smart">Smart</option>
                        <option value="TNT">TNT</option>
                        <option value="Globe">Globe</option>
                        <option value="TM">TM</option>
                    </select>
                    <label>Reference Number</label>
                    <input type="text" name="reference" required>
                `;
            } else if (method === "bank") {
                fields.innerHTML = `
                    <label>Bank</label>
                    <select name="bank_option" id="bank_option" onchange="showBankInput()" required>
                        <option value="">-- Select Bank --</option>
                        <option value="BDO">BDO</option>
                        <option value="Other">Other</option>
                    </select>
                    <div id="otherBankField" style="display:none;">
                        <label>Enter Bank Name</label>
                        <input type="text" name="bank_name">
                    </div>
                    <label>Account Name</label>
                    <input type="text" name="account_name" required>
                    <label>Reference Number</label>
                    <input type="text" name="reference" required>
                `;
            }
        }

        function showBankInput() {
            const bankOption = document.getElementById("bank_option").value;
            const otherField = document.getElementById("otherBankField");
            otherField.style.display = (bankOption === "Other") ? "block" : "none";
        }
    </script>
</head>
<body>
<?php include 'navbar.php'; ?>

<main>
<div class="cashier-box">
    <h2>Complete Your Payment</h2>
    <form method="POST">
        <label>Select Payment Method</label>
        <div class="radio-group">
            <label><input type="radio" name="payment_method" value="gcash" onchange="showFields()"> GCash</label><br>
            <label><input type="radio" name="payment_method" value="network" onchange="showFields()"> Network Provider</label><br>
            <label><input type="radio" name="payment_method" value="bank" onchange="showFields()"> Bank Transfer</label>
        </div>

        <div id="fields"></div>

        <button type="submit" class="submit-btn">Pay ₱299 & Activate</button>
    </form>
</div>
</main>
</body>
</html>

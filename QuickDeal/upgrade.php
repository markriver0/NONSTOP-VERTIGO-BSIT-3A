<?php
include './db/db_conn.php';
include './auth/auth.php';

$loggedInUserId = $_SESSION['user_id'] ?? null;
$isPremium = false;

if ($loggedInUserId) {
    $checkStmt = $conn->prepare("SELECT * FROM subscriptions WHERE user_id = ? AND plan = 'premium' AND end_date >= CURDATE() AND is_active = 1 ORDER BY end_date DESC LIMIT 1");
    $checkStmt->bind_param("i", $loggedInUserId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $isPremium = $result->num_rows > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upgrade to Premium</title>
    
    <style>
* {
  box-sizing: border-box;
  padding: 0;
  margin: 0;

  font-family: var(--ff--primary);
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
  font-weight: 900;
}

main{
  background-color: var(--color--primary--100);
  width: 90%;
  margin-inline: auto;
}

section{
  margin-bottom: 4em;

  transition: background-color 0.5s ease, color 0.5s ease;
}


        .pricing-section {
            margin: 3rem auto;            
            padding: 2rem;

            height: 80dvh;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);

            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;

            
        }
        .pricing-header {
            text-align: center;
        }
        .pricing-header h1 {
            font-size: 2rem;
        }
        .pricing-header span {
            color: #5e4ce6;
        }
        .plan-card {
            background-color: #5e4ce6;
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin: 2rem auto 0;
            text-align: center;
            position: relative;
                        display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;

            width: 100%;
            height: 100%

           
        }
        .plan-card span.badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: gold;
            color: black;
            padding: .5em 1em;
            font-size: 1.2rem;
            border-radius: 10px;
        }
        .plan-card h2 {
            margin-bottom: 0.5em;
            font-size: 4rem;
        }
        .plan-card p.price {
            font-size: 2.4rem;
            font-weight: bold;
            margin: 0;
        }
        .plan-card ul, p {
            list-style: none;
            padding: 0;
            margin: 1rem 0;
            font-size: 1.2rem;
        }
        .plan-card button {
            background-color: white;
            color: #5e4ce6;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .plan-card button:hover {
            background-color: #eee;
        }
        .info-box {
            margin: 2rem auto;
            background: #e6f7e6;
            color: #2a7a2a;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            max-width: 500px;
            font-size: 1rem;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>


<main>
<section class="pricing-section">
    <div class="pricing-header">
        <h1>Upgrade to <span>Premium</span></h1>
        <p>Enjoy <strong>unlimited listings</strong> and <strong>featured ads</strong> for 30 days</p>
    </div>

    <?php if (!$isPremium): ?>
        <div class="plan-card">
            
            <span class="badge">Most Popular</span>
            <div class="flex">
            <h2>Professional</h2>
            <p class="price">₱299</p>
            <p style="font-size: 0.9rem;">30 days access</p>
            <ul>
                <li>✔ Unlimited Listings</li>
                <li>✔ Featured Ad Slots</li>
                <li>✔ Premium Visibility</li>
            </ul>
            <form method="GET" action="cashier.php">
                <button type="submit">Upgrade Now</button>
            </form>
            </div>
        </div>
    <?php else: ?>
        <div class="info-box">
            🎉 You already have an active Premium plan!<br>
            You can enjoy all premium features until your plan expires.
        </div>
    <?php endif; ?>
</section>

</main>

</body>
</html>

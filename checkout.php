<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

$error_message = '';
$success_message = '';

if (isset($_POST['order'])) {
    // Sanitize and validate input
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $number = mysqli_real_escape_string($conn, trim($_POST['number']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $method = mysqli_real_escape_string($conn, $_POST['method']);
    $flat = mysqli_real_escape_string($conn, trim($_POST['flat']));
    $street = mysqli_real_escape_string($conn, trim($_POST['street']));
    $city = mysqli_real_escape_string($conn, trim($_POST['city']));
    $state = mysqli_real_escape_string($conn, trim($_POST['state']));
    $country = mysqli_real_escape_string($conn, trim($_POST['country']));
    $pin_code = mysqli_real_escape_string($conn, trim($_POST['pin_code']));
    
    // Validate inputs
    if (empty($name) || strlen($name) < 3) {
        $error_message = "Please enter a valid name (at least 3 characters).";
    } elseif (!preg_match('/^\d{10}$/', $number)) {
        $error_message = "Please enter a valid 10-digit phone number.";
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } elseif (empty($flat) || empty($street) || empty($city) || empty($country) || !preg_match('/^\d{6}$/', $pin_code)) {
        $error_message = "Please fill all address fields correctly and enter a valid pin code.";
    } else {
        // Build the address
        $address = "flat no. $flat, $street, $city, $state, $country - $pin_code";
        $placed_on = date('d-M-Y');

        $cart_total = 0;
        $cart_products = [];

        $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        if (mysqli_num_rows($cart_query) > 0) {
            while ($cart_item = mysqli_fetch_assoc($cart_query)) {
                $cart_products[] = $cart_item['name'] . ' (' . $cart_item['quantity'] . ') ';
                $sub_total = ($cart_item['price'] * $cart_item['quantity']);
                $cart_total += $sub_total;
            }
        }

        $total_products = implode(',', $cart_products);

        $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name = '$name' AND number = '$number' AND email = '$email' AND method = '$method' AND address = '$address' AND total_products = '$total_products' AND total_price = '$cart_total'") or die('query failed');

        if ($cart_total == 0) {
            $error_message = 'Enter your details......';
        } elseif (mysqli_num_rows($order_query) > 0) {
            $error_message = 'Order placed already!';
        } else {
            mysqli_query($conn, "INSERT INTO `orders` (user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES ('$user_id', '$name', '$number', '$email', '$method', '$address', '$total_products', '$cart_total', '$placed_on')") or die('query failed');
            mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
            $success_message = 'Thank you for your order!';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom admin CSS file link -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        .message-box {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-size: 16px;
            display: flex;
            align-items: center;
        }

        .message-box.success {
            background-color: #d4edda; /* Light green */
            color: #155724; /* Dark green */
            border: 1px solid #c3e6cb; /* Darker green */
        }

        .message-box.error {
            background-color: #f8d7da; /* Light red */
            color: #721c24; /* Dark red */
            border: 1px solid #f5c6cb; /* Darker red */
        }
    </style>
</head>
<body>
   
<?php @include 'header.php'; ?>

<section class="heading">
    <h3>Checkout Order</h3>
    <p> <a href="home.php">Home</a> / Checkout </p>
</section>

<section class="display-order">
    <?php
        $grand_total = 0;
        $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        if (mysqli_num_rows($select_cart) > 0) {
            while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
                $grand_total += $total_price;
    ?>    
    <p> <?php echo $fetch_cart['name'] ?> <span>(<?php echo '₹'.$fetch_cart['price'].'/-'.' x '.$fetch_cart['quantity'] ?>)</span> </p>
    <?php
            }
        } else {
            echo '<p class="empty">Your cart is empty</p>';
        }
    ?>
    <div class="grand-total">Grand Total: <span>₹<?php echo $grand_total; ?>/-</span></div>
</section>

<section class="checkout">

    <form action="" method="POST">

        <h3>Place Your Order</h3>


        <?php
        // Display error or success messages
        if ($error_message) {
            echo '<div class="message-box error">' . $error_message . '</div>';
        } elseif ($success_message) {
            echo '<div class="message-box success">' . $success_message . '</div>';
        }
        ?>


        <div class="flex">
            <div class="inputBox">
                <span>Your Name:</span>
                <input type="text" name="name" required placeholder="Enter your name">
            </div>
            <div class="inputBox">
                <span>Your Number:</span>
                <input type="text" name="number" required placeholder="Enter your number" maxlength="10" pattern="\d{10}" title="Please enter a valid 10-digit phone number">
            </div>
            <div class="inputBox">
                <span>Your Email:</span>
                <input type="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="inputBox">
                <span>Payment Method:</span>
                <select name="method" required>
                    <option value="cash on delivery">Cash on Delivery</option>
                    <option value="credit card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="paytm">Paytm</option>
                </select>
            </div>
            <div class="inputBox">
                <span>Address Line 01:</span>
                <input type="text" name="flat" required placeholder="e.g. Flat No.">
            </div>
            <div class="inputBox">
                <span>Address Line 02:</span>
                <input type="text" name="street" required placeholder="e.g. Street Name">
            </div>
            <div class="inputBox">
                <span>City:</span>
                <input type="text" name="city" required placeholder="e.g. Surendranagar">
            </div>
            <div class="inputBox">
                <span>State:</span>
                <input type="text" name="state" required placeholder="e.g. Gujarat">
            </div>
            <div class="inputBox">
                <span>Country:</span>
                <input type="text" name="country" required placeholder="e.g. India">
            </div>
            <div class="inputBox">
                <span>Pin Code:</span>
                <input type="text" name="pin_code" required placeholder="e.g. 363001" maxlength="6" pattern="\d{6}" title="Please enter a valid 6-digit pin code">
            </div>
        </div>

        <input type="submit" name="order" value="Order Now" class="btn">

        
    </form>

</section>

<?php @include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>

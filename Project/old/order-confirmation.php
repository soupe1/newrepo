<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation - OSP eStore</title>
    <link rel="stylesheet" href="css/project.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        .confirmation-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .invoice {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .invoice h2 {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .invoice-items {
            margin: 15px 0;
        }
        
        .invoice-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        
        .invoice-summary {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        
        .delivery-summary {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .delivery-message {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    
    // In a real implementation, check if user is logged in
    // if (!isset($_SESSION['user_id'])) {
    //     header("Location: sign-in.php");
    //     exit();
    // }
    

    $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
    
    // If no order ID is provided or order info is not in session, redirect to home
    if (!$order_id || !isset($_SESSION['order_info'])) {
        header("Location: main.php");
        exit();
    }
    

    $order = $_SESSION['order_info'];
    

    $items = $order['items'];
    

    $subtotal = $order['subtotal'];
    $shipping = $order['shipping'];
    $total = $order['total_price'];
    
    include 'common_files/head-header.php';
    ?>

    <main>
        <div class="confirmation-container">
            <div class="success-message">
                <h1>Order Confirmed!</h1>
                <p>Thank you for your order! Your order has been placed successfully.</p>
            </div>
            
            <div class="invoice">
                <h2>Order #<?= $order_id ?></h2>
                <p><strong>Date:</strong> <?= $order['order_date'] ?></p>
                <p><strong>Customer:</strong> <?= $order['customer_name'] ?></p>
                
                <h3>Order Items</h3>
                <div class="invoice-items">
                    <?php foreach($items as $item): ?>
                    <div class="invoice-item">
                        <span><?= $item['name'] ?> <?= isset($item['quantity']) && $item['quantity'] > 1 ? 'x' . $item['quantity'] : '' ?></span>
                        <span>$<?= number_format($item['price'] * ($item['quantity'] ?? 1), 2) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="invoice-summary">
                    <div class="invoice-item"><strong>Subtotal:</strong> $<?= number_format($subtotal, 2) ?></div>
                    <div class="invoice-item"><strong>Shipping:</strong> $<?= number_format($shipping, 2) ?></div>
                    <div class="invoice-item" style="font-weight: bold;"><strong>Total:</strong> $<?= number_format($total, 2) ?></div>
                </div>
                
                <h3>Delivery Details</h3>
                <p><strong>Delivery From:</strong> <?= $order['warehouse'] ?> (<?= $order['warehouse_address'] ?>)</p>
                <p><strong>Delivery To:</strong> <?= $order['customer_address'] ?></p>
                <p><strong>Estimated Delivery Date:</strong> <?= date('Y-m-d', strtotime($order['delivery_date'])) ?></p>
            </div>

            <div class="delivery-summary">
                <h3>Delivery Information</h3>
                <p>Your order will be delivered from <?= $order['warehouse'] ?> to your address.</p>
                <p>The delivery vehicle will follow the most optimal route for quick and efficient delivery.</p>
            </div>

            <div class="delivery-message">
                <p>Your order will be delivered by Truck #<?= $order['truck_id'] ?>.</p>
                <p>Thank you for shopping with OSP eStore!</p>
                <a href="main.php" class="btn">Continue Shopping</a>
            </div>
        </div>
    </main>

    <script>
        $(document).ready(function() {

            localStorage.removeItem('cart');
        });
    </script>
</body>
</html>

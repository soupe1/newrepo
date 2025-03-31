<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - OSP eStore</title>
    <link rel="stylesheet" href="css/project.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        .checkout-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .order-summary {
            flex: 1;
            min-width: 300px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        
        .checkout-form {
            flex: 2;
            min-width: 400px;
        }
        
        .form-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .cart-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        
        .form-buttons {
            text-align: right;
            margin-top: 20px;
        }
        
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }
        
        .btn-secondary {
            background-color: #f0f0f0;
            color: #333;
        }
        
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        
        .map-container {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        
        .map-frame {
            width: 100%;
            height: 300px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <?php
    // Assume user logged in
    // session_start();
    // if (!isset($_SESSION['user_id'])) {
    //     header("Location: sign-in.php");
    //     exit();
    // }
    
    //assume the user is logged in
    $user_logged_in = true; 
    $user_id = 1;
    
    // Get cart data from session l8er
    // $cart = $_SESSION['cart'] ?? [];
    
    // For now geting the cart from localStorage
    
    include 'common_files/head-header.php';
    ?>

    <main>
        <h1>Checkout</h1>
        
        <div class="checkout-container">
            <div class="order-summary">
                <h2>Order Summary</h2>
                <div id="cart-items">
                    <!--cart items will be loaded here -->
                </div>
                
                <div id="order-totals">
                    <!-- totals will be calculated here -->
                </div>
                

                <div class="map-container" style="margin-top: 20px;">
                    <h3>Delivery Route Map</h3>
                    <div id="map-frame-container">
                        <!-- Map will be loaded here -->
                    </div>
                </div>
            </div>
            
            <div class="checkout-form">
                <form id="checkout-form" action="php/process-checkout.php" method="POST">
                    <div class="form-section">
                        <h2>Select Warehouse Location</h2>
                        <div class="form-group">
                            <label for="warehouse">Warehouse:</label>
                            <select id="warehouse" name="warehouse" required>
                                <option value="1">Downtown Warehouse</option>
                                <option value="2">South Branch</option>
                                <option value="3">North Branch</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="time_slot">Delivery Time Slot:</label>
                            <select id="time_slot" name="time_slot" required>
                                <option value="morning">9:00 AM - 12:00 PM</option>
                                <option value="afternoon">1:00 PM - 5:00 PM</option>
                                <option value="evening">6:00 PM - 9:00 PM</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h2>Delivery Details</h2>
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="city">City:</label>
                            <input type="text" id="city" name="city" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="delivery_date">Delivery Date:</label>
                            <input type="date" id="delivery_date" name="delivery_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h2>Payment Information</h2>
                        <div class="form-group">
                            <label for="card_number">Credit Card Number:</label>
                            <input type="text" id="card_number" name="card_number" placeholder="Enter 16-digit card number" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="expiry">Expiry Date:</label>
                            <input type="month" id="expiry" name="expiry" required min="<?php echo date('Y-m'); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="cvv">CVV:</label>
                            <input type="text" id="cvv" name="cvv" placeholder="3 or 4 digits" required>
                        </div>
                    </div>
                    
                    <input type="hidden" id="cart_data" name="cart_data" value="">
                    
                    <div class="form-buttons">
                        <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
                        <button type="submit" id="confirm-payment" class="btn btn-primary">Confirm Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        $(document).ready(function() {

            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            /*
            // dummy items for testing
            if (cart.length === 0) {
                // remove this l8er
                cart = [
                    {id: 1, name: 'Smartphone XL Pro', price: 899.99, quantity: 1},
                    {id: 2, name: 'Wireless Headphones', price: 129.99, quantity: 1},
                    {id: 3, name: 'Smart Watch', price: 249.99, quantity: 1}
                ];
                localStorage.setItem('cart', JSON.stringify(cart));
            }*/

            $.post({
                    data: {cart_items: cart},
                    url: 'php/db-submit.php'
            }).done(function(resp) {
                displayCart(resp);
            });
            
            function displayCart(cart) {
                cart = JSON.parse(cart);

                let html = '';
                let subtotal = 0;
                
                if (cart.length === 0) {
                    html = '<p>Your cart is empty. <a href="main.php">Continue shopping</a></p>';
                    $('#confirm-payment').prop('disabled', true);
                } else {
                    cart.forEach(item => {
                        const quantity = item.quantity || 1;
                        const itemTotal = item.price * quantity;
                        subtotal += itemTotal;
                        
                        html += `
                            <div class="cart-item">
                                <div class="summary-row">
                                    <span>${item.item_name} x ${quantity}</span>
                                    <span>$${itemTotal.toFixed(2)}</span>
                                </div>
                            </div>
                        `;
                    });
                    

                    const shipping = 15.00;
                    const total = subtotal + shipping;
                    
                    let totalsHtml = `
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>$${subtotal.toFixed(2)}</span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span>$${shipping.toFixed(2)}</span>
                        </div>
                        <div class="total-row">
                            <span>Total:</span>
                            <span>$${total.toFixed(2)}</span>
                        </div>
                    `;
                    
                    $('#order-totals').html(totalsHtml);
                }
                
                $('#cart-items').html(html);
            }
            

            function updateDeliveryMap() {
                const warehouseId = $('#warehouse').val();
                const destinationAddress = $('#address').val() + ', ' + $('#city').val();
                

                const warehouseLocations = {
                    '1': '1507 Yonge St, Toronto, ON',
                    '2': '25 Queens Quay E, Toronto, ON',
                    '3': '1000 Murray Ross Pkwy, North York, ON'
                };
                
                const warehouseAddress = warehouseLocations[warehouseId];
                

                if (warehouseAddress && destinationAddress.length > 5) {
                    const originEncoded = encodeURIComponent(warehouseAddress);
                    const destinationEncoded = encodeURIComponent(destinationAddress);
                    

                    $('#map-frame-container').html(`
                        <iframe 
                            class="map-frame"
                            frameborder="0" 
                            style="border:0"
                            src="https://www.google.com/maps/embed/v1/directions?origin=${originEncoded}&destination=${destinationEncoded}&key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8" 
                            allowfullscreen>
                        </iframe>
                    `);
                }
            }
            

            $('#warehouse').on('change', updateDeliveryMap);
            $('#address, #city').on('change keyup blur', updateDeliveryMap);
            

            updateDeliveryMap();
            
 
            $('#checkout-form').on('submit', function(e) {
                const cardNumber = $('#card_number').val().replace(/\D/g, '');
                if (cardNumber.length !== 16) {
                    alert('Please enter a valid 16-digit credit card number');
                    e.preventDefault();
                    return false;
                }
     
                const cvv = $('#cvv').val();
                if (!/^\d{3,4}$/.test(cvv)) {
                    alert('Please enter a valid CVV (3 or 4 digits)');
                    e.preventDefault();
                    return false;
                }
     
                const deliveryDate = new Date($('#delivery_date').val());
                const today = new Date();
                today.setHours(0, 0, 0, 0);
     
                if (deliveryDate <= today) {
                    alert('Delivery date must be in the future');
                    e.preventDefault();
                    return false;
                }
                
                e.preventDefault();
     
                console.log(cart);

                $.post({
                    data: {cart_items: cart},
                    url: 'php/db-submit.php'
                }).done(function(resp) {
                    $.post({
                    data: {cart_data: resp},
                    url: 'php/process-checkout.php'
                    }).done(function(resp) {
                       console.log(resp);
                       
                       // REDIRECT TO ORDER CONFIRM
                    });
                });
     
                return true;
            });
        });
    </script>
</body>
</html>

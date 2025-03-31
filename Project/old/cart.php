<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart - OSP eStore</title>
    <link rel="stylesheet" href="css/project.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        .cart-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .cart-item-details {
            flex: 3;
        }
        .cart-item-quantity {
            flex: 1;
            text-align: center;
        }
        .cart-item-actions {
            flex: 1;
            text-align: right;
        }
        .quantity {
            width: 60px;
            padding: 5px;
            text-align: center;
        }
        .remove-item {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .cart-summary {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .cart-buttons {
            margin-top: 20px;
            text-align: right;
        }
        button {
            padding: 10px 15px;
            margin-left: 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        #continue-shopping {
            background-color: #f0f0f0;
            color: #333;
        }
        #proceed-to-checkout {
            background-color: #4CAF50;
            color: white;
        }
        #proceed-to-checkout:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <?php 
    // assuming user logged in
    // session_start();
    // $user_logged_in = isset($_SESSION['user_id']);
    
    // assume the user is logged in
    $user_logged_in = true;
    
    include 'common_files/head-header.php';
    ?>

    <main>
        <div class="cart-container">
            <h1>Your Shopping Cart</h1>
            
            <div id="cart-items">
                <!-- will load cart items hear properly l8er -->
            </div>
            
            <div id="cart-summary" class="cart-summary" style="display: none;">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="subtotal">$0.00</span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>$15.00</span>
                </div>
                <div class="summary-row" style="font-weight: bold; margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd;">
                    <span>Total:</span>
                    <span id="total">$15.00</span>
                </div>
            </div>
            
            <div class="cart-buttons">
                <button id="continue-shopping">Continue Shopping</button>
                <button id="proceed-to-checkout" disabled>Proceed to Checkout</button>
            </div>
        </div>
    </main>

    <script>
        $(document).ready(function() {

            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // // data for testing purposes
            // cart = [
            //     {id: 1, name: 'Smartphone XL Pro', price: 899.99, quantity: 1},
            //     {id: 2, name: 'Wireless Headphones', price: 129.99, quantity: 1},
            //     {id: 3, name: 'Smart Watch', price: 249.99, quantity: 1}
            // ];

            $.post({
                data: {cart_items: cart},
                url: 'php/db-submit.php'
            }).done(function(resp) {
                updateCartDisplay(resp);
            });
            
            function updateCartDisplay(data) {
                let html = '';
                let subtotal = 0;
                
                try {
                    if (cart.length == 0) {
                        throw new Error("Cart Empty");
                    }

                    data = JSON.parse(data);

                    data.forEach((item, index) => {
                        const quantity = item.quantity || 1;
                        const itemTotal = item.price * quantity;
                        subtotal += itemTotal;
                        
                        html += `
                            <div class="cart-item">
                                <div class="cart-item-details">
                                    <h3>${item.item_name}</h3>
                                    <p>Unit Price: $${item.price.toFixed(2)}</p>
                                </div>
                                <div class="cart-item-quantity">
                                    <label for="qty-${index}">Quantity:</label>
                                    <input type="number" id="qty-${index}" class="quantity" value="${quantity}" min="1" data-value="${item.item_id}">
                                </div>
                                <div class="cart-item-actions">
                                    <p>Total: $${itemTotal.toFixed(2)}</p>
                                    <button class="remove-item" data-value="${item.item_id}">Remove</button>
                                </div>
                            </div>
                        `;
                    });
                    
                    $('#cart-summary').show();
                    $('#proceed-to-checkout').prop('disabled', false);
                    

                    const total = subtotal + 15.00;
                    $('#subtotal').text(`$${subtotal.toFixed(2)}`);
                    $('#total').text(`$${total.toFixed(2)}`);

                } catch (error) {
                    html = '<p>Your cart is empty.</p>';
                    $('#cart-summary').hide();
                    $('#proceed-to-checkout').prop('disabled', true);
                }
                if (cart.length === 0) {
                    
                } else {
                    
                }
                
                $('#cart-items').html(html);
                

                $('.remove-item').on('click', removeItem);
                $('.quantity').on('change', updateQuantity);
            }


            function removeItem() {
                cart.splice(cart.indexOf($(this).data('value')), 1);
                localStorage.setItem('cart', JSON.stringify(cart));
                $.post({
                    data: {cart_items: cart},
                    url: 'php/db-submit.php'
                }).done(function(resp) {
                    updateCartDisplay(resp);
                });
            }

            function updateQuantity() {
                const newQuantity = parseInt($(this).val());

                count = 0;
                cart.forEach(element => {
                    if (element == $(this).data('value')) {
                        count++;
                    }
                });

                if (newQuantity > count) {
                    for (let i = 0; i < (newQuantity - count); i++) {
                        cart.push($(this).data('value'));
                    }
                } else if (newQuantity < count) {
                    for (let i = 0; i < (count - newQuantity); i++) {
                        cart.splice(cart.indexOf($(this).data('value')), 1);
                    }
                }

                localStorage.setItem('cart', JSON.stringify(cart));

                $.post({
                    data: {cart_items: cart},
                    url: 'php/db-submit.php'
                }).done(function(resp) {
                    updateCartDisplay(resp);
                });
            }

            function count(array, value) {
                count = 0;
                array.forEach(element => {
                    if (element == value) {
                        count++;
                    }
                });
                return count;
            }


            $('#continue-shopping').on('click', function() {
                window.location.href = 'main.php';
            });


            $('#proceed-to-checkout').on('click', function() {
                // would sync with the server here l8ter for now just redirect to checkout
                
                // proper implementaiton:
                /*
                $.ajax({
                    url: 'sync_cart.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(cart),
                    success: function(response) {
                        window.location.href = 'checkout.php';
                    }
                });
                */
                
                sessionStorage.setItem('cart', JSON.stringify(cart));
                window.location.href = 'checkout.php';
            });
        });
    </script>
</body>
</html>

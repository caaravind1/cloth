<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Payment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body { background: #1c1c1e; color: #fff; font-family: 'Poppins', sans-serif; }
        .container { margin-top: 50px; background: rgba(31, 31, 31, 0.9); padding: 20px; border-radius: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Enter Card Details</h2>
    <p><strong>Shipping Address:</strong> <span id="shipping_address_display"></span></p>

    <form method="POST" action="dummy_payment.php">
        <div class="form-group">
            <label for="card_number">Card Number</label>
            <input type="text" id="card_number" name="card_number" class="form-control" 
                   placeholder="1234 5678 9101 112" maxlength="15" required>
        </div>
        <div class="form-group">
            <label for="expiry_date">Expiry Date</label>
            <input type="text" id="expiry_date" name="expiry_date" class="form-control" 
                   placeholder="MM/YY" maxlength="5" required>
        </div>
        <div class="form-group">
            <label for="cvv">CVV</label>
            <input type="password" id="cvv" name="cvv" class="form-control" placeholder="123" maxlength="3" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit Payment</button>
    </form>
</div>

<script>
window.addEventListener('load', function () {
    const shippingAddress = sessionStorage.getItem('shipping_address');
    if (shippingAddress) {
        document.getElementById('shipping_address_display').textContent = shippingAddress;
    }

    // Card Number - limit to 15 digits
    document.getElementById('card_number').addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '').slice(0, 15);
    });

    // Expiry Date - auto-format as MM/YY
    document.getElementById('expiry_date').addEventListener('input', function(e) {
        let input = this.value.replace(/\D/g, '').slice(0, 4);
        if (input.length >= 2) {
            input = input.slice(0, 2) + '/' + input.slice(2);
        }
        this.value = input;
    });

    // CVV - limit to 3 digits
    document.getElementById('cvv').addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '').slice(0, 3);
    });
});
</script>

</body>
</html>

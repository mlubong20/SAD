<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

$todaySales = Sales::getTodaySales();
$totalSales = Sales::getTotalSales();
$weeklySales = Sales::getWeeklySales(); // New function for weekly sales
$monthlySales = Sales::getMonthlySales(); // New function for monthly sales
$transactions = OrderItem::all();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Point of Sale System :: Sales</title>
    <link rel="stylesheet" type="text/css" href="./css/main.css">
    <link rel="stylesheet" type="text/css" href="./css/admin.css">
    <link rel="stylesheet" type="text/css" href="./css/util.css">

    <!-- Datatables Library -->
    <link rel="stylesheet" type="text/css" href="./css/datatable.css">
    <script src="./js/datatable.js"></script>
    <script src="./js/main.js"></script>
</head>

<body>
    <?php require 'templates/admin_header.php' ?>

    <div class="flex">
        <?php require 'templates/admin_navbar.php' ?>
        <main>

            <div class="flex">
                <div style="flex: 2; padding: 16px;">
                    <div class="subtitle">Sales Information</div>
                    <hr />

                    <!-- Combo Box for Sales Period Selection -->
                    <div>
                        <label for="salesPeriod">Choose Sales Period:</label>
                        <select id="salesPeriod" name="salesPeriod">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="total">Total</option>
                        </select>
                    </div>

                    <!-- Cards for displaying sales -->
                    <div class="card" id="dailySalesCard">
                        <div class="card-header">
                            <div class="card-title">Today's Sales</div>
                        </div>
                        <div class="card-content">
                            <?= $todaySales ?> PHP
                        </div>
                    </div>

                    <div class="card mt-16" id="weeklySalesCard">
                        <div class="card-header">
                            <div class="card-title">Weekly Sales</div>
                        </div>
                        <div class="card-content">
                            <?= $weeklySales ?> PHP
                        </div>
                    </div>

                    <div class="card mt-16" id="monthlySalesCard">
                        <div class="card-header">
                            <div class="card-title">Monthly Sales</div>
                        </div>
                        <div class="card-content">
                            <?= $monthlySales ?> PHP
                        </div>
                    </div>

                    <div class="card mt-16" id="totalSalesCard">
                        <div class="card-header">
                            <div class="card-title">Total Sales</div>
                        </div>
                        <div class="card-content">
                            <?= $totalSales ?> PHP
                        </div>
                    </div>
                </div>

                <div style="flex: 5; padding: 16px">
                    <div class="subtitle">Transactions</div>
                    <hr />

                    <table id="transactionsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price (with 12% Tax)</th>
                                <th>Date and Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 1; ?>
                            <?php foreach ($transactions as $transaction): 
                                $priceWithTax = $transaction->price * 1.12; // Calculate price with 12% tax
                                $subtotal = $transaction->quantity * $transaction->price; // Subtotal before tax
                                $subtotalWithTax = $transaction->quantity * $priceWithTax; // Subtotal with tax
                                $taxAmount = $subtotalWithTax - $subtotal; // Tax amount
                            ?>
                                <tr>
                                    <td><?= sprintf('%05d', $counter++) ?></td>
                                    <td><?= htmlspecialchars($transaction->product_name) ?></td>
                                    <td><?= htmlspecialchars($transaction->quantity) ?></td>
                                    <td><?= number_format($priceWithTax, 2) ?> PHP</td> <!-- Price with Tax -->
                                    <td><?= htmlspecialchars($transaction->created_at) ?></td>
                                    <td>
                                        <button class="receipt-btn" 
                                            data-transaction-id="<?= $transaction->id ?>" 
                                            data-product="<?= htmlspecialchars($transaction->product_name) ?>" 
                                            data-quantity="<?= htmlspecialchars($transaction->quantity) ?>" 
                                            data-price="<?= number_format($transaction->price, 2) ?>" 
                                            data-subtotal="<?= number_format($subtotal, 2) ?>" 
                                            data-tax="<?= number_format($taxAmount, 2) ?>" 
                                            data-total="<?= number_format($subtotalWithTax, 2) ?>" 
                                            data-date="<?= htmlspecialchars($transaction->created_at) ?>">
                                            Receipt
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

   <!-- Receipt Modal -->
<div id="receiptModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2>KIRISTINE CAFE</h2>
        <h2>Receipt</h2>
        <p><strong>Receipt ID:</strong> <span id="receiptId"></span></p>
        <p><strong>Product:</strong> <span id="receiptProduct"></span></p>
        <p><strong>Quantity:</strong> <span id="receiptQuantity"></span></p>
        <p><strong>Price (per unit):</strong> <span id="receiptPrice"></span> PHP</p>
        <p><strong>Total Price (before Tax):</strong> <span id="receiptSubtotal"></span> PHP</p>
        <p><strong>12% Tax:</strong> <span id="receiptTax"></span> PHP</p>
        <p><strong>Total Price (after Tax):</strong> <span id="receiptTotal"></span> PHP</p>
        <p><strong>Date and Time:</strong> <span id="receiptDate"></span></p>
        <p>THANK YOU!</p>
        <button onclick="printReceipt()" style="margin-top: 20px; padding: 10px; font-size: 16px;">Print Receipt</button>
    </div>
</div>

<script>
    // Starting receipt ID
    let receiptIdCounter = 1;

    // Show Receipt Modal
    document.querySelectorAll('.receipt-btn').forEach(button => {
        button.addEventListener('click', function () {
            const receiptId = receiptIdCounter.toString().padStart(5, '0'); // Format as 00001, 00002, etc.

            // Populate receipt data
            document.getElementById('receiptId').innerText = receiptId;
            document.getElementById('receiptProduct').innerText = this.dataset.product;
            document.getElementById('receiptQuantity').innerText = this.dataset.quantity;
            document.getElementById('receiptPrice').innerText = this.dataset.price;
            document.getElementById('receiptSubtotal').innerText = this.dataset.subtotal;
            document.getElementById('receiptTax').innerText = this.dataset.tax;
            document.getElementById('receiptTotal').innerText = this.dataset.total;
            document.getElementById('receiptDate').innerText = this.dataset.date;

            // Increment receipt ID for next receipt
            receiptIdCounter++;

            // Show modal
            const modal = document.getElementById('receiptModal');
            modal.style.display = 'flex'; // Display as flex to keep it centered
        });
    });

    // Close Modal
    function closeModal() {
        document.getElementById('receiptModal').style.display = 'none';
    }

    // Print Receipt Function
    function printReceipt() {
        const printContents = document.querySelector('.modal-content').innerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = `
            <div style="font-family: 'Courier New', Courier, monospace; font-size: 14px;">
                ${printContents}
            </div>
        `;

        window.print();
        document.body.innerHTML = originalContents;
        location.reload(); // Reload to restore the original page state
    }

    // Function to update the sales display based on the selected period
    document.getElementById('salesPeriod').addEventListener('change', function() {
        const selectedPeriod = this.value;

        // Hide all sales cards initially
        document.getElementById('dailySalesCard').style.display = 'none';
        document.getElementById('weeklySalesCard').style.display = 'none';
        document.getElementById('monthlySalesCard').style.display = 'none';
        document.getElementById('totalSalesCard').style.display = 'none';

        // Show the selected sales card
        if (selectedPeriod === 'daily') {
            document.getElementById('dailySalesCard').style.display = 'block';
        } else if (selectedPeriod === 'weekly') {
            document.getElementById('weeklySalesCard').style.display = 'block';
        } else if (selectedPeriod === 'monthly') {
            document.getElementById('monthlySalesCard').style.display = 'block';
        } else if (selectedPeriod === 'total') {
            document.getElementById('totalSalesCard').style.display = 'block';
        }
    });

    // Trigger change event on page load to ensure the correct sales card is shown
    document.getElementById('salesPeriod').dispatchEvent(new Event('change'));
</script>


<style>
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.3); /* Lighter dark overlay */
        display: none; /* Initially hidden */
        justify-content: center;
        align-items: center;
        z-index: 1000;
        overflow: auto;
    }

    .modal-content {
        background: #f1f1f1; /* Light gray background */
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Softer shadow */
        width: 300px;
        max-width: 90%; /* Responsive design */
        text-align: center;
        position: relative;
        font-family: "Courier New", Courier, monospace; /* Receipt-like font */
        font-size: 14px; /* Standard receipt font size */
        color: #333333; /* Dark gray text for a readable contrast */
        border: 2px solid #ccc; /* Simple border */
    }

    .modal-content.show {
        transform: scale(1.05); /* Slight zoom-in effect when shown */
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
        font-size: 16px;
        color: #333333; /* Dark gray close button */
        border: none;
        background: none;
        padding: 5px;
    }

    .close-btn:hover {
        color: #666666; /* Darker gray on hover */
    }

    .close-btn:focus {
        outline: none;
        border: 2px solid #333333; /* Focus outline for accessibility */
    }

    @media (max-width: 600px) {
        .modal-content {
            width: 80%; /* Take up more space on small screens */
        }
    }
    /* Combo Box Styling */
    #salesPeriod {
    width: 200px; /* Set a fixed width for consistency */
    padding: 8px 12px; /* Add padding for better spacing */
    font-size: 14px; /* Use a smaller font size for better alignment */
    font-family: Arial, sans-serif; /* Consistent font with the rest of the design */
    border: 1px solid #bfae8f; /* Soft brown border for a natural look */
    border-radius: 4px; /* Slight rounding for a soft appearance */
    background-color: #f1f1f1; /* Light holographic white background */
    color: #4d4d4d; /* Dark gray text for readability */
    box-sizing: border-box; /* Ensure padding is included in width */
    transition: border-color 0.3s ease, box-shadow 0.3s ease; /* Smooth transition on focus */
    background: linear-gradient(145deg, #f9f9f9, #d1c6b1); /* Light gradient for holographic feel */
}

/* Combo Box Focus Style */
#salesPeriod:focus {
    outline: none; /* Remove the default focus outline */
    border-color: #5e8d4f; /* Soft green border to match the theme */
    box-shadow: 0 0 5px rgba(94, 141, 79, 0.6); /* Soft green shadow for better focus indication */
    background: linear-gradient(145deg, #d4e2c1, #f1f1f1); /* Subtle holographic green gradient */
}

/* Combo Box Option Styling */
#salesPeriod option {
    padding: 10px; /* Add padding inside the options for better readability */
    font-size: 14px; /* Ensure consistency in font size */
    background-color: #f1f1f1; /* Light background for options */
    color: #4d4d4d; /* Dark text for options */
    border: none; /* Remove border for a clean look */
}

/* Hover effect for options */
#salesPeriod option:hover {
    background-color: #d1e1d1; /* Light green hover effect for better user interaction */
    color: #333; /* Darker text on hover for better contrast */
}

/* Optional: Styling for the combo box in the container */
.container {
    width: 80%;
    margin: 0 auto;
    padding-top: 50px; /* Add padding to position the combo box nicely */
    text-align: center;
    font-family: 'Arial', sans-serif;
    background-color: #f9f9f9; /* Harmonize the background with the combo box */
}

h2 {
    color: #4d4d4d; /* Dark gray text for the heading */
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
}



</style>

</body>

</html>

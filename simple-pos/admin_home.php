<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

$products = Product::all();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Point of Sale System :: Home</title>
    <link rel="stylesheet" type="text/css" href="./css/main.css">
    <link rel="stylesheet" type="text/css" href="./css/admin.css">
    <link rel="stylesheet" type="text/css" href="./css/util.css">

    <!-- Datatables  Library -->
    <link rel="stylesheet" type="text/css" href="./css/datatable.css">
    <script src="./js/datatable.js"></script>
    <script src="./js/main.js"></script>

    <style>
        /* Style for the search bar container */
        .search-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .search-bar {
            padding: 8px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 250px;
        }
    </style>
</head>

<body>

    <?php require 'templates/admin_header.php' ?>

    <div class="flex">
        <?php require 'templates/admin_navbar.php' ?>
        <main>
            <div class="wrapper w-60p">
                <span class="subtitle">Product List</span>
                <hr />

                <?php displayFlashMessage('delete_product') ?>
                <?php displayFlashMessage('add_stock') ?>

                <!-- Search Bar -->
                <div class="search-container">
                    <input type="text" id="productSearch" class="search-bar" placeholder="Search products...">
                </div>

                <table id="productsTable">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Stocks</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $skuCounter = 1; ?>
                        <?php foreach ($products as $product) : ?>
                        <tr>
                            <td class="sku-cell">SKU<?= str_pad($skuCounter++, 3, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($product->name) ?></td>
                            <td><?= htmlspecialchars($product->category->name) ?></td>
                            <td><?= htmlspecialchars($product->quantity) ?></td>
                            <td><?= htmlspecialchars($product->price) ?></td>
                            <td>
                                <a href="#" onclick="addStock(<?= $product->id ?>)" class="text-green-300">Add Stock</a>
                                <a href="admin_update_item.php?id=<?= $product->id ?>" class="text-primary ml-16">Update</a>
                                <a href="api/product_controller.php?action=delete&id=<?= $product->id ?>" class="text-red-500 ml-16">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </main>
    </div>

    <script>
        // JavaScript to filter products based on the search bar input
        document.getElementById('productSearch').addEventListener('input', function() {
            let searchTerm = this.value.toLowerCase();
            let rows = document.querySelectorAll('#productsTable tbody tr');
            
            rows.forEach(row => {
                let sku = row.querySelector('.sku-cell').textContent.toLowerCase();
                let name = row.cells[1].textContent.toLowerCase();
                let category = row.cells[2].textContent.toLowerCase();

                if (sku.includes(searchTerm) || name.includes(searchTerm) || category.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
<

</body>

</html>

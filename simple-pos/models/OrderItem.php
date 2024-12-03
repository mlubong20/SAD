<?php

require_once __DIR__ . '/../_init.php';

class OrderItem
{
    public $id;
    public $order_id;
    public $product_id;
    public $quantity;
    public $price;
    public $product_name;
    public $created_at; // Property for created_at

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->order_id = $data['order_id'];
        $this->product_id = $data['product_id'];
        $this->quantity = $data['quantity'];
        $this->price = $data['price'];
        $this->product_name = $data['product_name'];
        $this->created_at = $data['created_at'] ?? null; // Initialize created_at with a fallback
    }

    /**
     * Add a new order item to the database.
     */
    public static function add($orderId, $item)
{
    global $connection;

    // Debug: Print out the item array to check its contents
    error_log("Received item: " . print_r($item, true)); // Logs the item array to the error log

    // Validate the item data before proceeding
    if (!isset($item['id']) || !isset($item['quantity'])) {
        throw new Exception("Invalid product or quantity. Product ID or Quantity is missing.");
    }

    if (!is_numeric($item['quantity']) || $item['quantity'] <= 0) {
        throw new Exception("Invalid quantity. The quantity must be a positive number.");
    }

    // Find the product based on the provided product ID
    $product = Product::find($item['id']);
    
    // If product is not found, throw an error
    if (!$product) {
        throw new Exception("Product not found with ID: " . $item['id']);
    }

    // Check if there's enough stock
    if ($product->quantity < $item['quantity']) {
        throw new Exception("Not enough stock for product: " . $product->name);
    }

    // Prepare and execute the SQL to add the order item
    $stmt = $connection->prepare('INSERT INTO `order_items` (order_id, product_id, quantity, price, created_at) VALUES (:order_id, :product_id, :quantity, :price, NOW())');
    $stmt->bindParam("order_id", $orderId);
    $stmt->bindParam("product_id", $item['id']);
    $stmt->bindParam("quantity", $item['quantity']);
    $stmt->bindParam("price", $product->price);

    if ($stmt->execute()) {
        // Update the product quantity after the order is added
        $product->quantity -= $item['quantity'];
        $product->update(); // Assuming the update method saves the new quantity to the database
    } else {
        throw new Exception("Failed to add order item to the database.");
    }
}


    /**
     * Get all order items, joining with product names.
     */
    public static function all()
    {
        global $connection;

        // Prepare the SQL query to get order items with product names
        $stmt = $connection->prepare('
            SELECT 
                order_items.*, 
                products.name AS product_name
            FROM order_items
            INNER JOIN products
            ON order_items.product_id = products.id
        ');

        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        // Check if created_at is present in the result set, if not, set it to null
        foreach ($result as &$item) {
            $item['created_at'] = $item['created_at'] ?? null; // Set to null if not present
        }

        // Map the result to OrderItem objects
        return array_map(fn($item) => new OrderItem($item), $result);
    }
}


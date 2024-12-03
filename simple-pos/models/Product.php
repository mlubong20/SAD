<?php

require_once __DIR__.'/../_init.php';

class Product 
{
    public $id;
    public $name;
    public $category_id;
    public $quantity;
    public $price;
    public $category;

    // Constructor to initialize the Product object
    public function __construct($product)
    {
        $this->id = $product['id'];
        $this->name = $product['name'];
        $this->category_id = $product['category_id'];
        $this->quantity = intval($product['quantity']);
        $this->price = floatval($product['price']);
        $this->category = $this->getCategory($product); // Get associated category
    }

    // Update product details
    public function update()
    {
        global $connection;

        try {
            $stmt = $connection->prepare('UPDATE products SET name=:name, category_id=:category_id, quantity=:quantity, price=:price WHERE id=:id');
            $stmt->bindParam('name', $this->name);
            $stmt->bindParam('category_id', $this->category_id);
            $stmt->bindParam('quantity', $this->quantity);
            $stmt->bindParam('price', $this->price);
            $stmt->bindParam('id', $this->id);
            $stmt->execute();
        } catch (Exception $e) {
            throw new Exception('Error updating product: ' . $e->getMessage());
        }
    }

    // Delete the product
    public function delete()
    {
        global $connection;

        try {
            $stmt = $connection->prepare('DELETE FROM products WHERE id=:id');
            $stmt->bindParam('id', $this->id);
            $stmt->execute();
        } catch (Exception $e) {
            throw new Exception('Error deleting product: ' . $e->getMessage());
        }
    }

    // Get the category for the product
    private function getCategory($product)
    {
        if (isset($product['category_name'])) {
            return new Category([
                'id' => $product['category_id'],
                'name' => $product['category_name']
            ]);
        }

        return Category::find($product['category_id']);
    }

    // Retrieve all products with their categories
    public static function all()
    {
        global $connection;

        try {
            $stmt = $connection->prepare('SELECT products.*, categories.name AS category_name FROM products INNER JOIN categories ON products.category_id = categories.id');
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetchAll();

            return array_map(fn($item) => new Product($item), $result);
        } catch (Exception $e) {
            throw new Exception('Error fetching all products: ' . $e->getMessage());
        }
    }

    // Find a specific product by its ID
    public static function find($id)
    {
        global $connection;

        try {
            $stmt = $connection->prepare('SELECT products.*, categories.name AS category_name FROM products INNER JOIN categories ON products.category_id = categories.id WHERE products.id=:id');
            $stmt->bindParam('id', $id);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            $result = $stmt->fetchAll();

            if (count($result) >= 1) {
                return new Product($result[0]);
            }

            return null;
        } catch (Exception $e) {
            throw new Exception('Error finding product: ' . $e->getMessage());
        }
    }

    // Add a new product to the database
    public static function add($name, $category_id, $quantity, $price)
    {
        global $connection;

        try {
            $sql_command = 'INSERT INTO products (name, category_id, quantity, price) VALUES (:name, :category_id, :quantity, :price)';
            $stmt = $connection->prepare($sql_command);
            $stmt->bindParam('name', $name);
            $stmt->bindParam('category_id', $category_id);
            $stmt->bindParam('quantity', $quantity);
            $stmt->bindParam('price', $price);
            $stmt->execute();

            // Return the last inserted product's ID
            return $connection->lastInsertId();
        } catch (Exception $e) {
            throw new Exception('Error adding new product: ' . $e->getMessage());
        }
    }

    // Save or update product (handles both add and update scenarios)
    public function save()
    {
        if ($this->id) {
            // If the product has an ID, it's an update
            $this->update();
        } else {
            // Otherwise, it's a new product, so add it
            return self::add($this->name, $this->category_id, $this->quantity, $this->price);
        }
    }
}

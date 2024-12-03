<?php

require_once __DIR__.'/../_init.php';

class Order
{
    public $id;
    public $created_at;

    public function __construct($order)
    {
        // Initialize Order with data from the database
        $this->id = $order['id'];
        $this->created_at = $order['created_at'];
    }

    /**
     * Create a new order record in the database
     *
     * @return Order|null The created Order object or null if creation failed.
     */
    public static function create()
    {
        global $connection;

        try {
            // Insert a new order into the database (assuming auto-increment for id and default value for created_at)
            $sql_command = 'INSERT INTO orders (created_at) VALUES (NOW())';
            $stmt = $connection->prepare($sql_command);

            // Execute the statement
            $stmt->execute();

            // Get and return the last inserted order
            return static::getLastRecord();
        } catch (PDOException $e) {
            // Handle any database errors and log them (optional)
            error_log("Error creating order: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get the most recent order record.
     *
     * @return Order|null The last Order object or null if no orders exist.
     */
    public static function getLastRecord()
    {
        global $connection;

        try {
            // Prepare the statement to get the last inserted order
            $stmt = $connection->prepare('SELECT * FROM `orders` ORDER BY id DESC LIMIT 1');
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            // Fetch the result
            $result = $stmt->fetch();

            // Check if result exists and return it as an Order object
            if ($result) {
                return new Order($result);
            }

            return null;
        } catch (PDOException $e) {
            // Handle any database errors and log them (optional)
            error_log("Error fetching last order: " . $e->getMessage());
            return null;
        }
    }
}
?>

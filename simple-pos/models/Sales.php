<?php

require_once __DIR__ . '/../_init.php';

class Sales
{
    public static function getTodaySales()
    {
        global $connection;

        $sql_command = ("
            SELECT 
                SUM(order_items.quantity * order_items.price) AS today,
                DATE_FORMAT(orders.created_at, '%Y-%m-%d') AS _date
            FROM 
                order_items 
            INNER JOIN 
                orders ON order_items.order_id = orders.id 
            WHERE 
                DATE(orders.created_at) = CURDATE()
            GROUP BY 
                _date
        ");

        $stmt = $connection->prepare($sql_command);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return $result[0]['today'];
        }

        return 0;
    }

    public static function getTotalSales()
    {
        global $connection;

        $sql_command = "SELECT SUM(quantity * price) AS total FROM order_items";

        $stmt = $connection->prepare($sql_command);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return $result[0]['total'];
        }

        return 0;
    }

    public static function getWeeklySales()
    {
        global $connection;

        $sql_command = ("
            SELECT 
                SUM(order_items.quantity * order_items.price) AS weekly,
                YEARWEEK(orders.created_at, 1) AS _week
            FROM 
                order_items 
            INNER JOIN 
                orders ON order_items.order_id = orders.id 
            WHERE 
                YEARWEEK(orders.created_at, 1) = YEARWEEK(CURDATE(), 1)
            GROUP BY 
                _week
        ");

        $stmt = $connection->prepare($sql_command);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return $result[0]['weekly'];
        }

        return 0;
    }

    public static function getMonthlySales()
    {
        global $connection;

        $sql_command = ("
            SELECT 
                SUM(order_items.quantity * order_items.price) AS monthly,
                DATE_FORMAT(orders.created_at, '%Y-%m') AS _month
            FROM 
                order_items 
            INNER JOIN 
                orders ON order_items.order_id = orders.id 
            WHERE 
                DATE_FORMAT(orders.created_at, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
            GROUP BY 
                _month
        ");

        $stmt = $connection->prepare($sql_command);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return $result[0]['monthly'];
        }

        return 0;
    }
}

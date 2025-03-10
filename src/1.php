<?php
// I'll start by creating a database class that handles connection and query execution.

class Database {
    private $connection;

    public function __construct($host, $username, $password, $dbname) {
        $this->connection = new mysqli($host, $username, $password, $dbname);
        if ($this->connection->connect_error) {
            throw new Exception("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        if ($stmt === false) {
            throw new Exception("SQL prepare failed: " . $this->connection->error);
        }

        // Bind parameters if there are any
        if (!empty($params)) {
            $types = str_repeat("s", count($params));  // assuming all params are strings for simplicity
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new Exception("SQL execute failed: " . $stmt->error);
        }

        return $stmt->get_result();
    }

    public function close() {
        $this->connection->close();
    }
}

// Next, i'd create a product class that handles fetching product information from the database.

class Product {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getPriceById($productId) {
        $sql = "SELECT price FROM products WHERE id = ?";
        $result = $this->db->query($sql, [$productId]);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['price'];
        }
        throw new Exception("Product not found.");
    }
}

// I would then create an order class that handles the order processing logic.

class Order {
    private $db;
    private $product;

    public function __construct(Database $db, Product $product) {
        $this->db = $db;
        $this->product = $product;
    }

    public function processOrder($orderData) {
        try {
            // Validate and calculate the order
            $price = $this->product->getPriceById($orderData['product_id']);
            $total = $price * $orderData['quantity'];

            // Insert the order
            $sql = "INSERT INTO orders (user_id, product_id, quantity, total) VALUES (?, ?, ?, ?)";
            $params = [$orderData['user_id'], $orderData['product_id'], $orderData['quantity'], $total];
            $this->db->query($sql, $params);

            // Return success message
            return "Order placed successfully!";
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}

// I would then create the controller that uses these classes to process the order.

function processOrder($orderData) {
    try {
        // Initialize database and other necessary components
        $db = new Database("localhost", "root", "", "shop");
        $product = new Product($db);
        $order = new Order($db, $product);

        // Process the order and return the result
        return $order->processOrder($orderData);

    } catch (Exception $e) {
        // If there was an error in any of the operations, return the error message
        return "Error: " . $e->getMessage();
    } finally {
        if (isset($db)) {
            $db->close();
        }
    }
}

<?php
/*
 * The following script is running extremely slow in a production environment.
 * Task:
 * * Identify the performance issues and rewrite the function to optimize database queries.
 * * Apply best practices while maintaining legacy compatibility.
 *
 * Hints:
 * * Overall there are three issue to fix
 * * Are there any patterns that might be suitable?
 */


function getUsers() {
    // Single database connection for the entire function
    $db = new PDO("mysql:host=localhost;dbname=shop", "root", "");
    $users = [];
    
    // Fetch users with relevant fields
    $userStmt = $db->query("SELECT id, name FROM users");
    $usersData = $userStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Collect user IDs from the users query for efficient order fetching
    $userIds = array_column($usersData, 'id');
    
    // Fetch orders for all users in one query
    $ordersStmt = $db->query("SELECT user_id, order_id, total FROM orders WHERE user_id IN (" . implode(',', $userIds) . ")");
    $ordersData = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Map orders to users
    foreach ($usersData as $user) {
        // Filter orders for the current user
        $userOrders = array_filter($ordersData, function($order) use ($user) {
            return $order['user_id'] === $user['id'];
        });
        
        // Add orders to user data
        $users[] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'orders' => $userOrders
        ];
    }

    return $users;
}

print_r(getUsers());

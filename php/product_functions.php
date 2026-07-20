<?php
include '../config/database.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'search':
        $query = $_GET['query'] ?? '';
        
        if (strlen($query) < 2) {
            echo json_encode(['success' => false, 'message' => 'Query too short']);
            exit;
        }
        
        $searchQuery = "%$query%";
        $sql = "SELECT id, name, price, image FROM products 
                WHERE name LIKE ? OR description LIKE ? 
                LIMIT 5";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $searchQuery, $searchQuery);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        
        echo json_encode(['success' => true, 'results' => $products]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
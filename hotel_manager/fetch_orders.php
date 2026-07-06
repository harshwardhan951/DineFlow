<?php
session_start();
include '../db.php';

if (!isset($_SESSION['hotel_id'])) {
    exit("No hotel assigned!");
}

$hotel_id = $_SESSION['hotel_id'];

// Fetch orders
$sql = "SELECT o.*, u.username AS customer_name 
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.hotel_id = ?
        ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();

// Generate HTML
if($result->num_rows > 0){
    echo '<table>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
            </tr>';
    while($row = $result->fetch_assoc()){
        $status_class = $row['status'];
        echo '<tr>
                <td>#'.$row['id'].'</td>
                <td>'.$row['customer_name'].'</td>
                <td>₹'.$row['total_amount'].'</td>
                <td><span class="status '.$status_class.'">'.$row['status'].'</span></td>
              </tr>';
    }
    echo '</table>';
} else {
    echo "<p>No orders yet.</p>";
}
?>

<?php
include 'db.php';
session_start();

// Validate PIN and get room ID
$pin = $_GET['pin'] ?? '';
if (empty($pin)) {
    die("Room PIN is required");
}

// Get room ID
$stmt = $pdo->prepare("SELECT id FROM rooms WHERE pin_code = ?");
$stmt->execute([$pin]);
$room_id = $stmt->fetchColumn();

if (!$room_id) {
    die("Invalid room PIN");
}

// Get messages with user info
$msgs = $pdo->prepare("SELECT m.message, u.username, m.sent_at, u.id as user_id 
                      FROM messages m 
                      LEFT JOIN users u ON m.user_id = u.id 
                      WHERE m.room_id = ? 
                      ORDER BY m.sent_at ASC");
$msgs->execute([$room_id]);

$current_date = null;
while ($m = $msgs->fetch(PDO::FETCH_ASSOC)) {
    try {
        $sent_at = new DateTime($m['sent_at']);
        $message_date = $sent_at->format('Y-m-d');
        $time_format = $sent_at->format('h:i A');
        
        // Show date divider if date changed
        if ($message_date !== $current_date) {
            $current_date = $message_date;
            $day_name = $sent_at->format('l');
            $date_display = $sent_at->format('M j, Y');
            echo "<div class='day-divider'><span>$day_name, $date_display</span></div>";
        }
        
        // Determine message class
        $message_class = (isset($_SESSION['user_id']) && $m['user_id'] == $_SESSION['user_id']) 
            ? 'sent' 
            : 'received';
            
        // Output message
        echo "<div class='message $message_class'>";
        echo "<div class='message-sender'>" . htmlspecialchars($m['username'] ?? 'Unknown') . "</div>";
        echo "<div class='message-content'>" . htmlspecialchars($m['message']) . "</div>";
        echo "<div class='message-info'>$time_format</div>";
        echo "</div>";
        
    } catch (Exception $e) {
        error_log("Error processing message: " . $e->getMessage());
        continue;
    }
}

// Show empty state if no messages
if ($msgs->rowCount() === 0) {
    echo "<div class='message info'>No messages yet. Be the first to send one!</div>";
}
?>
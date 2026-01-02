<?php
// Reset lab progress
require_once '../progress.php';
resetLab(9);

// Lab 9 - Database Setup Script
$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'root';

// Connect to MySQL server
$conn = new mysqli($db_host, $db_user, $db_pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read and execute SQL file
$sql = file_get_contents('database_setup.sql');

// Execute multi-query
if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
}

if ($conn->error) {
    echo "Error setting up database: " . $conn->error;
} else {
    // Create transcripts directory and files
    $transcriptDir = __DIR__ . '/transcripts';
    if (!file_exists($transcriptDir)) {
        mkdir($transcriptDir, 0755, true);
    }
    
    // Create transcript files with chat logs
    
    // 1.txt - Carlos's chat with password revealed
    $carlos_chat = "CHAT TRANSCRIPT - Session #1
Date: 2024-12-20 14:32:15
User: carlos

[14:32:15] Support Agent: Hello! Welcome to our live chat support. How can I help you today?

[14:32:45] carlos: Hi, I forgot my password and I'm locked out of my account.

[14:33:02] Support Agent: No problem! I can help you with that. Let me look up your account.

[14:33:30] Support Agent: I found your account. For security purposes, I'll reset your password.

[14:34:15] Support Agent: Your new password has been set to: h5a2xfj8k3

[14:34:30] Support Agent: Please log in with this password and change it immediately from your account settings.

[14:34:45] carlos: Thank you so much! That was quick.

[14:35:00] Support Agent: You're welcome! Is there anything else I can help you with?

[14:35:15] carlos: No, that's all. Thanks again!

[14:35:30] Support Agent: Have a great day! Chat ended.

--- END OF TRANSCRIPT ---";

    // 2.txt - Wiener's chat (generic support)
    $wiener_chat = "CHAT TRANSCRIPT - Session #2
Date: 2024-12-21 09:15:00
User: wiener

[09:15:00] Support Agent: Hello! Welcome to our live chat support. How can I help you today?

[09:15:30] wiener: Hi, I have a question about my order #12345.

[09:15:45] Support Agent: Of course! Let me look that up for you.

[09:16:15] Support Agent: I see your order was shipped yesterday and should arrive within 3-5 business days.

[09:16:30] wiener: Great, thanks for checking!

[09:16:45] Support Agent: You're welcome! Anything else?

[09:17:00] wiener: No, that's all. Bye!

[09:17:15] Support Agent: Take care! Chat ended.

--- END OF TRANSCRIPT ---";

    // 3.txt - Admin's chat (internal discussion)
    $admin_chat = "CHAT TRANSCRIPT - Session #3
Date: 2024-12-22 11:00:00
User: administrator

[11:00:00] Support Agent: Hi Admin, internal support channel.

[11:00:30] administrator: Can you check the server status?

[11:01:00] Support Agent: All systems operational. Database backup completed at 03:00.

[11:01:30] administrator: Perfect. Any unusual activity?

[11:02:00] Support Agent: None reported. Security logs are clean.

[11:02:30] administrator: Good. Let me know if anything comes up.

[11:03:00] Support Agent: Will do. Chat ended.

--- END OF TRANSCRIPT ---";

    // 4.txt - Support's chat
    $support_chat = "CHAT TRANSCRIPT - Session #4
Date: 2024-12-22 15:45:00
User: support

[15:45:00] Support Agent: Team chat initialized.

[15:45:30] support: Testing the chat system for the daily check.

[15:46:00] Support Agent: System test successful. All features working.

[15:46:30] support: Great, logging off.

[15:47:00] Support Agent: Chat ended.

--- END OF TRANSCRIPT ---";

    // Write transcript files
    file_put_contents($transcriptDir . '/1.txt', $carlos_chat);
    file_put_contents($transcriptDir . '/2.txt', $wiener_chat);
    file_put_contents($transcriptDir . '/3.txt', $admin_chat);
    file_put_contents($transcriptDir . '/4.txt', $support_chat);
    
    header("Location: index.php?setup=success");
    exit();
}

$conn->close();
?>

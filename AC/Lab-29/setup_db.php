<?php
// Lab 29: LinkedPro Newsletter Platform - Database Setup

// Use centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$db_host = $creds['host'];
$db_user = $creds['user'];
$db_pass = $creds['pass'];

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

echo "<h2>Lab 29: LinkedPro Newsletter Platform - Database Setup</h2>";
echo "<style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0f1419; color: #e0e0e0; padding: 2rem; }
    .success { color: #057642; }
    .error { color: #ff6b6b; }
    .info { color: #0a66c2; }
    pre { background: #1a1a2e; padding: 1rem; border-radius: 8px; overflow-x: auto; }
    a { color: #0a66c2; }
    .btn { display: inline-block; background: linear-gradient(135deg, #0a66c2, #004182); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; margin-top: 1rem; }
</style>";

try {
    // Connect without database first
    $conn = new mysqli($db_host, $db_user, $db_pass);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p class='info'>Connected to MySQL server...</p>";
    
    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/database_setup.sql');
    
    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
        
        echo "<p class='success'>✓ Database 'ac_lab29' created successfully!</p>";
        echo "<p class='success'>✓ Tables created: users, newsletters, subscribers, articles, activity_log</p>";
        echo "<p class='success'>✓ Test data inserted successfully!</p>";
        
        echo "<h3>Test Accounts Created:</h3>";
        echo "<pre>";
        echo "┌─────────────────────────────────────────────────────────────────┐\n";
        echo "│ Role                │ Username        │ Password              │\n";
        echo "├─────────────────────────────────────────────────────────────────┤\n";
        echo "│ ⚔️ Attacker          │ attacker        │ attacker123           │\n";
        echo "│ 👩‍💼 Creator (Alice)  │ alice_ceo       │ alice123              │\n";
        echo "│ 👨‍💼 Creator (Bob)    │ bob_investor    │ bob123                │\n";
        echo "│ 👩‍🏫 Creator (Carol)  │ carol_professor │ carol123              │\n";
        echo "└─────────────────────────────────────────────────────────────────┘\n";
        echo "</pre>";
        
        echo "<h3>Newsletters Created:</h3>";
        echo "<pre>";
        echo "1. Tech Leadership Weekly (Alice) - 8 subscribers\n";
        echo "2. Venture Capital Insider (Bob) - 6 subscribers\n";
        echo "3. AI Research Digest (Carol) - 4 subscribers\n";
        echo "</pre>";
        
        echo "<p class='success'>✓ Setup complete! You can now start the lab.</p>";
        echo "<a href='index.php' class='btn'>→ Go to Lab</a>";
        
    } else {
        throw new Exception("Error executing SQL: " . $conn->error);
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Make sure MySQL is running and the credentials are correct.</p>";
}
?>

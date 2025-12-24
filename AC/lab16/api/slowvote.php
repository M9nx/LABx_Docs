<?php
/**
 * Slowvote API Endpoint
 * 
 * ⚠️ VULNERABLE: This endpoint only checks if user is logged in (authenticated)
 * but does NOT verify if the user is authorized to view the specific poll.
 * 
 * The web UI (view-poll.php) properly implements access control,
 * but this API endpoint bypasses those checks!
 */

session_start();
require_once '../config.php';
require_once '../../progress.php';

header('Content-Type: application/json');

// Check if user is authenticated
// ✅ Authentication check exists
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'error' => 'Unauthorized',
        'message' => 'You must be logged in to use this API'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$pollId = $_GET['poll_id'] ?? $_POST['poll_id'] ?? $_POST['params']['poll_id'] ?? 0;
$output = $_GET['output'] ?? $_POST['output'] ?? 'json';

if (empty($action)) {
    echo json_encode([
        'error' => 'Missing action parameter',
        'available_actions' => ['info', 'list', 'vote']
    ]);
    exit;
}

switch ($action) {
    case 'info':
        if (empty($pollId)) {
            echo json_encode(['error' => 'Missing poll_id parameter']);
            exit;
        }
        
        // ❌ VULNERABILITY: NO AUTHORIZATION CHECK!
        // The code only verifies the user is logged in, but doesn't check
        // if they have permission to view this specific poll.
        // 
        // MISSING CODE:
        // - Check if poll visibility allows this user
        // - Check poll_permissions table for specific access
        // - Verify user is creator OR has explicit permission
        
        // Get poll details - returns data for ANY poll_id
        $stmt = $pdo->prepare("
            SELECT s.*, u.username as creator_name, u.full_name as creator_full_name, u.email as creator_email
            FROM slowvotes s
            JOIN users u ON s.creator_id = u.id
            WHERE s.id = ?
        ");
        $stmt->execute([$pollId]);
        $poll = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$poll) {
            echo json_encode(['error' => 'Poll not found']);
            exit;
        }
        
        // Get poll options
        $stmt = $pdo->prepare("SELECT id, option_text, vote_count FROM poll_options WHERE poll_id = ?");
        $stmt->execute([$pollId]);
        $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Check if this is an IDOR exploit (accessing private poll without permission)
        $isExploit = false;
        if ($poll['visibility'] !== 'everyone' && $poll['creator_id'] != $userId) {
            // Check if user has explicit permission
            $stmt = $pdo->prepare("SELECT can_view FROM poll_permissions WHERE poll_id = ? AND user_id = ?");
            $stmt->execute([$pollId, $userId]);
            $permission = $stmt->fetch();
            
            if (!$permission || !$permission['can_view']) {
                $isExploit = true;
                // Mark lab as solved!
                markLabSolved(16);
            }
        }
        
        $response = [
            'success' => true,
            'poll' => [
                'id' => $poll['id'],
                'title' => $poll['title'],
                'description' => $poll['description'],
                'visibility' => $poll['visibility'],
                'creator' => [
                    'id' => $poll['creator_id'],
                    'username' => $poll['creator_name'],
                    'full_name' => $poll['creator_full_name'],
                    'email' => $poll['creator_email']
                ],
                'allow_multiple' => (bool)$poll['allow_multiple'],
                'is_closed' => (bool)$poll['is_closed'],
                'created_at' => $poll['created_at']
            ],
            'options' => $options,
            'total_votes' => array_sum(array_column($options, 'vote_count'))
        ];
        
        if ($isExploit) {
            $response['_security_note'] = [
                'exploit_detected' => true,
                'message' => 'You accessed a restricted poll without proper authorization!',
                'poll_visibility' => $poll['visibility'],
                'your_user_id' => $userId,
                'lab_solved' => true
            ];
        }
        
        if ($output === 'human') {
            header('Content-Type: text/html');
            echo "<html><head><title>Poll Data</title>";
            echo "<style>body{background:#1a1a2e;color:#e0e0e0;font-family:monospace;padding:2rem;}";
            echo "pre{background:#0d0d0d;padding:1rem;border-radius:10px;overflow:auto;}</style></head><body>";
            echo "<h2>Poll V{$poll['id']} Data</h2>";
            if ($isExploit) {
                echo "<div style='background:rgba(0,255,0,0.2);border:2px solid #00ff00;padding:1rem;border-radius:10px;margin-bottom:1rem;'>";
                echo "<h3 style='color:#00ff00;'>🎉 Lab Solved!</h3>";
                echo "<p>You successfully exploited the IDOR vulnerability to access a restricted poll!</p>";
                echo "<a href='../success.php?poll_id={$pollId}' style='color:#9370DB;'>View Success Page →</a>";
                echo "</div>";
            }
            echo "<pre>" . htmlspecialchars(json_encode($response, JSON_PRETTY_PRINT)) . "</pre>";
            echo "<a href='../dashboard.php' style='color:#9370DB;'>← Back to Dashboard</a>";
            echo "</body></html>";
        } else {
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
        break;
        
    case 'list':
        // List all polls (also vulnerable - shows IDs of private polls)
        $stmt = $pdo->query("SELECT id, title, visibility, created_at FROM slowvotes ORDER BY id");
        $polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'polls' => $polls,
            'total' => count($polls)
        ], JSON_PRETTY_PRINT);
        break;
        
    case 'vote':
        echo json_encode([
            'error' => 'Voting via API is disabled in this lab',
            'hint' => 'Focus on the info action to exploit the IDOR vulnerability'
        ]);
        break;
        
    default:
        echo json_encode([
            'error' => 'Unknown action',
            'available_actions' => ['info', 'list']
        ]);
}
?>

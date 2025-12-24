<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prevention - IDOR Slowvote Bypass</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(106, 90, 205, 0.3);
            padding: 1rem 2rem;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.3rem;
            font-weight: bold;
            color: #9370DB;
            text-decoration: none;
        }
        .logo-icon {
            background: linear-gradient(135deg, #9370DB, #6A5ACD);
            color: #fff;
            padding: 0.4rem 0.6rem;
            border-radius: 6px;
        }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-links a { color: #e0e0e0; text-decoration: none; }
        .nav-links a:hover { color: #9370DB; }
        .docs-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
            padding-top: 70px;
        }
        .sidebar {
            background: rgba(0, 0, 0, 0.3);
            border-right: 1px solid rgba(106, 90, 205, 0.3);
            padding: 2rem 0;
            position: sticky;
            top: 70px;
            height: calc(100vh - 70px);
            overflow-y: auto;
        }
        .sidebar h3 {
            color: #9370DB;
            padding: 0 1.5rem;
            margin-bottom: 1rem;
        }
        .sidebar-nav { list-style: none; }
        .sidebar-nav a {
            display: block;
            padding: 0.75rem 1.5rem;
            color: #aaa;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(147, 112, 219, 0.1);
            color: #9370DB;
            border-left-color: #9370DB;
        }
        .sidebar-nav a.sub-item { padding-left: 2.5rem; font-size: 0.9rem; }
        .main-content {
            padding: 2rem 3rem;
            max-width: 900px;
        }
        .page-title {
            margin-bottom: 2rem;
        }
        .page-title h1 {
            color: #9370DB;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #9370DB;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(106, 90, 205, 0.3);
        }
        .section h3 {
            color: #b794f4;
            margin: 1.5rem 0 0.75rem;
        }
        .section p, .section li { line-height: 1.8; color: #ccc; margin-bottom: 0.75rem; }
        .section ul, .section ol { padding-left: 1.5rem; }
        .code-block {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #88ff88;
            overflow-x: auto;
        }
        .code-block.vulnerable { border-color: rgba(255, 68, 68, 0.5); }
        .code-block.secure { border-color: rgba(0, 200, 0, 0.5); }
        .code-label {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
            font-size: 0.75rem;
            margin-bottom: 0.5rem;
        }
        .code-label.vulnerable { background: rgba(255, 68, 68, 0.3); color: #ff6666; }
        .code-label.secure { background: rgba(0, 200, 0, 0.3); color: #66ff66; }
        .tip-box {
            background: rgba(0, 200, 0, 0.1);
            border: 1px solid rgba(0, 200, 0, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .checklist {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .checklist li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .checklist li::before {
            content: '☐';
            color: #9370DB;
        }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(106, 90, 205, 0.3);
        }
        .nav-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            background: rgba(147, 112, 219, 0.2);
            color: #9370DB;
            transition: all 0.3s;
        }
        .nav-btn:hover { background: rgba(147, 112, 219, 0.4); }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">P</span>
                Phabricator
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="index.php">Lab Home</a>
                <a href="login.php">Start Lab</a>
            </nav>
        </div>
    </header>

    <div class="docs-layout">
        <nav class="sidebar">
            <h3>📚 Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="docs.php">Overview</a></li>
                <li><a href="docs-vulnerability.php">The Vulnerability</a></li>
                <li><a href="docs-vulnerability.php#auth-vs-authz" class="sub-item">Auth vs AuthZ</a></li>
                <li><a href="docs-vulnerability.php#api-flaw" class="sub-item">API Design Flaw</a></li>
                <li><a href="docs-exploitation.php">Exploitation Guide</a></li>
                <li><a href="docs-exploitation.php#step-by-step" class="sub-item">Step by Step</a></li>
                <li><a href="docs-exploitation.php#payloads" class="sub-item">Attack Payloads</a></li>
                <li><a href="docs-prevention.php" class="active">Prevention</a></li>
                <li><a href="#secure-code" class="sub-item">Secure Code</a></li>
                <li><a href="#best-practices" class="sub-item">Best Practices</a></li>
                <li><a href="docs-testing.php">Testing Guide</a></li>
                <li><a href="docs-references.php">References</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <div class="page-title">
                <h1>Prevention & Mitigation</h1>
                <p style="color: #888;">How to properly implement authorization in APIs</p>
            </div>

            <div class="section" id="secure-code">
                <h2>🔒 Secure Code Implementation</h2>
                
                <h3>The Fix</h3>
                <p>Add authorization checks to the API endpoint that mirror the UI's access control:</p>
                
                <span class="code-label vulnerable">❌ VULNERABLE CODE</span>
                <div class="code-block vulnerable">
// api/slowvote.php - Missing authorization
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Unauthorized']));
}

$pollId = $_POST['poll_id'];
$stmt = $pdo->prepare("SELECT * FROM slowvotes WHERE id = ?");
$stmt->execute([$pollId]);
$poll = $stmt->fetch();

echo json_encode($poll); // Returns ANY poll!
                </div>

                <span class="code-label secure">✅ SECURE CODE</span>
                <div class="code-block secure">
// api/slowvote.php - With proper authorization
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Unauthorized']));
}

$userId = $_SESSION['user_id'];
$pollId = $_POST['poll_id'];

$stmt = $pdo->prepare("SELECT * FROM slowvotes WHERE id = ?");
$stmt->execute([$pollId]);
$poll = $stmt->fetch();

if (!$poll) {
    http_response_code(404);
    die(json_encode(['error' => 'Poll not found']));
}

// AUTHORIZATION CHECK - Same as UI!
if (!canUserViewPoll($userId, $poll)) {
    http_response_code(403);
    die(json_encode(['error' => 'Forbidden - You do not have access']));
}

echo json_encode($poll);

// Authorization helper function
function canUserViewPoll($userId, $poll) {
    global $pdo;
    
    // Public polls - anyone can view
    if ($poll['visibility'] === 'everyone') {
        return true;
    }
    
    // Creator can always view their own polls
    if ($poll['creator_id'] === $userId) {
        return true;
    }
    
    // Private polls - only creator
    if ($poll['visibility'] === 'nobody') {
        return false;
    }
    
    // Specific visibility - check permissions table
    if ($poll['visibility'] === 'specific') {
        $stmt = $pdo->prepare(
            "SELECT can_view FROM poll_permissions 
             WHERE poll_id = ? AND user_id = ? AND can_view = 1"
        );
        $stmt->execute([$poll['id'], $userId]);
        return $stmt->fetch() !== false;
    }
    
    return false;
}
                </div>
            </div>

            <div class="section" id="best-practices">
                <h2>📋 Best Practices</h2>
                
                <h3>1. Centralized Authorization</h3>
                <p>Create a single authorization layer used by ALL code paths (UI and API):</p>
                <div class="code-block secure">
// lib/Authorization.php
class Authorization {
    public static function checkPollAccess($userId, $pollId) {
        $poll = Poll::findById($pollId);
        
        if (!$poll) {
            throw new NotFoundException();
        }
        
        if (!self::canViewPoll($userId, $poll)) {
            throw new ForbiddenException();
        }
        
        return $poll;
    }
    
    private static function canViewPoll($userId, $poll) {
        // Centralized visibility logic
        // Used by BOTH UI and API
    }
}

// In API endpoint:
$poll = Authorization::checkPollAccess($userId, $pollId);

// In UI controller:
$poll = Authorization::checkPollAccess($userId, $pollId);
                </div>

                <h3>2. Defense in Depth</h3>
                <ul>
                    <li>Apply authorization at multiple layers (database, application, API gateway)</li>
                    <li>Use database-level row security policies where supported</li>
                    <li>Implement API request signing to prevent tampering</li>
                    <li>Log all access attempts for audit purposes</li>
                </ul>

                <h3>3. Indirect Object References</h3>
                <p>Instead of exposing database IDs, use indirect references:</p>
                <div class="code-block secure">
// Instead of: poll_id=2
// Use: poll_ref=a7f3b9c2-user-specific-token

// Server generates user-specific tokens for accessible resources
$userPolls = getAccessiblePolls($userId);
$pollRefs = [];
foreach ($userPolls as $poll) {
    $pollRefs[$poll['id']] = generateUserToken($userId, $poll['id']);
}

// API validates the token belongs to the requesting user
$pollId = validateAndDecodeToken($userId, $_POST['poll_ref']);
                </div>

                <h3>4. Rate Limiting</h3>
                <p>Prevent enumeration attacks with rate limiting:</p>
                <div class="code-block secure">
// Limit API requests per user
$rateLimit = new RateLimiter($userId);
if ($rateLimit->isExceeded('slowvote-api', 100, 60)) { // 100 req/min
    http_response_code(429);
    die(json_encode(['error' => 'Too Many Requests']));
}
                </div>
            </div>

            <div class="section" id="checklist">
                <h2>✅ Security Checklist</h2>
                <div class="checklist">
                    <ul>
                        <li>Implement authorization checks in ALL API endpoints</li>
                        <li>Use the same authorization logic for UI and API</li>
                        <li>Create a centralized authorization service</li>
                        <li>Test API endpoints directly (not just through UI)</li>
                        <li>Use indirect object references where possible</li>
                        <li>Implement rate limiting on sensitive endpoints</li>
                        <li>Log and monitor access patterns</li>
                        <li>Conduct regular security reviews of API access control</li>
                        <li>Include IDOR testing in penetration test scope</li>
                        <li>Return generic errors (don't reveal object existence)</li>
                    </ul>
                </div>
            </div>

            <div class="section">
                <h2>🛡️ Framework-Specific Solutions</h2>
                
                <h3>Laravel (PHP)</h3>
                <div class="code-block secure">
// Using Laravel Policies
public function view(User $user, Poll $poll)
{
    if ($poll->visibility === 'everyone') return true;
    if ($poll->creator_id === $user->id) return true;
    if ($poll->visibility === 'specific') {
        return $poll->permissions()->where('user_id', $user->id)->exists();
    }
    return false;
}

// In Controller
$this->authorize('view', $poll);
                </div>

                <h3>Spring Boot (Java)</h3>
                <div class="code-block secure">
@PreAuthorize("@pollSecurityService.canView(#pollId, authentication)")
@GetMapping("/api/slowvote/{pollId}")
public ResponseEntity&lt;Poll&gt; getPoll(@PathVariable Long pollId) {
    return ResponseEntity.ok(pollService.findById(pollId));
}
                </div>

                <h3>Express.js (Node)</h3>
                <div class="code-block secure">
const checkPollAccess = async (req, res, next) => {
    const poll = await Poll.findById(req.params.id);
    if (!poll) return res.status(404).json({ error: 'Not found' });
    
    if (!canUserViewPoll(req.user.id, poll)) {
        return res.status(403).json({ error: 'Forbidden' });
    }
    
    req.poll = poll;
    next();
};

router.get('/api/slowvote/:id', checkPollAccess, getPoll);
                </div>
            </div>

            <div class="nav-buttons">
                <a href="docs-exploitation.php" class="nav-btn">← Exploitation Guide</a>
                <a href="docs-testing.php" class="nav-btn">Testing Guide →</a>
            </div>
        </main>
    </div>
</body>
</html>

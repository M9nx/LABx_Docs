<?php
/**
 * Lab 28: Documentation Part 2 - Technical Analysis
 * MTN Developers Portal IDOR
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Analysis - Lab 28</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }
        .logo-icon {
            width: 45px;
            height: 45px;
            background: #000;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
            color: #ffcc00;
        }
        .logo-text {
            font-size: 1.4rem;
            font-weight: bold;
            color: #000;
        }
        .nav-links a {
            color: #000;
            text-decoration: none;
            margin-left: 1.5rem;
            font-weight: 500;
        }
        .main-content {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 204, 0, 0.1);
            border: 1px solid rgba(255, 204, 0, 0.3);
            border-radius: 8px;
            color: #ffcc00;
            text-decoration: none;
            transition: all 0.3s;
        }
        .nav-btn:hover {
            background: rgba(255, 204, 0, 0.2);
        }
        .doc-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid rgba(255, 204, 0, 0.2);
            padding-bottom: 0.5rem;
        }
        .doc-tab {
            padding: 0.75rem 1.5rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            color: #888;
            text-decoration: none;
            transition: all 0.3s;
        }
        .doc-tab:hover {
            color: #ffcc00;
            background: rgba(255, 204, 0, 0.1);
        }
        .doc-tab.active {
            background: rgba(255, 204, 0, 0.15);
            color: #ffcc00;
            border-color: #ffcc00;
        }
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .card h1, .card h2, .card h3 {
            color: #ffcc00;
            margin-bottom: 1rem;
        }
        .card h1 { font-size: 1.75rem; }
        .card h2 { font-size: 1.4rem; margin-top: 1.5rem; }
        .card h3 { font-size: 1.15rem; margin-top: 1.25rem; color: #ff9900; }
        .card p {
            color: #aaa;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .card ul, .card ol {
            padding-left: 1.5rem;
            color: #aaa;
            line-height: 1.8;
        }
        .card li { margin-bottom: 0.5rem; }
        code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            color: #00ff88;
            font-family: 'Consolas', monospace;
        }
        code.block {
            display: block;
            background: #0d1117;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            overflow-x: auto;
            white-space: pre;
            color: #c9d1d9;
            border: 1px solid #30363d;
            font-size: 0.85rem;
            line-height: 1.6;
        }
        .highlight-box {
            background: rgba(255, 204, 0, 0.1);
            border-left: 4px solid #ffcc00;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .danger-box {
            background: rgba(255, 68, 68, 0.1);
            border-left: 4px solid #ff4444;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .info-box {
            background: rgba(68, 136, 255, 0.1);
            border-left: 4px solid #4488ff;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .success-box {
            background: rgba(68, 255, 68, 0.1);
            border-left: 4px solid #44ff44;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .highlight-box h3, .danger-box h3, .info-box h3, .success-box h3 {
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        .danger-box h3 { color: #ff4444; }
        .highlight-box h3 { color: #ffcc00; }
        .info-box h3 { color: #4488ff; }
        .success-box h3 { color: #44ff44; }
        .toc {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .toc h3 {
            color: #ffcc00;
            margin-bottom: 1rem;
            font-size: 1rem;
        }
        .toc ul {
            list-style: none;
            padding: 0;
        }
        .toc li { margin-bottom: 0.5rem; }
        .toc a {
            color: #888;
            text-decoration: none;
        }
        .toc a:hover { color: #ffcc00; }
        .code-comparison {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1rem 0;
        }
        @media (max-width: 800px) {
            .code-comparison { grid-template-columns: 1fr; }
        }
        .code-box {
            background: #0d1117;
            border-radius: 8px;
            overflow: hidden;
        }
        .code-box-header {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .code-box-header.vulnerable {
            background: rgba(255, 68, 68, 0.3);
            color: #ff6b6b;
        }
        .code-box-header.secure {
            background: rgba(68, 255, 68, 0.3);
            color: #6bff6b;
        }
        .code-box code {
            display: block;
            padding: 1rem;
            margin: 0;
            font-size: 0.8rem;
            background: transparent;
            border: none;
            border-radius: 0;
        }
        .vulnerability-marker {
            background: rgba(255, 68, 68, 0.2);
            border-left: 3px solid #ff4444;
            padding-left: 0.5rem;
            margin: 0 -1rem;
            padding-right: 1rem;
        }
        .annotation {
            color: #6a9955;
            font-style: italic;
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="index.php" class="logo">
            <div class="logo-icon">MTN</div>
            <div class="logo-text">Developers Portal</div>
        </a>
        <nav class="nav-links">
            <a href="index.php">← Back to Lab</a>
            <a href="login.php">Login</a>
            <a href="success.php">Submit Flag</a>
        </nav>
    </header>

    <main class="main-content">
        <div class="nav-buttons">
            <a href="docs.php" class="nav-btn">← Part 1: Overview</a>
            <a href="docs-mitigation.php" class="nav-btn">Part 3: Mitigation →</a>
        </div>

        <div class="doc-tabs">
            <a href="docs.php" class="doc-tab">Part 1: Overview</a>
            <a href="docs-technical.php" class="doc-tab active">Part 2: Technical Analysis</a>
            <a href="docs-mitigation.php" class="doc-tab">Part 3: Mitigation</a>
        </div>

        <div class="card">
            <h1>🔬 Technical Analysis: Vulnerable Code</h1>
            <p>
                This section provides an in-depth analysis of the vulnerable code, explaining 
                exactly why the IDOR vulnerability exists and how it can be exploited.
            </p>
        </div>

        <div class="toc">
            <h3>📑 Table of Contents - Part 2</h3>
            <ul>
                <li><a href="#api-endpoint">1. Vulnerable API Endpoint</a></li>
                <li><a href="#code-flow">2. Code Flow Analysis</a></li>
                <li><a href="#auth-vs-authz">3. Authentication vs Authorization</a></li>
                <li><a href="#info-disclosure">4. Information Disclosure</a></li>
                <li><a href="#attack-vectors">5. Attack Vectors</a></li>
                <li><a href="#database">6. Database Schema</a></li>
            </ul>
        </div>

        <div class="card" id="api-endpoint">
            <h2>1. Vulnerable API Endpoint</h2>
            <p>
                The vulnerability exists in the <code>/api/remove_member.php</code> endpoint. 
                Let's examine the code:
            </p>

            <code class="block"><span class="annotation">// api/remove_member.php - VULNERABLE CODE</span>
&lt;?php
require_once '../config.php';

header('Content-Type: application/json');

<span class="annotation">// Step 1: Check if user is logged in (Authentication)</span>
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

<span class="annotation">// Step 2: Parse JSON request body</span>
$data = json_decode(file_get_contents('php://input'), true);

<span class="annotation">// Step 3: Validate required parameters exist</span>
if (!isset($data['team_id']) || !isset($data['user_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$teamId = $data['team_id'];
$userId = $data['user_id'];

<span class="vulnerability-marker"><span class="annotation">// ⚠️ VULNERABILITY: No authorization check!</span>
<span class="annotation">// The code SHOULD verify that $_SESSION['lab28_user_id'] has</span>
<span class="annotation">// owner or admin role on the specified team_id</span>
<span class="annotation">// But this check is MISSING!</span></span>

<span class="annotation">// Step 4: Directly process the removal</span>
$result = removeMemberFromTeam($pdo, $teamId, $userId);
// ... rest of code</code>

            <div class="danger-box">
                <h3>🔴 The Critical Flaw</h3>
                <p>
                    Notice that after authentication (line 7-11), the code jumps directly to 
                    processing the removal. There is <strong>NO code</strong> that checks whether 
                    the logged-in user (<code>$_SESSION['lab28_user_id']</code>) has permission 
                    to manage the team specified in <code>$teamId</code>.
                </p>
            </div>
        </div>

        <div class="card" id="code-flow">
            <h2>2. Code Flow Analysis</h2>
            
            <h3>Current (Vulnerable) Flow</h3>
            <ol>
                <li><strong>Request arrives:</strong> POST to /api/remove_member.php</li>
                <li><strong>Authentication check:</strong> Is user logged in? ✓</li>
                <li><strong>Parameter validation:</strong> Are team_id and user_id provided? ✓</li>
                <li><strong style="color: #ff4444;">Authorization check:</strong> Does user have permission? ✗ SKIPPED</li>
                <li><strong>Process removal:</strong> Call removeMemberFromTeam() directly</li>
                <li><strong>Return response:</strong> Include full user and team details</li>
            </ol>

            <h3>The Helper Function</h3>
            <code class="block"><span class="annotation">// config.php - Helper function (also vulnerable by design)</span>
function removeMemberFromTeam($pdo, $teamId, $userId) {
    <span class="annotation">// This function TRUSTS its inputs</span>
    <span class="annotation">// It assumes the caller has already verified authorization</span>
    
    $stmt = $pdo->prepare("
        DELETE FROM team_members 
        WHERE team_id = ? AND user_id = ?
    ");
    $stmt->execute([$teamId, $userId]);
    
    if ($stmt->rowCount() > 0) {
        <span class="annotation">// Fetch and return user/team details (Information Disclosure)</span>
        return [
            'success' => true,
            'user' => getUserById($pdo, $userId),
            'team' => getTeamById($pdo, $teamId)
        ];
    }
    return ['success' => false];
}</code>

            <div class="info-box">
                <h3>💡 Defense in Depth Failure</h3>
                <p>
                    The helper function also doesn't verify permissions. While helper functions 
                    often trust their callers, ideally authorization should be enforced at 
                    multiple layers (defense in depth).
                </p>
            </div>
        </div>

        <div class="card" id="auth-vs-authz">
            <h2>3. Authentication vs Authorization</h2>
            <p>
                Understanding the difference between authentication and authorization is crucial 
                for understanding this vulnerability:
            </p>

            <div class="code-comparison">
                <div>
                    <div class="highlight-box">
                        <h3>🔐 Authentication (Identity)</h3>
                        <p><strong>"Who are you?"</strong></p>
                        <ul style="margin-top: 0.5rem;">
                            <li>Verifies identity via credentials</li>
                            <li>Establishes a session</li>
                            <li>Proves the user is who they claim to be</li>
                        </ul>
                        <p style="color: #44ff44; margin-top: 0.5rem;">✓ Present in this code</p>
                    </div>
                </div>
                <div>
                    <div class="danger-box">
                        <h3>🛡️ Authorization (Permission)</h3>
                        <p><strong>"What are you allowed to do?"</strong></p>
                        <ul style="margin-top: 0.5rem;">
                            <li>Verifies permission for action</li>
                            <li>Checks role and ownership</li>
                            <li>Enforces access control policies</li>
                        </ul>
                        <p style="color: #ff4444; margin-top: 0.5rem;">✗ Missing in this code</p>
                    </div>
                </div>
            </div>

            <h3>What's Missing</h3>
            <p>The code should include something like:</p>
            <code class="block"><span class="annotation">// This authorization check is MISSING from the vulnerable code</span>
$actorId = $_SESSION['lab28_user_id'];
$actorRole = getUserTeamRole($pdo, $teamId, $actorId);

if ($actorRole !== 'owner' && $actorRole !== 'admin') {
    http_response_code(403);
    echo json_encode([
        'success' => false, 
        'error' => 'You do not have permission to manage this team'
    ]);
    exit;
}</code>
        </div>

        <div class="card" id="info-disclosure">
            <h2>4. Information Disclosure</h2>
            <p>
                Beyond the IDOR, the API also leaks sensitive information in its response:
            </p>

            <code class="block"><span class="annotation">// The vulnerable response includes PII</span>
{
    "success": true,
    "message": "Member removed successfully",
    "removed_user": {
        "user_id": "1113",
        "username": "carol_admin",      <span class="annotation">// Leaked username</span>
        "full_name": "Carol Administrator", <span class="annotation">// Leaked full name</span>
        "email": "carol@mtn.com"        <span class="annotation">// Leaked email</span>
    },
    "from_team": {
        "team_id": "0002",
        "name": "Team B - API Integration", <span class="annotation">// Leaked team name</span>
        "description": "Bob's team..."     <span class="annotation">// Leaked description</span>
    }
}</code>

            <div class="danger-box">
                <h3>🔴 Why This Matters</h3>
                <ul>
                    <li><strong>Email Harvesting:</strong> Attacker can enumerate emails by trying different user_ids</li>
                    <li><strong>Team Enumeration:</strong> Discover team names and purposes</li>
                    <li><strong>Social Engineering:</strong> Use leaked info for phishing attacks</li>
                    <li><strong>Privacy Violation:</strong> Exposing PII without consent</li>
                </ul>
            </div>

            <h3>Proper Response Design</h3>
            <p>A secure response should be minimal:</p>
            <code class="block"><span class="annotation">// Secure response - minimal information</span>
{
    "success": true,
    "message": "Member removed successfully"
}

<span class="annotation">// Or for errors - don't leak existence info</span>
{
    "success": false,
    "error": "Unable to complete this action"  <span class="annotation">// Generic message</span>
}</code>
        </div>

        <div class="card" id="attack-vectors">
            <h2>5. Attack Vectors</h2>
            <p>This vulnerability enables multiple attack scenarios:</p>

            <h3>Vector 1: Targeted Member Removal</h3>
            <p>Remove specific users from specific teams if you know their IDs.</p>
            <code class="block">POST /api/remove_member.php
{"team_id": "0002", "user_id": "1113"}</code>

            <h3>Vector 2: Mass Enumeration & Removal</h3>
            <p>Script to remove all users from all teams:</p>
            <code class="block">for team_id in range(0001, 9999):
    for user_id in range(1111, 9999):
        POST /api/remove_member.php
        {"team_id": str(team_id), "user_id": str(user_id)}
        <span class="annotation"># Response reveals if user existed in team</span></code>

            <h3>Vector 3: Information Gathering</h3>
            <p>Even failed removals might leak info (depending on error handling):</p>
            <code class="block"><span class="annotation">// If the error reveals "User not found in team"</span>
<span class="annotation">// vs "Team not found" vs "User not found"</span>
<span class="annotation">// Attacker can enumerate valid IDs</span></code>

            <div class="highlight-box">
                <h3>💡 ID Format Discovery</h3>
                <p>
                    In this lab, IDs are 4-digit numbers (0001-9999). In real applications, 
                    IDs might be UUIDs, sequential integers, or other formats. Understanding 
                    the ID format is key to exploitation.
                </p>
            </div>
        </div>

        <div class="card" id="database">
            <h2>6. Database Schema</h2>
            <p>Understanding the database structure helps in exploitation:</p>

            <code class="block"><span class="annotation">-- Key tables for this vulnerability</span>

CREATE TABLE users (
    user_id VARCHAR(10) PRIMARY KEY,  <span class="annotation">-- e.g., "1111"</span>
    username VARCHAR(50) UNIQUE,
    full_name VARCHAR(100),
    email VARCHAR(100),
    password_hash VARCHAR(255)
);

CREATE TABLE teams (
    team_id VARCHAR(10) PRIMARY KEY,  <span class="annotation">-- e.g., "0001"</span>
    name VARCHAR(100),
    description TEXT,
    created_by VARCHAR(10)  <span class="annotation">-- References users.user_id</span>
);

CREATE TABLE team_members (
    team_id VARCHAR(10),
    user_id VARCHAR(10),
    role ENUM('owner', 'admin', 'member'),
    joined_at TIMESTAMP,
    PRIMARY KEY (team_id, user_id),
    FOREIGN KEY (team_id) REFERENCES teams(team_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

<span class="annotation">-- The vulnerability allows DELETE on team_members</span>
<span class="annotation">-- for any team_id/user_id combination</span></code>

            <div class="info-box">
                <h3>💡 Exploitation Insight</h3>
                <p>
                    The <code>team_members</code> table has a composite primary key (team_id, user_id). 
                    The DELETE statement in the vulnerable code removes exactly one row matching 
                    both values. By iterating through IDs, an attacker could empty all teams.
                </p>
            </div>
        </div>

        <div class="nav-buttons" style="margin-top: 2rem;">
            <a href="docs.php" class="nav-btn">← Part 1: Overview</a>
            <a href="docs-mitigation.php" class="nav-btn">Part 3: Mitigation →</a>
        </div>
    </main>
</body>
</html>

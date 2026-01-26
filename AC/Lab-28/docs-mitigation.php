<?php
/**
 * Lab 28: Documentation Part 3 - Mitigation & Secure Implementation
 * MTN Developers Portal IDOR
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mitigation Guide - Lab 28</title>
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
        .annotation {
            color: #6a9955;
            font-style: italic;
        }
        .checklist {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .checklist-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 204, 0, 0.1);
        }
        .checklist-item:last-child {
            border-bottom: none;
        }
        .check-icon {
            width: 24px;
            height: 24px;
            background: #44ff44;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-size: 14px;
            flex-shrink: 0;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #000;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 0.5rem 0.5rem 0.5rem 0;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 204, 0, 0.4);
        }
        .btn-secondary {
            background: transparent;
            border: 2px solid #ffcc00;
            color: #ffcc00;
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
            <a href="docs-technical.php" class="nav-btn">← Part 2: Technical Analysis</a>
            <a href="index.php" class="nav-btn">Back to Lab →</a>
        </div>

        <div class="doc-tabs">
            <a href="docs.php" class="doc-tab">Part 1: Overview</a>
            <a href="docs-technical.php" class="doc-tab">Part 2: Technical Analysis</a>
            <a href="docs-mitigation.php" class="doc-tab active">Part 3: Mitigation</a>
        </div>

        <div class="card">
            <h1>🛡️ Mitigation & Secure Implementation</h1>
            <p>
                This section provides comprehensive guidance on fixing the IDOR vulnerability 
                and implementing secure access control in team management functionality.
            </p>
        </div>

        <div class="toc">
            <h3>📑 Table of Contents - Part 3</h3>
            <ul>
                <li><a href="#quick-fix">1. Quick Fix</a></li>
                <li><a href="#secure-implementation">2. Secure Implementation</a></li>
                <li><a href="#defense-in-depth">3. Defense in Depth</a></li>
                <li><a href="#response-design">4. Secure Response Design</a></li>
                <li><a href="#testing">5. Testing for IDOR</a></li>
                <li><a href="#checklist">6. Security Checklist</a></li>
                <li><a href="#references">7. References & Resources</a></li>
            </ul>
        </div>

        <div class="card" id="quick-fix">
            <h2>1. Quick Fix</h2>
            <p>
                The minimum fix requires adding an authorization check before processing the 
                removal request:
            </p>

            <code class="block"><span class="annotation">// Add this BEFORE calling removeMemberFromTeam()</span>

$actorId = $_SESSION['lab28_user_id'];
$actorRole = getUserTeamRole($pdo, $teamId, $actorId);

<span class="annotation">// Only owners and admins can remove members</span>
if ($actorRole !== 'owner' && $actorRole !== 'admin') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Forbidden: You do not have permission to manage this team'
    ]);
    exit;
}

<span class="annotation">// Additional check: Cannot remove the owner</span>
$targetRole = getUserTeamRole($pdo, $teamId, $userId);
if ($targetRole === 'owner') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Cannot remove team owner. Transfer ownership first.'
    ]);
    exit;
}

<span class="annotation">// Now safe to proceed</span>
$result = removeMemberFromTeam($pdo, $teamId, $userId);</code>

            <div class="success-box">
                <h3>✅ This Fix Ensures</h3>
                <ul>
                    <li>Only team owners and admins can remove members</li>
                    <li>Team owners cannot be removed (prevents orphaned teams)</li>
                    <li>Proper HTTP status codes (403 Forbidden)</li>
                </ul>
            </div>
        </div>

        <div class="card" id="secure-implementation">
            <h2>2. Secure Implementation</h2>
            <p>
                Here's a complete secure implementation of the remove member endpoint:
            </p>

            <code class="block">&lt;?php
<span class="annotation">// api/remove_member.php - SECURE VERSION</span>
require_once '../config.php';

header('Content-Type: application/json');

<span class="annotation">// 1. Authentication Check</span>
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

<span class="annotation">// 2. Rate Limiting (prevent enumeration)</span>
if (!checkRateLimit($_SESSION['lab28_user_id'], 'remove_member', 10)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Too many requests']);
    exit;
}

<span class="annotation">// 3. Input Validation</span>
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['team_id']) || !isset($data['user_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

<span class="annotation">// Sanitize and validate format</span>
$teamId = filter_var($data['team_id'], FILTER_SANITIZE_STRING);
$userId = filter_var($data['user_id'], FILTER_SANITIZE_STRING);

if (!preg_match('/^\d{4}$/', $teamId) || !preg_match('/^\d{4}$/', $userId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid parameter format']);
    exit;
}

<span class="annotation">// 4. Authorization Check - THE KEY FIX</span>
$actorId = $_SESSION['lab28_user_id'];
$actorRole = getUserTeamRole($pdo, $teamId, $actorId);

if (!in_array($actorRole, ['owner', 'admin'])) {
    <span class="annotation">// Log unauthorized attempt</span>
    logSecurityEvent($pdo, 'unauthorized_remove_attempt', [
        'actor' => $actorId,
        'target_team' => $teamId,
        'target_user' => $userId
    ]);
    
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

<span class="annotation">// 5. Business Logic Validation</span>
$targetRole = getUserTeamRole($pdo, $teamId, $userId);

<span class="annotation">// Cannot remove owner</span>
if ($targetRole === 'owner') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Cannot remove team owner']);
    exit;
}

<span class="annotation">// Admins cannot remove other admins (only owners can)</span>
if ($actorRole === 'admin' && $targetRole === 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Admins cannot remove other admins']);
    exit;
}

<span class="annotation">// 6. Execute the removal</span>
try {
    $result = removeMemberFromTeam($pdo, $teamId, $userId);
    
    <span class="annotation">// Log successful action for audit</span>
    logActivity($pdo, $actorId, 'member_removed', $teamId, $userId);
    
    <span class="annotation">// Return minimal response (no PII)</span>
    echo json_encode([
        'success' => true,
        'message' => 'Member removed successfully'
    ]);
    
} catch (Exception $e) {
    <span class="annotation">// Log error internally, return generic message</span>
    error_log("Remove member error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'An error occurred']);
}</code>
        </div>

        <div class="card" id="defense-in-depth">
            <h2>3. Defense in Depth</h2>
            <p>
                A robust security implementation includes multiple layers of protection:
            </p>

            <h3>Layer 1: API Gateway / WAF</h3>
            <ul>
                <li>Rate limiting at the edge</li>
                <li>Request validation</li>
                <li>Anomaly detection</li>
            </ul>

            <h3>Layer 2: Application Middleware</h3>
            <ul>
                <li>Authentication verification</li>
                <li>CSRF protection</li>
                <li>Session validation</li>
            </ul>

            <h3>Layer 3: Controller/Endpoint</h3>
            <ul>
                <li>Authorization checks (MAIN FIX)</li>
                <li>Input validation</li>
                <li>Business logic validation</li>
            </ul>

            <h3>Layer 4: Service/Repository</h3>
            <ul>
                <li>Additional authorization verification</li>
                <li>Data integrity checks</li>
            </ul>

            <h3>Layer 5: Database</h3>
            <ul>
                <li>Row-level security (PostgreSQL)</li>
                <li>Stored procedure with checks</li>
                <li>Audit triggers</li>
            </ul>

            <div class="info-box">
                <h3>💡 Example: Database-Level Protection</h3>
                <code class="block" style="margin-top: 0.5rem;">CREATE PROCEDURE remove_team_member(
    IN actor_id VARCHAR(10),
    IN p_team_id VARCHAR(10),
    IN p_user_id VARCHAR(10)
)
BEGIN
    DECLARE actor_role VARCHAR(20);
    
    <span class="annotation">-- Verify actor has permission</span>
    SELECT role INTO actor_role 
    FROM team_members 
    WHERE team_id = p_team_id AND user_id = actor_id;
    
    IF actor_role NOT IN ('owner', 'admin') THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Unauthorized';
    END IF;
    
    <span class="annotation">-- Proceed with removal</span>
    DELETE FROM team_members 
    WHERE team_id = p_team_id AND user_id = p_user_id;
END;</code>
            </div>
        </div>

        <div class="card" id="response-design">
            <h2>4. Secure Response Design</h2>
            <p>
                Proper response design prevents information disclosure:
            </p>

            <div class="code-comparison">
                <div class="code-box">
                    <div class="code-box-header vulnerable">❌ Vulnerable Response</div>
                    <code>{
  "success": true,
  "removed_user": {
    "user_id": "1113",
    "username": "carol_admin",
    "full_name": "Carol Admin",
    "email": "carol@mtn.com"
  },
  "from_team": {
    "team_id": "0002",
    "name": "Team B",
    "description": "..."
  }
}</code>
                </div>
                <div class="code-box">
                    <div class="code-box-header secure">✅ Secure Response</div>
                    <code>{
  "success": true,
  "message": "Member removed"
}

<span class="annotation">// For errors:</span>
{
  "success": false,
  "error": "Unable to complete"
}

<span class="annotation">// Note: Same error for</span>
<span class="annotation">// - user not found</span>
<span class="annotation">// - team not found</span>
<span class="annotation">// - no permission</span></code>
                </div>
            </div>

            <h3>Response Design Principles</h3>
            <ul>
                <li><strong>Minimal Information:</strong> Return only what the client needs</li>
                <li><strong>Generic Errors:</strong> Don't reveal what specifically failed</li>
                <li><strong>No PII in Responses:</strong> Never include user details unless absolutely necessary</li>
                <li><strong>Consistent Timing:</strong> Prevent timing-based enumeration</li>
            </ul>
        </div>

        <div class="card" id="testing">
            <h2>5. Testing for IDOR</h2>
            <p>
                Security testing should include these IDOR-specific tests:
            </p>

            <h3>Manual Testing Checklist</h3>
            <ol>
                <li>Login as User A, capture a valid request</li>
                <li>Change object IDs to resources owned by User B</li>
                <li>Test with:
                    <ul>
                        <li>Sequential IDs (user_id + 1)</li>
                        <li>Known IDs from other users</li>
                        <li>Random/guessed IDs</li>
                    </ul>
                </li>
                <li>Verify proper 403 Forbidden response</li>
                <li>Check that no data is leaked in error responses</li>
            </ol>

            <h3>Automated Testing Example</h3>
            <code class="block"><span class="annotation"># Python script to test for IDOR</span>
import requests

def test_idor_member_removal():
    <span class="annotation"># Login as attacker (user A)</span>
    session = requests.Session()
    session.post('/login', data={'user': 'attacker', 'pass': 'attacker123'})
    
    <span class="annotation"># Try to remove user from team we don't own</span>
    response = session.post('/api/remove_member.php', json={
        'team_id': '0002',  <span class="annotation"># Bob's team (not ours)</span>
        'user_id': '1113'   <span class="annotation"># Carol</span>
    })
    
    <span class="annotation"># Should be 403 Forbidden</span>
    assert response.status_code == 403, f"IDOR FOUND! Got {response.status_code}"
    
    <span class="annotation"># Should not contain user details</span>
    assert 'carol' not in response.text.lower()
    assert 'email' not in response.text.lower()
    
    print("✓ IDOR protection working correctly")</code>
        </div>

        <div class="card" id="checklist">
            <h2>6. Security Checklist</h2>
            
            <div class="checklist">
                <div class="checklist-item">
                    <span class="check-icon">✓</span>
                    <div>
                        <strong>Authorization on every request</strong>
                        <p style="color: #888; font-size: 0.9rem;">Verify the authenticated user has permission to access/modify the requested resource</p>
                    </div>
                </div>
                <div class="checklist-item">
                    <span class="check-icon">✓</span>
                    <div>
                        <strong>Server-side validation</strong>
                        <p style="color: #888; font-size: 0.9rem;">Never trust client-supplied IDs without verification</p>
                    </div>
                </div>
                <div class="checklist-item">
                    <span class="check-icon">✓</span>
                    <div>
                        <strong>Indirect object references</strong>
                        <p style="color: #888; font-size: 0.9rem;">Consider using per-user mappings instead of direct database IDs</p>
                    </div>
                </div>
                <div class="checklist-item">
                    <span class="check-icon">✓</span>
                    <div>
                        <strong>Rate limiting</strong>
                        <p style="color: #888; font-size: 0.9rem;">Prevent enumeration attacks with request throttling</p>
                    </div>
                </div>
                <div class="checklist-item">
                    <span class="check-icon">✓</span>
                    <div>
                        <strong>Audit logging</strong>
                        <p style="color: #888; font-size: 0.9rem;">Log all access attempts, especially failures</p>
                    </div>
                </div>
                <div class="checklist-item">
                    <span class="check-icon">✓</span>
                    <div>
                        <strong>Minimal responses</strong>
                        <p style="color: #888; font-size: 0.9rem;">Don't leak information in API responses</p>
                    </div>
                </div>
                <div class="checklist-item">
                    <span class="check-icon">✓</span>
                    <div>
                        <strong>Consistent error messages</strong>
                        <p style="color: #888; font-size: 0.9rem;">Use generic errors to prevent enumeration</p>
                    </div>
                </div>
                <div class="checklist-item">
                    <span class="check-icon">✓</span>
                    <div>
                        <strong>Security testing</strong>
                        <p style="color: #888; font-size: 0.9rem;">Include IDOR tests in your security test suite</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" id="references">
            <h2>7. References & Resources</h2>
            
            <h3>OWASP Resources</h3>
            <ul>
                <li><a href="https://owasp.org/API-Security/editions/2023/en/0xa1-broken-object-level-authorization/" style="color: #4dabf7;">OWASP API Security Top 10 - BOLA</a></li>
                <li><a href="https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/04-Testing_for_Insecure_Direct_Object_References" style="color: #4dabf7;">OWASP Testing Guide - IDOR</a></li>
                <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html" style="color: #4dabf7;">OWASP Authorization Cheat Sheet</a></li>
            </ul>

            <h3>HackerOne Reports</h3>
            <ul>
                <li><a href="https://hackerone.com/reports/1448475" style="color: #4dabf7;">Report #1448475 - MTN IDOR (This Lab's Inspiration)</a></li>
            </ul>

            <h3>Additional Reading</h3>
            <ul>
                <li>PortSwigger Web Security Academy - Access Control</li>
                <li>PentesterLab - IDOR Exercises</li>
                <li>Bug Bounty Bootcamp by Vickie Li - Chapter on IDOR</li>
            </ul>

            <div class="highlight-box">
                <h3>📚 Key Takeaway</h3>
                <p>
                    IDOR vulnerabilities are consistently in the top API security risks. 
                    Always implement proper authorization checks at multiple layers, and 
                    remember that <strong>authentication is not authorization</strong>.
                </p>
            </div>
        </div>

        <div class="card">
            <h2>🎉 Congratulations!</h2>
            <p>
                You've completed the documentation for Lab 28. You now understand:
            </p>
            <ul>
                <li>How IDOR vulnerabilities work</li>
                <li>Why authentication alone is not sufficient</li>
                <li>How to properly implement authorization checks</li>
                <li>Best practices for secure API design</li>
            </ul>
            <br>
            <a href="login.php" class="btn">Practice the Exploit</a>
            <a href="success.php" class="btn btn-secondary">Submit Your Flag</a>
        </div>

        <div class="nav-buttons" style="margin-top: 2rem;">
            <a href="docs-technical.php" class="nav-btn">← Part 2: Technical Analysis</a>
            <a href="index.php" class="nav-btn">Back to Lab Home →</a>
        </div>
    </main>
</body>
</html>

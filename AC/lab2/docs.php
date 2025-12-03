<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 2 Documentation - Unprotected Admin Functionality with Unpredictable URL</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            color: #e0e0e0;
            line-height: 1.7;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 60px;
            padding: 40px 0;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            color: #ff4444;
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 8px rgba(255, 68, 68, 0.3);
        }

        .header .subtitle {
            color: #999;
            font-size: 1.3rem;
            font-weight: 300;
            font-style: italic;
        }

        /* Blog-style content */
        .blog-content {
            max-width: 100%;
        }

        .blog-content h1 {
            color: #ff4444;
            font-size: 2.5rem;
            margin: 50px 0 25px 0;
            padding-bottom: 15px;
            border-bottom: 3px solid #ff4444;
            text-shadow: 0 2px 4px rgba(255, 68, 68, 0.3);
        }

        .blog-content h2 {
            color: #ff5555;
            font-size: 2rem;
            margin: 40px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #444;
        }

        .blog-content h3 {
            color: #ff6666;
            font-size: 1.4rem;
            margin: 30px 0 15px 0;
            font-weight: 600;
        }

        .blog-content h4 {
            color: #ff8888;
            font-size: 1.2rem;
            margin: 25px 0 10px 0;
            font-weight: 500;
        }

        .blog-content p {
            margin: 15px 0;
            text-align: justify;
            color: #ccc;
        }

        .blog-content ul, .blog-content ol {
            margin: 15px 0;
            padding-left: 25px;
        }

        .blog-content li {
            margin: 8px 0;
            color: #ccc;
        }

        .blog-content strong {
            color: #fff;
            font-weight: 600;
        }

        /* Code blocks */
        .code-block {
            background: #111;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 0.9rem;
            line-height: 1.6;
            color: #f0f0f0;
            overflow-x: auto;
            white-space: pre-wrap;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .inline-code {
            background: #222;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 0.9rem;
            color: #ff6666;
            border: 1px solid #444;
        }

        /* Alert boxes */
        .alert {
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
            border-left: 5px solid;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .alert-danger {
            background: rgba(255, 68, 68, 0.1);
            border-left-color: #ff4444;
            color: #ffcccc;
            border: 1px solid rgba(255, 68, 68, 0.3);
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.1);
            border-left-color: #ffc107;
            color: #fff3cd;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .alert-info {
            background: rgba(0, 123, 255, 0.1);
            border-left-color: #007bff;
            color: #cce7ff;
            border: 1px solid rgba(0, 123, 255, 0.3);
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border-left-color: #28a745;
            color: #d4edda;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        /* Navigation */
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin: 50px 0;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            border-color: #ff4444;
        }

        .btn-secondary {
            background: transparent;
            color: #ff4444;
            border-color: #ff4444;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 68, 68, 0.3);
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            background: #111;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        th {
            background: #222;
            color: #ff4444;
            font-weight: 600;
        }

        tr:hover {
            background: rgba(255, 68, 68, 0.05);
        }

        /* Blockquotes */
        blockquote {
            border-left: 4px solid #ff4444;
            padding: 20px 25px;
            margin: 25px 0;
            background: rgba(255, 68, 68, 0.05);
            font-style: italic;
            color: #ddd;
            border-radius: 0 8px 8px 0;
        }

        /* Section dividers */
        hr {
            border: none;
            border-top: 2px solid #333;
            margin: 40px 0;
        }

        /* Steps styling */
        .step {
            background: rgba(255, 68, 68, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .step-number {
            background: #ff4444;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }

        /* Attack chain styling */
        .attack-chain {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Lab 2 Documentation</h1>
            <p class="subtitle">Information Disclosure via Client-Side Configuration</p>
        </div>

        <div class="blog-content">

            <h1>Advanced Information Disclosure Vulnerability</h1>

            <p>Welcome to the second lab in our access control series.</p>

            <p>This lab demonstrates a more sophisticated vulnerability where <strong>information disclosure</strong> through client-side code leads to unprotected administrative functionality.</p>

            <p>Unlike Lab 1's direct enumeration approach, this vulnerability exploits exposed configuration data to discover hidden admin endpoints.</p>

<div class="alert alert-danger">
<strong>⚠️ Information Disclosure Alert</strong><br>
This vulnerability demonstrates how seemingly innocent client-side code can expose critical system information, leading to complete administrative access.
</div>

            <h2>Understanding Information Disclosure</h2>

            <p>Information disclosure vulnerabilities occur when applications inadvertently reveal sensitive information that should remain confidential.</p>

            <p>In this lab, we focus on:</p>

- **Client-side configuration exposure**: Sensitive URLs embedded in JavaScript
- **Debug information leakage**: Development artifacts in production
- **Unpredictable URL discovery**: Finding "hidden" admin endpoints through code analysis
- **Attack chain exploitation**: Combining information disclosure with access control failures

            <h3>Why This Matters</h3>

            <p>Modern web applications often use client-side frameworks and configuration files.</p>

            <p>Developers frequently make the mistake of:</p>

1. **Hardcoding sensitive URLs** in client-accessible JavaScript

2. **Leaving debug functions** in production code

3. **Exposing internal API endpoints** through configuration objects

4. **Using "security by obscurity"** instead of proper access controls

            <hr>

            <h1>Step-by-Step Exploitation Walkthrough</h1>

            <h2>Prerequisites</h2>

            <p>Before starting this lab, ensure you have:</p>

- XAMPP running with Apache and MySQL services
- Lab deployed to <span class="inline-code">http://localhost/AC/lab2/</span>
- Web browser with Developer Tools access
- Understanding of JavaScript and browser console usage

            <h2>Discovery Phase</h2>

<div class="step">
<span class="step-number">1</span>
<strong>Initial Application Reconnaissance</strong>

Navigate to the lab URL:

<div class="code-block">http://localhost/AC/lab2/</div>

This appears to be a more polished application compared to Lab 1.

Notice:
- Modern interface design
- No obvious admin links or hints
- Seemingly secure from directory enumeration
</div>

<div class="step">
<span class="step-number">2</span>
<strong>Source Code Analysis</strong>

Right-click anywhere on the page and select "View Page Source" or press <span class="inline-code">Ctrl+U</span>.

Look for JavaScript code and configuration objects that might contain sensitive information.
</div>

<div class="step">
<span class="step-number">3</span>
<strong>Browser Developer Tools Investigation</strong>

Open Developer Tools (<span class="inline-code">F12</span>) and examine:

- **Console tab**: Look for any logged information
- **Sources tab**: Examine JavaScript files
- **Network tab**: Monitor requests and responses
</div>

<div class="step">
<span class="step-number">4</span>
<strong>JavaScript Configuration Discovery</strong>

In the page source or console, you'll find a configuration object similar to:

<div class="code-block">const config = {
    apiEndpoints: {
        users: '/api/users',
        products: '/api/products',
        admin: '/admin-panel-x7k9p2m5q8w1.php'  // EXPOSED!
    }
};</div>

This reveals the "unpredictable" admin URL!
</div>

            <h2>Exploitation Phase</h2>

<div class="step">
<span class="step-number">5</span>
<strong>Console-Based Discovery</strong>

In the browser console, try these commands:

<div class="code-block">// Check if configuration is globally accessible
console.log(window.appConfig);

// Look for admin-specific functions
getAdminPanelUrl();

// Use the provided helper function
quickAdminAccess();</div>
</div>

<div class="step">
<span class="step-number">6</span>
<strong>Direct URL Access</strong>

Using the discovered URL, navigate to:

<div class="code-block">http://localhost/AC/lab2/admin-panel-x7k9p2m5q8w1.php</div>

Just like Lab 1, you'll gain immediate administrative access without authentication!
</div>

<div class="step">
<span class="step-number">7</span>
<strong>Impact Demonstration</strong>

Complete the lab by performing any administrative action such as:

- Deleting a user account
- Viewing sensitive user information
- Modifying system configurations

This demonstrates the complete compromise achieved through information disclosure.
</div>

            <hr>

            <h1>Comprehensive Vulnerability Analysis</h1>

            <h2>Information Disclosure in Client-Side Code</h2>

The primary vulnerability stems from exposing sensitive configuration in client-side JavaScript:

<div class="code-block">// VULNERABILITY: Admin endpoint exposed in client-side configuration
const config = {
    apiEndpoints: {
        users: '/api/users',
        products: '/api/products',
        admin: '/admin-panel-x7k9p2m5q8w1.php'  // Exposed to all users!
    }
};

// VULNERABILITY: Global exposure of configuration
window.appConfig = config;</div>

## Developer Debug Functions

The application includes development helper functions that remain accessible in production:

<div class="code-block">// VULNERABILITY: Development functions in production
function getAdminPanelUrl() {
    return window.appConfig.apiEndpoints.admin;
}

function quickAdminAccess() {
    window.location.href = getAdminPanelUrl();
}

// VULNERABILITY: Console information disclosure
console.log('%cFor admin access, use: quickAdminAccess()', 'color: #764ba2;');
console.log('%cAdmin panel URL: ' + window.appConfig?.apiEndpoints?.admin, 'color: #ff6b6b;');</div>

## Server-Side Access Control Failures

The discovered admin panel suffers from the same access control issues as Lab 1:

<div class="code-block">&lt;?php
// VULNERABILITY: No authentication checks
// MISSING: session_start() and authentication validation

require_once 'config.php';

// VULNERABILITY: No authorization checks  
// MISSING: if ($_SESSION['role'] !== 'admin') { deny access }

// Direct processing of admin operations
if (isset($_POST['delete_user'])) {
    $userId = $_POST['user_id']; // No input validation!
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    echo "User deleted successfully!";
}

// Displaying all user data without access control
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?&gt;</div>

---

# Attack Chain Analysis

This vulnerability demonstrates a sophisticated **attack chain** where multiple security failures combine:

Each step in the chain builds upon the previous vulnerability, creating a complete compromise scenario.

<div class="attack-chain">
<strong>🔗 Complete Attack Chain</strong>

1. **Information Gathering** → Reconnaissance of client-side code
2. **Information Disclosure** → Discovery of admin URL in JavaScript
3. **URL Access** → Direct navigation to exposed admin endpoint  
4. **Access Control Bypass** → No authentication/authorization checks
5. **Privilege Escalation** → Full administrative access achieved
6. **Impact Realization** → Data manipulation, deletion, or theft
</div>

## Multiple Vulnerability Classes

This lab demonstrates several OWASP Top 10 vulnerabilities:

| Vulnerability | OWASP Category | Severity | Impact |
|---------------|----------------|----------|--------|
| Information Disclosure | A01:2021 - Broken Access Control | High | Reveals sensitive URLs |
| Unprotected Admin Functionality | A01:2021 - Broken Access Control | Critical | Complete system access |
| Insecure Configuration | A05:2021 - Security Misconfiguration | Medium | Development artifacts in production |
| Client-Side Security Issues | A08:2021 - Software and Data Integrity Failures | Medium | Exposed sensitive configuration |

---

# Comprehensive Mitigation Strategies

## Client-Side Security Fixes

### Environment-Based Configuration Loading

<div class="code-block">// SECURITY FIX #1: Environment-specific configuration
const config = {
    apiEndpoints: {
        users: '/api/users',
        products: '/api/products'
        // NOTE: Admin endpoints completely removed from client-side config
    },
    environment: 'production', // No debug mode in production
    features: {
        debugConsole: false,
        adminShortcuts: false
    }
};

// SECURITY FIX #2: No global object exposure
// Removed: window.appConfig = config;

// Use module patterns instead
const getApiEndpoint = (endpoint) => {
    const allowedEndpoints = ['users', 'products'];
    if (allowedEndpoints.includes(endpoint)) {
        return config.apiEndpoints[endpoint];
    }
    throw new Error('Unauthorized endpoint access');
};</div>

### Production Build Process

<div class="code-block">// SECURITY FIX #3: Build-time security checks
// Use environment variables for sensitive configurations
const ADMIN_ENDPOINT = process.env.NODE_ENV === 'production' 
    ? undefined  // Never expose in production
    : '/admin-panel.php';  // Only in development

// SECURITY FIX #4: Remove debug functions in production builds
if (process.env.NODE_ENV !== 'production') {
    window.debugFunctions = {
        getAdminUrl: () => ADMIN_ENDPOINT,
        quickAdminAccess: () => window.location.href = ADMIN_ENDPOINT
    };
}</div>

## Server-Side Security Implementation

### Proper Authentication and Authorization

<div class="code-block">&lt;?php
// SECURITY FIX #5: Comprehensive access control
session_start();

// Check authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    http_response_code(401);
    header('Location: /login.php?error=authentication_required');
    exit;
}

// Check authorization
if ($_SESSION['role'] !== 'admin') {
    error_log("Unauthorized admin access attempt by user: " . $_SESSION['username']);
    http_response_code(403);
    header('Location: /unauthorized.php');
    exit;
}

// Additional security measures
if (!validateSessionToken($_SESSION['user_id'], $_SESSION['token'])) {
    session_destroy();
    http_response_code(401);
    header('Location: /login.php?error=session_expired');
    exit;
}
?&gt;</div>

### URL Security and Access Control

<div class="code-block">// SECURITY FIX #6: Proper URL management
// Generate unpredictable URLs with proper validation
function generateSecureAdminUrl($sessionId, $userId) {
    $urlToken = hash('sha256', $sessionId . $userId . SECRET_KEY . time());
    return "/admin/{$urlToken}.php";
}

// SECURITY FIX #7: Time-limited URL access
function validateAdminUrlAccess($urlToken, $sessionId, $userId) {
    $stored_token = getStoredUrlToken($userId);
    $creation_time = getUrlCreationTime($urlToken);
    
    // Check token validity and expiration (e.g., 1 hour)
    if (!hash_equals($stored_token, $urlToken)) {
        return false;
    }
    
    if (time() - $creation_time > 3600) {
        deleteExpiredUrlToken($urlToken);
        return false;
    }
    
    return true;
}</div>

---

# Advanced Security Measures

## Content Security Policy (CSP)

<div class="code-block">&lt;!-- SECURITY FIX #8: Implement CSP to prevent script injection --&gt;
&lt;meta http-equiv="Content-Security-Policy" 
      content="default-src 'self'; 
               script-src 'self' 'unsafe-inline'; 
               object-src 'none'; 
               base-uri 'self';"&gt;</div>

## Information Leakage Prevention

<div class="code-block">// SECURITY FIX #9: Sanitize client-side logging
const logger = {
    log: (message, data = null) => {
        if (config.environment !== 'production') {
            console.log(message, data);
        }
        // Send to secure logging endpoint instead
        sendToSecureLog('INFO', message, data);
    },
    
    error: (message, error = null) => {
        // Never log sensitive information
        const sanitizedError = sanitizeErrorForLogging(error);
        sendToSecureLog('ERROR', message, sanitizedError);
    }
};

// SECURITY FIX #10: Remove all admin references from client code
// No hardcoded admin URLs
// No debug functions in production
// No console messages revealing system information</div>

## Runtime Security Monitoring

<div class="code-block">&lt;?php
// SECURITY FIX #11: Monitor for suspicious activities
function detectUnusualAccess($userId, $endpoint) {
    // Log admin panel access attempts
    if (strpos($endpoint, 'admin') !== false) {
        $logData = [
            'user_id' => $userId,
            'endpoint' => $endpoint,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'timestamp' => time(),
            'session_id' => session_id()
        ];
        
        logSecurityEvent('ADMIN_ACCESS_ATTEMPT', $logData);
        
        // Check for brute force patterns
        if (detectBruteForcePattern($userId, $_SERVER['REMOTE_ADDR'])) {
            blockIpAddress($_SERVER['REMOTE_ADDR']);
            alertSecurityTeam('POTENTIAL_BRUTE_FORCE', $logData);
        }
    }
}
?&gt;</div>

---

# Security Best Practices for Modern Web Applications

## Development Process Security

<div class="alert alert-success">
<strong>✅ Secure Development Checklist</strong>

1. **Separate Configurations**: Use different config files for development/production

2. **Environment Variables**: Store sensitive data in environment variables, not code

3. **Build Process Security**: Remove debug code and sensitive information during production builds

4. **Code Review**: Review all client-side code for information disclosure

5. **Security Testing**: Include client-side security in your testing process
</div>

## Client-Side Security Principles

1. **Trust Nothing**: Never store sensitive information on the client

2. **Minimize Exposure**: Only send necessary data to the client

3. **Validate Server-Side**: All security decisions must be made on the server

4. **Use CSP**: Implement Content Security Policy to prevent injection attacks

5. **Monitor Client Behavior**: Log and analyze client-side security events

## Server-Side Access Control

1. **Defense in Depth**: Multiple layers of security controls

2. **Principle of Least Privilege**: Grant minimum necessary permissions

3. **Session Management**: Proper session handling and validation

4. **Audit Logging**: Comprehensive logging of administrative actions

5. **Regular Security Reviews**: Periodic assessment of access controls

---

# Conclusion

This lab demonstrates how **information disclosure vulnerabilities** can be chained with **access control failures** to achieve complete system compromise.

The key lessons include understanding that modern attacks often combine multiple vulnerabilities rather than relying on a single exploit.

## Critical Takeaways

1. **Client-side security is an illusion** - Never rely on hiding information in client code

2. **Development artifacts are dangerous** - Remove all debug code from production

3. **Defense in depth is essential** - Multiple security layers prevent single points of failure

4. **Information disclosure enables other attacks** - Small leaks can lead to major breaches

5. **Modern applications need modern security** - Traditional approaches may miss client-side vulnerabilities

## The Modern Threat Landscape

Today's web applications face increasingly sophisticated attacks that combine multiple vulnerabilities.

This lab showcases how attackers chain discoveries together:

**Traditional Attack**: Directory enumeration → Admin panel discovery

**Modern Attack**: Client-side analysis → Information disclosure → Admin panel access → Privilege escalation

<blockquote>
"The security of a system is only as strong as its weakest link. In modern web applications, that link is often the unnecessary information we expose to our users."
<br><em>- Security Principle</em>
</blockquote>

        </div>

        <div class="nav-buttons">
            <a href="lab-description.php" class="btn btn-secondary">← Lab Description</a>
            <a href="success.php" class="btn btn-secondary">Success Page →</a>
            <a href="../index.php" class="btn btn-primary">← Back to Labs</a>
        </div>
    </div>
</body>
</html>
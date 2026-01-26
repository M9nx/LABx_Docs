<?php
// Lab 29: LinkedPro Newsletter Platform - Lab Description Page
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - Lab 29: Newsletter Subscriber IDOR</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #0a0a0f 0%, #0f1419 50%, #0a0a0f 100%);
            color: #e0e0e0;
            min-height: 100vh;
        }
        .nav-bar {
            background: rgba(10, 10, 15, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(10, 102, 194, 0.2);
        }
        .nav-logo {
            font-size: 1.4rem;
            font-weight: bold;
            color: #0a66c2;
            text-decoration: none;
        }
        .nav-logo span {
            color: #057642;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
        }
        .nav-links a {
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        .nav-links a:hover, .nav-links a.active {
            color: #0a66c2;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(10, 102, 194, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(10, 102, 194, 0.2);
            border-color: #0a66c2;
            color: #0a66c2;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .back-link:hover {
            color: #0a66c2;
        }
        .lab-header {
            background: linear-gradient(135deg, rgba(10, 102, 194, 0.1), rgba(5, 118, 66, 0.1));
            border: 1px solid rgba(10, 102, 194, 0.2);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .lab-badge {
            display: inline-block;
            background: linear-gradient(135deg, #0a66c2, #004182);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 16px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .difficulty-badge {
            display: inline-block;
            background: rgba(255, 170, 0, 0.15);
            color: #ffaa00;
            padding: 0.4rem 0.8rem;
            border-radius: 16px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }
        .lab-header h1 {
            font-size: 1.75rem;
            color: #fff;
            margin-bottom: 0.75rem;
        }
        .lab-header p {
            color: #888;
            line-height: 1.6;
        }
        .section {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .section h2 {
            color: #0a66c2;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section p {
            color: #aaa;
            line-height: 1.7;
            margin-bottom: 1rem;
        }
        .section ul, .section ol {
            color: #aaa;
            margin-left: 1.5rem;
            line-height: 1.8;
        }
        .section li {
            margin-bottom: 0.5rem;
        }
        .section code {
            background: rgba(10, 102, 194, 0.15);
            color: #7fc4fd;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        .code-block {
            background: #1a1a2e;
            border: 1px solid rgba(10, 102, 194, 0.2);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-block code {
            background: none;
            color: #7fc4fd;
            padding: 0;
            font-size: 0.85rem;
            line-height: 1.6;
        }
        .objective-box {
            background: linear-gradient(135deg, rgba(5, 118, 66, 0.1), rgba(5, 118, 66, 0.05));
            border: 1px solid rgba(5, 118, 66, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .objective-box h3 {
            color: #057642;
            margin-bottom: 0.75rem;
        }
        .objective-box p {
            color: #aaa;
            margin-bottom: 0;
        }
        .flag-format {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }
        .flag-format strong {
            color: #ffa500;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0a66c2, #004182);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(10, 102, 194, 0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #0a66c2;
            border: 1px solid rgba(10, 102, 194, 0.3);
        }
        .btn-secondary:hover {
            background: rgba(10, 102, 194, 0.1);
        }
        .reference-link {
            color: #0a66c2;
            text-decoration: none;
        }
        .reference-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <nav class="nav-bar">
        <a href="index.php" class="nav-logo">Linked<span>Pro</span></a>
        <div class="nav-links">
            <a href="../index.php" class="btn-back">← All Labs</a>
            <a href="index.php">Home</a>
            <a href="lab-description.php" class="active">Lab Info</a>
            <a href="docs.php">Documentation</a>
            <a href="login.php">Login</a>
        </div>
    </nav>
    
    <div class="container">
        
        <div class="lab-header">
            <span class="lab-badge">Lab 29</span>
            <span class="difficulty-badge">🟠 Practitioner</span>
            <h1>Unauthorized User can View Subscribers of Other Users Newsletters</h1>
            <p>A LinkedPro-style professional platform where newsletter creators can see their subscriber list, but a missing authorization check allows any authenticated user to view subscriber data of ANY newsletter.</p>
        </div>
        
        <div class="objective-box">
            <h3>🎯 Lab Objective</h3>
            <p>Access the subscriber list of a newsletter you don't own by exploiting the IDOR vulnerability in the API endpoint. The subscriber list contains sensitive PII including email addresses, job titles, and professional information.</p>
            <div class="flag-format">
                <strong>Flag Format:</strong> <code>FLAG{...}</code> - Displayed when you successfully access another user's newsletter subscribers
            </div>
        </div>
        
        <div class="section">
            <h2>📖 Background Story</h2>
            <p>LinkedPro is a professional networking platform where thought leaders can create newsletters. Followers can subscribe to receive updates, and creators can view their subscriber list through a "Subscribers" button on their newsletter dashboard.</p>
            <p>The subscriber list is sensitive because it reveals:</p>
            <ul>
                <li>Professional email addresses</li>
                <li>Job titles and companies</li>
                <li>Geographic locations</li>
                <li>Connection counts</li>
                <li>Subscription preferences</li>
            </ul>
        </div>
        
        <div class="section">
            <h2>🔍 Vulnerability Details</h2>
            <p>The API endpoint that returns subscriber data is:</p>
            <div class="code-block">
                <code>GET /api/get_subscribers.php?seriesUrn=urn:li:fsd_contentSeries:&lt;NewsletterId&gt;&count=10&start=0</code>
            </div>
            <p>The <code>seriesUrn</code> parameter (newsletter identifier) is publicly visible on newsletter pages. The vulnerability exists because:</p>
            <ul>
                <li>The API accepts any valid <code>seriesUrn</code> parameter</li>
                <li>No authorization check verifies if the requesting user owns the newsletter</li>
                <li>The response includes full subscriber details including PII</li>
            </ul>
        </div>
        
        <div class="section">
            <h2>📋 Attack Steps</h2>
            <ol>
                <li>Log in to the platform (any account works, including attacker account)</li>
                <li>Browse the Newsletters page to see all available newsletters</li>
                <li>Note the <code>newsletter_urn</code> of a newsletter you don't own (displayed on each newsletter card)</li>
                <li>If you're a newsletter creator, click "Subscribers" on your own newsletter to see the legitimate request</li>
                <li>Capture the API request in your browser's developer tools or with Burp Suite</li>
                <li>Replay the request, replacing the <code>seriesUrn</code> with the victim's newsletter URN</li>
                <li>The response will contain the full subscriber list with PII!</li>
            </ol>
        </div>
        
        <div class="section">
            <h2>🔗 Alternative Attack Vector</h2>
            <p>You can also directly access the subscribers page for any newsletter by manipulating the URL:</p>
            <div class="code-block">
                <code>/subscribers.php?id=&lt;newsletter_id&gt;</code>
            </div>
            <p>The page will display all subscribers even if you don't own the newsletter.</p>
        </div>
        
        <div class="section">
            <h2>⚠️ Real-World Impact</h2>
            <p>This vulnerability type was reported to LinkedIn (HackerOne). In real scenarios, this could lead to:</p>
            <ul>
                <li><strong>Privacy Breach:</strong> Exposure of subscriber emails and professional information</li>
                <li><strong>Competitive Intelligence:</strong> Competitors could identify who follows industry thought leaders</li>
                <li><strong>Targeted Phishing:</strong> Attackers could craft targeted phishing campaigns using subscriber data</li>
                <li><strong>Social Engineering:</strong> Information about connections and job titles aids social engineering attacks</li>
            </ul>
        </div>
        
        <div class="section">
            <h2>🧪 Test Accounts</h2>
            <div class="code-block">
                <code>Attacker:    attacker / attacker123<br>
Creator 1:   alice_ceo / alice123 (owns "Tech Leadership Weekly")<br>
Creator 2:   bob_investor / bob123 (owns "Venture Capital Insider")<br>
Creator 3:   carol_professor / carol123 (owns "AI Research Digest")</code>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            <a href="docs.php" class="btn btn-secondary">📚 Full Documentation</a>
            <a href="index.php" class="btn btn-secondary">← Back to Lab Home</a>
        </div>
    </div>
</body>
</html>

<div class="doc-header">
    <h1>📋 Overview</h1>
    <p>Understanding multi-step process access control vulnerabilities</p>
</div>

<div class="content-section">
    <h2>What is a Multi-Step Process?</h2>
    <p>
        Multi-step processes are common in web applications, especially for sensitive administrative
        operations. They break down complex actions into sequential steps, often requiring user
        confirmation at each stage. Common examples include:
    </p>
    <ul>
        <li>User role management wizards</li>
        <li>Financial transaction confirmations</li>
        <li>Account deletion workflows</li>
        <li>Order placement and checkout processes</li>
        <li>Configuration change approval systems</li>
    </ul>
    
    <p>
        While these multi-step processes improve user experience and reduce accidental actions,
        they can introduce security vulnerabilities if access controls are not consistently
        applied across <strong>all steps</strong>.
    </p>
</div>

<div class="content-section">
    <h2>The Security Challenge</h2>
    <p>
        Developers often assume that if access control is enforced on the first step, subsequent
        steps are "automatically" protected. This assumption is fundamentally flawed because:
    </p>
    <ul>
        <li>Each HTTP request is independent and stateless</li>
        <li>Attackers can directly request any endpoint in the sequence</li>
        <li>Session state doesn't guarantee the user went through previous steps</li>
        <li>Parameters can be manipulated between steps</li>
    </ul>

    <div class="warning-box">
        <h4>⚠️ Common Misconception</h4>
        <p>
            "The user can only reach Step 3 by going through Steps 1 and 2, so Step 3 doesn't
            need its own access control check." — This is a dangerous assumption that leads
            to access control bypass vulnerabilities.
        </p>
    </div>
</div>

<div class="content-section">
    <h2>Lab Scenario Overview</h2>
    <p>
        In this lab, you'll explore a vulnerable admin panel that implements a 3-step process
        for changing user roles:
    </p>

    <div class="flow-diagram">
        <div class="flow-step protected">
            <div class="step-title">Step 1</div>
            <div class="step-desc">Select User</div>
            <div class="step-status">✅ Admin check enforced</div>
        </div>
        <span class="flow-arrow">→</span>
        <div class="flow-step protected">
            <div class="step-title">Step 2</div>
            <div class="step-desc">Choose New Role</div>
            <div class="step-status">✅ Admin check enforced</div>
        </div>
        <span class="flow-arrow">→</span>
        <div class="flow-step vulnerable">
            <div class="step-title">Step 3</div>
            <div class="step-desc">Confirm Change</div>
            <div class="step-status">❌ NO admin check!</div>
        </div>
    </div>

    <p>
        The vulnerability exists because the confirmation step (admin-confirm.php) only verifies
        that the user is logged in, but doesn't verify that they have admin privileges.
    </p>
</div>

<div class="content-section">
    <h2>Impact Assessment</h2>
    <p>
        The impact of this vulnerability can be severe:
    </p>
    <ul>
        <li><strong>Privilege Escalation:</strong> Non-admin users can promote themselves to admin</li>
        <li><strong>Unauthorized Actions:</strong> Attackers can perform admin-only operations</li>
        <li><strong>Complete System Compromise:</strong> Once admin, attacker has full control</li>
        <li><strong>Data Breach Risk:</strong> Admin access may expose sensitive information</li>
    </ul>

    <div class="danger-box">
        <h4>🔴 Critical Severity</h4>
        <p>
            This type of vulnerability typically receives a CVSS score of 8.0+ (High/Critical)
            because it allows direct privilege escalation to administrative roles.
        </p>
    </div>
</div>

<div class="content-section">
    <h2>Learning Objectives</h2>
    <p>By completing this lab, you will:</p>
    <ol>
        <li>Understand how multi-step processes can be vulnerable to access control bypass</li>
        <li>Learn to identify missing authorization checks in sequential workflows</li>
        <li>Practice exploiting the vulnerability using direct HTTP requests</li>
        <li>Understand the importance of consistent authorization across all endpoints</li>
        <li>Learn prevention techniques for securing multi-step processes</li>
    </ol>
</div>

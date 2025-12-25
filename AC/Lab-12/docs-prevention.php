<div class="doc-header">
    <h1>🛡️ Prevention</h1>
    <p>How to properly secure multi-step processes</p>
</div>

<div class="content-section">
    <h2>Core Principle</h2>
    <p>
        The fundamental rule for securing multi-step processes:
    </p>
    
    <div class="danger-box">
        <h4>🔑 Golden Rule</h4>
        <p style="font-size: 1.1rem; font-weight: bold;">
            Every step in a multi-step process must independently verify that the user
            is authorized to perform the action, regardless of previous steps.
        </p>
    </div>

    <p>
        Never rely on previous steps to have performed authorization. Each endpoint
        is independently accessible and must be independently secured.
    </p>
</div>

<div class="content-section">
    <h2>Fix 1: Add Authorization to All Steps</h2>
    <p>The most straightforward fix is to add the admin check to Step 3:</p>
    
    <div class="code-block">
        <code><span class="comment">// admin-confirm.php - FIXED VERSION</span>
<span class="function">session_start</span>();
<span class="keyword">require_once</span> <span class="string">'config.php'</span>;

<span class="comment">// ✅ SECURE: Check both login AND admin role</span>
<span class="keyword">if</span> (!<span class="function">isset</span>(<span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>]) || <span class="variable">$_SESSION</span>[<span class="string">'role'</span>] !== <span class="string">'admin'</span>) {
    <span class="function">header</span>(<span class="string">"Location: login.php"</span>);
    <span class="keyword">exit</span>;
}

<span class="comment">// Now proceed with the role change...</span>
<span class="variable">$username</span> = <span class="variable">$_POST</span>[<span class="string">'username'</span>] ?? <span class="string">''</span>;
<span class="variable">$role</span> = <span class="variable">$_POST</span>[<span class="string">'role'</span>] ?? <span class="string">''</span>;
<span class="comment">// ... rest of the logic</span></code>
    </div>
</div>

<div class="content-section">
    <h2>Fix 2: Use Centralized Authorization</h2>
    <p>Create a reusable authorization function to ensure consistency:</p>
    
    <div class="code-block">
        <code><span class="comment">// auth.php - Centralized authorization functions</span>

<span class="keyword">function</span> <span class="function">requireLogin</span>() {
    <span class="keyword">if</span> (!<span class="function">isset</span>(<span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>])) {
        <span class="function">header</span>(<span class="string">"Location: login.php"</span>);
        <span class="keyword">exit</span>;
    }
}

<span class="keyword">function</span> <span class="function">requireAdmin</span>() {
    <span class="function">requireLogin</span>();
    
    <span class="keyword">if</span> (<span class="variable">$_SESSION</span>[<span class="string">'role'</span>] !== <span class="string">'admin'</span>) {
        <span class="function">http_response_code</span>(<span class="number">403</span>);
        <span class="keyword">die</span>(<span class="string">'Access denied: Admin privileges required'</span>);
    }
}

<span class="keyword">function</span> <span class="function">requirePermission</span>(<span class="variable">$permission</span>) {
    <span class="function">requireLogin</span>();
    
    <span class="variable">$userPermissions</span> = <span class="function">getUserPermissions</span>(<span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>]);
    <span class="keyword">if</span> (!<span class="function">in_array</span>(<span class="variable">$permission</span>, <span class="variable">$userPermissions</span>)) {
        <span class="function">http_response_code</span>(<span class="number">403</span>);
        <span class="keyword">die</span>(<span class="string">'Access denied: Insufficient permissions'</span>);
    }
}</code>
    </div>

    <p>Then use it consistently across all steps:</p>
    
    <div class="code-block">
        <code><span class="comment">// admin.php, admin-roles.php, admin-confirm.php</span>
<span class="keyword">require_once</span> <span class="string">'auth.php'</span>;
<span class="function">requireAdmin</span>(); <span class="comment">// All steps use the same check</span></code>
    </div>
</div>

<div class="content-section">
    <h2>Fix 3: Use Anti-CSRF Tokens with State</h2>
    <p>Add tokens that are validated across steps to prevent direct access:</p>
    
    <div class="code-block">
        <code><span class="comment">// Step 1: Generate a workflow token</span>
<span class="variable">$_SESSION</span>[<span class="string">'role_change_token'</span>] = <span class="function">bin2hex</span>(<span class="function">random_bytes</span>(<span class="number">32</span>));
<span class="variable">$_SESSION</span>[<span class="string">'role_change_step'</span>] = <span class="number">1</span>;
<span class="variable">$_SESSION</span>[<span class="string">'role_change_started'</span>] = <span class="function">time</span>();

<span class="comment">// Step 2: Verify step progression</span>
<span class="keyword">if</span> (<span class="variable">$_SESSION</span>[<span class="string">'role_change_step'</span>] !== <span class="number">1</span>) {
    <span class="keyword">die</span>(<span class="string">'Invalid workflow state'</span>);
}
<span class="variable">$_SESSION</span>[<span class="string">'role_change_step'</span>] = <span class="number">2</span>;

<span class="comment">// Step 3: Verify entire workflow</span>
<span class="keyword">if</span> (<span class="variable">$_SESSION</span>[<span class="string">'role_change_step'</span>] !== <span class="number">2</span>) {
    <span class="keyword">die</span>(<span class="string">'Invalid workflow state'</span>);
}
<span class="keyword">if</span> (<span class="variable">$_POST</span>[<span class="string">'token'</span>] !== <span class="variable">$_SESSION</span>[<span class="string">'role_change_token'</span>]) {
    <span class="keyword">die</span>(<span class="string">'Invalid CSRF token'</span>);
}
<span class="comment">// Also check time limit (e.g., 15 minutes)</span>
<span class="keyword">if</span> (<span class="function">time</span>() - <span class="variable">$_SESSION</span>[<span class="string">'role_change_started'</span>] > <span class="number">900</span>) {
    <span class="keyword">die</span>(<span class="string">'Session expired'</span>);
}</code>
    </div>

    <div class="warning-box">
        <h4>⚠️ Defense in Depth</h4>
        <p>
            Workflow tokens add an extra layer but should <strong>never</strong> replace
            proper authorization checks. Always verify the user's permissions at every step.
        </p>
    </div>
</div>

<div class="content-section">
    <h2>Fix 4: Database-Level Role Verification</h2>
    <p>Don't rely on session data alone—verify from the database:</p>
    
    <div class="code-block">
        <code><span class="comment">// Get fresh role data from database</span>
<span class="keyword">function</span> <span class="function">isUserAdmin</span>(<span class="variable">$userId</span>) {
    <span class="keyword">global</span> <span class="variable">$conn</span>;
    
    <span class="variable">$stmt</span> = <span class="variable">$conn</span>-><span class="function">prepare</span>(<span class="string">"SELECT role FROM users WHERE id = ?"</span>);
    <span class="variable">$stmt</span>-><span class="function">bind_param</span>(<span class="string">"i"</span>, <span class="variable">$userId</span>);
    <span class="variable">$stmt</span>-><span class="function">execute</span>();
    <span class="variable">$result</span> = <span class="variable">$stmt</span>-><span class="function">get_result</span>();
    <span class="variable">$user</span> = <span class="variable">$result</span>-><span class="function">fetch_assoc</span>();
    
    <span class="keyword">return</span> <span class="variable">$user</span> && <span class="variable">$user</span>[<span class="string">'role'</span>] === <span class="string">'admin'</span>;
}

<span class="comment">// Use it in admin-confirm.php</span>
<span class="keyword">if</span> (!<span class="function">isUserAdmin</span>(<span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>])) {
    <span class="function">http_response_code</span>(<span class="number">403</span>);
    <span class="keyword">die</span>(<span class="string">'Unauthorized'</span>);
}</code>
    </div>
</div>

<div class="content-section">
    <h2>Fix 5: Use Authorization Middleware</h2>
    <p>In frameworks, use middleware to enforce authorization:</p>
    
    <div class="code-block">
        <code><span class="comment">// Laravel Example</span>
Route::<span class="function">middleware</span>([<span class="string">'auth'</span>, <span class="string">'admin'</span>])-><span class="function">group</span>(<span class="keyword">function</span>() {
    Route::<span class="function">get</span>(<span class="string">'/admin/step1'</span>, [AdminController::<span class="keyword">class</span>, <span class="string">'step1'</span>]);
    Route::<span class="function">get</span>(<span class="string">'/admin/step2'</span>, [AdminController::<span class="keyword">class</span>, <span class="string">'step2'</span>]);
    Route::<span class="function">post</span>(<span class="string">'/admin/confirm'</span>, [AdminController::<span class="keyword">class</span>, <span class="string">'confirm'</span>]);
});

<span class="comment">// Express.js Example</span>
<span class="keyword">const</span> <span class="variable">adminAuth</span> = (<span class="variable">req</span>, <span class="variable">res</span>, <span class="variable">next</span>) => {
    <span class="keyword">if</span> (!<span class="variable">req</span>.user || <span class="variable">req</span>.user.role !== <span class="string">'admin'</span>) {
        <span class="keyword">return</span> <span class="variable">res</span>.<span class="function">status</span>(<span class="number">403</span>).<span class="function">json</span>({ error: <span class="string">'Forbidden'</span> });
    }
    <span class="function">next</span>();
};

<span class="variable">router</span>.<span class="function">post</span>(<span class="string">'/admin/confirm'</span>, <span class="variable">adminAuth</span>, <span class="variable">confirmHandler</span>);</code>
    </div>
</div>

<div class="content-section">
    <h2>Security Checklist</h2>
    <p>When implementing multi-step processes, verify:</p>
    
    <table style="width: 100%; border-collapse: collapse; margin: 1rem 0;">
        <thead>
            <tr style="background: rgba(0, 200, 0, 0.2);">
                <th style="padding: 1rem; text-align: left; border: 1px solid #333; color: #66ff66;">Check</th>
                <th style="padding: 1rem; text-align: left; border: 1px solid #333; color: #66ff66;">Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;">Every step verifies authorization independently</td>
                <td style="padding: 1rem; border: 1px solid #333;">☐</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;">Authorization logic is centralized</td>
                <td style="padding: 1rem; border: 1px solid #333;">☐</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;">CSRF tokens are validated</td>
                <td style="padding: 1rem; border: 1px solid #333;">☐</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;">Session timeout is enforced</td>
                <td style="padding: 1rem; border: 1px solid #333;">☐</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;">Direct endpoint access is tested</td>
                <td style="padding: 1rem; border: 1px solid #333;">☐</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;">Audit logging is in place</td>
                <td style="padding: 1rem; border: 1px solid #333;">☐</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="content-section">
    <h2>Testing Your Fixes</h2>
    <p>After implementing fixes, verify by testing:</p>
    <ol>
        <li>Direct POST to confirmation endpoint with non-admin session</li>
        <li>Skipping steps in the workflow</li>
        <li>Using expired workflow tokens</li>
        <li>Replaying captured admin requests</li>
        <li>Modifying parameters between steps</li>
    </ol>

    <div class="info-box">
        <h4>💡 Automated Testing</h4>
        <p>
            Include access control tests in your CI/CD pipeline. Tools like OWASP ZAP
            can automatically test for authorization bypass issues.
        </p>
    </div>
</div>

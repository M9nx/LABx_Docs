<style>
    .doc-section h1 { font-size: 2.2rem; color: #ff4444; margin-bottom: 1rem; }
    .doc-section h2 { font-size: 1.5rem; color: #ff6666; margin: 2rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255, 68, 68, 0.3); }
    .doc-section h3 { font-size: 1.2rem; color: #ff8888; margin: 1.5rem 0 0.75rem; }
    .doc-section p { color: #ccc; line-height: 1.8; margin-bottom: 1rem; }
    .doc-section ul, .doc-section ol { margin: 1rem 0 1rem 1.5rem; color: #ccc; }
    .doc-section li { margin-bottom: 0.5rem; line-height: 1.6; }
    .doc-section code { background: rgba(255, 68, 68, 0.15); padding: 0.2rem 0.5rem; border-radius: 4px; font-family: 'Consolas', monospace; font-size: 0.9rem; color: #ff8888; }
    .doc-section pre { background: #0d0d0d; border: 1px solid #333; border-radius: 10px; padding: 1.5rem; overflow-x: auto; margin: 1rem 0; }
    .doc-section pre code { background: none; padding: 0; color: #88ff88; font-size: 0.85rem; }
    .success-box { background: rgba(0, 200, 0, 0.1); border: 1px solid rgba(0, 200, 0, 0.4); border-radius: 10px; padding: 1.5rem; margin: 1.5rem 0; }
    .success-box h4 { color: #66ff66; margin-bottom: 0.5rem; }
    .info-box { background: rgba(0, 150, 255, 0.1); border: 1px solid rgba(0, 150, 255, 0.4); border-radius: 10px; padding: 1.5rem; margin: 1.5rem 0; }
    .info-box h4 { color: #00aaff; margin-bottom: 0.5rem; }
    .comparison-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin: 1.5rem 0; }
    @media (max-width: 768px) { .comparison-grid { grid-template-columns: 1fr; } }
    .vulnerable-code { background: rgba(255, 68, 68, 0.1); border: 1px solid rgba(255, 68, 68, 0.4); border-radius: 10px; padding: 1.5rem; }
    .secure-code { background: rgba(0, 200, 0, 0.1); border: 1px solid rgba(0, 200, 0, 0.4); border-radius: 10px; padding: 1.5rem; }
    .code-title { font-weight: bold; margin-bottom: 0.5rem; }
    .vulnerable-code .code-title { color: #ff6666; }
    .secure-code .code-title { color: #66ff66; }
    .defense-layer { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 68, 68, 0.3); border-radius: 10px; padding: 1.5rem; margin: 1rem 0; }
    .defense-layer h4 { color: #ff6666; margin-bottom: 0.5rem; }
    .checklist { list-style: none; margin: 0; padding: 0; }
    .checklist li { padding: 0.5rem 0; display: flex; align-items: flex-start; gap: 0.75rem; }
    .checklist li::before { content: "☐"; color: #888; }
</style>

<div class="doc-section">
    <h1>Prevention Strategies</h1>
    <p>
        This section covers secure coding practices and defense-in-depth strategies to prevent 
        IDOR vulnerabilities like the one demonstrated in this lab.
    </p>

    <h2>Fix 1: Object-Level Authorization</h2>
    <p>
        The most direct fix is to verify that the banner belongs to the specified campaign before 
        deletion:
    </p>

    <div class="comparison-grid">
        <div class="vulnerable-code">
            <div class="code-title">❌ Vulnerable Code</div>
            <pre><code>// Deletes ANY banner by ID
$stmt = $pdo->prepare("
    DELETE FROM banners 
    WHERE banner_id = ?
");
$stmt->execute([$bannerId]);</code></pre>
        </div>

        <div class="secure-code">
            <div class="code-title">✅ Secure Code</div>
            <pre><code>// Only deletes if banner belongs 
// to the specified campaign
$stmt = $pdo->prepare("
    DELETE FROM banners 
    WHERE banner_id = ? 
    AND campaign_id = ?
");
$stmt->execute([
    $bannerId, 
    $campaignId
]);</code></pre>
        </div>
    </div>

    <h2>Fix 2: Full Ownership Chain Validation</h2>
    <p>
        For maximum security, validate the entire ownership chain in a single query:
    </p>

    <pre><code class="language-php">// Comprehensive ownership validation
$stmt = $pdo->prepare("
    DELETE b FROM banners b
    INNER JOIN campaigns c ON b.campaign_id = c.campaign_id
    INNER JOIN clients cl ON c.client_id = cl.client_id
    WHERE b.banner_id = ?
      AND c.campaign_id = ?
      AND cl.client_id = ?
      AND cl.manager_id = ?
");
$stmt->execute([$bannerId, $campaignId, $clientId, $_SESSION['manager_id']]);

if ($stmt->rowCount() === 0) {
    // Either banner doesn't exist OR user doesn't have permission
    die("Access denied or banner not found");
}</code></pre>

    <div class="success-box">
        <h4>✅ Why This Works</h4>
        <p>
            The JOIN-based deletion only succeeds if ALL relationships are valid: the banner belongs 
            to the campaign, the campaign belongs to the client, and the client belongs to the 
            authenticated manager. Any break in this chain prevents deletion.
        </p>
    </div>

    <h2>Fix 3: Authorization Helper Function</h2>
    <p>
        Create a reusable authorization function for consistent access control:
    </p>

    <pre><code class="language-php">class Authorization {
    private $pdo;
    
    public function canAccessBanner($managerId, $bannerId) {
        $stmt = $this->pdo->prepare("
            SELECT b.banner_id
            FROM banners b
            JOIN campaigns c ON b.campaign_id = c.campaign_id
            JOIN clients cl ON c.client_id = cl.client_id
            WHERE b.banner_id = ?
              AND cl.manager_id = ?
        ");
        $stmt->execute([$bannerId, $managerId]);
        return $stmt->fetch() !== false;
    }
    
    public function canAccessCampaign($managerId, $campaignId) {
        $stmt = $this->pdo->prepare("
            SELECT c.campaign_id
            FROM campaigns c
            JOIN clients cl ON c.client_id = cl.client_id
            WHERE c.campaign_id = ?
              AND cl.manager_id = ?
        ");
        $stmt->execute([$campaignId, $managerId]);
        return $stmt->fetch() !== false;
    }
}

// Usage in banner-delete.php
$auth = new Authorization($pdo);
if (!$auth->canAccessBanner($_SESSION['manager_id'], $bannerId)) {
    http_response_code(403);
    die("Access denied");
}</code></pre>

    <h2>Defense-in-Depth Layers</h2>

    <div class="defense-layer">
        <h4>🔐 Layer 1: Use Indirect References</h4>
        <p>Instead of exposing database IDs, use indirect references that are meaningless to attackers:</p>
        <pre><code>// Map internal IDs to random tokens
$bannerRef = bin2hex(random_bytes(16));  // "a3f2c1d8e9..."
$_SESSION['banner_refs'][$bannerRef] = $bannerId;

// In URLs
banner-delete.php?ref=a3f2c1d8e9...

// On server, resolve reference to ID
$bannerId = $_SESSION['banner_refs'][$_GET['ref']] ?? null;</code></pre>
    </div>

    <div class="defense-layer">
        <h4>🔐 Layer 2: UUID Primary Keys</h4>
        <p>Use UUIDs instead of sequential integers to make enumeration impractical:</p>
        <pre><code>-- Table definition with UUID
CREATE TABLE banners (
    banner_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    banner_name VARCHAR(255),
    campaign_id CHAR(36),
    ...
);

-- Example ID: 550e8400-e29b-41d4-a716-446655440000</code></pre>
    </div>

    <div class="defense-layer">
        <h4>🔐 Layer 3: Audit Logging</h4>
        <p>Log all resource access attempts for detection and forensics:</p>
        <pre><code class="language-php">function logAccess($userId, $resourceType, $resourceId, $action, $success) {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO access_log (user_id, resource_type, resource_id, action, 
                                success, ip_address, timestamp)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $userId, $resourceType, $resourceId, $action, 
        $success ? 1 : 0, $_SERVER['REMOTE_ADDR']
    ]);
}

// Usage
logAccess($_SESSION['manager_id'], 'banner', $bannerId, 'delete', $success);</code></pre>
    </div>

    <div class="defense-layer">
        <h4>🔐 Layer 4: Rate Limiting</h4>
        <p>Implement rate limiting to slow down enumeration attacks:</p>
        <pre><code class="language-php">function checkRateLimit($userId, $action, $maxAttempts = 100, $window = 3600) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM access_log 
        WHERE user_id = ? AND action = ? 
          AND timestamp > DATE_SUB(NOW(), INTERVAL ? SECOND)
    ");
    $stmt->execute([$userId, $action, $window]);
    $count = $stmt->fetchColumn();
    
    if ($count >= $maxAttempts) {
        http_response_code(429);
        die("Rate limit exceeded. Try again later.");
    }
}</code></pre>
    </div>

    <h2>Secure Architecture Patterns</h2>

    <h3>Repository Pattern with Built-in Authorization</h3>
    <pre><code class="language-php">class BannerRepository {
    private $pdo;
    private $currentUserId;
    
    public function __construct($pdo, $currentUserId) {
        $this->pdo = $pdo;
        $this->currentUserId = $currentUserId;
    }
    
    // All queries automatically filter by ownership
    public function findById($bannerId) {
        $stmt = $this->pdo->prepare("
            SELECT b.* FROM banners b
            JOIN campaigns c ON b.campaign_id = c.campaign_id
            JOIN clients cl ON c.client_id = cl.client_id
            WHERE b.banner_id = ?
              AND cl.manager_id = ?
        ");
        $stmt->execute([$bannerId, $this->currentUserId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function delete($bannerId) {
        // Uses same authorization-aware query
        $banner = $this->findById($bannerId);
        if (!$banner) {
            throw new AccessDeniedException("Banner not found or access denied");
        }
        
        $stmt = $this->pdo->prepare("DELETE FROM banners WHERE banner_id = ?");
        return $stmt->execute([$bannerId]);
    }
}</code></pre>

    <h2>Security Review Checklist</h2>
    <div class="info-box">
        <h4>🔍 IDOR Prevention Checklist</h4>
        <ul class="checklist">
            <li>All direct object references include ownership validation</li>
            <li>Authorization checks verify the entire ownership chain</li>
            <li>Object IDs are validated server-side, not just client-side</li>
            <li>Indirect reference maps are used where appropriate</li>
            <li>UUIDs are used instead of sequential integers for sensitive resources</li>
            <li>Access attempts are logged with success/failure status</li>
            <li>Rate limiting prevents rapid enumeration</li>
            <li>Authorization logic is centralized and reusable</li>
            <li>Unit tests cover authorization edge cases</li>
            <li>Regular security reviews check for IDOR patterns</li>
        </ul>
    </div>
</div>

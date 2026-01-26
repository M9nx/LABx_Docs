<h1 class="doc-title">Prevention & Secure Coding</h1>
<p class="doc-subtitle">How to properly implement authorization and prevent IDOR vulnerabilities</p>

<div class="section">
    <h2>The Fix</h2>
    <p>
        The vulnerability is fixed by adding an authorization check that verifies the logged-in 
        user is requesting their own data:
    </p>
    
    <div class="code-block">
<span class="comment">// SECURE CODE - api/getUserNotes.php</span>

<span class="comment">// ✓ Authentication check</span>
<span class="keyword">if</span> (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

<span class="comment">// Parse request</span>
$data = json_decode(file_get_contents('php://input'), true);
$requestedEmail = $data['params']['updates'][0]['value']['userEmail'];

<span class="comment">// ✓ AUTHORIZATION CHECK - Verify ownership!</span>
<span class="keyword">if</span> ($requestedEmail !== $_SESSION['email']) {
    http_response_code(403);
    echo json_encode([
        'error' => 'Forbidden',
        'message' => 'You can only access your own data'
    ]);
    exit;
}

<span class="comment">// Now safe to query - user is requesting their own data</span>
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$requestedEmail]);
$userData = $stmt->fetch();
    </div>
    
    <div class="success-box">
        <h4>✅ Why This Works</h4>
        <p>
            By comparing the requested email against the authenticated session's email, we ensure 
            users can only retrieve data that belongs to them.
        </p>
    </div>
</div>

<div class="section">
    <h2>Alternative: Remove User-Controlled Parameter</h2>
    <p>
        An even more secure approach is to not accept the email from the client at all:
    </p>
    
    <div class="code-block">
<span class="comment">// MOST SECURE - Don't trust client input</span>

<span class="comment">// ✓ Authentication check</span>
<span class="keyword">if</span> (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

<span class="comment">// ✓ Use ONLY the session email - ignore any client-provided email</span>
$userEmail = $_SESSION['email'];

<span class="comment">// Query using the authenticated user's email only</span>
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$userEmail]);
$userData = $stmt->fetch();
    </div>
    
    <div class="note-box">
        <h4>💡 Defense in Depth</h4>
        <p>
            When designing APIs, ask: "Does the client need to specify this parameter?" If the 
            server already knows who the user is (from the session), don't accept user identifiers 
            from the request body.
        </p>
    </div>
</div>

<div class="section">
    <h2>Authorization Patterns</h2>
    <p>Different scenarios require different authorization approaches:</p>
    
    <table>
        <tr>
            <th>Scenario</th>
            <th>Authorization Check</th>
            <th>Example</th>
        </tr>
        <tr>
            <td>Own Data Only</td>
            <td>Session user matches resource owner</td>
            <td><code>$resource->user_id === $_SESSION['user_id']</code></td>
        </tr>
        <tr>
            <td>Role-Based</td>
            <td>User has required role/permission</td>
            <td><code>$_SESSION['role'] === 'admin'</code></td>
        </tr>
        <tr>
            <td>Organization-Based</td>
            <td>User belongs to same organization</td>
            <td><code>$resource->org_id === $_SESSION['org_id']</code></td>
        </tr>
        <tr>
            <td>Hierarchical</td>
            <td>User manages the resource owner</td>
            <td><code>isManagerOf($_SESSION['user_id'], $resource->user_id)</code></td>
        </tr>
        <tr>
            <td>Shared Resources</td>
            <td>Resource explicitly shared with user</td>
            <td><code>$resource->isSharedWith($_SESSION['user_id'])</code></td>
        </tr>
    </table>
</div>

<div class="section">
    <h2>Secure API Design Principles</h2>
    
    <h3>1. Never Trust Client Input for Identity</h3>
    <div class="code-block">
<span class="comment">// ❌ BAD - Trusting client-provided user identifier</span>
$userId = $_POST['user_id'];
$data = getUserData($userId);

<span class="comment">// ✓ GOOD - Using server-side session</span>
$userId = $_SESSION['user_id'];
$data = getUserData($userId);
    </div>
    
    <h3>2. Validate All Object References</h3>
    <div class="code-block">
<span class="comment">// ❌ BAD - Direct object access without validation</span>
$noteId = $_GET['note_id'];
$note = getNoteById($noteId);

<span class="comment">// ✓ GOOD - Verify ownership before access</span>
$noteId = $_GET['note_id'];
$note = getNoteById($noteId);
<span class="keyword">if</span> ($note->user_id !== $_SESSION['user_id']) {
    http_response_code(403);
    exit('Access denied');
}
    </div>
    
    <h3>3. Use Indirect References</h3>
    <div class="code-block">
<span class="comment">// ❌ BAD - Exposing database IDs</span>
/api/notes/12345  <span class="comment">// Sequential ID is guessable</span>

<span class="comment">// ✓ GOOD - Use UUIDs or user-scoped indices</span>
/api/notes/a1b2c3d4-e5f6-7890  <span class="comment">// UUID not guessable</span>
/api/notes/1  <span class="comment">// "1" means user's 1st note, not global note #1</span>
    </div>
    
    <h3>4. Implement Row-Level Security</h3>
    <div class="code-block">
<span class="comment">// Include user context in ALL queries</span>
$stmt = $pdo->prepare("
    SELECT * FROM notes 
    WHERE id = ? AND user_id = ?
");
$stmt->execute([$noteId, $_SESSION['user_id']]);
    </div>
</div>

<div class="section">
    <h2>Framework-Specific Solutions</h2>
    
    <h3>Laravel (PHP)</h3>
    <div class="code-block">
<span class="comment">// Using Policies for authorization</span>
<span class="keyword">class</span> NotePolicy {
    <span class="keyword">public function</span> view(User $user, Note $note) {
        <span class="keyword">return</span> $user->id === $note->user_id;
    }
}

<span class="comment">// In controller</span>
<span class="keyword">public function</span> show(Note $note) {
    $this->authorize('view', $note);
    <span class="keyword">return</span> $note;
}
    </div>
    
    <h3>Express.js (Node)</h3>
    <div class="code-block">
<span class="comment">// Authorization middleware</span>
<span class="keyword">const</span> authorizeOwner = <span class="keyword">async</span> (req, res, next) => {
    <span class="keyword">const</span> resource = <span class="keyword">await</span> Resource.findById(req.params.id);
    
    <span class="keyword">if</span> (resource.userId !== req.user.id) {
        <span class="keyword">return</span> res.status(403).json({ error: 'Forbidden' });
    }
    
    req.resource = resource;
    next();
};

app.get('/api/notes/:id', authenticate, authorizeOwner, getNote);
    </div>
    
    <h3>Django (Python)</h3>
    <div class="code-block">
<span class="comment"># Using get_object_or_404 with user filter</span>
<span class="keyword">def</span> get_note(request, note_id):
    note = get_object_or_404(
        Note, 
        id=note_id, 
        user=request.user  <span class="comment"># Ensures ownership</span>
    )
    <span class="keyword">return</span> JsonResponse(note.to_dict())
    </div>
</div>

<div class="section">
    <h2>Security Testing Checklist</h2>
    <p>Before deploying any API endpoint, verify:</p>
    <ul>
        <li>☐ Can authenticated User A access User B's resources by changing IDs?</li>
        <li>☐ Are all user-controlled object references validated against ownership?</li>
        <li>☐ Does the API return 403 (Forbidden) for unauthorized access attempts?</li>
        <li>☐ Are access control checks centralized (not duplicated in every endpoint)?</li>
        <li>☐ Is there logging for authorization failures (potential attack detection)?</li>
        <li>☐ Are sequential/predictable IDs avoided in favor of UUIDs?</li>
        <li>☐ Is row-level security enforced at the database query level?</li>
    </ul>
</div>

<div class="warning-box">
    <h4>⚠️ Common Mistakes to Avoid</h4>
    <ul style="margin: 0.5rem 0 0 1.5rem;">
        <li>Assuming authentication implies authorization</li>
        <li>Checking authorization in frontend only (server must verify)</li>
        <li>Forgetting to check ownership on UPDATE and DELETE operations</li>
        <li>Using role-based checks when ownership checks are needed</li>
        <li>Not validating indirect references (e.g., email → user data)</li>
    </ul>
</div>

<div class="doc-header">
    <h1>💻 Code Examples</h1>
    <p>Vulnerable vs. secure implementations across languages</p>
</div>

<div class="content-section">
    <h2>PHP - Complete Secure Implementation</h2>
    
    <h3>auth.php - Authorization Helper</h3>
    <div class="code-block">
        <code><span class="keyword">&lt;?php</span>
<span class="comment">// auth.php - Centralized authorization</span>

<span class="keyword">class</span> <span class="function">Auth</span> {
    <span class="keyword">private static</span> <span class="variable">$conn</span>;
    
    <span class="keyword">public static function</span> <span class="function">init</span>(<span class="variable">$connection</span>) {
        self::<span class="variable">$conn</span> = <span class="variable">$connection</span>;
    }
    
    <span class="keyword">public static function</span> <span class="function">requireLogin</span>() {
        <span class="keyword">if</span> (<span class="function">session_status</span>() === PHP_SESSION_NONE) {
            <span class="function">session_start</span>();
        }
        
        <span class="keyword">if</span> (!<span class="function">isset</span>(<span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>])) {
            <span class="function">header</span>(<span class="string">"Location: login.php"</span>);
            <span class="keyword">exit</span>;
        }
    }
    
    <span class="keyword">public static function</span> <span class="function">requireAdmin</span>() {
        self::<span class="function">requireLogin</span>();
        
        <span class="comment">// Always check from database, not session</span>
        <span class="variable">$stmt</span> = self::<span class="variable">$conn</span>-><span class="function">prepare</span>(
            <span class="string">"SELECT role FROM users WHERE id = ? AND role = 'admin'"</span>
        );
        <span class="variable">$stmt</span>-><span class="function">bind_param</span>(<span class="string">"i"</span>, <span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>]);
        <span class="variable">$stmt</span>-><span class="function">execute</span>();
        
        <span class="keyword">if</span> (<span class="variable">$stmt</span>-><span class="function">get_result</span>()-><span class="function">num_rows</span> === <span class="number">0</span>) {
            <span class="function">http_response_code</span>(<span class="number">403</span>);
            <span class="keyword">die</span>(<span class="string">'Access denied: Admin privileges required'</span>);
        }
    }
    
    <span class="keyword">public static function</span> <span class="function">generateCSRFToken</span>() {
        <span class="keyword">if</span> (!<span class="function">isset</span>(<span class="variable">$_SESSION</span>[<span class="string">'csrf_token'</span>])) {
            <span class="variable">$_SESSION</span>[<span class="string">'csrf_token'</span>] = <span class="function">bin2hex</span>(<span class="function">random_bytes</span>(<span class="number">32</span>));
        }
        <span class="keyword">return</span> <span class="variable">$_SESSION</span>[<span class="string">'csrf_token'</span>];
    }
    
    <span class="keyword">public static function</span> <span class="function">validateCSRFToken</span>(<span class="variable">$token</span>) {
        <span class="keyword">if</span> (!<span class="function">isset</span>(<span class="variable">$_SESSION</span>[<span class="string">'csrf_token'</span>]) || 
            !<span class="function">hash_equals</span>(<span class="variable">$_SESSION</span>[<span class="string">'csrf_token'</span>], <span class="variable">$token</span>)) {
            <span class="function">http_response_code</span>(<span class="number">403</span>);
            <span class="keyword">die</span>(<span class="string">'Invalid CSRF token'</span>);
        }
    }
}</code>
    </div>

    <h3>admin-confirm-secure.php</h3>
    <div class="code-block">
        <code><span class="keyword">&lt;?php</span>
<span class="function">session_start</span>();
<span class="keyword">require_once</span> <span class="string">'config.php'</span>;
<span class="keyword">require_once</span> <span class="string">'auth.php'</span>;

<span class="comment">// Initialize auth with database connection</span>
Auth::<span class="function">init</span>(<span class="variable">$conn</span>);

<span class="comment">// ✅ SECURE: Check admin authorization</span>
Auth::<span class="function">requireAdmin</span>();

<span class="comment">// ✅ SECURE: Validate CSRF token</span>
Auth::<span class="function">validateCSRFToken</span>(<span class="variable">$_POST</span>[<span class="string">'csrf_token'</span>] ?? <span class="string">''</span>);

<span class="comment">// Validate and sanitize input</span>
<span class="variable">$username</span> = <span class="function">filter_input</span>(INPUT_POST, <span class="string">'username'</span>, FILTER_SANITIZE_STRING);
<span class="variable">$role</span> = <span class="variable">$_POST</span>[<span class="string">'role'</span>] ?? <span class="string">''</span>;

<span class="keyword">if</span> (!<span class="function">in_array</span>(<span class="variable">$role</span>, [<span class="string">'admin'</span>, <span class="string">'user'</span>])) {
    <span class="keyword">die</span>(<span class="string">'Invalid role'</span>);
}

<span class="comment">// Execute update with prepared statement</span>
<span class="variable">$stmt</span> = <span class="variable">$conn</span>-><span class="function">prepare</span>(<span class="string">"UPDATE users SET role = ? WHERE username = ?"</span>);
<span class="variable">$stmt</span>-><span class="function">bind_param</span>(<span class="string">"ss"</span>, <span class="variable">$role</span>, <span class="variable">$username</span>);
<span class="variable">$stmt</span>-><span class="function">execute</span>();

<span class="comment">// Log the action</span>
<span class="function">error_log</span>(<span class="function">sprintf</span>(
    <span class="string">"Role change: %s changed %s to %s"</span>,
    <span class="variable">$_SESSION</span>[<span class="string">'username'</span>],
    <span class="variable">$username</span>,
    <span class="variable">$role</span>
));

<span class="function">header</span>(<span class="string">"Location: admin.php?updated="</span> . <span class="function">urlencode</span>(<span class="variable">$username</span>));
<span class="keyword">exit</span>;</code>
    </div>
</div>

<div class="content-section">
    <h2>Python Flask - Secure Implementation</h2>
    
    <div class="code-block">
        <code><span class="keyword">from</span> functools <span class="keyword">import</span> wraps
<span class="keyword">from</span> flask <span class="keyword">import</span> Flask, session, redirect, request, abort

app = Flask(__name__)

<span class="comment"># Decorator for admin-only routes</span>
<span class="keyword">def</span> <span class="function">admin_required</span>(f):
    @wraps(f)
    <span class="keyword">def</span> <span class="function">decorated_function</span>(*args, **kwargs):
        <span class="keyword">if</span> <span class="string">'user_id'</span> <span class="keyword">not in</span> session:
            <span class="keyword">return</span> redirect(<span class="string">'/login'</span>)
        
        <span class="comment"># Always verify from database</span>
        user = User.query.get(session[<span class="string">'user_id'</span>])
        <span class="keyword">if not</span> user <span class="keyword">or</span> user.role != <span class="string">'admin'</span>:
            abort(<span class="number">403</span>)
        
        <span class="keyword">return</span> f(*args, **kwargs)
    <span class="keyword">return</span> decorated_function

<span class="comment"># Step 1: Select user</span>
@app.route(<span class="string">'/admin/step1'</span>)
@admin_required  <span class="comment"># ✅ Protected</span>
<span class="keyword">def</span> <span class="function">admin_step1</span>():
    users = User.query.all()
    <span class="keyword">return</span> render_template(<span class="string">'admin_step1.html'</span>, users=users)

<span class="comment"># Step 2: Choose role</span>
@app.route(<span class="string">'/admin/step2'</span>)
@admin_required  <span class="comment"># ✅ Protected</span>
<span class="keyword">def</span> <span class="function">admin_step2</span>():
    username = request.args.get(<span class="string">'username'</span>)
    <span class="keyword">return</span> render_template(<span class="string">'admin_step2.html'</span>, username=username)

<span class="comment"># Step 3: Confirm change</span>
@app.route(<span class="string">'/admin/confirm'</span>, methods=[<span class="string">'POST'</span>])
@admin_required  <span class="comment"># ✅ Protected - Same decorator as other steps!</span>
<span class="keyword">def</span> <span class="function">admin_confirm</span>():
    username = request.form.get(<span class="string">'username'</span>)
    new_role = request.form.get(<span class="string">'role'</span>)
    
    <span class="keyword">if</span> new_role <span class="keyword">not in</span> [<span class="string">'admin'</span>, <span class="string">'user'</span>]:
        abort(<span class="number">400</span>)
    
    user = User.query.filter_by(username=username).first()
    <span class="keyword">if</span> user:
        user.role = new_role
        db.session.commit()
    
    <span class="keyword">return</span> redirect(<span class="string">'/admin?updated='</span> + username)</code>
    </div>
</div>

<div class="content-section">
    <h2>Node.js Express - Secure Implementation</h2>
    
    <div class="code-block">
        <code><span class="keyword">const</span> express = require(<span class="string">'express'</span>);
<span class="keyword">const</span> router = express.Router();

<span class="comment">// Middleware: Verify admin authorization</span>
<span class="keyword">const</span> <span class="function">requireAdmin</span> = <span class="keyword">async</span> (req, res, next) => {
    <span class="keyword">if</span> (!req.session.userId) {
        <span class="keyword">return</span> res.redirect(<span class="string">'/login'</span>);
    }
    
    <span class="comment">// Always check database for current role</span>
    <span class="keyword">const</span> user = <span class="keyword">await</span> User.findById(req.session.userId);
    
    <span class="keyword">if</span> (!user || user.role !== <span class="string">'admin'</span>) {
        <span class="keyword">return</span> res.status(<span class="number">403</span>).json({ 
            error: <span class="string">'Access denied: Admin privileges required'</span> 
        });
    }
    
    req.currentUser = user;
    next();
};

<span class="comment">// Apply middleware to ALL admin routes</span>
router.use(<span class="string">'/admin'</span>, requireAdmin);

<span class="comment">// Step 1: List users</span>
router.get(<span class="string">'/admin/users'</span>, <span class="keyword">async</span> (req, res) => {
    <span class="keyword">const</span> users = <span class="keyword">await</span> User.find({});
    res.render(<span class="string">'admin-users'</span>, { users });
});

<span class="comment">// Step 2: Select role</span>
router.get(<span class="string">'/admin/role/:username'</span>, <span class="keyword">async</span> (req, res) => {
    <span class="keyword">const</span> user = <span class="keyword">await</span> User.findOne({ username: req.params.username });
    res.render(<span class="string">'admin-role'</span>, { user });
});

<span class="comment">// Step 3: Confirm change - Protected by router.use() above!</span>
router.post(<span class="string">'/admin/confirm'</span>, <span class="keyword">async</span> (req, res) => {
    <span class="keyword">const</span> { username, role } = req.body;
    
    <span class="keyword">if</span> (![<span class="string">'admin'</span>, <span class="string">'user'</span>].includes(role)) {
        <span class="keyword">return</span> res.status(<span class="number">400</span>).json({ error: <span class="string">'Invalid role'</span> });
    }
    
    <span class="keyword">await</span> User.updateOne({ username }, { role });
    
    <span class="comment">// Log the action</span>
    console.log(<span class="string">`Admin ${req.currentUser.username} changed ${username} to ${role}`</span>);
    
    res.redirect(<span class="string">`/admin/users?updated=${username}`</span>);
});

module.exports = router;</code>
    </div>
</div>

<div class="content-section">
    <h2>Java Spring Boot - Secure Implementation</h2>
    
    <div class="code-block">
        <code><span class="keyword">@RestController</span>
<span class="keyword">@RequestMapping</span>(<span class="string">"/admin"</span>)
<span class="keyword">@PreAuthorize</span>(<span class="string">"hasRole('ADMIN')"</span>)  <span class="comment">// ✅ Applied to entire controller</span>
<span class="keyword">public class</span> <span class="function">AdminController</span> {

    <span class="keyword">@Autowired</span>
    <span class="keyword">private</span> UserService userService;

    <span class="comment">// Step 1: Get all users</span>
    <span class="keyword">@GetMapping</span>(<span class="string">"/users"</span>)
    <span class="keyword">public</span> List&lt;UserDTO&gt; <span class="function">getUsers</span>() {
        <span class="keyword">return</span> userService.getAllUsers();
    }

    <span class="comment">// Step 2: Get role options</span>
    <span class="keyword">@GetMapping</span>(<span class="string">"/roles/{username}"</span>)
    <span class="keyword">public</span> RoleOptionsDTO <span class="function">getRoleOptions</span>(<span class="keyword">@PathVariable</span> String username) {
        User user = userService.findByUsername(username);
        <span class="keyword">return new</span> RoleOptionsDTO(user);
    }

    <span class="comment">// Step 3: Confirm role change - Still protected by class-level annotation!</span>
    <span class="keyword">@PostMapping</span>(<span class="string">"/confirm"</span>)
    <span class="keyword">public</span> ResponseEntity&lt;?&gt; <span class="function">confirmRoleChange</span>(
            <span class="keyword">@Valid @RequestBody</span> RoleChangeRequest request,
            <span class="keyword">@AuthenticationPrincipal</span> UserDetails currentUser) {
        
        <span class="comment">// Additional validation</span>
        <span class="keyword">if</span> (!Role.isValid(request.getRole())) {
            <span class="keyword">return</span> ResponseEntity.badRequest().body(<span class="string">"Invalid role"</span>);
        }

        userService.updateRole(request.getUsername(), request.getRole());
        
        <span class="comment">// Audit log</span>
        auditService.log(<span class="string">"ROLE_CHANGE"</span>, currentUser.getUsername(), 
            request.getUsername(), request.getRole());

        <span class="keyword">return</span> ResponseEntity.ok().build();
    }
}</code>
    </div>
</div>

<div class="content-section">
    <h2>Key Patterns Summary</h2>
    
    <table style="width: 100%; border-collapse: collapse; margin: 1rem 0;">
        <thead>
            <tr style="background: rgba(255, 68, 68, 0.2);">
                <th style="padding: 1rem; text-align: left; border: 1px solid #333; color: #ff6666;">Pattern</th>
                <th style="padding: 1rem; text-align: left; border: 1px solid #333; color: #ff6666;">Implementation</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;">Centralized Auth</td>
                <td style="padding: 1rem; border: 1px solid #333;">Single auth function/middleware for all checks</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;">Database Verification</td>
                <td style="padding: 1rem; border: 1px solid #333;">Always query database, never trust session alone</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;">Route Grouping</td>
                <td style="padding: 1rem; border: 1px solid #333;">Apply middleware to route groups, not individual routes</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;">Annotations/Decorators</td>
                <td style="padding: 1rem; border: 1px solid #333;">Class-level security annotations cover all methods</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;">Audit Logging</td>
                <td style="padding: 1rem; border: 1px solid #333;">Log all privileged actions for accountability</td>
            </tr>
        </tbody>
    </table>
</div>

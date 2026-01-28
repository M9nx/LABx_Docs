<?php
/**
 * LABx_Docs - Centralized Database Configuration
 * Manages database credentials via session/cookies
 * No hardcoded credentials - user provides via UI
 */

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get database credentials from session
 * Returns default empty values if not set
 */
function getDbCredentials() {
    return [
        'host' => $_SESSION['db_host'] ?? 'localhost',
        'user' => $_SESSION['db_user'] ?? '',
        'pass' => $_SESSION['db_pass'] ?? '',
        'configured' => isset($_SESSION['db_user']) && !empty($_SESSION['db_user'])
    ];
}

/**
 * Save database credentials to session
 */
function saveDbCredentials($host, $user, $pass) {
    $_SESSION['db_host'] = $host;
    $_SESSION['db_user'] = $user;
    $_SESSION['db_pass'] = $pass;
    $_SESSION['db_configured'] = true;
    $_SESSION['db_last_test'] = time();
}

/**
 * Test database connection
 * Returns array with status and message
 */
function testDbConnection($host = null, $user = null, $pass = null) {
    // Use provided credentials or get from session
    if ($host === null) {
        $creds = getDbCredentials();
        $host = $creds['host'];
        $user = $creds['user'];
        $pass = $creds['pass'];
    }
    
    if (empty($user)) {
        return [
            'success' => false,
            'message' => 'No credentials configured',
            'configured' => false
        ];
    }
    
    // Suppress warnings and try to connect
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        return [
            'success' => false,
            'message' => 'Connection failed: ' . $conn->connect_error,
            'configured' => true
        ];
    }
    
    // Get MySQL version for display
    $version = $conn->server_info;
    $conn->close();
    
    // Update last successful test time
    $_SESSION['db_last_test'] = time();
    $_SESSION['db_connected'] = true;
    
    return [
        'success' => true,
        'message' => 'Connected to MySQL ' . $version,
        'configured' => true
    ];
}

/**
 * Get connection status for display
 */
function getConnectionStatus() {
    $creds = getDbCredentials();
    
    if (!$creds['configured']) {
        return [
            'status' => 'unconfigured',
            'color' => 'gray',
            'message' => 'Not configured'
        ];
    }
    
    $test = testDbConnection();
    
    if ($test['success']) {
        return [
            'status' => 'connected',
            'color' => 'green',
            'message' => $test['message']
        ];
    } else {
        return [
            'status' => 'error',
            'color' => 'red',
            'message' => $test['message']
        ];
    }
}

/**
 * Create a database connection using stored credentials
 */
function createDbConnection($dbname = null) {
    $creds = getDbCredentials();
    
    if (!$creds['configured']) {
        return null;
    }
    
    mysqli_report(MYSQLI_REPORT_OFF);
    
    if ($dbname) {
        $conn = @new mysqli($creds['host'], $creds['user'], $creds['pass'], $dbname);
    } else {
        $conn = @new mysqli($creds['host'], $creds['user'], $creds['pass']);
    }
    
    if ($conn->connect_error) {
        return null;
    }
    
    return $conn;
}

/**
 * Create PDO connection using stored credentials
 */
function createPdoConnection($dbname = null) {
    $creds = getDbCredentials();
    
    if (!$creds['configured']) {
        return null;
    }
    
    try {
        $dsn = "mysql:host={$creds['host']}";
        if ($dbname) {
            $dsn .= ";dbname=$dbname";
        }
        $pdo = new PDO($dsn, $creds['user'], $creds['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        return null;
    }
}

// Handle AJAX requests for connection testing
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'test':
            // Test with provided credentials
            $host = $_POST['host'] ?? 'localhost';
            $user = $_POST['user'] ?? '';
            $pass = $_POST['pass'] ?? '';
            
            $result = testDbConnection($host, $user, $pass);
            
            // If successful, save credentials
            if ($result['success']) {
                saveDbCredentials($host, $user, $pass);
            }
            
            echo json_encode($result);
            break;
            
        case 'status':
            // Get current connection status
            echo json_encode(getConnectionStatus());
            break;
            
        case 'credentials':
            // Get current credentials (masked password)
            $creds = getDbCredentials();
            echo json_encode([
                'host' => $creds['host'],
                'user' => $creds['user'],
                'configured' => $creds['configured']
            ]);
            break;
            
        case 'clear':
            // Clear stored credentials
            unset($_SESSION['db_host']);
            unset($_SESSION['db_user']);
            unset($_SESSION['db_pass']);
            unset($_SESSION['db_configured']);
            unset($_SESSION['db_connected']);
            echo json_encode(['success' => true, 'message' => 'Credentials cleared']);
            break;
            
        default:
            echo json_encode(['error' => 'Unknown action']);
    }
    exit;
}
?>

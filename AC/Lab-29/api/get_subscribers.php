<?php
// Lab 29: LinkedPro Newsletter Platform - VULNERABLE API ENDPOINT
// GET /api/get_subscribers.php
// 
// VULNERABILITY: Missing authorization check - any authenticated user can
// view subscriber list of ANY newsletter by providing the seriesUrn
//
// This mimics the LinkedIn vulnerability where the API endpoint:
// GET /voyager/api/voyagerPublishingDashSeriesSubscribers
// did not verify that the requesting user owned the newsletter

require_once '../config.php';

// Set JSON content type
header('Content-Type: application/json');
header('X-API-Version: 2.0');

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'code' => 'UNAUTHORIZED',
        'message' => 'Authentication required'
    ]);
    exit();
}

$user = getCurrentUser($conn);

// Get parameters - mimicking LinkedIn's API structure
$seriesUrn = $_GET['seriesUrn'] ?? '';
$decorationId = $_GET['decorationId'] ?? 'com.linkedin.voyager.dash.deco.publishing.SeriesSubscriberMiniProfile-2';
$count = min(intval($_GET['count'] ?? 10), 50); // Max 50 per request
$start = intval($_GET['start'] ?? 0);
$q = $_GET['q'] ?? 'contentSeries';

// Extract newsletter URN from the full URN
// Format: urn:li:fsd_contentSeries:7890123456 -> fsd_contentSeries:7890123456
$newsletter_urn = str_replace('urn:li:', '', $seriesUrn);

if (empty($newsletter_urn)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'code' => 'INVALID_PARAM',
        'message' => 'seriesUrn parameter is required'
    ]);
    exit();
}

// Find the newsletter by URN
$stmt = $conn->prepare("SELECT * FROM newsletters WHERE newsletter_urn = ?");
$stmt->bind_param("s", $newsletter_urn);
$stmt->execute();
$newsletter = $stmt->get_result()->fetch_assoc();

if (!$newsletter) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'code' => 'NOT_FOUND',
        'message' => 'Newsletter not found'
    ]);
    exit();
}

// ============================================================
// VULNERABILITY: NO AUTHORIZATION CHECK!
// The code should verify: $newsletter['creator_id'] == $user['user_id']
// But this check is MISSING, allowing any user to view any newsletter's subscribers
// ============================================================

// Log the API access (for detection)
$isOwner = ($newsletter['creator_id'] == $user['user_id']);
logActivity($conn, $user['user_id'], 'api_get_subscribers', 'newsletter', $newsletter['id'], 
    json_encode([
        'seriesUrn' => $seriesUrn,
        'is_owner' => $isOwner,
        'potential_idor' => !$isOwner
    ])
);

// Get subscribers with pagination
$stmt = $conn->prepare("
    SELECT u.user_id, u.username, u.email, u.full_name, u.headline, u.location, 
           u.connections, u.profile_picture, s.subscribed_at, s.notification_enabled
    FROM subscribers s
    JOIN users u ON s.user_id = u.user_id
    WHERE s.newsletter_id = ?
    ORDER BY s.subscribed_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $newsletter['id'], $count, $start);
$stmt->execute();
$subscribers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get total count
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM subscribers WHERE newsletter_id = ?");
$stmt->bind_param("i", $newsletter['id']);
$stmt->execute();
$totalCount = $stmt->get_result()->fetch_assoc()['total'];

// Format response to mimic LinkedIn's API structure
$elements = [];
foreach ($subscribers as $sub) {
    $elements[] = [
        'miniProfile' => [
            'entityUrn' => 'urn:li:fsd_profile:' . $sub['user_id'],
            'publicIdentifier' => $sub['username'],
            'firstName' => explode(' ', $sub['full_name'])[0],
            'lastName' => explode(' ', $sub['full_name'])[1] ?? '',
            'headline' => $sub['headline'],
            'location' => $sub['location'],
            'backgroundImage' => null,
            'picture' => [
                'artifacts' => [
                    ['fileIdentifyingUrlPathSegment' => $sub['profile_picture']]
                ]
            ]
        ],
        'emailAddress' => $sub['email'], // Sensitive PII exposed!
        'connectionCount' => $sub['connections'],
        'subscriptionInfo' => [
            'subscribedAt' => strtotime($sub['subscribed_at']) * 1000,
            'notificationEnabled' => $sub['notification_enabled']
        ]
    ];
}

// Return JSON response
$response = [
    'status' => 'success',
    'metadata' => [
        'decorationId' => $decorationId,
        'seriesUrn' => $seriesUrn,
        'q' => $q,
        'paging' => [
            'count' => $count,
            'start' => $start,
            'total' => $totalCount,
            'links' => []
        ]
    ],
    'data' => [
        'contentSeries' => [
            'urn' => 'urn:li:' . $newsletter_urn,
            'title' => $newsletter['title'],
            'subscriberCount' => $totalCount
        ],
        'elements' => $elements
    ],
    // Include flag if IDOR was exploited
    '_debug' => !$isOwner ? [
        'vulnerability_exploited' => true,
        'flag' => 'FLAG{linkedin_idor_newsletter_subscribers_exposed_2024}',
        'message' => 'You accessed subscribers of a newsletter you do not own!'
    ] : null
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>

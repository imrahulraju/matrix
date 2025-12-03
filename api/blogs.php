<?php
require_once '../includes/auth.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$dataFile = __DIR__ . '/../assets/data/blogs.json';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($dataFile)) {
        $content = file_get_contents($dataFile);
        if (empty($content)) {
            echo json_encode([]);
        } else {
            // Validate JSON before outputting
            $decoded = json_decode($content);
            if (json_last_error() === JSON_ERROR_NONE) {
                echo $content;
            } else {
                // If file contains invalid JSON, return empty array and log error (if possible)
                echo json_encode([]); 
            }
        }
    } else {
        // File doesn't exist, return empty array
        echo json_encode([]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check Auth
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    // Since this endpoint handles Create, Update, and Delete (via full overwrite),
    // we ideally need to know WHAT action is being performed to check specific permissions.
    // However, the current JS implementation sends the ENTIRE array every time.
    // So for now, we require 'update' permission as a baseline to save anything,
    // or 'create'/'delete' if we could distinguish.
    // A safer approach with the current architecture is to require at least one write permission.
    
    if (!hasPermission('create') && !hasPermission('update') && !hasPermission('delete')) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if ($data !== null) {
        // Ensure directory exists
        $dir = dirname($dataFile);
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0755, true)) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create data directory. Check parent directory permissions.', 'path' => $dir]);
                exit;
            }
        }

        // Check writability
        if (!is_writable($dir)) {
             http_response_code(500);
             echo json_encode(['error' => 'Data directory is not writable. Please set permissions (chmod 775) for: ' . $dir]);
             exit;
        }

        if (@file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT))) {
            echo json_encode(['success' => true, 'message' => 'Data saved successfully']);
        } else {
            http_response_code(500);
            $err = error_get_last();
            echo json_encode(['error' => 'Failed to save data to file.', 'details' => $err['message'] ?? 'Unknown error']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>

<?php
function getBlogs() {
    $dataFile = __DIR__ . '/../assets/data/blogs.json';
    $realPath = realpath($dataFile);

    if ($realPath && file_exists($realPath)) {
        $json = file_get_contents($realPath);
        if ($json === false) {
            // Log error if file cannot be read
            error_log("Error reading blog data file: " . $realPath);
            return [];
        }
        
        $blogs = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Log JSON error
            error_log("JSON decode error: " . json_last_error_msg());
            return [];
        }

        if (is_array($blogs)) {
            // Sort by date descending
            usort($blogs, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            return $blogs;
        }
    } else {
        // Log if file doesn't exist
        error_log("Blog data file not found: " . $dataFile);
    }
    return [];
}

function getBlogById($id) {
    $blogs = getBlogs();
    foreach ($blogs as $blog) {
        if ($blog['id'] == $id) {
            return $blog;
        }
    }
    return null;
}

function formatDate($dateString) {
    return date('F j, Y', strtotime($dateString));
}
?>

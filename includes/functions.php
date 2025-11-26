<?php
function getBlogs() {
    $dataFile = __DIR__ . '/../assets/data/blogs.json';
    if (file_exists($dataFile)) {
        $json = file_get_contents($dataFile);
        $blogs = json_decode($json, true);
        if (is_array($blogs)) {
            // Sort by date descending
            usort($blogs, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            return $blogs;
        }
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

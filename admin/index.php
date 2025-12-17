<?php
require_once '../includes/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tour Matrix</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
    <style>
        body {
            background-color: #f8f9fc;
            padding-top: 80px;
        }

        .mx-admin-header {
            background: white;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            z-index: 100;
        }

        .mx-admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 16px;
        }

        .mx-admin-table {
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border-collapse: collapse;
        }

        .mx-admin-table th,
        .mx-admin-table td {
            padding: 16px 24px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .mx-admin-table th {
            background: #f8f9fc;
            font-weight: 600;
            color: #1e2339;
        }

        .mx-admin-table tr:last-child td {
            border-bottom: none;
        }

        .mx-action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            margin-right: 5px;
        }

        .mx-btn-edit {
            background: #e3f2fd;
            color: #1976d2;
        }

        .mx-btn-delete {
            background: #ffebee;
            color: #c62828;
        }

        /* Modal Styles for Add/Edit */
        .mx-admin-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .mx-admin-modal.active {
            display: flex;
        }

        .mx-admin-modal-content {
            background: white;
            width: 600px;
            max-width: 90%;
            border-radius: 12px;
            padding: 30px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .mx-form-group {
            margin-bottom: 20px;
        }

        .mx-form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .mx-form-input,
        .mx-form-textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: inherit;
        }

        .mx-form-textarea {
            height: 150px;
            resize: vertical;
        }

        .mx-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .mx-close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
        }

        /* Quill Editor Height */
        #editor-container {
            height: 200px;
        }
    </style>
</head>

<body>

    <header class="mx-admin-header">
        <div class="mx-header__logo">
            <img src="../assets/images/logo.png" alt="Tour Matrix" style="height: 40px;">
        </div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <?php if (hasPermission('manage_users')): ?>
                <a href="users.php" class="mx-btn mx-btn--small" style="background: #1976d2; text-decoration: none;">Manage Users</a>
            <?php endif; ?>
            <a href="logout.php" class="mx-btn mx-btn--small" style="background: #333; text-decoration: none;">Logout</a>
        </div>
    </header>

    <div class="mx-admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 class="mx-title" style="margin-bottom: 0; font-size: 28px; text-align: left;">Manage Blog Posts</h1>
            <button onclick="openModal()" class="mx-btn">Add New Post</button>
        </div>

        <table class="mx-admin-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="blog-table-body">
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px;">
                        <div class="mx-loader" style="display: inline-block; width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                        <p style="margin-top: 10px; color: #666;">Loading posts...</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <style>
            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        </style>
    </div>

    <!-- Add/Edit Modal -->
    <div id="post-modal" class="mx-admin-modal">
        <div class="mx-admin-modal-content">
            <div class="mx-modal-header">
                <h2 id="modal-title">Add New Post</h2>
                <button onclick="closeModal()" class="mx-close-btn">&times;</button>
            </div>
            <form id="post-form">
                <input type="hidden" id="post-id">
                <div class="mx-form-group">
                    <label class="mx-form-label">Title</label>
                    <input type="text" id="post-title" class="mx-form-input" required>
                </div>
                <div class="mx-form-group">
                    <label class="mx-form-label">Category</label>
                    <select id="post-category" class="mx-form-input" required>
                        <option value="Technology">Technology</option>
                        <option value="Business Growth">Business Growth</option>
                        <option value="Operations">Operations</option>
                        <option value="Customer Experience">Customer Experience</option>
                        <option value="Trends">Trends</option>
                        <option value="Management">Management</option>
                    </select>
                </div>
                <div class="mx-form-group">
                    <label class="mx-form-label">Featured Image</label>
                    
                    <!-- Toggle Options -->
                    <div style="margin-bottom: 10px;">
                        <label style="margin-right: 15px; cursor: pointer;">
                            <input type="radio" name="image-source" value="url" checked onchange="toggleImageSource('url')"> Image URL
                        </label>
                        <label style="cursor: pointer;">
                            <input type="radio" name="image-source" value="upload" onchange="toggleImageSource('upload')"> Upload Image
                        </label>
                    </div>

                    <!-- URL Input Container -->
                    <div id="image-url-container">
                        <input type="text" id="post-image-url" class="mx-form-input" placeholder="Enter image URL (e.g. assets/images/solution--1.jpg)">
                    </div>

                    <!-- Upload Input Container -->
                    <div id="image-upload-container" style="display: none;">
                        <input type="file" id="post-image-file" class="mx-form-input" accept="image/*">
                    </div>

                    <!-- Hidden Field for Final Value -->
                    <input type="hidden" id="post-image">

                    <!-- Preview -->
                    <div id="image-preview-container" style="margin-top: 10px; display: none;">
                        <img id="preview-img" src="" alt="Preview" style="max-width: 100%; height: auto; max-height: 200px; border-radius: 6px; border: 1px solid #ddd;">
                    </div>
                </div>
                <div class="mx-form-group">
                    <label class="mx-form-label">Excerpt</label>
                    <textarea id="post-excerpt" class="mx-form-textarea" style="height: 80px;" required></textarea>
                </div>
                <div class="mx-form-group">
                    <label class="mx-form-label">Content</label>
                    <div id="editor-container"></div>
                </div>
                <button type="submit" class="mx-btn" style="width: 100%;">Save Post</button>
            </form>
        </div>
    </div>

    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        window.API_BASE_URL = '../api/';
    </script>
    <script src="../assets/js/blog-manager.js"></script>
    <script>
        // Pass permissions and user to JS
        const PERMISSIONS = {
            create: <?php echo hasPermission('create') ? 'true' : 'false'; ?>,
            update: <?php echo hasPermission('update') ? 'true' : 'false'; ?>,
            delete: <?php echo hasPermission('delete') ? 'true' : 'false'; ?>
        };
        const CURRENT_USER = '<?php echo htmlspecialchars($_SESSION['admin_username']); ?>';

        // Initialize Quill
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['link', 'image'],
                    ['clean']
                ]
            }
        });

        // Initialize and Render
        (async function() {
            await blogManager.init(); // Ensure data is fetched
            renderTable();
            if (!PERMISSIONS.create) {
                const addBtn = document.querySelector('button[onclick="openModal()"]');
                if (addBtn) addBtn.style.display = 'none';
            }
        })();

        function renderTable() {
            const tbody = document.getElementById('blog-table-body');
            const blogs = blogManager.getAll();

            if (blogs.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #666;">
                            No blog posts found. Click "Add New Post" to create one.
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = blogs.map(blog => `
                <tr>
                    <td>${blog.title}</td>
                    <td><span style="color: #666; font-size: 14px;">${blog.author || 'Unknown'}</span></td>
                    <td><span style="background: #f0f3ff; color: #831bee; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">${blog.category}</span></td>
                    <td>${blog.date}</td>
                    <td>
                        ${PERMISSIONS.update ? `<button onclick="editPost(${blog.id})" class="mx-action-btn mx-btn-edit">Edit</button>` : ''}
                        ${PERMISSIONS.delete ? `<button onclick="deletePost(${blog.id})" class="mx-action-btn mx-btn-delete">Delete</button>` : ''}
                    </td>
                </tr>
            `).join('');
        }

        // Modal Logic
        const modal = document.getElementById('post-modal');
        const form = document.getElementById('post-form');
        const modalTitle = document.getElementById('modal-title');

        // Toggle Logic
        function toggleImageSource(source) {
            const urlContainer = document.getElementById('image-url-container');
            const uploadContainer = document.getElementById('image-upload-container');
            
            if (source === 'url') {
                urlContainer.style.display = 'block';
                uploadContainer.style.display = 'none';
            } else {
                urlContainer.style.display = 'none';
                uploadContainer.style.display = 'block';
            }
        }

        // URL Input Logic
        document.getElementById('post-image-url').addEventListener('input', function(e) {
            const url = e.target.value;
            document.getElementById('post-image').value = url;
            
            if (url) {
                document.getElementById('preview-img').src = url; // Note: Relative paths might not preview correctly in admin unless handled
                document.getElementById('image-preview-container').style.display = 'block';
            } else {
                document.getElementById('image-preview-container').style.display = 'none';
            }
        });

        // Image Upload Logic
        document.getElementById('post-image-file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('image', file);

            // Show loading state if desired, or just wait
            const previewContainer = document.getElementById('image-preview-container');
            const previewImg = document.getElementById('preview-img');
            
            fetch('../api/upload_image.php', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                const text = await response.text();
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Server response:', text);
                    throw new Error('Server error: ' + text.substring(0, 200)); 
                }
            })
            .then(data => {
                if (data.success) {
                    const url = data.url;
                    document.getElementById('post-image').value = url;
                    // Also update the URL input so if they switch back it's there
                    document.getElementById('post-image-url').value = url;
                    
                    previewImg.src = '../' + url;
                    previewContainer.style.display = 'block';
                } else {
                    alert('Upload failed: ' + (data.error || 'Unknown error'));
                    e.target.value = ''; // Reset input
                }
            })
            .catch(err => {
                console.error(err);
                alert('Upload failed: ' + err.message);
            });
        });

        function openModal(isEdit = false) {
            modal.classList.add('active');
            if (!isEdit) {
                form.reset();
                document.getElementById('post-id').value = '';
                
                // Default State
                document.getElementById('post-image').value = 'assets/images/solution--1.jpg';
                document.getElementById('post-image-url').value = 'assets/images/solution--1.jpg';
                document.getElementById('image-preview-container').style.display = 'none';
                
                // Reset Toggle
                document.querySelector('input[name="image-source"][value="url"]').checked = true;
                toggleImageSource('url');

                quill.root.innerHTML = ''; // Clear editor
                modalTitle.innerText = 'Add New Post';
            }
        }

        function closeModal() {
            modal.classList.remove('active');
        }

        function editPost(id) {
            const blog = blogManager.getById(id);
            if (blog) {
                document.getElementById('post-id').value = blog.id;
                document.getElementById('post-title').value = blog.title;
                document.getElementById('post-category').value = blog.category;
                document.getElementById('post-image').value = blog.image;
                document.getElementById('post-excerpt').value = blog.excerpt;

                // Set URL input value
                document.getElementById('post-image-url').value = blog.image;

                // Show Preview
                if (blog.image) {
                    // Handle relative path for preview
                    const src = blog.image.startsWith('http') ? blog.image : '../' + blog.image;
                    document.getElementById('preview-img').src = src;
                    document.getElementById('image-preview-container').style.display = 'block';
                } else {
                    document.getElementById('image-preview-container').style.display = 'none';
                }

                // Reset Toggle to URL by default when editing
                document.querySelector('input[name="image-source"][value="url"]').checked = true;
                toggleImageSource('url');

                // Set Quill content
                quill.root.innerHTML = blog.content;

                modalTitle.innerText = 'Edit Post';
                openModal(true);
            }
        }

        function deletePost(id) {
            if (confirm('Are you sure you want to delete this post?')) {
                blogManager.delete(id);
                renderTable();
            }
        }

        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const id = document.getElementById('post-id').value;
            const postData = {
                title: document.getElementById('post-title').value,
                category: document.getElementById('post-category').value,
                image: document.getElementById('post-image').value,
                excerpt: document.getElementById('post-excerpt').value,
                content: quill.root.innerHTML // Get content from Quill
            };

            if (id) {
                blogManager.update(id, postData);
            } else {
                postData.author = CURRENT_USER; // Add author for new posts
                blogManager.create(postData);
            }

            closeModal();
            renderTable();
        });

        // Close modal on outside click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    </script>
</body>

</html>

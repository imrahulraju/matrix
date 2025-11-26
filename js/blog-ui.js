// Blog UI Logic for Public Pages

document.addEventListener('DOMContentLoaded', async () => {
    // Ensure data is loaded
    await blogManager.init();

    // Check which page we are on
    if (document.querySelector('.mx-blog-grid')) {
        renderBlogList();
    } else if (document.querySelector('.mx-blog-detail-section')) {
        renderBlogDetail();
    }
});

let currentPage = 1;
let itemsPerPage = 6;

function renderBlogList() {
    const grid = document.querySelector('.mx-blog-grid');
    const paginationContainer = document.querySelector('.mx-pagination');
    const itemsPerPageSelect = document.getElementById('itemsPerPage');

    // Update state from dropdown if available
    if (itemsPerPageSelect) {
        itemsPerPage = parseInt(itemsPerPageSelect.value);

        // Remove existing listener to avoid duplicates (though simple assignment overwrites usually)
        itemsPerPageSelect.onchange = (e) => {
            itemsPerPage = parseInt(e.target.value);
            currentPage = 1; // Reset to first page
            renderBlogList();
        };
    }

    const blogs = blogManager.getAll();
    const totalItems = blogs.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    // Ensure current page is valid
    if (currentPage > totalPages) currentPage = totalPages || 1;
    if (currentPage < 1) currentPage = 1;

    // Slice data
    const startIndex = (currentPage - 1) * itemsPerPage;
    const paginatedBlogs = blogs.slice(startIndex, startIndex + itemsPerPage);

    if (blogs.length === 0) {
        grid.innerHTML = '<p style="text-align:center; grid-column: 1/-1;">No blog posts found.</p>';
        paginationContainer.innerHTML = '';
        return;
    }

    // Render Grid
    grid.innerHTML = paginatedBlogs.map(blog => `
        <div class="mx-blog-card">
            <div class="mx-blog-card__image">
                <img src="${blog.image}" alt="${blog.title}" onerror="this.src='assets/images/solution--1.jpg'">
            </div>
            <div class="mx-blog-card__content">
                <div class="mx-blog-card__meta">
                    <span class="mx-blog-card__date">${formatDate(blog.date)}</span>
                    <span class="mx-blog-card__category">${blog.category}</span>
                </div>
                <h3 class="mx-blog-card__title">${blog.title}</h3>
                <p class="mx-blog-card__excerpt">${blog.excerpt}</p>
                <a href="blog-detail.html?id=${blog.id}" class="mx-blog-card__link">Read More 
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>
    `).join('');

    // Render Pagination
    renderPagination(paginationContainer, totalPages);
}

function renderPagination(container, totalPages) {
    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '';

    // Prev Button
    html += `
        <a href="#" class="mx-pagination__item mx-pagination__item--prev ${currentPage === 1 ? 'disabled' : ''}" data-page="${currentPage - 1}">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="pointer-events: none;">
                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    `;

    // Page Numbers
    // Simple logic: Show all if <= 7, otherwise show start, end, and current neighborhood
    // For simplicity in this iteration, let's just show all or a simple subset

    for (let i = 1; i <= totalPages; i++) {
        if (
            i === 1 ||
            i === totalPages ||
            (i >= currentPage - 1 && i <= currentPage + 1)
        ) {
            html += `<a href="#" class="mx-pagination__item ${i === currentPage ? 'mx-pagination__item--active' : ''}" data-page="${i}">${i}</a>`;
        } else if (
            (i === currentPage - 2 && i > 1) ||
            (i === currentPage + 2 && i < totalPages)
        ) {
            html += `<span class="mx-pagination__dots">...</span>`;
        }
    }

    // Next Button
    html += `
        <a href="#" class="mx-pagination__item mx-pagination__item--next ${currentPage === totalPages ? 'disabled' : ''}" data-page="${currentPage + 1}">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="pointer-events: none;">
                <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    `;

    container.innerHTML = html;

    // Add Event Listeners
    container.querySelectorAll('.mx-pagination__item').forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            if (item.classList.contains('disabled')) return;

            const newPage = parseInt(item.dataset.page);
            if (newPage && newPage !== currentPage) {
                currentPage = newPage;
                renderBlogList();
                // Scroll to top of list
                document.querySelector('.mx-blog-list-section').scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

function renderBlogDetail() {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');

    if (!id) {
        window.location.href = 'blog-list.html';
        return;
    }

    const blog = blogManager.getById(id);

    if (!blog) {
        document.querySelector('.mx-blog-detail__main').innerHTML = '<h2>Post not found</h2><a href="blog-list.html" class="mx-btn">Back to Blog</a>';
        return;
    }

    // Update Page Title
    document.title = `${blog.title} - Metrix Blog`;

    // Update Banner
    document.querySelector('.mx-blog-detail__title').textContent = blog.title;
    document.querySelector('.mx-blog-detail__date').textContent = formatDate(blog.date);
    document.querySelector('.mx-blog-detail__category').textContent = blog.category;

    // Update Content
    const mainContent = document.querySelector('.mx-blog-detail__main');

    // Image
    const imgContainer = mainContent.querySelector('.mx-blog-detail__image img');
    if (imgContainer) {
        imgContainer.src = blog.image;
        imgContainer.alt = blog.title;
    }

    // Text Content
    const textContainer = mainContent.querySelector('.mx-blog-detail__text');
    // We assume content is HTML safe or from trusted admin
    textContainer.innerHTML = blog.content;

    // Re-append share section if needed, or just leave it static
    // (The static HTML has a share section at the bottom, we just replaced the text above it)
    // Actually, replacing innerHTML wipes the share buttons if they were inside .mx-blog-detail__text
    // In my HTML structure, .mx-blog-detail__share is a sibling of .mx-blog-detail__text, so it's safe.

    renderRecentPosts(id);
    renderCategories();
    setupShareLinks(blog.title);
}

function setupShareLinks(title) {
    const currentUrl = encodeURIComponent(window.location.href);
    const encodedTitle = encodeURIComponent(title);

    const shareLinks = {
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${currentUrl}`,
        twitter: `https://twitter.com/intent/tweet?url=${currentUrl}&text=${encodedTitle}`,
        linkedin: `https://www.linkedin.com/shareArticle?mini=true&url=${currentUrl}&title=${encodedTitle}`,
        whatsapp: `https://api.whatsapp.com/send?text=${encodedTitle}%20${currentUrl}`
    };

    const container = document.querySelector('.mx-blog-detail__share-links');
    if (container) {
        container.innerHTML = `
            <a href="${shareLinks.facebook}" target="_blank" class="mx-blog-detail__share-link">Facebook</a>
            <a href="${shareLinks.twitter}" target="_blank" class="mx-blog-detail__share-link">Twitter</a>
            <a href="${shareLinks.linkedin}" target="_blank" class="mx-blog-detail__share-link">LinkedIn</a>
            <a href="${shareLinks.whatsapp}" target="_blank" class="mx-blog-detail__share-link">WhatsApp</a>
        `;
    }
}

function renderRecentPosts(currentId) {
    const list = document.querySelector('.mx-blog-sidebar-list');
    if (!list) return;

    const blogs = blogManager.getAll()
        .filter(b => b.id != currentId)
        .slice(0, 3);

    if (blogs.length === 0) {
        list.innerHTML = '<li>No recent posts.</li>';
        return;
    }

    list.innerHTML = blogs.map(blog => `
        <li>
            <a href="blog-detail.html?id=${blog.id}">
                <span class="mx-blog-sidebar-list__title">${blog.title}</span>
                <span class="mx-blog-sidebar-list__date">${formatDate(blog.date)}</span>
            </a>
        </li>
    `).join('');
}

function renderCategories() {
    const list = document.querySelector('.mx-blog-sidebar-list--categories');
    if (!list) return;

    const blogs = blogManager.getAll();
    const categories = {};

    // Count categories
    blogs.forEach(blog => {
        if (categories[blog.category]) {
            categories[blog.category]++;
        } else {
            categories[blog.category] = 1;
        }
    });

    if (Object.keys(categories).length === 0) {
        list.innerHTML = '<li>No categories found.</li>';
        return;
    }

    list.innerHTML = Object.entries(categories).map(([category, count]) => `
        <li>
            <a href="#">${category} <span>(${count})</span></a>
        </li>
    `).join('');
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

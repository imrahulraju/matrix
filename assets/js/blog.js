document.addEventListener('DOMContentLoaded', () => {
    const blogContainer = document.getElementById('blog-container');

    if (!blogContainer) return;

    // Priority 1: Try to fetch from JSON (Best for Server)
    fetch('assets/data/blogs.json')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(fetchedData => {
            renderBlogs(fetchedData, blogContainer);
        })
        .catch(error => {
            console.warn('Fetch failed, falling back to local data:', error);

            // Priority 2: Fallback to global variable (Best for Local/offline)
            const fallbackData = (typeof blogData !== 'undefined') ? blogData : [];

            if (fallbackData.length > 0) {
                renderBlogs(fallbackData, blogContainer);
            } else {
                blogContainer.innerHTML = '<p>No blogs found.</p>';
            }
        });
});

function renderBlogs(data, container) {
    if (data.length === 0) {
        container.innerHTML = '<p>No blogs found.</p>';
        return;
    }

    // Sort by latest date descending
    const sortedBlogs = data.sort((a, b) => new Date(b.date) - new Date(a.date));
    const latestBlogs = sortedBlogs.slice(0, 3); // Display up to 3 blogs

    container.innerHTML = ''; // Clear container

    latestBlogs.forEach(blog => {
        const blogCard = document.createElement('div');
        blogCard.classList.add('mx-card');

        // Create the HTML structure for the blog card
        blogCard.innerHTML = `
            <div class="mx-card__image">
                <img src="${blog.image}" alt="${blog.title}" style="width: 100%; border-radius: 12px; height: 200px; object-fit: cover;">
            </div>
            <div class="mx-card__content" style="padding-top: 16px;">
                <span class="mx-card__category" style="font-size: 12px; color: #831bee; font-weight: 600; text-transform: uppercase;">${blog.category}</span>
                <div class="mx-card__title" style="margin-top: 8px;">
                    <h3 style="font-size: 18px; margin-bottom: 8px;">${blog.title}</h3>
                </div>
                <div class="mx-card__description">
                    <p style="font-size: 14px; color: #51597E; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">${blog.excerpt}</p>
                </div>
                <a href="blog-detail.php?id=${blog.id}" class="mx-btn mx-btn--small" style="margin-top: 16px; display: inline-block;">Read More</a>
            </div>
        `;

        container.appendChild(blogCard);
    });
}

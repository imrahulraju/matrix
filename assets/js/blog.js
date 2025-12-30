document.addEventListener('DOMContentLoaded', () => {
    const blogContainer = document.getElementById('blog-container');

    if (!blogContainer) return;

    // Use global blogData variable if access to local file fails
    // In a real environment with a server, we could try fetch first, but for local file support,
    // using the JS variable is safer and more reliable.
    const data = (typeof blogData !== 'undefined') ? blogData : [];

    if (!data || data.length === 0) {
        // Only try fetch if the global variable isn't there, as a last resort
        fetch('assets/data/blogs.json')
            .then(response => response.json())
            .then(fetchedData => {
                renderBlogs(fetchedData, blogContainer);
            })
            .catch(error => {
                console.error('Error fetching blog data:', error);
                // Don't show error to user immediately if we can avoid it, 
                // but since we are here, both methods failed.
                blogContainer.innerHTML = '<p>No blogs found.</p>';
            });
    } else {
        renderBlogs(data, blogContainer);
    }
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
                <a href="blog-details.php?id=${blog.id}" class="mx-btn mx-btn--small" style="margin-top: 16px; display: inline-block;">Read More</a>
            </div>
        `;

        container.appendChild(blogCard);
    });
}

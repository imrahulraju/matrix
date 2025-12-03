/**
 * Blog Manager - Handles Data Persistence (LocalStorage + JSON) and Auth
 */

const BLOG_STORAGE_KEY = 'mx_blog_data';
const AUTH_STORAGE_KEY = 'mx_admin_session';
const API_BASE_URL = window.API_BASE_URL || 'api/';
const DATA_URL = API_BASE_URL + 'blogs.json';

class BlogManager {
    constructor() {
        this.blogs = [];
        this.init();
    }

    async init() {
        try {
            const response = await fetch(`${API_BASE_URL}blogs.php`);
            if (response.ok) {
                const text = await response.text();
                try {
                    this.blogs = JSON.parse(text);
                    // Sync with local storage for fallback/faster read on other pages if needed
                    localStorage.setItem(BLOG_STORAGE_KEY, JSON.stringify(this.blogs));
                } catch (e) {
                    console.error('Failed to parse blogs JSON:', e);
                    console.error('Server response:', text);
                    // Fallback to local storage if server response is invalid
                    const storedData = localStorage.getItem(BLOG_STORAGE_KEY);
                    if (storedData) this.blogs = JSON.parse(storedData);
                }
            } else {
                console.error('Failed to fetch blogs from server. Status:', response.status);
                // Fallback to local storage if server fails
                const storedData = localStorage.getItem(BLOG_STORAGE_KEY);
                if (storedData) this.blogs = JSON.parse(storedData);
            }
        } catch (error) {
            console.error('Error loading blog data:', error);
            // Fallback
            const storedData = localStorage.getItem(BLOG_STORAGE_KEY);
            if (storedData) this.blogs = JSON.parse(storedData);
        }
    }

    async saveToStorage() {
        // Save to LocalStorage (for immediate UI updates/backup)
        localStorage.setItem(BLOG_STORAGE_KEY, JSON.stringify(this.blogs));

        // Save to Server (Persistent)
        try {
            const response = await fetch(`${API_BASE_URL}blogs.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(this.blogs)
            });

            if (!response.ok) {
                const text = await response.text();
                console.error('Server Error Response:', text);
                throw new Error(`Server responded with ${response.status}: ${text}`);
            }

            const result = await response.json();
            if (!result.success) {
                throw new Error(result.error || 'Unknown error');
            }

            console.log('Data saved to server successfully');
        } catch (error) {
            console.error('Error saving to server:', error);
            alert('Failed to save changes to server. \nError: ' + error.message);
        }
    }

    getAll() {
        return this.blogs.sort((a, b) => new Date(b.date) - new Date(a.date));
    }

    getById(id) {
        return this.blogs.find(blog => blog.id == id);
    }

    async create(blog) {
        const newBlog = {
            id: Date.now(), // Simple ID generation
            ...blog,
            date: new Date().toISOString().split('T')[0] // Current date YYYY-MM-DD
        };
        this.blogs.unshift(newBlog);
        await this.saveToStorage();
        return newBlog;
    }

    async update(id, updatedFields) {
        const index = this.blogs.findIndex(blog => blog.id == id);
        if (index !== -1) {
            this.blogs[index] = { ...this.blogs[index], ...updatedFields };
            await this.saveToStorage();
            return this.blogs[index];
        }
        return null;
    }

    async delete(id) {
        this.blogs = this.blogs.filter(blog => blog.id != id);
        await this.saveToStorage();
    }

    // Auth Methods
    login(username, password) {
        // Hardcoded credentials for demo purposes
        if (username === 'admin' && password === 'admin123') {
            const session = {
                user: username,
                token: Date.now().toString()
            };
            localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify(session));
            return true;
        }
        return false;
    }

    logout() {
        localStorage.removeItem(AUTH_STORAGE_KEY);
        window.location.href = 'login.html';
    }

    isAuthenticated() {
        return !!localStorage.getItem(AUTH_STORAGE_KEY);
    }

    checkAuth() {
        if (!this.isAuthenticated()) {
            window.location.href = 'login.html';
        }
    }
}

// Export instance
const blogManager = new BlogManager();

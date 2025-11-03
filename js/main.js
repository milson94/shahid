document.addEventListener('DOMContentLoaded', () => {

    // --- Theme Toggle Functionality ---
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;

    // Function to apply the saved theme
    const applyTheme = () => {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            body.classList.add(savedTheme);
        }
    };

    // Function to handle the toggle click
    const toggleTheme = () => {
        body.classList.toggle('dark-mode');
        // Save the current theme preference to localStorage
        let theme = 'light';
        if (body.classList.contains('dark-mode')) {
            theme = 'dark-mode';
        }
        localStorage.setItem('theme', theme);
    };

    // Add event listener and apply initial theme
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
    applyTheme();


    // --- Lightbox Functionality ---
    // This targets all links within any element that has the class .image-gallery-grid
    const galleries = document.querySelectorAll('.image-gallery-grid a');
    
    if (galleries.length > 0) {
        new SimpleLightbox('.image-gallery-grid a', {
            // Optional: You can add options here to customize the lightbox
            // captionDelay: 250,
            // captionsData: 'alt',
        });
    }

    console.log("CDTUR Website Initialized: Theme and Lightbox are active.");
});
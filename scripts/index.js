// ScrollReveal configuration
document.addEventListener('DOMContentLoaded', function () {
    ScrollReveal().reveal('.reveal', {
        duration: 1000,
        distance: '50px',
        easing: 'ease-in-out',
        origin: 'bottom',
        reset: true,
    });

    // Show/hide the scroll-to-top button
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');
    window.onscroll = function () {
        if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
            scrollToTopBtn.style.display = 'block';
        } else {
            scrollToTopBtn.style.display = 'none';
        }
    };
});

// Scroll to the top function
function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Feedback form submission handler
document.getElementById('feedbackForm').addEventListener('submit', function (event) {
    event.preventDefault();
    showModal();
});

// Show the modal
function showModal() {
    const modal = document.getElementById('thankYouModal');
    modal.style.display = 'block';
}

// Close the modal
function closeModal() {
    const modal = document.getElementById('thankYouModal');
    modal.style.display = 'none';
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('thankYouModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
};

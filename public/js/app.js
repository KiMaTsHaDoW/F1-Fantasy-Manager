// F1 Fantasy - JavaScript principal


document.addEventListener('DOMContentLoaded', function () {
    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity .5s';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 500);
        }, 4000);
    });

    // Highlight current nav link
    const currentPath = window.location.pathname;
    document.querySelectorAll('.main-nav a').forEach(function (link) {
        if (link.getAttribute('href') === currentPath) {
            link.style.color = '#fff';
        }
    });
});

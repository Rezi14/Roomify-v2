document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("sidebarToggle");
    const sidebar = document.querySelector(".sidebar");
    const body = document.body;

    if (toggleBtn && sidebar) {
        // Toggle sidebar ketika tombol diklik
        toggleBtn.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            sidebar.classList.toggle("show");
            body.classList.toggle("sidebar-open");
        });

        // Tutup sidebar jika klik di luar sidebar (area gelap)
        document.addEventListener("click", function (e) {
            if (
                body.classList.contains("sidebar-open") &&
                !sidebar.contains(e.target) &&
                !toggleBtn.contains(e.target)
            ) {
                sidebar.classList.remove("show");
                body.classList.remove("sidebar-open");
            }
        });

        // Tutup sidebar ketika link navigasi diklik (pada mobile)
        const navLinks = sidebar.querySelectorAll(".nav-link");
        navLinks.forEach((link) => {
            link.addEventListener("click", function () {
                if (window.innerWidth <= 991) {
                    sidebar.classList.remove("show");
                    body.classList.remove("sidebar-open");
                }
            });
        });

        // Handle resize window
        let resizeTimer;
        window.addEventListener("resize", function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                if (window.innerWidth > 991) {
                    sidebar.classList.remove("show");
                    body.classList.remove("sidebar-open");
                }
            }, 250);
        });
    }

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach((alert) => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

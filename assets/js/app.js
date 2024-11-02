document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.getElementById("sidebar");
  const content = document.getElementById("content");
  const overlay = document.querySelector(".overlay");
  const sidebarCollapse = document.getElementById("sidebarCollapse");
  const sidebarCollapseDesktop = document.getElementById(
    "sidebarCollapseDesktop"
  );

  // Mobile toggle
  sidebarCollapse.addEventListener("click", function () {
    sidebar.classList.toggle("active");
    content.classList.toggle("active");
    overlay.classList.toggle("active");
  });

  // Desktop toggle
  sidebarCollapseDesktop.addEventListener("click", function () {
    sidebar.classList.toggle("collapsed");
    content.classList.toggle("collapsed");
  });

  // Close sidebar when clicking overlay
  overlay.addEventListener("click", function () {
    sidebar.classList.remove("active");
    content.classList.remove("active");
    overlay.classList.remove("active");
  });

  // Handle window resize
  window.addEventListener("resize", function () {
    if (window.innerWidth > 768) {
      overlay.classList.remove("active");
      sidebar.classList.remove("active");
      content.classList.remove("active");
    }
  });
});

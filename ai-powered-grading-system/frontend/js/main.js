// ================================
// General Scripts
// ================================

// Cache DOM elements for navigation
const navLinks = document.querySelectorAll(".sidebar-nav a");
const navItems = document.querySelectorAll(".sidebar-nav ul li");
const sections = document.querySelectorAll(".tab-section");

// --------------------
// Handle click active state for sidebar (General Navigation)
// --------------------
navLinks.forEach((link) => {
  link.addEventListener("click", (e) => {
    e.preventDefault();
    const targetId = link.getAttribute("href").substring(1);

    // Update active class in nav
    navItems.forEach((li) => li.classList.remove("active"));
    link.parentElement.classList.add("active");

    // Show corresponding section
    sections.forEach((section) => {
      section.classList.toggle("hidden", section.id !== targetId);
      section.classList.toggle("active", section.id === targetId);
    });

    // Smooth scroll to section
    const targetSection = document.getElementById(targetId);
    if (targetSection) {
      targetSection.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  });
});

// --------------------
// Highlight nav item on scroll (General)
// --------------------
window.addEventListener("scroll", () => {
  const scrollY = window.pageYOffset;
  let currentSectionId = "";

  sections.forEach((section, index) => {
    const sectionTop = section.offsetTop - 100;
    const sectionHeight = section.offsetHeight;

    if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
      currentSectionId = section.id;
    }

    // Ensure last section is active if near bottom
    if (
      index === sections.length - 1 &&
      window.innerHeight + scrollY >= document.body.offsetHeight - 50
    ) {
      currentSectionId = section.id;
    }
  });

  navItems.forEach((li) => {
    li.classList.toggle(
      "active",
      li.querySelector("a").getAttribute("href") === `#${currentSectionId}`
    );
  });
});

// General utility functions
function getRoleName(role_id) {
  const roles = {
    1: "Super Admin",
    2: "MIS Admin",
    3: "Professor",
    4: "Student",
  };
  return roles[role_id] || "Unknown";
}

<!-- Same smooth scroll + active state script as super-admin -->
    <script>
      const navLinks = document.querySelectorAll(".sidebar-nav ul li a");
      const sections = document.querySelectorAll(".tab-section");
      const navItems = document.querySelectorAll(".sidebar-nav ul li");

      navLinks.forEach((link) => {
        link.addEventListener("click", () => {
          navLinks.forEach((l) => l.parentElement.classList.remove("active"));
          link.parentElement.classList.add("active");
        });
      });

      window.addEventListener("scroll", () => {
        let current = "";
        const scrollY = window.pageYOffset;

        sections.forEach((section, index) => {
          const sectionTop = section.offsetTop - 100;
          const sectionHeight = section.offsetHeight;

          if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
            current = section.getAttribute("id");
          }

          if (
            index === sections.length - 1 &&
            window.innerHeight + scrollY >= document.body.offsetHeight - 50
          ) {
            current = section.getAttribute("id");
          }
        });

        navItems.forEach((li) => {
          li.classList.remove("active");
          if (li.querySelector("a").getAttribute("href") === "#" + current) {
            li.classList.add("active");
          }
        });
      });
    </script>
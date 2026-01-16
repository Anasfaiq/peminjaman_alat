// User Dropdown Menu
document.addEventListener("DOMContentLoaded", function () {
  const userDropdownButton = document.getElementById("userBtn");
  const userDropdownMenu = document.querySelector(".user-dropdown-menu");

  if (userDropdownButton && userDropdownMenu) {
    userDropdownButton.addEventListener("click", (e) => {
      e.stopPropagation();
      userDropdownButton.classList.toggle("active");
      userDropdownMenu.classList.toggle("hidden");
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", (e) => {
      if (!e.target.closest(".user-dropdown-wrapper")) {
        userDropdownButton.classList.remove("active");
        userDropdownMenu.classList.add("hidden");
      }
    });
  }
});

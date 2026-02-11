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


const addBtn = document.getElementById("addBtn");
const modal = document.getElementById("modal");
const backdrop = document.getElementById("modalBackdrop");

const form = document.getElementById("userForm");
const idUser = document.getElementById("id_user");
const nama = document.getElementById("nama");
const username = document.getElementById("username");
const role = document.getElementById("role");
const password = document.getElementById("password");
const passwordHint = document.getElementById("passwordHint");
const modalTitle = document.getElementById("modalTitle");

// OPEN ADD
addBtn.addEventListener("click", () => {
  form.reset();
  form.action = "proses/proses-add-user.php";
  idUser.value = "";
  passwordHint.classList.add("hidden");
  modalTitle.innerText = "Add User";
  modal.classList.remove("hidden");
  backdrop.classList.remove("hidden");
});

// CLOSE MODAL
document.querySelectorAll("#closeBtn").forEach(btn => {
  btn.addEventListener("click", () => {
    modal.classList.add("hidden");
    backdrop.classList.add("hidden");
  });
});

// OPEN EDIT
document.querySelectorAll(".edit-button").forEach(btn => {
  btn.addEventListener("click", () => {
    modalTitle.innerText = "Edit User";
    form.action = "proses/proses-update-user.php";

    idUser.value = btn.dataset.id;
    nama.value = btn.dataset.nama;
    username.value = btn.dataset.username;
    role.value = btn.dataset.role;

    password.value = "";
    passwordHint.classList.remove("hidden");

    modal.classList.remove("hidden");
    backdrop.classList.remove("hidden");
  });
});

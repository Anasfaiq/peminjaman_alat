// User Dropdown Menu
document.addEventListener("DOMContentLoaded", function () {
  const userDropdownButton = document.getElementById("userBtn");
  const userDropdownMenu = document.querySelector(".user-dropdown-menu");

  if (userDropdownButton && userDropdownMenu) {
    // Toggle dropdown ketika button diklik
    userDropdownButton.addEventListener("click", function (e) {
      e.stopPropagation();
      const isHidden = userDropdownMenu.classList.contains("hidden");
      if (isHidden) {
        userDropdownMenu.classList.remove("hidden");
      } else {
        userDropdownMenu.classList.add("hidden");
      }
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function (e) {
      const isClickInsideWrapper = e.target.closest(".user-dropdown-wrapper");
      if (
        !isClickInsideWrapper &&
        !userDropdownMenu.classList.contains("hidden")
      ) {
        userDropdownMenu.classList.add("hidden");
      }
    });
  } else {
    if (!userDropdownButton) console.warn("userBtn element not found");
    if (!userDropdownMenu) console.warn("user-dropdown-menu element not found");
  }
});

// form handling for add/edit (generic for all pages)

const addBtn = document.getElementById("addBtn");
const modal = document.getElementById("modal");
const backdrop = document.getElementById("modalBackdrop");
const form = document.getElementById("userForm");
const modalTitle = document.getElementById("modalTitle");

// Get all form fields (will be null if they don't exist on this page)
const formFields = {
  // Regular underscored field names
  id_user: document.getElementById("id_user"),
  id_alat: document.getElementById("id_alat"),
  id_kategori: document.getElementById("id_kategori"),
  id_peminjaman: document.getElementById("id_peminjaman"),
  id_pengembalian: document.getElementById("id_pengembalian"),
  nama: document.getElementById("nama"),
  nama_alat: document.getElementById("nama_alat"),
  nama_kategori: document.getElementById("nama_kategori"),
  username: document.getElementById("username"),
  role: document.getElementById("role"),
  password: document.getElementById("password"),
  passwordHint: document.getElementById("passwordHint"),
  keterangan: document.getElementById("keterangan"),
  kondisi: document.getElementById("kondisi"),
  harga_barang: document.getElementById("harga_barang"),
  harga_sewa: document.getElementById("harga_sewa"),
  stok: document.getElementById("stok"),
  tanggal_pinjam: document.getElementById("tanggal_pinjam"),
  tanggal_kembali_rencana: document.getElementById("tanggal_kembali_rencana"),
  tanggal_kembali: document.getElementById("tanggal_kembali"),
  status: document.getElementById("status"),
  kondisi_kembali: document.getElementById("kondisi_kembali"),
  denda: document.getElementById("denda"),

  // Select field versions (with _select suffix)
  id_user_select: document.getElementById("id_user_select"),
  id_alat_select: document.getElementById("id_alat_select"),
  id_peminjaman_select: document.getElementById("id_peminjaman_select"),
  jumlah: document.getElementById("jumlah"),
};

// OPEN ADD BUTTON
if (addBtn) {
  addBtn.addEventListener("click", () => {
    if (!form || !modal || !backdrop) {
      console.warn("Form, modal, or backdrop not found");
      return;
    }

    form.reset();

    // Set action path based on page (infer from URL or form attributes)
    const pageName = window.location.pathname
      .split("/")
      .pop()
      .replace(".php", "");

    // Map page name to process name (handle special cases like data_peminjaman -> peminjaman)
    const processNameMap = {
      user: "user",
      alat: "alat",
      kategori: "kategori",
      data_peminjaman: "peminjaman",
      pengembalian: "pengembalian",
    };

    const processName = processNameMap[pageName] || pageName;
    let actionPath = "proses/proses-add-" + processName + ".php";

    form.action = actionPath;

    // Clear all ID fields
    Object.keys(formFields).forEach((key) => {
      if (key.startsWith("id_") && formFields[key]) {
        formFields[key].value = "";
      }
    });

    // Hide password hint if exists
    if (formFields.passwordHint) {
      formFields.passwordHint.classList.add("hidden");
    }

    // Set modal title
    const titleMap = {
      user: "Add User",
      alat: "Add Alat",
      kategori: "Add Category",
      data_peminjaman: "Add Peminjaman",
      pengembalian: "Add Pengembalian",
    };
    modalTitle.innerText = titleMap[pageName] || "Add Data";

    modal.classList.remove("hidden");
    backdrop.classList.remove("hidden");
  });
} else {
  console.warn("Add button not found");
}

// CLOSE MODAL
const closeBtn = document.getElementById("closeBtn");
const closeBtnX = document.getElementById("closeBtnX");

if (closeBtn || closeBtnX) {
  if (closeBtn) {
    closeBtn.addEventListener("click", () => {
      if (modal) modal.classList.add("hidden");
      if (backdrop) backdrop.classList.add("hidden");
    });
  }
  if (closeBtnX) {
    closeBtnX.addEventListener("click", () => {
      if (modal) modal.classList.add("hidden");
      if (backdrop) backdrop.classList.add("hidden");
    });
  }
} else {
  console.warn("No close buttons found");
}

// CLOSE MODAL BY CLICKING BACKDROP
if (backdrop) {
  backdrop.addEventListener("click", () => {
    if (modal) modal.classList.add("hidden");
    backdrop.classList.add("hidden");
  });
}

// OPEN EDIT BUTTON - Generic Handler
const editButtons = document.querySelectorAll(".edit-button");
if (editButtons.length > 0) {
  editButtons.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();

      if (!form || !modal || !backdrop) {
        console.warn("Form, modal, or backdrop not found");
        return;
      }

      // Get the current page name
      const pageName = window.location.pathname
        .split("/")
        .pop()
        .replace(".php", "");

      // Map page name to process name (handle special cases like data_peminjaman -> peminjaman)
      const processNameMap = {
        user: "user",
        alat: "alat",
        kategori: "kategori",
        data_peminjaman: "peminjaman",
        pengembalian: "pengembalian",
      };

      const processName = processNameMap[pageName] || pageName;
      let updateAction = "proses/proses-update-" + processName + ".php";
      form.action = updateAction;

      // Determine ID field name based on page
      const idFieldNames = {
        user: "id_user",
        alat: "id_alat",
        kategori: "id_kategori",
        data_peminjaman: "id_peminjaman",
        pengembalian: "id_pengembalian",
      };

      const idFieldName = idFieldNames[pageName];

      // Clear form and populate with data from button attributes
      form.reset();

      // Set ID
      if (idFieldName && formFields[idFieldName]) {
        formFields[idFieldName].value = btn.dataset.id;
      }

      // Set other fields based on available data attributes
      const dataAttributes = btn.dataset;

      // Explicit mapping from dataset keys (camelCase) to form field names (underscore)
      const fieldMapping = {
        idPeminjaman: "id_peminjaman_select",
        idUser: "id_user_select",
        idAlat: "id_alat_select",
        idKategori: "id_kategori",
        namaAlat: "nama_alat",
        namaKategori: "nama_kategori",
        hargaBarang: "harga_barang",
        hargaSewa: "harga_sewa",
        tanggalPinjam: "tanggal_pinjam",
        tanggalKembali: "tanggal_kembali",
        tanggalKembaliRencana: "tanggal_kembali_rencana",
        kondisi: "kondisi",
        kondisiKembali: "kondisi_kembali",
        stok: "stok",
        jumlah: "jumlah",
        denda: "denda",
        status: "status",
        nama: "nama",
      };

      Object.keys(dataAttributes).forEach((key) => {
        if (key !== "id") {
          // Get the mapped field name, or use the key as-is if no mapping exists
          const fieldName = fieldMapping[key] || key;
          const field = formFields[fieldName];

          if (field) {
            field.value = dataAttributes[key];
          }
        }
      });

      // Show password hint if exists (for user edit)
      if (formFields.passwordHint) {
        formFields.passwordHint.classList.remove("hidden");
      }

      // Set modal title
      const titleMap = {
        user: "Edit User",
        alat: "Edit Alat",
        kategori: "Edit Category",
        data_peminjaman: "Edit Peminjaman",
        pengembalian: "Edit Pengembalian",
      };
      modalTitle.innerText = titleMap[pageName] || "Edit Data";

      modal.classList.remove("hidden");
      backdrop.classList.remove("hidden");
    });
  });
} else {
  console.warn("No edit buttons found on this page");
}

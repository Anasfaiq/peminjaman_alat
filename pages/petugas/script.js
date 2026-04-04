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

// persetujuan peminjaman
let currentPeminjamanId = null;
let currentPeminjamanStatus = null;

function openDetailModal(id, nama, status) {
  currentPeminjamanId = id;
  currentPeminjamanStatus = status;
  document.getElementById("peminjamName").textContent = nama;

  // Fetch detail alat
  fetch("proses/proses-get-detail-peminjaman.php?id=" + id)
    .then((response) => response.json())
    .then((data) => {
      let html = "";
      if (data.length > 0) {
        data.forEach((item) => {
          html += `<div class="modal-field-item">
                <strong>${item.nama_alat}</strong> - Jumlah: ${item.jumlah}
              </div>`;
        });
      } else {
        html = '<p class="text-gray-500">Tidak ada detail alat</p>';
      }
      document.getElementById("detailAlatList").innerHTML = html;
    });

  // Tampilkan/sembunyikan button berdasarkan status
  const btnSetujui = document.getElementById("btnSetujui");
  const btnTolak = document.getElementById("btnTolak");
  const btnBatalkan = document.getElementById("btnBatalkan");
  const btnHapus = document.getElementById("btnHapus");

  // Reset semua button ke hidden
  btnSetujui.classList.add("hidden");
  btnTolak.classList.add("hidden");
  btnBatalkan.classList.add("hidden");
  btnHapus.classList.add("hidden");

  // Tampilkan button sesuai status
  if (status === "Menunggu") {
    btnSetujui.classList.remove("hidden");
    btnTolak.classList.remove("hidden");
  } else if (status === "Disetujui") {
    btnBatalkan.classList.remove("hidden");
  } else if (status === "Ditolak") {
    btnHapus.classList.remove("hidden");
  }

  document.getElementById("detailModal").classList.remove("hidden");
  document.getElementById("detailModalContent").classList.remove("hidden");
}

function closeDetailModal() {
  document.getElementById("detailModal").classList.add("hidden");
  document.getElementById("detailModalContent").classList.add("hidden");
}

function updateStatusPeminjaman(id, status) {
  let confirmMessage = "";

  if (status === "Disetujui") {
    confirmMessage = "Yakin ingin menyetujui peminjaman ini?";
  } else if (status === "Ditolak") {
    confirmMessage = "Yakin ingin menolak peminjaman ini?";
  } else if (status === "Menunggu") {
    confirmMessage = "Yakin ingin membatalkan persetujuan peminjaman ini?";
  }

  if (!confirm(confirmMessage)) return;

  fetch("proses/proses-update-status-peminjaman.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "id_peminjaman=" + id + "&status=" + status,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert(data.message);
        location.reload();
      } else {
        alert("Gagal: " + data.message);
      }
    });
}

// Close modal klik outside
document
  .getElementById("detailModal")
  .addEventListener("click", closeDetailModal);

function deletePeminjaman(id) {
  fetch("proses/proses-delete-peminjaman.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "id_peminjaman=" + id,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert(data.message);
        location.reload();
      } else {
        alert("Gagal: " + data.message);
      }
    });
}

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

    function openDetailModal(id, nama) {
      currentPeminjamanId = id;
      document.getElementById('peminjamName').textContent = nama;
      
      // Fetch detail alat
      fetch('proses/proses-get-detail-peminjaman.php?id=' + id)
        .then(response => response.json())
        .then(data => {
          let html = '';
          if(data.length > 0) {
            data.forEach(item => {
              html += `<div class="modal-field-item">
                <strong>${item.nama_alat}</strong> - Jumlah: ${item.jumlah}
              </div>`;
            });
          } else {
            html = '<p class="text-gray-500">Tidak ada detail alat</p>';
          }
          document.getElementById('detailAlatList').innerHTML = html;
        });
      
      document.getElementById('detailModal').classList.remove('hidden');
      document.getElementById('detailModalContent').classList.remove('hidden');
    }

    function closeDetailModal() {
      document.getElementById('detailModal').classList.add('hidden');
      document.getElementById('detailModalContent').classList.add('hidden');
    }

    function updateStatusPeminjaman(id, status) {
      if(!confirm(`Yakin ingin ${status === 'Disetujui' ? 'menyetujui' : 'menolak'} peminjaman ini?`)) return;
      
      fetch('proses/proses-update-status-peminjaman.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id_peminjaman=' + id + '&status=' + status
      })
      .then(response => response.json())
      .then(data => {
        if(data.success) {
          alert(data.message);
          location.reload();
        } else {
          alert('Gagal: ' + data.message);
        }
      });
    }

    // Close modal klik outside
    document.getElementById('detailModal').addEventListener('click', closeDetailModal);
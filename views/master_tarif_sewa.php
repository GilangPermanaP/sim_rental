<?php defined('SECURE_ACCESS') or die(header("Location: index.php?action=login")); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental AM - Master Tarif Sewa</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require_once 'views/layout/sidebar.php'; ?>

    <div class="main-content">
        <h1 class="page-title">MASTER TARIF SEWA</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="search-sort-bar">
                <form action="index.php" method="GET" class="search-box">
                    <input type="hidden" name="action" value="master_tarif_sewa">
                    <input type="text" name="search" class="form-control" placeholder="Cari kelas kendaraan..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>

                <div class="sort-box">
                    <button class="btn btn-primary" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Tambah Tarif Kelas</button>
                </div>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>
                                <a href="index.php?action=master_tarif_sewa&search=<?php echo urlencode($search); ?>&sort_by=jenis_kendaraan&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration:none; color:inherit;">
                                    Kelas / Jenis Kendaraan <i class="fa-solid fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a href="index.php?action=master_tarif_sewa&search=<?php echo urlencode($search); ?>&sort_by=tarif_per_hari&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration:none; color:inherit;">
                                    Tarif Per Hari <i class="fa-solid fa-sort"></i>
                                </a>
                            </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tariffs)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 30px;">Data tarif sewa tidak ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tariffs as $t): ?>
                                <tr>
                                    <td style="font-weight:600;"><?php echo htmlspecialchars($t['jenis_kendaraan']); ?></td>
                                    <td><span style="font-weight:700; color:#3b71ca;">Rp <?php echo number_format($t['tarif_per_hari'], 0, ',', '.'); ?></span></td>
                                    <td>
                                        <button class="btn btn-secondary" style="padding: 6px 12px; font-size:13px;" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($t)); ?>)">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <a href="index.php?action=tarif_sewa_delete&id=<?php echo $t['id_tarif']; ?>" class="btn btn-danger" style="padding: 6px 12px; font-size:13px;" onclick="return confirm('Hapus tarif kelas kendaraan ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal" id="addModal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Tambah Tarif Kelas Kendaraan</h4>
                <button class="close-btn" onclick="closeAddModal()">&times;</button>
            </div>
            <form action="index.php?action=tarif_sewa_create" method="POST">
                <div class="form-group">
                    <label>Jenis / Kelas Kendaraan</label>
                    <input type="text" name="jenis_kendaraan" class="form-control" placeholder="Contoh: City Car, Motor, SUV/Premium..." required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Tarif Per Hari (Rp)</label>
                    <input type="number" name="tarif_per_hari" min="0" step="1000" class="form-control" required autocomplete="off">
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Edit Tarif Kelas Kendaraan</h4>
                <button class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <form action="index.php?action=tarif_sewa_update" method="POST">
                <input type="hidden" name="id_tarif" id="edit_id">
                <div class="form-group">
                    <label>Jenis / Kelas Kendaraan</label>
                    <input type="text" name="jenis_kendaraan" id="edit_jenis" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Tarif Per Hari (Rp)</label>
                    <input type="number" name="tarif_per_hari" id="edit_tarif" min="0" step="1000" class="form-control" required autocomplete="off">
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.add('active');
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.remove('active');
        }
        function openEditModal(data) {
            document.getElementById('edit_id').value = data.id_tarif;
            document.getElementById('edit_jenis').value = data.jenis_kendaraan;
            document.getElementById('edit_tarif').value = Math.round(data.tarif_per_hari);
            document.getElementById('editModal').classList.add('active');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }
        window.onclick = function(event) {
            let addModal = document.getElementById('addModal');
            let editModal = document.getElementById('editModal');
            if (event.target === addModal) {
                closeAddModal();
            }
            if (event.target === editModal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>

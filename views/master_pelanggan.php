<?php defined('SECURE_ACCESS') or die(header("Location: index.php?action=login")); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental AM - Master Pelanggan</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require_once 'views/layout/sidebar.php'; ?>

    <div class="main-content">
        <h1 class="page-title">MASTER DATA PELANGGAN</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="search-sort-bar">
                <form action="index.php" method="GET" class="search-box">
                    <input type="hidden" name="action" value="master_pelanggan">
                    <input type="text" name="search" class="form-control" placeholder="Cari NIK, nama, atau telp..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>

                <div class="sort-box">
                    <button class="btn btn-primary" onclick="openAddModal()"><i class="fa-solid fa-user-plus"></i> Tambah Pelanggan</button>
                </div>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>
                                <a href="index.php?action=master_pelanggan&search=<?php echo urlencode($search); ?>&sort_by=nik&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration:none; color:inherit;">
                                    NIK <i class="fa-solid fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a href="index.php?action=master_pelanggan&search=<?php echo urlencode($search); ?>&sort_by=nama_pelanggan&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration:none; color:inherit;">
                                    Nama Pelanggan <i class="fa-solid fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a href="index.php?action=master_pelanggan&search=<?php echo urlencode($search); ?>&sort_by=no_telp&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration:none; color:inherit;">
                                    No Telp <i class="fa-solid fa-sort"></i>
                                </a>
                            </th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px;">Data pelanggan tidak ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customers as $c): ?>
                                <tr>
                                    <td><span style="font-family:monospace; background-color:#FFF8F8; padding:4px 8px; border-radius:6px; border:1px solid #F5C6CB; font-weight:700;"><?php echo htmlspecialchars($c['nik']); ?></span></td>
                                    <td style="font-weight:600;"><?php echo htmlspecialchars($c['nama_pelanggan']); ?></td>
                                    <td><?php echo htmlspecialchars($c['no_telp']); ?></td>
                                    <td><?php echo htmlspecialchars($c['alamat']); ?></td>
                                    <td>
                                        <button class="btn btn-secondary" style="padding: 6px 12px; font-size:13px;" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($c)); ?>)">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <a href="index.php?action=pelanggan_delete&id=<?php echo $c['id_pelanggan']; ?>" class="btn btn-danger" style="padding: 6px 12px; font-size:13px;" onclick="return confirm('Hapus pelanggan ini?')">
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
                <h4>Tambah Pelanggan Baru</h4>
                <button class="close-btn" onclick="closeAddModal()">&times;</button>
            </div>
            <form action="index.php?action=pelanggan_create" method="POST">
                <div class="form-group">
                    <label>NIK (Nomor Induk Kependudukan)</label>
                    <input type="text" name="nik" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="text" name="no_telp" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="3" required></textarea>
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
                <h4>Edit Data Pelanggan</h4>
                <button class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <form action="index.php?action=pelanggan_update" method="POST">
                <input type="hidden" name="id_pelanggan" id="edit_id">
                <div class="form-group">
                    <label>NIK (Nomor Induk Kependudukan)</label>
                    <input type="text" name="nik" id="edit_nik" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" id="edit_nama" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="text" name="no_telp" id="edit_no_telp" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <textarea name="alamat" id="edit_alamat" class="form-control" rows="3" required></textarea>
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
            document.getElementById('edit_id').value = data.id_pelanggan;
            document.getElementById('edit_nik').value = data.nik;
            document.getElementById('edit_nama').value = data.nama_pelanggan;
            document.getElementById('edit_no_telp').value = data.no_telp;
            document.getElementById('edit_alamat').value = data.alamat;
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

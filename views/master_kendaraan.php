<?php defined('SECURE_ACCESS') or die(header("Location: index.php?action=login")); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental AM - Master Kendaraan</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require_once 'views/layout/sidebar.php'; ?>

    <div class="main-content">
        <h1 class="page-title">MASTER KENDARAAN</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="search-sort-bar">
                <form action="index.php" method="GET" class="search-box">
                    <input type="hidden" name="action" value="master_kendaraan">
                    <input type="text" name="search" class="form-control" placeholder="Cari kendaraan atau plat..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>

                <div class="sort-box">
                    <button class="btn btn-primary" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Tambah Kendaraan</button>
                </div>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>
                                <a href="index.php?action=master_kendaraan&search=<?php echo urlencode($search); ?>&sort_by=nama_kendaraan&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration:none; color:inherit;">
                                    Nama Kendaraan <i class="fa-solid fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a href="index.php?action=master_kendaraan&search=<?php echo urlencode($search); ?>&sort_by=no_plat&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration:none; color:inherit;">
                                    No Plat <i class="fa-solid fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a href="index.php?action=master_kendaraan&search=<?php echo urlencode($search); ?>&sort_by=jenis_kendaraan&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration:none; color:inherit;">
                                    Kelas <i class="fa-solid fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a href="index.php?action=master_kendaraan&search=<?php echo urlencode($search); ?>&sort_by=status&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration:none; color:inherit;">
                                    Status <i class="fa-solid fa-sort"></i>
                                </a>
                            </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vehicles)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px;">Data kendaraan tidak ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($vehicles as $v): ?>
                                <?php 
                                    $img_src = 'assets/images/' . $v['foto_kendaraan'];
                                    if (empty($v['foto_kendaraan']) || !file_exists($img_src)) {
                                        $img_src = 'assets/images/default.jpg';
                                    }
                                ?>
                                <tr>
                                    <td><img src="<?php echo $img_src; ?>" alt="Avatar" class="avatar-circle"></td>
                                    <td style="font-weight:600;"><?php echo htmlspecialchars($v['nama_kendaraan']); ?></td>
                                    <td><span style="font-family:monospace; background-color:#FFF8F8; padding:4px 8px; border-radius:6px; border:1px solid #F5C6CB; font-weight:700;"><?php echo htmlspecialchars($v['no_plat']); ?></span></td>
                                    <td><?php echo htmlspecialchars($v['jenis_kendaraan']); ?></td>
                                    <td>
                                        <?php if ($v['status'] === 'Tersedia'): ?>
                                            <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Tersedia</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><i class="fa-solid fa-circle-minus"></i> Disewa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-secondary" style="padding: 6px 12px; font-size:13px;" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($v)); ?>)">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <a href="index.php?action=kendaraan_delete&id=<?php echo $v['id_kendaraan']; ?>" class="btn btn-danger" style="padding: 6px 12px; font-size:13px;" onclick="return confirm('Hapus kendaraan ini?')">
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

    <div class="modal" id="addModal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Tambah Kendaraan Baru</h4>
                <button class="close-btn" onclick="closeAddModal()">&times;</button>
            </div>
            <form action="index.php?action=kendaraan_create" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nama Kendaraan</label>
                    <input type="text" name="nama_kendaraan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nomor Plat</label>
                    <input type="text" name="no_plat" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Jenis Kelas Kendaraan</label>
                    <select name="jenis_kendaraan" class="form-control" required>
                        <option value="Motor">Motor</option>
                        <option value="City Car">City Car</option>
                        <option value="SUV/Premium">SUV/Premium</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="Tersedia">Tersedia</option>
                        <option value="Disewa">Disewa</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Foto Kendaraan</label>
                    <input type="file" name="foto_kendaraan" class="form-control">
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Edit Data Kendaraan</h4>
                <button class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <form action="index.php?action=kendaraan_update" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_kendaraan" id="edit_id">
                <div class="form-group">
                    <label>Nama Kendaraan</label>
                    <input type="text" name="nama_kendaraan" id="edit_nama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nomor Plat</label>
                    <input type="text" name="no_plat" id="edit_plat" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Jenis Kelas Kendaraan</label>
                    <select name="jenis_kendaraan" id="edit_jenis" class="form-control" required>
                        <option value="Motor">Motor</option>
                        <option value="City Car">City Car</option>
                        <option value="SUV/Premium">SUV/Premium</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="edit_status" class="form-control" required>
                        <option value="Tersedia">Tersedia</option>
                        <option value="Disewa">Disewa</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Ganti Foto Kendaraan</label>
                    <input type="file" name="foto_kendaraan" class="form-control">
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
            document.getElementById('edit_id').value = data.id_kendaraan;
            document.getElementById('edit_nama').value = data.nama_kendaraan;
            document.getElementById('edit_plat').value = data.no_plat;
            document.getElementById('edit_jenis').value = data.jenis_kendaraan;
            document.getElementById('edit_status').value = data.status;
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

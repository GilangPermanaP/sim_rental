<?php defined('SECURE_ACCESS') or die(header("Location: index.php?action=login")); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental AM - Master User</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require_once 'views/layout/sidebar.php'; ?>

    <div class="main-content">
        <h1 class="page-title">MASTER DATA USER</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="search-sort-bar">
                <form action="index.php" method="GET" class="search-box">
                    <input type="hidden" name="action" value="master_user">
                    <input type="text" name="search" class="form-control" placeholder="Cari username atau nama..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>

                <div class="sort-box">
                    <button class="btn btn-primary" onclick="openAddModal()"><i class="fa-solid fa-user-plus"></i> Tambah User</button>
                </div>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>
                                <a href="index.php?action=master_user&search=<?php echo urlencode($search); ?>&sort_by=username&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration:none; color:inherit;">
                                    Username <i class="fa-solid fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a href="index.php?action=master_user&search=<?php echo urlencode($search); ?>&sort_by=nama_lengkap&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration:none; color:inherit;">
                                    Nama Lengkap <i class="fa-solid fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a href="index.php?action=master_user&search=<?php echo urlencode($search); ?>&sort_by=role&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration:none; color:inherit;">
                                    Role <i class="fa-solid fa-sort"></i>
                                </a>
                            </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 30px;">Data user tidak ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td style="font-weight:600;"><?php echo htmlspecialchars($u['username']); ?></td>
                                    <td><?php echo htmlspecialchars($u['nama_lengkap']); ?></td>
                                    <td>
                                        <?php if ($u['role'] === 'Admin'): ?>
                                            <span class="badge badge-success"><i class="fa-solid fa-user-shield"></i> Admin</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><i class="fa-solid fa-user"></i> Operator</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-secondary" style="padding: 6px 12px; font-size:13px;" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($u)); ?>)">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <?php if ($u['id'] != $_SESSION['user']['id']): ?>
                                            <a href="index.php?action=user_delete&id=<?php echo $u['id']; ?>" class="btn btn-danger" style="padding: 6px 12px; font-size:13px;" onclick="return confirm('Hapus user ini?')">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-danger" style="padding: 6px 12px; font-size:13px; opacity:0.5; cursor:not-allowed;" disabled title="Tidak bisa menghapus diri sendiri">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
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
                <h4>Tambah User Baru</h4>
                <button class="close-btn" onclick="closeAddModal()">&times;</button>
            </div>
            <form action="index.php?action=user_create" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" class="form-control" required>
                        <option value="Admin">Admin</option>
                        <option value="Operator">Operator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required autocomplete="new-password">
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
                <h4>Edit Data User</h4>
                <button class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <form action="index.php?action=user_update" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" id="edit_username" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="edit_nama" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" id="edit_role" class="form-control" required>
                        <option value="Admin">Admin</option>
                        <option value="Operator">Operator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Password (Kosongkan jika tidak ingin diubah)</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password baru..." autocomplete="new-password">
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
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_username').value = data.username;
            document.getElementById('edit_nama').value = data.nama_lengkap;
            document.getElementById('edit_role').value = data.role;
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

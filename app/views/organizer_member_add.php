<?php 
$pageTitle = "Thêm Thành Viên Vào CLB";
require_once __DIR__ . '/includes/headers.php'; 
?>

<div style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #e2e8f0; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #1e3a5f; margin-top: 0; margin-bottom: 20px; font-size: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
         THÊM THÀNH VIÊN VÀO CLB
    </h2>

    <?php if (!empty($error)): ?>
        <div style="background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="index.php?page=club-member-add" method="POST">
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 6px; color: #334155;">Username / Tên Đăng Nhập (*)</label>
                <input type="text" name="username" placeholder="Nhập username của sinh viên..." required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box;">
                <small style="color: #64748b; margin-top: 4px; display: block;">Tài khoản phải tồn tại trong hệ thống trước khi thêm vào CLB.</small>
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 6px; color: #334155;">Vai Trò / Chức Vụ (*)</label>
                <input type="text" name="position" value="Thành viên" placeholder="VD: Trưởng ban Truyền thông, Thành viên..." required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box;">
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 6px; color: #334155;">Trạng Thái (*)</label>
                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; background: white;">
                    <option value="1">Hoạt động</option>
                    <option value="0">Tạm ngưng</option>
                </select>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px;">
            <a href="index.php?page=club-members" style="background: #e2e8f0; color: #475569; padding: 10px 20px; border-radius: 8px; font-weight: bold; text-decoration: none;">
                Hủy Bỏ
            </a>
            <button type="submit" style="background: #16a34a; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: bold; cursor: pointer;">
                Thêm Thành Viên
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
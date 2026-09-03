<?php 
$pageTitle = "Thêm Sự Kiện Mới";
require_once __DIR__ . '/includes/headers.php'; 
?>

<div style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #e2e8f0; max-width: 800px; margin: 0 auto;">
    <h2 style="color: #1e3a5f; margin-top: 0; margin-bottom: 20px; font-size: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
         THÊM SỰ KIỆN MỚI
    </h2>

    <?php if (!empty($error)): ?>
        <div style="background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="index.php?page=event-create" method="POST">
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 6px; color: #334155;">Mã CLB (*)</label>
                <input type="text" name="club_id" value="1" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box;">
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 6px; color: #334155;">Tên Sự Kiện (*)</label>
                <input type="text" name="event_name" placeholder="Nhập tên sự kiện..." required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box;">
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 6px; color: #334155;">Thời Gian Tổ Chức (*)</label>
                <input type="datetime-local" name="event_date" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box;">
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 6px; color: #334155;">Số Lượng Slot (*)</label>
                <input type="number" name="slots" placeholder="VD: 50" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box;">
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 6px; color: #334155;">Địa Điểm Tổ Chức (*)</label>
                <input type="text" name="location" placeholder="VD: Hội trường A" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box;">
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 6px; color: #334155;">Trạng Thái (*)</label>
                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; background: white;">
                    <option value="1">Mở đăng ký</option>
                    <option value="0">Đóng đăng ký</option>
                </select>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px;">
            <a href="index.php?page=event" style="background: #e2e8f0; color: #475569; padding: 10px 20px; border-radius: 8px; font-weight: bold; text-decoration: none;">
                Hủy Bỏ
            </a>
            <button type="submit" style="background: #16a34a; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: bold; cursor: pointer;">
                Lưu Sự Kiện
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
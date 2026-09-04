<?php 
$pageTitle = "Câu Lạc Bộ Của Tôi";
require_once __DIR__ . '/../includes/headers.php';

$createdAt = (!empty($club) && !empty($club['created_at'])) 
    ? date('d/m/Y', strtotime($club['created_at'])) 
    : 'Chưa cập nhật';
?>

<div style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #e2e8f0; max-width: 900px; margin: 0 auto;">
    <h2 style="color: #1e3a5f; margin-top: 0; margin-bottom: 20px; font-size: 22px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
         CÂU LẠC BỘ BẠN ĐANG QUẢN LÝ
    </h2>

    <?php if (empty($club)): ?>
        <div style="padding: 20px; background: #fff3cd; color: #856404; border-radius: 8px; font-weight: bold; text-align: center;">
             Tài khoản của bạn chưa được phân công quản lý câu lạc bộ nào trong hệ thống!
        </div>
    <?php else: ?>
        
        <!-- KHOỐI THÔNG TIN TỔNG QUAN -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px; background: #f8fafc; padding: 18px; border-radius: 10px; border: 1px solid #cbd5e1;">
            <div>
                <span style="font-size: 13px; color: #64748b; font-weight: bold; display: block; margin-bottom: 4px;">MÃ CLB</span>
                <strong style="color: #1e3a5f; font-size: 16px;">#<?= htmlspecialchars($club['club_id']) ?></strong>
            </div>

            <div>
                <span style="font-size: 13px; color: #64748b; font-weight: bold; display: block; margin-bottom: 4px;">NGƯỜI QUẢN LÝ</span>
                <strong style="color: #1e3a5f; font-size: 16px;"><?= htmlspecialchars($club['creator_name'] ?? $club['created_by']) ?></strong>
            </div>

            <div>
                <span style="font-size: 13px; color: #64748b; font-weight: bold; display: block; margin-bottom: 4px;">NGÀY THÀNH LẬP</span>
                <strong style="color: #1e3a5f; font-size: 16px;"><?= $createdAt ?></strong>
            </div>

            <div>
                <span style="font-size: 13px; color: #64748b; font-weight: bold; display: block; margin-bottom: 4px;">TRẠNG THÁI</span>
                <?= (int)$club['status'] === 1 
                    ? '<span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 12px; font-weight: bold; font-size: 13px; display: inline-block;">Đang hoạt động</span>' 
                    : '<span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 12px; font-weight: bold; font-size: 13px; display: inline-block;">Tạm ngưng</span>'; 
                ?>
            </div>
        </div>

        <!-- CHI TIẾT NỘI DUNG CLB -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div style="border: 1px solid #e2e8f0; padding: 18px; border-radius: 8px; background: #ffffff;">
                <h4 style="margin: 0 0 8px 0; color: #1e3a5f; font-size: 15px;"> TÊN CÂU LẠC BỘ</h4>
                <p style="margin: 0; color: #1e293b; font-size: 18px; font-weight: bold;"><?= htmlspecialchars($club['club_name'] ?? 'Chưa cập nhật') ?></p>
            </div>

            <div style="border: 1px solid #e2e8f0; padding: 18px; border-radius: 8px; background: #ffffff;">
                <h4 style="margin: 0 0 8px 0; color: #1e3a5f; font-size: 15px;"> GIỚI THIỆU CÂU LẠC BỘ</h4>
                <p style="margin: 0; color: #334155; line-height: 1.6; white-space: pre-line;"><?= htmlspecialchars($club['description'] ?? 'Chưa có thông tin giới thiệu.') ?></p>
            </div>

            <div style="border: 1px solid #e2e8f0; padding: 18px; border-radius: 8px; background: #ffffff;">
                <h4 style="margin: 0 0 8px 0; color: #1e3a5f; font-size: 15px;"> NỘI QUY CÂU LẠC BỘ</h4>
                <p style="margin: 0; color: #334155; line-height: 1.6; white-space: pre-line;"><?= htmlspecialchars($club['rule'] ?? 'Chưa có quy định.') ?></p>
            </div>
        </div>

    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
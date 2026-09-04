<?php 
$pageTitle = "Điểm Danh Sự Kiện";

// Khai báo giá trị mặc định để tránh cảnh báo Undefined Variable
$events         = $events ?? [];
$selectedEvent  = $selectedEvent ?? null;
$attendanceList = $attendanceList ?? [];

require_once __DIR__ . '/../includes/headers.php';
?>

<div style="display: flex; flex-direction: column; gap: 20px;">
    <!-- BỘ LỌC CHỌN SỰ KIỆN -->
    <div style="background: white; padding: 20px 25px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
        <form method="GET" action="index.php" style="display: flex; gap: 15px; align-items: center; width: 100%;">
            <input type="hidden" name="page" value="attendance">
            <label style="font-weight: bold; color: #1e3a5f; white-space: nowrap;">Chọn sự kiện điểm danh:</label>
            <select name="id" onchange="this.form.submit()" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; background: white;">
                <option value="">-- Chọn Sự Kiện --</option>
                <?php if (!empty($events)): foreach ($events as $ev): ?>
                    <option value="<?= $ev->getEventId() ?>" <?= (isset($_GET['id']) && $_GET['id'] == $ev->getEventId()) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ev->getEventName()) ?> (<?= $ev->getEventDate()->format('d/m/Y') ?>)
                    </option>
                <?php endforeach; endif; ?>
            </select>
        </form>
    </div>

    <!-- DANH SÁCH SINH VIÊN ĐIỂM DANH -->
    <?php if ($selectedEvent): ?>
    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <h2 style="color: #1e3a5f; margin-top: 0; font-size: 18px;">
            DANH SÁCH THAM GIA: <?= htmlspecialchars($selectedEvent->getEventName()) ?>
        </h2>
        <p style="color: #64748b; margin-bottom: 20px; font-size: 14px;">
            Tổng số sinh viên được duyệt: <strong><?= count($attendanceList) ?></strong>
        </p>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; color: #475569; text-align: left; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px;">STT</th>
                    <th style="padding: 12px;">Họ và Tên</th>
                    <th style="padding: 12px;">Mã SV / Username</th>
                    <th style="padding: 12px;">Email</th>
                    <th style="padding: 12px; text-align: center;">Thời Gian Điểm Danh</th>
                    <th style="padding: 12px; text-align: center;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($attendanceList)): foreach ($attendanceList as $index => $sv): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;" id="row-<?= htmlspecialchars($sv['username']) ?>">
                        <td style="padding: 12px; color: #64748b;"><?= $index + 1 ?></td>
                        <td style="padding: 12px; font-weight: bold; color: #1e3a5f;"><?= htmlspecialchars($sv['fullname']) ?></td>
                        <td style="padding: 12px;"><?= htmlspecialchars($sv['username']) ?></td>
                        <td style="padding: 12px; color: #475569;"><?= htmlspecialchars($sv['email']) ?></td>
                        <td style="padding: 12px; text-align: center;" class="time-cell">
                            <?= $sv['attendance_status'] == 1 ? date('d/m/Y H:i:s', strtotime($sv['attendance_time'])) : '<span style="color:#94a3b8;">Chưa điểm danh</span>' ?>
                        </td>
                        <td style="padding: 12px; text-align: center;" class="action-cell">
                            <?php if ($sv['attendance_status'] == 1): ?>
                                <button type="button" 
                                        onclick="toggleAttendance('<?= $selectedEvent->getEventId() ?>', '<?= htmlspecialchars($sv['username']) ?>', 0)"
                                        style="background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 6px 14px; border-radius: 6px; font-weight: bold; cursor: pointer;">
                                    ✓ Có mặt
                                </button>
                            <?php else: ?>
                                <button type="button" 
                                        onclick="toggleAttendance('<?= $selectedEvent->getEventId() ?>', '<?= htmlspecialchars($sv['username']) ?>', 1)"
                                        style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 6px; font-weight: bold; cursor: pointer;">
                                    Điểm danh
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #64748b;">
                            Chưa có sinh viên nào được duyệt đăng ký sự kiện này.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
function toggleAttendance(eventId, username, status) {
    const formData = new URLSearchParams();
    formData.append('event_id', eventId);
    formData.append('username', username);
    formData.append('status', status);

    fetch('index.php?page=api-attendance', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = document.getElementById(`row-${username}`);
            const actionCell = row.querySelector('.action-cell');
            const timeCell = row.querySelector('.time-cell');

            if (data.status === 1) {
                actionCell.innerHTML = `
                    <button type="button" 
                            onclick="toggleAttendance('${eventId}', '${username}', 0)"
                            style="background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 6px 14px; border-radius: 6px; font-weight: bold; cursor: pointer;">
                        ✓ Có mặt
                    </button>`;
                timeCell.innerHTML = data.time;
            } else {
                actionCell.innerHTML = `
                    <button type="button" 
                            onclick="toggleAttendance('${eventId}', '${username}', 1)"
                            style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 6px; font-weight: bold; cursor: pointer;">
                        Điểm danh
                    </button>`;
                timeCell.innerHTML = '<span style="color:#94a3b8;">Chưa điểm danh</span>';
            }
        } else {
            alert(data.message || 'Thao tác thất bại!');
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
        alert('Đã xảy ra lỗi kết nối máy chủ!');
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
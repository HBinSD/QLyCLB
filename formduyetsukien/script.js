

const form = document.getElementById('joinForm');
if (form) {
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();

        // Validate phía client
        let isValid = true;

        const fullname = document.getElementById('fullname').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const club = document.getElementById('club').value;
        const reason = document.getElementById('reason').value.trim();

        if (fullname.length < 2) {
            showError('fullname', 'Họ và tên phải có ít nhất 2 ký tự');
            isValid = false;
        }

        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('email', 'Email không hợp lệ');
            isValid = false;
        }

        if (!phone || !/^[0-9]{9,11}$/.test(phone)) {
            showError('phone', 'Số điện thoại phải gồm 9-11 chữ số');
            isValid = false;
        }

        if (!club) {
            showError('club', 'Vui lòng chọn câu lạc bộ');
            isValid = false;
        }

        if (reason.length < 10) {
            showError('reason', 'Lý do phải có ít nhất 10 ký tự');
            isValid = false;
        }

        if (!isValid) return;

        // Gửi form
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Đang gửi...';

        try {
            const formData = new FormData(form);
            const response = await fetch('submit.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showAlert(result.message, 'success');
                form.reset();
            } else {
                showAlert(result.message || 'Có lỗi xảy ra', 'error');
            }
        } catch (err) {
            showAlert('Không thể kết nối đến server. Vui lòng thử lại.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Gửi yêu cầu';
        }
    });
}

function showError(fieldId, message) {
    const input = document.getElementById(fieldId);
    const errorEl = document.getElementById(fieldId + '-error');
    if (input) input.classList.add('error-input');
    if (errorEl) errorEl.textContent = message;
}

function clearErrors() {
    document.querySelectorAll('.error').forEach(el => el.textContent = '');
    document.querySelectorAll('.error-input').forEach(el => el.classList.remove('error-input'));
}

function showAlert(message, type) {
    const alert = document.getElementById('alert');
    if (!alert) return;

    alert.textContent = message;
    alert.className = 'alert alert-' + type;
    alert.style.display = 'block';

    // Tự ẩn sau 5 giây
    setTimeout(() => {
        alert.style.display = 'none';
    }, 5000);

    // Cuộn lên đầu
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ==================== ADMIN: REVIEW REQUEST ====================

async function reviewRequest(id, status) {
    const action = status === 'approved' ? 'duyệt' : 'từ chối';
    const note = prompt(`Nhập ghi chú (tùy chọn) khi ${action} yêu cầu #${id}:`) || '';

    if (!confirm(`Bạn có chắc muốn ${action} yêu cầu #${id}?`)) {
        return;
    }

    try {
        const response = await fetch('review.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, status, note })
        });

        const result = await response.json();

        if (result.success) {
            showAlert(result.message, 'success');
            // Reload trang sau 1 giây
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(result.message || 'Có lỗi xảy ra', 'error');
        }
    } catch (err) {
        showAlert('Không thể kết nối đến server.', 'error');
    }
}

// ==================== ADMIN: FILTER ====================

document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        // Active state
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const filter = this.dataset.filter;
        const cards = document.querySelectorAll('.request-card');

        cards.forEach(card => {
            if (filter === 'all' || card.dataset.status === filter) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

// Hàm kiểm tra form đăng nhập/đăng ký
function validateForm(form) {
    let valid = true;
    const inputs = form.querySelectorAll('input[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            valid = false;
            input.classList.add('error');
        } else {
            input.classList.remove('error');
        }
    });
    
    return valid;
}

// Xử lý sự kiện khi trang được tải
document.addEventListener('DOMContentLoaded', function() {
    // Kiểm tra và xác thực form
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                alert('Vui lòng điền đầy đủ thông tin bắt buộc');
            }
        });
    });
    
    // Xử lý chọn ghế
    const seatCheckboxes = document.querySelectorAll('.seat input[type="checkbox"]');
    seatCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const selectedSeats = document.querySelectorAll('.seat input[type="checkbox"]:checked');
            if (selectedSeats.length > 6) {
                alert('Bạn chỉ có thể chọn tối đa 6 ghế');
                this.checked = false;
            }
        });
    });
    
    // Hiển thị thông báo tự động biến mất
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
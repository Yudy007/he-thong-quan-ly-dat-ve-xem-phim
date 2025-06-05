// =============================================
// HÀM CHUNG CHO TOÀN BỘ HỆ THỐNG
// =============================================

/**
 * Khởi tạo các sự kiện khi tài liệu tải xong
 */
document.addEventListener('DOMContentLoaded', function() {
    // 1. Xử lý form validation
    initFormValidation();
    
    // 2. Xử lý chọn ghế
    initSeatSelection();
    
    // 3. Xử lý thông báo tự động ẩn
    initAutoDismissAlerts();
    
    // 4. Xử lý menu responsive
    initResponsiveMenu();
    
    // 5. Xử lý chuyển đổi giao diện ghế
    initSeatAdjustment();
});

// =============================================
// FORM VALIDATION
// =============================================

function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    markFieldAsError(field, 'Vui lòng điền thông tin này');
                } else {
                    removeFieldError(field);
                    
                    // Kiểm tra định dạng email
                    if (field.type === 'email' && !isValidEmail(field.value)) {
                        isValid = false;
                        markFieldAsError(field, 'Email không hợp lệ');
                    }
                    
                    // Kiểm tra mật khẩu xác nhận
                    if (field.name === 'confirm_password') {
                        const password = form.querySelector('input[name="password"]');
                        if (password && password.value !== field.value) {
                            isValid = false;
                            markFieldAsError(field, 'Mật khẩu không khớp');
                        }
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showToast('error', 'Vui lòng kiểm tra lại thông tin');
            }
        });
    });
}

function markFieldAsError(field, message) {
    const formGroup = field.closest('.form-group');
    if (!formGroup) return;
    
    formGroup.classList.add('error');
    
    // Tạo hoặc cập nhật thông báo lỗi
    let errorElement = formGroup.querySelector('.error-message');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        formGroup.appendChild(errorElement);
    }
    
    errorElement.textContent = message;
}

function removeFieldError(field) {
    const formGroup = field.closest('.form-group');
    if (formGroup) {
        formGroup.classList.remove('error');
        
        const errorElement = formGroup.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();
        }
    }
}

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// =============================================
// XỬ LÝ CHỌN GHẾ
// =============================================

function initSeatSelection() {
    const seatMap = document.querySelector('.seat-map');
    if (!seatMap) return;
    
    seatMap.addEventListener('click', function(e) {
        const seatItem = e.target.closest('.seat');
        if (!seatItem) return;
        
        const checkbox = seatItem.querySelector('input[type="checkbox"]');
        if (!checkbox || checkbox.disabled) return;
        
        // Toggle trạng thái chọn
        checkbox.checked = !checkbox.checked;
        
        // Cập nhật giao diện
        if (checkbox.checked) {
            seatItem.classList.add('selected');
        } else {
            seatItem.classList.remove('selected');
        }
        
        // Cập nhật bộ đếm ghế
        updateSelectedSeatsCounter();
    });
}

function updateSelectedSeatsCounter() {
    const selectedSeats = document.querySelectorAll('.seat input[type="checkbox"]:checked');
    const counter = document.getElementById('selected-seats-counter');
    
    if (counter) {
        counter.textContent = `Đã chọn: ${selectedSeats.length} ghế`;
    }
    
    // Giới hạn tối đa 6 ghế
    if (selectedSeats.length >= 6) {
        document.querySelectorAll('.seat:not(.selected)').forEach(seat => {
            seat.classList.add('disabled');
        });
    } else {
        document.querySelectorAll('.seat.disabled').forEach(seat => {
            seat.classList.remove('disabled');
        });
    }
}

// =============================================
// XỬ LÝ GIAO DIỆN GHẾ NHÂN VIÊN
// =============================================

function initSeatAdjustment() {
    const seatAdjustment = document.querySelector('.seat-adjustment');
    if (!seatAdjustment) return;
    
    // Tự động cập nhật màu sắc khi thay đổi trạng thái
    const statusSelects = document.querySelectorAll('.status-select');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const seatItem = this.closest('.seat');
            if (!seatItem) return;
            
            // Cập nhật class theo trạng thái
            seatItem.className = 'seat ' + this.value;
        });
    });
}

// =============================================
// THÔNG BÁO & TOAST
// =============================================

function initAutoDismissAlerts() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
}

function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Hiển thị toast
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    // Tự động ẩn sau 5s
    setTimeout(() => {
        toast.classList.remove('show');
        
        setTimeout(() => {
            toast.remove();
        }, 500);
    }, 5000);
}

// =============================================
// MENU RESPONSIVE
// =============================================

function initResponsiveMenu() {
    const menuToggle = document.getElementById('menu-toggle');
    if (!menuToggle) return;
    
    const mainNav = document.querySelector('header nav ul');
    
    menuToggle.addEventListener('click', function() {
        mainNav.classList.toggle('show');
    });
    
    // Đóng menu khi click bên ngoài
    document.addEventListener('click', function(e) {
        if (!mainNav.contains(e.target)) {
            mainNav.classList.remove('show');
        }
    });
}

// =============================================
// XỬ LÝ DATE PICKER (nếu sử dụng)
// =============================================

function initDatePickers() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    dateInputs.forEach(input => {
        // Đặt giá trị mặc định là ngày hiện tại
        if (!input.value) {
            const today = new Date().toISOString().split('T')[0];
            input.value = today;
        }
        
        // Thêm trình chọn ngày
        input.addEventListener('focus', function() {
            this.type = 'date';
        });
    });
}

// =============================================
// XỬ LÝ AJAX (ví dụ)
// =============================================

function loadMovieDetails(movieId) {
    fetch(`api/movie_details.php?id=${movieId}`)
        .then(response => response.json())
        .then(data => {
            // Cập nhật giao diện với dữ liệu mới
            updateMovieUI(data);
        })
        .catch(error => {
            console.error('Error loading movie details:', error);
            showToast('error', 'Lỗi tải thông tin phim');
        });
}

// =============================================
// HÀM TIỆN ÍCH
// =============================================

/**
 * Định dạng số tiền
 * @param {number} amount 
 * @returns {string}
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}
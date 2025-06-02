-- BACKUP bằng Data Pump
expdp qlvexemphim/phim123@ORCL schemas=qlvexemphim dumpfile=phim_backup.dmp directory=DATA_PUMP_DIR logfile=backup.log

-- PHỤC HỒI lại dữ liệu
impdp qlvexemphim/phim123@ORCL schemas=qlvexemphim dumpfile=phim_backup.dmp directory=DATA_PUMP_DIR logfile=import.log

-- Nếu user lỗi, reset:
-- DROP USER qlvexemphim CASCADE;
-- Sau đó import lại

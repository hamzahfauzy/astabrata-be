CREATE TABLE request_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    request_id CHAR(32) NOT NULL,

    log_type ENUM('request', 'response') NOT NULL,

    method VARCHAR(10) NULL,

    url TEXT NULL,

    status_code SMALLINT UNSIGNED NULL,

    payload LONGTEXT NULL,

    ip_address VARCHAR(45) NULL,

    duration DECIMAL(12,2) NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_request_id (request_id),
    INDEX idx_log_type (log_type),
    INDEX idx_created_at (created_at),
    INDEX idx_status_code (status_code)
);
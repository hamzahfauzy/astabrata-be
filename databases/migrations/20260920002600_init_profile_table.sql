CREATE TABLE profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    personal_number VARCHAR(50) NOT NULL COMMENT 'NIK',
    family_number VARCHAR(50) NULL COMMENT 'No. KK',
    name VARCHAR(150) NOT NULL,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_profiles_personal_number (personal_number),

    INDEX idx_profiles_family_number (family_number),
    INDEX idx_profiles_name (name)
) ENGINE=InnoDB;

CREATE TABLE profile_periods (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    profile_id BIGINT UNSIGNED NOT NULL,
    period_id BIGINT UNSIGNED NOT NULL,

    serial_number VARCHAR(50) NULL,

    stage VARCHAR(50) NULL,
    status VARCHAR(50) NULL,
    
    last_stage VARCHAR(50) NULL,
    last_stage_result VARCHAR(50) NULL,

    program_type VARCHAR(100) NULL,

    phone VARCHAR(30) NULL,

    address TEXT NULL,

    village VARCHAR(100) NULL,
    region VARCHAR(100) NULL,
    regency VARCHAR(100) NOT NULL DEFAULT 'Asahan',
    province VARCHAR(100) NOT NULL DEFAULT 'Sumatera Utara',

    education VARCHAR(100) NULL,

    family_dependent_number INT UNSIGNED NOT NULL DEFAULT 0,

    social_assistance_type VARCHAR(100) NULL,
    business_assistance_type VARCHAR(100) NULL,

    has_bank_account VARCHAR(100) NOT NULL DEFAULT "YA",
    bank_account_name VARCHAR(150) NULL,
    bank_account_number VARCHAR(100) NULL,
    bank_name VARCHAR(100) NULL,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_profile_periods_profile
        FOREIGN KEY (profile_id)
        REFERENCES profiles(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_profile_periods_period
        FOREIGN KEY (period_id)
        REFERENCES periods(id)
        ON DELETE RESTRICT,

    UNIQUE KEY uq_profile_periods_profile_period (
        profile_id,
        period_id
    ),

    INDEX idx_profile_periods_period (period_id),
    INDEX idx_profile_periods_stage (stage),
    INDEX idx_profile_periods_status (status),
    INDEX idx_profile_periods_program_type (program_type)
) ENGINE=InnoDB;

CREATE TABLE profile_businesses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    profile_period_id BIGINT UNSIGNED NOT NULL,

    is_active VARCHAR(100) NOT NULL DEFAULT "YA",

    cluster VARCHAR(100) NULL,
    product VARCHAR(255) NULL,
    manager VARCHAR(150) NULL,

    is_location_in_home BOOLEAN NULL,

    address TEXT NULL,

    village VARCHAR(100) NULL,
    region VARCHAR(100) NULL,
    regency VARCHAR(100) NOT NULL DEFAULT 'Asahan',
    province VARCHAR(100) NOT NULL DEFAULT 'Sumatera Utara',

    start_month VARCHAR(50) NULL,

    surface_area VARCHAR(100) NULL,
    building_area VARCHAR(100) NULL,

    electricity VARCHAR(100) NULL,

    has_employee VARCHAR(100) NOT NULL DEFAULT "YA",
    num_of_employee INT UNSIGNED NOT NULL DEFAULT 0,

    daily_production VARCHAR(100) NULL,

    legal VARCHAR(255) NULL,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_profile_businesses_profile_period
        FOREIGN KEY (profile_period_id)
        REFERENCES profile_periods(id)
        ON DELETE CASCADE,

    INDEX idx_profile_businesses_profile_period (
        profile_period_id
    ),

    INDEX idx_profile_businesses_active (
        is_active
    )
) ENGINE=InnoDB;

CREATE TABLE profile_assessments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    profile_period_id BIGINT UNSIGNED NOT NULL,

    assessor_id BIGINT UNSIGNED NULL,
    assessor_name VARCHAR(150) NULL,

    phone VARCHAR(30) NULL,

    assessment_person VARCHAR(150) NULL,
    geo_tag VARCHAR(255) NULL,
    lat VARCHAR(100) NULL,
    lng VARCHAR(100) NULL,

    address TEXT NULL,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_profile_assessments_profile_period
        FOREIGN KEY (profile_period_id)
        REFERENCES profile_periods(id)
        ON DELETE CASCADE,

    INDEX idx_profile_assessments_profile_period (
        profile_period_id
    ),

    INDEX idx_profile_assessments_assessor (
        assessor_id
    )
) ENGINE=InnoDB;

CREATE TABLE profile_purposes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    profile_period_id BIGINT UNSIGNED NOT NULL,

    issue TEXT NULL,
    goals TEXT NULL,
    notes TEXT NULL,
    training_needs TEXT NULL,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_profile_purposes_profile_period
        FOREIGN KEY (profile_period_id)
        REFERENCES profile_periods(id)
        ON DELETE CASCADE,

    INDEX idx_profile_purposes_profile_period (
        profile_period_id
    )
) ENGINE=InnoDB;

CREATE TABLE profile_budgets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    profile_period_id BIGINT UNSIGNED NOT NULL,

    description VARCHAR(255) NOT NULL,

    qty DECIMAL(14,2) NOT NULL DEFAULT 0,

    unit VARCHAR(50) NULL,

    price DECIMAL(18,2) NOT NULL DEFAULT 0,

    total_price DECIMAL(18,2)
        GENERATED ALWAYS AS (qty * price) STORED,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_profile_budgets_profile_period
        FOREIGN KEY (profile_period_id)
        REFERENCES profile_periods(id)
        ON DELETE CASCADE,

    INDEX idx_profile_budgets_profile_period (
        profile_period_id
    )
) ENGINE=InnoDB;

CREATE TABLE profile_incomes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    profile_period_id BIGINT UNSIGNED NOT NULL,

    type ENUM('BUSINESS', 'OTHER') NOT NULL,

    description VARCHAR(255) NULL,

    amount VARCHAR(100) NOT NULL DEFAULT 0,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_profile_incomes_profile_period
        FOREIGN KEY (profile_period_id)
        REFERENCES profile_periods(id)
        ON DELETE CASCADE,

    INDEX idx_profile_incomes_profile_period (
        profile_period_id
    ),

    INDEX idx_profile_incomes_type (
        type
    )
) ENGINE=InnoDB;

CREATE TABLE profile_stages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    profile_period_id BIGINT UNSIGNED NOT NULL,

    name VARCHAR(150) NOT NULL,
    stage_type VARCHAR(50) NULL,

    result VARCHAR(50) NULL,
    data JSON NULL,

    created_by BIGINT UNSIGNED NULL,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_profile_stages_profile_period
        FOREIGN KEY (profile_period_id)
        REFERENCES profile_periods(id)
        ON DELETE CASCADE,

    INDEX idx_profile_stages_profile_period (
        profile_period_id
    ),

    INDEX idx_profile_stages_result (
        result
    ),

    INDEX idx_profile_stages_created_by (
        created_by
    )
) ENGINE=InnoDB;

CREATE TABLE profile_documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    profile_period_id BIGINT UNSIGNED NOT NULL,

    name VARCHAR(150) NULL,
    file_url TEXT NULL,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_profile_documents_profile_period
        FOREIGN KEY (profile_period_id)
        REFERENCES profile_periods(id)
        ON DELETE CASCADE

) ENGINE=InnoDB;
ALTER TABLE instances
ADD COLUMN region_type INT  NOT NULL DEFAULT 0,
ADD COLUMN region_id BIGINT UNSIGNED  NULL,
ADD COLUMN village_id BIGINT UNSIGNED NULL;

ALTER TABLE instances
    ADD INDEX idx_region_id (region_id),
    ADD INDEX idx_village_id (village_id),
    ADD CONSTRAINT fk_instances_region
        FOREIGN KEY (region_id)
        REFERENCES regions(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    ADD CONSTRAINT fk_instances_village
        FOREIGN KEY (village_id)
        REFERENCES villages(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT;
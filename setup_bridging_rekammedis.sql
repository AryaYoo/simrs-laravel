-- SQL Injection Script for BPJS Rekam Medis (E-Claim) Bridging
-- Use this to create necessary tables if they don't exist in your SIMRS database.

-- 1. Table for Resume Medis / Discharge Summary (FHIR Composition data)
-- This table stores data specifically needed for the Rekam Medis bridging that might not be in the standard transaction tables.
CREATE TABLE IF NOT EXISTS `bpjs_rekam_medis_resume` (
    `no_rawat` VARCHAR(20) NOT NULL,
    `no_sep` VARCHAR(40) NOT NULL,
    `keluhan_utama` TEXT DEFAULT NULL,
    `riwayat_penyakit` TEXT DEFAULT NULL, -- Reason for admission
    `diagnosis_masuk` TEXT DEFAULT NULL,
    `pemeriksaan_fisik` TEXT DEFAULT NULL,
    `plan_of_care` TEXT DEFAULT NULL,
    `instruksi_pulang` TEXT DEFAULT NULL,
    `alergi` TEXT DEFAULT NULL,
    `tgl_input` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `petugas_id` VARCHAR(20) DEFAULT NULL,
    PRIMARY KEY (`no_rawat`),
    INDEX (`no_sep`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Table for Bridging Logs
-- Tracks every attempt to send data to BPJS API.
CREATE TABLE IF NOT EXISTS `bpjs_rekam_medis_log` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `no_sep` VARCHAR(40) NOT NULL,
    `no_rawat` VARCHAR(20) NOT NULL,
    `tgl_kirim` DATETIME NOT NULL,
    `payload_request` LONGTEXT DEFAULT NULL, -- Encrypted dataMR or raw JSON
    `response_code` VARCHAR(10) DEFAULT NULL,
    `response_message` TEXT DEFAULT NULL,
    `status_sukses` TINYINT(1) DEFAULT 0, -- 0: Failed, 1: Success
    `user_id` VARCHAR(50) DEFAULT NULL,
    INDEX (`no_sep`),
    INDEX (`no_rawat`),
    INDEX (`tgl_kirim`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Table for Mapping Internal Procedure to SNOMED/LOINC (Optional but recommended)
-- If your internal RS codes don't match international standards requested by BPJS.
CREATE TABLE IF NOT EXISTS `bpjs_rekam_medis_mapping` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kategori` ENUM('procedure', 'observation', 'medication') NOT NULL,
    `kode_rs` VARCHAR(50) NOT NULL,
    `nama_rs` VARCHAR(255) DEFAULT NULL,
    `kode_standar` VARCHAR(50) NOT NULL, -- SNOMED / LOINC / ICD code
    `nama_standar` VARCHAR(255) DEFAULT NULL,
    UNIQUE KEY (`kategori`, `kode_rs`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

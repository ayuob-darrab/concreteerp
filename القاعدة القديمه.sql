-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 24, 2026 at 12:35 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `concreteerp`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts_type`
--

CREATE TABLE `accounts_type` (
  `id` int NOT NULL,
  `typename` varchar(200) NOT NULL,
  `code` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `accounts_type`
--

INSERT INTO `accounts_type` (`id`, `typename`, `code`) VALUES
(1, 'حساب موظف', 'emp'),
(2, 'حساب مقاول', 'cont'),
(3, 'سوبر ادمن', 'SA'),
(4, 'حساب مندوب', 'delegate');

-- --------------------------------------------------------

--
-- Table structure for table `account_balances`
--

CREATE TABLE `account_balances` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `account_type` enum('contractor','supplier','employee','customer','cash_register','bank') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `currency_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_debits` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_credits` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `balance_type` enum('debit','credit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_transaction_id` bigint UNSIGNED DEFAULT NULL,
  `last_transaction_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `advances`
--

CREATE TABLE `advances` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `advance_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `beneficiary_type` enum('employee','agent','supplier','contractor') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `remaining_amount` decimal(15,2) NOT NULL,
  `deduction_type` enum('percentage','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `deduction_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `auto_deduction` tinyint(1) NOT NULL DEFAULT '1',
  `status` enum('pending','approved','active','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `requested_by` bigint UNSIGNED NOT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approval_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `requested_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `advance_payments`
--

CREATE TABLE `advance_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `advance_id` bigint UNSIGNED NOT NULL,
  `payment_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_type` enum('manual','salary_deduction','invoice_deduction','commission_deduction') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_before` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `reference_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `payment_method` enum('cash','bank_transfer','check') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `paid_at` datetime NOT NULL,
  `paid_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `advance_settings`
--

CREATE TABLE `advance_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `max_advance_employee` decimal(15,2) NOT NULL DEFAULT '0.00',
  `max_advance_agent` decimal(15,2) NOT NULL DEFAULT '0.00',
  `max_advance_supplier` decimal(15,2) NOT NULL DEFAULT '0.00',
  `max_advance_contractor` decimal(15,2) NOT NULL DEFAULT '0.00',
  `default_deduction_employee` decimal(5,2) NOT NULL DEFAULT '10.00',
  `default_deduction_agent` decimal(5,2) NOT NULL DEFAULT '15.00',
  `default_deduction_supplier` decimal(5,2) NOT NULL DEFAULT '20.00',
  `default_deduction_contractor` decimal(5,2) NOT NULL DEFAULT '0.00',
  `auto_deduction_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `allow_overpayment` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `shift_id` bigint UNSIGNED DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `shift_start_time` time DEFAULT NULL,
  `late_minutes` int NOT NULL DEFAULT '0',
  `early_minutes` int NOT NULL DEFAULT '0',
  `check_out_time` time DEFAULT NULL,
  `shift_end_time` time DEFAULT NULL,
  `early_leave_minutes` int NOT NULL DEFAULT '0',
  `overtime_minutes` int NOT NULL DEFAULT '0',
  `total_work_minutes` int NOT NULL DEFAULT '0',
  `status` enum('present','late','absent','on_leave','on_mission','sick_leave','half_day') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `check_in_latitude` decimal(10,8) DEFAULT NULL,
  `check_in_longitude` decimal(11,8) DEFAULT NULL,
  `check_out_latitude` decimal(10,8) DEFAULT NULL,
  `check_out_longitude` decimal(11,8) DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `manager_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `modified_by` bigint UNSIGNED DEFAULT NULL,
  `modified_at` timestamp NULL DEFAULT NULL,
  `modification_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_in_ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_out_ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` bigint UNSIGNED DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `method` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `backups`
--

CREATE TABLE `backups` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `companies_count` int NOT NULL DEFAULT '0',
  `users_count` int NOT NULL DEFAULT '0',
  `tables_count` int NOT NULL DEFAULT '0',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint UNSIGNED NOT NULL,
  `longitude` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city_id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_code_v2` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_admin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `files_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `max_users` int UNSIGNED DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `longitude`, `latitude`, `city_id`, `company_code`, `branch_code_v2`, `branch_name`, `branch_admin`, `phone`, `email`, `address`, `files_path`, `is_active`, `max_users`, `created_date`, `created_at`, `updated_at`, `deleted_at`) VALUES
(30, NULL, NULL, 1, 'W4bFD', NULL, 'الفرع الرئيسي', 'عبدالله علي', '77777777777', NULL, 'بغداد', NULL, 1, NULL, '2026-04-20 07:54:28', '2026-04-20 04:54:28', '2026-04-20 04:54:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `car_type_id` bigint UNSIGNED NOT NULL,
  `car_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `car_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `car_model` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mixer_capacity` decimal(8,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `driver_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver_id` bigint UNSIGNED DEFAULT NULL,
  `backup_driver_id` bigint UNSIGNED DEFAULT NULL,
  `add_date` date NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `operational_status` enum('available','reserved','in_maintenance','out_of_service','scrapped') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `last_maintenance_date` date DEFAULT NULL,
  `next_maintenance_date` date DEFAULT NULL,
  `maintenance_interval_days` int NOT NULL DEFAULT '30',
  `fuel_consumption` decimal(5,2) DEFAULT NULL COMMENT 'لتر/ساعة أو لتر/كم',
  `fuel_consumption_unit` enum('per_hour','per_km') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'per_hour',
  `odometer_reading` int NOT NULL DEFAULT '0' COMMENT 'قراءة العداد',
  `working_hours` int NOT NULL DEFAULT '0' COMMENT 'ساعات العمل',
  `status_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'سبب الحالة',
  `status_changed_at` datetime DEFAULT NULL,
  `status_changed_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `company_code`, `branch_id`, `car_type_id`, `car_name`, `car_number`, `car_model`, `mixer_capacity`, `is_active`, `driver_name`, `driver_id`, `backup_driver_id`, `add_date`, `note`, `created_at`, `updated_at`, `operational_status`, `last_maintenance_date`, `next_maintenance_date`, `maintenance_interval_days`, `fuel_consumption`, `fuel_consumption_unit`, `odometer_reading`, `working_hours`, `status_reason`, `status_changed_at`, `status_changed_by`, `deleted_at`) VALUES
(5, 'W4bFD', 30, 28, 'مارسيدس', '5747852', '2022', NULL, 1, 'محمد علي', 5, 5, '2026-04-20', NULL, '2026-04-20 07:05:11', '2026-04-20 07:23:30', 'available', NULL, NULL, 30, NULL, 'per_hour', 0, 0, NULL, NULL, NULL, NULL),
(6, 'W4bFD', 30, 27, 'مارسيدس', '14455', '2022', 25.00, 1, 'سائق خباطة', 79, 80, '2026-04-20', NULL, '2026-04-20 07:05:34', '2026-04-20 08:08:14', 'available', NULL, NULL, 30, NULL, 'per_hour', 0, 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cars_types`
--

CREATE TABLE `cars_types` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'كود فريد لنوع السيارة',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` decimal(8,2) DEFAULT NULL,
  `company_code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cars_types`
--

INSERT INTO `cars_types` (`id`, `code`, `name`, `capacity`, `company_code`, `note`, `created_at`, `updated_at`) VALUES
(18, 'CT-KBLBZ', 'خباطة', NULL, 'fB6zg', NULL, '2026-01-18 17:43:25', '2026-01-18 17:43:25'),
(19, 'CT-K6OOS', 'بم', NULL, 'fB6zg', NULL, '2026-01-18 17:43:34', '2026-01-18 17:46:10'),
(20, 'CT-ZCHBK', 'شفل', NULL, 'fB6zg', NULL, '2026-01-18 17:43:39', '2026-01-18 17:43:39'),
(21, 'CT-VVNEK', 'لوري', NULL, 'fB6zg', NULL, '2026-01-18 17:46:17', '2026-01-18 17:46:17'),
(27, 'CT-NQPNE', 'خباطة', NULL, 'W4bFD', NULL, '2026-04-20 07:04:25', '2026-04-20 07:04:25'),
(28, 'CT-9BYDJ', 'بم', NULL, 'W4bFD', NULL, '2026-04-20 07:04:32', '2026-04-20 07:04:32');

-- --------------------------------------------------------

--
-- Table structure for table `car_drivers`
--

CREATE TABLE `car_drivers` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `car_id` bigint UNSIGNED NOT NULL COMMENT 'معرف السيارة',
  `driver_id` bigint UNSIGNED NOT NULL COMMENT 'معرف السائق (الموظف)',
  `shift_id` bigint UNSIGNED NOT NULL COMMENT 'معرف الشفت',
  `driver_type` enum('primary','backup') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'primary' COMMENT 'نوع السائق: رئيسي أو احتياطي',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'هل التكليف نشط؟',
  `assigned_date` date DEFAULT NULL COMMENT 'تاريخ التكليف',
  `end_date` date DEFAULT NULL COMMENT 'تاريخ انتهاء التكليف',
  `end_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'سبب إنهاء التكليف',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'ملاحظات',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `car_drivers`
--

INSERT INTO `car_drivers` (`id`, `company_code`, `car_id`, `driver_id`, `shift_id`, `driver_type`, `is_active`, `assigned_date`, `end_date`, `end_reason`, `notes`, `created_at`, `updated_at`) VALUES
(9, 'W4bFD', 6, 79, 19, 'primary', 0, '2026-04-20', '2026-04-20', 'تم التحديث من صفحة التعديل', NULL, '2026-04-20 07:59:02', '2026-04-20 07:59:02'),
(10, 'W4bFD', 6, 79, 19, 'primary', 0, '2026-04-20', '2026-04-20', 'تم التحديث من صفحة التعديل', NULL, '2026-04-20 07:59:02', '2026-04-20 07:59:02'),
(11, 'W4bFD', 6, 79, 19, 'primary', 0, '2026-04-20', '2026-04-20', 'تم التحديث من صفحة التعديل', NULL, '2026-04-20 07:59:02', '2026-04-20 08:08:14'),
(12, 'W4bFD', 6, 79, 19, 'primary', 1, '2026-04-20', NULL, NULL, NULL, '2026-04-20 08:08:14', '2026-04-20 08:08:14'),
(13, 'W4bFD', 6, 80, 19, 'backup', 1, '2026-04-20', NULL, NULL, NULL, '2026-04-20 08:08:14', '2026-04-20 08:08:14');

-- --------------------------------------------------------

--
-- Table structure for table `car_maintenances`
--

CREATE TABLE `car_maintenances` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `car_id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `maintenance_type` enum('periodic','emergency','repair','inspection','oil_change','tires','tire_change','brake','engine','electrical','body','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'periodic',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `maintenance_date` date NOT NULL,
  `odometer_reading` int DEFAULT NULL,
  `parts_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `labor_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `workshop_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `workshop_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `performed_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `next_maintenance_date` date DEFAULT NULL,
  `next_maintenance_km` int DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `invoice_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_payment_card_id` bigint UNSIGNED DEFAULT NULL,
  `payment_reference` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_registers`
--

CREATE TABLE `cash_registers` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `transaction_type` enum('cash_in','cash_out') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `payment_id` bigint UNSIGNED DEFAULT NULL,
  `financial_transaction_id` bigint UNSIGNED DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `closing_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `handled_by` bigint UNSIGNED NOT NULL,
  `handled_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `checks`
--

CREATE TABLE `checks` (
  `id` bigint UNSIGNED NOT NULL,
  `check_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `check_date` date NOT NULL,
  `due_date` date NOT NULL,
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_branch` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `drawer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `drawer_type` enum('contractor','customer','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `drawer_id` bigint UNSIGNED DEFAULT NULL,
  `beneficiary_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','deposited','collected','returned','bounced','cancelled','replaced') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `deposit_date` date DEFAULT NULL,
  `collection_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `return_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `bounce_count` int NOT NULL DEFAULT '0',
  `transaction_id` bigint UNSIGNED DEFAULT NULL,
  `invoice_id` bigint UNSIGNED DEFAULT NULL,
  `replacement_check_id` bigint UNSIGNED DEFAULT NULL,
  `company_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `image_front` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_back` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `check_status_logs`
--

CREATE TABLE `check_status_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `check_id` bigint UNSIGNED NOT NULL,
  `from_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `performed_by` bigint UNSIGNED DEFAULT NULL,
  `performed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chemicals`
--

CREATE TABLE `chemicals` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit_cost` decimal(18,2) NOT NULL DEFAULT '0.00',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chemicals`
--

INSERT INTO `chemicals` (`id`, `company_code`, `branch_id`, `name`, `unit`, `quantity_total`, `unit_cost`, `description`, `created_at`, `updated_at`) VALUES
(4, 'W4bFD', 30, 'FHFH', 'liter', 100.00, 1500.00, NULL, '2026-04-20 05:52:34', '2026-04-20 05:53:06'),
(5, 'W4bFD', 30, 'KHHKt', 'liter', 200.00, 1000.00, NULL, '2026-04-20 05:52:47', '2026-04-20 05:53:26');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int NOT NULL,
  `name_en` varchar(100) NOT NULL,
  `name_ar` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name_en`, `name_ar`) VALUES
(1, 'Baghdad', 'بغداد'),
(2, 'Basra', 'البصرة'),
(3, 'Mosul', 'الموصل'),
(5, 'Najaf', 'النجف'),
(6, 'Karbala', 'كربلاء'),
(7, 'Sulaymaniyah', 'السليمانية'),
(8, 'Kirkuk', 'كركوك'),
(9, 'Dhi Qar', 'ذي قار'),
(10, 'Anbar', 'الأنبار'),
(11, 'Erbil', 'أربيل'),
(12, 'Babylon', 'بابل'),
(13, 'Diyala', 'ديالى'),
(14, 'Wasit', 'واسط'),
(15, 'Maysan', 'ميسان'),
(16, 'Muthanna', 'المثنى'),
(17, 'Qadisiyyah', 'القادسية'),
(18, 'Dohuk', 'دهوك'),
(19, 'Saladin', 'صلاح الدين'),
(20, 'Halabja', 'حلبجة'),
(22, 'Nineveh', 'نينوى');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int NOT NULL,
  `code` varchar(5) NOT NULL,
  `code_v2` varchar(10) DEFAULT NULL,
  `latitude` varchar(250) DEFAULT NULL,
  `longitude` varchar(250) DEFAULT NULL,
  `managername` text NOT NULL,
  `name` varchar(150) NOT NULL,
  `city_id` int NOT NULL,
  `phone` varchar(25) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `is_suspended` tinyint(1) NOT NULL DEFAULT '0',
  `userAdmin` int DEFAULT NULL,
  `address` text NOT NULL,
  `note` text,
  `creation_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'سعر إنشاء الشركة',
  `logo` varchar(200) NOT NULL,
  `files_path` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `code`, `code_v2`, `latitude`, `longitude`, `managername`, `name`, `city_id`, `phone`, `email`, `created_at`, `updated_at`, `is_active`, `is_suspended`, `userAdmin`, `address`, `note`, `creation_price`, `logo`, `files_path`, `deleted_at`) VALUES
(1, 'SA', NULL, '', '', 'مدير النظام', 'السوبر ادمن', 1, '7713863214', 'ninesoft@gmail.com', '2025-10-02 23:04:29', '2026-03-18 13:16:08', 1, 0, NULL, 'بغداد - شارع الصناعة', NULL, 0.00, 'uploads/companies_logo/nine.png', NULL, NULL),
(27, 'uxixu', NULL, NULL, NULL, 'محمد علي كامل', 'شركة الامين', 1, '07777777777', 'amean@gmail.com', '2026-04-13 17:24:02', '2026-04-20 03:59:27', 1, 0, NULL, 'بغداد الامين', NULL, 0.00, 'uploads/uxixu/companies_logo/1776090242_nLJDuoP3LZoMRff8.jpg', NULL, '2026-04-20 03:59:27'),
(28, 'Sfhlh', NULL, NULL, NULL, 'محمد علي', 'شركة الاحسان', 1, '07488888888', 'ah@gmail.com', '2026-04-17 18:48:11', '2026-04-20 03:59:27', 1, 0, NULL, 'بغداد', NULL, 0.00, 'uploads/Sfhlh/companies_logo/1776440891_Gh9Hr7hygo57oamA.png', NULL, '2026-04-20 03:59:27'),
(29, 'W4bFD', NULL, NULL, NULL, 'محمد علي حسين', 'شركة الامين', 1, '01222222222', NULL, '2026-04-20 07:05:39', '2026-04-20 04:05:39', 1, 0, NULL, 'بغداد', NULL, 0.00, 'uploads/W4bFD/companies_logo/1776657939_VqpR0BnEGb81RU1K.png', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `company_payment_cards`
--

CREATE TABLE `company_payment_cards` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `card_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `holder_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_number_masked` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `expiry_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_payment_card_transactions`
--

CREATE TABLE `company_payment_card_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `company_payment_card_id` bigint UNSIGNED NOT NULL,
  `transaction_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('deposit','withdrawal') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_before` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `reference_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `company_code` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_subscriptions`
--

CREATE TABLE `company_subscriptions` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan_type` enum('monthly','yearly','percentage','trial','hybrid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `users_count` int NOT NULL DEFAULT '1',
  `price_per_user` decimal(12,2) DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `base_fee` decimal(15,2) NOT NULL DEFAULT '0.00',
  `percentage_rate` decimal(5,2) DEFAULT NULL,
  `order_fee_type` enum('percentage','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `fixed_order_fee` decimal(15,2) DEFAULT NULL,
  `orders_limit` int DEFAULT NULL,
  `orders_used` int NOT NULL DEFAULT '0',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `auto_renew` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('active','expired','suspended') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `payment_status` enum('pending','paid','partial') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension_days` int NOT NULL DEFAULT '0',
  `grace_days_used` int NOT NULL DEFAULT '0',
  `grace_period_start` date DEFAULT NULL,
  `is_in_grace_period` tinyint(1) NOT NULL DEFAULT '0',
  `days_to_deduct` int NOT NULL DEFAULT '0',
  `last_invoice_date` date DEFAULT NULL,
  `extension_deducted` tinyint(1) NOT NULL DEFAULT '0',
  `duration_quantity` int NOT NULL DEFAULT '1',
  `years_count` int NOT NULL DEFAULT '1',
  `total_days` int DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_subscriptions`
--

INSERT INTO `company_subscriptions` (`id`, `company_code`, `plan_type`, `users_count`, `price_per_user`, `total_amount`, `base_fee`, `percentage_rate`, `order_fee_type`, `fixed_order_fee`, `orders_limit`, `orders_used`, `start_date`, `end_date`, `auto_renew`, `status`, `payment_status`, `paid_amount`, `paid_at`, `payment_method`, `payment_reference`, `extension_days`, `grace_days_used`, `grace_period_start`, `is_in_grace_period`, `days_to_deduct`, `last_invoice_date`, `extension_deducted`, `duration_quantity`, `years_count`, `total_days`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(3, 'W4bFD', 'monthly', 11, 10000.00, 390000.00, 390000.00, 0.00, 'percentage', 0.00, NULL, 0, '2026-04-20', '2026-07-19', 0, 'active', 'paid', 390000.00, '2026-04-20 04:06:35', 'online', 'CARD-1-1776657995', 0, 0, NULL, 0, 0, NULL, 0, 3, 1, 90, NULL, 1, '2026-04-20 04:06:35', '2026-04-20 10:54:52');

-- --------------------------------------------------------

--
-- Table structure for table `company_subscription_prices`
--

CREATE TABLE `company_subscription_prices` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_per_user_monthly` decimal(12,2) DEFAULT NULL,
  `price_per_user_yearly` decimal(12,2) DEFAULT NULL,
  `custom_percentage_rate` decimal(5,2) DEFAULT NULL,
  `custom_fixed_order_fee` decimal(12,2) DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `concrete_mixes`
--

CREATE TABLE `concrete_mixes` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `costPrice` decimal(18,2) DEFAULT NULL,
  `salePrice` decimal(18,2) DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `classification` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cement` double(8,2) NOT NULL,
  `sand` double(8,2) NOT NULL,
  `gravel` double(8,2) NOT NULL,
  `water` double(8,2) NOT NULL,
  `cement_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sand_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gravel_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `water_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `concrete_mixes`
--


-- --------------------------------------------------------

--
-- Table structure for table `concrete_mix_category_prices`
--

CREATE TABLE `concrete_mix_category_prices` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'كود الشركة',
  `concrete_mix_id` bigint UNSIGNED NOT NULL COMMENT 'معرف الخلطة',
  `pricing_category_id` bigint UNSIGNED NOT NULL COMMENT 'معرف الفئة السعرية',
  `price_per_meter` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'سعر المتر المكعب',
  `cost_per_meter` decimal(15,2) DEFAULT NULL COMMENT 'تكلفة المتر المكعب',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'ملاحظات',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'فعالة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `concrete_mix_category_prices`
--

INSERT INTO `concrete_mix_category_prices` (`id`, `company_code`, `concrete_mix_id`, `pricing_category_id`, `price_per_meter`, `cost_per_meter`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(7, 'W4bFD', 71, 1, 60000.00, NULL, NULL, 1, '2026-04-20 06:00:37', '2026-04-20 06:00:37'),
(8, 'W4bFD', 71, 2, 65000.00, NULL, NULL, 1, '2026-04-20 06:00:37', '2026-04-20 06:00:37'),
(9, 'W4bFD', 71, 3, 70000.00, NULL, NULL, 1, '2026-04-20 06:00:37', '2026-04-20 06:00:37');

-- --------------------------------------------------------

--
-- Table structure for table `concrete_mix_chemicals`
--

CREATE TABLE `concrete_mix_chemicals` (
  `id` bigint UNSIGNED NOT NULL,
  `concrete_mix_id` bigint UNSIGNED NOT NULL,
  `chemical_id` bigint UNSIGNED NOT NULL,
  `quantity` decimal(18,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `concrete_mix_chemicals`
--

INSERT INTO `concrete_mix_chemicals` (`id`, `concrete_mix_id`, `chemical_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 27, 1, 2.00, '2026-04-13 15:15:22', '2026-04-13 15:15:22'),
(2, 27, 2, 2.00, '2026-04-13 15:15:22', '2026-04-13 15:15:22'),
(3, 49, 3, 1.00, '2026-04-17 20:11:42', '2026-04-17 20:11:42'),
(4, 71, 4, 1.50, '2026-04-20 05:57:51', '2026-04-20 05:58:52'),
(5, 71, 5, 1.50, '2026-04-20 05:57:51', '2026-04-20 05:58:52');

-- --------------------------------------------------------

--
-- Table structure for table `concrete_mix_prices`
--

CREATE TABLE `concrete_mix_prices` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contractors`
--

CREATE TABLE `contractors` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contract_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contract_name_en` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contract_adminstarter` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `license_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `phone1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `opening_balance_type` enum('debit','credit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'debit',
  `credit_limit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payment_terms` int NOT NULL DEFAULT '30',
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `price_category_id` int UNSIGNED DEFAULT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `contractor_type` enum('individual','company','government') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'company',
  `classification` enum('A','B','C','D','VIP') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'C',
  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
  `isactive` tinyint(1) NOT NULL DEFAULT '1',
  `is_blocked` tinyint(1) NOT NULL DEFAULT '0',
  `block_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `blocked_at` timestamp NULL DEFAULT NULL,
  `blocked_by` int UNSIGNED DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city_id` int UNSIGNED DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `createdate` date DEFAULT NULL,
  `last_order_date` date DEFAULT NULL,
  `total_orders` int NOT NULL DEFAULT '0',
  `total_quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `company_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contract_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `internal_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contractors`
--

INSERT INTO `contractors` (`id`, `code`, `contract_name`, `contract_name_en`, `contract_adminstarter`, `license_number`, `tax_number`, `user_id`, `phone1`, `phone2`, `whatsapp`, `email`, `website`, `opening_balance`, `opening_balance_type`, `credit_limit`, `payment_terms`, `discount_percentage`, `price_category_id`, `currency`, `contractor_type`, `classification`, `rating`, `isactive`, `is_blocked`, `block_reason`, `blocked_at`, `blocked_by`, `address`, `city_id`, `latitude`, `longitude`, `createdate`, `last_order_date`, `total_orders`, `total_quantity`, `total_amount`, `created_by`, `updated_by`, `company_code`, `branch_id`, `logo`, `contract_file`, `documents`, `note`, `internal_notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 'CON-00001', 'الاخوين للمقاولات', NULL, 'عبدالعظيم كامل', NULL, NULL, 78, '77888888888', NULL, NULL, NULL, NULL, 0.00, 'debit', 0.00, 30, 0.00, NULL, 'IQD', 'company', 'C', 0.0, 1, 0, NULL, NULL, NULL, 'بغداد', NULL, NULL, NULL, '2026-04-20', NULL, 0, 0.00, 0.00, NULL, NULL, 'W4bFD', 30, '1776663331_8cy8Q8kOO61B45pp.png', NULL, NULL, NULL, NULL, '2026-04-20 05:35:31', '2026-04-20 05:36:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contractor_accounts`
--

CREATE TABLE `contractor_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `contractor_id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` int UNSIGNED NOT NULL,
  `account_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `opening_balance_type` enum('debit','credit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'debit',
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `total_invoiced` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_paid` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `last_invoice_date` timestamp NULL DEFAULT NULL,
  `last_payment_date` timestamp NULL DEFAULT NULL,
  `last_transaction_date` timestamp NULL DEFAULT NULL,
  `is_frozen` tinyint(1) NOT NULL DEFAULT '0',
  `freeze_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contractor_accounts`
--

INSERT INTO `contractor_accounts` (`id`, `contractor_id`, `company_code`, `branch_id`, `account_number`, `opening_balance`, `opening_balance_type`, `current_balance`, `currency`, `total_invoiced`, `total_paid`, `total_discount`, `last_invoice_date`, `last_payment_date`, `last_transaction_date`, `is_frozen`, `freeze_reason`, `created_at`, `updated_at`) VALUES
(3, 3, 'W4bFD', 30, 'ACC-2026-00001', 0.00, 'debit', 0.00, 'IQD', 0.00, 0.00, 0.00, NULL, NULL, NULL, 0, NULL, '2026-04-20 05:35:31', '2026-04-20 05:35:31');

-- --------------------------------------------------------

--
-- Table structure for table `contractor_checks`
--

CREATE TABLE `contractor_checks` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `contractor_id` bigint UNSIGNED NOT NULL,
  `type` enum('received','issued') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `check_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_account` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `drawer_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payee_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','deposited','collected','rejected','returned','endorsed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `deposited_at` timestamp NULL DEFAULT NULL,
  `deposited_by` bigint UNSIGNED DEFAULT NULL,
  `collected_at` timestamp NULL DEFAULT NULL,
  `collected_amount` decimal(12,2) DEFAULT NULL,
  `collected_by` bigint UNSIGNED DEFAULT NULL,
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `return_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `returned_at` timestamp NULL DEFAULT NULL,
  `returned_by` bigint UNSIGNED DEFAULT NULL,
  `endorsed_to` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endorsed_at` timestamp NULL DEFAULT NULL,
  `endorsement_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `endorsed_by` bigint UNSIGNED DEFAULT NULL,
  `cancellation_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contractor_check_status_history`
--

CREATE TABLE `contractor_check_status_history` (
  `id` bigint UNSIGNED NOT NULL,
  `check_id` bigint UNSIGNED NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `changed_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contractor_invoices`
--

CREATE TABLE `contractor_invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `contractor_id` bigint UNSIGNED NOT NULL,
  `work_order_id` bigint UNSIGNED DEFAULT NULL,
  `invoice_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','issued','partial','paid','cancelled','overdue') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancellation_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint UNSIGNED DEFAULT NULL,
  `issued_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contractor_receipts`
--

CREATE TABLE `contractor_receipts` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `contractor_id` bigint UNSIGNED NOT NULL,
  `invoice_id` bigint UNSIGNED DEFAULT NULL,
  `type` enum('receipt','payment') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `receipt_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `receipt_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` enum('cash','bank_transfer','check') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_reference` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_bank` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_date` date DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `cancellation_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `decimal_places` int NOT NULL DEFAULT '0',
  `rate_updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `code`, `name_ar`, `name_en`, `symbol`, `exchange_rate`, `is_default`, `is_active`, `decimal_places`, `rate_updated_at`, `created_at`, `updated_at`) VALUES
(1, 'IQD', 'دينار عراقي', 'Iraqi Dinar', 'د.ع', 1.000000, 1, 1, 0, NULL, '2025-12-26 19:11:40', '2025-12-26 19:11:40'),
(2, 'USD', 'دولار أمريكي', 'US Dollar', '$', 1500.000000, 0, 1, 2, NULL, '2025-12-26 19:11:40', '2025-12-26 19:11:40');

-- --------------------------------------------------------

--
-- Table structure for table `customer_payments`
--

CREATE TABLE `customer_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `payment_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_code` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `work_order_id` bigint UNSIGNED NOT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` enum('cash','deferred') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remaining_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `company_payment_card_id` bigint UNSIGNED DEFAULT NULL,
  `reference_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('unpaid','partial','paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_payments`
--

INSERT INTO `customer_payments` (`id`, `payment_number`, `company_code`, `branch_id`, `work_order_id`, `customer_name`, `customer_phone`, `payment_type`, `payment_method`, `total_amount`, `paid_amount`, `remaining_amount`, `company_payment_card_id`, `reference_number`, `receipt_number`, `status`, `notes`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'CP-W4bFD-202604-0001', 'W4bFD', 30, 3, 'زبون مباشر', '01111222222', 'cash', 'cash', 1500000.00, 1500000.00, 0.00, NULL, NULL, NULL, 'paid', NULL, 77, 77, '2026-04-20 08:41:56', '2026-04-20 08:41:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_payment_records`
--

CREATE TABLE `customer_payment_records` (
  `id` bigint UNSIGNED NOT NULL,
  `record_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_payment_id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_before` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `company_payment_card_id` bigint UNSIGNED DEFAULT NULL,
  `reference_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_payment_records`
--

INSERT INTO `customer_payment_records` (`id`, `record_number`, `customer_payment_id`, `company_code`, `branch_id`, `payment_method`, `amount`, `balance_before`, `balance_after`, `company_payment_card_id`, `reference_number`, `receipt_number`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'CPR-W4bFD-202604-0001', 1, 'W4bFD', 30, 'cash', 1500000.00, 1500000.00, 0.00, NULL, NULL, NULL, 'دفعة من إجمالي 1500000 د.ع - توزيع تلقائي', 77, '2026-04-20 08:41:56', '2026-04-20 08:41:56');

-- --------------------------------------------------------

--
-- Table structure for table `daily_cash_summaries`
--

CREATE TABLE `daily_cash_summaries` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `summary_date` date NOT NULL,
  `currency_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_receipts` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_payments` decimal(15,2) NOT NULL DEFAULT '0.00',
  `closing_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `receipts_count` int NOT NULL DEFAULT '0',
  `payments_count` int NOT NULL DEFAULT '0',
  `status` enum('open','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `opened_by` bigint UNSIGNED DEFAULT NULL,
  `opened_at` datetime DEFAULT NULL,
  `closed_by` bigint UNSIGNED DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver_assignments`
--

CREATE TABLE `driver_assignments` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `car_id` bigint UNSIGNED NOT NULL,
  `driver_id` bigint UNSIGNED NOT NULL,
  `assignment_type` enum('primary','backup') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'primary',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `end_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_by` bigint UNSIGNED NOT NULL,
  `ended_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `company_code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_types_id` bigint UNSIGNED NOT NULL,
  `employee_type_code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shift_id` bigint UNSIGNED NOT NULL,
  `isactive` tinyint(1) NOT NULL DEFAULT '1',
  `createdate` date DEFAULT NULL,
  `file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `personImage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `salary` decimal(7,0) DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `national_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `contract_type` enum('permanent','contract','daily','hourly') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'permanent',
  `contract_start_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iban` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `annual_leave_balance` int NOT NULL DEFAULT '0',
  `sick_leave_balance` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `company_code`, `branch_id`, `fullname`, `employee_types_id`, `employee_type_code`, `shift_id`, `isactive`, `createdate`, `file`, `personImage`, `phone`, `salary`, `email`, `national_id`, `hire_date`, `contract_type`, `contract_start_date`, `contract_end_date`, `bank_name`, `bank_account`, `iban`, `emergency_contact_name`, `emergency_contact_phone`, `address`, `annual_leave_balance`, `sick_leave_balance`, `created_at`, `updated_at`, `deleted_at`) VALUES
(5, NULL, 'W4bFD', 30, 'محمد علي', 8, 'ENG', 19, 1, '2026-04-20', NULL, NULL, '88888888888', 400000, NULL, NULL, NULL, 'permanent', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-04-20 05:21:20', '2026-04-20 05:21:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_allowances`
--

CREATE TABLE `employee_allowances` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `allowance_type` enum('transportation','housing','meals','phone','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `custom_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT '1',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_bonuses`
--

CREATE TABLE `employee_bonuses` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `bonus_type` enum('performance','attendance','overtime','eid','annual','project','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `custom_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `bonus_date` date NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `paid_in_payroll_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_deductions`
--

CREATE TABLE `employee_deductions` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `deduction_type` enum('absence','late','early_leave','violation','damage','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `custom_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `deduction_date` date NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_deducted` tinyint(1) NOT NULL DEFAULT '0',
  `deducted_in_payroll_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_leaves`
--

CREATE TABLE `employee_leaves` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `leave_type` enum('annual','sick','emergency','unpaid','maternity','paternity','study','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `days_count` int NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `attachment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `requested_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_shifts`
--

CREATE TABLE `employee_shifts` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `shift_id` bigint UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'هل هذا الشفت الرئيسي للموظف',
  `assigned_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `end_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_shifts`
--

INSERT INTO `employee_shifts` (`id`, `company_code`, `employee_id`, `shift_id`, `is_active`, `is_primary`, `assigned_date`, `end_date`, `end_reason`, `notes`, `created_at`, `updated_at`) VALUES
(11, 'W4bFD', 5, 19, 1, 1, '2026-04-20', NULL, NULL, NULL, '2026-04-20 05:21:20', '2026-04-20 05:21:20'),
(12, 'W4bFD', 5, 20, 1, 0, '2026-04-20', NULL, NULL, NULL, '2026-04-20 05:21:20', '2026-04-20 05:21:20');

-- --------------------------------------------------------

--
-- Table structure for table `employee_types`
--

CREATE TABLE `employee_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_types`
--

INSERT INTO `employee_types` (`id`, `name`, `code`, `description`, `created_at`, `updated_at`) VALUES
(8, 'مهندس', 'ENG', 'مسؤول عن الجانب الفني مثل جودة الكونكريت والإنتاج والمواصفات.', '2026-04-20 04:21:07', '2026-04-20 04:21:07'),
(9, 'محاسب', 'ACC', 'يتابع الفواتير والمدفوعات والرواتب والحسابات المالية.', '2026-04-20 04:21:07', '2026-04-20 04:21:07'),
(10, 'سائق خباطة', 'DRV', 'مسؤول عن قيادة الخباطة وتوصيل الكونكريت للمواقع.', '2026-04-20 04:21:07', '2026-04-24 12:31:15'),
(11, 'أمين مستودع', 'WHS', 'يتحكم بدخول وخروج المواد والمعدات من المخزن.', '2026-04-20 04:21:07', '2026-04-20 04:21:07'),
(12, 'حارس', 'GRD', 'مسؤول عن أمن المصنع ومراقبة الدخول والخروج.', '2026-04-20 04:21:07', '2026-04-20 04:21:07'),
(13, 'سائق بم', 'PMP_DRV', 'مسؤول عن قيادة البَم وتشغيله في موقع الصب.', '2026-04-24 12:31:15', '2026-04-24 12:31:15');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financial_accounts`
--

CREATE TABLE `financial_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `account_type` enum('contractor','supplier','direct_client','employee') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_holder_id` bigint UNSIGNED DEFAULT NULL,
  `account_holder_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `opening_balance_type` enum('debit','credit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'debit',
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financial_transactions`
--

CREATE TABLE `financial_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `transaction_type` enum('sale_invoice','purchase_invoice','payment_received','payment_made','salary','commission','expense','loss','refund','adjustment') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_id` bigint UNSIGNED DEFAULT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `reference_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `exchange_rate` decimal(10,4) NOT NULL DEFAULT '1.0000',
  `amount_in_iqd` decimal(15,2) DEFAULT NULL,
  `payment_method` enum('cash','check','bank_transfer','credit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `check_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_date` date DEFAULT NULL,
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `performed_by` bigint UNSIGNED NOT NULL,
  `performed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventories`
--

CREATE TABLE `inventories` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` int DEFAULT NULL,
  `unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity_total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `unit_cost` decimal(18,2) NOT NULL DEFAULT '0.00',
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventories`
--

INSERT INTO `inventories` (`id`, `code`, `name`, `company_code`, `branch_id`, `unit`, `quantity_total`, `unit_cost`, `note`, `created_at`, `updated_at`) VALUES
(9, 'GKLWE', 'اسمنت', 'W4bFD', 30, 'ton', 40.00, 6250.00, 'لا يوجد', '2026-04-20 04:54:28', '2026-04-20 05:49:37'),
(10, '8ODEN', 'رمل', 'W4bFD', 30, 'm3', 72.00, 6805.56, 'لا يوجد', '2026-04-20 04:54:28', '2026-04-20 05:50:00'),
(11, 'DXZQE', 'حصى', 'W4bFD', 30, 'm3', 40.00, 5500.00, 'لا يوجد', '2026-04-20 04:54:28', '2026-04-20 05:50:51'),
(12, 'CKYUB', 'مياه', 'W4bFD', 30, 'liter', 100000.00, 0.10, 'لا يوجد', '2026-04-20 04:54:28', '2026-04-20 05:52:13');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_histories`
--

CREATE TABLE `inventory_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `material_code` varchar(25) NOT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `MaterialEquipment_id` int NOT NULL,
  `countUnit` int NOT NULL,
  `company_code` varchar(255) NOT NULL,
  `total_cost` decimal(18,2) DEFAULT NULL,
  `shipment_date` date NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `inventory_histories`
--

INSERT INTO `inventory_histories` (`id`, `material_code`, `supplier_id`, `MaterialEquipment_id`, `countUnit`, `company_code`, `total_cost`, `shipment_date`, `note`, `user_id`, `created_at`, `updated_at`) VALUES
(13, 'GKLWE', 6, 11, 2, 'W4bFD', 250000.00, '2026-04-20', NULL, 76, '2026-04-20 05:49:37', '2026-04-20 05:49:37'),
(14, '8ODEN', 5, 9, 2, 'W4bFD', 245000.00, '2026-04-20', NULL, 76, '2026-04-20 05:50:00', '2026-04-20 05:50:00'),
(15, '8ODEN', 5, 9, 2, 'W4bFD', 245000.00, '2026-04-20', NULL, 76, '2026-04-20 05:50:00', '2026-04-20 05:50:00'),
(16, 'DXZQE', 6, 10, 2, 'W4bFD', 220000.00, '2026-04-20', NULL, 76, '2026-04-20 05:50:51', '2026-04-20 05:50:51'),
(17, 'CKYUB', 6, 12, 100000, 'W4bFD', 10000.00, '2026-04-20', NULL, 76, '2026-04-20 05:52:13', '2026-04-20 05:52:13'),
(18, '4', 5, 12, 100, 'W4bFD', 150000.00, '2026-04-20', NULL, 76, '2026-04-20 05:53:06', '2026-04-20 05:53:06'),
(19, '5', 5, 12, 200, 'W4bFD', 200000.00, '2026-04-20', NULL, 76, '2026-04-20 05:53:26', '2026-04-20 05:53:26');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_losses`
--

CREATE TABLE `inventory_losses` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `material_type` enum('inventory','chemical') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `material_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `material_id` bigint UNSIGNED DEFAULT NULL,
  `material_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity_lost` decimal(15,4) NOT NULL,
  `quantity_base` decimal(15,4) NOT NULL,
  `unit_cost` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `unit_price_display` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `total_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `reported_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_type` enum('sale','credit_note','debit_note') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sale',
  `account_id` bigint UNSIGNED DEFAULT NULL,
  `work_order_id` bigint UNSIGNED DEFAULT NULL,
  `company_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `party_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `party_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `party_tax_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remaining_amount` decimal(15,2) NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `exchange_rate` decimal(15,4) NOT NULL DEFAULT '1.0000',
  `status` enum('draft','issued','partially_paid','paid','overdue','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint UNSIGNED DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_id` bigint UNSIGNED NOT NULL,
  `item_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'concrete',
  `item_id` bigint UNSIGNED DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(15,3) NOT NULL,
  `unit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'م³',
  `unit_price` decimal(15,2) NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location_logs`
--

CREATE TABLE `location_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `shipment_id` bigint UNSIGNED NOT NULL,
  `vehicle_id` bigint UNSIGNED DEFAULT NULL,
  `driver_id` bigint UNSIGNED DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `speed` decimal(5,2) DEFAULT NULL,
  `heading` decimal(5,2) DEFAULT NULL,
  `accuracy` decimal(10,2) DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_records`
--

CREATE TABLE `maintenance_records` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `vehicle_id` bigint UNSIGNED NOT NULL,
  `maintenance_type` enum('scheduled','preventive','corrective','emergency') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `odometer_before` int DEFAULT NULL,
  `odometer_after` int DEFAULT NULL,
  `working_hours_before` int DEFAULT NULL,
  `working_hours_after` int DEFAULT NULL,
  `labor_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `parts_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `parts_used` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `started_at` datetime NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `performed_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_workshop` tinyint(1) NOT NULL DEFAULT '0',
  `workshop_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `next_maintenance_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `reserved_quantity` decimal(15,3) NOT NULL DEFAULT '0.000',
  `unit_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `material_components`
--

CREATE TABLE `material_components` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `material_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `material_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `material_equipment`
--

CREATE TABLE `material_equipment` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` int NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `material_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `material_equipment`
--

INSERT INTO `material_equipment` (`id`, `name`, `capacity`, `code`, `material_type`, `company_code`, `note`, `created_at`, `updated_at`) VALUES
(9, 'تك رمل', 18, 'm3', 'رمل', 'W4bFD', NULL, '2026-04-20 05:41:30', '2026-04-20 05:41:30'),
(10, 'تك حصو', 20, 'm3', 'حصى', 'W4bFD', NULL, '2026-04-20 05:41:45', '2026-04-20 05:41:45'),
(11, 'اسمنت', 1, 'ton', 'اسمنت', 'W4bFD', 'كل 1 طن = 20 كيس', '2026-04-20 05:42:14', '2026-04-20 05:42:14'),
(12, 'لتر', 1, 'liter', 'مياه', 'W4bFD', NULL, '2026-04-20 05:42:44', '2026-04-20 05:42:44');

-- --------------------------------------------------------

--
-- Table structure for table `material_reservations`
--

CREATE TABLE `material_reservations` (
  `id` bigint UNSIGNED NOT NULL,
  `job_id` bigint UNSIGNED NOT NULL,
  `material_id` bigint UNSIGNED NOT NULL,
  `inventory_id` bigint UNSIGNED NOT NULL,
  `quantity_reserved` decimal(15,4) NOT NULL,
  `quantity_used` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `status` enum('reserved','partially_used','fully_used','released') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'reserved',
  `reserved_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reserved_by` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `measurement_units`
--

CREATE TABLE `measurement_units` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `measurement_units`
--

INSERT INTO `measurement_units` (`id`, `name`, `code`, `note`, `created_at`, `updated_at`) VALUES
(1, 'متر مكعب', 'm3', 'لا يوجد', NULL, NULL),
(2, 'طن', 'ton', 'كل 1 طن = 20 كيس .', NULL, '2025-10-25 07:05:57'),
(4, 'لتر', 'liter', '1 لتر = 100 سيسي', '2025-11-03 16:33:21', '2025-11-03 16:34:26');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_resets_table', 1),
(2, '2019_08_19_000000_create_failed_jobs_table', 1),
(3, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(4, '2025_10_06_061646_create_branches_table', 1),
(5, '2025_10_06_070202_create_employee_types_table', 2),
(6, '2025_10_06_070605_create_shift_times_table', 2),
(7, '2025_10_06_071139_create_employees_table', 2),
(8, '2025_10_10_122512_create_materials_table', 3),
(9, '2025_10_10_143300_create_cars_types_table', 4),
(10, '2025_10_10_143709_create_cars_table', 4),
(11, '2025_10_13_045340_create_contractors_table', 5),
(12, '2025_10_24_171316_create_material_components_table', 6),
(13, '2025_10_25_070848_create_material_equipment_table', 7),
(14, '2025_10_25_083502_create_measurement_units_table', 8),
(15, '2025_10_25_112221_create_concrete_mixes_table', 9),
(16, '2025_10_29_195513_create_chemicals_table', 10),
(17, '2025_10_29_200950_create_concrete_mix_chemicals_table', 10),
(18, '2025_11_01_115939_create_inventories_table', 11),
(19, '2025_11_01_120216_create_suppliers_table', 11),
(20, '2025_11_01_120234_create_inventory_histories_table', 11),
(21, '2025_11_14_141927_create_concrete_mix_types_table', 12),
(22, '2025_11_22_072948_create_request_status_table', 13),
(23, '2025_11_22_073956_create_workrequests_table', 14),
(24, '2025_12_02_150220_create_work_orders_table', 15),
(25, '2025_12_05_134603_create_work_orders_table', 16),
(26, '2025_12_14_000001_create_order_stages_table', 17),
(27, '2025_12_14_000002_create_order_histories_table', 17),
(28, '2025_12_14_000003_create_order_executions_table', 17),
(29, '2025_12_14_000004_create_order_price_changes_table', 17),
(30, '2025_12_15_041045_add_deleted_at_to_work_orders_table', 18),
(31, '2025_12_15_045955_add_created_by_to_work_orders_table', 19),
(32, '2025_12_15_053840_update_companies_table_add_subscription_fields', 20),
(33, '2025_12_15_054347_stage1_extend_core_tables', 20),
(34, '2025_12_15_060302_create_company_subscriptions_table', 21),
(35, '2025_12_16_042739_add_is_suspended_to_companies_table', 22),
(36, '2025_12_16_050000_add_delegate_account_type', 23),
(37, '2025_12_16_062234_create_subscription_history_table', 24),
(38, '2025_12_18_create_roles_table', 25),
(39, '2025_12_18_create_settings_table', 26),
(40, '2025_12_18_create_backups_table', 27),
(41, '2025_12_18_create_notifications_table', 28),
(42, '2025_12_18_081901_add_capacity_to_cars_types_table', 29),
(43, '2025_12_18_083156_fix_contractors_logo_nullable', 30),
(44, '2025_12_18_000001_create_financial_accounts_table', 31),
(45, '2025_12_18_000002_create_financial_transactions_table', 31),
(46, '2025_12_18_000003_create_payments_table', 31),
(47, '2025_12_18_000004_create_cash_registers_table', 31),
(48, '2025_12_18_095951_add_deleted_at_to_users_table', 32),
(49, '2025_12_18_100104_add_deleted_at_to_companies_table', 33),
(50, '2025_12_18_100254_add_deleted_at_to_branches_table', 34),
(51, '2025_12_18_101851_create_pricing_categories_table', 35),
(52, '2025_12_18_101910_create_concrete_mix_prices_table', 35),
(53, '2025_12_18_110001_create_pricing_tiers_table', 35),
(54, '2025_12_18_110002_create_concrete_mix_pricing_table', 35),
(55, '2025_12_18_110003_seed_pricing_tiers_for_existing_companies', 36),
(56, '2025_12_18_120001_update_pricing_categories_table', 37),
(57, '2025_12_18_120002_create_concrete_mix_category_prices_table', 38),
(58, '2025_12_18_150001_create_support_tickets_table', 39),
(59, '2025_12_18_150002_create_ticket_replies_table', 39),
(60, '2025_12_19_000001_create_supplier_payments_table', 40),
(61, '2025_12_19_000001_improve_contractors_table', 41),
(62, '2025_12_19_000002_create_contractor_accounts_table', 42),
(63, '2025_12_19_000003_create_financial_transactions_table', 42),
(64, '2025_12_19_000004_create_invoices_table', 42),
(65, '2025_12_19_000005_create_checks_table', 42),
(66, '2025_12_19_000006_create_receipts_table', 42),
(67, '2025_12_19_000007_create_notifications_tables', 43),
(68, '2025_12_20_000001_add_deleted_at_to_contractors_table', 43),
(69, '2025_12_24_000001_add_subscription_payment_and_extension_fields', 44),
(70, '2025_12_24_000002_add_action_types_to_subscription_history', 45),
(71, '2025_12_25_000001_create_advances_table', 46),
(72, '2025_12_25_000002_create_advance_payments_table', 46),
(73, '2025_12_25_000003_create_advance_settings_table', 46),
(74, '2025_12_25_100001_update_employees_table', 46),
(75, '2025_12_25_100002_create_employee_allowances_table', 46),
(76, '2025_12_25_100003_create_payroll_table', 46),
(77, '2025_12_25_100004_create_employee_deductions_table', 46),
(78, '2025_12_25_100005_create_employee_bonuses_table', 46),
(79, '2025_12_25_100006_create_employee_leaves_table', 46),
(80, '2025_12_25_100007_create_salary_adjustments_table', 46),
(81, '2025_12_25_200001_update_work_orders_negotiations', 47),
(82, '2025_12_25_200002_create_order_negotiations_table', 47),
(83, '2025_12_25_200003_create_order_timeline_table', 47),
(84, '2025_12_25_300001_update_cars_operational_fields', 48),
(85, '2025_12_25_300002_create_vehicle_drivers_table', 48),
(86, '2025_12_25_300003_create_maintenance_records_table', 48),
(87, '2025_12_25_300004_create_vehicle_reservations_table', 48),
(88, '2025_12_25_300005_create_vehicle_status_history_table', 48),
(89, '2025_12_25_400001_create_work_jobs_table', 48),
(90, '2025_12_25_400002_create_work_shipments_table', 48),
(91, '2025_12_25_400003_create_shipment_events_table', 48),
(92, '2025_12_25_400004_create_location_logs_table', 48),
(93, '2025_12_25_400005_create_work_losses_table', 48),
(94, '2025_12_25_400006_create_material_reservations_table', 48),
(95, '2025_12_25_500001_create_currencies_table', 48),
(96, '2025_12_25_500002_create_payment_receipts_table', 48),
(97, '2025_12_25_500003_create_payment_vouchers_table', 48),
(98, '2025_12_25_500004_create_account_balances_table', 48),
(99, '2025_12_25_500005_create_daily_cash_summaries_table', 48),
(100, '2025_12_25_600001_update_notifications_table', 49),
(101, '2025_12_25_600002_create_notification_templates_table', 49),
(102, '2025_12_25_600003_create_notification_settings_table', 50),
(103, '2025_12_25_600004_create_notification_logs_table', 50),
(104, '2025_12_27_100001_create_contractor_invoices_table', 50),
(105, '2025_12_27_100002_create_contractor_checks_table', 50),
(106, '2025_12_27_100003_create_contractor_receipts_table', 50),
(107, '2025_12_27_100004_create_contractor_check_status_history_table', 50),
(108, '2025_12_31_000001_add_related_fields_to_notifications_table', 51),
(109, '2026_01_02_225035_add_code_to_cars_types_table', 52),
(110, '2026_01_08_000001_add_user_id_to_employees_table', 53),
(111, '2026_01_12_000001_create_payment_cards_table', 54),
(112, '2026_01_12_000002_add_order_fee_type_to_company_subscriptions', 55),
(113, '2026_01_16_000001_add_employee_user_type', 56),
(114, '2026_01_16_000002_create_driver_assignments_table', 57),
(115, '2026_01_16_000001_create_car_drivers_table', 58),
(116, '2026_01_16_000002_fix_car_drivers_unique_constraint', 59),
(117, '2026_01_16_000003_remove_car_drivers_unique_constraint', 60),
(118, '2026_01_16_100001_create_attendances_table', 61),
(119, '2026_01_18_100000_add_car_name_and_mixer_capacity_to_cars_table', 62),
(120, '2026_01_19_100000_create_car_maintenances_table', 63),
(121, '2026_01_19_210000_rename_cost_to_total_cost_in_car_maintenances', 64),
(122, '2026_01_19_214800_add_performed_by_to_car_maintenances', 65),
(123, '2026_01_21_195748_add_session_tracking_to_users_table', 66),
(124, '2026_01_21_202329_add_is_logged_in_to_users_table', 67),
(125, '2026_01_21_214549_add_subscription_deactivation_to_users_table', 68),
(126, '2025_12_31_152915_update_advance_number_length', 69),
(127, '2026_01_02_204149_create_work_shipments_table', 70),
(128, '2026_01_03_204215_add_driver_fields_to_cars_table', 71),
(129, '2026_01_12_000002_add_order_fee_fields_to_subscriptions', 72),
(130, '2026_01_17_000001_update_subscription_system', 73),
(131, '2026_01_17_211408_add_additional_user_action_type_to_subscription_history', 73),
(132, '2026_01_19_210000_update_maintenance_type_enum', 73),
(133, '2026_01_20_200424_add_material_type_to_material_equipment_table', 74),
(134, '2026_01_20_203618_add_location_map_url_to_work_orders_table', 75),
(135, '2026_01_23_000001_add_pump_fields_to_work_jobs', 76),
(136, '2026_01_24_000001_create_employee_shifts_table', 77),
(137, '2026_01_24_000002_migrate_employee_shifts_data', 78),
(138, '2026_01_27_000001_add_creation_price_to_companies_table', 79),
(139, '2026_02_15_200000_add_customer_name_to_work_orders', 80),
(140, '2026_02_16_200000_add_payment_fields_to_work_orders_table', 81),
(141, '2026_02_17_000001_create_company_payment_cards_table', 82),
(142, '2026_02_17_000002_create_customer_payments_table', 83),
(143, '2026_02_19_100000_add_status_to_financial_transactions_table', 84),
(144, '2026_02_20_160833_add_card_id_to_supplier_payments', 85),
(145, '2026_02_20_170000_add_missing_price_columns_to_work_orders', 86),
(147, '2026_02_18_000001_change_car_drivers_driver_to_users', 87),
(148, '2026_02_21_000001_add_trial_days_to_subscription_pricing', 87),
(149, '2026_02_18_100000_add_max_users_to_branches_table', 88),
(150, '2026_02_27_143048_fix_car_drivers_driver_id_foreign_key', 89),
(151, '2026_02_27_152912_add_username_to_users_table', 90),
(152, '2026_03_07_000001_create_seo_settings_table', 91),
(153, '2026_03_08_000001_fix_customer_payment_records_foreign_keys', 92),
(154, '2026_03_08_100000_seed_iraqi_governorates', 93),
(155, '2026_03_24_120000_create_public_display_tables', 93),
(156, '2026_03_25_100000_create_public_contact_settings_table', 94),
(157, '2026_03_25_160000_backfill_public_display_block_titles', 95),
(158, '2026_04_01_120000_add_performance_indexes', 96),
(159, '2026_04_08_120000_add_payment_fields_to_car_maintenances', 97),
(161, '2026_04_09_120000_create_inventory_losses_table', 98),
(162, '2026_04_10_100000_add_damaged_status_to_work_shipments', 99),
(163, '2026_04_11_120000_add_completed_with_loss_to_work_shipments', 100),
(164, '2026_04_11_130000_add_completed_fields_to_work_orders_table', 101),
(165, '2026_04_12_140000_contractors_code_unique_per_company', 102),
(166, '2026_04_17_120000_widen_users_account_code_column', 103),
(167, '2026_04_20_120000_make_email_columns_nullable', 104),
(168, '2026_04_21_100000_add_code_and_description_to_employee_types_table', 105),
(169, '2026_04_21_150000_make_branches_email_nullable', 106),
(170, '2026_04_22_100000_add_employee_type_code_to_employees_table', 107),
(171, '2026_04_22_110000_add_emp_type_code_to_users_table', 108),
(172, '2026_04_22_120000_drop_emp_type_id_from_users_table', 109),
(173, '2026_04_23_100000_create_audit_logs_table', 110),
(174, '2026_04_23_110000_add_unique_constraint_to_suppliers_table', 111),
(175, '2026_04_23_120000_add_shift_id_to_users_table', 112),
(176, '2026_04_23_130000_create_user_shifts_table', 113),
(177, '2026_04_23_140000_switch_car_driver_ids_to_users', 114),
(178, '2026_04_20_200000_create_page_seo_settings_table', 115),
(179, '2026_04_24_153052_sync_driver_employee_types_data', 116);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('info','warning','success','danger') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `related_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` bigint UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `sent_by` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SA',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `notification_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `priority` enum('low','normal','high','urgent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `channels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '["app","sms","whatsapp"]',
  `sent_channels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'القنوات التي تم الإرسال عبرها فعلاً',
  `action_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_label` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `company_code`, `title`, `message`, `type`, `related_type`, `related_id`, `is_read`, `sent_by`, `read_at`, `created_at`, `updated_at`, `branch_id`, `notification_type`, `priority`, `channels`, `sent_channels`, `action_url`, `action_label`, `icon`, `expires_at`) VALUES
(3, 'ALL', 'سيتم تحديث البرنامج', 'تحديث البرنامج', 'info', NULL, NULL, 0, 'SA', NULL, '2026-04-17 16:27:31', '2026-04-17 16:27:31', NULL, 'general', 'normal', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'W4bFD', 'تم إنشاء اشتراك جديد', 'مرحباً شركة شركة الامين، تم إنشاء اشتراك جديد بنجاح.\nنوع الاشتراك: شهري\nالمدة: 3 شهر\nتاريخ البداية: 2026/04/20\nتاريخ النهاية: 2026/07/19', 'success', NULL, NULL, 0, '1', NULL, '2026-04-20 04:06:35', '2026-04-20 04:06:35', NULL, 'general', 'normal', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notification_logs`
--

CREATE TABLE `notification_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `notification_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel` enum('app','sms','whatsapp','email') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','sent','delivered','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `provider` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider_response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sent_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_settings`
--

CREATE TABLE `notification_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `notify_new_order` tinyint(1) NOT NULL DEFAULT '1',
  `notify_order_approved` tinyint(1) NOT NULL DEFAULT '1',
  `notify_order_rejected` tinyint(1) NOT NULL DEFAULT '1',
  `notify_order_completed` tinyint(1) NOT NULL DEFAULT '1',
  `notify_payment_received` tinyint(1) NOT NULL DEFAULT '1',
  `notify_invoice_issued` tinyint(1) NOT NULL DEFAULT '1',
  `notify_check_due` tinyint(1) NOT NULL DEFAULT '1',
  `notify_check_bounced` tinyint(1) NOT NULL DEFAULT '1',
  `channel_database` tinyint(1) NOT NULL DEFAULT '1',
  `channel_email` tinyint(1) NOT NULL DEFAULT '1',
  `channel_sms` tinyint(1) NOT NULL DEFAULT '0',
  `channel_whatsapp` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_templates`
--

CREATE TABLE `notification_templates` (
  `id` bigint UNSIGNED NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `body_ar` text COLLATE utf8mb4_general_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `body_en` text COLLATE utf8mb4_general_ci,
  `variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `default_channels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `default_priority` enum('low','normal','high','urgent') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'normal',
  `default_icon` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `action_route` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_executions`
--

CREATE TABLE `order_executions` (
  `id` bigint UNSIGNED NOT NULL,
  `work_order_id` bigint UNSIGNED NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `car_id` bigint UNSIGNED DEFAULT NULL,
  `driver_id` bigint UNSIGNED DEFAULT NULL,
  `execution_date` datetime NOT NULL,
  `departure_time` datetime DEFAULT NULL,
  `arrival_time` datetime DEFAULT NULL,
  `pour_start_time` datetime DEFAULT NULL,
  `pour_end_time` datetime DEFAULT NULL,
  `return_time` datetime DEFAULT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `slump` decimal(5,2) DEFAULT NULL,
  `quality_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `inventory_deducted` tinyint(1) NOT NULL DEFAULT '0',
  `inventory_deducted_at` datetime DEFAULT NULL,
  `inventory_deducted_by` bigint UNSIGNED DEFAULT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `status` enum('scheduled','loading','in_transit','pouring','completed','returned','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED NOT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_histories`
--

CREATE TABLE `order_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `work_order_id` bigint UNSIGNED NOT NULL,
  `action_type` enum('created','status_changed','price_changed','quantity_modified','scheduled','execution_added','inventory_deducted','note_added','approval_requested','approval_given','approval_rejected','cancelled','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `new_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `user_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_histories`
--

INSERT INTO `order_histories` (`id`, `work_order_id`, `action_type`, `field_name`, `old_value`, `new_value`, `user_id`, `user_type`, `description`, `notes`, `metadata`, `created_at`) VALUES
(1, 1, 'created', NULL, NULL, NULL, 62, 'employee', 'تم إنشاء طلب عمل جديد', NULL, NULL, '2026-04-13 17:30:34'),
(2, 2, 'created', NULL, NULL, NULL, 62, 'employee', 'تم إنشاء طلب عمل جديد', NULL, NULL, '2026-04-13 19:35:59'),
(3, 3, 'created', NULL, NULL, NULL, 77, 'employee', 'تم إنشاء طلب عمل جديد', NULL, NULL, '2026-04-20 07:08:13'),
(4, 4, 'created', NULL, NULL, NULL, 78, 'employee', 'تم إنشاء طلب عمل جديد', NULL, NULL, '2026-04-20 10:58:40');

-- --------------------------------------------------------

--
-- Table structure for table `order_negotiations`
--

CREATE TABLE `order_negotiations` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `stage` enum('initial_request','branch_offer','requester_accept','requester_reject','requester_counter','branch_counter','final_agreement','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_offered` decimal(15,2) DEFAULT NULL,
  `discount_offered` decimal(10,2) DEFAULT NULL,
  `suggested_date` date DEFAULT NULL,
  `suggested_time` time DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `action_by` bigint UNSIGNED NOT NULL,
  `action_by_type` enum('branch_employee','contractor','agent','customer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_price_changes`
--

CREATE TABLE `order_price_changes` (
  `id` bigint UNSIGNED NOT NULL,
  `work_order_id` bigint UNSIGNED NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `new_price` decimal(10,2) NOT NULL,
  `change_amount` decimal(10,2) NOT NULL,
  `change_percentage` decimal(5,2) NOT NULL,
  `change_type` enum('initial','customer_request','quantity_change','market_change','discount','surcharge','correction','management','final_approval') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `changed_by` bigint UNSIGNED NOT NULL,
  `changed_by_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requires_approval` tinyint(1) NOT NULL DEFAULT '0',
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approval_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `accounting_impact` tinyint(1) NOT NULL DEFAULT '0',
  `accounting_processed` tinyint(1) NOT NULL DEFAULT '0',
  `accounting_processed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_stages`
--

CREATE TABLE `order_stages` (
  `id` bigint UNSIGNED NOT NULL,
  `work_order_id` bigint UNSIGNED NOT NULL,
  `stage` enum('new','under_review','waiting_customer','approved','rejected','scheduled','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_stages`
--

INSERT INTO `order_stages` (`id`, `work_order_id`, `stage`, `user_id`, `notes`, `metadata`, `created_at`) VALUES
(1, 1, 'in_progress', 62, 'تم إنشاء الطلب', NULL, '2026-04-13 17:30:34'),
(2, 2, 'in_progress', 62, 'تم إنشاء الطلب', NULL, '2026-04-13 19:35:59'),
(3, 3, 'in_progress', 77, 'تم إنشاء الطلب', NULL, '2026-04-20 07:08:13'),
(4, 4, 'new', 78, 'تم إنشاء الطلب', NULL, '2026-04-20 10:58:40');

-- --------------------------------------------------------

--
-- Table structure for table `order_timeline`
--

CREATE TABLE `order_timeline` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `event_type` enum('created','updated','status_changed','branch_reviewed','offer_sent','offer_accepted','offer_rejected','counter_offer','final_approval','assigned','dispatched','completed','cancelled','note_added','attachment_added') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_by_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by_type` enum('system','employee','contractor','agent','customer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'system',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_seo_settings`
--

CREATE TABLE `page_seo_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `page_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `og_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `canonical_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `schema_markup` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sitemap_priority` decimal(2,1) NOT NULL DEFAULT '0.8',
  `sitemap_changefreq` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_seo_settings`
--

INSERT INTO `page_seo_settings` (`id`, `page_key`, `page_title`, `meta_title`, `meta_description`, `meta_keywords`, `og_title`, `og_description`, `canonical_url`, `schema_markup`, `sitemap_priority`, `sitemap_changefreq`, `created_at`, `updated_at`) VALUES
(1, 'home', 'ConcreteERP | نظام إدارة مصانع الخرسانة الجاهزة', 'ConcreteERP | نظام إدارة مصانع الخرسانة الجاهزة', 'نظام ConcreteERP هو الحل المتكامل لإدارة مصانع الخرسانة الجاهزة - إدارة الطلبات، أسطول الميكسر، المخزون، المقاولين، والمحاسبة في منصة واحدة.', 'نظام إدارة مصانع الخرسانة الجاهزة، برنامج ERP للخرسانة، نظام مقاولات وخرسانة، إدارة أسطول الميكسر، نظام محاسبة مصنع خرسانة', 'ConcreteERP | نظام إدارة مصانع الخرسانة الجاهزة', 'نظام ConcreteERP هو الحل المتكامل لإدارة مصانع الخرسانة الجاهزة - إدارة الطلبات، أسطول الميكسر، المخزون، المقاولين، والمحاسبة.', 'https://concreteerp.app/', '{\n    \"@context\": \"https:\\/\\/schema.org\",\n    \"@type\": \"SoftwareApplication\",\n    \"name\": \"ConcreteERP\",\n    \"applicationCategory\": \"BusinessApplication\",\n    \"operatingSystem\": \"Web\",\n    \"description\": \"نظام إدارة متكامل لمصانع الخرسانة الجاهزة\",\n    \"inLanguage\": \"ar\",\n    \"url\": \"https:\\/\\/concreteerp.app\",\n    \"offers\": {\n        \"@type\": \"Offer\",\n        \"price\": \"0\",\n        \"priceCurrency\": \"IQD\"\n    }\n}', 1.0, 'monthly', '2026-04-20 11:35:26', '2026-04-20 11:35:26'),
(2, 'system-benefits', 'فوائد النظام | ConcreteERP — برنامج ERP للخرسانة', 'فوائد النظام | ConcreteERP — برنامج ERP للخرسانة', 'اكتشف فوائد نظام ConcreteERP لإدارة مصانع الخرسانة الجاهزة: تقليل الهدر، تحسين الكفاءة، تتبع الشحنات، وإدارة المقاولين بسهولة.', 'فوائد نظام ERP، برنامج إدارة الخرسانة، تحسين كفاءة المصنع، إدارة أسطول الميكسر، نظام مقاولات وخرسانة', 'فوائد النظام | ConcreteERP — برنامج ERP للخرسانة', 'اكتشف فوائد نظام ConcreteERP لإدارة مصانع الخرسانة الجاهزة: تقليل الهدر، تحسين الكفاءة، وإدارة المقاولين.', 'https://concreteerp.app/system-benefits', NULL, 0.8, 'monthly', '2026-04-20 11:35:26', '2026-04-20 11:35:26'),
(3, 'features', 'مميزات النظام | ConcreteERP — نظام مقاولات وخرسانة', 'مميزات النظام | ConcreteERP — نظام مقاولات وخرسانة', 'استكشف مميزات ConcreteERP: إدارة الطلبات، تتبع الشحنات، إدارة المخزون، الخلطات الخرسانية، المقاولين، الحضور، والتقارير المالية.', 'مميزات نظام الخرسانة، إدارة الطلبات، تتبع الشحنات، إدارة المخزون، نظام محاسبة مصنع خرسانة، إدارة أسطول الميكسر', 'مميزات النظام | ConcreteERP — نظام مقاولات وخرسانة', 'استكشف مميزات ConcreteERP: إدارة الطلبات، تتبع الشحنات، إدارة المخزون، والتقارير المالية في منصة واحدة.', 'https://concreteerp.app/features', NULL, 0.8, 'monthly', '2026-04-20 11:35:26', '2026-04-20 11:35:26'),
(4, 'about', 'عن النظام | ConcreteERP — إدارة مصانع الخرسانة', 'عن النظام | ConcreteERP — إدارة مصانع الخرسانة', 'تعرف على فلسفة ConcreteERP وأهدافه في دعم مصانع الخرسانة الجاهزة ورقمنة العمليات التشغيلية والمالية في العراق والشرق الأوسط.', 'عن ConcreteERP، نظام إدارة مصانع الخرسانة، برنامج ERP للخرسانة، رقمنة مصانع الخرسانة، نظام مقاولات وخرسانة', 'عن النظام | ConcreteERP — إدارة مصانع الخرسانة', 'تعرف على فلسفة ConcreteERP وأهدافه في دعم مصانع الخرسانة الجاهزة ورقمنة العمليات التشغيلية والمالية.', 'https://concreteerp.app/about', NULL, 0.8, 'monthly', '2026-04-20 11:35:26', '2026-04-20 11:35:26'),
(5, 'contact', 'تواصل معنا | ConcreteERP — نظام الخرسانة الجاهزة', 'تواصل معنا | ConcreteERP — نظام الخرسانة الجاهزة', 'تواصل مع فريق ConcreteERP للاستفسار عن الاشتراك أو الدعم الفني أو طلب عرض توضيحي لنظام إدارة مصانع الخرسانة الجاهزة.', 'تواصل ConcreteERP، دعم فني، طلب عرض، اشتراك نظام الخرسانة، نظام إدارة مصانع الخرسانة الجاهزة', 'تواصل معنا | ConcreteERP — نظام الخرسانة الجاهزة', 'تواصل مع فريق ConcreteERP للاستفسار عن الاشتراك أو الدعم الفني أو طلب عرض توضيحي.', 'https://concreteerp.app/contact', '{\n    \"@context\": \"https:\\/\\/schema.org\",\n    \"@type\": \"LocalBusiness\",\n    \"name\": \"ConcreteERP\",\n    \"email\": \"ninesoftware1@gmail.com\",\n    \"telephone\": \"+9647713863214\",\n    \"url\": \"https:\\/\\/concreteerp.app\\/contact\"\n}', 0.8, 'monthly', '2026-04-20 11:35:26', '2026-04-20 11:35:26');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL,
  `transaction_id` bigint UNSIGNED DEFAULT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `payment_type` enum('full','partial','advance') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'full',
  `direction` enum('in','out') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in',
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `payment_date` date NOT NULL,
  `payment_method` enum('cash','check','bank_transfer','credit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `receipt_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_date` date DEFAULT NULL,
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `received_by` bigint UNSIGNED DEFAULT NULL,
  `paid_to` bigint UNSIGNED DEFAULT NULL,
  `recorded_by` bigint UNSIGNED NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_cards`
--

CREATE TABLE `payment_cards` (
  `id` bigint UNSIGNED NOT NULL,
  `card_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `holder_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_number_masked` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `expiry_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_cards`
--

INSERT INTO `payment_cards` (`id`, `card_type`, `card_name`, `holder_name`, `card_number`, `card_number_masked`, `opening_balance`, `current_balance`, `expiry_date`, `is_active`, `notes`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'visa', 'زين كاش الرئيسي', 'ناين سوفت', '8788787888787878', '************7878', 0.00, 540000.00, '2026-07-01', 1, NULL, 1, '2026-04-13 14:27:26', '2026-04-20 04:06:35', NULL),
(2, 'visa', 'فيزا الشركة  الرئيسية', 'ناين سوفت', '11179798718787/', '***********787/', 0.00, 0.00, '2026-12-01', 1, NULL, 1, '2026-04-17 16:03:00', '2026-04-17 16:03:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_card_transactions`
--

CREATE TABLE `payment_card_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `payment_card_id` bigint UNSIGNED NOT NULL,
  `transaction_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('deposit','withdrawal') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_before` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `reference_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `company_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_card_transactions`
--

INSERT INTO `payment_card_transactions` (`id`, `payment_card_id`, `transaction_number`, `type`, `amount`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `company_code`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(4, 1, 'PCT-2026-000004', 'withdrawal', 10000.00, 390000.00, 380000.00, 'manual', NULL, NULL, 'سحب مبلغ', 1, '2026-04-17 16:01:00', '2026-04-17 16:01:00'),
(5, 1, 'PCT-2026-000005', 'deposit', 10000.00, 380000.00, 390000.00, 'manual', NULL, NULL, 'ىةىت', 1, '2026-04-17 16:01:32', '2026-04-17 16:01:32'),
(6, 1, 'PCT-2026-000006', 'deposit', 150000.00, 390000.00, 540000.00, 'subscription', 3, 'W4bFD', 'دفع اشتراك شركة W4bFD', 1, '2026-04-20 04:06:35', '2026-04-20 04:06:35');

-- --------------------------------------------------------

--
-- Table structure for table `payment_receipts`
--

CREATE TABLE `payment_receipts` (
  `id` bigint UNSIGNED NOT NULL,
  `receipt_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `transaction_id` bigint UNSIGNED DEFAULT NULL,
  `payer_type` enum('contractor','customer','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payer_id` bigint UNSIGNED DEFAULT NULL,
  `payer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payer_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `amount_in_default` decimal(15,2) DEFAULT NULL,
  `amount_in_words` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` enum('cash','bank_transfer','check','card','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_date` date DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `related_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','bounced') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'confirmed',
  `cancelled_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `received_by` bigint UNSIGNED NOT NULL,
  `received_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_vouchers`
--

CREATE TABLE `payment_vouchers` (
  `id` bigint UNSIGNED NOT NULL,
  `voucher_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `transaction_id` bigint UNSIGNED DEFAULT NULL,
  `payee_type` enum('supplier','contractor','employee','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payee_id` bigint UNSIGNED DEFAULT NULL,
  `payee_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `amount_in_default` decimal(15,2) DEFAULT NULL,
  `amount_in_words` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` enum('cash','bank_transfer','check','card','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_date` date DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `related_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` bigint UNSIGNED DEFAULT NULL,
  `requires_approval` tinyint(1) NOT NULL DEFAULT '0',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `status` enum('draft','pending_approval','approved','paid','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `cancelled_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `paid_by` bigint UNSIGNED DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `payroll_month` tinyint NOT NULL,
  `payroll_year` year NOT NULL,
  `basic_salary` decimal(15,2) NOT NULL,
  `allowances_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `allowances_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `bonuses_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `bonuses_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `overtime_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `overtime_hours` decimal(5,2) NOT NULL DEFAULT '0.00',
  `deductions_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `deductions_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `advances_deducted` decimal(15,2) NOT NULL DEFAULT '0.00',
  `advances_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `absence_deduction` decimal(15,2) NOT NULL DEFAULT '0.00',
  `absence_days` int NOT NULL DEFAULT '0',
  `insurance_deduction` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_deduction` decimal(15,2) NOT NULL DEFAULT '0.00',
  `gross_salary` decimal(15,2) NOT NULL,
  `net_salary` decimal(15,2) NOT NULL,
  `status` enum('draft','approved','paid','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `payment_method` enum('cash','bank_transfer','check') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `paid_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pricing_categories`
--

CREATE TABLE `pricing_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'اسم الفئة السعرية',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'وصف الفئة',
  `sort_order` int NOT NULL DEFAULT '0' COMMENT 'ترتيب العرض',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'حالة الفئة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pricing_categories`
--

INSERT INTO `pricing_categories` (`id`, `name`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'العام', NULL, 1, 1, '2026-04-13 14:46:15', '2026-04-13 14:46:15'),
(2, 'خاص', NULL, 2, 1, '2026-04-13 14:48:07', '2026-04-13 14:48:07'),
(3, 'حكومي', NULL, 3, 1, '2026-04-13 14:48:23', '2026-04-13 14:48:23');

-- --------------------------------------------------------

--
-- Table structure for table `public_contact_channels`
--

CREATE TABLE `public_contact_channels` (
  `id` bigint UNSIGNED NOT NULL,
  `channel_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `public_contact_settings`
--

CREATE TABLE `public_contact_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'عنوان يظهر في ترويسة الصفحة',
  `intro_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'النص التمهيدي',
  `hint_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'نص تلميح أسفل وسائل التواصل',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telegram` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `public_contact_settings`
--

INSERT INTO `public_contact_settings` (`id`, `title`, `intro_text`, `hint_text`, `email`, `whatsapp`, `telegram`, `facebook`, `instagram`, `phone`, `created_at`, `updated_at`) VALUES
(1, 'مرحبا بكم', 'يمكنكم التواصل مع فريق ConcreteERP عبر القنوات أدناه للحصول على إجابة بخصوص خطط الاستخدام، تفعيل الشركة، أو أي استفسار تقني عام قبل البدء.', 'يُفضّل ذكر اسم المصنع أو الشركة عند التواصل لتسريع خدمتكم.', 'ninesoftware1@gmail.com', '07713863214', 'nine9soft', NULL, NULL, NULL, '2026-03-24 21:04:35', '2026-03-24 21:25:42');

-- --------------------------------------------------------

--
-- Table structure for table `public_display_blocks`
--

CREATE TABLE `public_display_blocks` (
  `id` bigint UNSIGNED NOT NULL,
  `page_key` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `block_type` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `list_items` json DEFAULT NULL,
  `icon_fa` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `public_display_blocks`
--

INSERT INTO `public_display_blocks` (`id`, `page_key`, `block_type`, `title`, `body`, `list_items`, `icon_fa`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'landing', 'sidebar_note', 'صورة اليوم والعمليات', 'البرنامج يساعد من يدير مصنع خرسانة جاهزة على أن تكون «صورة اليوم» واضحة لديه: ماذا يُنفَّذ، ماذا عُهِد به للعميل، وأين تتجه الأرقام — دون أن تضيع التفاصيل بين اتصالات وهواتف وملفات غير مرتبطة.', NULL, NULL, 1, 1, '2026-03-24 20:26:13', '2026-03-24 21:48:07'),
(2, 'landing', 'plain', 'الضغط اليومي وقرار سريع', 'العمل في هذا القطاع سريع وضاغط؛ القرار غالباً يُتخذ قبل منتصف النهار، والاعتماد على ذاكرة فردية أو نسخ متعددة من البيانات يولّد توتراً غير ضرورياً. ConcreteERP لا يقدم نفسه كـ«سحر يحل كل شيء»، بل كمنصة ترتّب البيئة التشغيلية: معلومة واحدة يُبنى عليها العمل، وفرق يعرف كل واحدٍ دوره ضمن إطار موحّد.', NULL, NULL, 2, 1, '2026-03-24 20:26:13', '2026-03-24 21:47:08'),
(3, 'landing', 'plain', 'التركيز على الجودة والنمو', 'بذلك يصبح التركيز أقرب إلى الجودة والتسليم والعلاقة مع العميل، وأقلّ على إعادة السؤال نفسه في القاعات والمكاتب. إن كان مصنعك ينمو أو يضيف فروعاً، فالفائدة العامة تبقى: تقليل الفوضى الإدارية والاستعداد لتشغيل أوسع دون أن ينهار «محور المعلومة» في الوسط.', NULL, NULL, 3, 1, '2026-03-24 20:26:13', '2026-03-24 21:47:08'),
(4, 'landing', 'highlight', 'باختصار: كيف يساعدك على الأرض؟', NULL, '[\"أقل تخمين: قرارات مبنية على وضع العمل المتفق عليه في النظام، لا على روايات متفرقة.\", \"تنسيق أوضح بين الفريق: الإدارة والميدان يطلعان على نفس السياق — دون أن تعني ذلك ركوباً تقنياً معقّداً للجميع.\", \"استمرارية: ما يُسجَّل اليوم يبقى مرجعاً غداً، فيسهّل المراجعة والمتابعة والتدقيق عند الحاجة.\"]', NULL, 4, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(5, 'landing', 'bridge', 'استكشف بقية الموقع', 'لمزيد من التفاصيل عن الفوائد والمميزات وغيرها، استخدم القائمة في أعلى الصفحة — هذه المساحة مخصصة للفكرة العامة عن البرنامج فقط.', NULL, NULL, 5, 1, '2026-03-24 20:26:13', '2026-03-24 21:47:08'),
(6, 'system_benefits', 'plain', 'رؤية موحّدة لدورة العمل', 'يوفر ConcreteERP رؤية كاملة لدورة العمل اليومية في مصنع الخرسانة الجاهزة، ويساعد الإدارة على التحكم في الطلبات، التسعير، الأسطول، والنتائج المالية من شاشة واحدة.', NULL, NULL, 1, 1, '2026-03-24 20:26:13', '2026-03-24 21:47:08'),
(7, 'system_benefits', 'kpi', 'إدارة الطلبات', 'من أول اتصال حتى الفاتورة والتحصيل', NULL, NULL, 2, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(8, 'system_benefits', 'kpi', 'الشحنات والأسطول', 'تتبع التنفيذ والرحلات والجاهزية', NULL, NULL, 3, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(9, 'system_benefits', 'kpi', 'تقارير فورية', 'لوحات مؤشرات تساعد القرار', NULL, NULL, 4, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(10, 'system_benefits', 'sidebar', 'هل لديك استفسار؟', 'يمكنك مراسلتنا عبر واتساب للاطلاع على خطط الاشتراك أو الإجابة عن أسئلتك التشغيلية.', NULL, NULL, 5, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(11, 'system_benefits', 'sidebar_note', 'تنبيه الوصول والصلاحيات', 'الوصول للبيانات يتطلب تسجيل دخول لضمان الأمان والصلاحيات.', NULL, NULL, 6, 1, '2026-03-24 20:26:13', '2026-03-24 21:47:08'),
(12, 'system_benefits', 'card', '١. إدارة الطلبات من أول اتصال حتى الفاتورة', NULL, '[\"تسجيل طلبات العملاء مع كل التفاصيل (الموقع، الكميات، أوقات الصب).\", \"متابعة حالة الطلب: جديد، قيد التفاوض، معتمد، قيد التنفيذ، مكتمل.\", \"ربط الطلبات بالفواتير، المدفوعات، والتقارير المالية.\"]', 'fa-file-invoice', 7, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(13, 'system_benefits', 'card', '٢. تحكم كامل في الأسطول والشحنات', NULL, '[\"إدارة سيارات النقل والمضخات وربطها بأوامر العمل.\", \"متابعة حالة كل شحنة وزمن خروجها ووصولها ومكانها.\", \"تقارير عن استغلال الأسطول، الأعطال، والصيانة.\"]', 'fa-truck', 8, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(14, 'system_benefits', 'card', '٣. تسعير مرن وشفاف', NULL, '[\"تعريف فئات سعرية مختلفة حسب العميل أو المشروع أو المنطقة.\", \"إدارة خصومات واتفاقيات خاصة مع المقاولين والعملاء الرئيسيين.\", \"تقارير ربحية على مستوى العميل أو المشروع أو الفرع.\"]', 'fa-tags', 9, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(15, 'system_benefits', 'card', '٤. صلاحيات واضحة لكل نوع مستخدم', NULL, '[\"صلاحيات مخصصة للسوبر أدمن، إدارة الشركة، مدير الفرع، الموظفين، والسائقين.\", \"سجل نشاط للمستخدمين وعمليات الدخول والتعديلات المهمة.\", \"التحكم في تفعيل/إيقاف الشركات والمستخدمين حسب حالة الاشتراك.\"]', 'fa-user-shield', 10, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(16, 'system_benefits', 'card', '٥. تقارير ولوحات تحكم للإدارة العليا', NULL, '[\"تقارير مالية يومية وشهرية عن المبيعات، المقبوضات، والذمم.\", \"إحصائيات عن الطلبات، العملاء، الفروع، والأسطول في لوحة واحدة.\", \"مؤشرات أداء رئيسية تساعد في اتخاذ القرار بسرعة.\"]', 'fa-chart-line', 11, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(17, 'system_benefits', 'card', '٦. دعم فني ومتابعة تشغيلية', NULL, '[\"نظام تذاكر دعم لتوثيق المشاكل والطلبات من المستخدمين.\", \"سجل للأخطاء وحالة النظام لمتابعة الاستقرار والأداء.\", \"متابعة حالة اشتراك كل شركة وتنبيهات قبل انتهاء الاشتراك.\"]', 'fa-headset', 12, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(18, 'system_benefits', 'footer_note', 'تصميم مخصص لمصانع الخرسانة', 'تم تصميم ConcreteERP خصيصًا لمصانع الخرسانة الجاهزة، ليتوافق مع دورة العمل الحقيقية في الميدان وليس كنظام محاسبي عام فقط.', NULL, NULL, 13, 1, '2026-03-24 20:26:13', '2026-03-24 21:47:08'),
(19, 'features', 'plain', 'منصة تشغيلية وتقارير', 'يجمع ConcreteERP بين وحدات تشغيلية متخصصة (طلبات، شحنات، مخزون، خلطات، مقاولين) وطبقة تقارير ومحاسبة تساعد الإدارة على رؤية الصورة الكاملة دون الاعتماد على جداول متفرقة أو برامج منفصلة. سواء كنت تدير فرعاً واحداً أو شبكة فروع، يبقى النظام نقطة مرجعية واحدة للبيانات والصلاحيات.', NULL, NULL, 1, 1, '2026-03-24 20:26:13', '2026-03-24 21:47:08'),
(20, 'features', 'sidebar', 'جاهز للاستفسار؟', 'تواصل معنا عبر واتساب لمعرفة خطط الاشتراك أو طلب عرض.', NULL, NULL, 2, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(21, 'features', 'card', 'إدارة الطلبات وأوامر العمل', 'تسجيل الطلبات مع تفاصيل الموقع والكميات والأوقات، ومتابعة مراحل التنفيذ حتى الإغلاق المالي.', '[\"ربط الطلبات بالمقاولين والفروع.\", \"تتبع حالات الطلب والموافقات.\", \"تجهيز أوامر عمل للميدان والإنتاج.\"]', 'fa-clipboard-list', 3, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(22, 'features', 'card', 'الشحنات والأسطول', 'تنسيق رحلات الخلاطات والمضخات مع متابعة حالة كل شحنة وزمن التنفيذ.', '[\"ربط السائقين والمركبات بأوامر العمل.\", \"تسجيل المغادرة والوصول وأحداث التنفيذ.\", \"دعم تقارير استغلال الأسطول.\"]', 'fa-truck-moving', 4, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(23, 'features', 'card', 'المخزون والمواد', 'متابعة المواد الرئيسية والمعدات والوحدات مع ربطها بالفروع والموردين عند الحاجة.', '[\"تعريف وحدات القياس والمستودعات.\", \"تتبع حركة المواد حسب الفرع.\", \"دعم قرارات الشراء والتغطية.\"]', 'fa-warehouse', 5, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(24, 'features', 'card', 'الخلطات والمواد الكيميائية', 'إدارة تركيبات الخرسانة والإضافات بما يتوافق مع سياسة الجودة في المصنع.', '[\"قوائم خلطات لكل شركة أو فرع.\", \"ربط التركيبات بطلبات العملاء.\", \"تنظيم المواد الكيميانية المرتبطة بالإنتاج.\"]', 'fa-flask', 6, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(25, 'features', 'card', 'المقاولون والعملاء', 'ملفات للمقاولين مع تتبع الطلبات والمديونية والتعامل مع طلبات الاعتماد.', '[\"بوابة أو مسارات لطلبات المقاولين.\", \"متابعة الاعتمادات والفوترة.\", \"تقارير ذمم وتسويات عند التفعيل.\"]', 'fa-handshake', 7, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(26, 'features', 'card', 'الحضور والموارد البشرية', 'دعم عمليات الحضور والرواتب والسلف ضمن نفس البيئة التشغيلية للشركة.', '[\"تقارير حضور يمكن ربطها بالفروع.\", \"مسارات موافقات وطلبات السلف.\", \"تخفيف العبء عن الإدارة المالية اليدوية.\"]', 'fa-user-clock', 8, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(27, 'features', 'plain', 'التقارير ولوحات المؤشرات', 'لوحات ومؤشرات تساعد على مراقبة الإنتاج والمبيعات والتشغيل اليومي.', '[\"تقارير مالية وتشغيلية حسب الصلاحيات.\", \"دعم قرارات الإدارة العليا.\", \"تقليل الاعتماد على ملفات Excel المتفرقة.\"]', 'fa-chart-pie', 9, 1, '2026-03-24 20:26:13', '2026-03-26 10:06:43'),
(28, 'features', 'card', 'الصلاحيات والأمان', 'نموذج صلاحيات متعدد المستويات يناسب الشركة والفرع والميدان.', '[\"فصل واضح بين أدوار المستخدمين.\", \"حماية البيانات الحساسة للشركة.\", \"قابلية التوسع مع نمو الفروع.\"]', 'fa-shield-halved', 10, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(29, 'features', 'long_text', 'لماذا تهم «المميزات المتكاملة» في مصنع الخرسانة؟', 'مصانع الخرسانة الجاهزة تعتمد على تزامن دقيق بين قسم المبيعات، التخطيط، الإنتاج، والتوزيع.\nأي تأخير في المعلومات—سواء في حالة الشحنة أو في الرصيد المخزني أو في تسعيرة العميل—يتحول مباشرة إلى تكلفة إضافية أو شكوى من المقاول. ConcreteERP يقلل هذه الفجوات بربط العمليات داخل نظام واحد، بحيث يكون الطلب هو المصدر نفسه للبيانات التي يراها مدير الفرع، المحاسب، ومشرف الأسطول.\n\nكما أن النظام مصمم لبيئة الويب، ما يسهّل على الفرق العمل من الموقع أو المكتب دون الحاجة لتثبيتات معقدة على كل جهاز، مع الحفاظ على تجربة عربية كاملة الاتجاه (RTL) تناسب فرق العمل المحلية.\n\nلمزيد من التفاصيل التشغيلية والتواصل استخدم الروابط في القائمة العلوية (فوائد النظام، تواصل معنا).', NULL, NULL, 11, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(30, 'features', 'note', 'ملاحظة عن التفعيل والخطط', 'تختلف تفعيلات الوحدات حسب خطة الاشتراك وإعدادات شركتك؛ يمكن لفريق الدعم توضيح ما يناسب مصنعك.', NULL, NULL, 12, 1, '2026-03-24 20:26:13', '2026-03-24 21:47:08'),
(31, 'about', 'section', 'رؤيتنا', 'نؤمن بأن مصانع الخرسانة الجاهزة تحتاج إلى أنظمة تعكس طبيعة عملها: طلبات متكررة، جداول زمنية ضيقة، أساطيل متحركة، وتفاعل يومي مع المقاولين والمشاريع.\nConcreteERP وُلد من هذه الحقيقة—ليس كنسخة عامة من برامج المحاسبة، بل كمنصة تربط ما يحدث في الميدان بما يجب أن يظهر في التقارير والذمم.', NULL, NULL, 2, 1, '2026-03-24 20:26:13', '2026-03-24 22:16:25'),
(32, 'about', 'section', 'ما الذي يميز النظام؟', 'يوفّر النظام لغة عربية كاملة، وواجهات تناسب فرق التشغيل في المصنع والفرع، مع هيكل صلاحيات يحترم فصل مهام الإدارة عن الميدان.\nكما يساعد على توحيد بيانات الطلبات والشحنات بحيث لا تضيع المعلومات بين الهاتف، ورق الحقل، وجداول منفصلة.\n\nعلى المدى الطويل، يهدف ConcreteERP إلى تقليل التسرب المالي الناتج عن التأخير في التحديث، وتحسين قابلية التدقيق عبر سجلات أوضح للطلبات والمدفوعات والتعامل مع العملاء.', NULL, NULL, 5, 1, '2026-03-24 20:26:13', '2026-03-24 22:16:12'),
(33, 'about', 'section', 'قيمنا في المنتج', NULL, '[\"الوضوح: شاشات وبيانات تفهمها الإدارة دون تعقيد غير ضروري.\", \"المرونة: دعم شركات متعددة الفروع مع إمكانية ضبط الإعدادات حسب السياسة.\", \"الاستمرارية: تحديثات ودعم يساعدان المصنع على مواكبة نمو الحجم دون إعادة بناء كاملة كل سنة.\", \"القرب من العميل: قنوات تواصل للاستفسار عن الاشتراك والتشغيل.\"]', NULL, 1, 1, '2026-03-24 20:26:13', '2026-03-24 22:16:25'),
(34, 'about', 'card', 'لمن هذا النظام؟', 'يخدم ConcreteERP شركات الخرسانة الجاهزة التي تدير طلبات يومية، وتعتمد على أسطول نقل ومضخات، وتريد ربط الإنتاج والتوزيع بالمحاسبة دون فقدان التفاصيل. إن كنت تمثل إدارة مصنع أو فرعاً إقليمياً أو مقاولاً يتعامل مع مصنع متعدد الطلبات، فإن تجربة موحّدة للبيانات تبقى حجر الأساس لاتخاذ قرار أسرع وأدق.', NULL, NULL, 3, 1, '2026-03-24 20:26:13', '2026-03-24 22:16:12'),
(35, 'about', 'note', 'التطوير المستمر للمنتج', 'ConcreteERP منتج مستمر التطوير؛ قد تتغير بعض الشاشات والوحدات مع إصدارات أحدث مع الحفاظ على نفس الفلسفة التشغيلية.', NULL, NULL, 4, 1, '2026-03-24 20:26:13', '2026-03-24 22:16:12'),
(36, 'contact', 'welcome', NULL, 'يمكنكم التواصل مع فريق ConcreteERP عبر القنوات أدناه للحصول على إجابة بخصوص خطط الاستخدام، تفعيل الشركة، أو أي استفسار تقني عام قبل البدء.', NULL, NULL, 1, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13'),
(37, 'contact', 'hint', NULL, 'يُفضّل ذكر اسم المصنع أو الشركة عند التواصل لتسريع خدمتكم.', NULL, NULL, 2, 1, '2026-03-24 20:26:13', '2026-03-24 20:26:13');

-- --------------------------------------------------------

--
-- Table structure for table `public_display_videos`
--

CREATE TABLE `public_display_videos` (
  `id` bigint UNSIGNED NOT NULL,
  `page_key` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `youtube_url` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `public_display_videos`
--

INSERT INTO `public_display_videos` (`id`, `page_key`, `youtube_url`, `title`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(6, 'landing', 'DqRbU_iVH8E', 'بيليلي', 1, 1, '2026-03-26 13:00:19', '2026-03-26 13:00:19');

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` bigint UNSIGNED NOT NULL,
  `receipt_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `receipt_type` enum('payment_in','payment_out') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'payment_in',
  `account_id` bigint UNSIGNED DEFAULT NULL,
  `transaction_id` bigint UNSIGNED DEFAULT NULL,
  `invoice_id` bigint UNSIGNED DEFAULT NULL,
  `company_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `party_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `amount_in_words` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` enum('cash','bank_transfer','check','card','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `receipt_date` date NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `is_printed` tinyint(1) NOT NULL DEFAULT '0',
  `printed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `request_status`
--

CREATE TABLE `request_status` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `request_status`
--

INSERT INTO `request_status` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(1, 'طلب جديد', 'new', '2025-11-22 04:33:40', '2025-11-22 04:33:40'),
(2, 'تم القبول', 'approved', '2025-11-22 04:33:40', '2025-11-22 04:33:40'),
(3, 'قيد العمل', 'in_progress', '2025-11-22 04:33:40', '2025-11-22 04:33:40'),
(4, 'تم الإنجاز', 'completed', '2025-11-22 04:33:40', '2025-11-22 04:33:40'),
(5, 'تم الرفض', 'rejected', '2025-11-22 04:33:40', '2025-11-22 04:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `code`, `name`, `description`, `is_system`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'SA', 'سوبر أدمن', 'صلاحيات كاملة على النظام', 1, 1, '2025-12-18 01:23:51', '2025-12-18 01:23:51'),
(2, 'CM', 'مدير شركة', 'إدارة شركة كاملة', 1, 1, '2025-12-18 01:23:51', '2025-12-18 01:23:51'),
(3, 'BM', 'مدير فرع', 'إدارة فرع واحد', 1, 1, '2025-12-18 01:23:51', '2025-12-18 01:23:51'),
(4, 'cont', 'مقاول', 'حساب مقاول', 1, 1, '2025-12-18 01:23:51', '2025-12-18 01:23:51'),
(5, 'delegate', 'مندوب', 'حساب مندوب', 1, 1, '2025-12-18 01:23:51', '2025-12-18 01:23:51'),
(6, 'DRIVER', 'تجريبي', 'تجربه ادخال بيانات', 0, 1, '2025-12-18 01:24:03', '2025-12-18 01:24:03');

-- --------------------------------------------------------

--
-- Table structure for table `salary_adjustments`
--

CREATE TABLE `salary_adjustments` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `adjustment_type` enum('increase','decrease') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_salary` decimal(15,2) NOT NULL,
  `new_salary` decimal(15,2) NOT NULL,
  `difference` decimal(15,2) NOT NULL,
  `effective_date` date NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `approved_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seo_settings`
--

CREATE TABLE `seo_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `site_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ConcreteERP',
  `meta_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `og_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `og_image` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'website',
  `twitter_card` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'summary_large_image',
  `twitter_site` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canonical_domain` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `robots` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'index, follow',
  `locale` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ar_IQ',
  `locale_alternate` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_meta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `structured_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seo_settings`
--

INSERT INTO `seo_settings` (`id`, `site_name`, `meta_title`, `meta_description`, `meta_keywords`, `og_title`, `og_description`, `og_image`, `og_type`, `twitter_card`, `twitter_site`, `canonical_domain`, `robots`, `locale`, `locale_alternate`, `extra_meta`, `structured_data`, `created_at`, `updated_at`) VALUES
(1, 'ConcreteERP - برنامج إدارة مصانع الخرسانة الجاهزة', 'برنامج إدارة مصانع الخرسانة الجاهزة | ConcreteERP', 'ConcreteERP نظام ERP لإدارة مصانع ومحطات الخرسانة الجاهزة: إدارة الطلبات، التسعير، أوامر العمل، الشحنات، الأسطول، المخزون، الفوترة والتقارير التشغيلية والمالية.', 'برنامج إدارة مصانع الخرسانة الجاهزة، نظام ERP للخرسانة الجاهزة، برنامج محاسبة الخرسانة الجاهزة، إدارة محطات الخرسانة، إدارة طلبات الخرسانة، إدارة شحنات الخرسانة، إدارة أسطول الخرسانة، إدارة مخزون الخرسانة، تقارير مصانع الخرسانة، ConcreteERP', 'ConcreteERP | نظام إدارة مصانع الخرسانة الجاهزة', 'حل متكامل لإدارة محطات الخرسانة: الطلبات، العقود، الشحنات، الفوترة، المخزون، الأسطول والتقارير في منصة واحدة.', NULL, 'website', 'summary_large_image', '@ConcreteERP', 'https://concreteerp.app', 'index, follow', 'ar_IQ', 'ar', '<meta name=\\theme-color\\ content=\\#0d9488\\>\r\n<meta name=\\author\\ content=\\ConcreteERP\\>\r\n<meta name=\\application-name\\ content=\\ConcreteERP\\>', '{\r\n    \"@context\": \"https://schema.org\",\r\n    \"@type\": \"SoftwareApplication\",\r\n    \"name\": \"ConcreteERP\",\r\n    \"applicationCategory\": \"BusinessApplication\",\r\n    \"operatingSystem\": \"Web\",\r\n    \"url\": \"https://concreteerp.app\",\r\n    \"description\": \"نظام ERP لإدارة مصانع ومحطات الخرسانة الجاهزة: الطلبات، الشحنات، المخزون، الفوترة والتقارير.\",\r\n    \"offers\": {\r\n        \"@type\": \"Offer\",\r\n        \"price\": \"0\",\r\n        \"priceCurrency\": \"USD\"\r\n    }\r\n}', '2026-03-08 20:03:11', '2026-04-17 16:26:32');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `group`, `description`, `created_at`, `updated_at`) VALUES
(1, 'app_name', 'نضام الكونكريت', 'string', 'general', 'اسم النظام', '2025-12-18 01:39:04', '2025-12-18 01:39:47'),
(2, 'support_email', 'support@niniesoft.com', 'string', 'general', 'البريد الإلكتروني للدعم', '2025-12-18 01:39:04', '2026-03-18 13:15:21'),
(3, 'timezone', 'Asia/Baghdad', 'string', 'general', 'المنطقة الزمنية', '2025-12-18 01:39:04', '2025-12-18 01:39:04'),
(4, 'currency', 'دينار عراقي', 'string', 'general', 'العملة الافتراضية', '2025-12-18 01:39:04', '2025-12-18 01:39:04'),
(5, 'force_https', '1', 'boolean', 'security', 'فرض HTTPS', '2025-12-18 01:39:04', '2026-04-17 16:07:13'),
(6, 'enable_2fa', '0', 'boolean', 'security', 'تفعيل التحقق بخطوتين', '2025-12-18 01:39:04', '2025-12-18 01:39:04'),
(7, 'session_lifetime', '120', 'integer', 'security', 'مدة الجلسة بالدقائق', '2025-12-18 01:39:04', '2025-12-18 01:39:04'),
(8, 'auto_backup_enabled', '1', 'string', 'general', NULL, '2025-12-18 02:21:13', '2025-12-18 03:44:51'),
(9, 'auto_backup_interval', '0.016', 'string', 'general', NULL, '2025-12-18 02:21:13', '2025-12-18 03:00:17'),
(10, 'font_family', 'Almarai', 'string', 'general', NULL, '2026-03-15 21:15:50', '2026-03-29 18:21:24'),
(11, 'font_size', '13', 'string', 'general', NULL, '2026-03-15 21:15:50', '2026-03-15 22:29:18'),
(12, 'font_color_light', '#000000', 'string', 'general', NULL, '2026-03-29 18:54:12', '2026-03-29 18:59:46'),
(13, 'font_color_dark', '#ffffff', 'string', 'general', NULL, '2026-03-29 18:54:12', '2026-03-29 18:59:46');

-- --------------------------------------------------------

--
-- Table structure for table `shift_times`
--

CREATE TABLE `shift_times` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shift_times`
--

INSERT INTO `shift_times` (`id`, `company_code`, `name`, `start_time`, `end_time`, `notes`, `created_at`, `updated_at`) VALUES
(10, 'AMIN', 'الشفت الصباحي', '06:00:00', '14:00:00', NULL, '2026-01-18 17:55:10', '2026-01-18 17:55:10'),
(11, 'AMIN', 'الشفت المسائي', '14:00:00', '22:00:00', NULL, '2026-01-18 17:55:10', '2026-01-18 17:55:10'),
(12, 'fB6zg', 'صباحي', '08:00:00', '15:00:00', NULL, '2026-01-18 18:00:39', '2026-01-18 18:00:39'),
(13, 'fB6zg', 'مسائي', '15:00:00', '21:00:00', NULL, '2026-01-18 18:00:56', '2026-01-18 18:00:56'),
(14, 'b5csK', 'الصباحي', '08:00:00', '15:44:00', NULL, '2026-04-11 08:47:18', '2026-04-11 08:47:18'),
(19, 'W4bFD', 'صباحي', '09:30:00', '17:00:00', NULL, '2026-04-20 05:14:55', '2026-04-20 05:14:55'),
(20, 'W4bFD', 'مسائي', '15:00:00', '21:00:00', NULL, '2026-04-20 05:15:23', '2026-04-20 05:15:23');

-- --------------------------------------------------------

--
-- Table structure for table `shipment_events`
--

CREATE TABLE `shipment_events` (
  `id` bigint UNSIGNED NOT NULL,
  `shipment_id` bigint UNSIGNED NOT NULL,
  `event_type` enum('created','prepared','departed','arrived','work_started','work_ended','returned','cancelled','issue_reported','location_updated') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `recorded_by` bigint UNSIGNED DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscription_history`
--

CREATE TABLE `subscription_history` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subscription_id` bigint UNSIGNED DEFAULT NULL,
  `plan_type` enum('monthly','yearly','percentage','trial','hybrid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `base_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `percentage_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `order_fee_type` enum('percentage','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `fixed_order_fee` decimal(15,2) DEFAULT NULL,
  `orders_limit` int DEFAULT NULL,
  `orders_used` int NOT NULL DEFAULT '0',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `actual_start_date` date DEFAULT NULL,
  `actual_end_date` date DEFAULT NULL,
  `auto_renew` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('active','expired','suspended','cancelled','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `payment_status` enum('pending','paid','partial') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension_days` int NOT NULL DEFAULT '0',
  `extension_deducted` tinyint(1) NOT NULL DEFAULT '0',
  `duration_quantity` int NOT NULL DEFAULT '1',
  `total_days` int DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `action_type` enum('created','renewed','terminated','expired','suspended','extended','payment','additional_user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_history`
--

INSERT INTO `subscription_history` (`id`, `company_code`, `subscription_id`, `plan_type`, `base_fee`, `percentage_rate`, `order_fee_type`, `fixed_order_fee`, `orders_limit`, `orders_used`, `start_date`, `end_date`, `actual_start_date`, `actual_end_date`, `auto_renew`, `status`, `payment_status`, `paid_amount`, `paid_at`, `payment_method`, `payment_reference`, `extension_days`, `extension_deducted`, `duration_quantity`, `total_days`, `notes`, `action_type`, `created_by`, `created_at`, `updated_at`) VALUES
(5, 'W4bFD', 3, 'monthly', 150000.00, 0.00, 'percentage', NULL, NULL, 0, '2026-04-20', '2026-07-19', '2026-04-20', NULL, 0, 'active', 'paid', 150000.00, NULL, NULL, NULL, 0, 0, 3, 90, NULL, 'created', 1, '2026-04-20 04:06:35', '2026-04-20 04:06:35'),
(6, 'W4bFD', 3, 'monthly', 120000.00, 0.00, 'percentage', NULL, NULL, 0, '2026-04-20', '2026-07-19', '2026-04-20', NULL, 0, 'active', 'paid', 0.00, NULL, 'cash', NULL, 0, 0, 1, NULL, 'زيادة عدد المستخدمين من 5 إلى 8 (4 شهر متبقٍ) - التكلفة: 120,000 دينار', 'additional_user', 1, '2026-04-20 10:54:52', '2026-04-20 10:54:52'),
(7, 'W4bFD', 3, 'monthly', 120000.00, 0.00, 'percentage', NULL, NULL, 0, '2026-04-20', '2026-07-19', '2026-04-20', NULL, 0, 'active', 'paid', 0.00, NULL, 'cash', NULL, 0, 0, 1, NULL, 'زيادة عدد المستخدمين من 8 إلى 11 (4 شهر متبقٍ) - التكلفة: 120,000 دينار', 'additional_user', 1, '2026-04-20 10:54:52', '2026-04-20 10:54:52');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_invoices`
--

CREATE TABLE `subscription_invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subscription_id` bigint UNSIGNED DEFAULT NULL,
  `invoice_type` enum('subscription','orders_percentage','renewal','additional_user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'subscription',
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `users_count` int NOT NULL DEFAULT '1',
  `price_per_user` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `orders_count` int DEFAULT NULL,
  `orders_total_value` decimal(12,2) DEFAULT NULL,
  `percentage_rate` decimal(5,2) DEFAULT NULL,
  `payment_status` enum('pending','paid','partial','overdue') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `due_date` date DEFAULT NULL,
  `paid_at` date DEFAULT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_invoices`
--

INSERT INTO `subscription_invoices` (`id`, `invoice_number`, `company_code`, `subscription_id`, `invoice_type`, `period_start`, `period_end`, `users_count`, `price_per_user`, `subtotal`, `discount`, `total_amount`, `orders_count`, `orders_total_value`, `percentage_rate`, `payment_status`, `paid_amount`, `due_date`, `paid_at`, `payment_method`, `payment_reference`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(5, 'INV-202604-0001', 'W4bFD', 3, 'subscription', '2026-04-20', '2026-07-19', 5, 10000.00, 150000.00, 0.00, 150000.00, NULL, NULL, NULL, 'paid', 150000.00, '2026-04-21', NULL, NULL, NULL, NULL, 1, '2026-04-20 04:06:35', '2026-04-20 04:06:35'),
(6, 'INV-202604-0002', 'W4bFD', 3, 'additional_user', '2026-04-20', '2026-07-19', 3, 10000.00, 120000.00, 0.00, 120000.00, NULL, NULL, NULL, 'paid', 120000.00, '2026-04-27', '2026-04-20', 'cash', 'CASH-1776682492', 'زيادة عدد المستخدمين من 5 إلى 8 (حسب المدة المتبقية: 4 شهر). ', 1, '2026-04-20 10:54:52', '2026-04-20 10:54:52'),
(7, 'INV-202604-0003', 'W4bFD', 3, 'additional_user', '2026-04-20', '2026-07-19', 3, 10000.00, 120000.00, 0.00, 120000.00, NULL, NULL, NULL, 'paid', 120000.00, '2026-04-27', '2026-04-20', 'cash', 'CASH-1776682492', 'زيادة عدد المستخدمين من 8 إلى 11 (حسب المدة المتبقية: 4 شهر). ', 1, '2026-04-20 10:54:52', '2026-04-20 10:54:52');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_pricing`
--

CREATE TABLE `subscription_pricing` (
  `id` bigint UNSIGNED NOT NULL,
  `standard_price_monthly` decimal(12,2) NOT NULL DEFAULT '10000.00',
  `standard_price_yearly` decimal(12,2) NOT NULL DEFAULT '8000.00',
  `default_percentage_rate` decimal(5,2) NOT NULL DEFAULT '5.00',
  `default_fixed_order_fee` decimal(12,2) NOT NULL DEFAULT '1000.00',
  `grace_period_days` int NOT NULL DEFAULT '7',
  `warning_days` int NOT NULL DEFAULT '4',
  `payment_due_days` int NOT NULL DEFAULT '7',
  `trial_days` int UNSIGNED NOT NULL DEFAULT '7',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_pricing`
--

INSERT INTO `subscription_pricing` (`id`, `standard_price_monthly`, `standard_price_yearly`, `default_percentage_rate`, `default_fixed_order_fee`, `grace_period_days`, `warning_days`, `payment_due_days`, `trial_days`, `notes`, `created_at`, `updated_at`) VALUES
(1, 10000.00, 10000.00, 0.00, 0.00, 1, 1, 1, 7, NULL, '2026-01-17 14:22:10', '2026-02-21 19:02:54');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint UNSIGNED NOT NULL,
  `supplier_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` int DEFAULT NULL,
  `opening_balance` decimal(18,2) NOT NULL DEFAULT '0.00',
  `phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_name`, `company_name`, `company_code`, `branch_id`, `opening_balance`, `phone`, `address`, `note`, `created_at`, `updated_at`) VALUES
(5, 'محمد كازم حسين', 'موسى علي كاظم', 'W4bFD', 30, 840000.00, '12444444444', 'بغداد', NULL, '2026-04-20 05:38:10', '2026-04-20 05:53:26'),
(6, 'عبدالله علي', 'موسى علي كاظم', 'W4bFD', 30, 480000.00, '12444444444', 'بغداد', NULL, '2026-04-20 05:38:10', '2026-04-20 05:52:13');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payments`
--

CREATE TABLE `supplier_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `payment_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `balance_before` decimal(18,2) NOT NULL,
  `balance_after` decimal(18,2) NOT NULL,
  `payment_method` enum('cash','bank_transfer','check') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `company_payment_card_id` bigint UNSIGNED DEFAULT NULL,
  `reference_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier_payments`
--

INSERT INTO `supplier_payments` (`id`, `payment_number`, `supplier_id`, `company_code`, `branch_id`, `amount`, `balance_before`, `balance_after`, `payment_method`, `company_payment_card_id`, `reference_number`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'PAY-2026-0001', 6, 'W4bFD', 30, 80000.00, 480000.00, 400000.00, 'cash', NULL, NULL, NULL, 76, '2026-04-20 06:20:53', '2026-04-20 06:20:53'),
(2, 'PAY-2026-0002', 6, 'W4bFD', 30, 20000.00, 400000.00, 380000.00, 'cash', NULL, NULL, NULL, 76, '2026-04-20 06:30:10', '2026-04-20 06:30:10'),
(3, 'PAY-2026-0003', 6, 'W4bFD', 30, 380000.00, 380000.00, 0.00, 'cash', NULL, NULL, NULL, 76, '2026-04-20 06:30:46', '2026-04-20 06:30:46');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` bigint UNSIGNED NOT NULL,
  `ticket_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('technical','billing','feature_request','bug','general') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `priority` enum('low','medium','high','urgent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','pending_response','resolved','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `assigned_to` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_response_at` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `rating` tinyint DEFAULT NULL,
  `feedback` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_replies`
--

CREATE TABLE `ticket_replies` (
  `id` bigint UNSIGNED NOT NULL,
  `ticket_id` bigint UNSIGNED NOT NULL,
  `user_type` enum('customer','support') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `user_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `is_internal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usertype_id` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` int DEFAULT NULL,
  `shift_id` bigint UNSIGNED DEFAULT NULL,
  `emp_type_code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `deactivated_by_subscription` tinyint(1) NOT NULL DEFAULT '0',
  `subscription_deactivated_at` timestamp NULL DEFAULT NULL,
  `is_logged_in` tinyint(1) NOT NULL DEFAULT '0',
  `device_fingerprint` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `session_timeout_minutes` int NOT NULL DEFAULT '120',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `username`, `password`, `usertype_id`, `company_code`, `branch_id`, `shift_id`, `emp_type_code`, `account_code`, `is_active`, `deactivated_by_subscription`, `subscription_deactivated_at`, `is_logged_in`, `device_fingerprint`, `remember_token`, `current_session_id`, `last_activity_at`, `session_timeout_minutes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'سوبر ادمن', 'info@gmail.com', 'info', '$2y$10$VWncIrv4O1zoRlBfnfMaK.u0sx0g9QgPxdatOpvFeyslggdtL5mFC', 'SA', 'SA', NULL, NULL, NULL, 'SA', 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 480, '2025-09-27 08:57:04', '2026-04-24 12:32:18', NULL),
(50, 'سوبر ادمن2', 'info2@gmail.com', 'info2', '$2y$10$jRiGGXnmD2W7PhU2xKqMgO5dx6AMFgQt4jLGOTOkIaqn0K6JC2yTW', 'SA', 'SA', NULL, NULL, NULL, 'SA', 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 120, '2026-02-27 20:36:53', '2026-04-11 09:08:25', NULL),
(51, 'سوبر ادمن1', 'info1@gmail.com', 'info1', '$2y$10$LePCtBKKxEFZVrDYLQXGPuTE4q07h.KCwFpY33yhIPWoyOof0Z/u.', 'SA', 'SA', NULL, NULL, NULL, 'SA', 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 120, '2026-03-18 19:46:59', '2026-03-18 19:46:59', NULL),
(52, 'سوبر ادمن3', 'info3@gmail.com', 'info3', '$2y$10$KDYGxjIMlmy6BED59hDgFu4vgDRVb.n.vaEQ8apvHyt2Kx25.f59i', 'SA', 'SA', NULL, NULL, NULL, 'SA', 1, 0, NULL, 0, NULL, NULL, '0u3xbWKnvAfNdMpSWM005ydfEn3oYEe9kDgFrPru', '2026-03-31 13:19:59', 120, '2026-03-18 19:47:30', '2026-03-31 13:19:59', NULL),
(53, 'سوبر ادمن9', 'info9@gmail.com', 'info4', '$2y$10$VCUl2N4TuZeACML7qOSTweSwEKxqjWf2SZFihEIsxh4KJtXEv1/vS', 'SA', 'SA', NULL, NULL, NULL, 'SA', 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 120, '2026-03-26 10:36:26', '2026-04-13 16:47:56', NULL),
(69, 'سوبر ادمن5', 'info5@gmail.com', 'info5', '$2y$10$W1Q3ZaUZQE15BJtqYiWLnOGiXfaT1S33vTUCk/2svt5syuV71EzRS', 'SA', 'SA', NULL, NULL, NULL, 'SA', 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 120, '2026-04-17 16:06:51', '2026-04-17 16:06:51', NULL),
(76, 'مدير شركة الامين', 'amean@system.local', 'amean', '$2y$10$kLZOfFM4pkpduIFQ/75iB.clNxZyfiSitwZu02bZg2Mk1YiIP52Ga', 'CM', 'W4bFD', NULL, NULL, NULL, 'emp', 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 120, '2026-04-20 04:10:22', '2026-04-24 12:35:34', NULL),
(77, 'مدير فرع', 'brm@system.local', 'brm', '$2y$10$6MMgVS/4o2IlgDTpHi.GYu5LHSHiskP78kPDCv8/t8IWsPDn.n1w.', 'BM', 'W4bFD', 30, NULL, 'ENG', 'emp', 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 120, '2026-04-20 05:30:50', '2026-04-20 11:08:43', NULL),
(78, 'الاحسان للمقاولات', 'con@system.local', 'con', '$2y$10$5uYqXf06FQHQpRjXq2LZF.JuPcmJqY8IcudMddLwPfLDUKOI.rBrW', 'CM', 'W4bFD', 30, NULL, NULL, 'cont', 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 120, '2026-04-20 05:36:04', '2026-04-20 11:00:14', NULL),
(79, 'سائق خباطة', 'd1@system.local', 'd1', '$2y$10$l3.oBkw5MmrUGqc.uORQ1ezNjJWbATKYBUP10zEqb71gAjNRUX0h6', 'US', 'W4bFD', 30, 19, 'DRV', 'driver', 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 120, '2026-04-20 07:06:29', '2026-04-20 08:41:07', NULL),
(80, 'سائق بم', 'd2@system.local', 'd2', '$2y$10$HbTZziR95UwZGcPAE9W6AeJCMNp5J5H.dH4kQ19Ipc7VH.r5y0u2m', 'US', 'W4bFD', 30, 19, 'DRV', 'driver', 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 120, '2026-04-20 07:06:58', '2026-04-20 07:43:46', NULL),
(81, 'مهندس', 'eng@system.local', 'eng', '$2y$10$nxFgpNZl881ARyPk4AlPf.0M7P6tvgtveMnW1CvzyqGxg0ha5gSaq', 'US', 'W4bFD', 30, NULL, 'ENG', 'emp', 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 120, '2026-04-20 10:55:37', '2026-04-20 11:14:10', NULL),
(82, 'محاسب', 'acc@system.local', 'acc', '$2y$10$AugVuqn6ZaA6kx8p98wY.eDjg7HGbsM0qZj7CMfwmThmkChcGL1tm', 'US', 'W4bFD', 30, NULL, 'ACC', 'emp', 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 120, '2026-04-20 10:56:15', '2026-04-20 11:20:32', NULL),
(83, 'مدير مستودع', 'wh@system.local', 'wh', '$2y$10$H56qSD/8z.yfZrOM2wV.M.t.5aEXMs5CoNVAhlbXdNyhW5MBdKPxS', 'US', 'W4bFD', 30, NULL, 'WHS', 'emp', 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 120, '2026-04-20 10:56:54', '2026-04-20 11:25:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `usertype`
--

CREATE TABLE `usertype` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usertype`
--

INSERT INTO `usertype` (`id`, `name`, `code`, `role`, `created_at`, `updated_at`) VALUES
(1, 'سوبر ادمن', 'SA', 1, '2025-10-08 11:33:51', '2025-10-13 18:06:25'),
(2, 'مدير شركة', 'CM', 2, '2025-10-08 11:33:51', '2025-10-13 18:06:13'),
(3, 'مدير فرع', 'BM', 3, '2025-10-08 11:33:51', '2025-10-16 19:39:33'),
(4, 'مستخدم', 'US', 4, '2025-10-08 11:33:51', '2025-10-16 19:39:37'),
(5, 'موظف', 'EMP', 0, '2026-01-15 18:38:30', '2026-01-15 18:38:30');

-- --------------------------------------------------------

--
-- Table structure for table `user_shifts`
--

CREATE TABLE `user_shifts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `shift_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_shifts`
--

INSERT INTO `user_shifts` (`id`, `user_id`, `shift_id`, `created_at`, `updated_at`) VALUES
(1, 80, 19, '2026-04-20 07:43:46', '2026-04-20 07:43:46'),
(2, 80, 20, '2026-04-20 07:43:46', '2026-04-20 07:43:46'),
(3, 79, 19, '2026-04-20 07:44:06', '2026-04-20 07:44:06'),
(4, 79, 20, '2026-04-20 07:44:06', '2026-04-20 07:44:06');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_drivers`
--

CREATE TABLE `vehicle_drivers` (
  `id` bigint UNSIGNED NOT NULL,
  `vehicle_id` bigint UNSIGNED NOT NULL,
  `driver_id` bigint UNSIGNED NOT NULL,
  `assignment_type` enum('primary','backup') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'primary',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `assigned_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_reservations`
--

CREATE TABLE `vehicle_reservations` (
  `id` bigint UNSIGNED NOT NULL,
  `vehicle_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `job_id` bigint UNSIGNED DEFAULT NULL,
  `reserved_from` datetime NOT NULL,
  `reserved_to` datetime NOT NULL,
  `driver_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('pending','confirmed','in_use','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `purpose` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `reserved_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_status_history`
--

CREATE TABLE `vehicle_status_history` (
  `id` bigint UNSIGNED NOT NULL,
  `vehicle_id` bigint UNSIGNED NOT NULL,
  `old_status` enum('available','reserved','in_maintenance','out_of_service','scrapped') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_status` enum('available','reserved','in_maintenance','out_of_service','scrapped') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `related_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` bigint UNSIGNED DEFAULT NULL,
  `changed_by` bigint UNSIGNED NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `work_jobs`
--

CREATE TABLE `work_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `job_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `customer_type` enum('contractor','agent_customer','direct_customer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `concrete_type_id` bigint UNSIGNED NOT NULL,
  `total_quantity` decimal(10,2) NOT NULL,
  `executed_quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `completion_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `unit_price` decimal(15,2) NOT NULL,
  `total_price` decimal(15,2) NOT NULL,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `final_price` decimal(15,2) NOT NULL,
  `location_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_map_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `scheduled_date` date NOT NULL,
  `scheduled_time` time DEFAULT NULL,
  `actual_start_date` date DEFAULT NULL,
  `actual_end_date` date DEFAULT NULL,
  `status` enum('pending','materials_reserved','in_progress','partially_completed','completed','cancelled','on_hold') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `supervisor_id` bigint UNSIGNED DEFAULT NULL,
  `default_pump_id` bigint UNSIGNED DEFAULT NULL,
  `default_pump_driver_id` bigint UNSIGNED DEFAULT NULL,
  `pump_assigned_at` timestamp NULL DEFAULT NULL,
  `pump_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `internal_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `total_shipments` int NOT NULL DEFAULT '0',
  `total_working_hours` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_by` bigint UNSIGNED NOT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `work_jobs`
--

INSERT INTO `work_jobs` (`id`, `job_number`, `company_code`, `branch_id`, `order_id`, `customer_type`, `customer_id`, `customer_name`, `customer_phone`, `concrete_type_id`, `total_quantity`, `executed_quantity`, `completion_percentage`, `unit_price`, `total_price`, `discount_amount`, `final_price`, `location_address`, `location_map_url`, `latitude`, `longitude`, `scheduled_date`, `scheduled_time`, `actual_start_date`, `actual_end_date`, `status`, `supervisor_id`, `default_pump_id`, `default_pump_driver_id`, `pump_assigned_at`, `pump_notes`, `notes`, `internal_notes`, `total_shipments`, `total_working_hours`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 'WJ-20260420-0001', 'W4bFD', 30, 3, 'direct_customer', 77, 'زبون مباشر', '01111222222', 71, 25.00, 25.00, 0.00, 60000.00, 1500000.00, 0.00, 1500000.00, 'بغداد', NULL, NULL, NULL, '2026-04-20', '11:07:00', '2026-04-20', NULL, 'completed', NULL, 5, NULL, '2026-04-20 07:08:33', NULL, NULL, NULL, 0, 0.00, 77, NULL, '2026-04-20 07:08:13', '2026-04-20 08:40:52', NULL),
(4, 'WJ-20260420-0004', 'W4bFD', 30, 4, 'contractor', 78, 'الاحسان للمقاولات', '', 71, 50.00, 0.00, 0.00, 65000.00, 3250000.00, 0.00, 3250000.00, 'بغداد', NULL, NULL, NULL, '2026-04-20', '08:00:00', '2026-04-20', NULL, 'in_progress', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 81, NULL, '2026-04-20 11:04:29', '2026-04-20 11:05:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `work_losses`
--

CREATE TABLE `work_losses` (
  `id` bigint UNSIGNED NOT NULL,
  `company_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `job_id` bigint UNSIGNED DEFAULT NULL,
  `shipment_id` bigint UNSIGNED DEFAULT NULL,
  `vehicle_id` bigint UNSIGNED DEFAULT NULL,
  `loss_type` enum('accident','vehicle_breakdown','material_spoilage','spillage','rejection','weather','road_issue','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity_lost` decimal(10,2) DEFAULT NULL,
  `estimated_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `actual_cost` decimal(15,2) DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `investigation_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `investigated_by` bigint UNSIGNED DEFAULT NULL,
  `investigated_at` datetime DEFAULT NULL,
  `resolution` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `resolution_date` date DEFAULT NULL,
  `status` enum('reported','investigating','resolved','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'reported',
  `reported_by` bigint UNSIGNED NOT NULL,
  `reported_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `work_orders`
--

CREATE TABLE `work_orders` (
  `id` bigint UNSIGNED NOT NULL,
  `sender_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_id` bigint UNSIGNED DEFAULT NULL,
  `classification` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `status_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `request_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_map_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `location_lat` decimal(10,7) DEFAULT NULL,
  `location_lng` decimal(10,7) DEFAULT NULL,
  `delivery_datetime` datetime DEFAULT NULL,
  `customer_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_date` datetime DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `review_user` bigint UNSIGNED DEFAULT NULL,
  `review_date` datetime DEFAULT NULL,
  `review_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `accept_user` bigint UNSIGNED DEFAULT NULL,
  `accept_date` datetime DEFAULT NULL,
  `accept_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rejected_user` bigint UNSIGNED DEFAULT NULL,
  `rejected_date` datetime DEFAULT NULL,
  `rejected_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `branch_approval_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_approval_user_id` bigint UNSIGNED DEFAULT NULL,
  `branch_approval_date` datetime DEFAULT NULL,
  `branch_approval_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `requester_approval_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requester_approval_user_id` bigint UNSIGNED DEFAULT NULL,
  `requester_approval_date` datetime DEFAULT NULL,
  `requester_approval_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) DEFAULT NULL,
  `initial_price` decimal(15,2) DEFAULT NULL,
  `price_approved` tinyint(1) NOT NULL DEFAULT '0',
  `payment_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid' COMMENT 'حالة الدفع: unpaid, partial, paid',
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'المبلغ المدفوع',
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'طريقة الدفع: cash, bank_transfer, check, card',
  `payment_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'ملاحظة الدفع',
  `paid_at` datetime DEFAULT NULL COMMENT 'تاريخ الدفع',
  `paid_by` bigint UNSIGNED DEFAULT NULL COMMENT 'من قام بتسجيل الدفع',
  `approved_price` decimal(15,2) DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `client_approved` enum('accepted','rejected','edit_requested','pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `client_approved_at` datetime DEFAULT NULL,
  `final_price` decimal(15,2) DEFAULT NULL,
  `execution_date` date DEFAULT NULL,
  `execution_time` time DEFAULT NULL,
  `cancellation_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `order_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_source` enum('contractor','agent','direct') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'direct',
  `branch_approved` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `branch_price` decimal(15,2) DEFAULT NULL,
  `branch_discount` decimal(10,2) DEFAULT NULL,
  `branch_suggested_date` date DEFAULT NULL,
  `branch_suggested_time` time DEFAULT NULL,
  `branch_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `branch_reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `branch_reviewed_at` datetime DEFAULT NULL,
  `requester_response` enum('pending','accepted','rejected','edit_requested') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `requester_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `requester_responded_by` bigint UNSIGNED DEFAULT NULL,
  `requester_responded_at` datetime DEFAULT NULL,
  `final_approved` tinyint(1) NOT NULL DEFAULT '0',
  `final_approved_by` bigint UNSIGNED DEFAULT NULL,
  `final_approved_at` datetime DEFAULT NULL,
  `final_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `concrete_type_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `map_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `completed_by` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `work_orders`
--

INSERT INTO `work_orders` (`id`, `sender_type`, `sender_id`, `classification`, `company_code`, `branch_id`, `status_code`, `request_type`, `quantity`, `location`, `location_map_url`, `location_lat`, `location_lng`, `delivery_datetime`, `customer_phone`, `customer_name`, `request_date`, `created_by`, `review_user`, `review_date`, `review_note`, `accept_user`, `accept_date`, `accept_note`, `rejected_user`, `rejected_date`, `rejected_note`, `branch_approval_status`, `branch_approval_user_id`, `branch_approval_date`, `branch_approval_note`, `requester_approval_status`, `requester_approval_user_id`, `requester_approval_date`, `requester_approval_note`, `note`, `price`, `initial_price`, `price_approved`, `payment_status`, `paid_amount`, `payment_method`, `payment_note`, `paid_at`, `paid_by`, `approved_price`, `approved_by`, `approved_at`, `approved_note`, `client_approved`, `client_approved_at`, `final_price`, `execution_date`, `execution_time`, `cancellation_reason`, `created_at`, `updated_at`, `deleted_at`, `order_number`, `order_source`, `branch_approved`, `branch_price`, `branch_discount`, `branch_suggested_date`, `branch_suggested_time`, `branch_notes`, `branch_reviewed_by`, `branch_reviewed_at`, `requester_response`, `requester_notes`, `requester_responded_by`, `requester_responded_at`, `final_approved`, `final_approved_by`, `final_approved_at`, `final_notes`, `concrete_type_notes`, `map_location`, `latitude`, `longitude`, `completed_at`, `completed_by`) VALUES
(3, 'branch', 77, '71', 'W4bFD', 30, 'completed', 'direct', 25.00, 'بغداد', NULL, NULL, NULL, '2026-04-20 11:07:00', '01111222222', 'زبون مباشر', '2026-04-20 10:08:13', 77, NULL, NULL, NULL, 77, '2026-04-20 10:08:13', 'طلب مباشر من الفرع', NULL, NULL, NULL, 'approved', 77, '2026-04-20 10:08:13', 'طلب مباشر - موافقة تلقائية', 'approved', NULL, '2026-04-20 10:08:13', NULL, NULL, 1500000.00, NULL, 0, 'paid', 1500000.00, 'cash', NULL, '2026-04-20 11:41:56', 77, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2026-04-20', '11:07:00', NULL, '2026-04-20 07:08:13', '2026-04-20 10:57:55', NULL, NULL, 'direct', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-20 13:57:55', 81),
(4, 'cont', 78, '71', 'W4bFD', 30, 'in_progress', NULL, 50.00, 'بغداد', NULL, NULL, NULL, '2026-04-20 13:58:00', '77888888888', NULL, '2026-04-20 13:58:40', 78, NULL, NULL, NULL, 81, '2026-04-20 14:04:29', NULL, NULL, NULL, NULL, 'approved', 81, '2026-04-20 13:59:45', NULL, 'approved', 78, '2026-04-20 14:00:10', NULL, NULL, 3250000.00, NULL, 0, 'unpaid', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, 3250000.00, '2026-04-20', '08:00:00', NULL, '2026-04-20 10:58:40', '2026-04-20 11:04:29', NULL, NULL, 'direct', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `work_shipments`
--

CREATE TABLE `work_shipments` (
  `id` bigint UNSIGNED NOT NULL,
  `job_id` bigint UNSIGNED NOT NULL,
  `shipment_number` int NOT NULL,
  `planned_quantity` decimal(10,2) NOT NULL,
  `actual_quantity` decimal(10,2) DEFAULT NULL,
  `mixer_id` bigint UNSIGNED DEFAULT NULL,
  `truck_id` bigint UNSIGNED DEFAULT NULL,
  `pump_id` bigint UNSIGNED DEFAULT NULL,
  `mixer_driver_id` bigint UNSIGNED DEFAULT NULL,
  `truck_driver_id` bigint UNSIGNED DEFAULT NULL,
  `pump_driver_id` bigint UNSIGNED DEFAULT NULL,
  `departure_time` datetime DEFAULT NULL,
  `arrival_time` datetime DEFAULT NULL,
  `work_start_time` datetime DEFAULT NULL,
  `work_end_time` datetime DEFAULT NULL,
  `return_time` datetime DEFAULT NULL,
  `status` enum('planned','preparing','departed','arrived','working','completed','completed_with_loss','returned','cancelled','damaged') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planned',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `driver_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `work_shipments`
--

INSERT INTO `work_shipments` (`id`, `job_id`, `shipment_number`, `planned_quantity`, `actual_quantity`, `mixer_id`, `truck_id`, `pump_id`, `mixer_driver_id`, `truck_driver_id`, `pump_driver_id`, `departure_time`, `arrival_time`, `work_start_time`, `work_end_time`, `return_time`, `status`, `notes`, `driver_notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 20.00, 20.00, 2, NULL, NULL, 1, NULL, NULL, '2026-04-13 22:32:01', '2026-04-13 22:32:05', '2026-04-13 22:32:08', '2026-04-13 22:32:13', NULL, 'completed', NULL, NULL, 62, '2026-04-13 19:31:57', '2026-04-13 19:32:13'),
(2, 2, 1, 20.00, 20.00, 2, NULL, NULL, 1, NULL, NULL, '2026-04-13 22:36:38', '2026-04-13 22:36:42', '2026-04-13 22:36:45', '2026-04-13 22:36:48', NULL, 'completed', NULL, NULL, 62, '2026-04-13 19:36:34', '2026-04-13 19:36:48'),
(3, 3, 1, 25.00, 25.00, 6, NULL, NULL, 79, NULL, NULL, '2026-04-20 11:34:24', '2026-04-20 11:40:32', '2026-04-20 11:40:45', '2026-04-20 11:40:52', '2026-04-20 11:40:59', 'returned', NULL, NULL, 77, '2026-04-20 08:33:55', '2026-04-20 08:40:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts_type`
--
ALTER TABLE `accounts_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_balances`
--
ALTER TABLE `account_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_account_balance` (`company_code`,`branch_id`,`account_type`,`account_id`,`currency_code`),
  ADD KEY `account_balances_branch_id_foreign` (`branch_id`),
  ADD KEY `account_balances_account_type_account_id_index` (`account_type`,`account_id`),
  ADD KEY `account_balances_balance_type_index` (`balance_type`),
  ADD KEY `account_balances_company_code_index` (`company_code`);

--
-- Indexes for table `advances`
--
ALTER TABLE `advances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `advances_advance_number_unique` (`advance_number`),
  ADD KEY `advances_branch_id_foreign` (`branch_id`),
  ADD KEY `advances_requested_by_foreign` (`requested_by`),
  ADD KEY `advances_approved_by_foreign` (`approved_by`),
  ADD KEY `advances_created_by_foreign` (`created_by`),
  ADD KEY `advances_updated_by_foreign` (`updated_by`),
  ADD KEY `advances_company_code_branch_id_index` (`company_code`,`branch_id`),
  ADD KEY `advances_beneficiary_type_beneficiary_id_index` (`beneficiary_type`,`beneficiary_id`),
  ADD KEY `advances_status_index` (`status`),
  ADD KEY `advances_requested_at_index` (`requested_at`),
  ADD KEY `idx_adv_company_status` (`company_code`,`status`);

--
-- Indexes for table `advance_payments`
--
ALTER TABLE `advance_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `advance_payments_payment_number_unique` (`payment_number`),
  ADD KEY `advance_payments_paid_by_foreign` (`paid_by`),
  ADD KEY `advance_payments_advance_id_index` (`advance_id`),
  ADD KEY `advance_payments_payment_type_index` (`payment_type`),
  ADD KEY `advance_payments_paid_at_index` (`paid_at`);

--
-- Indexes for table `advance_settings`
--
ALTER TABLE `advance_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_company_branch` (`company_code`,`branch_id`),
  ADD KEY `advance_settings_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_daily_attendance` (`employee_id`,`attendance_date`),
  ADD KEY `attendances_company_code_attendance_date_index` (`company_code`,`attendance_date`),
  ADD KEY `attendances_employee_id_attendance_date_index` (`employee_id`,`attendance_date`),
  ADD KEY `attendances_branch_id_attendance_date_index` (`branch_id`,`attendance_date`),
  ADD KEY `attendances_status_index` (`status`),
  ADD KEY `attendances_user_id_foreign` (`user_id`),
  ADD KEY `attendances_shift_id_foreign` (`shift_id`),
  ADD KEY `attendances_modified_by_foreign` (`modified_by`),
  ADD KEY `idx_att_emp_date` (`employee_id`,`attendance_date`),
  ADD KEY `idx_att_company_date` (`company_code`,`attendance_date`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `audit_logs_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `audit_logs_company_code_action_created_at_index` (`company_code`,`action`,`created_at`),
  ADD KEY `audit_logs_company_code_index` (`company_code`),
  ADD KEY `audit_logs_action_index` (`action`),
  ADD KEY `audit_logs_created_at_index` (`created_at`);

--
-- Indexes for table `backups`
--
ALTER TABLE `backups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `backups_created_by_foreign` (`created_by`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_branch_code_v2_unique` (`branch_code_v2`),
  ADD KEY `idx_branch_company` (`company_code`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_operational_status` (`operational_status`),
  ADD KEY `idx_next_maintenance` (`next_maintenance_date`),
  ADD KEY `idx_car_company_branch` (`company_code`,`branch_id`);

--
-- Indexes for table `cars_types`
--
ALTER TABLE `cars_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_company_car_type_code` (`company_code`,`code`);

--
-- Indexes for table `car_drivers`
--
ALTER TABLE `car_drivers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_drivers_company_code_index` (`company_code`),
  ADD KEY `car_drivers_car_id_index` (`car_id`),
  ADD KEY `car_drivers_driver_id_index` (`driver_id`),
  ADD KEY `car_drivers_shift_id_index` (`shift_id`),
  ADD KEY `car_drivers_driver_type_index` (`driver_type`),
  ADD KEY `car_drivers_is_active_index` (`is_active`),
  ADD KEY `idx_car_shift_type_active` (`car_id`,`shift_id`,`driver_type`,`is_active`);

--
-- Indexes for table `car_maintenances`
--
ALTER TABLE `car_maintenances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_maintenances_company_code_car_id_index` (`company_code`,`car_id`),
  ADD KEY `car_maintenances_company_code_branch_id_index` (`company_code`,`branch_id`),
  ADD KEY `car_maintenances_maintenance_date_index` (`maintenance_date`),
  ADD KEY `car_maintenances_status_index` (`status`),
  ADD KEY `car_maintenances_company_payment_card_id_foreign` (`company_payment_card_id`);

--
-- Indexes for table `cash_registers`
--
ALTER TABLE `cash_registers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cash_registers_company_code_index` (`company_code`),
  ADD KEY `cash_registers_branch_id_index` (`branch_id`),
  ADD KEY `cash_registers_handled_at_index` (`handled_at`),
  ADD KEY `cash_registers_transaction_type_index` (`transaction_type`),
  ADD KEY `cash_registers_company_code_branch_id_handled_at_index` (`company_code`,`branch_id`,`handled_at`);

--
-- Indexes for table `checks`
--
ALTER TABLE `checks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_checks_number` (`check_number`),
  ADD KEY `idx_checks_due` (`due_date`),
  ADD KEY `idx_checks_status` (`status`),
  ADD KEY `idx_checks_drawer` (`drawer_type`,`drawer_id`),
  ADD KEY `idx_checks_company` (`company_code`),
  ADD KEY `idx_checks_branch` (`branch_id`),
  ADD KEY `idx_checks_company_due` (`company_code`,`due_date`),
  ADD KEY `idx_checks_company_status` (`company_code`,`status`),
  ADD KEY `idx_chk_company_status` (`company_code`,`status`),
  ADD KEY `idx_chk_due` (`due_date`);

--
-- Indexes for table `check_status_logs`
--
ALTER TABLE `check_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_check_logs_check` (`check_id`);

--
-- Indexes for table `chemicals`
--
ALTER TABLE `chemicals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `companies_code_v2_unique` (`code_v2`);

--
-- Indexes for table `company_payment_cards`
--
ALTER TABLE `company_payment_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_payment_cards_created_by_foreign` (`created_by`),
  ADD KEY `company_payment_cards_company_code_index` (`company_code`),
  ADD KEY `company_payment_cards_branch_id_index` (`branch_id`),
  ADD KEY `company_payment_cards_is_active_index` (`is_active`);

--
-- Indexes for table `company_payment_card_transactions`
--
ALTER TABLE `company_payment_card_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_payment_card_transactions_transaction_number_unique` (`transaction_number`),
  ADD KEY `cpct_created_by_fk` (`created_by`),
  ADD KEY `company_payment_card_transactions_company_payment_card_id_index` (`company_payment_card_id`),
  ADD KEY `company_payment_card_transactions_company_code_index` (`company_code`),
  ADD KEY `company_payment_card_transactions_branch_id_index` (`branch_id`),
  ADD KEY `company_payment_card_transactions_type_index` (`type`);

--
-- Indexes for table `company_subscriptions`
--
ALTER TABLE `company_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_subscription_period` (`company_code`,`start_date`,`end_date`);

--
-- Indexes for table `company_subscription_prices`
--
ALTER TABLE `company_subscription_prices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `concrete_mixes`
--
ALTER TABLE `concrete_mixes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `concrete_mix_category_prices`
--
ALTER TABLE `concrete_mix_category_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_mix_category_price` (`company_code`,`concrete_mix_id`,`pricing_category_id`),
  ADD KEY `concrete_mix_category_prices_company_code_index` (`company_code`),
  ADD KEY `concrete_mix_category_prices_concrete_mix_id_index` (`concrete_mix_id`),
  ADD KEY `concrete_mix_category_prices_pricing_category_id_index` (`pricing_category_id`);

--
-- Indexes for table `concrete_mix_chemicals`
--
ALTER TABLE `concrete_mix_chemicals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `concrete_mix_chemicals_concrete_mix_id_foreign` (`concrete_mix_id`),
  ADD KEY `concrete_mix_chemicals_chemical_id_foreign` (`chemical_id`);

--
-- Indexes for table `concrete_mix_prices`
--
ALTER TABLE `concrete_mix_prices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contractors`
--
ALTER TABLE `contractors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contractors_company_code_code_unique` (`company_code`,`code`),
  ADD KEY `idx_contractors_code` (`code`),
  ADD KEY `idx_contractors_classification` (`classification`),
  ADD KEY `idx_contractors_is_blocked` (`is_blocked`),
  ADD KEY `idx_contractors_last_order` (`last_order_date`),
  ADD KEY `idx_cont_company_branch` (`company_code`,`branch_id`),
  ADD KEY `idx_cont_company_active` (`company_code`,`isactive`);

--
-- Indexes for table `contractor_accounts`
--
ALTER TABLE `contractor_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contractor_accounts_contractor_id_foreign` (`contractor_id`);

--
-- Indexes for table `contractor_checks`
--
ALTER TABLE `contractor_checks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contractor_checks_branch_id_foreign` (`branch_id`),
  ADD KEY `contractor_checks_deposited_by_foreign` (`deposited_by`),
  ADD KEY `contractor_checks_collected_by_foreign` (`collected_by`),
  ADD KEY `contractor_checks_returned_by_foreign` (`returned_by`),
  ADD KEY `contractor_checks_endorsed_by_foreign` (`endorsed_by`),
  ADD KEY `contractor_checks_cancelled_by_foreign` (`cancelled_by`),
  ADD KEY `contractor_checks_created_by_foreign` (`created_by`),
  ADD KEY `contractor_checks_company_code_type_status_index` (`company_code`,`type`,`status`),
  ADD KEY `contractor_checks_contractor_id_status_index` (`contractor_id`,`status`),
  ADD KEY `contractor_checks_due_date_status_index` (`due_date`,`status`);

--
-- Indexes for table `contractor_check_status_history`
--
ALTER TABLE `contractor_check_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contractor_check_status_history_check_id_foreign` (`check_id`),
  ADD KEY `contractor_check_status_history_changed_by_foreign` (`changed_by`);

--
-- Indexes for table `contractor_invoices`
--
ALTER TABLE `contractor_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contractor_invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `contractor_invoices_branch_id_foreign` (`branch_id`),
  ADD KEY `contractor_invoices_work_order_id_foreign` (`work_order_id`),
  ADD KEY `contractor_invoices_cancelled_by_foreign` (`cancelled_by`),
  ADD KEY `contractor_invoices_created_by_foreign` (`created_by`),
  ADD KEY `contractor_invoices_company_code_status_index` (`company_code`,`status`),
  ADD KEY `contractor_invoices_contractor_id_status_index` (`contractor_id`,`status`),
  ADD KEY `contractor_invoices_due_date_index` (`due_date`);

--
-- Indexes for table `contractor_receipts`
--
ALTER TABLE `contractor_receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contractor_receipts_receipt_number_unique` (`receipt_number`),
  ADD KEY `contractor_receipts_branch_id_foreign` (`branch_id`),
  ADD KEY `contractor_receipts_invoice_id_foreign` (`invoice_id`),
  ADD KEY `contractor_receipts_approved_by_foreign` (`approved_by`),
  ADD KEY `contractor_receipts_cancelled_by_foreign` (`cancelled_by`),
  ADD KEY `contractor_receipts_created_by_foreign` (`created_by`),
  ADD KEY `contractor_receipts_company_code_type_status_index` (`company_code`,`type`,`status`),
  ADD KEY `contractor_receipts_contractor_id_type_index` (`contractor_id`,`type`),
  ADD KEY `contractor_receipts_receipt_date_index` (`receipt_date`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `currencies_code_unique` (`code`),
  ADD KEY `currencies_code_index` (`code`),
  ADD KEY `currencies_is_default_index` (`is_default`);

--
-- Indexes for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_payments_payment_number_unique` (`payment_number`),
  ADD KEY `customer_payments_company_payment_card_id_foreign` (`company_payment_card_id`),
  ADD KEY `customer_payments_created_by_foreign` (`created_by`),
  ADD KEY `customer_payments_updated_by_foreign` (`updated_by`),
  ADD KEY `customer_payments_company_code_index` (`company_code`),
  ADD KEY `customer_payments_branch_id_index` (`branch_id`),
  ADD KEY `customer_payments_work_order_id_index` (`work_order_id`),
  ADD KEY `customer_payments_status_index` (`status`),
  ADD KEY `customer_payments_customer_phone_index` (`customer_phone`);

--
-- Indexes for table `customer_payment_records`
--
ALTER TABLE `customer_payment_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_payment_records_record_number_unique` (`record_number`),
  ADD KEY `customer_payment_records_company_payment_card_id_foreign` (`company_payment_card_id`),
  ADD KEY `customer_payment_records_created_by_foreign` (`created_by`),
  ADD KEY `customer_payment_records_customer_payment_id_index` (`customer_payment_id`),
  ADD KEY `customer_payment_records_company_code_index` (`company_code`),
  ADD KEY `customer_payment_records_branch_id_index` (`branch_id`);

--
-- Indexes for table `daily_cash_summaries`
--
ALTER TABLE `daily_cash_summaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_daily_summary` (`company_code`,`branch_id`,`summary_date`,`currency_code`),
  ADD KEY `daily_cash_summaries_branch_id_foreign` (`branch_id`),
  ADD KEY `daily_cash_summaries_opened_by_foreign` (`opened_by`),
  ADD KEY `daily_cash_summaries_closed_by_foreign` (`closed_by`),
  ADD KEY `daily_cash_summaries_summary_date_index` (`summary_date`),
  ADD KEY `daily_cash_summaries_status_index` (`status`),
  ADD KEY `daily_cash_summaries_company_code_index` (`company_code`);

--
-- Indexes for table `driver_assignments`
--
ALTER TABLE `driver_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_assignments_car_id_index` (`car_id`),
  ADD KEY `driver_assignments_driver_id_index` (`driver_id`),
  ADD KEY `driver_assignments_end_date_index` (`end_date`),
  ADD KEY `driver_assignments_company_code_branch_id_index` (`company_code`,`branch_id`),
  ADD KEY `driver_assignments_assigned_by_foreign` (`assigned_by`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employees_user_id_foreign` (`user_id`),
  ADD KEY `idx_emp_company_branch` (`company_code`,`branch_id`),
  ADD KEY `idx_emp_company_active` (`company_code`,`isactive`),
  ADD KEY `idx_emp_type` (`employee_types_id`),
  ADD KEY `idx_emp_shift` (`shift_id`);

--
-- Indexes for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_allowances_branch_id_foreign` (`branch_id`),
  ADD KEY `employee_allowances_created_by_foreign` (`created_by`),
  ADD KEY `employee_allowances_employee_id_index` (`employee_id`),
  ADD KEY `employee_allowances_is_active_index` (`is_active`),
  ADD KEY `employee_allowances_company_code_branch_id_index` (`company_code`,`branch_id`);

--
-- Indexes for table `employee_bonuses`
--
ALTER TABLE `employee_bonuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_bonuses_branch_id_foreign` (`branch_id`),
  ADD KEY `employee_bonuses_paid_in_payroll_id_foreign` (`paid_in_payroll_id`),
  ADD KEY `employee_bonuses_created_by_foreign` (`created_by`),
  ADD KEY `employee_bonuses_employee_id_index` (`employee_id`),
  ADD KEY `employee_bonuses_bonus_date_index` (`bonus_date`),
  ADD KEY `employee_bonuses_is_paid_index` (`is_paid`),
  ADD KEY `employee_bonuses_company_code_branch_id_index` (`company_code`,`branch_id`);

--
-- Indexes for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_deductions_branch_id_foreign` (`branch_id`),
  ADD KEY `employee_deductions_deducted_in_payroll_id_foreign` (`deducted_in_payroll_id`),
  ADD KEY `employee_deductions_created_by_foreign` (`created_by`),
  ADD KEY `employee_deductions_employee_id_index` (`employee_id`),
  ADD KEY `employee_deductions_deduction_date_index` (`deduction_date`),
  ADD KEY `employee_deductions_is_deducted_index` (`is_deducted`),
  ADD KEY `employee_deductions_company_code_branch_id_index` (`company_code`,`branch_id`);

--
-- Indexes for table `employee_leaves`
--
ALTER TABLE `employee_leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_leaves_branch_id_foreign` (`branch_id`),
  ADD KEY `employee_leaves_approved_by_foreign` (`approved_by`),
  ADD KEY `employee_leaves_requested_by_foreign` (`requested_by`),
  ADD KEY `employee_leaves_employee_id_index` (`employee_id`),
  ADD KEY `employee_leaves_start_date_end_date_index` (`start_date`,`end_date`),
  ADD KEY `employee_leaves_status_index` (`status`),
  ADD KEY `employee_leaves_company_code_branch_id_index` (`company_code`,`branch_id`);

--
-- Indexes for table `employee_shifts`
--
ALTER TABLE `employee_shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_shift_active` (`employee_id`,`shift_id`,`is_active`),
  ADD KEY `employee_shifts_company_code_is_active_index` (`company_code`,`is_active`),
  ADD KEY `employee_shifts_shift_id_is_active_index` (`shift_id`,`is_active`);

--
-- Indexes for table `employee_types`
--
ALTER TABLE `employee_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_types_code_unique` (`code`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `financial_accounts`
--
ALTER TABLE `financial_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `financial_accounts_company_code_index` (`company_code`),
  ADD KEY `financial_accounts_branch_id_index` (`branch_id`),
  ADD KEY `financial_accounts_account_type_index` (`account_type`),
  ADD KEY `financial_accounts_is_active_index` (`is_active`),
  ADD KEY `financial_accounts_current_balance_index` (`current_balance`);

--
-- Indexes for table `financial_transactions`
--
ALTER TABLE `financial_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `financial_transactions_company_code_index` (`company_code`),
  ADD KEY `financial_transactions_branch_id_index` (`branch_id`),
  ADD KEY `financial_transactions_account_id_index` (`account_id`),
  ADD KEY `financial_transactions_order_id_index` (`order_id`),
  ADD KEY `financial_transactions_transaction_type_index` (`transaction_type`),
  ADD KEY `financial_transactions_performed_at_index` (`performed_at`),
  ADD KEY `financial_transactions_is_approved_index` (`is_approved`),
  ADD KEY `financial_transactions_reference_number_index` (`reference_number`),
  ADD KEY `idx_ft_company_txn_type` (`company_code`,`transaction_type`),
  ADD KEY `idx_ft_company_created` (`company_code`,`created_at`),
  ADD KEY `idx_ft_status` (`status`);

--
-- Indexes for table `inventories`
--
ALTER TABLE `inventories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inventories_code_unique` (`code`),
  ADD KEY `idx_inv_company_branch` (`company_code`,`branch_id`),
  ADD KEY `idx_inv_code` (`code`);

--
-- Indexes for table `inventory_histories`
--
ALTER TABLE `inventory_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_invh_company` (`company_code`);

--
-- Indexes for table `inventory_losses`
--
ALTER TABLE `inventory_losses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_losses_company_code_index` (`company_code`),
  ADD KEY `inventory_losses_branch_id_index` (`branch_id`),
  ADD KEY `inventory_losses_material_type_index` (`material_type`),
  ADD KEY `inventory_losses_material_code_index` (`material_code`),
  ADD KEY `inventory_losses_material_id_index` (`material_id`),
  ADD KEY `inventory_losses_created_by_index` (`created_by`),
  ADD KEY `inventory_losses_reported_at_index` (`reported_at`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `idx_invoices_date` (`invoice_date`),
  ADD KEY `idx_invoices_due` (`due_date`),
  ADD KEY `idx_invoices_status` (`status`),
  ADD KEY `idx_invoices_company` (`company_code`),
  ADD KEY `idx_invoices_branch` (`branch_id`),
  ADD KEY `idx_invoices_company_date` (`company_code`,`invoice_date`),
  ADD KEY `idx_inv_invoice_company_status` (`company_code`,`status`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_invoice_items_invoice` (`invoice_id`);

--
-- Indexes for table `location_logs`
--
ALTER TABLE `location_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_logs_driver_id_foreign` (`driver_id`),
  ADD KEY `idx_location_shipment` (`shipment_id`),
  ADD KEY `idx_location_vehicle` (`vehicle_id`),
  ADD KEY `idx_location_recorded` (`recorded_at`);

--
-- Indexes for table `maintenance_records`
--
ALTER TABLE `maintenance_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maintenance_records_branch_id_foreign` (`branch_id`),
  ADD KEY `maintenance_records_created_by_foreign` (`created_by`),
  ADD KEY `idx_vehicle` (`vehicle_id`),
  ADD KEY `idx_type` (`maintenance_type`),
  ADD KEY `idx_dates` (`started_at`,`completed_at`),
  ADD KEY `idx_company` (`company_code`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `material_components`
--
ALTER TABLE `material_components`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `material_equipment`
--
ALTER TABLE `material_equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `material_reservations`
--
ALTER TABLE `material_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_reservations_reserved_by_foreign` (`reserved_by`),
  ADD KEY `idx_reservations_job` (`job_id`),
  ADD KEY `idx_reservations_material` (`material_id`),
  ADD KEY `idx_reservations_inventory` (`inventory_id`);

--
-- Indexes for table `measurement_units`
--
ALTER TABLE `measurement_units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_company_code_is_read_index` (`company_code`,`is_read`),
  ADD KEY `notifications_created_at_index` (`created_at`),
  ADD KEY `notifications_company_code_index` (`company_code`),
  ADD KEY `notifications_is_read_index` (`is_read`),
  ADD KEY `idx_notification_type` (`notification_type`),
  ADD KEY `idx_notification_priority` (`priority`),
  ADD KEY `notifications_related_type_related_id_index` (`related_type`,`related_id`),
  ADD KEY `idx_notif_company` (`company_code`);

--
-- Indexes for table `notification_logs`
--
ALTER TABLE `notification_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notification` (`notification_id`),
  ADD KEY `idx_channel` (`channel`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `notification_settings`
--
ALTER TABLE `notification_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_templates`
--
ALTER TABLE `notification_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notification_templates_type_unique` (`type`);

--
-- Indexes for table `order_executions`
--
ALTER TABLE `order_executions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_executions_work_order_id_index` (`work_order_id`),
  ADD KEY `order_executions_execution_date_index` (`execution_date`),
  ADD KEY `order_executions_status_index` (`status`),
  ADD KEY `order_executions_car_id_index` (`car_id`);

--
-- Indexes for table `order_histories`
--
ALTER TABLE `order_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_histories_work_order_id_index` (`work_order_id`),
  ADD KEY `order_histories_action_type_index` (`action_type`),
  ADD KEY `order_histories_user_id_index` (`user_id`),
  ADD KEY `order_histories_created_at_index` (`created_at`);

--
-- Indexes for table `order_negotiations`
--
ALTER TABLE `order_negotiations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_negotiations_action_by_foreign` (`action_by`),
  ADD KEY `order_negotiations_order_id_index` (`order_id`),
  ADD KEY `order_negotiations_stage_index` (`stage`),
  ADD KEY `order_negotiations_created_at_index` (`created_at`);

--
-- Indexes for table `order_price_changes`
--
ALTER TABLE `order_price_changes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_price_changes_work_order_id_index` (`work_order_id`),
  ADD KEY `order_price_changes_change_type_index` (`change_type`),
  ADD KEY `order_price_changes_changed_by_index` (`changed_by`),
  ADD KEY `order_price_changes_created_at_index` (`created_at`);

--
-- Indexes for table `order_stages`
--
ALTER TABLE `order_stages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_stages_work_order_id_index` (`work_order_id`),
  ADD KEY `order_stages_stage_index` (`stage`),
  ADD KEY `order_stages_created_at_index` (`created_at`);

--
-- Indexes for table `order_timeline`
--
ALTER TABLE `order_timeline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_timeline_created_by_foreign` (`created_by`),
  ADD KEY `order_timeline_order_id_index` (`order_id`),
  ADD KEY `order_timeline_event_type_index` (`event_type`),
  ADD KEY `order_timeline_created_at_index` (`created_at`);

--
-- Indexes for table `page_seo_settings`
--
ALTER TABLE `page_seo_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_seo_settings_page_key_unique` (`page_key`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_company_code_index` (`company_code`),
  ADD KEY `payments_account_id_index` (`account_id`),
  ADD KEY `payments_order_id_index` (`order_id`),
  ADD KEY `payments_payment_date_index` (`payment_date`),
  ADD KEY `payments_payment_type_index` (`payment_type`),
  ADD KEY `payments_direction_index` (`direction`),
  ADD KEY `payments_receipt_number_index` (`receipt_number`);

--
-- Indexes for table `payment_cards`
--
ALTER TABLE `payment_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_cards_created_by_foreign` (`created_by`);

--
-- Indexes for table `payment_card_transactions`
--
ALTER TABLE `payment_card_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_card_transactions_transaction_number_unique` (`transaction_number`),
  ADD KEY `payment_card_transactions_created_by_foreign` (`created_by`),
  ADD KEY `payment_card_transactions_payment_card_id_created_at_index` (`payment_card_id`,`created_at`),
  ADD KEY `payment_card_transactions_reference_type_reference_id_index` (`reference_type`,`reference_id`);

--
-- Indexes for table `payment_receipts`
--
ALTER TABLE `payment_receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_receipts_receipt_number_unique` (`receipt_number`),
  ADD KEY `payment_receipts_branch_id_foreign` (`branch_id`),
  ADD KEY `payment_receipts_transaction_id_foreign` (`transaction_id`),
  ADD KEY `payment_receipts_received_by_foreign` (`received_by`),
  ADD KEY `payment_receipts_receipt_number_index` (`receipt_number`),
  ADD KEY `payment_receipts_payer_type_payer_id_index` (`payer_type`,`payer_id`),
  ADD KEY `payment_receipts_received_at_index` (`received_at`),
  ADD KEY `payment_receipts_status_index` (`status`),
  ADD KEY `payment_receipts_company_code_index` (`company_code`);

--
-- Indexes for table `payment_vouchers`
--
ALTER TABLE `payment_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_vouchers_voucher_number_unique` (`voucher_number`),
  ADD KEY `payment_vouchers_branch_id_foreign` (`branch_id`),
  ADD KEY `payment_vouchers_transaction_id_foreign` (`transaction_id`),
  ADD KEY `payment_vouchers_approved_by_foreign` (`approved_by`),
  ADD KEY `payment_vouchers_paid_by_foreign` (`paid_by`),
  ADD KEY `payment_vouchers_created_by_foreign` (`created_by`),
  ADD KEY `payment_vouchers_voucher_number_index` (`voucher_number`),
  ADD KEY `payment_vouchers_payee_type_payee_id_index` (`payee_type`,`payee_id`),
  ADD KEY `payment_vouchers_paid_at_index` (`paid_at`),
  ADD KEY `payment_vouchers_status_index` (`status`),
  ADD KEY `payment_vouchers_company_code_index` (`company_code`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_month` (`employee_id`,`payroll_month`,`payroll_year`),
  ADD KEY `payroll_branch_id_foreign` (`branch_id`),
  ADD KEY `payroll_paid_by_foreign` (`paid_by`),
  ADD KEY `payroll_approved_by_foreign` (`approved_by`),
  ADD KEY `payroll_created_by_foreign` (`created_by`),
  ADD KEY `payroll_payroll_year_payroll_month_index` (`payroll_year`,`payroll_month`),
  ADD KEY `payroll_status_index` (`status`),
  ADD KEY `payroll_company_code_branch_id_index` (`company_code`,`branch_id`),
  ADD KEY `idx_pay_company_status` (`company_code`,`status`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `pricing_categories`
--
ALTER TABLE `pricing_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `public_contact_channels`
--
ALTER TABLE `public_contact_channels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `public_contact_channels_channel_type_index` (`channel_type`);

--
-- Indexes for table `public_contact_settings`
--
ALTER TABLE `public_contact_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `public_display_blocks`
--
ALTER TABLE `public_display_blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `public_display_blocks_page_key_index` (`page_key`),
  ADD KEY `public_display_blocks_block_type_index` (`block_type`);

--
-- Indexes for table `public_display_videos`
--
ALTER TABLE `public_display_videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `public_display_videos_page_key_index` (`page_key`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipts_receipt_number_unique` (`receipt_number`),
  ADD KEY `idx_receipts_date` (`receipt_date`),
  ADD KEY `idx_receipts_company` (`company_code`),
  ADD KEY `idx_receipts_branch` (`branch_id`),
  ADD KEY `idx_rec_company_date` (`company_code`,`created_at`);

--
-- Indexes for table `request_status`
--
ALTER TABLE `request_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_code_unique` (`code`);

--
-- Indexes for table `salary_adjustments`
--
ALTER TABLE `salary_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salary_adjustments_branch_id_foreign` (`branch_id`),
  ADD KEY `salary_adjustments_approved_by_foreign` (`approved_by`),
  ADD KEY `salary_adjustments_employee_id_index` (`employee_id`),
  ADD KEY `salary_adjustments_effective_date_index` (`effective_date`),
  ADD KEY `salary_adjustments_company_code_branch_id_index` (`company_code`,`branch_id`);

--
-- Indexes for table `seo_settings`
--
ALTER TABLE `seo_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `shift_times`
--
ALTER TABLE `shift_times`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shipment_events`
--
ALTER TABLE `shipment_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shipment_events_recorded_by_foreign` (`recorded_by`),
  ADD KEY `idx_events_shipment` (`shipment_id`),
  ADD KEY `idx_events_type` (`event_type`),
  ADD KEY `idx_events_recorded` (`recorded_at`);

--
-- Indexes for table `subscription_history`
--
ALTER TABLE `subscription_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscription_history_company_code_index` (`company_code`),
  ADD KEY `subscription_history_subscription_id_index` (`subscription_id`),
  ADD KEY `subscription_history_status_index` (`status`),
  ADD KEY `subscription_history_start_date_index` (`start_date`),
  ADD KEY `subscription_history_end_date_index` (`end_date`);

--
-- Indexes for table `subscription_invoices`
--
ALTER TABLE `subscription_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscription_invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `company_code` (`company_code`);

--
-- Indexes for table `subscription_pricing`
--
ALTER TABLE `subscription_pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_company_branch_name_unique` (`company_code`,`branch_id`,`supplier_name`),
  ADD KEY `idx_sup_company_branch` (`company_code`,`branch_id`);

--
-- Indexes for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_payments_payment_number_unique` (`payment_number`),
  ADD KEY `supplier_payments_branch_id_foreign` (`branch_id`),
  ADD KEY `supplier_payments_created_by_foreign` (`created_by`),
  ADD KEY `supplier_payments_supplier_id_company_code_index` (`supplier_id`,`company_code`),
  ADD KEY `supplier_payments_payment_number_index` (`payment_number`),
  ADD KEY `idx_suppay_supplier_date` (`supplier_id`,`created_at`),
  ADD KEY `idx_suppay_company` (`company_code`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `support_tickets_ticket_number_unique` (`ticket_number`),
  ADD KEY `support_tickets_status_index` (`status`),
  ADD KEY `support_tickets_priority_index` (`priority`),
  ADD KEY `support_tickets_category_index` (`category`),
  ADD KEY `support_tickets_created_at_index` (`created_at`),
  ADD KEY `support_tickets_company_code_index` (`company_code`);

--
-- Indexes for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_replies_ticket_id_index` (`ticket_id`),
  ADD KEY `ticket_replies_created_at_index` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD KEY `idx_user_company` (`company_code`),
  ADD KEY `idx_user_company_type` (`company_code`,`usertype_id`),
  ADD KEY `users_emp_type_code_index` (`emp_type_code`),
  ADD KEY `users_shift_id_index` (`shift_id`);

--
-- Indexes for table `usertype`
--
ALTER TABLE `usertype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_shifts`
--
ALTER TABLE `user_shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_shifts_user_shift_unique` (`user_id`,`shift_id`),
  ADD KEY `user_shifts_user_id_index` (`user_id`),
  ADD KEY `user_shifts_shift_id_index` (`shift_id`);

--
-- Indexes for table `vehicle_drivers`
--
ALTER TABLE `vehicle_drivers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assignment` (`vehicle_id`,`driver_id`,`assignment_type`,`start_date`),
  ADD KEY `vehicle_drivers_assigned_by_foreign` (`assigned_by`),
  ADD KEY `idx_vehicle` (`vehicle_id`),
  ADD KEY `idx_driver` (`driver_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `vehicle_reservations`
--
ALTER TABLE `vehicle_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_reservations_driver_id_foreign` (`driver_id`),
  ADD KEY `vehicle_reservations_reserved_by_foreign` (`reserved_by`),
  ADD KEY `idx_vehicle` (`vehicle_id`),
  ADD KEY `idx_dates` (`reserved_from`,`reserved_to`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_order` (`order_id`);

--
-- Indexes for table `vehicle_status_history`
--
ALTER TABLE `vehicle_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_status_history_changed_by_foreign` (`changed_by`),
  ADD KEY `idx_vehicle` (`vehicle_id`),
  ADD KEY `idx_changed_at` (`changed_at`);

--
-- Indexes for table `work_jobs`
--
ALTER TABLE `work_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `work_jobs_job_number_unique` (`job_number`),
  ADD KEY `work_jobs_branch_id_foreign` (`branch_id`),
  ADD KEY `work_jobs_concrete_type_id_foreign` (`concrete_type_id`),
  ADD KEY `work_jobs_supervisor_id_foreign` (`supervisor_id`),
  ADD KEY `work_jobs_created_by_foreign` (`created_by`),
  ADD KEY `work_jobs_updated_by_foreign` (`updated_by`),
  ADD KEY `idx_work_jobs_company_branch` (`company_code`,`branch_id`),
  ADD KEY `idx_work_jobs_order` (`order_id`),
  ADD KEY `idx_work_jobs_status` (`status`),
  ADD KEY `idx_work_jobs_scheduled` (`scheduled_date`),
  ADD KEY `idx_work_jobs_customer` (`customer_type`,`customer_id`),
  ADD KEY `work_jobs_default_pump_id_foreign` (`default_pump_id`),
  ADD KEY `work_jobs_default_pump_driver_id_foreign` (`default_pump_driver_id`);

--
-- Indexes for table `work_losses`
--
ALTER TABLE `work_losses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `work_losses_branch_id_foreign` (`branch_id`),
  ADD KEY `work_losses_vehicle_id_foreign` (`vehicle_id`),
  ADD KEY `work_losses_investigated_by_foreign` (`investigated_by`),
  ADD KEY `work_losses_reported_by_foreign` (`reported_by`),
  ADD KEY `idx_losses_company_branch` (`company_code`,`branch_id`),
  ADD KEY `idx_losses_job` (`job_id`),
  ADD KEY `idx_losses_shipment` (`shipment_id`),
  ADD KEY `idx_losses_type` (`loss_type`),
  ADD KEY `idx_losses_status` (`status`),
  ADD KEY `idx_losses_reported` (`reported_at`);

--
-- Indexes for table `work_orders`
--
ALTER TABLE `work_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `work_orders_order_number_unique` (`order_number`),
  ADD KEY `work_orders_order_source_index` (`order_source`),
  ADD KEY `work_orders_branch_approved_index` (`branch_approved`),
  ADD KEY `work_orders_requester_response_index` (`requester_response`),
  ADD KEY `work_orders_final_approved_index` (`final_approved`),
  ADD KEY `work_orders_payment_status_index` (`payment_status`),
  ADD KEY `idx_wo_company_branch` (`company_code`,`branch_id`);

--
-- Indexes for table `work_shipments`
--
ALTER TABLE `work_shipments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_job_shipment` (`job_id`,`shipment_number`),
  ADD KEY `work_shipments_truck_id_foreign` (`truck_id`),
  ADD KEY `work_shipments_pump_id_foreign` (`pump_id`),
  ADD KEY `work_shipments_mixer_driver_id_foreign` (`mixer_driver_id`),
  ADD KEY `work_shipments_truck_driver_id_foreign` (`truck_driver_id`),
  ADD KEY `work_shipments_pump_driver_id_foreign` (`pump_driver_id`),
  ADD KEY `work_shipments_created_by_foreign` (`created_by`),
  ADD KEY `idx_shipments_status` (`status`),
  ADD KEY `idx_shipments_mixer` (`mixer_id`),
  ADD KEY `idx_shipments_departure` (`departure_time`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts_type`
--
ALTER TABLE `accounts_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `account_balances`
--
ALTER TABLE `account_balances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `advances`
--
ALTER TABLE `advances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `advance_payments`
--
ALTER TABLE `advance_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `advance_settings`
--
ALTER TABLE `advance_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `backups`
--
ALTER TABLE `backups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cars_types`
--
ALTER TABLE `cars_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `car_drivers`
--
ALTER TABLE `car_drivers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `car_maintenances`
--
ALTER TABLE `car_maintenances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cash_registers`
--
ALTER TABLE `cash_registers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `checks`
--
ALTER TABLE `checks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `check_status_logs`
--
ALTER TABLE `check_status_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chemicals`
--
ALTER TABLE `chemicals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `company_payment_cards`
--
ALTER TABLE `company_payment_cards`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `company_payment_card_transactions`
--
ALTER TABLE `company_payment_card_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_subscriptions`
--
ALTER TABLE `company_subscriptions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company_subscription_prices`
--
ALTER TABLE `company_subscription_prices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `concrete_mixes`
--
ALTER TABLE `concrete_mixes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `concrete_mix_category_prices`
--
ALTER TABLE `concrete_mix_category_prices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `concrete_mix_chemicals`
--
ALTER TABLE `concrete_mix_chemicals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `concrete_mix_prices`
--
ALTER TABLE `concrete_mix_prices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contractors`
--
ALTER TABLE `contractors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contractor_accounts`
--
ALTER TABLE `contractor_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contractor_checks`
--
ALTER TABLE `contractor_checks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contractor_check_status_history`
--
ALTER TABLE `contractor_check_status_history`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contractor_invoices`
--
ALTER TABLE `contractor_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contractor_receipts`
--
ALTER TABLE `contractor_receipts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customer_payments`
--
ALTER TABLE `customer_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer_payment_records`
--
ALTER TABLE `customer_payment_records`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `daily_cash_summaries`
--
ALTER TABLE `daily_cash_summaries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `driver_assignments`
--
ALTER TABLE `driver_assignments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_bonuses`
--
ALTER TABLE `employee_bonuses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_leaves`
--
ALTER TABLE `employee_leaves`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_shifts`
--
ALTER TABLE `employee_shifts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `employee_types`
--
ALTER TABLE `employee_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `financial_accounts`
--
ALTER TABLE `financial_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `financial_transactions`
--
ALTER TABLE `financial_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventories`
--
ALTER TABLE `inventories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `inventory_histories`
--
ALTER TABLE `inventory_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `inventory_losses`
--
ALTER TABLE `inventory_losses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `location_logs`
--
ALTER TABLE `location_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_records`
--
ALTER TABLE `maintenance_records`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_components`
--
ALTER TABLE `material_components`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_equipment`
--
ALTER TABLE `material_equipment`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `material_reservations`
--
ALTER TABLE `material_reservations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `measurement_units`
--
ALTER TABLE `measurement_units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=180;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notification_logs`
--
ALTER TABLE `notification_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_settings`
--
ALTER TABLE `notification_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_templates`
--
ALTER TABLE `notification_templates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_executions`
--
ALTER TABLE `order_executions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_histories`
--
ALTER TABLE `order_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_negotiations`
--
ALTER TABLE `order_negotiations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_price_changes`
--
ALTER TABLE `order_price_changes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_stages`
--
ALTER TABLE `order_stages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_timeline`
--
ALTER TABLE `order_timeline`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `page_seo_settings`
--
ALTER TABLE `page_seo_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_cards`
--
ALTER TABLE `payment_cards`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment_card_transactions`
--
ALTER TABLE `payment_card_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payment_receipts`
--
ALTER TABLE `payment_receipts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_vouchers`
--
ALTER TABLE `payment_vouchers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pricing_categories`
--
ALTER TABLE `pricing_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `public_contact_channels`
--
ALTER TABLE `public_contact_channels`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `public_contact_settings`
--
ALTER TABLE `public_contact_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `public_display_blocks`
--
ALTER TABLE `public_display_blocks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `public_display_videos`
--
ALTER TABLE `public_display_videos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `request_status`
--
ALTER TABLE `request_status`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `salary_adjustments`
--
ALTER TABLE `salary_adjustments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seo_settings`
--
ALTER TABLE `seo_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `shift_times`
--
ALTER TABLE `shift_times`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `shipment_events`
--
ALTER TABLE `shipment_events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscription_history`
--
ALTER TABLE `subscription_history`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subscription_invoices`
--
ALTER TABLE `subscription_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subscription_pricing`
--
ALTER TABLE `subscription_pricing`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `usertype`
--
ALTER TABLE `usertype`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_shifts`
--
ALTER TABLE `user_shifts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vehicle_drivers`
--
ALTER TABLE `vehicle_drivers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicle_reservations`
--
ALTER TABLE `vehicle_reservations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicle_status_history`
--
ALTER TABLE `vehicle_status_history`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `work_jobs`
--
ALTER TABLE `work_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `work_losses`
--
ALTER TABLE `work_losses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `work_orders`
--
ALTER TABLE `work_orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `work_shipments`
--
ALTER TABLE `work_shipments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_balances`
--
ALTER TABLE `account_balances`
  ADD CONSTRAINT `account_balances_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `advances`
--
ALTER TABLE `advances`
  ADD CONSTRAINT `advances_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `advances_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `advances_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `advances_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `advances_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `car_drivers`
--
ALTER TABLE `car_drivers`
  ADD CONSTRAINT `car_drivers_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `car_maintenances`
--
ALTER TABLE `car_maintenances`
  ADD CONSTRAINT `car_maintenances_company_payment_card_id_foreign` FOREIGN KEY (`company_payment_card_id`) REFERENCES `company_payment_cards` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `company_payment_cards`
--
ALTER TABLE `company_payment_cards`
  ADD CONSTRAINT `company_payment_cards_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `company_payment_cards_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `company_payment_card_transactions`
--
ALTER TABLE `company_payment_card_transactions`
  ADD CONSTRAINT `cpct_branch_id_fk` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cpct_card_id_fk` FOREIGN KEY (`company_payment_card_id`) REFERENCES `company_payment_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cpct_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD CONSTRAINT `customer_payments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_payments_company_payment_card_id_foreign` FOREIGN KEY (`company_payment_card_id`) REFERENCES `company_payment_cards` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customer_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customer_payments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customer_payments_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_payment_records`
--
ALTER TABLE `customer_payment_records`
  ADD CONSTRAINT `customer_payment_records_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_payment_records_company_payment_card_id_foreign` FOREIGN KEY (`company_payment_card_id`) REFERENCES `company_payment_cards` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customer_payment_records_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customer_payment_records_customer_payment_id_foreign` FOREIGN KEY (`customer_payment_id`) REFERENCES `customer_payments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_losses`
--
ALTER TABLE `inventory_losses`
  ADD CONSTRAINT `inventory_losses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_losses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

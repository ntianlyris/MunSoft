-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2026 at 06:18 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `munsoft_polanco`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins_tbl`
--

CREATE TABLE `admins_tbl` (
  `adminID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins_tbl`
--

INSERT INTO `admins_tbl` (`adminID`, `userID`, `employee_id`, `status`) VALUES
(2, 10, 3, 'active'),
(3, 11, 3, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `config_deductions`
--

CREATE TABLE `config_deductions` (
  `config_deduction_id` int(11) NOT NULL,
  `deduction_type_id` int(11) NOT NULL,
  `deduct_acct_code` varchar(20) NOT NULL,
  `deduct_code` varchar(20) NOT NULL,
  `deduct_title` varchar(100) NOT NULL,
  `is_employee_share` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0-false, 1-true',
  `deduct_category` enum('STATUTORY','LOAN','OTHER') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `config_earnings`
--

CREATE TABLE `config_earnings` (
  `config_earning_id` int(11) NOT NULL,
  `earning_code` enum('Sal-Reg','Sal-Cas','PERA','RA','TA','SUB','HZD','LNDRY') NOT NULL,
  `earning_title` varchar(50) NOT NULL,
  `earning_acct_code` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `config_earnings`
--

INSERT INTO `config_earnings` (`config_earning_id`, `earning_code`, `earning_title`, `earning_acct_code`) VALUES
(1, 'Sal-Reg', 'Salaries - Regular', '50101010'),
(2, 'Sal-Cas', 'Salaries - Casual', '50101020'),
(3, 'PERA', 'Personal Economic Relief Allowance', '50102010'),
(4, 'RA', 'Representation Allowance', '50102020'),
(5, 'TA', 'Transportation Allowance', '50102030'),
(8, 'SUB', 'Subsistence Allowance', '50102050'),
(9, 'HZD', 'Hazard Pay', '50102110'),
(10, 'LNDRY', 'Laundry Allowance', '50102060');

-- --------------------------------------------------------

--
-- Table structure for table `deduction_types`
--

CREATE TABLE `deduction_types` (
  `deduction_type_id` int(11) NOT NULL,
  `deduction_type_name` varchar(100) NOT NULL,
  `deduction_type_code` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deduction_types`
--

INSERT INTO `deduction_types` (`deduction_type_id`, `deduction_type_name`, `deduction_type_code`, `created_at`) VALUES
(1, 'BIR', 'BIR', '2025-07-23 02:21:21'),
(2, 'GSIS', 'GSIS', '2025-07-23 02:21:21'),
(3, 'PAG-IBIG', 'PAGIBIG', '2025-07-23 02:21:21'),
(4, 'PhilHealth', 'PHIC', '2025-07-23 02:21:21'),
(5, 'Other Payables', 'OTHERS', '2025-07-23 02:21:21');

-- --------------------------------------------------------

--
-- Table structure for table `departments_tbl`
--

CREATE TABLE `departments_tbl` (
  `dept_id` int(11) NOT NULL,
  `dept_code` varchar(20) NOT NULL,
  `dept_title` varchar(50) NOT NULL,
  `dept_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments_tbl`
--

INSERT INTO `departments_tbl` (`dept_id`, `dept_code`, `dept_title`, `dept_name`) VALUES
(1, '3-01-001', 'MMO', 'Municipal Mayor\'s Office'),
(2, '3-01-002', 'MVO', 'Municipal Vice Mayor\'s Office'),
(3, '3-01-003', 'SBLEG', 'SB Legislative'),
(4, '3-01-004', 'SBSEC', 'SB Secretariat'),
(5, '3-01-005', 'MTO', 'Municipal Treasurer\'s Office'),
(6, '3-01-006', 'MASSO', 'Municipal Assessor\'s Office'),
(7, '3-01-007', 'MACCTG', 'Municipal Accountant\'s Office'),
(8, '3-01-008', 'MBO', 'Municipal Budget Officer\'s Office'),
(9, '3-01-009', 'MPDO', 'Municipal Planning'),
(10, '3-01-010', 'MEO', 'Municipal Engineer\'s Office'),
(11, '3-01-011', 'MHO', 'Municipal Health Officer\'s Office'),
(12, '3-01-012', 'MCR', 'Municipal Civil Registrar\'s Office'),
(13, '3-02-001', 'MADMIN', 'Municipal Administrator\'s Office'),
(14, '3-02-003', 'MAO', 'Municipal Agriculturist\'s Office'),
(15, '3-02-005', 'MSWDO', 'Municipal Social Welfare'),
(16, '3-03-001', 'MKT', 'Market Operations (MADMIN)');

-- --------------------------------------------------------

--
-- Table structure for table `employees_tbl`
--

CREATE TABLE `employees_tbl` (
  `employee_id` int(11) NOT NULL,
  `userID` varchar(11) NOT NULL,
  `employee_id_num` varchar(20) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `extension` varchar(10) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` varchar(20) NOT NULL,
  `civil_status` varchar(20) NOT NULL,
  `address` varchar(200) NOT NULL,
  `prof_expertise` varchar(50) NOT NULL,
  `hire_date` date NOT NULL,
  `employment_type` varchar(20) NOT NULL COMMENT 'regular, non-regular',
  `employee_status` varchar(50) NOT NULL,
  `tin` varchar(20) NOT NULL,
  `gsis_bp` varchar(20) NOT NULL,
  `philhealth_no` varchar(20) NOT NULL,
  `pagibig_mid` varchar(20) NOT NULL,
  `sss_no` varchar(20) NOT NULL,
  `include_in_payroll` enum('1','0') DEFAULT '1' COMMENT '1 = included, 0 = excluded'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_deductions`
--

CREATE TABLE `employee_deductions` (
  `employee_deduction_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `total_deduction` decimal(10,2) NOT NULL,
  `effective_date` date NOT NULL,
  `deduction_particulars` varchar(50) NOT NULL,
  `end_date` date DEFAULT NULL,
  `date_created` date NOT NULL,
  `date_updated` date NOT NULL,
  `is_frozen` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_deductions_components`
--

CREATE TABLE `employee_deductions_components` (
  `deduction_component_id` int(11) NOT NULL,
  `employee_deduction_id` int(11) NOT NULL,
  `config_deduction_id` int(11) NOT NULL,
  `deduction_comp_amt` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_earnings`
--

CREATE TABLE `employee_earnings` (
  `employee_earning_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `employment_id` int(11) NOT NULL,
  `locked_rate` decimal(10,2) NOT NULL,
  `gross_amount` decimal(10,2) NOT NULL,
  `earning_particulars` varchar(50) NOT NULL,
  `effective_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `date_created` date NOT NULL,
  `date_updated` date NOT NULL,
  `is_frozen` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_earnings_components`
--

CREATE TABLE `employee_earnings_components` (
  `earning_component_id` int(11) NOT NULL,
  `employee_earning_id` int(11) NOT NULL,
  `config_earning_id` int(11) NOT NULL,
  `earning_comp_amt` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_employments_tbl`
--

CREATE TABLE `employee_employments_tbl` (
  `employment_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `employment_refnum` varchar(20) NOT NULL,
  `employment_type` varchar(50) NOT NULL,
  `employment_start` date NOT NULL,
  `employment_end` date DEFAULT NULL,
  `position_id` varchar(11) NOT NULL,
  `dept_assigned` varchar(11) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `work_nature` varchar(100) NOT NULL,
  `work_specifics` varchar(100) NOT NULL,
  `rate` decimal(20,2) NOT NULL,
  `empSalaryID` int(11) NOT NULL,
  `employment_particulars` varchar(50) NOT NULL,
  `employment_status` varchar(11) NOT NULL COMMENT 'active = current employment inactive = previous employment'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_govshares`
--

CREATE TABLE `employee_govshares` (
  `employee_govshare_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `govshare_id` int(11) NOT NULL,
  `govshare_amount` decimal(10,2) NOT NULL,
  `employee_rate` decimal(10,2) NOT NULL,
  `effective_start` date NOT NULL,
  `effective_end` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_leave_balances`
--

CREATE TABLE `employee_leave_balances` (
  `emp_leave_bal_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `allotted` int(11) DEFAULT 0,
  `used` int(11) DEFAULT 0,
  `remaining` int(11) DEFAULT 0,
  `accumulated` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `carried` decimal(6,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_leave_balances`
--

INSERT INTO `employee_leave_balances` (`emp_leave_bal_id`, `employee_id`, `leave_type_id`, `year`, `allotted`, `used`, `remaining`, `accumulated`, `created_at`, `updated_at`, `carried`) VALUES
(1, 7, 1, 2025, 20, 0, 20, 0, '2025-10-03 07:32:52', '2025-10-03 07:32:52', 0.00),
(2, 7, 2, 2025, 3, 0, 3, 0, '2025-10-03 07:32:52', '2025-10-03 07:32:52', 0.00),
(3, 7, 7, 2025, 5, 0, 5, 0, '2025-10-03 07:32:52', '2025-10-03 07:32:52', 0.00),
(4, 7, 8, 2025, 0, 0, 0, 0, '2025-10-03 07:32:52', '2025-10-03 07:32:52', 0.00),
(5, 7, 9, 2025, 0, 0, 0, 0, '2025-10-03 08:25:23', '2025-10-03 08:25:23', 0.00),
(6, 7, 10, 2025, 120, 0, 120, 0, '2025-10-03 08:25:23', '2025-10-03 08:25:23', 0.00),
(7, 14, 1, 2025, 20, 0, 20, 0, '2025-10-06 01:24:47', '2025-10-06 01:24:47', 0.00),
(8, 14, 2, 2025, 3, 0, 3, 0, '2025-10-06 01:24:47', '2025-10-06 01:24:47', 0.00),
(9, 14, 7, 2025, 5, 0, 5, 0, '2025-10-06 01:24:47', '2025-10-06 01:24:47', 0.00),
(10, 14, 8, 2025, 0, 0, 0, 0, '2025-10-06 01:24:47', '2025-10-06 01:24:47', 0.00),
(11, 14, 9, 2025, 0, 0, 0, 0, '2025-10-06 01:24:47', '2025-10-06 01:24:47', 0.00),
(12, 14, 10, 2025, 120, 0, 120, 0, '2025-10-06 01:24:47', '2025-10-06 01:24:47', 0.00),
(13, 11, 1, 2025, 0, 0, 0, 0, '2025-10-07 04:05:51', '2025-10-07 04:05:51', 0.00),
(14, 11, 2, 2025, 3, 0, 3, 0, '2025-10-07 04:05:51', '2025-10-07 04:05:51', 0.00),
(15, 11, 7, 2025, 5, 0, 5, 0, '2025-10-07 04:05:51', '2025-10-07 04:05:51', 0.00),
(16, 11, 8, 2025, 0, 0, 0, 0, '2025-10-07 04:05:51', '2025-10-07 04:05:51', 0.00),
(17, 11, 9, 2025, 5, 0, 5, 0, '2025-10-07 04:05:51', '2025-10-07 04:05:51', 0.00),
(18, 11, 10, 2025, 180, 0, 180, 0, '2025-10-07 04:05:51', '2025-10-07 04:05:51', 0.00),
(19, 3, 1, 2025, 0, 0, 0, 0, '2025-10-07 08:25:44', '2025-10-07 08:25:44', 0.00),
(20, 3, 2, 2025, 3, 0, 3, 0, '2025-10-07 08:25:44', '2025-10-07 08:25:44', 0.00),
(21, 3, 7, 2025, 5, 3, 2, 0, '2025-10-07 08:25:44', '2025-10-09 02:11:06', 0.00),
(22, 3, 8, 2025, 0, 0, 0, 0, '2025-10-07 08:25:44', '2025-10-07 08:25:44', 0.00),
(23, 3, 9, 2025, 5, 0, 5, 0, '2025-10-07 08:25:44', '2025-10-07 08:25:44', 0.00),
(24, 3, 10, 2025, 180, 0, 180, 0, '2025-10-07 08:25:44', '2025-10-07 08:25:44', 0.00),
(25, 7, 11, 2025, 7, 0, 7, 0, '2025-10-07 08:51:14', '2025-10-07 08:51:14', 0.00),
(26, 3, 11, 2025, 7, 0, 7, 0, '2025-10-07 08:51:30', '2025-10-07 08:51:30', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `employee_leave_transactions`
--

CREATE TABLE `employee_leave_transactions` (
  `emp_lv_trans_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `txn_date` date NOT NULL,
  `txn_type` enum('accrual','carryover','use','adjustment','reset') NOT NULL,
  `amount` decimal(6,2) NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `empsalaries_tbl`
--

CREATE TABLE `empsalaries_tbl` (
  `empSalaryID` int(11) NOT NULL,
  `monthly_rate` decimal(20,2) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `dateEffective` date NOT NULL,
  `salaryParticulars` varchar(50) NOT NULL,
  `salaryDescription` varchar(100) NOT NULL,
  `salaryStatus` varchar(20) NOT NULL COMMENT 'current,non_current'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `govshares`
--

CREATE TABLE `govshares` (
  `govshare_id` int(11) NOT NULL,
  `deduction_type_id` int(11) NOT NULL,
  `govshare_name` varchar(100) NOT NULL,
  `govshare_code` enum('L_R','HDMF','PHIC','ECC') NOT NULL,
  `govshare_acctcode` varchar(20) NOT NULL,
  `govshare_rate` decimal(5,2) NOT NULL,
  `is_percentage` tinyint(1) DEFAULT 1,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_applications`
--

CREATE TABLE `leave_applications` (
  `leave_application_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `dates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`dates`)),
  `days` int(3) NOT NULL,
  `reason` varchar(100) NOT NULL,
  `attach_path` varchar(200) NOT NULL,
  `status` enum('Pending','Approved','Disapproved','') NOT NULL,
  `date_filed` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_applications`
--

INSERT INTO `leave_applications` (`leave_application_id`, `employee_id`, `leave_type_id`, `dates`, `days`, `reason`, `attach_path`, `status`, `date_filed`) VALUES
(1, 3, 7, '[\"2025-10-13\",\"2025-10-14\",\"2025-10-31\"]', 0, 'Kapoy', '', 'Approved', '2025-10-09'),
(2, 3, 7, '[\"2025-11-01\",\"2025-11-02\",\"2025-11-03\"]', 0, 'fsfafa', '', 'Pending', '2025-10-09');

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `leave_type_id` int(11) NOT NULL,
  `leave_code` varchar(10) NOT NULL,
  `leave_name` varchar(100) NOT NULL,
  `yearly_allotment` int(11) DEFAULT 0,
  `monthly_accrual` tinyint(1) DEFAULT 0,
  `is_accumulative` tinyint(1) DEFAULT 0,
  `max_accumulation` int(11) DEFAULT NULL,
  `gender_restriction` enum('all','male','female') DEFAULT 'all',
  `reset_policy` enum('reset','carry_over','none') DEFAULT 'reset',
  `requires_attachment` tinyint(1) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `frequency_limit` varchar(50) DEFAULT NULL,
  `description` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`leave_type_id`, `leave_code`, `leave_name`, `yearly_allotment`, `monthly_accrual`, `is_accumulative`, `max_accumulation`, `gender_restriction`, `reset_policy`, `requires_attachment`, `active`, `frequency_limit`, `description`, `created_at`) VALUES
(1, 'VL', 'Vacation Leave', 0, 1, 1, 0, 'all', 'carry_over', 0, 1, '', 'Earned 1 day per 24 days service (~1.25 days/month). Can be carried over; commutable (monetized) upon separation/retirement.', '2025-09-28 06:14:39'),
(2, 'SL', 'Special Leave', 3, 0, 0, 0, 'all', 'reset', 0, 1, '3', 'For Special occasions of employees.', '2025-09-28 07:33:09'),
(7, 'FL', 'Forced Leave', 5, 0, 0, 0, 'all', 'reset', 0, 1, '5 per year', '5 days per year. Must be used annually; forfeited if unused. Exemptions apply (e.g., if balance is <5 VL credits).', '2025-09-28 08:05:39'),
(8, 'SL', 'Sick Leave', 0, 1, 1, 0, 'all', 'carry_over', 0, 1, '', 'Earned 1 day per 24 days service (~1.25 days/month). Same accrual as VL; can be carried over; monetizable.', '2025-09-28 08:06:16'),
(9, 'CL', 'Calamity Leave', 5, 0, 0, 0, 'all', 'reset', 0, 1, '5', 'Up to 5 days per year. For natural calamities/disasters; resets annually.', '2025-10-03 08:23:36'),
(10, 'STDL', 'Study Leave', 180, 0, 0, 0, 'all', 'reset', 0, 1, '', 'Max 6 months (with pay). For master’s bar/board review or other approved study; not annual, granted once every 5 years.', '2025-10-03 08:25:13'),
(11, 'SPL', 'Solo Parent Leave', 7, 0, 0, 0, 'all', 'reset', 0, 1, '7', '7 days per year. Non-accumulative; resets every year.', '2025-10-07 08:50:59');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_deductions`
--

CREATE TABLE `payroll_deductions` (
  `payroll_deduction_id` int(11) NOT NULL,
  `payroll_entry_id` int(11) NOT NULL,
  `deduction_component_id` int(11) NOT NULL,
  `payroll_deduct_amount` decimal(10,2) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_entries`
--

CREATE TABLE `payroll_entries` (
  `payroll_entry_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `employment_id` int(11) NOT NULL,
  `payroll_period_id` int(11) NOT NULL,
  `locked_period` tinyint(1) NOT NULL COMMENT '0=unlocked, 1=locked',
  `dept_id` int(11) NOT NULL,
  `locked_basic` decimal(10,2) NOT NULL,
  `gross` decimal(10,2) DEFAULT 0.00,
  `total_deductions` decimal(10,2) DEFAULT 0.00,
  `net_pay` decimal(10,2) DEFAULT 0.00,
  `earnings_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`earnings_breakdown`)),
  `deductions_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`deductions_breakdown`)),
  `govshares_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `emp_type_stamp` enum('Regular','Casual') NOT NULL COMMENT 'These values are from the employee_employments, to separate payrolls of the two ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_frequencies`
--

CREATE TABLE `payroll_frequencies` (
  `payroll_freq_id` int(11) NOT NULL,
  `freq_code` enum('monthly','semi-monthly') NOT NULL DEFAULT 'monthly',
  `freq_label` varchar(50) NOT NULL COMMENT 'Monthly, Semi-monthly',
  `is_active` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=active, 0=inactive ',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll_frequencies`
--

INSERT INTO `payroll_frequencies` (`payroll_freq_id`, `freq_code`, `freq_label`, `is_active`, `created_at`) VALUES
(1, 'monthly', 'Monthly', 0, '2025-07-26 17:22:41'),
(2, 'semi-monthly', 'Semi-monthly', 1, '2025-07-26 17:22:41');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_govshares`
--

CREATE TABLE `payroll_govshares` (
  `payroll_govshare_id` int(11) NOT NULL,
  `payroll_entry_id` int(11) NOT NULL,
  `employee_govshare_id` int(11) NOT NULL,
  `payroll_govshare_amount` decimal(10,2) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_periods`
--

CREATE TABLE `payroll_periods` (
  `payroll_period_id` int(11) NOT NULL,
  `period_label` varchar(50) DEFAULT NULL COMMENT 'e.g. "July 16–31, 2025"',
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `frequency` enum('monthly','semi-monthly') DEFAULT 'semi-monthly',
  `is_locked` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_year_controls`
--

CREATE TABLE `payroll_year_controls` (
  `payroll_year_control_id` int(11) NOT NULL,
  `year` int(4) NOT NULL,
  `is_closed` tinyint(1) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll_year_controls`
--

INSERT INTO `payroll_year_controls` (`payroll_year_control_id`, `year`, `is_closed`, `updated_at`) VALUES
(1, 2024, 1, '2025-07-27 08:42:54'),
(3, 2025, 1, '2026-02-23 13:22:49');

-- --------------------------------------------------------

--
-- Table structure for table `permissions_tbl`
--

CREATE TABLE `permissions_tbl` (
  `perm_id` int(11) NOT NULL,
  `perm_desc` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions_tbl`
--

INSERT INTO `permissions_tbl` (`perm_id`, `perm_desc`) VALUES
(1, 'Manage System'),
(2, 'Update Data');

-- --------------------------------------------------------

--
-- Table structure for table `positions_tbl`
--

CREATE TABLE `positions_tbl` (
  `position_id` int(11) NOT NULL,
  `position_refnum` varchar(20) NOT NULL,
  `position_itemnum` varchar(50) NOT NULL,
  `position_title` varchar(100) NOT NULL,
  `salary_grade` int(2) NOT NULL,
  `position_type` varchar(50) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `position_status` varchar(20) NOT NULL COMMENT '0=vacant,1=filled,2=unfunded'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remittances`
--

CREATE TABLE `remittances` (
  `remittance_id` int(10) UNSIGNED NOT NULL,
  `remittance_type` varchar(50) NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `employee_totals` decimal(10,2) NOT NULL,
  `employer_totals` decimal(10,2) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('Pending','Remitted') NOT NULL DEFAULT 'Pending',
  `or_number` varchar(50) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remittance_details`
--

CREATE TABLE `remittance_details` (
  `remit_detail_id` int(10) UNSIGNED NOT NULL,
  `remittance_id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) NOT NULL,
  `config_deduction_id` int(11) NOT NULL,
  `govshare_id` int(11) NOT NULL,
  `remittance_type` varchar(50) NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `govshare_amt` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles_tbl`
--

CREATE TABLE `roles_tbl` (
  `roleID` int(1) NOT NULL,
  `roleName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles_tbl`
--

INSERT INTO `roles_tbl` (`roleID`, `roleName`) VALUES
(1, 'Administrator'),
(2, 'Updater'),
(3, 'Employee'),
(4, 'HR'),
(5, 'Payroll Master');

-- --------------------------------------------------------

--
-- Table structure for table `role_perm_tbl`
--

CREATE TABLE `role_perm_tbl` (
  `role_perm_id` int(11) NOT NULL,
  `roleID` int(11) NOT NULL,
  `perm_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_perm_tbl`
--

INSERT INTO `role_perm_tbl` (`role_perm_id`, `roleID`, `perm_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 2),
(4, 3, 2),
(5, 4, 2),
(7, 5, 2),
(8, 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `signatories`
--

CREATE TABLE `signatories` (
  `signatory_id` int(11) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `position_title` varchar(150) DEFAULT NULL,
  `role_type` enum('HEAD','MAYOR','TREASURER','ACCOUNTANT','DISBURSING','PREPARATION') DEFAULT NULL,
  `dept_id` int(11) NOT NULL,
  `report_type` enum('PAYROLL','REMITTANCE','JOURNAL','ACCTG') NOT NULL,
  `sign_order` varchar(2) DEFAULT NULL,
  `sign_particulars` varchar(500) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `signatories`
--

INSERT INTO `signatories` (`signatory_id`, `full_name`, `position_title`, `role_type`, `dept_id`, `report_type`, `sign_order`, `sign_particulars`, `created_at`, `updated_at`, `is_active`) VALUES
(3, 'SHAIA RUTH R. UY', 'Municipal Mayor', 'MAYOR', 0, 'PAYROLL', '3', 'APPROVED FOR PAYMENT:', '2025-11-01 14:44:34', '2025-11-01 15:17:53', 1),
(4, 'PROSERPHINE G. GODINEZ, CPA', 'Municipal Accountant', 'ACCOUNTANT', 0, 'PAYROLL', '5', 'CERTIFIED: As to the completeness of supporting documents', '2025-11-01 14:45:01', '2025-11-01 16:28:08', 0),
(5, 'ARVIN C. CALAMBA', 'Municipal Treasurer', 'TREASURER', 0, 'PAYROLL', '2', 'CERTIFIED: Funds available in the amount of Php _____________', '2025-11-01 14:46:04', '2025-11-12 22:27:35', 1),
(6, 'ANNA KARENE E. FERNANDEZ', 'Cashier II', 'DISBURSING', 0, 'PAYROLL', '4', 'CERTIFIED: Each employee whose name appears above has\r\nbeen paid the amount indicated opposite his/her name', '2025-11-01 14:46:59', '2025-11-01 15:18:41', 1),
(8, 'PROSERPHINE G. GODINEZ, CPA', 'Municipal Accountant', 'HEAD', 7, 'PAYROLL', '1', 'CERTIFIED: Services have been duly rendered as stated', '2025-11-01 15:15:57', '2025-11-02 09:28:41', 1),
(14, 'HERNIBETH C. OTUD', 'Administrative Officer IV', 'PREPARATION', 7, 'JOURNAL', '1', 'Prepared by:', '2025-11-07 23:21:17', NULL, 1),
(15, 'PROSERPHINE G. GODINEZ, CPA', 'Municipal Accountant', 'ACCOUNTANT', 0, 'JOURNAL', '2', 'Approved by:', '2025-11-07 23:22:26', NULL, 1),
(16, 'PROSERPHINE G. GODINEZ, CPA', 'Municipal Accountant', 'ACCOUNTANT', 0, 'ACCTG', '2', 'Certified correct:', '2025-11-09 13:31:13', NULL, 1),
(17, 'HERNIBETH C. OTUD', 'Administrative Officer IV', 'PREPARATION', 0, 'ACCTG', '1', 'Prepared by:', '2025-11-09 13:31:42', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users_tbl`
--

CREATE TABLE `users_tbl` (
  `userID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `mobile` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_tbl`
--

INSERT INTO `users_tbl` (`userID`, `username`, `password`, `email`, `mobile`) VALUES
(1, 'admin', '$2y$10$rZn8vkQCyfNTEGFUPKJiOuryLoy3NtmZLwCVO6sLo5.S7FEDULHDm', '', '09123456789'),
(7, 'ntian', '$2y$10$D.wysPTZDpuoTwgBCkHpYOrf31M5uAvryFJoCciXdnrUfPlfQFfdm', '', '09123456789'),
(8, 'viceral', '$2y$10$tJKKX9ZScv2znhGLrrf70.J5MA7RHtNdTfocFnJJgcnkRg90Nk99a', '', '09123456789'),
(9, 'jongskie', '$2y$10$TsF.dqglID897d8CP67iHuiTbHX8qYZ6qgnAQwAbErG8La9tHn5eS', '', '09123456789'),
(10, 'PayMaster', '$2y$10$5iofkqvN7W/.XTzUMENeH.FCpTuGvM60ZGsLwibXe6HZEeayHbXau', '', '09123456789'),
(11, 'hrmo', '$2y$10$GqPpldpPT/ldmqAjIK08Bew/qc4HwwBAbTcyzF/YxeCPNh1sm/1Dy', '', '09123456789');

-- --------------------------------------------------------

--
-- Table structure for table `user_role_tbl`
--

CREATE TABLE `user_role_tbl` (
  `user_role_id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `roleID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_role_tbl`
--

INSERT INTO `user_role_tbl` (`user_role_id`, `userID`, `roleID`) VALUES
(1, 1, 1),
(7, 7, 3),
(8, 8, 3),
(9, 9, 3),
(10, 10, 5),
(11, 11, 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins_tbl`
--
ALTER TABLE `admins_tbl`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `config_deductions`
--
ALTER TABLE `config_deductions`
  ADD PRIMARY KEY (`config_deduction_id`);

--
-- Indexes for table `config_earnings`
--
ALTER TABLE `config_earnings`
  ADD PRIMARY KEY (`config_earning_id`);

--
-- Indexes for table `deduction_types`
--
ALTER TABLE `deduction_types`
  ADD PRIMARY KEY (`deduction_type_id`),
  ADD UNIQUE KEY `code` (`deduction_type_code`);

--
-- Indexes for table `departments_tbl`
--
ALTER TABLE `departments_tbl`
  ADD PRIMARY KEY (`dept_id`);

--
-- Indexes for table `employees_tbl`
--
ALTER TABLE `employees_tbl`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  ADD PRIMARY KEY (`employee_deduction_id`);

--
-- Indexes for table `employee_deductions_components`
--
ALTER TABLE `employee_deductions_components`
  ADD PRIMARY KEY (`deduction_component_id`);

--
-- Indexes for table `employee_earnings`
--
ALTER TABLE `employee_earnings`
  ADD PRIMARY KEY (`employee_earning_id`);

--
-- Indexes for table `employee_earnings_components`
--
ALTER TABLE `employee_earnings_components`
  ADD PRIMARY KEY (`earning_component_id`);

--
-- Indexes for table `employee_employments_tbl`
--
ALTER TABLE `employee_employments_tbl`
  ADD PRIMARY KEY (`employment_id`);

--
-- Indexes for table `employee_govshares`
--
ALTER TABLE `employee_govshares`
  ADD PRIMARY KEY (`employee_govshare_id`);

--
-- Indexes for table `employee_leave_balances`
--
ALTER TABLE `employee_leave_balances`
  ADD PRIMARY KEY (`emp_leave_bal_id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`,`leave_type_id`,`year`);

--
-- Indexes for table `employee_leave_transactions`
--
ALTER TABLE `employee_leave_transactions`
  ADD PRIMARY KEY (`emp_lv_trans_id`);

--
-- Indexes for table `empsalaries_tbl`
--
ALTER TABLE `empsalaries_tbl`
  ADD PRIMARY KEY (`empSalaryID`);

--
-- Indexes for table `govshares`
--
ALTER TABLE `govshares`
  ADD PRIMARY KEY (`govshare_id`);

--
-- Indexes for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD PRIMARY KEY (`leave_application_id`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`leave_type_id`);

--
-- Indexes for table `payroll_deductions`
--
ALTER TABLE `payroll_deductions`
  ADD PRIMARY KEY (`payroll_deduction_id`);

--
-- Indexes for table `payroll_entries`
--
ALTER TABLE `payroll_entries`
  ADD PRIMARY KEY (`payroll_entry_id`),
  ADD UNIQUE KEY `unique_entry` (`employee_id`,`payroll_period_id`),
  ADD KEY `employment_id` (`employment_id`);

--
-- Indexes for table `payroll_frequencies`
--
ALTER TABLE `payroll_frequencies`
  ADD PRIMARY KEY (`payroll_freq_id`);

--
-- Indexes for table `payroll_govshares`
--
ALTER TABLE `payroll_govshares`
  ADD PRIMARY KEY (`payroll_govshare_id`);

--
-- Indexes for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  ADD PRIMARY KEY (`payroll_period_id`),
  ADD UNIQUE KEY `uniq_period` (`date_start`,`date_end`);

--
-- Indexes for table `payroll_year_controls`
--
ALTER TABLE `payroll_year_controls`
  ADD PRIMARY KEY (`payroll_year_control_id`),
  ADD UNIQUE KEY `year` (`year`);

--
-- Indexes for table `positions_tbl`
--
ALTER TABLE `positions_tbl`
  ADD PRIMARY KEY (`position_id`);

--
-- Indexes for table `remittances`
--
ALTER TABLE `remittances`
  ADD PRIMARY KEY (`remittance_id`),
  ADD UNIQUE KEY `uk_type_period` (`remittance_type`,`period_start`,`period_end`),
  ADD KEY `idx_type_status_period` (`remittance_type`,`status`,`period_start`,`period_end`);

--
-- Indexes for table `remittance_details`
--
ALTER TABLE `remittance_details`
  ADD PRIMARY KEY (`remit_detail_id`),
  ADD UNIQUE KEY `unique_remit_detail` (`remittance_id`,`employee_id`,`remittance_type`),
  ADD KEY `idx_remittance_employee` (`remittance_id`,`employee_id`),
  ADD KEY `fk_remittance_details_employee` (`employee_id`);

--
-- Indexes for table `roles_tbl`
--
ALTER TABLE `roles_tbl`
  ADD PRIMARY KEY (`roleID`);

--
-- Indexes for table `role_perm_tbl`
--
ALTER TABLE `role_perm_tbl`
  ADD PRIMARY KEY (`role_perm_id`);

--
-- Indexes for table `signatories`
--
ALTER TABLE `signatories`
  ADD PRIMARY KEY (`signatory_id`);

--
-- Indexes for table `users_tbl`
--
ALTER TABLE `users_tbl`
  ADD PRIMARY KEY (`userID`);

--
-- Indexes for table `user_role_tbl`
--
ALTER TABLE `user_role_tbl`
  ADD PRIMARY KEY (`user_role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins_tbl`
--
ALTER TABLE `admins_tbl`
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `config_deductions`
--
ALTER TABLE `config_deductions`
  MODIFY `config_deduction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config_earnings`
--
ALTER TABLE `config_earnings`
  MODIFY `config_earning_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `deduction_types`
--
ALTER TABLE `deduction_types`
  MODIFY `deduction_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `departments_tbl`
--
ALTER TABLE `departments_tbl`
  MODIFY `dept_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `employees_tbl`
--
ALTER TABLE `employees_tbl`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  MODIFY `employee_deduction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_deductions_components`
--
ALTER TABLE `employee_deductions_components`
  MODIFY `deduction_component_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_earnings`
--
ALTER TABLE `employee_earnings`
  MODIFY `employee_earning_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_earnings_components`
--
ALTER TABLE `employee_earnings_components`
  MODIFY `earning_component_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_employments_tbl`
--
ALTER TABLE `employee_employments_tbl`
  MODIFY `employment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_govshares`
--
ALTER TABLE `employee_govshares`
  MODIFY `employee_govshare_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_leave_balances`
--
ALTER TABLE `employee_leave_balances`
  MODIFY `emp_leave_bal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `employee_leave_transactions`
--
ALTER TABLE `employee_leave_transactions`
  MODIFY `emp_lv_trans_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `empsalaries_tbl`
--
ALTER TABLE `empsalaries_tbl`
  MODIFY `empSalaryID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `govshares`
--
ALTER TABLE `govshares`
  MODIFY `govshare_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_applications`
--
ALTER TABLE `leave_applications`
  MODIFY `leave_application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `leave_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payroll_deductions`
--
ALTER TABLE `payroll_deductions`
  MODIFY `payroll_deduction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_entries`
--
ALTER TABLE `payroll_entries`
  MODIFY `payroll_entry_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_frequencies`
--
ALTER TABLE `payroll_frequencies`
  MODIFY `payroll_freq_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payroll_govshares`
--
ALTER TABLE `payroll_govshares`
  MODIFY `payroll_govshare_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  MODIFY `payroll_period_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_year_controls`
--
ALTER TABLE `payroll_year_controls`
  MODIFY `payroll_year_control_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `positions_tbl`
--
ALTER TABLE `positions_tbl`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remittances`
--
ALTER TABLE `remittances`
  MODIFY `remittance_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remittance_details`
--
ALTER TABLE `remittance_details`
  MODIFY `remit_detail_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles_tbl`
--
ALTER TABLE `roles_tbl`
  MODIFY `roleID` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `role_perm_tbl`
--
ALTER TABLE `role_perm_tbl`
  MODIFY `role_perm_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `signatories`
--
ALTER TABLE `signatories`
  MODIFY `signatory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users_tbl`
--
ALTER TABLE `users_tbl`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_role_tbl`
--
ALTER TABLE `user_role_tbl`
  MODIFY `user_role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `remittance_details`
--
ALTER TABLE `remittance_details`
  ADD CONSTRAINT `fk_remittance_details_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees_tbl` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_remittance_details_remittance` FOREIGN KEY (`remittance_id`) REFERENCES `remittances` (`remittance_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

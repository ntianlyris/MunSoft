-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 26, 2026 at 12:46 PM
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

--
-- Dumping data for table `config_deductions`
--

INSERT INTO `config_deductions` (`config_deduction_id`, `deduction_type_id`, `deduct_acct_code`, `deduct_code`, `deduct_title`, `is_employee_share`, `deduct_category`) VALUES
(1, 1, '20201010-1', 'W_TAX', 'Withholding Tax', 0, 'STATUTORY'),
(2, 2, '20201020-1', 'LR_INS', 'Life and Retirement Ins. Contributions', 1, 'STATUTORY'),
(4, 3, '20201030-1', 'PAG_PREM', 'PAG-IBIG Contributions', 1, 'STATUTORY'),
(5, 3, '20201030-2', 'PAG_MPL', 'Multi-Purpose Loan', 0, 'LOAN'),
(8, 5, '29999990-1', 'RRB_LN', 'RRB Salary Loan', 0, 'LOAN'),
(12, 5, '29999990-11', 'LBP_LN', 'LBP Salary Loan', 0, 'LOAN'),
(14, 5, '29999990-3', 'SSS_PREM', 'SSS Contributions', 0, 'OTHER'),
(16, 2, '20201020-6', 'PLREG', 'Policy Loan - Regular', 0, 'LOAN'),
(17, 5, '29999990-5', 'PMGEA_DUES', 'PMGEA Monthly Dues', 0, ''),
(18, 5, '29999990-6', 'PMGEA_LN', 'PMGEA Loan', 0, 'LOAN'),
(19, 2, '20201020-3', 'GFAL', 'GSIS GFAL', 0, 'LOAN'),
(20, 3, '20201030-3', 'MPL_FLEX', 'Multi-Purpose Loan Flex', 0, 'LOAN'),
(21, 4, '20201040', 'PHIC-PREM', 'PhilHealth Contributions', 1, 'STATUTORY'),
(22, 5, '29999990-7', 'POEMCO_LN', 'POEMCO Salary Loan', 0, 'LOAN'),
(23, 5, '29999990-12', 'AIA', 'AIA (Phil-Am Life Ins.)', 0, 'OTHER');

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

--
-- Dumping data for table `employees_tbl`
--

INSERT INTO `employees_tbl` (`employee_id`, `userID`, `employee_id_num`, `firstname`, `middlename`, `lastname`, `extension`, `birthdate`, `gender`, `civil_status`, `address`, `prof_expertise`, `hire_date`, `employment_type`, `employee_status`, `tin`, `gsis_bp`, `philhealth_no`, `pagibig_mid`, `sss_no`, `include_in_payroll`) VALUES
(1, '8', '1111', 'Vice', '', 'Ganda', '', '1991-01-01', 'Male', 'Married', 'Its Showtime', 'TV Host', '0000-00-00', '', '', '132-156-458', '546463132', '13132', '8979463', '787976461', '1'),
(2, '9', '2222', 'Jhong', '', 'Navarro', '', '1901-01-01', 'Male', 'Single', 'Its Showtime', 'Sample Kingkoy', '0000-00-00', '', '', '', '', '', '', '', '1'),
(3, '7', '3333', 'Christian Lyris', 'Calunsag', 'Tagsip', '', '1991-08-28', 'Male', 'Married', 'Pob. South, Polanco, Z.N.', 'Computer Scientist', '2024-12-03', '', 'Active', '282-082-407', '2005536139', '14-202034598-4', '121238416076', '10-1164225-2', '1'),
(5, '', '4444', 'CoCo', '', 'Martin', '', '1991-01-01', 'Male', 'Single', 'Quiapo', 'Drug Lord', '2024-06-01', '', 'Active', '', '', '', '', '', '1'),
(6, '', '5555', 'Syrone', '', 'Tubera', '', '1989-08-21', 'Male', 'Married', 'Dipolog City', 'IT Expert', '2023-02-01', '', 'Active', '', '', '', '', '', '1'),
(7, '', '097210-150', 'Evenie Rose', '', 'Abad', '', '2000-10-13', 'Female', 'Single', 'Villahermosa, Polanco, Z.N.', 'Licensed Professional Teacher', '2025-01-02', '', 'Active', '', '', '', '', '', '1'),
(8, '', '0016', 'Ivo', 'M', 'Mandantes', '', '1991-01-01', 'Male', 'Married', 'Isis, Polanco, Z.N.', 'Attorney', '2025-07-01', '', 'Active', '', '', '', '', '', '1'),
(9, '', '2025-111', 'Caryl', '', 'Eluna', '', '1999-08-08', 'Female', 'Single', 'Pob. North, Polanco, Z.N.', 'Financial Analyst/Teacher', '2025-07-01', '', 'Active', '', '', '', '', '', '1'),
(10, '', '999', 'Hernibeth', 'Calamba', 'Otud', '', '1976-12-09', 'Female', 'Married', 'Pob. South, Polanco, Z.N.', 'Financial Analyst', '2024-06-06', '', 'Active', '', '', '', '', '', '1'),
(11, '', '9999', 'Juan', 'Dela', 'Cruz', '', '1991-01-01', 'Male', 'Single', 'Taga Universe', 'Astronaut', '2025-01-01', '', 'Active', '', '', '', '', '', '1'),
(12, '', '5353', 'Ma. Cristy', '', 'Mariño', '', '2000-08-24', 'Female', 'Single', 'Isis, Polanco, ZN', 'Financial Expert', '2025-03-12', '', 'Active', '', '', '', '', '', '1'),
(13, '', '45365', 'Proserphine', 'Gonzaga', 'Godinez', '', '1980-12-30', 'Female', 'Married', 'Pob. South, Polanco, ZN', 'Certified Public Accountant', '2024-12-01', '', 'Active', '', '', '', '', '', '1'),
(14, '', '6575756', 'Imee', 'S', 'Arellano', '', '1993-04-11', 'Female', 'Single', 'New Sicayab, Polanco, ZN', 'LPT', '2023-02-01', '', 'Active', '', '', '', '', '', '1'),
(15, '', '56767', 'Monica', 'Elopre', 'Jakosalem', '', '1997-08-27', 'Female', 'Single', 'Pob. South, Polanco, ZN', 'Financial Expert', '2025-01-02', '', 'Active', '', '', '', '', '', '1'),
(18, '', '635345', 'Zaldy', '', 'Ca', '', '1991-01-01', 'Male', 'Married', 'Malacanang Palace', 'Kurakers', '2025-01-01', '', 'Active', '', '', '', '', '', '1');

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

--
-- Dumping data for table `employee_deductions`
--

INSERT INTO `employee_deductions` (`employee_deduction_id`, `employee_id`, `total_deduction`, `effective_date`, `deduction_particulars`, `end_date`, `date_created`, `date_updated`, `is_frozen`) VALUES
(1, 3, 1800.00, '2025-01-01', 'New Deduction', '2025-07-19', '2025-07-26', '2025-07-26', 0),
(4, 3, 6300.00, '2025-07-20', 'New Loan Added', '2025-10-30', '2025-07-26', '2025-11-09', 0),
(5, 10, 8442.00, '2024-06-06', 'Current Deductions', NULL, '2025-07-28', '2025-11-13', 0),
(6, 11, 1000.55, '2025-01-01', 'New Deduction', NULL, '2025-07-31', '0000-00-00', 0),
(7, 7, 5154.99, '2025-01-01', 'Philhealth', NULL, '2025-08-23', '2025-11-13', 0),
(8, 13, 18499.00, '2025-08-01', 'Philhelath', NULL, '2025-08-24', '2025-11-13', 0),
(9, 14, 8611.00, '2025-08-01', 'PhilHealth', NULL, '2025-08-24', '2025-11-13', 0),
(10, 8, 22000.00, '2025-07-01', 'New', NULL, '2025-08-28', '0000-00-00', 0),
(11, 6, 26055.00, '2025-07-01', 'New Loan', NULL, '2025-08-28', '2026-01-22', 0),
(12, 1, 13600.00, '2025-01-01', 'New Loans', NULL, '2025-08-28', '0000-00-00', 0),
(13, 9, 1200.00, '2025-01-01', 'New', NULL, '2025-11-06', '0000-00-00', 0),
(14, 5, 2050.00, '2025-11-01', 'New', NULL, '2025-11-06', '0000-00-00', 0),
(15, 3, 17799.00, '2025-10-31', 'New Deduction Added', NULL, '2025-11-09', '2025-11-13', 0);

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

--
-- Dumping data for table `employee_deductions_components`
--

INSERT INTO `employee_deductions_components` (`deduction_component_id`, `employee_deduction_id`, `config_deduction_id`, `deduction_comp_amt`) VALUES
(1, 1, 2, 500.00),
(2, 1, 4, 300.00),
(3, 1, 6, 500.00),
(4, 1, 14, 500.00),
(27, 6, 2, 1000.55),
(63, 10, 1, 20000.00),
(64, 10, 21, 2000.00),
(66, 12, 19, 2500.00),
(67, 12, 20, 1500.00),
(68, 12, 22, 1000.00),
(69, 12, 18, 580.00),
(70, 12, 12, 8020.00),
(71, 4, 2, 500.00),
(72, 4, 4, 300.00),
(73, 4, 14, 500.00),
(74, 4, 20, 4500.00),
(75, 4, 21, 500.00),
(82, 13, 2, 1000.00),
(83, 13, 4, 200.00),
(84, 14, 2, 1500.00),
(85, 14, 4, 200.00),
(86, 14, 21, 350.00),
(101, 7, 8, 2500.00),
(102, 7, 14, 1599.99),
(103, 7, 21, 500.00),
(104, 7, 23, 555.00),
(105, 15, 2, 500.00),
(106, 15, 4, 300.00),
(107, 15, 17, 1000.00),
(108, 15, 23, 15999.00),
(109, 8, 14, 2000.00),
(110, 8, 20, 5000.00),
(111, 8, 21, 500.00),
(112, 8, 23, 10999.00),
(113, 9, 18, 3000.00),
(114, 9, 21, 1050.00),
(115, 9, 23, 4561.00),
(116, 5, 1, 900.00),
(117, 5, 2, 2765.70),
(118, 5, 4, 3000.00),
(119, 5, 14, 750.00),
(120, 5, 16, 200.00),
(121, 5, 17, 427.30),
(122, 5, 23, 399.00),
(123, 11, 12, 10500.00),
(124, 11, 17, 15555.00);

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

--
-- Dumping data for table `employee_earnings`
--

INSERT INTO `employee_earnings` (`employee_earning_id`, `employee_id`, `employment_id`, `locked_rate`, `gross_amount`, `earning_particulars`, `effective_date`, `end_date`, `date_created`, `date_updated`, `is_frozen`) VALUES
(1, 3, 1, 12000.00, 14000.00, 'Entry-level', '2018-12-03', '2020-12-31', '2025-07-30', '2025-07-30', 0),
(3, 10, 3, 15000.00, 17000.00, 'Entry-level', '2025-01-01', '2025-06-30', '2025-07-31', '2025-07-31', 0),
(4, 10, 4, 20000.00, 22000.00, 'Promotion', '2025-07-01', '2025-07-15', '2025-07-31', '2025-07-31', 0),
(5, 3, 5, 15000.00, 17000.00, 'Promotion', '2021-01-01', '2024-11-03', '2025-07-31', '2025-07-31', 0),
(7, 3, 12, 21600.00, 123600.00, 'Promotion', '2024-11-04', NULL, '2025-07-31', '2025-11-12', 0),
(8, 10, 7, 30730.00, 32730.00, 'Step Increment', '2025-07-16', NULL, '2025-07-31', '0000-00-00', 0),
(9, 14, 13, 16427.00, 18427.00, 'Enrty-level', '2025-02-01', NULL, '2025-07-31', '0000-00-00', 0),
(10, 12, 9, 16427.00, 18427.00, 'Entry-level', '2025-03-12', NULL, '2025-07-31', '0000-00-00', 0),
(11, 7, 8, 12967.00, 14967.00, 'Entry-level', '2025-01-02', NULL, '2025-07-31', '0000-00-00', 0),
(12, 15, 10, 17775.00, 19775.00, 'Promotion', '2025-01-02', NULL, '2025-07-31', '0000-00-00', 0),
(13, 13, 11, 76534.00, 90534.00, 'Step Increment', '2024-12-01', NULL, '2025-07-31', '0000-00-00', 0),
(15, 11, 15, 60000.00, 62000.00, 'Elected', '2025-01-01', '2024-12-31', '2025-07-31', '2025-07-31', 0),
(16, 11, 15, 60000.00, 74000.00, 'Elected', '2025-01-01', NULL, '2025-07-31', '0000-00-00', 0),
(17, 8, 16, 70000.00, 85600.00, 'Elected', '2025-07-01', NULL, '2025-08-28', '0000-00-00', 0),
(18, 6, 17, 20000.00, 22000.00, 'Promotion', '2025-01-01', NULL, '2025-08-28', '0000-00-00', 0),
(19, 1, 18, 25000.00, 27000.00, 'New', '2025-01-01', NULL, '2025-08-28', '0000-00-00', 0),
(20, 2, 21, 15000.00, 37000.00, 'Original', '2025-01-01', NULL, '2025-10-23', '0000-00-00', 0),
(21, 9, 22, 12000.00, 14000.00, 'New', '2025-01-01', NULL, '2025-11-06', '0000-00-00', 0),
(22, 5, 23, 12000.00, 14000.00, 'New', '2025-01-01', NULL, '2025-11-06', '0000-00-00', 0);

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

--
-- Dumping data for table `employee_earnings_components`
--

INSERT INTO `employee_earnings_components` (`earning_component_id`, `employee_earning_id`, `config_earning_id`, `earning_comp_amt`) VALUES
(6, 1, 1, 12000.00),
(7, 1, 3, 2000.00),
(12, 3, 1, 15000.00),
(13, 3, 3, 2000.00),
(14, 4, 1, 20000.00),
(15, 4, 3, 2000.00),
(16, 5, 1, 15000.00),
(17, 5, 3, 2000.00),
(22, 8, 1, 30730.00),
(23, 8, 3, 2000.00),
(24, 9, 1, 16427.00),
(25, 9, 3, 2000.00),
(26, 10, 1, 16427.00),
(27, 10, 3, 2000.00),
(28, 11, 1, 12967.00),
(29, 11, 3, 2000.00),
(30, 12, 1, 17775.00),
(31, 12, 3, 2000.00),
(32, 13, 1, 76534.00),
(33, 13, 3, 2000.00),
(34, 13, 4, 6000.00),
(35, 13, 5, 6000.00),
(38, 15, 1, 60000.00),
(39, 15, 3, 2000.00),
(40, 16, 1, 60000.00),
(41, 16, 3, 2000.00),
(42, 16, 4, 6000.00),
(43, 16, 5, 6000.00),
(44, 17, 1, 70000.00),
(45, 17, 3, 2000.00),
(46, 17, 4, 6800.00),
(47, 17, 5, 6800.00),
(48, 18, 1, 20000.00),
(49, 18, 3, 2000.00),
(50, 19, 1, 25000.00),
(51, 19, 3, 2000.00),
(52, 20, 1, 15000.00),
(53, 20, 3, 2000.00),
(54, 20, 4, 6000.00),
(55, 20, 5, 6000.00),
(56, 20, 8, 5000.00),
(57, 20, 9, 2000.00),
(58, 20, 10, 1000.00),
(59, 21, 2, 12000.00),
(60, 21, 3, 2000.00),
(61, 22, 2, 12000.00),
(62, 22, 3, 2000.00),
(63, 7, 1, 21600.00),
(64, 7, 3, 2000.00),
(65, 7, 4, 50000.00),
(66, 7, 5, 50000.00);

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

--
-- Dumping data for table `employee_employments_tbl`
--

INSERT INTO `employee_employments_tbl` (`employment_id`, `employee_id`, `employment_refnum`, `employment_type`, `employment_start`, `employment_end`, `position_id`, `dept_assigned`, `designation`, `work_nature`, `work_specifics`, `rate`, `empSalaryID`, `employment_particulars`, `employment_status`) VALUES
(1, 3, '001', 'Regular', '2018-12-03', '2020-12-31', '6', '7', '', 'Administrative', 'Clerical Services', 12000.00, 0, 'Entry-level', '0'),
(3, 10, '465412', 'Regular', '2025-01-01', '2025-06-30', '8', '7', '', 'Administrative', 'Clerical Services', 15000.00, 0, 'Entry-level', '0'),
(4, 10, '43131', 'Regular', '2025-07-01', '2025-07-15', '10', '7', '', 'Administrative', 'Clerical Services', 20000.00, 0, 'Promotion', '0'),
(5, 3, '16565', 'Regular', '2021-01-01', '2024-11-03', '7', '7', '', 'Administrative', 'Clerical Services', 15000.00, 0, 'Promotion', '0'),
(7, 10, '4567', 'Regular', '2025-07-16', '0000-00-00', '10', '7', '', 'Administrative', 'Clerical Services', 30730.00, 0, 'Step Increment', '1'),
(9, 12, '2344', 'Regular', '2025-03-12', '0000-00-00', '13', '7', '', 'Administrative', 'Clerical Services', 16427.00, 0, 'Entry-level', '1'),
(10, 15, '56789', 'Regular', '2025-01-02', '0000-00-00', '5', '7', '', 'Administrative', 'Clerical Services', 17775.00, 0, 'Promotion', '1'),
(11, 13, '9876', 'Regular', '2024-12-01', '0000-00-00', '14', '7', '', 'Supervisory', 'Financial Services', 76534.00, 0, 'Step Increment', '1'),
(12, 3, '45678', 'Regular', '2024-11-04', '0000-00-00', '8', '7', '', 'Administrative', 'Clerical Services', 21600.00, 0, 'Promotion', '1'),
(13, 14, '4561318', 'Regular', '2025-02-01', '0000-00-00', '12', '7', '', 'Administrative', 'Clerical Services', 16427.00, 0, 'Enrty-level', '1'),
(15, 11, '647678645', 'Regular', '2025-01-01', '0000-00-00', '9', '3', '', 'Supervisory', 'Others', 60000.00, 0, 'Elected', '1'),
(16, 8, '1653213', 'Regular', '2025-07-01', '0000-00-00', '15', '3', '', 'Supervisory', 'Others', 70000.00, 0, 'Elected', '1'),
(17, 6, '3535535', 'Regular', '2025-01-01', '0000-00-00', '16', '5', '', 'Administrative', 'ICT Services', 20000.00, 0, 'Promotion', '1'),
(18, 1, '7654345', 'Regular', '2025-01-01', '0000-00-00', '11', '5', '', 'Administrative', 'Financial Services', 25000.00, 0, 'New', '1'),
(19, 7, '3252352345', 'Regular', '2010-01-01', '2024-12-31', '6', '7', '', 'Administrative', 'Clerical Services', 10000.00, 0, 'Original', '0'),
(20, 7, '4354645645', 'Regular', '2025-01-01', '0000-00-00', '7', '7', '', 'Administrative', 'Clerical Services', 15000.00, 0, 'Promotion', '1'),
(21, 2, '876878', 'Regular', '2025-01-01', '0000-00-00', '6', '7', '', 'Administrative', 'Clerical Services', 15000.00, 0, 'Original', '1'),
(22, 9, '354343', 'Casual', '2025-01-01', '2025-06-30', '17', '7', '', 'Office Staff', 'Clerical Services', 12000.00, 0, 'New', '1'),
(23, 5, '45346546', 'Casual', '2025-01-01', '2025-12-31', '18', '7', '', 'Office Staff', 'Clerical Services', 12000.00, 0, 'New', '1');

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

--
-- Dumping data for table `employee_govshares`
--

INSERT INTO `employee_govshares` (`employee_govshare_id`, `employee_id`, `govshare_id`, `govshare_amount`, `employee_rate`, `effective_start`, `effective_end`, `created_at`) VALUES
(1, 7, 8, 100.00, 12967.00, '2025-08-24', NULL, '2025-08-24 08:02:36'),
(2, 7, 1, 1556.04, 12967.00, '2025-08-24', NULL, '2025-08-24 08:02:36'),
(3, 7, 5, 200.00, 12967.00, '2025-08-24', NULL, '2025-08-24 08:02:36'),
(4, 7, 7, 324.18, 12967.00, '2025-08-24', NULL, '2025-08-24 08:02:36'),
(5, 14, 8, 100.00, 16427.00, '2025-08-24', NULL, '2025-08-24 08:02:45'),
(6, 14, 1, 1971.24, 16427.00, '2025-08-24', NULL, '2025-08-24 08:02:45'),
(7, 14, 5, 200.00, 16427.00, '2025-08-24', NULL, '2025-08-24 08:02:45'),
(8, 14, 7, 410.68, 16427.00, '2025-08-24', NULL, '2025-08-24 08:02:45'),
(9, 13, 8, 100.00, 76534.00, '2025-08-24', NULL, '2025-08-24 08:02:51'),
(10, 13, 1, 9184.08, 76534.00, '2025-08-24', NULL, '2025-08-24 08:02:51'),
(11, 13, 5, 200.00, 76534.00, '2025-08-24', NULL, '2025-08-24 08:02:51'),
(12, 13, 7, 1913.35, 76534.00, '2025-08-24', NULL, '2025-08-24 08:02:51'),
(13, 3, 8, 100.00, 21600.00, '2025-08-24', NULL, '2025-08-24 08:02:58'),
(14, 3, 1, 2592.00, 21600.00, '2025-08-24', NULL, '2025-08-24 08:02:58'),
(15, 3, 5, 200.00, 21600.00, '2025-08-24', NULL, '2025-08-24 08:02:58'),
(16, 3, 7, 540.00, 21600.00, '2025-08-24', NULL, '2025-08-24 08:02:58'),
(17, 8, 8, 100.00, 70000.00, '2025-08-28', NULL, '2025-08-28 02:18:28'),
(18, 8, 1, 8400.00, 70000.00, '2025-08-28', NULL, '2025-08-28 02:18:28'),
(19, 8, 5, 200.00, 70000.00, '2025-08-28', NULL, '2025-08-28 02:18:28'),
(20, 8, 7, 1750.00, 70000.00, '2025-08-28', NULL, '2025-08-28 02:18:28'),
(21, 11, 8, 100.00, 60000.00, '2025-08-28', NULL, '2025-08-28 06:01:43'),
(22, 11, 1, 7200.00, 60000.00, '2025-08-28', NULL, '2025-08-28 06:01:43'),
(23, 11, 5, 200.00, 60000.00, '2025-08-28', NULL, '2025-08-28 06:01:43'),
(24, 11, 7, 1500.00, 60000.00, '2025-08-28', NULL, '2025-08-28 06:01:43'),
(25, 6, 8, 100.00, 20000.00, '2025-08-28', NULL, '2025-08-28 14:56:01'),
(26, 6, 1, 2400.00, 20000.00, '2025-08-28', NULL, '2025-08-28 14:56:01'),
(27, 6, 5, 200.00, 20000.00, '2025-08-28', NULL, '2025-08-28 14:56:01'),
(28, 6, 7, 500.00, 20000.00, '2025-08-28', NULL, '2025-08-28 14:56:01'),
(29, 1, 1, 3000.00, 25000.00, '2025-08-28', NULL, '2025-08-28 15:00:46'),
(30, 1, 8, 100.00, 25000.00, '2025-08-28', NULL, '2025-08-28 15:00:46'),
(31, 1, 5, 200.00, 25000.00, '2025-08-28', NULL, '2025-08-28 15:00:46'),
(32, 1, 7, 625.00, 25000.00, '2025-08-28', NULL, '2025-08-28 15:00:46'),
(33, 9, 8, 100.00, 12000.00, '2025-11-06', NULL, '2025-11-06 13:28:33'),
(34, 9, 1, 1440.00, 12000.00, '2025-11-06', NULL, '2025-11-06 13:28:33'),
(35, 9, 5, 200.00, 12000.00, '2025-11-06', NULL, '2025-11-06 13:28:33'),
(36, 9, 7, 300.00, 12000.00, '2025-11-06', NULL, '2025-11-06 13:28:33'),
(37, 5, 8, 100.00, 12000.00, '2025-11-06', '2025-11-05', '2025-11-06 15:08:20'),
(38, 5, 1, 1440.00, 12000.00, '2025-11-06', '2025-11-05', '2025-11-06 15:08:20'),
(39, 5, 5, 200.00, 12000.00, '2025-11-06', '2025-11-05', '2025-11-06 15:08:20'),
(40, 5, 7, 300.00, 12000.00, '2025-11-06', '2025-11-05', '2025-11-06 15:08:20'),
(41, 5, 8, 100.00, 12000.00, '2025-11-06', '2025-11-05', '2025-11-06 15:08:29'),
(42, 5, 1, 1440.00, 12000.00, '2025-11-06', '2025-11-05', '2025-11-06 15:08:29'),
(43, 5, 5, 200.00, 12000.00, '2025-11-06', '2025-11-05', '2025-11-06 15:08:29'),
(44, 5, 7, 300.00, 12000.00, '2025-11-06', '2025-11-05', '2025-11-06 15:08:29'),
(45, 5, 8, 100.00, 12000.00, '2025-11-06', NULL, '2025-11-06 15:11:46'),
(46, 5, 1, 1440.00, 12000.00, '2025-11-06', NULL, '2025-11-06 15:11:46'),
(47, 5, 5, 200.00, 12000.00, '2025-11-06', NULL, '2025-11-06 15:11:46'),
(48, 5, 7, 300.00, 12000.00, '2025-11-06', NULL, '2025-11-06 15:11:46');

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

--
-- Dumping data for table `govshares`
--

INSERT INTO `govshares` (`govshare_id`, `deduction_type_id`, `govshare_name`, `govshare_code`, `govshare_acctcode`, `govshare_rate`, `is_percentage`, `active`, `created_at`) VALUES
(1, 2, 'Life and Retirement Ins. Contributions', 'L_R', '50103010', 0.12, 1, 1, '2025-08-05 05:53:18'),
(5, 3, 'PAG-IBIG Contributions', 'HDMF', '50103020', 200.00, 0, 1, '2025-08-05 07:59:51'),
(7, 4, 'PhilHealth Contributions', 'PHIC', '50103030', 0.05, 1, 1, '2025-08-06 03:49:33'),
(8, 2, 'ECC Contributions', 'ECC', '50103040', 100.00, 0, 1, '2025-08-06 12:43:38');

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

--
-- Dumping data for table `payroll_deductions`
--

INSERT INTO `payroll_deductions` (`payroll_deduction_id`, `payroll_entry_id`, `deduction_component_id`, `payroll_deduct_amount`, `created_at`) VALUES
(1, 1, 52, 5000.00, '2025-11-13'),
(2, 1, 51, 500.00, '2025-11-13'),
(3, 2, 76, 900.00, '2025-11-13'),
(4, 2, 77, 2765.70, '2025-11-13'),
(5, 2, 78, 3000.00, '2025-11-13'),
(6, 2, 79, 750.00, '2025-11-13'),
(7, 2, 80, 200.00, '2025-11-13'),
(8, 2, 81, 427.30, '2025-11-13'),
(9, 3, 54, 2500.00, '2025-11-13'),
(10, 3, 53, 500.00, '2025-11-13'),
(11, 4, 71, 500.00, '2025-11-13'),
(12, 4, 72, 300.00, '2025-11-13'),
(13, 4, 73, 500.00, '2025-11-13'),
(14, 4, 74, 4500.00, '2025-11-13'),
(15, 4, 75, 500.00, '2025-11-13'),
(16, 7, 56, 3000.00, '2025-11-13'),
(17, 7, 55, 1050.00, '2025-11-13'),
(18, 17, 100, 2000.00, '2025-11-13'),
(19, 17, 98, 5000.00, '2025-11-13'),
(20, 17, 99, 500.00, '2025-11-13'),
(21, 18, 95, 2500.00, '2025-11-13'),
(22, 18, 97, 1599.99, '2025-11-13'),
(23, 18, 96, 500.00, '2025-11-13'),
(24, 19, 76, 900.00, '2025-11-13'),
(25, 19, 77, 2765.70, '2025-11-13'),
(26, 19, 78, 3000.00, '2025-11-13'),
(27, 19, 79, 750.00, '2025-11-13'),
(28, 19, 80, 200.00, '2025-11-13'),
(29, 19, 81, 427.30, '2025-11-13'),
(30, 20, 92, 500.00, '2025-11-13'),
(31, 20, 93, 300.00, '2025-11-13'),
(32, 20, 94, 1000.00, '2025-11-13'),
(33, 22, 56, 3000.00, '2025-11-13'),
(34, 22, 55, 1050.00, '2025-11-13'),
(35, 33, 109, 2000.00, '2025-11-13'),
(36, 33, 110, 5000.00, '2025-11-13'),
(37, 33, 111, 500.00, '2025-11-13'),
(38, 33, 112, 10999.00, '2025-11-13'),
(39, 34, 101, 2500.00, '2025-11-13'),
(40, 34, 102, 1599.99, '2025-11-13'),
(41, 34, 103, 500.00, '2025-11-13'),
(42, 34, 104, 555.00, '2025-11-13'),
(43, 35, 116, 900.00, '2025-11-13'),
(44, 35, 117, 2765.70, '2025-11-13'),
(45, 35, 118, 3000.00, '2025-11-13'),
(46, 35, 119, 750.00, '2025-11-13'),
(47, 35, 120, 200.00, '2025-11-13'),
(48, 35, 121, 427.30, '2025-11-13'),
(49, 35, 122, 399.00, '2025-11-13'),
(50, 36, 105, 500.00, '2025-11-13'),
(51, 36, 106, 300.00, '2025-11-13'),
(52, 36, 107, 1000.00, '2025-11-13'),
(53, 36, 108, 15999.00, '2025-11-13'),
(54, 38, 113, 3000.00, '2025-11-13'),
(55, 38, 114, 1050.00, '2025-11-13'),
(56, 38, 115, 4561.00, '2025-11-13');

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

--
-- Dumping data for table `payroll_entries`
--

INSERT INTO `payroll_entries` (`payroll_entry_id`, `employee_id`, `employment_id`, `payroll_period_id`, `locked_period`, `dept_id`, `locked_basic`, `gross`, `total_deductions`, `net_pay`, `earnings_breakdown`, `deductions_breakdown`, `govshares_breakdown`, `emp_type_stamp`, `created_at`, `updated_at`) VALUES
(1, 13, 11, 19, 1, 7, 76534.00, 90534.00, 5500.00, 42517.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"32\",\"label\":\"Sal-Reg\",\"amount\":76534},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"33\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"34\",\"label\":\"RA\",\"amount\":6000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"35\",\"label\":\"TA\",\"amount\":6000}]', '[{\"config_deduction_id\":\"20\",\"deduct_comp_id\":\"52\",\"label\":\"MPL_FLEX\",\"amount\":5000},{\"config_deduction_id\":\"21\",\"deduct_comp_id\":\"51\",\"label\":\"PHIC-PREM\",\"amount\":500}]', '[{\"govshare_id\":\"1\",\"emp_govshare_id\":\"10\",\"label\":\"L_R\",\"amount\":9184.08},{\"govshare_id\":\"5\",\"emp_govshare_id\":\"11\",\"label\":\"HDMF\",\"amount\":200},{\"govshare_id\":\"7\",\"emp_govshare_id\":\"12\",\"label\":\"PHIC\",\"amount\":1913.35},{\"govshare_id\":\"8\",\"emp_govshare_id\":\"9\",\"label\":\"ECC\",\"amount\":100}]', 'Regular', '2025-11-13 11:56:46', '2025-11-13 11:56:46'),
(2, 10, 7, 19, 1, 7, 30730.00, 32730.00, 8043.00, 12343.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"22\",\"label\":\"Sal-Reg\",\"amount\":30730},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"23\",\"label\":\"PERA\",\"amount\":2000}]', '[{\"config_deduction_id\":\"1\",\"deduct_comp_id\":\"76\",\"label\":\"W_TAX\",\"amount\":900},{\"config_deduction_id\":\"2\",\"deduct_comp_id\":\"77\",\"label\":\"LR_INS\",\"amount\":2765.7},{\"config_deduction_id\":\"4\",\"deduct_comp_id\":\"78\",\"label\":\"PAG_PREM\",\"amount\":3000},{\"config_deduction_id\":\"14\",\"deduct_comp_id\":\"79\",\"label\":\"SSS_PREM\",\"amount\":750},{\"config_deduction_id\":\"16\",\"deduct_comp_id\":\"80\",\"label\":\"PLREG\",\"amount\":200},{\"config_deduction_id\":\"17\",\"deduct_comp_id\":\"81\",\"label\":\"PMGEA_DUES\",\"amount\":427.3}]', '[]', 'Regular', '2025-11-13 11:56:46', '2025-11-13 11:56:46'),
(3, 7, 20, 19, 1, 7, 12967.00, 14967.00, 3000.00, 5983.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"28\",\"label\":\"Sal-Reg\",\"amount\":12967},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"29\",\"label\":\"PERA\",\"amount\":2000}]', '[{\"config_deduction_id\":\"8\",\"deduct_comp_id\":\"54\",\"label\":\"RRB_LN\",\"amount\":2500},{\"config_deduction_id\":\"21\",\"deduct_comp_id\":\"53\",\"label\":\"PHIC-PREM\",\"amount\":500}]', '[{\"govshare_id\":\"1\",\"emp_govshare_id\":\"2\",\"label\":\"L_R\",\"amount\":1556.04},{\"govshare_id\":\"5\",\"emp_govshare_id\":\"3\",\"label\":\"HDMF\",\"amount\":200},{\"govshare_id\":\"7\",\"emp_govshare_id\":\"4\",\"label\":\"PHIC\",\"amount\":324.18},{\"govshare_id\":\"8\",\"emp_govshare_id\":\"1\",\"label\":\"ECC\",\"amount\":100}]', 'Regular', '2025-11-13 11:56:46', '2025-11-13 11:56:46'),
(4, 3, 12, 19, 1, 7, 21600.00, 123600.00, 6300.00, 58650.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"63\",\"label\":\"Sal-Reg\",\"amount\":21600},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"64\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"65\",\"label\":\"RA\",\"amount\":50000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"66\",\"label\":\"TA\",\"amount\":50000}]', '[{\"config_deduction_id\":\"2\",\"deduct_comp_id\":\"71\",\"label\":\"LR_INS\",\"amount\":500},{\"config_deduction_id\":\"4\",\"deduct_comp_id\":\"72\",\"label\":\"PAG_PREM\",\"amount\":300},{\"config_deduction_id\":\"14\",\"deduct_comp_id\":\"73\",\"label\":\"SSS_PREM\",\"amount\":500},{\"config_deduction_id\":\"20\",\"deduct_comp_id\":\"74\",\"label\":\"MPL_FLEX\",\"amount\":4500},{\"config_deduction_id\":\"21\",\"deduct_comp_id\":\"75\",\"label\":\"PHIC-PREM\",\"amount\":500}]', '[{\"govshare_id\":\"1\",\"emp_govshare_id\":\"14\",\"label\":\"L_R\",\"amount\":2592},{\"govshare_id\":\"5\",\"emp_govshare_id\":\"15\",\"label\":\"HDMF\",\"amount\":200},{\"govshare_id\":\"7\",\"emp_govshare_id\":\"16\",\"label\":\"PHIC\",\"amount\":540},{\"govshare_id\":\"8\",\"emp_govshare_id\":\"13\",\"label\":\"ECC\",\"amount\":100}]', 'Regular', '2025-11-13 11:56:46', '2025-11-13 11:56:46'),
(5, 15, 10, 19, 1, 7, 17775.00, 19775.00, 0.00, 9887.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"30\",\"label\":\"Sal-Reg\",\"amount\":17775},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"31\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2025-11-13 11:56:46', '2025-11-13 11:56:46'),
(6, 12, 9, 19, 1, 7, 16427.00, 18427.00, 0.00, 9213.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"26\",\"label\":\"Sal-Reg\",\"amount\":16427},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"27\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2025-11-13 11:56:46', '2025-11-13 11:56:46'),
(7, 14, 13, 19, 1, 7, 16427.00, 18427.00, 4050.00, 7188.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"24\",\"label\":\"Sal-Reg\",\"amount\":16427},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"25\",\"label\":\"PERA\",\"amount\":2000}]', '[{\"config_deduction_id\":\"18\",\"deduct_comp_id\":\"56\",\"label\":\"PMGEA_LN\",\"amount\":3000},{\"config_deduction_id\":\"21\",\"deduct_comp_id\":\"55\",\"label\":\"PHIC-PREM\",\"amount\":1050}]', '[{\"govshare_id\":\"1\",\"emp_govshare_id\":\"6\",\"label\":\"L_R\",\"amount\":1971.24},{\"govshare_id\":\"5\",\"emp_govshare_id\":\"7\",\"label\":\"HDMF\",\"amount\":200},{\"govshare_id\":\"7\",\"emp_govshare_id\":\"8\",\"label\":\"PHIC\",\"amount\":410.68},{\"govshare_id\":\"8\",\"emp_govshare_id\":\"5\",\"label\":\"ECC\",\"amount\":100}]', 'Regular', '2025-11-13 11:56:46', '2025-11-13 11:56:46'),
(8, 2, 21, 19, 1, 7, 15000.00, 37000.00, 0.00, 18500.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"52\",\"label\":\"Sal-Reg\",\"amount\":15000},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"53\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"54\",\"label\":\"RA\",\"amount\":6000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"55\",\"label\":\"TA\",\"amount\":6000},{\"config_earning_id\":\"8\",\"earning_comp_id\":\"56\",\"label\":\"SUB\",\"amount\":5000},{\"config_earning_id\":\"9\",\"earning_comp_id\":\"57\",\"label\":\"HZD\",\"amount\":2000},{\"config_earning_id\":\"10\",\"earning_comp_id\":\"58\",\"label\":\"LNDRY\",\"amount\":1000}]', '[]', '[]', 'Regular', '2025-11-13 11:56:46', '2025-11-13 11:56:46'),
(9, 13, 11, 20, 1, 7, 76534.00, 90534.00, 0.00, 42517.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"32\",\"label\":\"Sal-Reg\",\"amount\":76534},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"33\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"34\",\"label\":\"RA\",\"amount\":6000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"35\",\"label\":\"TA\",\"amount\":6000}]', '[]', '[]', 'Regular', '2025-11-13 13:20:26', '2025-11-13 13:20:26'),
(10, 7, 20, 20, 1, 7, 12967.00, 14967.00, 0.00, 5983.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"28\",\"label\":\"Sal-Reg\",\"amount\":12967},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"29\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2025-11-13 13:20:26', '2025-11-13 13:20:26'),
(11, 10, 7, 20, 1, 7, 30730.00, 32730.00, 0.00, 12343.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"22\",\"label\":\"Sal-Reg\",\"amount\":30730},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"23\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2025-11-13 13:20:26', '2025-11-13 13:20:26'),
(12, 3, 12, 20, 1, 7, 21600.00, 123600.00, 0.00, 58150.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"63\",\"label\":\"Sal-Reg\",\"amount\":21600},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"64\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"65\",\"label\":\"RA\",\"amount\":50000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"66\",\"label\":\"TA\",\"amount\":50000}]', '[]', '[]', 'Regular', '2025-11-13 13:20:26', '2025-11-13 13:20:26'),
(13, 15, 10, 20, 1, 7, 17775.00, 19775.00, 0.00, 9887.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"30\",\"label\":\"Sal-Reg\",\"amount\":17775},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"31\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2025-11-13 13:20:26', '2025-11-13 13:20:26'),
(14, 14, 13, 20, 1, 7, 16427.00, 18427.00, 0.00, 7188.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"24\",\"label\":\"Sal-Reg\",\"amount\":16427},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"25\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2025-11-13 13:20:26', '2025-11-13 13:20:26'),
(15, 12, 9, 20, 1, 7, 16427.00, 18427.00, 0.00, 9213.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"26\",\"label\":\"Sal-Reg\",\"amount\":16427},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"27\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2025-11-13 13:20:26', '2025-11-13 13:20:26'),
(16, 2, 21, 20, 1, 7, 15000.00, 37000.00, 0.00, 18500.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"52\",\"label\":\"Sal-Reg\",\"amount\":15000},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"53\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"54\",\"label\":\"RA\",\"amount\":6000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"55\",\"label\":\"TA\",\"amount\":6000},{\"config_earning_id\":\"8\",\"earning_comp_id\":\"56\",\"label\":\"SUB\",\"amount\":5000},{\"config_earning_id\":\"9\",\"earning_comp_id\":\"57\",\"label\":\"HZD\",\"amount\":2000},{\"config_earning_id\":\"10\",\"earning_comp_id\":\"58\",\"label\":\"LNDRY\",\"amount\":1000}]', '[]', '[]', 'Regular', '2025-11-13 13:20:26', '2025-11-13 13:20:26'),
(17, 13, 11, 21, 1, 7, 76534.00, 90534.00, 7500.00, 41517.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"32\",\"label\":\"Sal-Reg\",\"amount\":76534},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"33\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"34\",\"label\":\"RA\",\"amount\":6000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"35\",\"label\":\"TA\",\"amount\":6000}]', '[{\"config_deduction_id\":\"14\",\"deduct_comp_id\":\"100\",\"label\":\"SSS_PREM\",\"amount\":2000},{\"config_deduction_id\":\"20\",\"deduct_comp_id\":\"98\",\"label\":\"MPL_FLEX\",\"amount\":5000},{\"config_deduction_id\":\"21\",\"deduct_comp_id\":\"99\",\"label\":\"PHIC-PREM\",\"amount\":500}]', '[{\"govshare_id\":\"1\",\"emp_govshare_id\":\"10\",\"label\":\"L_R\",\"amount\":9184.08},{\"govshare_id\":\"5\",\"emp_govshare_id\":\"11\",\"label\":\"HDMF\",\"amount\":200},{\"govshare_id\":\"7\",\"emp_govshare_id\":\"12\",\"label\":\"PHIC\",\"amount\":1913.35},{\"govshare_id\":\"8\",\"emp_govshare_id\":\"9\",\"label\":\"ECC\",\"amount\":100}]', 'Regular', '2025-11-13 13:22:03', '2025-11-13 13:22:03'),
(18, 7, 20, 21, 1, 7, 12967.00, 14967.00, 4599.99, 5183.51, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"28\",\"label\":\"Sal-Reg\",\"amount\":12967},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"29\",\"label\":\"PERA\",\"amount\":2000}]', '[{\"config_deduction_id\":\"8\",\"deduct_comp_id\":\"95\",\"label\":\"RRB_LN\",\"amount\":2500},{\"config_deduction_id\":\"14\",\"deduct_comp_id\":\"97\",\"label\":\"SSS_PREM\",\"amount\":1599.99},{\"config_deduction_id\":\"21\",\"deduct_comp_id\":\"96\",\"label\":\"PHIC-PREM\",\"amount\":500}]', '[{\"govshare_id\":\"1\",\"emp_govshare_id\":\"2\",\"label\":\"L_R\",\"amount\":1556.04},{\"govshare_id\":\"5\",\"emp_govshare_id\":\"3\",\"label\":\"HDMF\",\"amount\":200},{\"govshare_id\":\"7\",\"emp_govshare_id\":\"4\",\"label\":\"PHIC\",\"amount\":324.18},{\"govshare_id\":\"8\",\"emp_govshare_id\":\"1\",\"label\":\"ECC\",\"amount\":100}]', 'Regular', '2025-11-13 13:22:03', '2025-11-13 13:22:03'),
(19, 10, 7, 21, 1, 7, 30730.00, 32730.00, 8043.00, 12343.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"22\",\"label\":\"Sal-Reg\",\"amount\":30730},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"23\",\"label\":\"PERA\",\"amount\":2000}]', '[{\"config_deduction_id\":\"1\",\"deduct_comp_id\":\"76\",\"label\":\"W_TAX\",\"amount\":900},{\"config_deduction_id\":\"2\",\"deduct_comp_id\":\"77\",\"label\":\"LR_INS\",\"amount\":2765.7},{\"config_deduction_id\":\"4\",\"deduct_comp_id\":\"78\",\"label\":\"PAG_PREM\",\"amount\":3000},{\"config_deduction_id\":\"14\",\"deduct_comp_id\":\"79\",\"label\":\"SSS_PREM\",\"amount\":750},{\"config_deduction_id\":\"16\",\"deduct_comp_id\":\"80\",\"label\":\"PLREG\",\"amount\":200},{\"config_deduction_id\":\"17\",\"deduct_comp_id\":\"81\",\"label\":\"PMGEA_DUES\",\"amount\":427.3}]', '[]', 'Regular', '2025-11-13 13:22:03', '2025-11-13 13:22:03'),
(20, 3, 12, 21, 1, 7, 21600.00, 123600.00, 1800.00, 60900.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"63\",\"label\":\"Sal-Reg\",\"amount\":21600},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"64\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"65\",\"label\":\"RA\",\"amount\":50000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"66\",\"label\":\"TA\",\"amount\":50000}]', '[{\"config_deduction_id\":\"2\",\"deduct_comp_id\":\"92\",\"label\":\"LR_INS\",\"amount\":500},{\"config_deduction_id\":\"4\",\"deduct_comp_id\":\"93\",\"label\":\"PAG_PREM\",\"amount\":300},{\"config_deduction_id\":\"17\",\"deduct_comp_id\":\"94\",\"label\":\"PMGEA_DUES\",\"amount\":1000}]', '[{\"govshare_id\":\"1\",\"emp_govshare_id\":\"14\",\"label\":\"L_R\",\"amount\":2592},{\"govshare_id\":\"5\",\"emp_govshare_id\":\"15\",\"label\":\"HDMF\",\"amount\":200},{\"govshare_id\":\"7\",\"emp_govshare_id\":\"16\",\"label\":\"PHIC\",\"amount\":540},{\"govshare_id\":\"8\",\"emp_govshare_id\":\"13\",\"label\":\"ECC\",\"amount\":100}]', 'Regular', '2025-11-13 13:22:03', '2025-11-13 13:22:03'),
(21, 15, 10, 21, 1, 7, 17775.00, 19775.00, 0.00, 9887.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"30\",\"label\":\"Sal-Reg\",\"amount\":17775},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"31\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2025-11-13 13:22:03', '2025-11-13 13:22:03'),
(22, 14, 13, 21, 1, 7, 16427.00, 18427.00, 4050.00, 7188.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"24\",\"label\":\"Sal-Reg\",\"amount\":16427},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"25\",\"label\":\"PERA\",\"amount\":2000}]', '[{\"config_deduction_id\":\"18\",\"deduct_comp_id\":\"56\",\"label\":\"PMGEA_LN\",\"amount\":3000},{\"config_deduction_id\":\"21\",\"deduct_comp_id\":\"55\",\"label\":\"PHIC-PREM\",\"amount\":1050}]', '[{\"govshare_id\":\"1\",\"emp_govshare_id\":\"6\",\"label\":\"L_R\",\"amount\":1971.24},{\"govshare_id\":\"5\",\"emp_govshare_id\":\"7\",\"label\":\"HDMF\",\"amount\":200},{\"govshare_id\":\"7\",\"emp_govshare_id\":\"8\",\"label\":\"PHIC\",\"amount\":410.68},{\"govshare_id\":\"8\",\"emp_govshare_id\":\"5\",\"label\":\"ECC\",\"amount\":100}]', 'Regular', '2025-11-13 13:22:03', '2025-11-13 13:22:03'),
(23, 12, 9, 21, 1, 7, 16427.00, 18427.00, 0.00, 9213.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"26\",\"label\":\"Sal-Reg\",\"amount\":16427},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"27\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2025-11-13 13:22:03', '2025-11-13 13:22:03'),
(24, 2, 21, 21, 1, 7, 15000.00, 37000.00, 0.00, 18500.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"52\",\"label\":\"Sal-Reg\",\"amount\":15000},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"53\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"54\",\"label\":\"RA\",\"amount\":6000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"55\",\"label\":\"TA\",\"amount\":6000},{\"config_earning_id\":\"8\",\"earning_comp_id\":\"56\",\"label\":\"SUB\",\"amount\":5000},{\"config_earning_id\":\"9\",\"earning_comp_id\":\"57\",\"label\":\"HZD\",\"amount\":2000},{\"config_earning_id\":\"10\",\"earning_comp_id\":\"58\",\"label\":\"LNDRY\",\"amount\":1000}]', '[]', '[]', 'Regular', '2025-11-13 13:22:03', '2025-11-13 13:22:03'),
(25, 13, 11, 22, 1, 7, 76534.00, 90534.00, 0.00, 41517.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"32\",\"label\":\"Sal-Reg\",\"amount\":76534},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"33\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"34\",\"label\":\"RA\",\"amount\":6000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"35\",\"label\":\"TA\",\"amount\":6000}]', '[]', '[]', 'Regular', '2025-11-13 13:43:30', '2025-11-13 13:43:30'),
(26, 7, 20, 22, 1, 7, 12967.00, 14967.00, 0.00, 5183.51, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"28\",\"label\":\"Sal-Reg\",\"amount\":12967},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"29\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2025-11-13 13:43:30', '2025-11-13 13:43:30'),
(27, 10, 7, 22, 1, 7, 30730.00, 32730.00, 0.00, 12343.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"22\",\"label\":\"Sal-Reg\",\"amount\":30730},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"23\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2025-11-13 13:43:30', '2025-11-13 13:43:30'),
(28, 3, 12, 22, 1, 7, 21600.00, 123600.00, 0.00, 60900.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"63\",\"label\":\"Sal-Reg\",\"amount\":21600},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"64\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"65\",\"label\":\"RA\",\"amount\":50000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"66\",\"label\":\"TA\",\"amount\":50000}]', '[]', '[]', 'Regular', '2025-11-13 13:43:30', '2025-11-13 13:43:30'),
(29, 15, 10, 22, 1, 7, 17775.00, 19775.00, 0.00, 9887.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"30\",\"label\":\"Sal-Reg\",\"amount\":17775},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"31\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2025-11-13 13:43:30', '2025-11-13 13:43:30'),
(30, 14, 13, 22, 1, 7, 16427.00, 18427.00, 0.00, 7188.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"24\",\"label\":\"Sal-Reg\",\"amount\":16427},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"25\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2025-11-13 13:43:30', '2025-11-13 13:43:30'),
(31, 12, 9, 22, 1, 7, 16427.00, 18427.00, 0.00, 9213.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"26\",\"label\":\"Sal-Reg\",\"amount\":16427},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"27\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2025-11-13 13:43:30', '2025-11-13 13:43:30'),
(32, 2, 21, 22, 1, 7, 15000.00, 37000.00, 0.00, 18500.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"52\",\"label\":\"Sal-Reg\",\"amount\":15000},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"53\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"54\",\"label\":\"RA\",\"amount\":6000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"55\",\"label\":\"TA\",\"amount\":6000},{\"config_earning_id\":\"8\",\"earning_comp_id\":\"56\",\"label\":\"SUB\",\"amount\":5000},{\"config_earning_id\":\"9\",\"earning_comp_id\":\"57\",\"label\":\"HZD\",\"amount\":2000},{\"config_earning_id\":\"10\",\"earning_comp_id\":\"58\",\"label\":\"LNDRY\",\"amount\":1000}]', '[]', '[]', 'Regular', '2025-11-13 13:43:30', '2025-11-13 13:43:30'),
(33, 13, 11, 23, 1, 7, 76534.00, 90534.00, 18499.00, 36017.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"32\",\"label\":\"Sal-Reg\",\"amount\":76534},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"33\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"34\",\"label\":\"RA\",\"amount\":6000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"35\",\"label\":\"TA\",\"amount\":6000}]', '[{\"config_deduction_id\":\"14\",\"deduct_comp_id\":\"109\",\"label\":\"SSS_PREM\",\"amount\":2000},{\"config_deduction_id\":\"20\",\"deduct_comp_id\":\"110\",\"label\":\"MPL_FLEX\",\"amount\":5000},{\"config_deduction_id\":\"21\",\"deduct_comp_id\":\"111\",\"label\":\"PHIC-PREM\",\"amount\":500},{\"config_deduction_id\":\"23\",\"deduct_comp_id\":\"112\",\"label\":\"AIA\",\"amount\":10999}]', '[{\"govshare_id\":\"1\",\"emp_govshare_id\":\"10\",\"label\":\"L_R\",\"amount\":9184.08},{\"govshare_id\":\"5\",\"emp_govshare_id\":\"11\",\"label\":\"HDMF\",\"amount\":200},{\"govshare_id\":\"7\",\"emp_govshare_id\":\"12\",\"label\":\"PHIC\",\"amount\":1913.35},{\"govshare_id\":\"8\",\"emp_govshare_id\":\"9\",\"label\":\"ECC\",\"amount\":100}]', 'Regular', '2025-11-13 13:45:06', '2025-11-13 13:45:06'),
(34, 7, 20, 23, 1, 7, 12967.00, 14967.00, 5154.99, 4906.01, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"28\",\"label\":\"Sal-Reg\",\"amount\":12967},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"29\",\"label\":\"PERA\",\"amount\":2000}]', '[{\"config_deduction_id\":\"8\",\"deduct_comp_id\":\"101\",\"label\":\"RRB_LN\",\"amount\":2500},{\"config_deduction_id\":\"14\",\"deduct_comp_id\":\"102\",\"label\":\"SSS_PREM\",\"amount\":1599.99},{\"config_deduction_id\":\"21\",\"deduct_comp_id\":\"103\",\"label\":\"PHIC-PREM\",\"amount\":500},{\"config_deduction_id\":\"23\",\"deduct_comp_id\":\"104\",\"label\":\"AIA\",\"amount\":555}]', '[{\"govshare_id\":\"1\",\"emp_govshare_id\":\"2\",\"label\":\"L_R\",\"amount\":1556.04},{\"govshare_id\":\"5\",\"emp_govshare_id\":\"3\",\"label\":\"HDMF\",\"amount\":200},{\"govshare_id\":\"7\",\"emp_govshare_id\":\"4\",\"label\":\"PHIC\",\"amount\":324.18},{\"govshare_id\":\"8\",\"emp_govshare_id\":\"1\",\"label\":\"ECC\",\"amount\":100}]', 'Regular', '2025-11-13 13:45:06', '2025-11-13 13:45:06'),
(35, 10, 7, 23, 1, 7, 30730.00, 32730.00, 8442.00, 12144.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"22\",\"label\":\"Sal-Reg\",\"amount\":30730},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"23\",\"label\":\"PERA\",\"amount\":2000}]', '[{\"config_deduction_id\":\"1\",\"deduct_comp_id\":\"116\",\"label\":\"W_TAX\",\"amount\":900},{\"config_deduction_id\":\"2\",\"deduct_comp_id\":\"117\",\"label\":\"LR_INS\",\"amount\":2765.7},{\"config_deduction_id\":\"4\",\"deduct_comp_id\":\"118\",\"label\":\"PAG_PREM\",\"amount\":3000},{\"config_deduction_id\":\"14\",\"deduct_comp_id\":\"119\",\"label\":\"SSS_PREM\",\"amount\":750},{\"config_deduction_id\":\"16\",\"deduct_comp_id\":\"120\",\"label\":\"PLREG\",\"amount\":200},{\"config_deduction_id\":\"17\",\"deduct_comp_id\":\"121\",\"label\":\"PMGEA_DUES\",\"amount\":427.3},{\"config_deduction_id\":\"23\",\"deduct_comp_id\":\"122\",\"label\":\"AIA\",\"amount\":399}]', '[]', 'Regular', '2025-11-13 13:45:06', '2025-11-13 13:45:06'),
(36, 3, 12, 23, 1, 7, 21600.00, 123600.00, 17799.00, 52900.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"63\",\"label\":\"Sal-Reg\",\"amount\":21600},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"64\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"65\",\"label\":\"RA\",\"amount\":50000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"66\",\"label\":\"TA\",\"amount\":50000}]', '[{\"config_deduction_id\":\"2\",\"deduct_comp_id\":\"105\",\"label\":\"LR_INS\",\"amount\":500},{\"config_deduction_id\":\"4\",\"deduct_comp_id\":\"106\",\"label\":\"PAG_PREM\",\"amount\":300},{\"config_deduction_id\":\"17\",\"deduct_comp_id\":\"107\",\"label\":\"PMGEA_DUES\",\"amount\":1000},{\"config_deduction_id\":\"23\",\"deduct_comp_id\":\"108\",\"label\":\"AIA\",\"amount\":15999}]', '[{\"govshare_id\":\"1\",\"emp_govshare_id\":\"14\",\"label\":\"L_R\",\"amount\":2592},{\"govshare_id\":\"5\",\"emp_govshare_id\":\"15\",\"label\":\"HDMF\",\"amount\":200},{\"govshare_id\":\"7\",\"emp_govshare_id\":\"16\",\"label\":\"PHIC\",\"amount\":540},{\"govshare_id\":\"8\",\"emp_govshare_id\":\"13\",\"label\":\"ECC\",\"amount\":100}]', 'Regular', '2025-11-13 13:45:06', '2025-11-13 13:45:06'),
(37, 15, 10, 23, 1, 7, 17775.00, 19775.00, 0.00, 9887.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"30\",\"label\":\"Sal-Reg\",\"amount\":17775},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"31\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2025-11-13 13:45:06', '2025-11-13 13:45:06'),
(38, 14, 13, 23, 1, 7, 16427.00, 18427.00, 8611.00, 4908.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"24\",\"label\":\"Sal-Reg\",\"amount\":16427},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"25\",\"label\":\"PERA\",\"amount\":2000}]', '[{\"config_deduction_id\":\"18\",\"deduct_comp_id\":\"113\",\"label\":\"PMGEA_LN\",\"amount\":3000},{\"config_deduction_id\":\"21\",\"deduct_comp_id\":\"114\",\"label\":\"PHIC-PREM\",\"amount\":1050},{\"config_deduction_id\":\"23\",\"deduct_comp_id\":\"115\",\"label\":\"AIA\",\"amount\":4561}]', '[{\"govshare_id\":\"1\",\"emp_govshare_id\":\"6\",\"label\":\"L_R\",\"amount\":1971.24},{\"govshare_id\":\"5\",\"emp_govshare_id\":\"7\",\"label\":\"HDMF\",\"amount\":200},{\"govshare_id\":\"7\",\"emp_govshare_id\":\"8\",\"label\":\"PHIC\",\"amount\":410.68},{\"govshare_id\":\"8\",\"emp_govshare_id\":\"5\",\"label\":\"ECC\",\"amount\":100}]', 'Regular', '2025-11-13 13:45:06', '2025-11-13 13:45:06'),
(39, 12, 9, 23, 1, 7, 16427.00, 18427.00, 0.00, 9213.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"26\",\"label\":\"Sal-Reg\",\"amount\":16427},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"27\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2025-11-13 13:45:06', '2025-11-13 13:45:06'),
(40, 2, 21, 23, 1, 7, 15000.00, 37000.00, 0.00, 18500.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"52\",\"label\":\"Sal-Reg\",\"amount\":15000},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"53\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"54\",\"label\":\"RA\",\"amount\":6000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"55\",\"label\":\"TA\",\"amount\":6000},{\"config_earning_id\":\"8\",\"earning_comp_id\":\"56\",\"label\":\"SUB\",\"amount\":5000},{\"config_earning_id\":\"9\",\"earning_comp_id\":\"57\",\"label\":\"HZD\",\"amount\":2000},{\"config_earning_id\":\"10\",\"earning_comp_id\":\"58\",\"label\":\"LNDRY\",\"amount\":1000}]', '[]', '[]', 'Regular', '2025-11-13 13:45:06', '2025-11-13 13:45:06'),
(41, 13, 11, 24, 1, 7, 76534.00, 90534.00, 0.00, 36017.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"32\",\"label\":\"Sal-Reg\",\"amount\":76534},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"33\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"34\",\"label\":\"RA\",\"amount\":6000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"35\",\"label\":\"TA\",\"amount\":6000}]', '[]', '[]', 'Regular', '2026-01-22 13:30:50', '2026-01-22 13:30:50'),
(42, 7, 20, 24, 1, 7, 12967.00, 14967.00, 0.00, 4906.01, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"28\",\"label\":\"Sal-Reg\",\"amount\":12967},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"29\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2026-01-22 13:30:50', '2026-01-22 13:30:50'),
(43, 10, 7, 24, 1, 7, 30730.00, 32730.00, 0.00, 12144.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"22\",\"label\":\"Sal-Reg\",\"amount\":30730},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"23\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2026-01-22 13:30:50', '2026-01-22 13:30:50'),
(44, 3, 12, 24, 1, 7, 21600.00, 123600.00, 0.00, 52900.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"63\",\"label\":\"Sal-Reg\",\"amount\":21600},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"64\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"65\",\"label\":\"RA\",\"amount\":50000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"66\",\"label\":\"TA\",\"amount\":50000}]', '[]', '[]', 'Regular', '2026-01-22 13:30:50', '2026-01-22 13:30:50'),
(45, 15, 10, 24, 1, 7, 17775.00, 19775.00, 0.00, 9887.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"30\",\"label\":\"Sal-Reg\",\"amount\":17775},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"31\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2026-01-22 13:30:50', '2026-01-22 13:30:50'),
(46, 14, 13, 24, 1, 7, 16427.00, 18427.00, 0.00, 4908.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"24\",\"label\":\"Sal-Reg\",\"amount\":16427},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"25\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2026-01-22 13:30:50', '2026-01-22 13:30:50'),
(47, 12, 9, 24, 1, 7, 16427.00, 18427.00, 0.00, 9213.50, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"26\",\"label\":\"Sal-Reg\",\"amount\":16427},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"27\",\"label\":\"PERA\",\"amount\":2000}]', '[]', '[]', 'Regular', '2026-01-22 13:30:50', '2026-01-22 13:30:50'),
(48, 2, 21, 24, 1, 7, 15000.00, 37000.00, 0.00, 18500.00, '[{\"config_earning_id\":\"1\",\"earning_comp_id\":\"52\",\"label\":\"Sal-Reg\",\"amount\":15000},{\"config_earning_id\":\"3\",\"earning_comp_id\":\"53\",\"label\":\"PERA\",\"amount\":2000},{\"config_earning_id\":\"4\",\"earning_comp_id\":\"54\",\"label\":\"RA\",\"amount\":6000},{\"config_earning_id\":\"5\",\"earning_comp_id\":\"55\",\"label\":\"TA\",\"amount\":6000},{\"config_earning_id\":\"8\",\"earning_comp_id\":\"56\",\"label\":\"SUB\",\"amount\":5000},{\"config_earning_id\":\"9\",\"earning_comp_id\":\"57\",\"label\":\"HZD\",\"amount\":2000},{\"config_earning_id\":\"10\",\"earning_comp_id\":\"58\",\"label\":\"LNDRY\",\"amount\":1000}]', '[]', '[]', 'Regular', '2026-01-22 13:30:50', '2026-01-22 13:30:50');

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

--
-- Dumping data for table `payroll_govshares`
--

INSERT INTO `payroll_govshares` (`payroll_govshare_id`, `payroll_entry_id`, `employee_govshare_id`, `payroll_govshare_amount`, `created_at`) VALUES
(1, 1, 10, 9184.08, '2025-11-13'),
(2, 1, 11, 200.00, '2025-11-13'),
(3, 1, 12, 1913.35, '2025-11-13'),
(4, 1, 9, 100.00, '2025-11-13'),
(5, 3, 2, 1556.04, '2025-11-13'),
(6, 3, 3, 200.00, '2025-11-13'),
(7, 3, 4, 324.18, '2025-11-13'),
(8, 3, 1, 100.00, '2025-11-13'),
(9, 4, 14, 2592.00, '2025-11-13'),
(10, 4, 15, 200.00, '2025-11-13'),
(11, 4, 16, 540.00, '2025-11-13'),
(12, 4, 13, 100.00, '2025-11-13'),
(13, 7, 6, 1971.24, '2025-11-13'),
(14, 7, 7, 200.00, '2025-11-13'),
(15, 7, 8, 410.68, '2025-11-13'),
(16, 7, 5, 100.00, '2025-11-13'),
(17, 17, 10, 9184.08, '2025-11-13'),
(18, 17, 11, 200.00, '2025-11-13'),
(19, 17, 12, 1913.35, '2025-11-13'),
(20, 17, 9, 100.00, '2025-11-13'),
(21, 18, 2, 1556.04, '2025-11-13'),
(22, 18, 3, 200.00, '2025-11-13'),
(23, 18, 4, 324.18, '2025-11-13'),
(24, 18, 1, 100.00, '2025-11-13'),
(25, 20, 14, 2592.00, '2025-11-13'),
(26, 20, 15, 200.00, '2025-11-13'),
(27, 20, 16, 540.00, '2025-11-13'),
(28, 20, 13, 100.00, '2025-11-13'),
(29, 22, 6, 1971.24, '2025-11-13'),
(30, 22, 7, 200.00, '2025-11-13'),
(31, 22, 8, 410.68, '2025-11-13'),
(32, 22, 5, 100.00, '2025-11-13'),
(33, 33, 10, 9184.08, '2025-11-13'),
(34, 33, 11, 200.00, '2025-11-13'),
(35, 33, 12, 1913.35, '2025-11-13'),
(36, 33, 9, 100.00, '2025-11-13'),
(37, 34, 2, 1556.04, '2025-11-13'),
(38, 34, 3, 200.00, '2025-11-13'),
(39, 34, 4, 324.18, '2025-11-13'),
(40, 34, 1, 100.00, '2025-11-13'),
(41, 36, 14, 2592.00, '2025-11-13'),
(42, 36, 15, 200.00, '2025-11-13'),
(43, 36, 16, 540.00, '2025-11-13'),
(44, 36, 13, 100.00, '2025-11-13'),
(45, 38, 6, 1971.24, '2025-11-13'),
(46, 38, 7, 200.00, '2025-11-13'),
(47, 38, 8, 410.68, '2025-11-13'),
(48, 38, 5, 100.00, '2025-11-13');

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

--
-- Dumping data for table `payroll_periods`
--

INSERT INTO `payroll_periods` (`payroll_period_id`, `period_label`, `date_start`, `date_end`, `frequency`, `is_locked`, `created_at`) VALUES
(1, 'January 1–15, 2025', '2025-01-01', '2025-01-15', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(2, 'January 16–31, 2025', '2025-01-16', '2025-01-31', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(3, 'February 1–15, 2025', '2025-02-01', '2025-02-15', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(4, 'February 16–28, 2025', '2025-02-16', '2025-02-28', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(5, 'March 1–15, 2025', '2025-03-01', '2025-03-15', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(6, 'March 16–31, 2025', '2025-03-16', '2025-03-31', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(7, 'April 1–15, 2025', '2025-04-01', '2025-04-15', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(8, 'April 16–30, 2025', '2025-04-16', '2025-04-30', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(9, 'May 1–15, 2025', '2025-05-01', '2025-05-15', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(10, 'May 16–31, 2025', '2025-05-16', '2025-05-31', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(11, 'June 1–15, 2025', '2025-06-01', '2025-06-15', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(12, 'June 16–30, 2025', '2025-06-16', '2025-06-30', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(13, 'July 1–15, 2025', '2025-07-01', '2025-07-15', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(14, 'July 16–31, 2025', '2025-07-16', '2025-07-31', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(15, 'August 1–15, 2025', '2025-08-01', '2025-08-15', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(16, 'August 16–31, 2025', '2025-08-16', '2025-08-31', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(17, 'September 1–15, 2025', '2025-09-01', '2025-09-15', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(18, 'September 16–30, 2025', '2025-09-16', '2025-09-30', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(19, 'October 1–15, 2025', '2025-10-01', '2025-10-15', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(20, 'October 16–31, 2025', '2025-10-16', '2025-10-31', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(21, 'November 1–15, 2025', '2025-11-01', '2025-11-15', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(22, 'November 16–30, 2025', '2025-11-16', '2025-11-30', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(23, 'December 1–15, 2025', '2025-12-01', '2025-12-15', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(24, 'December 16–31, 2025', '2025-12-16', '2025-12-31', 'semi-monthly', 0, '2025-07-27 08:42:55'),
(25, 'January 1–31, 2025', '2025-01-01', '2025-01-31', 'monthly', 0, '2025-07-28 02:03:04'),
(26, 'February 1–28, 2025', '2025-02-01', '2025-02-28', 'monthly', 0, '2025-07-28 02:03:04'),
(27, 'March 1–31, 2025', '2025-03-01', '2025-03-31', 'monthly', 0, '2025-07-28 02:03:04'),
(28, 'April 1–30, 2025', '2025-04-01', '2025-04-30', 'monthly', 0, '2025-07-28 02:03:04'),
(29, 'May 1–31, 2025', '2025-05-01', '2025-05-31', 'monthly', 0, '2025-07-28 02:03:04'),
(30, 'June 1–30, 2025', '2025-06-01', '2025-06-30', 'monthly', 0, '2025-07-28 02:03:04'),
(31, 'July 1–31, 2025', '2025-07-01', '2025-07-31', 'monthly', 0, '2025-07-28 02:03:04'),
(32, 'August 1–31, 2025', '2025-08-01', '2025-08-31', 'monthly', 0, '2025-07-28 02:03:04'),
(33, 'September 1–30, 2025', '2025-09-01', '2025-09-30', 'monthly', 0, '2025-07-28 02:03:04'),
(34, 'October 1–31, 2025', '2025-10-01', '2025-10-31', 'monthly', 0, '2025-07-28 02:03:04'),
(35, 'November 1–30, 2025', '2025-11-01', '2025-11-30', 'monthly', 0, '2025-07-28 02:03:04'),
(36, 'December 1–31, 2025', '2025-12-01', '2025-12-31', 'monthly', 0, '2025-07-28 02:03:04');

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
(1, 2024, 1, '2025-07-27 08:42:54');

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

--
-- Dumping data for table `positions_tbl`
--

INSERT INTO `positions_tbl` (`position_id`, `position_refnum`, `position_itemnum`, `position_title`, `salary_grade`, `position_type`, `dept_id`, `position_status`) VALUES
(4, '001', '', 'Administrative Aide I', 1, '1', 7, '0'),
(5, '002', '02', 'Administrative Assistant III ( Senior Bookkeeper )', 9, '0', 7, '1'),
(6, '003', '05', 'Administrative Aide IV ( Acctg. Clerk I )', 4, '0', 7, '1'),
(7, '004', '3', 'Administrative Offier IV', 15, '0', 7, '1'),
(8, '005', '6', 'Administrative Officer II ( Mgt. and Audit Analyst )', 11, '0', 7, '1'),
(9, '006', '', 'SB Member', 99, '0', 3, '1'),
(10, '007', '', 'Administrative Officer IV', 15, '0', 7, '1'),
(11, '008', '100', 'Cashier I', 11, '0', 5, '1'),
(12, '009', '03', 'Administrative Assistant II ( Bookkeeper I )', 8, '0', 7, '1'),
(13, '010', '04', 'Administrative Assistant II ( Acctg. Clerk III )', 8, '0', 7, '1'),
(14, '011', '', 'Municipal Accountant', 24, '0', 7, '1'),
(15, '5434345', '4353453', 'SB Member 2', 25, '0', 3, '1'),
(16, '5364564', '56', 'Computer Programmer I', 11, '0', 5, '1'),
(17, '456456', '', 'Plantilla', 1, '1', 7, '1'),
(18, '456564', '', 'Plantilla', 1, '1', 7, '1');

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

--
-- Dumping data for table `remittances`
--

INSERT INTO `remittances` (`remittance_id`, `remittance_type`, `period_start`, `period_end`, `employee_totals`, `employer_totals`, `total_amount`, `status`, `or_number`, `reference_no`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 'tax', '2025-10-01', '2025-10-31', 900.00, 0.00, 900.00, 'Pending', '', '', NULL, '2025-11-13 11:57:30', '2025-11-13 11:57:30'),
(2, 'gsis', '2025-10-01', '2025-10-31', 3265.70, 15303.36, 18569.06, 'Pending', '', '', NULL, '2025-11-13 11:57:30', '2025-11-13 11:57:30'),
(3, 'gsis_ecc', '2025-10-01', '2025-10-31', 0.00, 400.00, 400.00, 'Pending', '', '', NULL, '2025-11-13 11:57:30', '2025-11-13 11:57:30'),
(4, 'philhealth', '2025-10-01', '2025-10-31', 2550.00, 3188.21, 5738.21, 'Pending', '', '', NULL, '2025-11-13 11:57:30', '2025-11-13 11:57:30'),
(5, 'pagibig', '2025-10-01', '2025-10-31', 3300.00, 800.00, 4100.00, 'Pending', '', '', NULL, '2025-11-13 11:57:30', '2025-11-13 11:57:30'),
(6, 'sss', '2025-10-01', '2025-10-31', 1250.00, 0.00, 1250.00, 'Pending', '', '', NULL, '2025-11-13 11:57:30', '2025-11-13 11:57:30'),
(7, 'loans', '2025-10-01', '2025-10-31', 15200.00, 0.00, 15200.00, 'Pending', '', '', NULL, '2025-11-13 11:57:30', '2025-11-13 11:57:30'),
(8, 'tax', '2025-11-01', '2025-11-30', 900.00, 0.00, 900.00, 'Pending', '', '', NULL, '2025-11-13 13:43:09', '2025-11-13 13:43:09'),
(9, 'gsis', '2025-11-01', '2025-11-30', 3265.70, 15303.36, 18569.06, 'Pending', '', '', NULL, '2025-11-13 13:43:09', '2025-11-13 13:43:09'),
(10, 'gsis_ecc', '2025-11-01', '2025-11-30', 0.00, 400.00, 400.00, 'Pending', '', '', NULL, '2025-11-13 13:43:09', '2025-11-13 13:43:09'),
(11, 'philhealth', '2025-11-01', '2025-11-30', 2050.00, 3188.21, 5238.21, 'Pending', '', '', NULL, '2025-11-13 13:43:09', '2025-11-13 13:43:09'),
(12, 'pagibig', '2025-11-01', '2025-11-30', 3300.00, 800.00, 4100.00, 'Pending', '', '', NULL, '2025-11-13 13:43:09', '2025-11-13 13:43:09'),
(13, 'sss', '2025-11-01', '2025-11-30', 4349.99, 0.00, 4349.99, 'Pending', '', '', NULL, '2025-11-13 13:43:09', '2025-11-13 13:43:09'),
(14, 'loans', '2025-11-01', '2025-11-30', 10700.00, 0.00, 10700.00, 'Pending', '', '', NULL, '2025-11-13 13:43:09', '2025-11-13 13:43:09'),
(15, 'others', '2025-11-01', '2025-11-30', 0.00, 0.00, 0.00, 'Pending', '', '', NULL, '2025-11-13 13:43:09', '2025-11-13 13:43:09'),
(16, 'tax', '2025-12-01', '2025-12-31', 900.00, 0.00, 900.00, 'Pending', '', '', NULL, '2025-11-13 13:45:33', '2026-01-22 13:31:38'),
(17, 'gsis', '2025-12-01', '2025-12-31', 3265.70, 15303.36, 18569.06, 'Pending', '', '', NULL, '2025-11-13 13:45:33', '2026-01-22 13:31:38'),
(18, 'gsis_ecc', '2025-12-01', '2025-12-31', 0.00, 400.00, 400.00, 'Pending', '', '', NULL, '2025-11-13 13:45:33', '2026-01-22 13:31:38'),
(19, 'philhealth', '2025-12-01', '2025-12-31', 2050.00, 3188.21, 5238.21, 'Pending', '', '', NULL, '2025-11-13 13:45:33', '2026-01-22 13:31:38'),
(20, 'pagibig', '2025-12-01', '2025-12-31', 3300.00, 800.00, 4100.00, 'Pending', '', '', NULL, '2025-11-13 13:45:33', '2026-01-22 13:31:38'),
(21, 'sss', '2025-12-01', '2025-12-31', 4349.99, 0.00, 4349.99, 'Pending', '', '', NULL, '2025-11-13 13:45:33', '2026-01-22 13:31:38'),
(22, 'loans', '2025-12-01', '2025-12-31', 10700.00, 0.00, 10700.00, 'Pending', '', '', NULL, '2025-11-13 13:45:33', '2026-01-22 13:31:38'),
(23, 'others', '2025-12-01', '2025-12-31', 32513.00, 0.00, 32513.00, 'Pending', '', '', NULL, '2025-11-13 13:45:33', '2026-01-22 13:31:38');

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

--
-- Dumping data for table `remittance_details`
--

INSERT INTO `remittance_details` (`remit_detail_id`, `remittance_id`, `employee_id`, `config_deduction_id`, `govshare_id`, `remittance_type`, `amount`, `govshare_amt`) VALUES
(1, 1, 7, 0, 0, 'tax', 0.00, 0.00),
(2, 1, 14, 0, 0, 'tax', 0.00, 0.00),
(3, 1, 13, 0, 0, 'tax', 0.00, 0.00),
(4, 1, 15, 0, 0, 'tax', 0.00, 0.00),
(5, 1, 12, 0, 0, 'tax', 0.00, 0.00),
(6, 1, 2, 0, 0, 'tax', 0.00, 0.00),
(7, 1, 10, 1, 0, 'tax', 900.00, 0.00),
(8, 1, 3, 0, 0, 'tax', 0.00, 0.00),
(9, 2, 7, 0, 1, 'gsis', 0.00, 1556.04),
(10, 2, 14, 0, 1, 'gsis', 0.00, 1971.24),
(11, 2, 13, 0, 1, 'gsis', 0.00, 9184.08),
(12, 2, 15, 0, 0, 'gsis', 0.00, 0.00),
(13, 2, 12, 0, 0, 'gsis', 0.00, 0.00),
(14, 2, 2, 0, 0, 'gsis', 0.00, 0.00),
(15, 2, 10, 2, 0, 'gsis', 2765.70, 0.00),
(16, 2, 3, 2, 1, 'gsis', 500.00, 2592.00),
(17, 3, 7, 0, 8, 'gsis_ecc', 0.00, 100.00),
(18, 3, 14, 0, 8, 'gsis_ecc', 0.00, 100.00),
(19, 3, 13, 0, 8, 'gsis_ecc', 0.00, 100.00),
(20, 3, 15, 0, 0, 'gsis_ecc', 0.00, 0.00),
(21, 3, 12, 0, 0, 'gsis_ecc', 0.00, 0.00),
(22, 3, 2, 0, 0, 'gsis_ecc', 0.00, 0.00),
(23, 3, 10, 0, 0, 'gsis_ecc', 0.00, 0.00),
(24, 3, 3, 0, 8, 'gsis_ecc', 0.00, 100.00),
(25, 4, 7, 21, 7, 'philhealth', 500.00, 324.18),
(26, 4, 14, 21, 7, 'philhealth', 1050.00, 410.68),
(27, 4, 13, 21, 7, 'philhealth', 500.00, 1913.35),
(28, 4, 15, 0, 0, 'philhealth', 0.00, 0.00),
(29, 4, 12, 0, 0, 'philhealth', 0.00, 0.00),
(30, 4, 2, 0, 0, 'philhealth', 0.00, 0.00),
(31, 4, 10, 0, 0, 'philhealth', 0.00, 0.00),
(32, 4, 3, 21, 7, 'philhealth', 500.00, 540.00),
(33, 5, 7, 0, 5, 'pagibig', 0.00, 200.00),
(34, 5, 14, 0, 5, 'pagibig', 0.00, 200.00),
(35, 5, 13, 0, 5, 'pagibig', 0.00, 200.00),
(36, 5, 15, 0, 0, 'pagibig', 0.00, 0.00),
(37, 5, 12, 0, 0, 'pagibig', 0.00, 0.00),
(38, 5, 2, 0, 0, 'pagibig', 0.00, 0.00),
(39, 5, 10, 4, 0, 'pagibig', 3000.00, 0.00),
(40, 5, 3, 4, 5, 'pagibig', 300.00, 200.00),
(41, 6, 7, 0, 0, 'sss', 0.00, 0.00),
(42, 6, 14, 0, 0, 'sss', 0.00, 0.00),
(43, 6, 13, 0, 0, 'sss', 0.00, 0.00),
(44, 6, 15, 0, 0, 'sss', 0.00, 0.00),
(45, 6, 12, 0, 0, 'sss', 0.00, 0.00),
(46, 6, 2, 0, 0, 'sss', 0.00, 0.00),
(47, 6, 10, 14, 0, 'sss', 750.00, 0.00),
(48, 6, 3, 14, 0, 'sss', 500.00, 0.00),
(49, 7, 13, 20, 0, 'MPL_FLEX', 5000.00, 0.00),
(50, 7, 3, 20, 0, 'MPL_FLEX', 4500.00, 0.00),
(51, 7, 10, 16, 0, 'PLREG', 200.00, 0.00),
(52, 7, 14, 18, 0, 'PMGEA_LN', 3000.00, 0.00),
(53, 7, 7, 8, 0, 'RRB_LN', 2500.00, 0.00),
(54, 8, 7, 0, 0, 'tax', 0.00, 0.00),
(55, 8, 14, 0, 0, 'tax', 0.00, 0.00),
(56, 8, 13, 0, 0, 'tax', 0.00, 0.00),
(57, 8, 15, 0, 0, 'tax', 0.00, 0.00),
(58, 8, 12, 0, 0, 'tax', 0.00, 0.00),
(59, 8, 2, 0, 0, 'tax', 0.00, 0.00),
(60, 8, 10, 1, 0, 'tax', 900.00, 0.00),
(61, 8, 3, 0, 0, 'tax', 0.00, 0.00),
(62, 9, 7, 0, 1, 'gsis', 0.00, 1556.04),
(63, 9, 14, 0, 1, 'gsis', 0.00, 1971.24),
(64, 9, 13, 0, 1, 'gsis', 0.00, 9184.08),
(65, 9, 15, 0, 0, 'gsis', 0.00, 0.00),
(66, 9, 12, 0, 0, 'gsis', 0.00, 0.00),
(67, 9, 2, 0, 0, 'gsis', 0.00, 0.00),
(68, 9, 10, 2, 0, 'gsis', 2765.70, 0.00),
(69, 9, 3, 2, 1, 'gsis', 500.00, 2592.00),
(70, 10, 7, 0, 8, 'gsis_ecc', 0.00, 100.00),
(71, 10, 14, 0, 8, 'gsis_ecc', 0.00, 100.00),
(72, 10, 13, 0, 8, 'gsis_ecc', 0.00, 100.00),
(73, 10, 15, 0, 0, 'gsis_ecc', 0.00, 0.00),
(74, 10, 12, 0, 0, 'gsis_ecc', 0.00, 0.00),
(75, 10, 2, 0, 0, 'gsis_ecc', 0.00, 0.00),
(76, 10, 10, 0, 0, 'gsis_ecc', 0.00, 0.00),
(77, 10, 3, 0, 8, 'gsis_ecc', 0.00, 100.00),
(78, 11, 7, 21, 7, 'philhealth', 500.00, 324.18),
(79, 11, 14, 21, 7, 'philhealth', 1050.00, 410.68),
(80, 11, 13, 21, 7, 'philhealth', 500.00, 1913.35),
(81, 11, 15, 0, 0, 'philhealth', 0.00, 0.00),
(82, 11, 12, 0, 0, 'philhealth', 0.00, 0.00),
(83, 11, 2, 0, 0, 'philhealth', 0.00, 0.00),
(84, 11, 10, 0, 0, 'philhealth', 0.00, 0.00),
(85, 11, 3, 0, 7, 'philhealth', 0.00, 540.00),
(86, 12, 7, 0, 5, 'pagibig', 0.00, 200.00),
(87, 12, 14, 0, 5, 'pagibig', 0.00, 200.00),
(88, 12, 13, 0, 5, 'pagibig', 0.00, 200.00),
(89, 12, 15, 0, 0, 'pagibig', 0.00, 0.00),
(90, 12, 12, 0, 0, 'pagibig', 0.00, 0.00),
(91, 12, 2, 0, 0, 'pagibig', 0.00, 0.00),
(92, 12, 10, 4, 0, 'pagibig', 3000.00, 0.00),
(93, 12, 3, 4, 5, 'pagibig', 300.00, 200.00),
(94, 13, 7, 14, 0, 'sss', 1599.99, 0.00),
(95, 13, 14, 0, 0, 'sss', 0.00, 0.00),
(96, 13, 13, 14, 0, 'sss', 2000.00, 0.00),
(97, 13, 15, 0, 0, 'sss', 0.00, 0.00),
(98, 13, 12, 0, 0, 'sss', 0.00, 0.00),
(99, 13, 2, 0, 0, 'sss', 0.00, 0.00),
(100, 13, 10, 14, 0, 'sss', 750.00, 0.00),
(101, 13, 3, 0, 0, 'sss', 0.00, 0.00),
(102, 14, 13, 20, 0, 'MPL_FLEX', 5000.00, 0.00),
(103, 14, 10, 16, 0, 'PLREG', 200.00, 0.00),
(104, 14, 14, 18, 0, 'PMGEA_LN', 3000.00, 0.00),
(105, 14, 7, 8, 0, 'RRB_LN', 2500.00, 0.00),
(110, 16, 7, 0, 0, 'tax', 0.00, 0.00),
(111, 16, 14, 0, 0, 'tax', 0.00, 0.00),
(112, 16, 13, 0, 0, 'tax', 0.00, 0.00),
(113, 16, 15, 0, 0, 'tax', 0.00, 0.00),
(114, 16, 12, 0, 0, 'tax', 0.00, 0.00),
(115, 16, 2, 0, 0, 'tax', 0.00, 0.00),
(116, 16, 10, 1, 0, 'tax', 900.00, 0.00),
(117, 16, 3, 0, 0, 'tax', 0.00, 0.00),
(118, 17, 7, 0, 1, 'gsis', 0.00, 1556.04),
(119, 17, 14, 0, 1, 'gsis', 0.00, 1971.24),
(120, 17, 13, 0, 1, 'gsis', 0.00, 9184.08),
(121, 17, 15, 0, 0, 'gsis', 0.00, 0.00),
(122, 17, 12, 0, 0, 'gsis', 0.00, 0.00),
(123, 17, 2, 0, 0, 'gsis', 0.00, 0.00),
(124, 17, 10, 2, 0, 'gsis', 2765.70, 0.00),
(125, 17, 3, 2, 1, 'gsis', 500.00, 2592.00),
(126, 18, 7, 0, 8, 'gsis_ecc', 0.00, 100.00),
(127, 18, 14, 0, 8, 'gsis_ecc', 0.00, 100.00),
(128, 18, 13, 0, 8, 'gsis_ecc', 0.00, 100.00),
(129, 18, 15, 0, 0, 'gsis_ecc', 0.00, 0.00),
(130, 18, 12, 0, 0, 'gsis_ecc', 0.00, 0.00),
(131, 18, 2, 0, 0, 'gsis_ecc', 0.00, 0.00),
(132, 18, 10, 0, 0, 'gsis_ecc', 0.00, 0.00),
(133, 18, 3, 0, 8, 'gsis_ecc', 0.00, 100.00),
(134, 19, 7, 21, 7, 'philhealth', 500.00, 324.18),
(135, 19, 14, 21, 7, 'philhealth', 1050.00, 410.68),
(136, 19, 13, 21, 7, 'philhealth', 500.00, 1913.35),
(137, 19, 15, 0, 0, 'philhealth', 0.00, 0.00),
(138, 19, 12, 0, 0, 'philhealth', 0.00, 0.00),
(139, 19, 2, 0, 0, 'philhealth', 0.00, 0.00),
(140, 19, 10, 0, 0, 'philhealth', 0.00, 0.00),
(141, 19, 3, 0, 7, 'philhealth', 0.00, 540.00),
(142, 20, 7, 0, 5, 'pagibig', 0.00, 200.00),
(143, 20, 14, 0, 5, 'pagibig', 0.00, 200.00),
(144, 20, 13, 0, 5, 'pagibig', 0.00, 200.00),
(145, 20, 15, 0, 0, 'pagibig', 0.00, 0.00),
(146, 20, 12, 0, 0, 'pagibig', 0.00, 0.00),
(147, 20, 2, 0, 0, 'pagibig', 0.00, 0.00),
(148, 20, 10, 4, 0, 'pagibig', 3000.00, 0.00),
(149, 20, 3, 4, 5, 'pagibig', 300.00, 200.00),
(150, 21, 7, 14, 0, 'sss', 1599.99, 0.00),
(151, 21, 14, 0, 0, 'sss', 0.00, 0.00),
(152, 21, 13, 14, 0, 'sss', 2000.00, 0.00),
(153, 21, 15, 0, 0, 'sss', 0.00, 0.00),
(154, 21, 12, 0, 0, 'sss', 0.00, 0.00),
(155, 21, 2, 0, 0, 'sss', 0.00, 0.00),
(156, 21, 10, 14, 0, 'sss', 750.00, 0.00),
(157, 21, 3, 0, 0, 'sss', 0.00, 0.00),
(158, 22, 13, 20, 0, 'MPL_FLEX', 5000.00, 0.00),
(159, 22, 10, 16, 0, 'PLREG', 200.00, 0.00),
(160, 22, 14, 18, 0, 'PMGEA_LN', 3000.00, 0.00),
(161, 22, 7, 8, 0, 'RRB_LN', 2500.00, 0.00),
(258, 23, 7, 23, 0, 'AIA', 555.00, 0.00),
(259, 23, 14, 23, 0, 'AIA', 4561.00, 0.00),
(260, 23, 13, 23, 0, 'AIA', 10999.00, 0.00),
(261, 23, 10, 23, 0, 'AIA', 399.00, 0.00),
(262, 23, 3, 23, 0, 'AIA', 15999.00, 0.00);

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
  MODIFY `config_deduction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  MODIFY `employee_deduction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `employee_deductions_components`
--
ALTER TABLE `employee_deductions_components`
  MODIFY `deduction_component_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `employee_earnings`
--
ALTER TABLE `employee_earnings`
  MODIFY `employee_earning_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `employee_earnings_components`
--
ALTER TABLE `employee_earnings_components`
  MODIFY `earning_component_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `employee_employments_tbl`
--
ALTER TABLE `employee_employments_tbl`
  MODIFY `employment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `employee_govshares`
--
ALTER TABLE `employee_govshares`
  MODIFY `employee_govshare_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

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
  MODIFY `govshare_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
  MODIFY `payroll_deduction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `payroll_entries`
--
ALTER TABLE `payroll_entries`
  MODIFY `payroll_entry_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `payroll_frequencies`
--
ALTER TABLE `payroll_frequencies`
  MODIFY `payroll_freq_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payroll_govshares`
--
ALTER TABLE `payroll_govshares`
  MODIFY `payroll_govshare_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  MODIFY `payroll_period_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `payroll_year_controls`
--
ALTER TABLE `payroll_year_controls`
  MODIFY `payroll_year_control_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `positions_tbl`
--
ALTER TABLE `positions_tbl`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `remittances`
--
ALTER TABLE `remittances`
  MODIFY `remittance_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `remittance_details`
--
ALTER TABLE `remittance_details`
  MODIFY `remit_detail_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=606;

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

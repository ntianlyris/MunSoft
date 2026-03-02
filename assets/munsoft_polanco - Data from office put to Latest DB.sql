-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2026 at 06:32 PM
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
(2, 2, '20201020-1', 'LR_INS', 'Life and Retirement Ins. Premiums', 1, 'STATUTORY'),
(4, 3, '20201030-1', 'PAG_PREM', 'Pag-ibig Premium Contributions', 1, 'STATUTORY'),
(5, 3, '20201030-2', 'PAG_MPL', 'Multi-Purpose Loan', 0, 'LOAN'),
(8, 5, '29999990-4', 'RRB_LN', 'RRB Salary Loan', 0, 'LOAN'),
(12, 5, '29999990-11', 'LBP_LN', 'LBP Salary Loan', 0, 'LOAN'),
(14, 5, '29999990-9', 'SSS_PREM', 'SSS Premium Contributions', 0, 'OTHER'),
(16, 2, '20201020-6', 'PLREG', 'Policy Loan - Regular', 0, 'LOAN'),
(17, 5, '29999990-5', 'PMG_DUES', 'PMGEA Monthly Dues', 0, ''),
(18, 5, '29999990-6', 'PMG_LN', 'PMGEA Loan', 0, 'LOAN'),
(19, 2, '20201020-14', 'GFAL', 'GSIS GFAL', 0, 'LOAN'),
(20, 3, '20201030-3', 'LOT_LN', 'Pag-ibig Lot Loan', 0, 'LOAN'),
(21, 4, '20201040', 'PHIC-PREM', 'PhilHealth Premiums', 1, 'STATUTORY'),
(22, 5, '29999990-3', 'PMCO_LN', 'POEMCO Salary Loan', 0, 'LOAN'),
(23, 2, '20201020-2', 'ECC', 'Employee Compensation Contribution', 1, 'STATUTORY'),
(24, 2, '20201020-8', 'SAL_LN', 'Salary Loan/Conso Loan', 0, 'LOAN'),
(25, 2, '20201020-9', 'EMG_LN', 'Emergency Loan', 0, 'LOAN'),
(26, 2, '20201020-15', 'COM_LN', 'Computer Loan', 0, 'LOAN'),
(27, 2, '20201020-16', 'GMPL', 'GSIS Multi-Purpose Loan', 0, 'LOAN'),
(28, 2, '20201020-17', 'MPLTE', 'GSIS MPL Lite', 0, 'LOAN'),
(29, 3, '20201030-4', 'HSNG_LN', 'Pag-ibig Housing Loan', 0, 'LOAN'),
(30, 3, '20201030-5', 'CAL_LN', 'Pag-ibig Calamity Loan', 0, 'LOAN'),
(31, 3, '20201030-6', 'MP2', 'Pag-ibig MP2 Savings', 0, 'OTHER'),
(32, 5, '29999990-7', 'CRB_LN', 'CRBDI Salary Loan', 0, 'LOAN'),
(33, 5, '29999990-8', 'INS_AIA', 'Pension Insurance - PhilAm Life Ins. (AIA)', 0, 'OTHER');

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
(1, '', '097210-150', 'Evenie Rose', 'Acopiado', 'Abad', '', '2000-10-13', 'Female', 'Single', 'Villahermosa, Polanco, Z.N.', 'Licensed Professional Teacher', '2025-01-02', '', 'Active', '', '', '', '', '', '1'),
(2, '', '097210-939', 'Anjelyn', 'J', 'Agecia', '', '1994-07-12', 'Female', 'Single', 'Villahermosa, Polanco, Z.N.', 'Licensed Agriculturists', '2023-03-24', '', 'Active', '', '', '', '', '', '1'),
(3, '', '097210-296', 'Cheryl', 'M', 'Aguinaldo', '', '1985-08-31', 'Female', 'Married', 'Pob. South, Polanco, Z.N.', 'Social Worker', '2025-03-24', '', 'Active', '', '', '', '', '', '1'),
(4, '', '097210-039', 'Laura', 'A', 'Alfon', '', '1968-12-07', 'Female', 'Single', 'Pob. North, Polanco, Z.N.', 'Municipal Assessor', '1997-02-06', '', 'Active', '', '', '', '', '', '1'),
(5, '', '097210-177', 'Annie Rose', 'M', 'Anciano', '', '1995-12-03', 'Female', 'Single', 'Labrador, Polanco, Z.N.', 'CSE Professional', '2021-12-01', '', 'Active', '', '', '', '', '', '1'),
(6, '', '097210-018', 'Ma. Roselle', 'C', 'Aniñon', '', '1978-08-11', 'Female', 'Married', 'Pob. North, Polanco, Z.N.', 'Municipal Budget Officer', '2000-02-16', '', 'Active', '', '', '', '', '', '1'),
(7, '', '097210-672', 'Imee', 'S', 'Arellano', '', '1993-04-11', 'Female', 'Single', 'New Sicayab, Polanco, Z.N.', 'Licensed Professional Teacher', '2023-02-01', '', 'Active', '', '', '', '', '', '1'),
(8, '', '097210-241', 'Caren Eve', 'P', 'Astillero', '', '1993-09-07', 'Female', 'Married', 'Pob. North, Polanco, Z.N.', 'CSE Professional', '2024-02-01', '', 'Active', '', '', '', '', '', '1'),
(9, '', '097210-047', 'Airin', 'B', 'Ates', '', '1974-08-30', 'Female', 'Married', 'Pob. North, Polanco, Z.N.', 'Midwife', '1994-01-02', '', 'Active', '', '', '', '', '', '1'),
(10, '', '097210-070', 'Gerbert', 'L', 'Balaga', '', '1968-05-25', 'Male', 'Married', 'Pob. South, Polanco, Z.N.', 'Engineer', '1994-01-02', '', 'Active', '', '', '', '', '', '1'),
(11, '', '097210-857', 'Pilita', 'T', 'Balaga', '', '1968-10-13', 'Female', 'Married', 'Pob. South, Polanco, Z.N.', 'CSE Professional', '2022-03-01', '', 'Active', '', '', '', '', '', '1'),
(12, '', '097210-056', 'Gemma', 'C', 'Baluntang', '', '1974-01-15', 'Female', 'Married', 'Pob. South, Polanco, Z.N.', 'CSE Professional', '2008-02-01', '', 'Active', '', '', '', '', '', '1'),
(13, '', '097210-368', 'Carl', 'B', 'Barba', '', '1995-07-02', 'Male', 'Single', 'Obay, Polanco, Z.N.', 'IT Specialist', '2022-02-02', '', 'Active', '', '', '', '', '', '1'),
(14, '', '097210-049', 'Maria Aileen', 'B', 'Barba', '', '1972-11-18', 'Female', 'Married', 'Obay, Polanco, Z.N.', 'Social Worker', '2006-05-16', '', 'Active', '', '', '', '', '', '1'),
(15, '', '097210-042', 'Emma', 'G', 'Barte', '', '1968-03-10', 'Female', 'Widow', 'Lapayanbaja, Polanco, Z.N.', 'Midwife', '1994-01-02', '', 'Active', '', '', '', '', '', '1'),
(16, '', '097210-593', 'Robemar', 'V', 'Benitez', '', '1990-09-14', 'Male', 'Married', 'Sto. Niño, Polanco, Z.N.', 'CSE Professional', '2023-08-01', '', 'Active', '', '', '', '', '', '0'),
(17, '', '097210-334', 'Marinel', 'C', 'Bonocan', '', '1994-09-15', 'Female', 'Married', 'Pian, Polanco, Z.N.', 'Licensed Professional Teacher', '2024-02-13', '', 'Active', '', '', '', '', '', '1'),
(18, '', '097210-072', 'Chirilie', 'O', 'Butawan', '', '1986-01-10', 'Female', 'Single', 'Dansullan, Polanco, Z.N.', 'Midwife', '2012-02-04', '', 'Active', '', '', '', '', '', '1'),
(19, '', '097210-273', 'Marieta', 'A', 'Cabrera', '', '1972-05-26', 'Female', 'Single', 'Pob. South, Polanco, Z.N.', 'Engineer', '1998-01-04', '', 'Active', '', '', '', '', '', '1'),
(20, '', '097210-058', 'Vilma', 'A', 'Cabrera', '', '1970-09-27', 'Female', 'Single', 'Pob. South, Polanco, Z.N.', 'CSE Professional', '1994-01-17', '', 'Active', '', '', '', '', '', '1'),
(21, '', '097210-048', 'Antonio', 'M', 'Cademas', '', '1969-06-13', 'Male', 'Married', 'Gulayon, Dipolog City, Z.N.', 'Sanitation Inspector', '2006-05-16', '', 'Active', '', '', '', '', '', '1'),
(22, '', '097210-631', 'Roena Mae', 'M', 'Cagadas', '', '1990-09-25', 'Female', 'Married', 'Pob. North, Polanco, Z.N.', 'Licensed Professional Teacher', '2022-02-02', '', 'Active', '', '', '', '', '', '1'),
(23, '', '097210-071', 'Manuel', 'R', 'Cajocon', 'Jr.', '1979-10-30', 'Male', 'Single', 'Anastacio, Polanco, Z.N.', 'HR Management Officer', '2004-02-02', '', 'Active', '', '', '', '', '', '1'),
(24, '', '097210-007', 'Eda', 'A', 'Calalang', '', '1974-09-22', 'Female', 'Single', 'Pob. North, Polanco, Z.N.', 'CSE Professional', '2013-03-27', '', 'Active', '', '', '', '', '', '1'),
(25, '', '097210-004', 'Arvin', 'C', 'Calamba', '', '1972-01-21', 'Male', 'Single', 'Gulayon, Dipolog City, Z.N.', 'Municipal Treasurer', '2004-04-10', '', 'Active', '', '', '', '', '', '1'),
(26, '', '097210-025', 'Jolan', 'S', 'Campos', '', '1983-05-04', 'Male', 'Married', 'Labrador, Polanco, Z.N.', 'Mechanical Specialist', '2008-02-10', '', 'Active', '', '', '', '', '', '1'),
(27, '', '097210-032', 'Erma', 'S', 'Caninit', '', '1986-04-27', 'Female', 'Single', 'Labrador, Polanco, Z.N.', 'Nurse', '2019-08-01', '', 'Active', '', '', '', '', '', '1'),
(28, '', '097210-330', 'Ian', 'T', 'Caninit', '', '1977-09-01', 'Male', 'Married', 'Linabo, Polanco, Z.N.', 'CSE Professional', '2020-08-03', '', 'Active', '', '', '', '', '', '1'),
(29, '', '097210-002', 'Jesebel', 'B', 'Cantoja', '', '1979-01-27', 'Female', 'Married', 'Sto. Niño, Polanco, Z.N.', 'CSE Professional', '2006-08-02', '', 'Active', '', '', '', '', '', '1'),
(30, '', '097210-274', 'Raffy', 'M', 'Cantoja', '', '1975-10-07', 'Male', 'Married', 'Sto. Niño, Polanco, Z.N.', 'CSE Professional', '2004-01-07', '', 'Active', '', '', '', '', '', '1'),
(31, '', '097210-065', 'Lorna', 'E', 'Carcellar', '', '1962-10-23', 'Female', 'Married', 'Pob. South, Polanco, Z.N.', 'CSE Professional', '1994-01-02', '', 'Active', '', '', '', '', '', '1'),
(32, '', '097210-012', 'Mark Louie', 'M', 'Carpo', '', '1988-01-29', 'Male', 'Single', 'Pob. North, Polanco, Z.N.', 'Chemical Engineer', '2020-10-01', '', 'Active', '', '', '', '', '', '1'),
(33, '', '097210-295', 'Catherine', 'M', 'Caulawon', '', '1995-03-03', 'Female', 'Single', 'Pob. South, Polanco, Z.N.', 'Municipal Social Welfare', '2019-12-02', '', 'Active', '', '', '', '', '', '1'),
(34, '', '097210-016', 'Alfred Mel', 'P', 'Dagaylo-an', '', '1977-01-13', 'Male', 'Married', 'Victoria CH, Obay, Polanco, Z.N.', 'Veterinarian', '2003-03-03', '', 'Active', '', '', '', '', '', '1'),
(35, '', '097210-248', 'Paul Ariel', 'A', 'Dalmacio', '', '1971-11-24', 'Male', 'Married', 'Obay, Polanco, Z.N.', 'Market Inspector', '2019-09-02', '', 'Active', '', '', '', '', '', '1'),
(36, '', '097210-017', 'Jose Benjie Aldrin', 'A', 'Dalumpines', '', '1973-11-27', 'Male', 'Married', 'Gulayon, Dipolog City, Z.N.', 'Engineer', '2005-01-02', '', 'Active', '', '', '', '', '', '1'),
(37, '', '097210-124', 'Emerson', 'S', 'Danduan', '', '1991-09-15', 'Male', 'Single', 'Pob. North, Polanco, Z.N.', 'Engineer', '2022-02-02', '', 'Active', '', '', '', '', '', '1'),
(38, '', '097210-059', 'Marcelito', 'N', 'Dela Cruz', '', '1985-11-30', 'Male', 'Single', 'Pob. South, Polanco, Z.N.', 'Nurse', '2008-10-12', '', 'Active', '', '', '', '', '', '1'),
(39, '', '097210-151', 'Jesse Manuel', 'C', 'Donal', '', '2000-12-10', 'Male', 'Single', 'Pob. North, Polanco, Z.N.', 'CSE Professional', '2024-02-01', '', 'Active', '', '', '', '', '', '1'),
(40, '', '097210-143', 'Sophia Mae', 'C', 'Donal', '', '1999-09-13', 'Female', 'Single', 'Pob. North, Polanco, Z.N.', 'CSE Professional', '2024-06-18', '', 'Active', '', '', '', '', '', '1'),
(41, '', '097210-052', 'Elma', 'P', 'Duran', '', '1974-11-16', 'Female', 'Married', 'Pob. South, Polanco, Z.N.', 'Midwife', '2008-12-10', '', 'Active', '', '', '', '', '', '1'),
(42, '', '097210-159', 'Dorothy', 'D', 'Eramis', '', '1979-11-02', 'Female', 'Married', 'Pob. South, Polanco, Z.N.', 'CSE Professional', '2022-02-04', '', 'Active', '', '', '', '', '', '1'),
(43, '', '097210-364', 'Luzminda', 'D', 'Español', '', '1965-12-31', 'Female', 'Married', 'Sto. Niño, Polanco, Z.N.', 'Administrative Aide', '2016-07-01', '', 'Active', '', '', '', '', '', '1'),
(44, '', '097210-679', 'Anna Karene', 'E', 'Fernandez', '', '1991-03-10', 'Female', 'Single', 'Pob. North, Polanco, Z.N.', 'Cashier', '2021-04-16', '', 'Active', '', '', '', '', '', '1'),
(45, '', '097210-242', 'John Andrey', 'E', 'Fernandez', '', '1993-10-09', 'Male', 'Single', 'Pob. North, Polanco, Z.N.', 'Engineer', '2022-01-17', '', 'Active', '', '', '', '', '', '1'),
(46, '', '097210-211', 'Proserphine', 'G', 'Godinez', '', '1978-12-30', 'Female', 'Married', 'Pob. South, Polanco, Z.N.', 'Municipal Accountant', '2020-10-01', '', 'Active', '', '', '', '', '', '1'),
(47, '', '097210-005', 'Ermintrude', 'D', 'Gonzales', '', '1982-01-26', 'Female', 'Married', 'Pob. North, Polanco, Z.N.', 'Revenue Clerk', '2004-08-01', '', 'Active', '', '', '', '', '', '1'),
(48, '', '097210-029', 'Mara Michelle', 'C', 'Gonzales', '', '1989-01-17', 'Female', 'Single', 'Pob. North, Polanco, Z.N.', 'Pharmacist', '2024-03-04', '', 'Active', '', '', '', '', '', '1'),
(49, '', '097210-068', 'Ronnie', 'B', 'Gonzales', '', '1976-10-14', 'Male', 'Married', 'Pob. North, Polanco, Z.N.', 'Agriculturist', '2002-01-08', '', 'Active', '', '', '', '', '', '1'),
(50, '', '097210-020', 'Raul', 'G', 'Guitarte', '', '1975-01-22', 'Male', 'Married', 'Pob. North, Polanco, Z.N.', 'CSE Professional', '2018-12-03', '', 'Active', '', '', '', '', '', '1'),
(51, '', '097210-361', 'Daisy Joy', 'B', 'Hizon', '', '1992-08-29', 'Female', 'Married', 'Villahermosa, Polanco, Z.N.', 'Administrative Aide', '2016-07-01', '', 'Active', '', '', '', '', '', '1'),
(52, '', '097210-034', 'Arlene', 'A', 'Imperial', '', '1976-05-31', 'Female', 'Married', 'San Pedro, Polanco, Z.N.', 'Midwife', '2008-04-02', '', 'Active', '', '', '', '', '', '1'),
(53, '', '097210-506', 'Monica', 'E', 'Jakosalem', '', '1997-08-27', 'Female', 'Single', 'Pob. South, Polanco, Z.N.', 'CSE Professional', '2022-02-02', '', 'Active', '', '', '', '', '', '1'),
(54, '', '097210-044', 'Rovi', 'C', 'Jalalon', '', '1971-02-25', 'Female', 'Widow', 'Pian, Polanco, Z.N.', 'CSE Professional', '1998-02-02', '', 'Active', '', '', '', '', '', '1'),
(55, '', '097210-055', 'Roger', 'B', 'Ladera', '', '1965-06-02', 'Male', 'Married', 'Pob. South, Polanco, Z.N.', 'Municipal Planning', '1994-01-02', '', 'Active', '', '', '', '', '', '1'),
(56, '', '097210-062', 'Mercy', 'B', 'Laurque', '', '1969-03-05', 'Female', 'Married', 'Pob. North, Polanco, Z.N.', 'Administrative Assistant', '1994-02-15', '', 'Active', '', '', '', '', '', '1'),
(57, '', '097210-267', 'Kurt', 'V', 'Leones', '', '1996-08-26', 'Male', 'Single', 'Central Dipolog City, Z.N.', 'IT Specialist', '2021-12-01', '', 'Active', '', '', '', '', '', '1'),
(58, '', '097210-073', 'Gergean', 'C', 'Lura', '', '1987-05-01', 'Female', 'Married', 'Guinles, Polanco, Z.N', 'Medical Technologist', '2019-01-03', '', 'Active', '', '', '', '', '', '1'),
(59, '', '097210-555', 'Jade Kemmond', 'O', 'Mag-abo', '', '1995-03-08', 'Male', 'Single', 'Bandera, Polanco, Z.N.', 'CSE Professional', '2021-12-01', '', 'Active', '', '', '', '', '', '1'),
(60, '', '097210-040', 'Marlon', 'T', 'Magbanua', '', '1995-07-30', 'Male', 'Single', 'Lingasad, Polanco, Z.N.', 'Midwife', '2019-08-01', '', 'Active', '', '', '', '', '', '1'),
(61, '', '097210-064', 'Jennylinda', 'O', 'Magtuba', '', '1972-01-24', 'Female', 'Single', 'Lingasad, Polanco, Z.N.', 'Nurse', '2007-01-03', '', 'Active', '', '', '', '', '', '1'),
(62, '', '097210-026', 'Ivo', 'M', 'Mandantes', '', '1988-02-29', 'Male', 'Married', 'Isis, Polanco, Z.N.', 'SB Member', '2019-07-01', '', 'Active', '', '', '', '', '', '1'),
(63, '', '097210-057', 'Rosemarie', 'T', 'Mohametano', '', '1969-02-10', 'Female', 'Married', 'Lingasad, Polanco, Z.N.', 'CSE Professional', '1994-10-05', '', 'Active', '', '', '', '', '', '1'),
(64, '', '097210-196', 'Jean Renzo', 'S', 'Montano', '', '1986-11-02', 'Male', 'Married', 'Anastacio, Polanco, Z.N.', 'Nurse', '2022-01-17', '', 'Active', '', '', '', '', '', '1'),
(65, '', '097210-260', 'April Mae', 'D', 'Montemayor', '', '1997-04-30', 'Female', 'Single', 'Lingasad, Polanco, Z.N.', 'Agricultural Technologist', '2025-03-12', '', 'Active', '', '', '', '', '', '1'),
(66, '', '097210-214', 'Roselyn', 'N', 'Narvaez', '', '1987-04-22', 'Female', 'Married', 'VCH, Obay, Polanco, Z.N.', 'Nurse', '2024-06-18', '', 'Active', '', '', '', '', '', '1'),
(67, '', '097210-205', 'Almar', 'D', 'Ocupe', '', '1986-11-05', 'Male', 'Single', 'Pob. South, Polanco, Z.N.', 'Nurse', '2024-02-01', '', 'Active', '', '', '', '', '', '1'),
(68, '', '097210-713', 'Harvey', 'B', 'Oga', '', '1993-03-29', 'Male', 'Married', 'Obay, Polanco, Z.N.', 'CSE Professional', '2024-02-01', '', 'Active', '', '', '', '', '', '1'),
(69, '', '097210-359', 'Anacleto', 'D', 'Olvis', 'III', '1980-12-12', 'Male', 'Married', 'Pob. North, Polanco, Z.N.', 'Administrative Aide', '2021-03-25', '', 'Active', '', '', '', '', '', '1'),
(70, '', '097210-001', 'Evan Hope', 'D', 'Olvis', '', '1978-11-04', 'Male', 'Married', 'Pob. North, Polanco, Z.N.', 'Municipal Vice Mayor', '2016-07-01', '', 'Active', '', '', '', '', '', '1'),
(71, '', '097210-198', 'Mary Analyn', 'D', 'Olvis', '', '1984-04-25', 'Female', 'Single', 'Pob. North, Polanco, Z.N.', 'Nurse', '2021-07-15', '', 'Active', '', '', '', '', '', '1'),
(72, '', '097210-340', 'Lendy', 'L', 'Ontolan', '', '1986-03-09', 'Female', 'Single', 'Pian, Polanco, Z.N.', 'Administrative Assistant', '2025-03-12', '', 'Active', '', '', '', '', '', '1'),
(73, '', '097210-106', 'Gerard Vicson', 'S', 'Opulentisima', '', '1980-10-08', 'Male', 'Single', 'Pob. North, Polanco, Z.N.', 'SB Member', '2022-07-01', '', 'Active', '', '', '', '', '', '1'),
(74, '', '097210-046', 'Hernibeth', 'C', 'Otud', '', '1976-12-09', 'Female', 'Married', 'Pob. South, Polanco, Z.N.', 'Administrative Officer', '2002-01-01', '', 'Active', '', '', '', '', '', '1'),
(75, '', '097210-053', 'Richard', 'A', 'Otud', '', '1969-03-12', 'Male', 'Married', 'Pob. North, Polanco, Z.N.', 'Engineering Aide', '1999-04-01', '', 'Active', '', '', '', '', '', '1'),
(76, '', '097210-277', 'Mark Angelo', 'W', 'Pagente', '', '2000-11-22', 'Male', 'Single', 'Isis, Polanco, Z.N.', 'SK Federation President', '2023-11-14', '', 'Active', '', '', '', '', '', '0'),
(77, '', '097210-043', 'Mary Sol', 'B', 'Pagente', '', '1974-02-09', 'Female', 'Single', 'Pob. North, Polanco, Z.N.', 'Midwife', '2019-09-02', '', 'Active', '', '', '', '', '', '1'),
(78, '', '097210-363', 'Jhyke', 'C', 'Palomaria', '', '1977-02-09', 'Male', 'Single', 'Pob. South, Polanco, Z.N.', 'Administrative Aide', '2016-07-01', '', 'Active', '', '', '', '', '', '1'),
(79, '', '097210-035', 'Rebecca', 'S', 'Parama', '', '1967-09-23', 'Female', 'Widow', 'Isis, Polanco, Z.N.', 'Midwife', '2009-11-16', '', 'Active', '', '', '', '', '', '1'),
(80, '', '097210-051', 'Josephine', 'S', 'Pastrano', '', '1967-11-24', 'Female', 'Married', 'Pob. South, Polanco, Z.N.', 'Supply Officer', '1994-02-02', '', 'Active', '', '', '', '', '', '1'),
(81, '', '097210-045', 'Clint', 'B', 'Penados', '', '1983-01-22', 'Male', 'Married', 'Pob. South, Polanco, Z.N.', 'Driver', '2009-02-02', '', 'Active', '', '', '', '', '', '1'),
(82, '', '097210-162', 'Shella Mae', 'T', 'Quiblat', '', '1992-05-01', 'Female', 'Married', 'Pob. North, Polanco, Z.N.', 'Social Worker', '2020-08-01', '', 'Active', '', '', '', '', '', '1'),
(83, '', '097210-199', 'Joan', 'A', 'Quisel', '', '1987-01-03', 'Female', 'Single', 'Silawe, Polanco, Z.N.', 'Midwife', '2021-12-01', '', 'Active', '', '', '', '', '', '1'),
(84, '', '097210-329', 'Lauren', 'B', 'Realista', '', '1966-03-25', 'Male', 'Married', 'San Antonio, Polanco, Z.N.', 'Driver', '2016-07-01', '', 'Active', '', '', '', '', '', '1'),
(85, '', '097210-207', 'Rhea', 'C', 'Repaja', '', '1992-07-06', 'Female', 'Single', 'Pob. North, Polanco, Z.N.', 'Assessment Officer', '2023-02-01', '', 'Active', '', '', '', '', '', '1'),
(86, '', '097210-508', 'Caryll Divine', 'E', 'Sabejon', '', '1999-06-05', 'Female', 'Single', 'Silawe, Polanco, Z.N.', 'CSE Professional', '2024-03-13', '', 'Active', '', '', '', '', '', '1'),
(87, '', '097210-037', 'Dante', 'M', 'Santander', '', '1969-12-30', 'Male', 'Married', 'Gulayon, Dipolog City, Z.N.', 'Municipal Civil Registrar', '1994-01-02', '', 'Active', '', '', '', '', '', '1'),
(88, '', '097210-030', 'Leoniboy', 'A', 'Sarigue', '', '1982-03-18', 'Male', 'Married', 'San Miguel, Polanco, Z.N.', 'Driver', '2011-09-16', '', 'Active', '', '', '', '', '', '1'),
(89, '', '097210-038', 'Almabella', 'I', 'Sulit', '', '1970-01-23', 'Female', 'Married', 'VCH, Obay, Polanco, Z.N.', 'Midwife', '2018-05-02', '', 'Active', '', '', '', '', '', '1'),
(90, '', '097210-061', 'Edgar', 'B', 'Tacloban', '', '1961-05-03', 'Male', 'Married', 'Pob. North, Polanco, Z.N.', 'Administrative Aide', '2004-01-07', '', 'Active', '', '', '', '', '', '1'),
(91, '', '097210-107', 'Christian Lyris', 'C', 'Tagsip', '', '1991-08-28', 'Male', 'Married', 'Pob. South, Polanco, Z.N.', 'Computer Scientist', '2018-12-03', '', 'Active', '', '', '', '', '', '1'),
(92, '', '097210-104', 'Winielyn', 'F', 'Trapal', '', '1990-04-30', 'Female', 'Married', 'Gulayon, Dipolog City, Z.N.', 'Medical Technologist', '2022-03-16', '', 'Active', '', '', '', '', '', '1'),
(93, '', '097210-884', 'Syrone Pherven', 'M', 'Tubera', '', '1989-08-21', 'Male', 'Married', 'Galas, Dipolog City, Z.N.', 'IT Specialist', '2023-02-01', '', 'Active', '', '', '', '', '', '1'),
(94, '', '097210-011', 'Shaia Ruth', 'R', 'Uy', '', '1999-08-09', 'Female', 'Single', 'Lapayanbaja, Polanco, Z.N.', 'Municipal Mayor', '2019-07-01', '', 'Active', '', '', '', '', '', '1'),
(95, '', '097210-309', 'Glyn', 'T', 'Villamero', '', '1979-12-13', 'Female', 'Married', 'Guinles, Polanco, Z.N.', 'Administrative Assistant', '2024-01-18', '', 'Active', '', '', '', '', '', '1'),
(96, '', '097210-080', 'Samuel', 'F', 'Baniqued', 'Jr.', '1983-07-31', 'Male', 'Married', 'Obay, Polanco, Z.N.', 'CSE Professional', '2025-03-12', '', 'Active', '', '', '', '', '', '1'),
(97, '', '097210-209', 'Kevin', 'V', 'Leones', '', '1998-07-01', 'Male', 'Single', 'Central Dipolog City, Z.N.', 'Engineer', '2025-03-12', '', 'Active', '', '', '', '', '', '1'),
(98, '', '097210-389', 'Marlon', 'P', 'Manlosa', '', '1968-05-02', 'Male', 'Married', 'Pob. North, Polanco, Z.N.', 'Administrative Aide', '1995-06-07', '', 'Active', '', '', '', '', '', '1'),
(99, '', '097210-388', 'Ma. Cristy Jean', 'T', 'Mariño', '', '2000-08-24', 'Female', 'Single', 'Isis, Polanco, Z.N.', 'Administrative Assistant', '2025-03-12', '', 'Active', '', '', '', '', '', '1'),
(100, '', '097210-253', 'Czeneal Clyre', 'G', 'Almazan', '', '1999-07-25', 'Female', 'Single', 'San Antonio, Polanco, Z.N.', 'Medical Technologist', '2024-11-04', '', 'Active', '', '', '', '', '', '1'),
(101, '', '097210-752', 'JR Niño Christopher', 'M', 'Arzadon', '', '1993-01-09', 'Male', 'Single', 'VCH, Obay, Polanco, Z.N.', 'Local Youth Dev\'t Officer', '2024-11-04', '', 'Active', '', '', '', '', '', '1'),
(102, '', '097210-381', 'Adolfo', 'A', 'Cademas', 'Jr.', '1990-09-08', 'Male', 'Single', 'Labrador, Polanco, Z.N.', 'Assessment Clerk', '2025-01-02', '', 'Active', '', '', '', '', '', '1'),
(103, '', '097210-203', 'Chadam', 'L', 'Empeynado', '', '1995-07-19', 'Male', 'Single', 'San Miguel, Polanco, Z.N.', 'Agricultural Technologist', '2023-02-01', '', 'Active', '', '', '', '', '', '1'),
(104, '', '097210-222', 'Nurodin', 'L', 'Empeynado', '', '1996-10-28', 'Male', 'Single', 'San Miguel, Polanco, Z.N.', 'Agricultural Technologist', '2024-11-18', '', 'Active', '', '', '', '', '', '1'),
(105, '', '097210-206', 'Melva', 'C', 'Fulmaran', '', '1990-10-28', 'Female', 'Single', 'Guinles, Polanco, Z.N', 'Agricultural Technologist', '2024-01-02', '', 'Active', '', '', '', '', '', '1'),
(106, '', '097210-287', 'Keith Eammon', 'L', 'Garay', '', '1985-03-03', 'Male', 'Married', 'Victoria Hills, Pob. North, Polanco Z.N.', 'Driver', '2024-11-04', '', 'Active', '', '', '', '', '', '1'),
(107, '', '097210-246', 'Mark Anthony', 'B', 'Gurdiel', '', '1981-01-19', 'Male', 'Married', 'Dicayas, Dipolog City, Z.N.', 'Administrative Aide', '2022-01-17', '', 'Active', '', '', '', '', '', '1'),
(108, '', '097210-210', 'Sheila', 'C', 'Jaralve', '', '1983-12-23', 'Female', 'Single', 'San Pedro, Polanco, Z.N.', 'Municipal Health Officer', '2024-03-22', '', 'Active', '', '', '', '', '', '1'),
(109, '', '097210-259', 'Rea Mae', 'P', 'Limbaga', '', '1997-05-08', 'Female', 'Single', 'Pob. South, Polanco, Z.N.', 'Social Worker', '2025-01-21', '', 'Active', '', '', '', '', '', '1'),
(110, '', '097210-197', 'John Arthur', '', 'Maravillas', '', '1998-04-11', 'Male', 'Single', 'Pob. South, Polanco, Z.N.', 'Social Worker', '2021-05-16', '', 'Active', '', '', '', '', '', '1'),
(111, '', '097210-365', 'Eric', 'T', 'Rubio', '', '1978-04-07', 'Male', 'Single', 'Pob. South, Polanco, Z.N.', 'Assessment Clerk', '2025-01-02', '', 'Active', '', '', '', '', '', '1'),
(112, '', '097210-111', 'Allana', 'M', 'Salvador', '', '1995-06-07', 'Female', 'Single', 'Guinles, Polanco, Z.N', 'Administrative Assistant', '2025-01-02', '', 'Active', '', '', '', '', '', '1'),
(113, '', '097210-204', 'Stephen Cesar', 'C', 'Sayre', '', '1997-12-02', 'Male', 'Married', 'Pob. North, Polanco, Z.N.', 'Agricultural Technologist', '2024-02-01', '', 'Active', '', '', '', '', '', '1'),
(114, '', '097210-208', 'Rosie', 'Q', 'Sorronda', '', '1978-03-09', 'Female', 'Widow', 'San Antonio, Polanco, Z.N.', 'Plantilla Casual', '2025-07-01', '', 'Active', '', '', '', '', '', '1'),
(115, '', '097210-171', 'Arlene', 'Z', 'Tejano', '', '1970-11-20', 'Female', 'Single', 'Lingasad, Polanco, Z.N.', 'Community Affairs Assistant', '2025-03-12', '', 'Active', '', '', '', '', '', '1'),
(116, '', '097210-307', 'Arlene', 'B', 'Tiol', '', '1971-05-28', 'Female', 'Widow', 'Pob. North, Polanco, Z.N.', 'Administrative Aide', '2025-01-02', '', 'Active', '', '', '', '', '', '1'),
(117, '', '097210-399', 'Arnel', 'A', 'Adaro', '', '2002-06-18', 'Male', 'Single', 'Dansullan, Polanco, Z.N.', 'Plantilla Casual', '2025-07-01', '', 'Active', '', '', '', '', '', '1'),
(118, '', '097210-401-A', 'Napoleon', 'P', 'Adriatico', '', '1956-07-30', 'Male', 'Married', 'Villahermosa, Polanco, Z.N.', 'Ex Officio', '2025-07-01', '', 'Active', '', '', '', '', '', '1'),
(119, '', '097210-003', 'Alfredo', 'S', 'Bait-it', '', '1952-04-11', 'Male', 'Married', 'Villahermosa, Polanco, Z.N.', 'SB Member', '2016-07-01', '', 'Active', '', '', '', '', '', '1'),
(120, '', '097210-328', 'Bonna Claire', 'L', 'Baliola', '', '1998-04-17', 'Female', 'Single', 'Pob. North, Polanco, Z.N.', 'Internal Auditor', '2020-10-01', '', 'Active', '', '', '', '', '', '1'),
(121, '', '097210-013', 'Dioniso', 'A', 'Dalida', '', '1954-05-27', 'Male', 'Married', 'Milad, Polanco, Z.N.', 'SB Member', '2025-07-01', '', 'Active', '', '', '', '', '', '1'),
(122, '', '097210-036', 'Rolando', 'P', 'Escuadro', '', '1960-12-23', 'Male', 'Married', 'Lapayanbaja, Polanco, Z.N.', 'SB Member', '2025-07-01', '', 'Active', '', '', '', '', '', '1'),
(123, '', '097210-010', 'Cinderelle', 'M', 'Gabucan', '', '1997-02-02', 'Female', 'Single', 'Pob. North, Polanco, Z.N.', 'SB Member', '2025-07-01', '', 'Active', '', '', '', '', '', '1'),
(124, '', '097210-067', 'Nessa Christabelle', 'P', 'Gonzales', '', '1994-03-25', 'Female', 'Single', 'Pob. North, Polanco, Z.N.', 'Private Secretary to the Mayor', '2025-07-01', '', 'Active', '', '', '', '', '', '1'),
(125, '', '097210-152', 'Ginalyn', 'A', 'Jutingo', '', '1976-08-02', 'Female', 'Married', 'Dipolog City, Z.N.', 'Plantilla Casual', '2025-07-01', '', 'Active', '', '', '', '', '', '1'),
(126, '', '097210-088', 'Flores May', 'L', 'Orosa', '', '1991-05-17', 'Female', 'Married', 'Obay, Polanco, Z.N.', 'Municipal Administrator', '2025-07-01', '', 'Active', '', '', '', '', '', '1'),
(127, '', '097210-300', 'Ryan', 'G', 'Paner', '', '1987-08-23', 'Male', 'Married', 'Pob. North, Polanco, Z.N.', 'Plantilla Casual', '2025-07-01', '', 'Active', '', '', '', '', '', '1'),
(128, '', '097210-200', 'Secenio', 'H', 'Ramayla', 'Jr.', '1981-12-16', 'Male', 'Married', 'Silawe, Polanco, Z.N.', 'Driver', '2025-09-01', '', 'Active', '', '', '', '', '', '1'),
(129, '', '097210-008', 'Jose Marion', 'F', 'Repaja', '', '1985-09-08', 'Male', 'Single', 'Pob. North, Polanco, Z.N.', 'SB Member', '2025-07-01', '', 'Active', '', '', '', '', '', '1'),
(130, '', '097210-009', 'Ray Geynill', 'R', 'Samonte', '', '1990-05-05', 'Male', 'Single', 'Pob. North, Polanco, Z.N.', 'SB Member', '2025-07-01', '', 'Active', '', '', '', '', '', '1'),
(131, '', '097210-110', 'Noel', 'V', 'Sandueta', '', '1978-03-12', 'Male', 'Married', 'Villahermosa, Polanco, Z.N.', 'Municipal Agriculturist', '2025-08-26', '', 'Active', '', '', '', '', '', '1'),
(132, '', '097210-227', 'Joselito', 'J', 'Tacloban', '', '1982-05-01', 'Male', 'Married', 'Pob. North, Polanco, Z.N.', 'Administrative Aide', '2024-01-02', '', 'Active', '', '', '', '', '', '1'),
(133, '', '097210-398', 'Joyce', 'J', 'Villaran', '', '1981-04-02', 'Female', 'Married', 'Letapan, Polanco, Z.N.', 'Plantilla Casual', '2025-07-01', '', 'Active', '', '', '', '', '', '1'),
(134, '', '097210-401', 'Norma', 'D', 'Adriatico', '', '1991-07-25', 'Female', 'Single', 'Bethlehem, Polanco, Z.N.', 'Administrative Aide', '2025-03-25', '', 'Active', '', '', '', '', '', '1'),
(135, '', '097210-XXX', 'Jeia Mae', 'J', 'Realista', '', '1993-05-09', 'Female', 'Single', 'Guinles, Polanco, Z.N', 'Plantilla Casual', '2025-07-01', '', 'Active', '', '', '', '', '', '1');

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
(3, 94, 38255.73, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(4, 10, 19318.38, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(5, 124, 6443.37, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(6, 64, 11058.45, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(7, 120, 13317.76, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(8, 67, 6301.95, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(9, 5, 3504.75, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(10, 84, 10397.76, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(11, 51, 7688.46, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(12, 78, 5680.42, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(13, 43, 7106.34, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(14, 88, 13109.10, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(15, 69, 7525.52, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(16, 111, 7664.67, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(17, 126, 23662.20, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(18, 23, 17044.87, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(19, 80, 28353.14, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(20, 12, 12664.50, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(21, 8, 2732.87, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(22, 107, 13084.27, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(23, 115, 2329.87, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(24, 11, 2977.75, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(25, 96, 4180.42, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(26, 26, 2150.92, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(27, 106, 11139.84, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(28, 70, 28249.86, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(29, 128, 2042.25, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(30, 62, 24682.82, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(31, 130, 23662.20, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(32, 129, 88716.35, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(33, 73, 24171.13, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(34, 121, 22579.17, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(35, 123, 23662.20, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(36, 119, 17459.17, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(37, 122, 17459.17, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(38, 118, 17459.17, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(40, 63, 26514.83, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(41, 54, 20402.15, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(42, 20, 86760.66, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(43, 86, 3198.37, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(44, 95, 13235.57, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(45, 17, 3952.62, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(46, 55, 79225.01, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(47, 19, 16110.50, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(48, 32, 11702.29, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(49, 39, 14060.43, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(50, 13, 15500.12, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(51, 87, 30517.14, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(52, 29, 23622.17, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(53, 42, 5949.54, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(54, 134, 3963.75, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(55, 6, 63840.66, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(56, 31, 17749.46, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(57, 40, 8027.75, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(58, 56, 5254.75, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(59, 72, 4582.37, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(60, 46, 35330.72, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(61, 74, 14498.17, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(62, 91, 16230.28, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(63, 53, 16412.08, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(64, 99, 4232.87, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(65, 7, 5113.05, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(66, 1, 3713.75, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(67, 25, 25565.87, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(68, 57, 8623.37, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '2026-03-02', 0),
(69, 44, 7195.25, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(70, 28, 9713.33, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(71, 93, 4077.75, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(72, 47, 17518.42, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(73, 59, 13109.43, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(74, 22, 14303.64, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(75, 68, 2932.87, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(76, 24, 8327.25, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0),
(77, 112, 9058.53, '2026-01-01', 'Deductions as of Feb. 2026', NULL, '2026-03-02', '0000-00-00', 0);

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
(13, 3, 2, 11555.73),
(14, 3, 4, 300.00),
(15, 3, 21, 2500.00),
(16, 3, 1, 23900.00),
(17, 4, 2, 4198.41),
(18, 4, 4, 300.00),
(19, 4, 21, 1166.22),
(20, 4, 1, 3420.00),
(21, 4, 17, 486.49),
(22, 4, 5, 9747.26),
(23, 5, 2, 3256.83),
(24, 5, 4, 300.00),
(25, 5, 21, 904.67),
(26, 5, 1, 1600.00),
(27, 5, 17, 381.87),
(28, 6, 2, 3256.83),
(29, 6, 27, 2648.41),
(30, 6, 26, 1966.67),
(31, 6, 4, 300.00),
(32, 6, 21, 904.67),
(33, 6, 1, 1600.00),
(34, 6, 17, 381.87),
(35, 7, 2, 3288.96),
(36, 7, 28, 1400.00),
(37, 7, 25, 655.56),
(38, 7, 16, 500.00),
(39, 7, 27, 2257.53),
(40, 7, 26, 1966.67),
(41, 7, 4, 300.00),
(42, 7, 21, 913.60),
(43, 7, 1, 1650.00),
(44, 7, 17, 385.44),
(45, 8, 2, 2431.98),
(46, 8, 27, 774.20),
(47, 8, 4, 1000.00),
(48, 8, 21, 675.55),
(49, 8, 1, 380.00),
(50, 8, 14, 750.00),
(51, 8, 17, 290.22),
(52, 9, 2, 1753.02),
(53, 9, 4, 300.00),
(54, 9, 21, 486.95),
(55, 9, 14, 750.00),
(56, 9, 17, 214.78),
(57, 10, 2, 1363.50),
(58, 10, 19, 4717.34),
(59, 10, 26, 1966.67),
(60, 10, 4, 300.00),
(61, 10, 21, 378.75),
(62, 10, 17, 171.50),
(63, 10, 18, 1500.00),
(64, 11, 2, 1363.50),
(65, 11, 4, 300.00),
(66, 11, 21, 378.75),
(67, 11, 14, 750.00),
(68, 11, 12, 4724.71),
(69, 11, 17, 171.50),
(70, 12, 2, 1363.50),
(71, 12, 26, 1966.67),
(72, 12, 4, 300.00),
(73, 12, 21, 378.75),
(74, 12, 18, 1500.00),
(75, 12, 17, 171.50),
(76, 13, 2, 1363.50),
(77, 13, 16, 100.00),
(78, 13, 19, 3542.59),
(79, 13, 4, 300.00),
(80, 13, 21, 378.75),
(81, 13, 14, 750.00),
(82, 13, 17, 671.50),
(83, 14, 2, 1363.50),
(84, 14, 28, 1866.67),
(85, 14, 27, 1443.00),
(86, 14, 26, 1966.67),
(87, 14, 4, 300.00),
(88, 14, 5, 3119.01),
(89, 14, 21, 378.75),
(90, 14, 17, 171.50),
(91, 14, 18, 2500.00),
(92, 15, 2, 1147.32),
(93, 15, 28, 1400.00),
(94, 15, 27, 1470.77),
(95, 15, 26, 1966.67),
(96, 15, 4, 254.96),
(97, 15, 5, 819.62),
(98, 15, 21, 318.70),
(99, 15, 17, 147.48),
(100, 16, 2, 1138.95),
(101, 16, 27, 3559.70),
(102, 16, 4, 253.10),
(103, 16, 21, 316.37),
(104, 16, 14, 750.00),
(105, 16, 17, 146.55),
(106, 16, 18, 1500.00),
(107, 17, 2, 7953.03),
(108, 17, 4, 300.00),
(109, 17, 21, 2209.17),
(110, 17, 1, 13200.00),
(111, 18, 2, 3288.96),
(112, 18, 19, 6040.20),
(113, 18, 26, 1966.67),
(114, 18, 4, 300.00),
(115, 18, 21, 913.60),
(116, 18, 1, 1650.00),
(117, 18, 17, 385.44),
(118, 18, 18, 2500.00),
(119, 19, 2, 3028.14),
(120, 19, 28, 2333.33),
(121, 19, 19, 5448.28),
(122, 19, 27, 4451.30),
(123, 19, 26, 1966.67),
(124, 19, 4, 300.00),
(125, 19, 5, 3172.47),
(126, 19, 21, 841.15),
(127, 19, 1, 1250.00),
(128, 19, 32, 2705.34),
(129, 19, 17, 356.46),
(130, 19, 18, 2500.00),
(131, 20, 2, 2431.98),
(132, 20, 28, 2333.33),
(133, 20, 19, 2471.36),
(134, 20, 27, 2782.06),
(135, 20, 4, 300.00),
(136, 20, 21, 675.55),
(137, 20, 1, 380.00),
(138, 20, 22, 1000.00),
(139, 20, 17, 290.22),
(140, 21, 2, 1737.27),
(141, 21, 4, 300.00),
(142, 21, 21, 482.57),
(143, 21, 17, 213.03),
(144, 22, 2, 1546.92),
(145, 22, 28, 466.67),
(146, 22, 25, 655.56),
(147, 22, 16, 50.00),
(148, 22, 27, 1078.87),
(149, 22, 26, 1966.67),
(150, 22, 4, 300.00),
(151, 22, 5, 1172.57),
(152, 22, 21, 429.70),
(153, 22, 8, 3725.43),
(154, 22, 17, 191.88),
(155, 22, 18, 1500.00),
(156, 23, 2, 1447.11),
(157, 23, 4, 300.00),
(158, 23, 21, 401.97),
(159, 23, 17, 180.79),
(160, 24, 2, 1373.58),
(161, 24, 4, 300.00),
(162, 24, 21, 381.55),
(163, 24, 14, 750.00),
(164, 24, 17, 172.62),
(165, 25, 2, 1363.50),
(166, 25, 26, 1966.67),
(167, 25, 4, 300.00),
(168, 25, 21, 378.75),
(169, 25, 17, 171.50),
(170, 26, 2, 1322.64),
(171, 26, 4, 293.92),
(172, 26, 21, 367.40),
(173, 26, 17, 166.96),
(174, 27, 2, 1284.03),
(175, 27, 28, 950.00),
(176, 27, 27, 583.94),
(177, 27, 26, 1966.67),
(178, 27, 4, 285.34),
(179, 27, 21, 356.67),
(180, 27, 8, 4550.52),
(181, 27, 17, 162.67),
(182, 27, 18, 1000.00),
(183, 28, 2, 9049.86),
(184, 28, 4, 300.00),
(185, 28, 21, 2500.00),
(186, 28, 1, 16400.00),
(187, 29, 2, 1363.50),
(188, 29, 4, 300.00),
(189, 29, 21, 378.75),
(190, 30, 2, 8203.95),
(191, 30, 4, 300.00),
(192, 30, 21, 2278.87),
(193, 30, 1, 13900.00),
(194, 31, 2, 7953.03),
(195, 31, 4, 300.00),
(196, 31, 21, 2209.17),
(197, 31, 1, 13200.00),
(198, 32, 2, 7953.03),
(199, 32, 4, 300.00),
(200, 32, 21, 2209.17),
(201, 32, 1, 13200.00),
(202, 32, 12, 65054.15),
(203, 33, 2, 8077.41),
(204, 33, 4, 300.00),
(205, 33, 21, 2243.72),
(206, 33, 1, 13550.00),
(207, 34, 21, 2209.17),
(208, 34, 1, 15250.00),
(209, 34, 17, 5120.00),
(210, 35, 2, 7953.03),
(211, 35, 4, 300.00),
(212, 35, 21, 2209.17),
(213, 35, 1, 13200.00),
(214, 36, 21, 2209.17),
(215, 36, 1, 15250.00),
(216, 37, 21, 2209.17),
(217, 37, 1, 15250.00),
(218, 38, 21, 2209.17),
(219, 38, 1, 15250.00),
(232, 40, 2, 3028.14),
(233, 40, 26, 1966.67),
(234, 40, 16, 500.00),
(235, 40, 19, 3575.10),
(236, 40, 27, 4461.21),
(237, 40, 28, 2100.00),
(238, 40, 4, 300.00),
(239, 40, 5, 5279.78),
(240, 40, 30, 1356.32),
(241, 40, 21, 841.15),
(242, 40, 1, 1250.00),
(243, 40, 17, 356.46),
(244, 40, 18, 1500.00),
(245, 41, 2, 2788.11),
(246, 41, 4, 300.00),
(247, 41, 31, 3000.00),
(248, 41, 30, 777.06),
(249, 41, 21, 774.47),
(250, 41, 1, 900.00),
(251, 41, 14, 2280.00),
(252, 41, 17, 415.83),
(253, 41, 18, 9166.68),
(254, 42, 2, 7953.03),
(255, 42, 16, 500.00),
(256, 42, 19, 3914.99),
(257, 42, 27, 18323.11),
(258, 42, 28, 2333.33),
(259, 42, 4, 300.00),
(260, 42, 31, 1000.00),
(261, 42, 30, 4725.92),
(262, 42, 21, 2209.17),
(263, 42, 1, 12950.00),
(264, 42, 17, 903.67),
(265, 42, 12, 30774.80),
(266, 42, 25, 872.64),
(267, 43, 2, 2072.43),
(268, 43, 4, 300.00),
(269, 43, 21, 575.67),
(270, 43, 17, 250.27),
(271, 44, 2, 1737.27),
(272, 44, 26, 1966.67),
(273, 44, 25, 1125.00),
(274, 44, 27, 2215.31),
(275, 44, 28, 1400.00),
(276, 44, 4, 300.00),
(277, 44, 30, 1195.72),
(278, 44, 21, 482.57),
(279, 44, 17, 213.03),
(280, 44, 18, 1600.00),
(281, 44, 22, 1000.00),
(282, 45, 2, 1535.49),
(283, 45, 4, 300.00),
(284, 45, 21, 426.52),
(285, 45, 17, 190.61),
(286, 45, 18, 1500.00),
(287, 46, 2, 8863.92),
(288, 46, 16, 3000.00),
(289, 46, 19, 9601.40),
(290, 46, 27, 22737.64),
(291, 46, 26, 1966.67),
(292, 46, 4, 300.00),
(293, 46, 5, 9718.25),
(294, 46, 30, 1803.38),
(295, 46, 21, 2462.20),
(296, 46, 1, 15850.00),
(297, 46, 18, 2921.55),
(298, 47, 2, 3288.96),
(299, 47, 19, 5973.36),
(300, 47, 4, 800.00),
(301, 47, 30, 2349.14),
(302, 47, 21, 913.60),
(303, 47, 1, 1650.00),
(304, 47, 14, 750.00),
(305, 47, 17, 385.44),
(306, 48, 2, 3288.96),
(307, 48, 25, 655.56),
(308, 48, 27, 1842.06),
(309, 48, 4, 300.00),
(310, 48, 21, 913.60),
(311, 48, 1, 1650.00),
(312, 48, 14, 750.00),
(313, 48, 17, 385.44),
(314, 48, 18, 1916.67),
(315, 49, 2, 1881.27),
(316, 49, 4, 800.00),
(317, 49, 21, 522.57),
(318, 49, 17, 229.03),
(319, 49, 12, 10627.56),
(320, 50, 2, 1737.27),
(321, 50, 27, 952.64),
(322, 50, 26, 1966.67),
(323, 50, 4, 300.00),
(324, 50, 21, 482.57),
(325, 50, 17, 213.03),
(326, 50, 12, 9847.94),
(327, 51, 2, 8077.41),
(328, 51, 24, 2678.52),
(329, 51, 4, 300.00),
(330, 51, 21, 2243.72),
(331, 51, 1, 13300.00),
(332, 51, 14, 3000.00),
(333, 51, 17, 917.49),
(334, 52, 2, 3057.66),
(335, 52, 26, 1966.67),
(336, 52, 16, 100.00),
(337, 52, 19, 9634.44),
(338, 52, 4, 1300.00),
(339, 52, 21, 849.35),
(340, 52, 1, 1300.00),
(341, 52, 14, 750.00),
(342, 52, 17, 359.74),
(343, 52, 27, 4304.31),
(344, 53, 2, 1737.27),
(345, 53, 26, 1966.67),
(346, 53, 4, 800.00),
(347, 53, 21, 482.57),
(348, 53, 14, 750.00),
(349, 53, 17, 213.03),
(350, 54, 2, 1363.50),
(351, 54, 4, 1300.00),
(352, 54, 21, 378.75),
(353, 54, 14, 750.00),
(354, 54, 17, 171.50),
(355, 55, 2, 8203.95),
(356, 55, 28, 2333.33),
(357, 55, 26, 1966.67),
(358, 55, 16, 500.00),
(359, 55, 27, 12118.40),
(360, 55, 4, 1450.00),
(361, 55, 31, 1000.00),
(362, 55, 5, 4547.97),
(363, 55, 21, 2278.87),
(364, 55, 1, 13700.00),
(365, 55, 17, 931.55),
(366, 55, 18, 2500.00),
(367, 55, 12, 12309.92),
(368, 56, 2, 3256.83),
(369, 56, 16, 1177.00),
(370, 56, 19, 4817.22),
(371, 56, 24, 2811.87),
(372, 56, 4, 300.00),
(373, 56, 21, 904.67),
(374, 56, 1, 1600.00),
(375, 56, 17, 381.87),
(376, 56, 18, 2500.00),
(377, 57, 2, 2431.98),
(378, 57, 4, 500.00),
(379, 57, 31, 3000.00),
(380, 57, 21, 675.55),
(381, 57, 1, 380.00),
(382, 57, 14, 750.00),
(383, 57, 17, 290.22),
(384, 58, 2, 1753.02),
(385, 58, 4, 300.00),
(386, 58, 21, 486.95),
(387, 58, 17, 214.78),
(388, 58, 18, 2500.00),
(389, 59, 2, 1628.91),
(390, 59, 4, 300.00),
(391, 59, 31, 1000.00),
(392, 59, 21, 452.47),
(393, 59, 17, 200.99),
(394, 59, 18, 1000.00),
(395, 60, 2, 8077.41),
(396, 60, 27, 10392.10),
(397, 60, 4, 300.00),
(398, 60, 21, 2243.72),
(399, 60, 1, 13300.00),
(400, 60, 17, 1017.49),
(401, 61, 2, 3256.83),
(402, 61, 26, 1966.67),
(403, 61, 4, 1000.00),
(404, 61, 31, 2000.00),
(405, 61, 21, 904.67),
(406, 61, 1, 1600.00),
(407, 61, 14, 750.00),
(408, 61, 17, 520.00),
(409, 61, 18, 2500.00),
(410, 62, 2, 2431.98),
(411, 62, 28, 1400.00),
(412, 62, 16, 200.00),
(413, 62, 27, 1257.81),
(414, 62, 26, 1966.67),
(415, 62, 4, 300.00),
(416, 62, 31, 500.00),
(417, 62, 5, 2524.34),
(418, 62, 30, 187.04),
(419, 62, 21, 675.55),
(420, 62, 1, 380.00),
(421, 62, 14, 750.00),
(422, 62, 17, 290.22),
(423, 62, 18, 3366.67),
(424, 63, 2, 1881.27),
(425, 63, 27, 1369.77),
(426, 63, 26, 1966.67),
(427, 63, 4, 300.00),
(428, 63, 31, 500.00),
(429, 63, 21, 522.57),
(430, 63, 12, 9642.77),
(431, 63, 17, 229.03),
(432, 64, 2, 1737.27),
(433, 64, 4, 300.00),
(434, 64, 21, 482.57),
(435, 64, 17, 213.03),
(436, 64, 18, 1500.00),
(437, 65, 2, 1753.02),
(438, 65, 27, 1608.30),
(439, 65, 4, 300.00),
(440, 65, 21, 486.95),
(441, 65, 14, 750.00),
(442, 65, 17, 214.78),
(443, 66, 2, 1363.50),
(444, 66, 4, 300.00),
(445, 66, 21, 378.75),
(446, 66, 17, 171.50),
(447, 66, 18, 1500.00),
(448, 67, 2, 7953.03),
(449, 67, 16, 300.00),
(450, 67, 4, 500.00),
(451, 67, 21, 2209.17),
(452, 67, 1, 12950.00),
(453, 67, 14, 750.00),
(454, 67, 17, 903.67),
(461, 69, 2, 2998.98),
(462, 69, 4, 1300.00),
(463, 69, 31, 500.00),
(464, 69, 21, 833.05),
(465, 69, 1, 1210.00),
(466, 69, 17, 353.22),
(467, 68, 1, 1720.00),
(468, 68, 2, 3256.83),
(469, 68, 4, 300.00),
(470, 68, 14, 1060.00),
(471, 68, 17, 381.87),
(472, 68, 21, 904.67),
(473, 68, 31, 1000.00),
(474, 70, 2, 2431.98),
(475, 70, 27, 1257.81),
(476, 70, 4, 300.00),
(477, 70, 5, 1861.10),
(478, 70, 21, 675.55),
(479, 70, 1, 430.00),
(480, 70, 17, 290.22),
(481, 70, 26, 1966.67),
(482, 70, 31, 500.00),
(483, 71, 2, 2431.98),
(484, 71, 4, 300.00),
(485, 71, 21, 675.55),
(486, 71, 1, 380.00),
(487, 71, 17, 290.22),
(488, 72, 2, 1989.18),
(489, 72, 28, 700.00),
(490, 72, 19, 5833.66),
(491, 72, 27, 2935.34),
(492, 72, 4, 300.00),
(493, 72, 21, 552.55),
(494, 72, 22, 1500.00),
(495, 72, 17, 241.02),
(496, 72, 18, 1500.00),
(497, 72, 26, 1966.67),
(498, 73, 2, 1896.30),
(499, 73, 4, 300.00),
(500, 73, 21, 526.75),
(501, 73, 17, 230.70),
(502, 73, 12, 10155.68),
(503, 74, 2, 1896.30),
(504, 74, 4, 300.00),
(505, 74, 31, 500.00),
(506, 74, 21, 526.75),
(507, 74, 14, 750.00),
(508, 74, 17, 230.70),
(509, 74, 18, 1277.78),
(510, 74, 12, 8822.11),
(511, 75, 2, 1881.27),
(512, 75, 4, 300.00),
(513, 75, 21, 522.57),
(514, 75, 17, 229.03),
(515, 76, 2, 1800.99),
(516, 76, 24, 1752.47),
(517, 76, 4, 900.00),
(518, 76, 30, 3150.34),
(519, 76, 21, 500.27),
(520, 76, 17, 223.18),
(521, 77, 2, 1737.27),
(522, 77, 4, 300.00),
(523, 77, 5, 1125.14),
(524, 77, 21, 482.57),
(525, 77, 14, 750.00),
(526, 77, 8, 4450.52),
(527, 77, 17, 213.03);

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
(1, 46, 57, 86101.00, 103401.00, 'Promotion', '2025-01-01', '2025-12-31', '2025-10-23', '2026-01-06', 0),
(2, 74, 58, 34572.00, 36572.00, 'Promotion', '2024-06-18', '2025-12-31', '2025-10-23', '2026-01-06', 0),
(5, 10, 118, 46649.00, 48649.00, '2ND TRANCHE - BBM', '2026-01-05', NULL, '2026-01-05', '0000-00-00', 0),
(6, 64, 119, 36187.00, 38187.00, '2ND TRANCHE - BBM', '2026-01-05', NULL, '2026-01-05', '0000-00-00', 0),
(7, 120, 120, 36187.00, 38187.00, '2ND TRANCHE - BBM', '2026-01-05', '2026-01-31', '2026-01-05', '2026-03-02', 0),
(8, 67, 121, 27022.00, 29022.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(9, 5, 122, 19478.00, 21478.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(10, 84, 123, 15150.00, 17150.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(11, 51, 124, 15150.00, 17150.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(12, 78, 125, 15150.00, 17150.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(13, 43, 126, 15150.00, 17150.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(14, 88, 127, 15150.00, 20937.50, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '2026-03-02', 0),
(15, 69, 128, 12748.00, 14748.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(16, 111, 129, 12655.00, 14655.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(17, 126, 130, 88367.00, 90367.00, '2ND TRANCHE - BBM', '2026-01-01', '2025-12-31', '2026-01-05', '2026-01-05', 0),
(18, 126, 130, 88367.00, 105667.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(19, 80, 131, 33646.00, 35646.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(20, 12, 132, 27022.00, 29022.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(21, 115, 133, 16079.00, 18079.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(22, 107, 134, 17188.00, 19188.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(23, 106, 135, 14267.00, 16267.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(24, 96, 136, 15150.00, 17150.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(25, 26, 137, 14696.00, 16696.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(26, 11, 138, 15262.00, 17262.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(27, 23, 139, 36544.00, 38544.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(28, 8, 140, 19303.00, 21303.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(29, 35, 141, 17315.00, 19315.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(30, 116, 142, 12655.00, 14655.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(31, 30, 143, 17188.00, 19188.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-05', '0000-00-00', 0),
(32, 36, 144, 88367.00, 105667.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(33, 97, 146, 29021.00, 31021.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(34, 45, 147, 39204.00, 41204.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(35, 75, 148, 15952.00, 17952.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(36, 46, 149, 89749.00, 107049.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(37, 74, 151, 36187.00, 38187.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(38, 91, 153, 27022.00, 29022.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(39, 53, 155, 20903.00, 22903.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(40, 7, 156, 19478.00, 21478.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(41, 1, 157, 15150.00, 17150.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(43, 4, 159, 88367.00, 105667.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(44, 85, 160, 27022.00, 29022.00, '2ND TRANCHE - BBM', '2026-01-01', '2025-12-31', '2026-01-06', '2026-01-06', 0),
(45, 85, 160, 27022.00, 29022.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(46, 37, 161, 27277.00, 29277.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(47, 50, 162, 21070.00, 23070.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(48, 102, 163, 15150.00, 17150.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(49, 25, 164, 88367.00, 105667.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(50, 57, 165, 36187.00, 38187.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(51, 22, 166, 21070.00, 23070.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(52, 59, 168, 21070.00, 23070.00, '2ND TRANCHE - BBM', '2026-01-01', '2025-12-31', '2026-01-06', '2026-01-06', 0),
(53, 59, 168, 21070.00, 23070.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(54, 16, 169, 20903.00, 22903.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(55, 68, 170, 20903.00, 22903.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(56, 47, 172, 22102.00, 24102.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(57, 44, 173, 33322.00, 35322.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(58, 28, 174, 27022.00, 29022.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(59, 24, 175, 20011.00, 22011.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(61, 6, 177, 91155.00, 108455.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(62, 31, 178, 36187.00, 38187.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(63, 56, 179, 19478.00, 21478.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(64, 72, 180, 18099.00, 20099.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(65, 40, 181, 27022.00, 29022.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(66, 55, 182, 98488.00, 115788.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(67, 19, 183, 36544.00, 38544.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(68, 32, 184, 36544.00, 38544.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(69, 39, 185, 20903.00, 22903.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(70, 13, 186, 19303.00, 21303.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(71, 87, 187, 88367.00, 105667.00, '2ND TRANCHE - BBM', '2026-01-01', '2026-01-31', '2026-01-06', '2026-03-02', 0),
(72, 29, 188, 33974.00, 35974.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(73, 134, 189, 15150.00, 17150.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(74, 42, 190, 19303.00, 21303.00, '2ND TRANCHE - BBM', '2025-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(75, 20, 191, 88367.00, 105667.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(76, 86, 192, 23027.00, 25027.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(78, 17, 194, 17061.00, 19061.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(79, 54, 195, 30979.00, 32979.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(80, 63, 196, 33646.00, 35646.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(81, 33, 197, 89749.00, 107049.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(82, 110, 198, 27022.00, 29022.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(83, 14, 199, 20011.00, 22011.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(84, 3, 201, 17188.00, 19188.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(85, 82, 202, 36187.00, 38187.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(86, 101, 203, 23027.00, 25027.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(87, 131, 204, 88367.00, 98017.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(88, 65, 205, 23027.00, 25027.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(89, 103, 206, 23027.00, 25027.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(90, 98, 207, 14479.00, 16479.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(91, 104, 208, 23027.00, 25027.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(92, 113, 209, 23027.00, 25027.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(93, 105, 210, 23027.00, 25027.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(94, 2, 212, 36187.00, 38187.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(95, 49, 213, 36187.00, 38187.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(96, 62, 214, 91155.00, 108455.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(97, 130, 215, 88367.00, 90367.00, '2ND TRANCHE - BBM', '2026-01-01', '2025-12-31', '2026-01-06', '2026-01-06', 0),
(98, 130, 215, 88367.00, 105667.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(99, 129, 216, 88367.00, 105667.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(100, 73, 217, 89749.00, 107049.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(101, 121, 218, 88367.00, 90367.00, '2ND TRANCHE - BBM', '2026-01-01', '2025-12-31', '2026-01-06', '2026-01-06', 0),
(102, 121, 218, 88367.00, 105667.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(103, 123, 219, 88367.00, 105667.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(104, 119, 220, 88367.00, 105667.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(105, 122, 221, 88367.00, 105667.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(106, 118, 222, 88367.00, 105667.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(107, 70, 223, 100554.00, 119654.00, '2ND TRANCHE - BBM', '2025-01-01', NULL, '2026-01-06', '2026-03-02', 0),
(108, 128, 224, 15150.00, 17150.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(109, 108, 229, 98185.00, 115485.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(110, 71, 230, 47727.00, 49727.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(111, 52, 231, 35049.00, 37049.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(112, 79, 232, 30597.00, 32597.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(113, 89, 233, 32099.00, 34099.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(114, 60, 234, 30597.00, 32597.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(115, 41, 235, 30024.00, 32024.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(116, 27, 236, 30597.00, 32597.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(117, 83, 238, 30308.00, 32308.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(118, 18, 239, 34733.00, 36733.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(119, 77, 240, 30597.00, 32597.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(120, 48, 241, 30024.00, 32024.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(121, 21, 242, 19526.00, 21526.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(122, 38, 243, 40604.00, 42604.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(123, 58, 244, 30597.00, 32597.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(124, 100, 248, 16833.00, 18833.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(125, 61, 249, 43996.00, 45996.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(126, 92, 250, 25586.00, 27586.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(127, 66, 251, 18957.00, 20957.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-01-06', '0000-00-00', 0),
(129, 94, 252, 128397.00, 139397.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-03-02', '0000-00-00', 0),
(130, 124, 253, 36187.00, 38187.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-03-02', '0000-00-00', 0),
(131, 120, 254, 36544.00, 38544.00, 'Step Increment', '2026-02-01', NULL, '2026-03-02', '0000-00-00', 0),
(132, 87, 256, 89749.00, 107049.00, 'Step Increment', '2026-02-01', NULL, '2026-03-02', '0000-00-00', 0),
(133, 95, 257, 19303.00, 21303.00, 'Promotion', '2026-01-01', NULL, '2026-03-02', '0000-00-00', 0),
(134, 99, 258, 19303.00, 21303.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-03-02', '0000-00-00', 0),
(135, 93, 259, 27022.00, 29022.00, 'Promotion', '2026-01-01', NULL, '2026-03-02', '0000-00-00', 0),
(136, 112, 260, 19303.00, 21303.00, '2ND TRANCHE - BBM', '2026-01-01', NULL, '2026-03-02', '0000-00-00', 0);

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
(1, 1, 1, 86101.00),
(2, 1, 3, 2000.00),
(3, 1, 4, 7650.00),
(4, 1, 5, 7650.00),
(5, 2, 1, 34572.00),
(6, 2, 3, 2000.00),
(12, 5, 1, 46649.00),
(13, 5, 3, 2000.00),
(14, 6, 1, 36187.00),
(15, 6, 3, 2000.00),
(18, 8, 1, 27022.00),
(19, 8, 3, 2000.00),
(20, 9, 1, 19478.00),
(21, 9, 1, 2000.00),
(22, 10, 1, 15150.00),
(23, 10, 1, 2000.00),
(24, 11, 1, 15150.00),
(25, 11, 3, 2000.00),
(26, 12, 1, 15150.00),
(27, 12, 3, 2000.00),
(28, 13, 1, 15150.00),
(29, 13, 3, 2000.00),
(33, 15, 1, 12748.00),
(34, 15, 3, 2000.00),
(35, 16, 1, 12655.00),
(36, 16, 3, 2000.00),
(37, 17, 1, 88367.00),
(38, 17, 3, 2000.00),
(39, 18, 1, 88367.00),
(40, 18, 4, 7650.00),
(41, 18, 5, 7650.00),
(42, 18, 3, 2000.00),
(43, 19, 1, 33646.00),
(44, 19, 3, 2000.00),
(45, 20, 1, 27022.00),
(46, 20, 3, 2000.00),
(47, 21, 1, 16079.00),
(48, 21, 3, 2000.00),
(49, 22, 1, 17188.00),
(50, 22, 3, 2000.00),
(51, 23, 1, 14267.00),
(52, 23, 3, 2000.00),
(53, 24, 1, 15150.00),
(54, 24, 3, 2000.00),
(55, 25, 1, 14696.00),
(56, 25, 3, 2000.00),
(57, 26, 1, 15262.00),
(58, 26, 3, 2000.00),
(59, 27, 1, 36544.00),
(60, 27, 3, 2000.00),
(61, 28, 1, 19303.00),
(62, 28, 3, 2000.00),
(63, 29, 1, 17315.00),
(64, 29, 3, 2000.00),
(65, 30, 1, 12655.00),
(66, 30, 3, 2000.00),
(67, 31, 1, 17188.00),
(68, 31, 3, 2000.00),
(69, 32, 1, 88367.00),
(70, 32, 3, 2000.00),
(71, 32, 4, 7650.00),
(72, 32, 5, 7650.00),
(73, 33, 1, 29021.00),
(74, 33, 3, 2000.00),
(75, 34, 1, 39204.00),
(76, 34, 1, 2000.00),
(77, 35, 1, 15952.00),
(78, 35, 3, 2000.00),
(79, 36, 1, 89749.00),
(80, 36, 3, 2000.00),
(81, 36, 4, 7650.00),
(82, 36, 5, 7650.00),
(83, 37, 1, 36187.00),
(84, 37, 3, 2000.00),
(85, 38, 1, 27022.00),
(86, 38, 3, 2000.00),
(87, 39, 1, 20903.00),
(88, 39, 3, 2000.00),
(89, 40, 1, 19478.00),
(90, 40, 3, 2000.00),
(91, 41, 1, 15150.00),
(92, 41, 3, 2000.00),
(95, 43, 1, 88367.00),
(96, 43, 3, 2000.00),
(97, 43, 4, 7650.00),
(98, 43, 5, 7650.00),
(99, 44, 1, 27022.00),
(100, 44, 3, 2000.00),
(101, 45, 1, 27022.00),
(102, 45, 3, 2000.00),
(103, 46, 1, 27277.00),
(104, 46, 3, 2000.00),
(105, 47, 1, 21070.00),
(106, 47, 3, 2000.00),
(107, 48, 1, 15150.00),
(108, 48, 3, 2000.00),
(109, 49, 1, 88367.00),
(110, 49, 3, 2000.00),
(111, 49, 4, 7650.00),
(112, 49, 5, 7650.00),
(113, 50, 1, 36187.00),
(114, 50, 3, 2000.00),
(115, 51, 1, 21070.00),
(116, 51, 3, 2000.00),
(117, 52, 1, 21070.00),
(118, 52, 3, 2000.00),
(119, 53, 1, 21070.00),
(120, 53, 3, 2000.00),
(121, 54, 1, 20903.00),
(122, 54, 3, 2000.00),
(123, 55, 1, 20903.00),
(124, 55, 3, 2000.00),
(125, 56, 1, 22102.00),
(126, 56, 3, 2000.00),
(127, 57, 1, 33322.00),
(128, 57, 3, 2000.00),
(129, 58, 1, 27022.00),
(130, 58, 3, 2000.00),
(131, 59, 1, 20011.00),
(132, 59, 3, 2000.00),
(135, 61, 1, 91155.00),
(136, 61, 3, 2000.00),
(137, 61, 4, 7650.00),
(138, 61, 5, 7650.00),
(139, 62, 1, 36187.00),
(140, 62, 3, 2000.00),
(141, 63, 1, 19478.00),
(142, 63, 3, 2000.00),
(143, 64, 1, 18099.00),
(144, 64, 3, 2000.00),
(145, 65, 1, 27022.00),
(146, 65, 3, 2000.00),
(147, 66, 1, 98488.00),
(148, 66, 3, 2000.00),
(149, 66, 4, 7650.00),
(150, 66, 5, 7650.00),
(151, 67, 1, 36544.00),
(152, 67, 3, 2000.00),
(153, 68, 1, 36544.00),
(154, 68, 3, 2000.00),
(155, 69, 1, 20903.00),
(156, 69, 3, 2000.00),
(157, 70, 1, 19303.00),
(158, 70, 3, 2000.00),
(159, 71, 1, 88367.00),
(160, 71, 3, 2000.00),
(161, 71, 4, 7650.00),
(162, 71, 5, 7650.00),
(163, 72, 1, 33974.00),
(164, 72, 3, 2000.00),
(165, 73, 1, 15150.00),
(166, 73, 3, 2000.00),
(167, 74, 1, 19303.00),
(168, 74, 3, 2000.00),
(169, 75, 1, 88367.00),
(170, 75, 3, 2000.00),
(171, 75, 4, 7650.00),
(172, 75, 5, 7650.00),
(173, 76, 1, 23027.00),
(174, 76, 3, 2000.00),
(177, 78, 1, 17061.00),
(178, 78, 3, 2000.00),
(179, 79, 1, 30979.00),
(180, 79, 3, 2000.00),
(181, 80, 1, 33646.00),
(182, 80, 3, 2000.00),
(183, 81, 1, 89749.00),
(184, 81, 3, 2000.00),
(185, 81, 4, 7650.00),
(186, 81, 5, 7650.00),
(187, 82, 1, 27022.00),
(188, 82, 3, 2000.00),
(189, 83, 1, 20011.00),
(190, 83, 3, 2000.00),
(191, 84, 1, 17188.00),
(192, 84, 3, 2000.00),
(193, 85, 1, 36187.00),
(194, 85, 3, 2000.00),
(195, 86, 1, 23027.00),
(196, 86, 3, 2000.00),
(197, 87, 1, 88367.00),
(198, 87, 3, 2000.00),
(199, 87, 4, 7650.00),
(200, 88, 1, 23027.00),
(201, 88, 3, 2000.00),
(202, 89, 1, 23027.00),
(203, 89, 1, 2000.00),
(204, 90, 1, 14479.00),
(205, 90, 3, 2000.00),
(206, 91, 1, 23027.00),
(207, 91, 3, 2000.00),
(208, 92, 1, 23027.00),
(209, 92, 3, 2000.00),
(210, 93, 1, 23027.00),
(211, 93, 3, 2000.00),
(212, 94, 1, 36187.00),
(213, 94, 3, 2000.00),
(214, 95, 1, 36187.00),
(215, 95, 3, 2000.00),
(216, 96, 1, 91155.00),
(217, 96, 3, 2000.00),
(218, 96, 4, 7650.00),
(219, 96, 5, 7650.00),
(220, 97, 1, 88367.00),
(221, 97, 3, 2000.00),
(222, 98, 1, 88367.00),
(223, 98, 3, 2000.00),
(224, 98, 4, 7650.00),
(225, 98, 5, 7650.00),
(226, 99, 1, 88367.00),
(227, 99, 3, 2000.00),
(228, 99, 4, 7650.00),
(229, 99, 5, 7650.00),
(230, 100, 1, 89749.00),
(231, 100, 3, 2000.00),
(232, 100, 5, 7650.00),
(233, 100, 4, 7650.00),
(234, 101, 1, 88367.00),
(235, 101, 3, 2000.00),
(236, 102, 1, 88367.00),
(237, 102, 3, 2000.00),
(238, 102, 4, 7650.00),
(239, 102, 5, 7650.00),
(240, 103, 1, 88367.00),
(241, 103, 3, 2000.00),
(242, 103, 4, 7650.00),
(243, 103, 5, 7650.00),
(244, 104, 1, 88367.00),
(245, 104, 3, 2000.00),
(246, 104, 4, 7650.00),
(247, 104, 5, 7650.00),
(248, 105, 1, 88367.00),
(249, 105, 3, 2000.00),
(250, 105, 4, 7650.00),
(251, 105, 5, 7650.00),
(252, 106, 1, 88367.00),
(253, 106, 3, 2000.00),
(254, 106, 4, 7650.00),
(255, 106, 5, 7650.00),
(260, 108, 1, 15150.00),
(261, 108, 3, 2000.00),
(262, 109, 1, 98185.00),
(263, 109, 3, 2000.00),
(264, 109, 4, 7650.00),
(265, 109, 5, 7650.00),
(266, 110, 1, 47727.00),
(267, 110, 3, 2000.00),
(268, 111, 1, 35049.00),
(269, 111, 3, 2000.00),
(270, 112, 1, 30597.00),
(271, 112, 3, 2000.00),
(272, 113, 1, 32099.00),
(273, 113, 3, 2000.00),
(274, 114, 1, 30597.00),
(275, 114, 3, 2000.00),
(276, 115, 1, 30024.00),
(277, 115, 3, 2000.00),
(278, 116, 1, 30597.00),
(279, 116, 3, 2000.00),
(280, 117, 1, 30308.00),
(281, 117, 3, 2000.00),
(282, 118, 1, 34733.00),
(283, 118, 3, 2000.00),
(284, 119, 1, 30597.00),
(285, 119, 3, 2000.00),
(286, 120, 1, 30024.00),
(287, 120, 3, 2000.00),
(288, 121, 1, 19526.00),
(289, 121, 3, 2000.00),
(290, 122, 1, 40604.00),
(291, 122, 3, 2000.00),
(292, 123, 1, 30597.00),
(293, 123, 3, 2000.00),
(294, 124, 1, 16833.00),
(295, 124, 3, 2000.00),
(296, 125, 1, 43996.00),
(297, 125, 3, 2000.00),
(298, 126, 1, 25586.00),
(299, 126, 3, 2000.00),
(300, 127, 1, 18957.00),
(301, 127, 3, 2000.00),
(305, 129, 1, 128397.00),
(306, 129, 3, 2000.00),
(307, 129, 4, 9000.00),
(308, 130, 1, 36187.00),
(309, 130, 3, 2000.00),
(312, 7, 1, 36187.00),
(313, 7, 3, 2000.00),
(314, 131, 1, 36544.00),
(315, 131, 3, 2000.00),
(316, 14, 1, 15150.00),
(317, 14, 3, 2000.00),
(318, 14, 9, 3787.50),
(319, 107, 1, 100554.00),
(320, 107, 3, 2000.00),
(321, 107, 4, 8550.00),
(322, 107, 5, 8550.00),
(323, 132, 1, 89749.00),
(324, 132, 3, 2000.00),
(325, 132, 4, 7650.00),
(326, 132, 5, 7650.00),
(327, 133, 1, 19303.00),
(328, 133, 3, 2000.00),
(329, 134, 1, 19303.00),
(330, 134, 3, 2000.00),
(331, 135, 1, 27022.00),
(332, 135, 3, 2000.00),
(333, 136, 1, 19303.00),
(334, 136, 3, 2000.00);

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
(2, 10, '1011-06', 'Regular', '2025-01-01', '2025-12-31', '6', '1', '', 'Supervisory', 'Others', 44588.00, 0, 'Promotion', '0'),
(4, 64, '1011-09', 'Regular', '2025-01-01', '2025-12-31', '8', '1', '', 'Operations', 'Others', 34572.00, 0, 'Original', '0'),
(5, 67, '1011-08', 'Regular', '2025-01-01', '2025-12-31', '18', '1', '', 'Operations', 'Others', 25661.00, 0, 'Original', '0'),
(6, 5, '1011-07', 'Regular', '2025-01-01', '2025-12-31', '7', '1', '', 'Administrative', 'Clerical Services', 18648.00, 0, 'Original', '0'),
(7, 84, '1011-15', 'Regular', '2026-01-01', '2025-12-31', '14', '1', '', 'Operations', 'Others', 14588.00, 0, 'Original', '0'),
(8, 51, '1011-12', 'Regular', '2026-01-01', '2025-12-31', '11', '4', '', 'Office Staff', 'Clerical Services', 14588.00, 0, 'Original', '0'),
(9, 78, '1011-13', 'Regular', '2025-01-01', '2025-12-31', '12', '1', '', 'Office Staff', 'Clerical Services', 14588.00, 0, 'Original', '0'),
(10, 43, '1011-11', 'Regular', '2025-01-01', '2025-12-31', '10', '1', '', 'Office Staff', 'Clerical Services', 14588.00, 0, 'Original', '0'),
(11, 88, '1011-14', 'Regular', '2025-01-01', '2025-12-31', '13', '11', '', 'Operations', 'Health Services', 14588.00, 0, 'Promotion', '0'),
(12, 69, '1011-04', 'Regular', '2025-01-01', '2025-12-31', '4', '1', '', 'Operations', 'Others', 12270.00, 0, 'Original', '0'),
(13, 111, '1011-03', 'Regular', '2025-01-02', '2025-12-31', '3', '6', '', 'Office Staff', 'Clerical Services', 12177.00, 0, 'Original', '0'),
(14, 126, '1031-01', 'Regular', '2025-07-01', '2025-12-31', '126', '13', '', 'Supervisory', 'Others', 84719.00, 0, 'Original', '0'),
(15, 23, '1031-17', 'Regular', '2025-01-01', '2025-12-31', '138', '13', '', 'Supervisory', 'Clerical Services', 34929.00, 0, 'Promotion', '0'),
(16, 80, '1031-05', 'Regular', '2025-01-01', '2025-12-31', '130', '5', '', 'Administrative', 'Clerical Services', 32215.00, 0, 'Promotion', '0'),
(17, 12, '1031-02', 'Regular', '2025-01-01', '2025-12-31', '127', '13', '', 'Administrative', 'Clerical Services', 25661.00, 0, 'Promotion', '0'),
(18, 8, '1031-03', 'Regular', '2025-01-01', '2025-12-31', '128', '9', '', 'Administrative', 'Clerical Services', 18481.00, 0, 'Original', '0'),
(19, 107, '1031-07', 'Regular', '2025-01-01', '2025-12-31', '132', '9', '', 'Administrative', 'Clerical Services', 16556.00, 0, 'Original', '0'),
(20, 132, '1031-08', 'Regular', '2025-01-01', '0000-00-00', '133', '10', '', 'Utility', 'Technical Services', 16430.00, 0, 'Original', '1'),
(21, 115, '1031-12', 'Regular', '2025-03-08', '2025-12-31', '137', '10', '', 'Administrative', 'Clerical Services', 15485.00, 0, 'Original', '0'),
(22, 11, '1031-04', 'Regular', '2025-01-01', '2025-12-31', '129', '13', '', 'Administrative', 'Clerical Services', 14701.00, 0, 'Original', '0'),
(23, 96, '1031-06', 'Regular', '2025-03-12', '2025-12-31', '131', '13', '', 'Administrative', 'Clerical Services', 14588.00, 0, 'Original', '0'),
(24, 26, '1031-10', 'Regular', '2025-01-01', '2025-12-31', '135', '10', '', 'Operations', 'Technical Services', 14167.00, 0, 'Original', '0'),
(25, 106, '1031-09', 'Regular', '2024-11-04', '2025-12-31', '134', '9', '', 'Operations', 'Janitorial Services', 13739.00, 0, 'Original', '0'),
(26, 70, '1016-01', 'Regular', '2025-07-01', '2025-12-31', '25', '2', '', 'Supervisory', 'Others', 96487.00, 0, 'Elected', '0'),
(35, 76, '1021-10', 'Regular', '2023-11-14', '0000-00-00', '40', '3', '', 'Supervisory', 'Others', 84719.00, 0, 'Elected', '1'),
(36, 20, '1022-01', 'Regular', '2025-01-01', '2025-12-31', '42', '4', '', 'Supervisory', 'Clerical Services', 84719.00, 0, 'Promotion', '0'),
(37, 63, '1022-07', 'Regular', '2025-01-01', '2025-12-31', '48', '4', '', 'Administrative', 'Clerical Services', 32215.00, 0, 'Promotion', '0'),
(38, 54, '1022-06', 'Regular', '2025-01-16', '2025-12-31', '47', '4', '', 'Administrative', 'Clerical Services', 29583.00, 0, 'Promotion', '0'),
(39, 86, '1022-02', 'Regular', '2025-02-10', '2025-12-31', '43', '4', '', 'Administrative', 'Clerical Services', 21943.00, 0, 'Promotion', '0'),
(40, 95, '1022-03', 'Regular', '2025-01-01', '2025-12-31', '44', '4', '', 'Administrative', 'Clerical Services', 18481.00, 0, 'Promotion', '0'),
(41, 17, '1022-04', 'Regular', '2025-01-01', '2025-12-31', '45', '4', '', 'Administrative', 'Clerical Services', 16430.00, 0, 'Promotion', '0'),
(43, 19, '1041-04', 'Regular', '2025-01-01', '2025-12-31', '90', '9', '', 'Supervisory', 'Clerical Services', 34929.00, 0, 'Original', '0'),
(44, 55, '1041-01', 'Regular', '2025-01-01', '2025-12-31', '87', '9', '', 'Supervisory', 'Others', 94840.00, 0, 'Original', '0'),
(45, 32, '1041-05', 'Regular', '2025-01-01', '2025-12-31', '91', '9', '', 'Supervisory', 'Others', 34048.00, 0, 'Original', '0'),
(46, 39, '1041-02', 'Regular', '2025-01-01', '2025-12-31', '88', '9', '', 'Administrative', 'Clerical Services', 19997.00, 0, 'Original', '0'),
(47, 13, '1041-03', 'Regular', '2025-01-01', '2025-12-31', '89', '9', '', 'Administrative', 'Clerical Services', 18481.00, 0, 'Original', '0'),
(48, 87, '1051-01', 'Regular', '2025-01-01', '2025-12-31', '122', '12', '', 'Supervisory', 'Clerical Services', 84719.00, 0, 'Promotion', '0'),
(49, 29, '1051-02', 'Regular', '2025-01-01', '2025-12-31', '123', '12', '', 'Administrative', 'Clerical Services', 32542.00, 0, 'Promotion', '0'),
(50, 42, '1051-04', 'Regular', '2025-01-01', '2025-12-31', '125', '12', '', 'Administrative', 'Clerical Services', 18481.00, 0, 'Original', '0'),
(51, 134, '1051-03', 'Regular', '2025-03-25', '2025-12-31', '124', '12', '', 'Administrative', 'Clerical Services', 14588.00, 0, 'Original', '0'),
(52, 6, '1071-01', 'Regular', '2025-01-01', '2025-12-31', '81', '8', '', 'Supervisory', 'Financial Services', 87507.00, 0, 'Promotion', '0'),
(53, 31, '1071-05', 'Regular', '2024-11-14', '2025-12-31', '86', '8', '', 'Administrative', 'Financial Services', 34572.00, 0, 'Promotion', '0'),
(54, 40, '1071-02', 'Regular', '2025-01-21', '2025-12-31', '82', '8', '', 'Administrative', 'Financial Services', 25661.00, 0, 'Promotion', '0'),
(55, 56, '1071-03', 'Regular', '2025-01-01', '2025-12-31', '84', '8', '', 'Administrative', 'Financial Services', 18648.00, 0, 'Promotion', '0'),
(56, 72, '1071-04', 'Regular', '2025-03-12', '2025-12-31', '85', '8', '', 'Administrative', 'Clerical Services', 17429.00, 0, 'Original', '0'),
(57, 46, '1081-01', 'Regular', '2025-01-01', '2025-12-31', '78', '7', '', 'Supervisory', 'Financial Services', 86101.00, 0, 'Promotion', '0'),
(58, 74, '1081-07', 'Regular', '2024-06-18', '2025-12-31', '75', '7', '', 'Administrative', 'Financial Services', 34572.00, 0, 'Promotion', '0'),
(59, 91, '1081-06', 'Regular', '2024-11-04', '2025-12-31', '74', '7', '', 'Administrative', 'Financial Services', 25661.00, 0, 'Promotion', '0'),
(61, 7, '1081-03', 'Regular', '2023-02-01', '2025-12-31', '71', '7', '', 'Administrative', 'Financial Services', 18481.00, 0, 'Original', '0'),
(62, 99, '1081-04', 'Regular', '2025-03-12', '2025-12-31', '72', '7', '', 'Administrative', 'Financial Services', 18481.00, 0, 'Original', '0'),
(63, 1, '1081-05', 'Regular', '2025-01-02', '2025-12-31', '73', '7', '', 'Administrative', 'Financial Services', 14588.00, 0, 'Original', '0'),
(64, 25, '1091-01', 'Regular', '2025-01-01', '2025-12-31', '49', '5', '', 'Supervisory', 'Financial Services', 84719.00, 0, 'Promotion', '0'),
(65, 57, '1091-03', 'Regular', '2024-11-04', '2025-12-31', '51', '5', '', 'Administrative', 'Financial Services', 34572.00, 0, 'Promotion', '0'),
(66, 44, '1091-10', 'Regular', '2024-12-03', '2025-12-31', '58', '5', '', 'Administrative', 'Financial Services', 31891.00, 0, 'Promotion', '0'),
(67, 28, '1091-04', 'Regular', '2025-01-01', '2025-12-31', '52', '5', '', 'Administrative', 'Financial Services', 25661.00, 0, 'Promotion', '0'),
(68, 93, '1091-13', 'Regular', '2025-01-02', '2025-12-31', '61', '5', '', 'Development', 'ICT Services', 25661.00, 0, 'Promotion', '0'),
(71, 22, '1091-09', 'Regular', '2025-01-01', '2025-12-31', '57', '5', '', 'Administrative', 'Financial Services', 20164.00, 0, 'Original', '0'),
(72, 16, '1091-06', 'Regular', '2025-01-01', '2025-12-31', '54', '5', '', 'Administrative', 'Financial Services', 19997.00, 0, 'Original', '0'),
(73, 68, '1091-07', 'Regular', '2025-01-01', '2025-12-31', '55', '5', '', 'Administrative', 'Financial Services', 19997.00, 0, 'Original', '0'),
(74, 24, '1091-11', 'Regular', '2025-01-01', '2025-12-31', '59', '5', '', 'Administrative', 'Financial Services', 19158.00, 0, 'Original', '0'),
(75, 112, '1091-12', 'Regular', '2025-01-02', '2025-12-31', '60', '5', '', 'Administrative', 'Financial Services', 18481.00, 0, 'Original', '0'),
(76, 4, '1101-01', 'Regular', '2025-03-12', '2025-12-31', '62', '6', '', 'Supervisory', 'Technical Services', 84719.00, 0, 'Promotion', '0'),
(77, 37, '1101-07', 'Regular', '2025-01-01', '2025-12-31', '68', '6', '', 'Administrative', 'Technical Services', 25916.00, 0, 'Promotion', '0'),
(78, 85, '1101-02', 'Regular', '2025-01-01', '2025-12-31', '63', '6', '', 'Administrative', 'Clerical Services', 25661.00, 0, 'Promotion', '0'),
(79, 50, '1101-03', 'Regular', '2025-01-01', '2025-12-31', '64', '6', '', 'Administrative', 'Clerical Services', 20164.00, 0, 'Promotion', '0'),
(80, 102, '1101-05', 'Regular', '2025-01-02', '2025-12-31', '66', '6', '', 'Administrative', 'Clerical Services', 14588.00, 0, 'Original', '0'),
(86, 109, '7611-03', 'Regular', '2025-01-21', '0000-00-00', '154', '15', '', 'Administrative', 'Social Services', 18481.00, 0, 'Original', '1'),
(89, 34, '8711-10', 'Regular', '2025-01-01', '0000-00-00', '148', '14', '', 'Administrative', 'Agricultural Services', 38245.00, 0, 'Promotion', '1'),
(98, 36, '8751-01', 'Regular', '2025-08-26', '2025-12-31', '92', '10', '', 'Supervisory', 'Technical Services', 84719.00, 0, 'Promotion', '0'),
(100, 45, '8751-05', 'Regular', '2025-01-21', '2025-12-31', '96', '10', '', 'Development', 'Technical Services', 37454.00, 0, 'Promotion', '0'),
(101, 97, '8751-02', 'Regular', '2025-03-12', '2025-12-31', '93', '10', '', 'Development', 'Technical Services', 27635.00, 0, 'Original', '0'),
(102, 90, '8751-04', 'Regular', '2025-01-01', '0000-00-00', '95', '10', '', 'Development', 'Technical Services', 16089.00, 0, 'Original', '1'),
(103, 75, '8751-03', 'Regular', '2025-01-01', '2025-12-31', '94', '10', '', 'Development', 'Technical Services', 15391.00, 0, 'Original', '0'),
(104, 35, '8811-01', 'Regular', '2025-01-01', '2025-12-31', '159', '16', '', 'Administrative', 'Others', 16556.00, 0, 'Original', '0'),
(105, 30, '8811-16', 'Regular', '2025-01-01', '2025-12-31', '162', '10', '', 'Utility', 'Technical Services', 16556.00, 0, 'Original', '0'),
(106, 116, '8811-14', 'Regular', '2025-01-02', '2025-12-31', '160', '13', '', 'Utility', 'Others', 12177.00, 0, 'Original', '0'),
(107, 117, '1011-17', 'Casual', '2025-07-01', '0000-00-00', '16', '1', '', 'Office Staff', 'Clerical Services', 12177.00, 0, 'Original', '1'),
(110, 133, '1011-20', 'Casual', '2025-07-01', '0000-00-00', '20', '1', '', 'Office Staff', 'Clerical Services', 12177.00, 0, 'Original', '1'),
(111, 127, '1011-19', 'Casual', '2025-07-01', '0000-00-00', '19', '1', '', 'Office Staff', 'Others', 12177.00, 0, 'Original', '1'),
(112, 125, '1011-18', 'Casual', '2025-07-01', '0000-00-00', '17', '1', '', 'Office Staff', 'Clerical Services', 12177.00, 0, 'Original', '1'),
(114, 135, '1016-05', 'Casual', '2025-07-01', '0000-00-00', '29', '2', '', 'Office Staff', 'Clerical Services', 12177.00, 0, 'Original', '1'),
(118, 10, '2026-01-1011-06', 'Regular', '2026-01-05', '0000-00-00', '6', '1', '', 'Supervisory', 'Others', 46649.00, 0, '2ND TRANCHE - BBM', '1'),
(119, 64, '2026-01-1011-10', 'Regular', '2026-01-05', '0000-00-00', '8', '1', '', 'Administrative', 'Others', 36187.00, 0, '2ND TRANCHE - BBM', '1'),
(120, 120, '2026-01-1011-5', 'Regular', '2026-01-05', '2026-01-31', '5', '1', '', 'Administrative', 'Others', 36187.00, 0, '2ND TRANCHE - BBM', '0'),
(121, 67, '2026-01-1011-08', 'Regular', '2026-01-01', '0000-00-00', '18', '1', '', 'Administrative', 'Others', 27022.00, 0, '2ND TRANCHE - BBM', '1'),
(122, 5, '2026-01-1011-07', 'Regular', '2026-01-01', '0000-00-00', '7', '1', '', 'Administrative', 'Others', 19478.00, 0, '2ND TRANCHE - BBM', '1'),
(123, 84, '2026-01-1011-15', 'Regular', '2026-01-01', '2025-12-31', '14', '1', '', 'Utility', 'Others', 15150.00, 0, '2ND TRANCHE - BBM', '1'),
(124, 51, '2026-01-1011-12', 'Regular', '2026-01-01', '2026-12-31', '11', '1', '', 'Administrative', 'Others', 15150.00, 0, '2ND TRANCHE - BBM', '1'),
(125, 78, '2026-01-1011-13', 'Regular', '2026-01-01', '0000-00-00', '12', '6', '', 'Administrative', 'Clerical Services', 15150.00, 0, '2ND TRANCHE - BBM', '1'),
(126, 43, '2026-01-1011-11', 'Regular', '2026-01-01', '0000-00-00', '10', '1', '', 'Administrative', 'Clerical Services', 15150.00, 0, '2ND TRANCHE - BBM', '1'),
(127, 88, '2026-01-1011-14', 'Regular', '2026-01-01', '0000-00-00', '13', '11', '', 'Operations', 'Health Services', 15150.00, 0, '2ND TRANCHE - BBM', '1'),
(128, 69, '2026-01-1011-4', 'Regular', '2026-01-01', '0000-00-00', '4', '1', '', 'Administrative', 'Clerical Services', 12748.00, 0, '2ND TRANCHE - BBM', '1'),
(129, 111, '2026-01-1011-03', 'Regular', '2026-01-01', '0000-00-00', '3', '1', '', 'Administrative', 'Clerical Services', 12655.00, 0, '2ND TRANCHE - BBM', '1'),
(130, 126, '2026-01-1031-1', 'Regular', '2026-01-01', '0000-00-00', '126', '13', '', 'Administrative', 'Others', 88367.00, 0, '2ND TRANCHE - BBM', '1'),
(131, 80, '2026-01-1031-05', 'Regular', '2026-01-01', '0000-00-00', '130', '5', '', 'Administrative', 'Others', 33646.00, 0, '2ND TRANCHE - BBM', '1'),
(132, 12, '2026-01-1031-02', 'Regular', '2026-01-01', '0000-00-00', '127', '13', '', 'Administrative', 'Others', 27022.00, 0, '2ND TRANCHE - BBM', '1'),
(133, 115, '2026-01-1031-12', 'Regular', '2026-01-01', '0000-00-00', '137', '10', '', 'Administrative', 'Clerical Services', 16079.00, 0, '2ND TRANCHE - BBM', '1'),
(134, 107, '2026-01-1031-07', 'Regular', '2026-01-01', '0000-00-00', '132', '13', '', 'Administrative', 'Clerical Services', 17188.00, 0, '2ND TRANCHE - BBM', '1'),
(135, 106, '2026-01-1031-09', 'Regular', '2026-01-01', '0000-00-00', '134', '9', '', 'Administrative', 'Clerical Services', 14267.00, 0, '2ND TRANCHE - BBM', '1'),
(136, 96, '2026-01-1031-06', 'Regular', '2026-01-01', '0000-00-00', '131', '13', '', 'Administrative', 'Clerical Services', 15150.00, 0, '2ND TRANCHE - BBM', '1'),
(137, 26, '2026-01-1031-10', 'Regular', '2026-01-01', '0000-00-00', '135', '10', '', 'Utility', 'Manual Labour', 14696.00, 0, '2ND TRANCHE - BBM', '1'),
(138, 11, '2026-01-1031-04', 'Regular', '2026-01-01', '0000-00-00', '129', '13', '', 'Administrative', 'Clerical Services', 15262.00, 0, '2ND TRANCHE - BBM', '1'),
(139, 23, '2026-01-1031-17', 'Regular', '2026-01-01', '0000-00-00', '138', '13', '', 'Supervisory', 'Clerical Services', 36544.00, 0, '2ND TRANCHE - BBM', '1'),
(140, 8, '2026-01-1031-03', 'Regular', '2026-01-01', '0000-00-00', '128', '9', '', 'Administrative', 'Clerical Services', 19303.00, 0, '2ND TRANCHE - BBM', '1'),
(141, 35, '2026-01-8811-01', 'Regular', '2026-01-01', '0000-00-00', '159', '16', '', 'Supervisory', 'Clerical Services', 17315.00, 0, '2ND TRANCHE - BBM', '1'),
(142, 116, '2026-01-8811-14', 'Regular', '2026-01-01', '0000-00-00', '160', '13', '', 'Utility', 'Clerical Services', 12655.00, 0, '2ND TRANCHE - BBM', '1'),
(143, 30, '2026-01-8811-16', 'Regular', '2026-01-01', '0000-00-00', '162', '10', '', 'Utility', 'Clerical Services', 17188.00, 0, '2ND TRANCHE - BBM', '1'),
(144, 36, '2026-01-8751-01', 'Regular', '2026-01-01', '0000-00-00', '92', '10', '', 'Supervisory', 'Others', 88367.00, 0, '2ND TRANCHE - BBM', '1'),
(146, 97, '2026-01-8751-02', 'Regular', '2026-01-01', '0000-00-00', '93', '10', '', 'Administrative', 'Technical Services', 29021.00, 0, '2ND TRANCHE - BBM', '1'),
(147, 45, '2026-01-8751-05', 'Regular', '2026-01-01', '0000-00-00', '96', '10', '', 'Administrative', 'Technical Services', 39204.00, 0, '2ND TRANCHE - BBM', '1'),
(148, 75, '2026-01-8751-03', 'Regular', '2026-01-01', '0000-00-00', '94', '10', '', 'Office Staff', 'Technical Services', 15952.00, 0, '2ND TRANCHE - BBM', '1'),
(149, 46, '2026-01-1081-1', 'Regular', '2026-01-01', '0000-00-00', '78', '7', '', 'Supervisory', 'Financial Services', 89749.00, 0, '2ND TRANCHE - BBM', '1'),
(151, 74, '2026-01-1081-7', 'Regular', '2026-01-01', '0000-00-00', '75', '7', '', 'Administrative', 'Financial Services', 36187.00, 0, '2ND TRANCHE - BBM', '1'),
(153, 91, '2026-01-1081-6', 'Regular', '2026-01-01', '0000-00-00', '74', '7', '', 'Administrative', 'Financial Services', 27022.00, 0, '2ND TRANCHE - BBM', '1'),
(154, 53, '2026-01-1081-2', 'Regular', '2026-01-01', '0000-00-00', '', '7', '', 'Administrative', 'Financial Services', 20903.00, 0, '2ND TRANCHE - BBM', '1'),
(155, 53, '2026-01-1081-02', 'Regular', '2026-01-01', '0000-00-00', '70', '7', '', 'Administrative', 'Financial Services', 20903.00, 0, '2ND TRANCHE - BBM', '1'),
(156, 7, '2026-01-1081-3', 'Regular', '2026-01-01', '0000-00-00', '71', '7', '', 'Administrative', 'Financial Services', 19478.00, 0, '2ND TRANCHE - BBM', '1'),
(157, 1, '2026-01-1081-05', 'Regular', '2026-01-01', '0000-00-00', '73', '7', '', 'Administrative', 'Clerical Services', 15150.00, 0, '2ND TRANCHE - BBM', '1'),
(159, 4, '2026-01-1101-1', 'Regular', '2026-01-01', '0000-00-00', '62', '6', '', 'Supervisory', 'Clerical Services', 88367.00, 0, '2ND TRANCHE - BBM', '1'),
(160, 85, '2026-01-1101-02', 'Regular', '2026-01-01', '0000-00-00', '63', '6', '', 'Administrative', 'Clerical Services', 27022.00, 0, '2ND TRANCHE - BBM', '1'),
(161, 37, '2026-01-1101-7', 'Regular', '2026-01-01', '0000-00-00', '68', '6', '', 'Administrative', 'Clerical Services', 27277.00, 0, '2ND TRANCHE - BBM', '1'),
(162, 50, '2026-01-1101-3', 'Regular', '2026-01-01', '0000-00-00', '64', '6', '', 'Administrative', 'Clerical Services', 21070.00, 0, '2ND TRANCHE - BBM', '1'),
(163, 102, '2026-01-1101-05', 'Regular', '2026-01-01', '0000-00-00', '66', '6', '', 'Administrative', 'Clerical Services', 15150.00, 0, '2ND TRANCHE - BBM', '1'),
(164, 25, '2026-01-1091-1', 'Regular', '2026-01-01', '0000-00-00', '49', '5', '', 'Supervisory', 'Financial Services', 88367.00, 0, '2ND TRANCHE - BBM', '1'),
(165, 57, '2026-01-1091-3', 'Regular', '2026-01-01', '0000-00-00', '51', '5', '', 'Administrative', 'Clerical Services', 36187.00, 0, '2ND TRANCHE - BBM', '1'),
(166, 22, '2026-01-1091-09', 'Regular', '2026-01-01', '0000-00-00', '57', '5', '', 'Administrative', 'Clerical Services', 21070.00, 0, '2ND TRANCHE - BBM', '1'),
(168, 59, '2026-01-1091-08', 'Regular', '2026-01-01', '0000-00-00', '56', '5', '', 'Administrative', 'Clerical Services', 21070.00, 0, '2ND TRANCHE - BBM', '1'),
(169, 16, '2026-01-1091-06', 'Regular', '2026-01-01', '0000-00-00', '54', '5', '', 'Administrative', 'Financial Services', 20903.00, 0, '2ND TRANCHE - BBM', '1'),
(170, 68, '2026-01-1091-07', 'Regular', '2026-01-01', '0000-00-00', '55', '5', '', 'Administrative', 'Clerical Services', 20903.00, 0, '2ND TRANCHE - BBM', '1'),
(172, 47, '2026-01-1091-05', 'Regular', '2026-01-01', '0000-00-00', '53', '5', '', 'Administrative', 'Clerical Services', 22102.00, 0, '2ND TRANCHE - BBM', '1'),
(173, 44, '2026-01-1091-10', 'Regular', '2026-01-01', '0000-00-00', '58', '5', '', 'Administrative', 'Clerical Services', 33322.00, 0, '2ND TRANCHE - BBM', '1'),
(174, 28, '2026-01-1091-04', 'Regular', '2026-01-01', '0000-00-00', '52', '5', '', 'Administrative', 'Clerical Services', 27022.00, 0, '2ND TRANCHE - BBM', '1'),
(175, 24, '2026-01-1091-11', 'Regular', '2026-01-01', '0000-00-00', '59', '5', '', 'Administrative', 'Clerical Services', 20011.00, 0, '2ND TRANCHE - BBM', '1'),
(177, 6, '2026-01-1071-1', 'Regular', '2026-01-01', '0000-00-00', '81', '8', '', 'Supervisory', 'Financial Services', 91155.00, 0, '2ND TRANCHE - BBM', '1'),
(178, 31, '2026-01-1071-05', 'Regular', '2026-01-01', '0000-00-00', '86', '8', '', 'Administrative', 'Clerical Services', 36187.00, 0, '2ND TRANCHE - BBM', '1'),
(179, 56, '2026-01-1071-03', 'Regular', '2026-01-01', '0000-00-00', '84', '8', '', 'Administrative', 'Clerical Services', 19478.00, 0, '2ND TRANCHE - BBM', '1'),
(180, 72, '2026-01-1071-4', 'Regular', '2026-01-01', '0000-00-00', '85', '8', '', 'Administrative', 'Clerical Services', 18099.00, 0, '2ND TRANCHE - BBM', '1'),
(181, 40, '2026-01-1071-02', 'Regular', '2026-01-01', '0000-00-00', '82', '8', '', 'Administrative', 'Clerical Services', 27022.00, 0, '2ND TRANCHE - BBM', '1'),
(182, 55, '2026-01-1041-1', 'Regular', '2026-01-01', '0000-00-00', '87', '9', '', 'Supervisory', 'Financial Services', 98488.00, 0, '2ND TRANCHE - BBM', '1'),
(183, 19, '2026-01-1041-4', 'Regular', '2026-01-01', '0000-00-00', '90', '9', '', 'Administrative', 'Clerical Services', 36544.00, 0, '2ND TRANCHE - BBM', '1'),
(184, 32, '2026-01-1041-5', 'Regular', '2026-01-01', '0000-00-00', '91', '9', '', 'Administrative', 'Clerical Services', 36544.00, 0, '2ND TRANCHE - BBM', '1'),
(185, 39, '2026-01-1041-2', 'Regular', '2026-01-01', '0000-00-00', '88', '9', '', 'Administrative', 'Financial Services', 20903.00, 0, '2ND TRANCHE - BBM', '1'),
(186, 13, '2026-01-1041-3', 'Regular', '2026-01-01', '0000-00-00', '89', '9', '', 'Administrative', 'ICT Services', 19303.00, 0, '2ND TRANCHE - BBM', '1'),
(187, 87, '2026-01-1051-1', 'Regular', '2026-01-01', '2026-01-31', '122', '12', '', 'Supervisory', 'Clerical Services', 88367.00, 0, '2ND TRANCHE - BBM', '0'),
(188, 29, '2026-01-1051-2', 'Regular', '2026-01-01', '0000-00-00', '123', '12', '', 'Administrative', 'Clerical Services', 33974.00, 0, '2ND TRANCHE - BBM', '1'),
(189, 134, '2026-01-1051-3', 'Regular', '2026-01-01', '0000-00-00', '124', '12', '', 'Administrative', 'Clerical Services', 15150.00, 0, '2ND TRANCHE - BBM', '1'),
(190, 42, '2026-01-1051-4', 'Regular', '2025-01-01', '0000-00-00', '125', '12', '', 'Administrative', 'Clerical Services', 19303.00, 0, '2ND TRANCHE - BBM', '1'),
(191, 20, '2026-01-1022-1', 'Regular', '2026-01-01', '0000-00-00', '42', '4', '', 'Supervisory', 'Financial Services', 88367.00, 0, '2ND TRANCHE - BBM', '1'),
(192, 86, '2026-01-1022-2', 'Regular', '2026-01-01', '0000-00-00', '43', '4', '', 'Administrative', 'Clerical Services', 23027.00, 0, '2ND TRANCHE - BBM', '1'),
(194, 17, '2026-01-1022-4', 'Regular', '2026-01-01', '0000-00-00', '45', '4', '', 'Administrative', 'Clerical Services', 17061.00, 0, '2ND TRANCHE - BBM', '1'),
(195, 54, '2026-01-1022-6', 'Regular', '2026-01-01', '0000-00-00', '47', '4', '', 'Administrative', 'Clerical Services', 30979.00, 0, '2ND TRANCHE - BBM', '1'),
(196, 63, '2026-01-1022-7', 'Regular', '2026-01-01', '0000-00-00', '48', '4', '', 'Administrative', 'Clerical Services', 33646.00, 0, '2ND TRANCHE - BBM', '1'),
(197, 33, '2026-01-7611-1', 'Regular', '2026-01-01', '0000-00-00', '152', '15', '', 'Supervisory', 'Financial Services', 89749.00, 0, '2ND TRANCHE - BBM', '1'),
(198, 110, '2026-01-7611-2', 'Regular', '2026-01-01', '0000-00-00', '153', '15', '', 'Administrative', 'Financial Services', 27022.00, 0, '2ND TRANCHE - BBM', '1'),
(199, 14, '2026-01-7611-4', 'Regular', '2026-01-01', '0000-00-00', '155', '15', '', 'Administrative', 'Financial Services', 20011.00, 0, '2ND TRANCHE - BBM', '1'),
(201, 3, '2026-01-7611-5', 'Regular', '2026-01-01', '0000-00-00', '156', '15', '', 'Administrative', 'Financial Services', 17188.00, 0, '2ND TRANCHE - BBM', '1'),
(202, 82, '2026-01-7611-6', 'Regular', '2026-01-01', '0000-00-00', '157', '15', '', 'Administrative', 'Financial Services', 36187.00, 0, '2ND TRANCHE - BBM', '1'),
(203, 101, '2026-01-7611-7', 'Regular', '2026-01-01', '0000-00-00', '158', '15', '', 'Administrative', 'Financial Services', 23027.00, 0, '2ND TRANCHE - BBM', '1'),
(204, 131, '2026-01-8711-1', 'Regular', '2026-01-01', '0000-00-00', '139', '14', '', 'Supervisory', 'Financial Services', 88367.00, 0, '2ND TRANCHE - BBM', '1'),
(205, 65, '2026-01-8711-7', 'Regular', '2026-01-01', '0000-00-00', '145', '14', '', 'Administrative', 'Clerical Services', 23027.00, 0, '2ND TRANCHE - BBM', '1'),
(206, 103, '2026-01-8711-5', 'Regular', '2026-01-01', '0000-00-00', '143', '14', '', 'Administrative', 'Financial Services', 23027.00, 0, '2ND TRANCHE - BBM', '1'),
(207, 98, '2026-01-8711-8', 'Regular', '2026-01-01', '0000-00-00', '146', '14', '', 'Administrative', 'Janitorial Services', 14479.00, 0, '2ND TRANCHE - BBM', '1'),
(208, 104, '2026-01-8711-4', 'Regular', '2026-01-01', '0000-00-00', '142', '14', '', 'Administrative', 'Financial Services', 23027.00, 0, '2ND TRANCHE - BBM', '1'),
(209, 113, '2026-01-8711-3', 'Regular', '2026-01-01', '0000-00-00', '141', '14', '', 'Administrative', 'Financial Services', 23027.00, 0, '2ND TRANCHE - BBM', '1'),
(210, 105, '2026-01-8711-2', 'Regular', '2026-01-01', '0000-00-00', '140', '14', '', 'Administrative', 'Financial Services', 23027.00, 0, '2ND TRANCHE - BBM', '1'),
(212, 2, '2026-01-8711-11', 'Regular', '2026-01-01', '0000-00-00', '149', '14', '', 'Administrative', 'Clerical Services', 36187.00, 0, '2ND TRANCHE - BBM', '1'),
(213, 49, '2026-01-8711-12', 'Regular', '2026-01-01', '0000-00-00', '150', '14', '', 'Administrative', 'Clerical Services', 36187.00, 0, '2ND TRANCHE - BBM', '1'),
(214, 62, '2026-01-1021-1', 'Regular', '2026-01-01', '0000-00-00', '31', '3', '', 'Supervisory', 'Others', 91155.00, 0, '2ND TRANCHE - BBM', '1'),
(215, 130, '2026-01-1021-2', 'Regular', '2026-01-01', '0000-00-00', '32', '3', '', 'Supervisory', 'Others', 88367.00, 0, '2ND TRANCHE - BBM', '1'),
(216, 129, '2026-01-1021-3', 'Regular', '2026-01-01', '0000-00-00', '33', '3', '', 'Supervisory', 'Financial Services', 88367.00, 0, '2ND TRANCHE - BBM', '1'),
(217, 73, '2026-01-1021-4', 'Regular', '2026-01-01', '0000-00-00', '34', '3', '', 'Administrative', 'Financial Services', 89749.00, 0, '2ND TRANCHE - BBM', '1'),
(218, 121, '2026-01-1021-5', 'Regular', '2026-01-01', '0000-00-00', '35', '3', '', 'Administrative', 'Financial Services', 88367.00, 0, '2ND TRANCHE - BBM', '1'),
(219, 123, '2026-01-1021-6', 'Regular', '2026-01-01', '0000-00-00', '36', '3', '', 'Administrative', 'Financial Services', 88367.00, 0, '2ND TRANCHE - BBM', '1'),
(220, 119, '2026-01-1021-7', 'Regular', '2026-01-01', '0000-00-00', '37', '3', '', 'Administrative', 'Financial Services', 88367.00, 0, '2ND TRANCHE - BBM', '1'),
(221, 122, '2026-01-1021-8', 'Regular', '2026-01-01', '0000-00-00', '38', '3', '', 'Administrative', 'Financial Services', 88367.00, 0, '2ND TRANCHE - BBM', '1'),
(222, 118, '2026-01-1021-9', 'Regular', '2026-01-01', '0000-00-00', '39', '3', '', 'Administrative', 'Financial Services', 88367.00, 0, '2ND TRANCHE - BBM', '1'),
(223, 70, '2026-01-1016-1', 'Regular', '2025-01-01', '0000-00-00', '25', '2', '', 'Supervisory', 'Financial Services', 100554.00, 0, '2ND TRANCHE - BBM', '1'),
(224, 128, '2026-01-1016-2', 'Regular', '2026-01-01', '0000-00-00', '28', '2', '', 'Administrative', 'Others', 15150.00, 0, '2ND TRANCHE - BBM', '1'),
(229, 108, '2026-01-4411-1', 'Regular', '2026-01-01', '0000-00-00', '98', '11', '', 'Supervisory', 'Health Services', 98185.00, 0, '2ND TRANCHE - BBM', '1'),
(230, 71, '2026-01-4411-2', 'Regular', '2026-01-01', '0000-00-00', '99', '11', '', 'Administrative', 'Health Services', 47727.00, 0, '2ND TRANCHE - BBM', '1'),
(231, 52, '2026-01-4411-3', 'Regular', '2026-01-01', '0000-00-00', '100', '11', '', 'Administrative', 'Health Services', 35049.00, 0, '2ND TRANCHE - BBM', '1'),
(232, 79, '2026-01-4411-5', 'Regular', '2026-01-01', '0000-00-00', '102', '11', '', 'Administrative', 'Health Services', 30597.00, 0, '2ND TRANCHE - BBM', '1'),
(233, 89, '2026-01-4411-6', 'Regular', '2026-01-01', '0000-00-00', '103', '11', '', 'Administrative', 'Clerical Services', 32099.00, 0, '2ND TRANCHE - BBM', '1'),
(234, 60, '2026-01-4411-7', 'Regular', '2026-01-01', '0000-00-00', '104', '11', '', 'Administrative', 'Health Services', 30597.00, 0, '2ND TRANCHE - BBM', '1'),
(235, 41, '2026-01-4411-9', 'Regular', '2026-01-01', '0000-00-00', '107', '11', '', 'Administrative', 'Health Services', 30024.00, 0, '2ND TRANCHE - BBM', '1'),
(236, 27, '2026-01-4411-12', 'Regular', '2026-01-01', '0000-00-00', '109', '11', '', 'Administrative', 'Health Services', 30597.00, 0, '2ND TRANCHE - BBM', '1'),
(237, 83, '2026-01-4411-13', 'Regular', '2026-01-01', '0000-00-00', '', '11', '', 'Administrative', 'Health Services', 30308.00, 0, '2ND TRANCHE - BBM', '1'),
(238, 83, '2026-01-4411-14', 'Regular', '2026-01-01', '0000-00-00', '111', '11', '', 'Administrative', 'Health Services', 30308.00, 0, '2ND TRANCHE - BBM', '1'),
(239, 18, '2026-01-4411-4', 'Regular', '2026-01-01', '0000-00-00', '101', '11', '', 'Administrative', 'Health Services', 34733.00, 0, '2ND TRANCHE - BBM', '1'),
(240, 77, '2026-01-4411-11', 'Regular', '2026-01-01', '0000-00-00', '108', '11', '', 'Administrative', 'Health Services', 30597.00, 0, '2ND TRANCHE - BBM', '1'),
(241, 48, '2026-01-4411-24', 'Regular', '2026-01-01', '0000-00-00', '121', '11', '', 'Administrative', 'Health Services', 30024.00, 0, '2ND TRANCHE - BBM', '1'),
(242, 21, '2026-01-4411-15', 'Regular', '2026-01-01', '0000-00-00', '112', '11', '', 'Administrative', 'Health Services', 19526.00, 0, '2ND TRANCHE - BBM', '1'),
(243, 38, '2026-01-4411-18', 'Regular', '2026-01-01', '0000-00-00', '115', '11', '', 'Administrative', 'Health Services', 40604.00, 0, '2ND TRANCHE - BBM', '1'),
(244, 58, '2026-01-4411-20', 'Regular', '2026-01-01', '0000-00-00', '119', '11', '', 'Administrative', 'Health Services', 30597.00, 0, '2ND TRANCHE - BBM', '1'),
(248, 100, '2026-01-4411-17', 'Regular', '2026-01-01', '0000-00-00', '114', '11', '', 'Administrative', 'Health Services', 16833.00, 0, '2ND TRANCHE - BBM', '1'),
(249, 61, '2026-01-4411-23', 'Regular', '2026-01-01', '0000-00-00', '120', '11', '', 'Administrative', 'Health Services', 43996.00, 0, '2ND TRANCHE - BBM', '1'),
(250, 92, '2026-01-4411-21', 'Regular', '2026-01-01', '0000-00-00', '118', '11', '', 'Administrative', 'Health Services', 25586.00, 0, '2ND TRANCHE - BBM', '1'),
(251, 66, '2026-01-4411-16', 'Regular', '2026-01-01', '0000-00-00', '113', '11', '', 'Administrative', 'Health Services', 18957.00, 0, '2ND TRANCHE - BBM', '1'),
(252, 94, '2026-1011-01', 'Regular', '2026-01-01', '0000-00-00', '1', '1', '', 'Supervisory', 'Others', 128397.00, 0, '2ND TRANCHE - BBM', '1'),
(253, 124, '2026-1011-02', 'Regular', '2026-01-01', '0000-00-00', '2', '1', '', 'Administrative', 'Clerical Services', 36187.00, 0, '2ND TRANCHE - BBM', '1'),
(254, 120, '2026-02-1011-5', 'Regular', '2026-02-01', '0000-00-00', '5', '1', '', 'Administrative', 'Clerical Services', 36544.00, 0, 'Step Increment', '1'),
(256, 87, '2026-02-1051-1', 'Regular', '2026-02-01', '0000-00-00', '122', '12', '', 'Supervisory', 'Clerical Services', 89749.00, 0, 'Step Increment', '1'),
(257, 95, '2026-01-1022-03', 'Regular', '2026-01-01', '0000-00-00', '44', '4', '', 'Administrative', 'Clerical Services', 19303.00, 0, 'Promotion', '1'),
(258, 99, '2026-01-1081-04', 'Regular', '2026-01-01', '0000-00-00', '72', '7', '', 'Administrative', 'Clerical Services', 19303.00, 0, '2ND TRANCHE - BBM', '1'),
(259, 93, '2026-01-1091-13', 'Regular', '2026-01-01', '0000-00-00', '61', '5', '', 'Administrative', 'ICT Services', 27022.00, 0, 'Promotion', '1'),
(260, 112, '2026-01-1091-12', 'Regular', '2026-01-01', '0000-00-00', '60', '5', '', 'Administrative', 'Clerical Services', 19303.00, 0, '2ND TRANCHE - BBM', '1');

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
(1, 94, 8, 100.00, 128397.00, '2026-03-02', NULL, '2026-03-01 20:09:16'),
(2, 94, 1, 15407.64, 128397.00, '2026-03-02', NULL, '2026-03-01 20:09:16'),
(3, 94, 5, 200.00, 128397.00, '2026-03-02', NULL, '2026-03-01 20:09:16'),
(4, 94, 7, 2500.00, 128397.00, '2026-03-02', NULL, '2026-03-01 20:09:16'),
(5, 10, 8, 100.00, 46649.00, '2026-03-02', NULL, '2026-03-01 20:09:49'),
(6, 10, 1, 5597.88, 46649.00, '2026-03-02', NULL, '2026-03-01 20:09:49'),
(7, 10, 5, 200.00, 46649.00, '2026-03-02', NULL, '2026-03-01 20:09:49'),
(8, 10, 7, 1166.23, 46649.00, '2026-03-02', NULL, '2026-03-01 20:09:49'),
(9, 124, 8, 100.00, 36187.00, '2026-03-02', NULL, '2026-03-01 20:10:16'),
(10, 124, 1, 4342.44, 36187.00, '2026-03-02', NULL, '2026-03-01 20:10:16'),
(11, 124, 5, 200.00, 36187.00, '2026-03-02', NULL, '2026-03-01 20:10:16'),
(12, 124, 7, 904.68, 36187.00, '2026-03-02', NULL, '2026-03-01 20:10:16'),
(13, 64, 8, 100.00, 36187.00, '2026-03-02', NULL, '2026-03-01 20:35:43'),
(14, 64, 1, 4342.44, 36187.00, '2026-03-02', NULL, '2026-03-01 20:35:43'),
(15, 64, 5, 200.00, 36187.00, '2026-03-02', NULL, '2026-03-01 20:35:43'),
(16, 64, 7, 904.68, 36187.00, '2026-03-02', NULL, '2026-03-01 20:35:43'),
(17, 120, 8, 100.00, 36544.00, '2026-03-02', NULL, '2026-03-01 20:36:13'),
(18, 120, 1, 4385.28, 36544.00, '2026-03-02', NULL, '2026-03-01 20:36:13'),
(19, 120, 5, 200.00, 36544.00, '2026-03-02', NULL, '2026-03-01 20:36:13'),
(20, 120, 7, 913.60, 36544.00, '2026-03-02', NULL, '2026-03-01 20:36:13'),
(21, 67, 8, 100.00, 27022.00, '2026-03-02', NULL, '2026-03-01 20:36:46'),
(22, 67, 1, 3242.64, 27022.00, '2026-03-02', NULL, '2026-03-01 20:36:46'),
(23, 67, 5, 200.00, 27022.00, '2026-03-02', NULL, '2026-03-01 20:36:46'),
(24, 67, 7, 675.55, 27022.00, '2026-03-02', NULL, '2026-03-01 20:36:46'),
(25, 5, 8, 100.00, 19478.00, '2026-03-02', NULL, '2026-03-01 20:37:15'),
(26, 5, 1, 2337.36, 19478.00, '2026-03-02', NULL, '2026-03-01 20:37:15'),
(27, 5, 5, 200.00, 19478.00, '2026-03-02', NULL, '2026-03-01 20:37:15'),
(28, 5, 7, 486.95, 19478.00, '2026-03-02', NULL, '2026-03-01 20:37:15'),
(29, 84, 8, 100.00, 15150.00, '2026-03-02', NULL, '2026-03-01 20:37:34'),
(30, 84, 1, 1818.00, 15150.00, '2026-03-02', NULL, '2026-03-01 20:37:34'),
(31, 84, 5, 200.00, 15150.00, '2026-03-02', NULL, '2026-03-01 20:37:34'),
(32, 84, 7, 378.75, 15150.00, '2026-03-02', NULL, '2026-03-01 20:37:34'),
(33, 51, 8, 100.00, 15150.00, '2026-03-02', NULL, '2026-03-01 20:37:53'),
(34, 51, 1, 1818.00, 15150.00, '2026-03-02', NULL, '2026-03-01 20:37:53'),
(35, 51, 5, 200.00, 15150.00, '2026-03-02', NULL, '2026-03-01 20:37:53'),
(36, 51, 7, 378.75, 15150.00, '2026-03-02', NULL, '2026-03-01 20:37:53'),
(37, 78, 8, 100.00, 15150.00, '2026-03-02', NULL, '2026-03-01 20:38:18'),
(38, 78, 1, 1818.00, 15150.00, '2026-03-02', NULL, '2026-03-01 20:38:18'),
(39, 78, 5, 200.00, 15150.00, '2026-03-02', NULL, '2026-03-01 20:38:18'),
(40, 78, 7, 378.75, 15150.00, '2026-03-02', NULL, '2026-03-01 20:38:18'),
(41, 43, 8, 100.00, 15150.00, '2026-03-02', NULL, '2026-03-01 20:38:39'),
(42, 43, 1, 1818.00, 15150.00, '2026-03-02', NULL, '2026-03-01 20:38:39'),
(43, 43, 5, 200.00, 15150.00, '2026-03-02', NULL, '2026-03-01 20:38:39'),
(44, 43, 7, 378.75, 15150.00, '2026-03-02', NULL, '2026-03-01 20:38:39'),
(45, 88, 8, 100.00, 15150.00, '2026-03-02', NULL, '2026-03-01 20:39:16'),
(46, 88, 1, 1818.00, 15150.00, '2026-03-02', NULL, '2026-03-01 20:39:16'),
(47, 88, 5, 200.00, 15150.00, '2026-03-02', NULL, '2026-03-01 20:39:16'),
(48, 88, 7, 378.75, 15150.00, '2026-03-02', NULL, '2026-03-01 20:39:16'),
(49, 69, 8, 100.00, 12748.00, '2026-03-02', NULL, '2026-03-01 20:39:37'),
(50, 69, 1, 1529.76, 12748.00, '2026-03-02', NULL, '2026-03-01 20:39:37'),
(51, 69, 5, 200.00, 12748.00, '2026-03-02', NULL, '2026-03-01 20:39:37'),
(52, 69, 7, 318.70, 12748.00, '2026-03-02', NULL, '2026-03-01 20:39:37'),
(53, 111, 8, 100.00, 12655.00, '2026-03-02', NULL, '2026-03-01 20:39:56'),
(54, 111, 1, 1518.60, 12655.00, '2026-03-02', NULL, '2026-03-01 20:39:56'),
(55, 111, 5, 200.00, 12655.00, '2026-03-02', NULL, '2026-03-01 20:39:56'),
(56, 111, 7, 316.38, 12655.00, '2026-03-02', NULL, '2026-03-01 20:39:56'),
(57, 126, 1, 10604.04, 88367.00, '2026-03-02', NULL, '2026-03-01 21:26:41'),
(58, 126, 8, 100.00, 88367.00, '2026-03-02', NULL, '2026-03-01 21:26:41'),
(59, 126, 5, 200.00, 88367.00, '2026-03-02', NULL, '2026-03-01 21:26:41'),
(60, 126, 7, 2209.18, 88367.00, '2026-03-02', NULL, '2026-03-01 21:26:41'),
(61, 23, 1, 4385.28, 36544.00, '2026-03-02', NULL, '2026-03-01 21:27:14'),
(62, 23, 8, 100.00, 36544.00, '2026-03-02', NULL, '2026-03-01 21:27:14'),
(63, 23, 5, 200.00, 36544.00, '2026-03-02', NULL, '2026-03-01 21:27:14'),
(64, 23, 7, 913.60, 36544.00, '2026-03-02', NULL, '2026-03-01 21:27:14'),
(65, 80, 1, 4037.52, 33646.00, '2026-03-02', NULL, '2026-03-01 21:27:40'),
(66, 80, 8, 100.00, 33646.00, '2026-03-02', NULL, '2026-03-01 21:27:40'),
(67, 80, 5, 200.00, 33646.00, '2026-03-02', NULL, '2026-03-01 21:27:40'),
(68, 80, 7, 841.15, 33646.00, '2026-03-02', NULL, '2026-03-01 21:27:40'),
(69, 12, 1, 3242.64, 27022.00, '2026-03-02', NULL, '2026-03-01 21:28:08'),
(70, 12, 8, 100.00, 27022.00, '2026-03-02', NULL, '2026-03-01 21:28:08'),
(71, 12, 5, 200.00, 27022.00, '2026-03-02', NULL, '2026-03-01 21:28:08'),
(72, 12, 7, 675.55, 27022.00, '2026-03-02', NULL, '2026-03-01 21:28:08'),
(73, 8, 1, 2316.36, 19303.00, '2026-03-02', NULL, '2026-03-01 21:28:48'),
(74, 8, 8, 100.00, 19303.00, '2026-03-02', NULL, '2026-03-01 21:28:48'),
(75, 8, 5, 200.00, 19303.00, '2026-03-02', NULL, '2026-03-01 21:28:48'),
(76, 8, 7, 482.58, 19303.00, '2026-03-02', NULL, '2026-03-01 21:28:48'),
(77, 107, 1, 2062.56, 17188.00, '2026-03-02', NULL, '2026-03-01 21:29:19'),
(78, 107, 8, 100.00, 17188.00, '2026-03-02', NULL, '2026-03-01 21:29:19'),
(79, 107, 5, 200.00, 17188.00, '2026-03-02', NULL, '2026-03-01 21:29:19'),
(80, 107, 7, 429.70, 17188.00, '2026-03-02', NULL, '2026-03-01 21:29:19'),
(81, 115, 1, 1929.48, 16079.00, '2026-03-02', NULL, '2026-03-01 21:29:39'),
(82, 115, 8, 100.00, 16079.00, '2026-03-02', NULL, '2026-03-01 21:29:39'),
(83, 115, 5, 200.00, 16079.00, '2026-03-02', NULL, '2026-03-01 21:29:39'),
(84, 115, 7, 401.98, 16079.00, '2026-03-02', NULL, '2026-03-01 21:29:39'),
(85, 11, 1, 1831.44, 15262.00, '2026-03-02', NULL, '2026-03-01 21:30:02'),
(86, 11, 8, 100.00, 15262.00, '2026-03-02', NULL, '2026-03-01 21:30:02'),
(87, 11, 5, 200.00, 15262.00, '2026-03-02', NULL, '2026-03-01 21:30:02'),
(88, 11, 7, 381.55, 15262.00, '2026-03-02', NULL, '2026-03-01 21:30:02'),
(89, 96, 1, 1818.00, 15150.00, '2026-03-02', NULL, '2026-03-01 21:30:22'),
(90, 96, 8, 100.00, 15150.00, '2026-03-02', NULL, '2026-03-01 21:30:22'),
(91, 96, 5, 200.00, 15150.00, '2026-03-02', NULL, '2026-03-01 21:30:22'),
(92, 96, 7, 378.75, 15150.00, '2026-03-02', NULL, '2026-03-01 21:30:22'),
(93, 26, 1, 1763.52, 14696.00, '2026-03-02', NULL, '2026-03-01 21:30:51'),
(94, 26, 8, 100.00, 14696.00, '2026-03-02', NULL, '2026-03-01 21:30:51'),
(95, 26, 5, 200.00, 14696.00, '2026-03-02', NULL, '2026-03-01 21:30:51'),
(96, 26, 7, 367.40, 14696.00, '2026-03-02', NULL, '2026-03-01 21:30:51'),
(97, 106, 1, 1712.04, 14267.00, '2026-03-02', NULL, '2026-03-01 21:31:11'),
(98, 106, 8, 100.00, 14267.00, '2026-03-02', NULL, '2026-03-01 21:31:11'),
(99, 106, 5, 200.00, 14267.00, '2026-03-02', NULL, '2026-03-01 21:31:11'),
(100, 106, 7, 356.68, 14267.00, '2026-03-02', NULL, '2026-03-01 21:31:11'),
(101, 70, 1, 12066.48, 100554.00, '2026-03-02', NULL, '2026-03-01 21:50:06'),
(102, 70, 8, 100.00, 100554.00, '2026-03-02', NULL, '2026-03-01 21:50:06'),
(103, 70, 5, 200.00, 100554.00, '2026-03-02', NULL, '2026-03-01 21:50:06'),
(104, 70, 7, 2500.00, 100554.00, '2026-03-02', NULL, '2026-03-01 21:50:06'),
(105, 128, 1, 1818.00, 15150.00, '2026-03-02', NULL, '2026-03-01 21:50:33'),
(106, 128, 8, 100.00, 15150.00, '2026-03-02', NULL, '2026-03-01 21:50:33'),
(107, 128, 5, 200.00, 15150.00, '2026-03-02', NULL, '2026-03-01 21:50:33'),
(108, 128, 7, 378.75, 15150.00, '2026-03-02', NULL, '2026-03-01 21:50:33'),
(109, 62, 1, 10938.60, 91155.00, '2026-03-02', NULL, '2026-03-01 22:44:04'),
(110, 62, 8, 100.00, 91155.00, '2026-03-02', NULL, '2026-03-01 22:44:04'),
(111, 62, 5, 200.00, 91155.00, '2026-03-02', NULL, '2026-03-01 22:44:04'),
(112, 62, 7, 2278.88, 91155.00, '2026-03-02', NULL, '2026-03-01 22:44:04'),
(113, 130, 8, 100.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:44:32'),
(114, 130, 1, 10604.04, 88367.00, '2026-03-02', NULL, '2026-03-01 22:44:32'),
(115, 130, 5, 200.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:44:32'),
(116, 130, 7, 2209.18, 88367.00, '2026-03-02', NULL, '2026-03-01 22:44:32'),
(117, 129, 8, 100.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:44:56'),
(118, 129, 1, 10604.04, 88367.00, '2026-03-02', NULL, '2026-03-01 22:44:56'),
(119, 129, 5, 200.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:44:56'),
(120, 129, 7, 2209.18, 88367.00, '2026-03-02', NULL, '2026-03-01 22:44:56'),
(121, 73, 8, 100.00, 89749.00, '2026-03-02', NULL, '2026-03-01 22:45:34'),
(122, 73, 1, 10769.88, 89749.00, '2026-03-02', NULL, '2026-03-01 22:45:34'),
(123, 73, 5, 200.00, 89749.00, '2026-03-02', NULL, '2026-03-01 22:45:34'),
(124, 73, 7, 2243.73, 89749.00, '2026-03-02', NULL, '2026-03-01 22:45:34'),
(125, 121, 8, 0.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:46:09'),
(126, 121, 1, 0.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:46:09'),
(127, 121, 5, 0.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:46:09'),
(128, 121, 7, 2209.18, 88367.00, '2026-03-02', NULL, '2026-03-01 22:46:09'),
(129, 123, 8, 100.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:46:32'),
(130, 123, 1, 10604.04, 88367.00, '2026-03-02', NULL, '2026-03-01 22:46:32'),
(131, 123, 5, 200.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:46:32'),
(132, 123, 7, 2209.18, 88367.00, '2026-03-02', NULL, '2026-03-01 22:46:32'),
(133, 119, 8, 0.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:47:03'),
(134, 119, 1, 0.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:47:03'),
(135, 119, 5, 0.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:47:03'),
(136, 119, 7, 2209.18, 88367.00, '2026-03-02', NULL, '2026-03-01 22:47:03'),
(137, 122, 8, 0.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:47:27'),
(138, 122, 1, 0.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:47:27'),
(139, 122, 5, 0.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:47:27'),
(140, 122, 7, 2209.18, 88367.00, '2026-03-02', NULL, '2026-03-01 22:47:27'),
(141, 118, 8, 0.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:47:48'),
(142, 118, 1, 0.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:47:48'),
(143, 118, 5, 0.00, 88367.00, '2026-03-02', NULL, '2026-03-01 22:47:48'),
(144, 118, 7, 2209.18, 88367.00, '2026-03-02', NULL, '2026-03-01 22:47:48'),
(145, 20, 8, 100.00, 88367.00, '2026-03-02', NULL, '2026-03-02 01:06:13'),
(146, 20, 1, 10604.04, 88367.00, '2026-03-02', NULL, '2026-03-02 01:06:13'),
(147, 20, 5, 200.00, 88367.00, '2026-03-02', NULL, '2026-03-02 01:06:13'),
(148, 20, 7, 2209.18, 88367.00, '2026-03-02', NULL, '2026-03-02 01:06:13'),
(149, 63, 8, 100.00, 33646.00, '2026-03-02', NULL, '2026-03-02 01:06:32'),
(150, 63, 1, 4037.52, 33646.00, '2026-03-02', NULL, '2026-03-02 01:06:32'),
(151, 63, 5, 200.00, 33646.00, '2026-03-02', NULL, '2026-03-02 01:06:32'),
(152, 63, 7, 841.15, 33646.00, '2026-03-02', NULL, '2026-03-02 01:06:32'),
(153, 54, 8, 100.00, 30979.00, '2026-03-02', NULL, '2026-03-02 01:06:50'),
(154, 54, 1, 3717.48, 30979.00, '2026-03-02', NULL, '2026-03-02 01:06:50'),
(155, 54, 5, 200.00, 30979.00, '2026-03-02', NULL, '2026-03-02 01:06:50'),
(156, 54, 7, 774.48, 30979.00, '2026-03-02', NULL, '2026-03-02 01:06:50'),
(157, 86, 8, 100.00, 23027.00, '2026-03-02', NULL, '2026-03-02 01:07:14'),
(158, 86, 1, 2763.24, 23027.00, '2026-03-02', NULL, '2026-03-02 01:07:14'),
(159, 86, 5, 200.00, 23027.00, '2026-03-02', NULL, '2026-03-02 01:07:14'),
(160, 86, 7, 575.68, 23027.00, '2026-03-02', NULL, '2026-03-02 01:07:14'),
(161, 95, 8, 100.00, 19303.00, '2026-03-02', NULL, '2026-03-02 01:34:00'),
(162, 95, 1, 2316.36, 19303.00, '2026-03-02', NULL, '2026-03-02 01:34:00'),
(163, 95, 5, 200.00, 19303.00, '2026-03-02', NULL, '2026-03-02 01:34:00'),
(164, 95, 7, 482.58, 19303.00, '2026-03-02', NULL, '2026-03-02 01:34:00'),
(165, 17, 8, 100.00, 17061.00, '2026-03-02', NULL, '2026-03-02 01:34:21'),
(166, 17, 1, 2047.32, 17061.00, '2026-03-02', NULL, '2026-03-02 01:34:21'),
(167, 17, 5, 200.00, 17061.00, '2026-03-02', NULL, '2026-03-02 01:34:21'),
(168, 17, 7, 426.53, 17061.00, '2026-03-02', NULL, '2026-03-02 01:34:21'),
(169, 55, 8, 100.00, 98488.00, '2026-03-02', NULL, '2026-03-02 01:34:45'),
(170, 55, 1, 11818.56, 98488.00, '2026-03-02', NULL, '2026-03-02 01:34:45'),
(171, 55, 5, 200.00, 98488.00, '2026-03-02', NULL, '2026-03-02 01:34:45'),
(172, 55, 7, 2462.20, 98488.00, '2026-03-02', NULL, '2026-03-02 01:34:45'),
(173, 19, 8, 100.00, 36544.00, '2026-03-02', NULL, '2026-03-02 01:35:03'),
(174, 19, 1, 4385.28, 36544.00, '2026-03-02', NULL, '2026-03-02 01:35:03'),
(175, 19, 5, 200.00, 36544.00, '2026-03-02', NULL, '2026-03-02 01:35:03'),
(176, 19, 7, 913.60, 36544.00, '2026-03-02', NULL, '2026-03-02 01:35:03'),
(177, 32, 8, 100.00, 36544.00, '2026-03-02', NULL, '2026-03-02 01:35:29'),
(178, 32, 1, 4385.28, 36544.00, '2026-03-02', NULL, '2026-03-02 01:35:29'),
(179, 32, 5, 200.00, 36544.00, '2026-03-02', NULL, '2026-03-02 01:35:29'),
(180, 32, 7, 913.60, 36544.00, '2026-03-02', NULL, '2026-03-02 01:35:29'),
(181, 39, 8, 100.00, 20903.00, '2026-03-02', NULL, '2026-03-02 01:35:56'),
(182, 39, 1, 2508.36, 20903.00, '2026-03-02', NULL, '2026-03-02 01:35:56'),
(183, 39, 5, 200.00, 20903.00, '2026-03-02', NULL, '2026-03-02 01:35:56'),
(184, 39, 7, 522.58, 20903.00, '2026-03-02', NULL, '2026-03-02 01:35:56'),
(185, 13, 8, 100.00, 19303.00, '2026-03-02', NULL, '2026-03-02 01:36:19'),
(186, 13, 1, 2316.36, 19303.00, '2026-03-02', NULL, '2026-03-02 01:36:19'),
(187, 13, 5, 200.00, 19303.00, '2026-03-02', NULL, '2026-03-02 01:36:19'),
(188, 13, 7, 482.58, 19303.00, '2026-03-02', NULL, '2026-03-02 01:36:19'),
(189, 87, 8, 100.00, 89749.00, '2026-03-02', NULL, '2026-03-02 01:36:51'),
(190, 87, 1, 10769.88, 89749.00, '2026-03-02', NULL, '2026-03-02 01:36:51'),
(191, 87, 5, 200.00, 89749.00, '2026-03-02', NULL, '2026-03-02 01:36:51'),
(192, 87, 7, 2243.73, 89749.00, '2026-03-02', NULL, '2026-03-02 01:36:51'),
(193, 29, 8, 100.00, 33974.00, '2026-03-02', NULL, '2026-03-02 01:37:11'),
(194, 29, 1, 4076.88, 33974.00, '2026-03-02', NULL, '2026-03-02 01:37:11'),
(195, 29, 5, 200.00, 33974.00, '2026-03-02', NULL, '2026-03-02 01:37:11'),
(196, 29, 7, 849.35, 33974.00, '2026-03-02', NULL, '2026-03-02 01:37:11'),
(197, 42, 8, 100.00, 19303.00, '2026-03-02', NULL, '2026-03-02 01:37:28'),
(198, 42, 1, 2316.36, 19303.00, '2026-03-02', NULL, '2026-03-02 01:37:28'),
(199, 42, 5, 200.00, 19303.00, '2026-03-02', NULL, '2026-03-02 01:37:28'),
(200, 42, 7, 482.58, 19303.00, '2026-03-02', NULL, '2026-03-02 01:37:28'),
(201, 134, 8, 100.00, 15150.00, '2026-03-02', NULL, '2026-03-02 01:37:49'),
(202, 134, 1, 1818.00, 15150.00, '2026-03-02', NULL, '2026-03-02 01:37:49'),
(203, 134, 5, 200.00, 15150.00, '2026-03-02', NULL, '2026-03-02 01:37:49'),
(204, 134, 7, 378.75, 15150.00, '2026-03-02', NULL, '2026-03-02 01:37:49'),
(205, 6, 8, 100.00, 91155.00, '2026-03-02', NULL, '2026-03-02 01:38:12'),
(206, 6, 1, 10938.60, 91155.00, '2026-03-02', NULL, '2026-03-02 01:38:12'),
(207, 6, 5, 200.00, 91155.00, '2026-03-02', NULL, '2026-03-02 01:38:12'),
(208, 6, 7, 2278.88, 91155.00, '2026-03-02', NULL, '2026-03-02 01:38:12'),
(209, 31, 8, 100.00, 36187.00, '2026-03-02', NULL, '2026-03-02 01:38:35'),
(210, 31, 1, 4342.44, 36187.00, '2026-03-02', NULL, '2026-03-02 01:38:35'),
(211, 31, 5, 200.00, 36187.00, '2026-03-02', NULL, '2026-03-02 01:38:35'),
(212, 31, 7, 904.68, 36187.00, '2026-03-02', NULL, '2026-03-02 01:38:35'),
(213, 40, 8, 100.00, 27022.00, '2026-03-02', NULL, '2026-03-02 01:38:54'),
(214, 40, 1, 3242.64, 27022.00, '2026-03-02', NULL, '2026-03-02 01:38:54'),
(215, 40, 5, 200.00, 27022.00, '2026-03-02', NULL, '2026-03-02 01:38:54'),
(216, 40, 7, 675.55, 27022.00, '2026-03-02', NULL, '2026-03-02 01:38:54'),
(217, 56, 8, 100.00, 19478.00, '2026-03-02', NULL, '2026-03-02 01:39:12'),
(218, 56, 1, 2337.36, 19478.00, '2026-03-02', NULL, '2026-03-02 01:39:12'),
(219, 56, 5, 200.00, 19478.00, '2026-03-02', NULL, '2026-03-02 01:39:12'),
(220, 56, 7, 486.95, 19478.00, '2026-03-02', NULL, '2026-03-02 01:39:12'),
(221, 72, 8, 100.00, 18099.00, '2026-03-02', NULL, '2026-03-02 01:39:30'),
(222, 72, 1, 2171.88, 18099.00, '2026-03-02', NULL, '2026-03-02 01:39:30'),
(223, 72, 5, 200.00, 18099.00, '2026-03-02', NULL, '2026-03-02 01:39:30'),
(224, 72, 7, 452.48, 18099.00, '2026-03-02', NULL, '2026-03-02 01:39:30'),
(225, 46, 1, 10769.88, 89749.00, '2026-03-02', NULL, '2026-03-02 04:41:25'),
(226, 46, 8, 100.00, 89749.00, '2026-03-02', NULL, '2026-03-02 04:41:25'),
(227, 46, 5, 200.00, 89749.00, '2026-03-02', NULL, '2026-03-02 04:41:25'),
(228, 46, 7, 2243.73, 89749.00, '2026-03-02', NULL, '2026-03-02 04:41:25'),
(229, 74, 1, 4342.44, 36187.00, '2026-03-02', NULL, '2026-03-02 04:42:23'),
(230, 74, 8, 100.00, 36187.00, '2026-03-02', NULL, '2026-03-02 04:42:23'),
(231, 74, 5, 200.00, 36187.00, '2026-03-02', NULL, '2026-03-02 04:42:23'),
(232, 74, 7, 904.68, 36187.00, '2026-03-02', NULL, '2026-03-02 04:42:23'),
(233, 91, 1, 3242.64, 27022.00, '2026-03-02', NULL, '2026-03-02 04:42:40'),
(234, 91, 8, 100.00, 27022.00, '2026-03-02', NULL, '2026-03-02 04:42:40'),
(235, 91, 5, 200.00, 27022.00, '2026-03-02', NULL, '2026-03-02 04:42:40'),
(236, 91, 7, 675.55, 27022.00, '2026-03-02', NULL, '2026-03-02 04:42:40'),
(237, 53, 1, 2508.36, 20903.00, '2026-03-02', NULL, '2026-03-02 04:42:58'),
(238, 53, 8, 100.00, 20903.00, '2026-03-02', NULL, '2026-03-02 04:42:58'),
(239, 53, 5, 200.00, 20903.00, '2026-03-02', NULL, '2026-03-02 04:42:58'),
(240, 53, 7, 522.58, 20903.00, '2026-03-02', NULL, '2026-03-02 04:42:58'),
(241, 7, 1, 2337.36, 19478.00, '2026-03-02', NULL, '2026-03-02 04:43:18'),
(242, 7, 8, 100.00, 19478.00, '2026-03-02', NULL, '2026-03-02 04:43:18'),
(243, 7, 5, 200.00, 19478.00, '2026-03-02', NULL, '2026-03-02 04:43:18'),
(244, 7, 7, 486.95, 19478.00, '2026-03-02', NULL, '2026-03-02 04:43:18'),
(245, 99, 1, 2316.36, 19303.00, '2026-03-02', NULL, '2026-03-02 04:43:43'),
(246, 99, 8, 100.00, 19303.00, '2026-03-02', NULL, '2026-03-02 04:43:43'),
(247, 99, 5, 200.00, 19303.00, '2026-03-02', NULL, '2026-03-02 04:43:43'),
(248, 99, 7, 482.58, 19303.00, '2026-03-02', NULL, '2026-03-02 04:43:43'),
(249, 1, 1, 1818.00, 15150.00, '2026-03-02', NULL, '2026-03-02 04:44:01'),
(250, 1, 8, 100.00, 15150.00, '2026-03-02', NULL, '2026-03-02 04:44:01'),
(251, 1, 5, 200.00, 15150.00, '2026-03-02', NULL, '2026-03-02 04:44:01'),
(252, 1, 7, 378.75, 15150.00, '2026-03-02', NULL, '2026-03-02 04:44:01'),
(253, 25, 1, 10604.04, 88367.00, '2026-03-02', NULL, '2026-03-02 05:56:18'),
(254, 25, 8, 100.00, 88367.00, '2026-03-02', NULL, '2026-03-02 05:56:18'),
(255, 25, 5, 200.00, 88367.00, '2026-03-02', NULL, '2026-03-02 05:56:18'),
(256, 25, 7, 2209.18, 88367.00, '2026-03-02', NULL, '2026-03-02 05:56:18'),
(257, 57, 1, 4342.44, 36187.00, '2026-03-02', NULL, '2026-03-02 05:56:37'),
(258, 57, 8, 100.00, 36187.00, '2026-03-02', NULL, '2026-03-02 05:56:37'),
(259, 57, 5, 200.00, 36187.00, '2026-03-02', NULL, '2026-03-02 05:56:37'),
(260, 57, 7, 904.68, 36187.00, '2026-03-02', NULL, '2026-03-02 05:56:37'),
(261, 44, 1, 3998.64, 33322.00, '2026-03-02', NULL, '2026-03-02 05:56:56'),
(262, 44, 8, 100.00, 33322.00, '2026-03-02', NULL, '2026-03-02 05:56:56'),
(263, 44, 5, 200.00, 33322.00, '2026-03-02', NULL, '2026-03-02 05:56:56'),
(264, 44, 7, 833.05, 33322.00, '2026-03-02', NULL, '2026-03-02 05:56:56'),
(265, 28, 1, 3242.64, 27022.00, '2026-03-02', NULL, '2026-03-02 05:57:15'),
(266, 28, 8, 100.00, 27022.00, '2026-03-02', NULL, '2026-03-02 05:57:15'),
(267, 28, 5, 200.00, 27022.00, '2026-03-02', NULL, '2026-03-02 05:57:15'),
(268, 28, 7, 675.55, 27022.00, '2026-03-02', NULL, '2026-03-02 05:57:15'),
(269, 93, 1, 3242.64, 27022.00, '2026-03-02', NULL, '2026-03-02 05:57:36'),
(270, 93, 8, 100.00, 27022.00, '2026-03-02', NULL, '2026-03-02 05:57:36'),
(271, 93, 5, 200.00, 27022.00, '2026-03-02', NULL, '2026-03-02 05:57:36'),
(272, 93, 7, 675.55, 27022.00, '2026-03-02', NULL, '2026-03-02 05:57:36'),
(273, 47, 1, 2652.24, 22102.00, '2026-03-02', NULL, '2026-03-02 05:57:59'),
(274, 47, 8, 100.00, 22102.00, '2026-03-02', NULL, '2026-03-02 05:57:59'),
(275, 47, 5, 200.00, 22102.00, '2026-03-02', NULL, '2026-03-02 05:57:59'),
(276, 47, 7, 552.55, 22102.00, '2026-03-02', NULL, '2026-03-02 05:57:59'),
(277, 59, 1, 2528.40, 21070.00, '2026-03-02', NULL, '2026-03-02 05:58:17'),
(278, 59, 8, 100.00, 21070.00, '2026-03-02', NULL, '2026-03-02 05:58:17'),
(279, 59, 5, 200.00, 21070.00, '2026-03-02', NULL, '2026-03-02 05:58:17'),
(280, 59, 7, 526.75, 21070.00, '2026-03-02', NULL, '2026-03-02 05:58:17'),
(281, 22, 1, 2528.40, 21070.00, '2026-03-02', NULL, '2026-03-02 05:58:35'),
(282, 22, 8, 100.00, 21070.00, '2026-03-02', NULL, '2026-03-02 05:58:35'),
(283, 22, 5, 200.00, 21070.00, '2026-03-02', NULL, '2026-03-02 05:58:35'),
(284, 22, 7, 526.75, 21070.00, '2026-03-02', NULL, '2026-03-02 05:58:35'),
(285, 68, 1, 2508.36, 20903.00, '2026-03-02', NULL, '2026-03-02 05:58:51'),
(286, 68, 8, 100.00, 20903.00, '2026-03-02', NULL, '2026-03-02 05:58:51'),
(287, 68, 5, 200.00, 20903.00, '2026-03-02', NULL, '2026-03-02 05:58:51'),
(288, 68, 7, 522.58, 20903.00, '2026-03-02', NULL, '2026-03-02 05:58:51'),
(289, 24, 1, 2401.32, 20011.00, '2026-03-02', NULL, '2026-03-02 05:59:09'),
(290, 24, 8, 100.00, 20011.00, '2026-03-02', NULL, '2026-03-02 05:59:09'),
(291, 24, 5, 200.00, 20011.00, '2026-03-02', NULL, '2026-03-02 05:59:09'),
(292, 24, 7, 500.28, 20011.00, '2026-03-02', NULL, '2026-03-02 05:59:09'),
(293, 112, 1, 2316.36, 19303.00, '2026-03-02', NULL, '2026-03-02 05:59:30'),
(294, 112, 8, 100.00, 19303.00, '2026-03-02', NULL, '2026-03-02 05:59:30'),
(295, 112, 5, 200.00, 19303.00, '2026-03-02', NULL, '2026-03-02 05:59:30'),
(296, 112, 7, 482.58, 19303.00, '2026-03-02', NULL, '2026-03-02 05:59:30');

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
(1, 2, 'Life and Retirement Ins. Contributions', 'L_R', '50103010', 0.12, 1, 1, '2025-08-04 13:53:18'),
(5, 3, 'PAG-IBIG Contributions', 'HDMF', '50103020', 200.00, 0, 1, '2025-08-04 15:59:51'),
(7, 4, 'PhilHealth Contributions', 'PHIC', '50103030', 0.05, 1, 1, '2025-08-05 11:49:33'),
(8, 2, 'ECC Contributions', 'ECC', '50103040', 100.00, 0, 1, '2025-08-05 20:43:38');

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

--
-- Dumping data for table `positions_tbl`
--

INSERT INTO `positions_tbl` (`position_id`, `position_refnum`, `position_itemnum`, `position_title`, `salary_grade`, `position_type`, `dept_id`, `position_status`) VALUES
(1, '001', '1011-1', 'Municipal Mayor', 27, '0', 1, '1'),
(2, '002', '1011-2', 'Private Secretary II', 15, '0', 1, '1'),
(3, '003', '1011-3', 'Administrative Aide I', 1, '0', 1, '1'),
(4, '004', '1011-4', 'Administrative Aide I', 1, '0', 1, '1'),
(5, '005', '1011-5', 'Internal Auditor II', 15, '0', 1, '1'),
(6, '006', '1011-6', 'LDRRMO III', 18, '0', 1, '1'),
(7, '007', '1011-7', 'LDRRM Assistant', 8, '0', 1, '1'),
(8, '009', '1011-9', 'LDRRMO II', 15, '0', 1, '1'),
(9, '010', '1011-10', 'Engineer II', 16, '0', 1, '0'),
(10, '011', '1011-11', 'Administrative Aide IV (Bookbinder II)', 4, '0', 1, '1'),
(11, '012', '1011-12', 'Administrative Aide IV (Bookbinder II)', 4, '0', 1, '1'),
(12, '013', '1011-13', 'Administrative Aide IV (Bookbinder II)', 4, '0', 1, '1'),
(13, '014', '1011-14', 'Administrative Aide IV (Driver II)', 4, '0', 1, '1'),
(14, '015', '1011-15', 'Administrative Aide IV (Driver II)', 4, '0', 1, '1'),
(15, '016', '1011-16', 'Licensing Officer I', 11, '0', 1, '0'),
(16, '017', '', 'Plantilla Casual', 1, '1', 1, '1'),
(17, '018', '', 'Plantilla Casual', 1, '1', 1, '1'),
(18, '008', '1011-8', 'LDRRMO I', 11, '0', 1, '1'),
(19, '019', '', 'Plantilla Casual', 1, '1', 1, '1'),
(20, '020', '', 'Plantilla Casual', 1, '1', 1, '1'),
(21, '021', '', 'Plantilla Casual', 1, '1', 1, '0'),
(22, '022', '', 'Plantilla Casual', 1, '1', 1, '0'),
(23, '023', '', 'Plantilla Casual', 1, '1', 1, '0'),
(24, '024', '', 'Plantilla Casual', 1, '1', 1, '0'),
(25, '025', '1016-1', 'Municipal Vice Mayor', 25, '0', 2, '1'),
(26, '026', '1016-2', 'Administrative Aide IV (Bookbinder II)', 4, '0', 2, '0'),
(27, '027', '1016-3', 'Administrative Aide IV (Bookbinder II)', 4, '0', 2, '0'),
(28, '028', '1016-4', 'Administrative Aide IV (Driver II)', 4, '0', 2, '1'),
(29, '029', '', 'Plantilla Casual', 1, '1', 2, '1'),
(30, '030', '', 'Plantilla Casual', 1, '1', 2, '0'),
(31, '031', '1021-1', 'SB Member', 24, '0', 3, '1'),
(32, '032', '1021-2', 'SB Member', 24, '0', 3, '1'),
(33, '033', '1021-3', 'SB Member', 24, '0', 3, '1'),
(34, '034', '1021-4', 'SB Member', 24, '0', 3, '1'),
(35, '035', '1021-5', 'SB Member', 24, '0', 3, '1'),
(36, '036', '1021-6', 'SB Member', 24, '0', 3, '1'),
(37, '037', '1021-7', 'SB Member', 24, '0', 3, '1'),
(38, '038', '1021-8', 'SB Member', 24, '0', 3, '1'),
(39, '039', '1021-9', 'SB Member (ABC Fed. President)', 24, '0', 3, '1'),
(40, '040', '1021-10', 'SB Member (SK Fed. President)', 24, '0', 3, '1'),
(41, '041', '1021-11', 'IPMR', 24, '0', 3, '0'),
(42, '042', '1022-1', 'Secretary to the SB', 24, '0', 4, '1'),
(43, '043', '1022-2', 'Administrative Officer I (Records Officer I)', 10, '0', 4, '1'),
(44, '044', '1022-3', 'Administrative Asst. II (Clerk IV)', 8, '0', 4, '1'),
(45, '045', '1022-4', 'Administrative Aide VI (Clerk III)', 6, '0', 4, '1'),
(46, '046', '1022-5', 'Administrative Aide III (Clerk I)', 3, '0', 4, '0'),
(47, '047', '1022-6', 'Local Legislative Officer II', 13, '0', 4, '1'),
(48, '048', '1022-7', 'Administrative Officer III', 14, '0', 4, '1'),
(49, '049', '1091-1', 'Municipal Treasurer', 24, '0', 5, '1'),
(50, '050', '1091-2', 'Asst. Municipal Treasurer', 22, '0', 5, '0'),
(51, '051', '1091-3', 'LRCO II', 15, '0', 5, '1'),
(52, '052', '1091-4', 'LRCO I', 11, '0', 5, '1'),
(53, '053', '1091-5', 'Revenue Collection Clerk III', 9, '0', 5, '1'),
(54, '054', '1091-6', 'Revenue Collection Clerk III', 9, '0', 5, '1'),
(55, '055', '1091-7', 'Revenue Collection Clerk III', 9, '0', 5, '1'),
(56, '056', '1091-8', 'Revenue Collection Clerk III', 9, '0', 5, '1'),
(57, '057', '1091-9', 'Revenue Collection Clerk III', 9, '0', 5, '1'),
(58, '058', '1091-10', 'Administrative Officer III (Cashier II)', 14, '0', 5, '1'),
(59, '059', '1091-11', 'Administrative Assistant II (Clerk IV)', 8, '0', 5, '1'),
(60, '060', '1091-12', 'Administrative Asst. II (Disb. Officer II)', 8, '0', 5, '1'),
(61, '061', '1091-13', 'Computer Programmer I', 11, '0', 5, '1'),
(62, '062', '1101-1', 'Municipal Assessor', 24, '0', 6, '1'),
(63, '063', '1101-2', 'LAOO I', 11, '0', 6, '1'),
(64, '064', '1101-3', 'Assessment Clerk III', 9, '0', 6, '1'),
(65, '065', '1101-4', 'Assessment Clerk II', 6, '0', 6, '2'),
(66, '066', '1101-5', 'Assessment Clerk I', 4, '0', 6, '1'),
(67, '067', '1101-6', 'LAOO II', 15, '0', 6, '0'),
(68, '068', '1101-7', 'Tax Mapper I', 11, '0', 6, '1'),
(69, '069', '', 'Plantilla Casual', 1, '1', 6, '0'),
(70, '071', '1081-2', 'Administrative Asst. III (Sernior Bookkeeper)', 9, '0', 7, '1'),
(71, '072', '1081-3', 'Administrative Asst. II (Bookkeeper I)', 8, '0', 7, '1'),
(72, '073', '1081-4', 'Administrative Asst. II (Acct. Clerk III)', 8, '0', 7, '1'),
(73, '074', '1081-5', 'Administrative Aide IV (Acctg. Clerk I)', 4, '0', 7, '1'),
(74, '075', '1081-6', 'Administrative Officer II (Mgt. and Audit Analyst I)', 11, '0', 7, '1'),
(75, '076', '1081-7', 'Administrative Officer IV', 15, '0', 7, '1'),
(76, '077', '', 'Plantilla Casual', 1, '1', 7, '0'),
(77, '078', '', 'Plantilla Casual', 1, '1', 7, '0'),
(78, '070', '1081-1', 'Municipal Accountant', 24, '0', 7, '1'),
(81, '079', '1071-1', 'Municipal Budget Officer', 24, '0', 8, '1'),
(82, '080', '1071-2', 'Administrative Officer II (Budget Officer I)', 11, '0', 8, '1'),
(84, '081', '1071-3', 'Administrative Asst. II', 8, '0', 8, '1'),
(85, '082', '1071-4', 'Administrative Asst. I (Bookbinder III)', 7, '0', 8, '1'),
(86, '083', '1071-5', 'Administrative Officer IV', 15, '0', 8, '1'),
(87, '084', '1041-1', 'MPDC', 24, '0', 9, '1'),
(88, '085', '1041-2', 'Economic Researcher', 9, '0', 9, '1'),
(89, '086', '1041-3', 'Draftsman II', 8, '0', 9, '1'),
(90, '087', '1041-4', 'Project Dev\'t Officer II', 15, '0', 9, '1'),
(91, '088', '1041-5', 'Environment Mgt. Specialist II', 15, '0', 9, '1'),
(92, '089', '8751-1', 'Municipal Engineer', 24, '0', 10, '1'),
(93, '090', '8751-2', 'Engineer I', 12, '0', 10, '1'),
(94, '091', '8751-3', 'Engineering Aide', 4, '0', 10, '1'),
(95, '092', '8751-4', 'Administrative Aide V (Carpenter II)', 5, '0', 10, '1'),
(96, '093', '8751-5', 'Engineer II', 16, '0', 10, '1'),
(97, '094', '', 'Plantilla Casual', 1, '0', 10, '0'),
(98, '095', '4411-1', 'Municipal Health Officer', 24, '0', 11, '1'),
(99, '096', '4411-2', 'Nurse III', 17, '0', 11, '1'),
(100, '097', '4411-3', 'Midwife III', 13, '0', 11, '1'),
(101, '098', '4411-4', 'Midwife III', 13, '0', 11, '1'),
(102, '099', '4411-5', 'Midwife II', 11, '0', 11, '1'),
(103, '100', '4411-6', 'Midwife II', 11, '0', 11, '1'),
(104, '101', '4411-7', 'Midwife II', 11, '0', 11, '1'),
(105, '102', '4411-8', 'Elma P. Duran', 11, '0', 11, '0'),
(106, '103', '4411-9', 'Midwife II', 11, '0', 11, '0'),
(107, '104', '4411-10', 'Midwife II', 11, '0', 11, '1'),
(108, '105', '4411-11', 'Midwife II', 11, '0', 11, '1'),
(109, '106', '4411-12', 'Midwife II', 11, '0', 11, '1'),
(110, '107', '4411-13', 'Midwife II', 11, '0', 11, '2'),
(111, '108', '4411-14', 'Midwife II', 11, '0', 11, '1'),
(112, '109', '4411-15', 'Sanitation Inspector I', 6, '0', 11, '1'),
(113, '110', '4411-16', 'Sanitation Inspector I', 6, '0', 11, '1'),
(114, '111', '4411-17', 'Administrative Aide IV (Clerk II)', 4, '0', 11, '1'),
(115, '112', '4411-18', 'Nurse I', 15, '0', 11, '1'),
(116, '113', '4411-19', 'Midwife I', 6, '0', 11, '2'),
(117, '114', '4411-20', 'Administrative Aide III (Driver I)', 3, '0', 11, '0'),
(118, '115', '4411-21', 'Laboratory Inspector II', 10, '0', 11, '1'),
(119, '116', '4411-22', 'Medical Technologist I', 11, '0', 11, '1'),
(120, '117', '4411-23', 'Nurse II', 16, '0', 11, '1'),
(121, '118', '4411-24', 'Pharmacist I', 11, '0', 11, '1'),
(122, '119', '1051-1', 'Municipal Civil Registrar', 24, '0', 12, '1'),
(123, '120', '1051-2', 'Registration Officer II', 14, '0', 12, '1'),
(124, '121', '1051-3', 'Administrative Aide IV (Clerk II)', 4, '0', 12, '1'),
(125, '122', '1051-4', 'Administrative Assistant II', 8, '0', 12, '1'),
(126, '123', '1031-1', 'Municipal Government Department Head I (Municipal Administrator)', 24, '0', 13, '1'),
(127, '124', '1031-2', 'Administrative Officer II (HRMO I)', 11, '0', 13, '1'),
(128, '125', '1031-3', 'Administrative Assistant II (HRMA I)', 8, '0', 13, '1'),
(129, '126', '1031-4', 'Administrative Aide IV (Clerk II)', 4, '0', 13, '1'),
(130, '127', '1031-5', 'Administrative Officer III (Supply Officer II)', 14, '0', 13, '1'),
(131, '128', '1031-6', 'Administrative Aide IV (Clerk II)', 4, '0', 13, '1'),
(132, '129', '1031-7', 'Administrative Aide VI (Utility Foreman)', 6, '0', 13, '1'),
(133, '130', '1031-8', 'Administrative Aide VI (Utility Foreman)', 6, '0', 13, '1'),
(134, '131', '1031-9', 'Administrative Aide III (Driver I)', 3, '0', 13, '1'),
(135, '132', '1031-10', 'Administrative Aide III (Driver I)', 3, '0', 13, '1'),
(136, '133', '1031-11', 'Administrative Aide VI (Com. Equipt. Operator I)', 6, '0', 13, '2'),
(137, '134', '1031-12', 'Com. Affairs Asst. I', 5, '0', 13, '1'),
(138, '135', '1031-17', 'Administrative Officer IV (HRMO II)', 15, '0', 13, '1'),
(139, '136', '8711-1', 'Municipal Agriculturist', 24, '0', 14, '1'),
(140, '137', '8711-2', 'Agricultural Technologist', 10, '0', 14, '1'),
(141, '138', '8711-3', 'Agricultural Technologist', 10, '0', 14, '1'),
(142, '139', '8711-4', 'Agricultural Technologist', 10, '0', 14, '1'),
(143, '140', '8711-5', 'Agricultural Technologist', 10, '0', 14, '1'),
(144, '141', '8711-6', 'Agricultural Technologist', 10, '0', 14, '0'),
(145, '142', '8711-7', 'Agricultural Technologist', 10, '0', 14, '1'),
(146, '143', '8711-8', 'Administrative Aide III (Utility Worker II)', 3, '0', 14, '1'),
(147, '144', '8711-9', 'Veterinarian I', 13, '0', 14, '2'),
(148, '145', '8711-10', 'Veterinarian II', 16, '0', 14, '1'),
(149, '146', '8711-11', 'Agriculturist II', 15, '0', 14, '1'),
(150, '147', '8711-12', 'Agriculturist II', 15, '0', 14, '1'),
(151, '148', '8711-13', 'Administrative Aide IV (BookBinder II)', 4, '0', 14, '0'),
(152, '149', '7611-1', 'MSWDO', 24, '0', 15, '1'),
(153, '150', '7611-2', 'Social Welfare Officer I', 11, '0', 15, '1'),
(154, '151', '7611-3', 'Youth Development Assistant II', 8, '0', 15, '1'),
(155, '152', '7611-4', 'Youth Development Assistant II', 8, '0', 15, '1'),
(156, '153', '7611-5', 'Day Care Worker I', 6, '0', 15, '1'),
(157, '154', '7611-6', 'Social Welfare Officer II', 15, '0', 15, '1'),
(158, '155', '7611-7', 'Youth Development Officer I', 10, '0', 15, '1'),
(159, '156', '8811-13', 'Market Inspector', 6, '0', 16, '1'),
(160, '157', '8811-14', 'Administrative Aide I (Utility Worker I)', 1, '0', 16, '1'),
(161, '158', '8811-15', 'Administrative Aide VI (Utility Foreman)', 6, '0', 16, '0'),
(162, '159', '8811-16', 'Administrative Aide VI (Utility Foreman)', 6, '0', 16, '1');

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
  MODIFY `config_deduction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

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
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  MODIFY `employee_deduction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `employee_deductions_components`
--
ALTER TABLE `employee_deductions_components`
  MODIFY `deduction_component_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=528;

--
-- AUTO_INCREMENT for table `employee_earnings`
--
ALTER TABLE `employee_earnings`
  MODIFY `employee_earning_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `employee_earnings_components`
--
ALTER TABLE `employee_earnings_components`
  MODIFY `earning_component_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=335;

--
-- AUTO_INCREMENT for table `employee_employments_tbl`
--
ALTER TABLE `employee_employments_tbl`
  MODIFY `employment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=261;

--
-- AUTO_INCREMENT for table `employee_govshares`
--
ALTER TABLE `employee_govshares`
  MODIFY `employee_govshare_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=297;

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
  MODIFY `govshare_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

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

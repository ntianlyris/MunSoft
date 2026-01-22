-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 12, 2025 at 09:59 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.4.24

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins_tbl`
--

INSERT INTO `admins_tbl` (`adminID`, `userID`, `employee_id`, `status`) VALUES
(2, 10, 3, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `departments_tbl`
--

CREATE TABLE `departments_tbl` (
  `dept_id` int(11) NOT NULL,
  `dept_code` varchar(20) NOT NULL,
  `dept_title` varchar(50) NOT NULL,
  `dept_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `employee_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `employees_tbl`
--

INSERT INTO `employees_tbl` (`employee_id`, `userID`, `employee_id_num`, `firstname`, `middlename`, `lastname`, `extension`, `birthdate`, `gender`, `civil_status`, `address`, `prof_expertise`, `hire_date`, `employment_type`, `employee_status`) VALUES
(1, '8', '1111', 'Vice', '', 'Ganda', '', '1991-01-01', 'Male', 'Married', 'Its Showtime', 'TV Host', '0000-00-00', '', ''),
(2, '9', '2222', 'Jhong', '', 'Navarro', '', '1901-01-01', 'Male', 'Single', 'Its Showtime', 'Sample Kingkoy', '0000-00-00', '', ''),
(3, '7', '3333', 'Christian Lyris', 'Calunsag', 'Tagsip', '', '1991-08-28', 'Male', 'Married', 'Pob. South, Polanco, Z.N.', 'Computer Scientist', '2024-12-03', '', 'Active'),
(5, '', '4444', 'CoCo', '', 'Martin', '', '1991-01-01', 'Male', 'Single', 'Quiapo', 'Drug Lord', '2024-06-01', '', 'Active'),
(6, '', '5555', 'Syrone', '', 'Tubera', '', '1989-08-21', 'Male', 'Married', 'Dipolog City', 'IT Expert', '2023-02-01', '', 'Active');

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
  `employment_end` date NOT NULL,
  `position_id` varchar(11) NOT NULL,
  `dept_assigned` varchar(11) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `work_nature` varchar(100) NOT NULL,
  `work_specifics` varchar(100) NOT NULL,
  `rate` decimal(20,2) NOT NULL,
  `empSalaryID` int(11) NOT NULL,
  `employment_particulars` varchar(50) NOT NULL,
  `employment_status` varchar(11) NOT NULL COMMENT 'active = current employment inactive = previous employment'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `permissions_tbl`
--

CREATE TABLE `permissions_tbl` (
  `perm_id` int(11) NOT NULL,
  `perm_desc` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `positions_tbl`
--

INSERT INTO `positions_tbl` (`position_id`, `position_refnum`, `position_itemnum`, `position_title`, `salary_grade`, `position_type`, `dept_id`, `position_status`) VALUES
(4, '001', '', 'Administrative Aide I', 1, '1', 1, '2'),
(5, '002', '1', 'Administrative Assistant III', 9, '0', 7, '0'),
(6, '003', '2', 'Administrative Aide IV', 4, '0', 1, '0'),
(7, '004', '3', 'Administrative Offier IV', 15, '0', 1, '0');

-- --------------------------------------------------------

--
-- Table structure for table `roles_tbl`
--

CREATE TABLE `roles_tbl` (
  `roleID` int(1) NOT NULL,
  `roleName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- Table structure for table `users_tbl`
--

CREATE TABLE `users_tbl` (
  `userID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `mobile` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_tbl`
--

INSERT INTO `users_tbl` (`userID`, `username`, `password`, `email`, `mobile`) VALUES
(1, 'admin', '$2y$10$rZn8vkQCyfNTEGFUPKJiOuryLoy3NtmZLwCVO6sLo5.S7FEDULHDm', '', '09123456789'),
(7, 'ntian', '$2y$10$D.wysPTZDpuoTwgBCkHpYOrf31M5uAvryFJoCciXdnrUfPlfQFfdm', '', '09123456789'),
(8, 'viceral', '$2y$10$tJKKX9ZScv2znhGLrrf70.J5MA7RHtNdTfocFnJJgcnkRg90Nk99a', '', '09123456789'),
(9, 'jongskie', '$2y$10$TsF.dqglID897d8CP67iHuiTbHX8qYZ6qgnAQwAbErG8La9tHn5eS', '', '09123456789'),
(10, 'PayMaster', '$2y$10$5iofkqvN7W/.XTzUMENeH.FCpTuGvM60ZGsLwibXe6HZEeayHbXau', '', '09123456789');

-- --------------------------------------------------------

--
-- Table structure for table `user_role_tbl`
--

CREATE TABLE `user_role_tbl` (
  `user_role_id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `roleID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_role_tbl`
--

INSERT INTO `user_role_tbl` (`user_role_id`, `userID`, `roleID`) VALUES
(1, 1, 1),
(7, 7, 3),
(8, 8, 3),
(9, 9, 3),
(10, 10, 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins_tbl`
--
ALTER TABLE `admins_tbl`
  ADD PRIMARY KEY (`adminID`);

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
-- Indexes for table `employee_employments_tbl`
--
ALTER TABLE `employee_employments_tbl`
  ADD PRIMARY KEY (`employment_id`);

--
-- Indexes for table `empsalaries_tbl`
--
ALTER TABLE `empsalaries_tbl`
  ADD PRIMARY KEY (`empSalaryID`);

--
-- Indexes for table `positions_tbl`
--
ALTER TABLE `positions_tbl`
  ADD PRIMARY KEY (`position_id`);

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
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `departments_tbl`
--
ALTER TABLE `departments_tbl`
  MODIFY `dept_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `employees_tbl`
--
ALTER TABLE `employees_tbl`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `employee_employments_tbl`
--
ALTER TABLE `employee_employments_tbl`
  MODIFY `employment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `empsalaries_tbl`
--
ALTER TABLE `empsalaries_tbl`
  MODIFY `empSalaryID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `positions_tbl`
--
ALTER TABLE `positions_tbl`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- AUTO_INCREMENT for table `users_tbl`
--
ALTER TABLE `users_tbl`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_role_tbl`
--
ALTER TABLE `user_role_tbl`
  MODIFY `user_role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

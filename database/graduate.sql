-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 07, 2025 at 04:30 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `graduate`
--

-- --------------------------------------------------------

--
-- Table structure for table `allocate_applicants`
--

CREATE TABLE `allocate_applicants` (
  `allocate_applicant_id` int(11) NOT NULL,
  `applications_uuid` varchar(40) NOT NULL,
  `department_uuid` varchar(40) NOT NULL,
  `reporting_date` date NOT NULL,
  `reported_date` date DEFAULT NULL,
  `allocation_status` enum('allocated','reported','not_reported','withdrawn') NOT NULL DEFAULT 'allocated',
  `applicant_uuid` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `allocate_applicants`
--

INSERT INTO `allocate_applicants` (`allocate_applicant_id`, `applications_uuid`, `department_uuid`, `reporting_date`, `reported_date`, `allocation_status`, `applicant_uuid`) VALUES
(1, '9c0b39a692119907f0069dcefc1bd472', '327600a2-4a18-43e9-acf4-587a53e963e8', '2024-11-04', NULL, 'allocated', '550e8400-e29b-41d4-a716-446655440045'),
(2, '767d522c-6a94-44e3-9507-1c888b0b40b7', '327600a2-4a18-43e9-acf4-587a53e963e8', '2024-11-04', '2024-11-07', 'reported', '7d1bded3-7ba3-4480-8d27-3e27bc65b76a'),
(3, '767d522c-6a94-44e3-9507-1c888b0b40b7', '327600a2-4a18-43e9-acf4-587a53e963e8', '2024-11-04', NULL, 'allocated', '7d1bded3-7ba3-4480-8d27-3e27bc65b76a');

-- --------------------------------------------------------

--
-- Table structure for table `applicant_attachements`
--

CREATE TABLE `applicant_attachements` (
  `attachment_id` int(11) NOT NULL,
  `uuid` int(11) NOT NULL,
  `applicant_uuid` char(36) NOT NULL,
  `declaration` varchar(30) NOT NULL,
  `file_name` varchar(100) NOT NULL,
  `file_path` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicant_attachements`
--

INSERT INTO `applicant_attachements` (`attachment_id`, `uuid`, `applicant_uuid`, `declaration`, `file_name`, `file_path`) VALUES
(1, 60, '550e8400-e29b-41d4-a716-446655440045', '', 'ID Copy', 'E:\\xamp\\htdocs\\graduate_interface\\writable\\uploads/1726830434_bbca83cc60971a51b781.pdf'),
(7, 0, '7d1bded3-7ba3-4480-8d27-3e27bc65b76a', 'National ID', 'Tobias_Caleb_Kalagho.pdf', 'uploads/Tobias_Caleb_Kalagho.pdf'),
(8, 0, '7d1bded3-7ba3-4480-8d27-3e27bc65b76a', 'Degree Certificate', 'Tobias_Caleb_Kalagho.pdf', 'uploads/Tobias_Caleb_Kalagho.pdf'),
(9, 0, 'cb8d3376-7ad3-4e9e-acf9-1c8321040661', 'National ID', 'ppda_certificate_2022.pdf', 'uploads/ppda_certificate_2022.pdf'),
(10, 0, 'cb8d3376-7ad3-4e9e-acf9-1c8321040661', 'Degree Certificate', 'inventory-batches-all-2025-08-11 (1) (2).pdf', 'uploads/inventory-batches-all-2025-08-11 (1) (2).pdf'),
(11, 0, '841a6036-6fde-41c5-837b-6af2791cb560', 'National ID', 'Precious__Phiri_id.pdf', 'uploads/Precious__Phiri_id.pdf'),
(12, 0, '841a6036-6fde-41c5-837b-6af2791cb560', 'Degree Certificate', 'Precious__Phiri_id - certificate.pdf', 'uploads/Precious__Phiri_id - certificate.pdf'),
(13, 1, 'aef0c35e-c38d-4598-9c24-ceb8178a3640', 'National ID', 'Precious__Phiri_id.pdf', 'uploads/Precious__Phiri_id.pdf'),
(14, 1, 'aef0c35e-c38d-4598-9c24-ceb8178a3640', 'Degree Certificate', 'Precious__Phiri_id - certificate.pdf', 'uploads/Precious__Phiri_id - certificate.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL,
  `uuid` char(36) NOT NULL,
  `cohort_uuid` varchar(255) NOT NULL,
  `applicant_uuid` varchar(255) NOT NULL,
  `signature` varchar(100) NOT NULL,
  `status` varchar(255) NOT NULL,
  `applied_date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`application_id`, `uuid`, `cohort_uuid`, `applicant_uuid`, `signature`, `status`, `applied_date`) VALUES
(1, '9c0b39a692119907f0069dcefc1bd472', 'ff30096d-7657-4da8-92c4-915dbb2e011c', '550e8400-e29b-41d4-a716-446655440045', '', 'allocated', '2024-09-20'),
(2, '767d522c-6a94-44e3-9507-1c888b0b40b7', 'ff30096d-7657-4da8-92c4-915dbb2e011c', '7d1bded3-7ba3-4480-8d27-3e27bc65b76a', '7d1bded3-7ba3-4480-8d27-3e27bc65b76a', 'allocated', '2024-10-04'),
(3, '6b8030ef-7753-4e6d-867e-d6eca14bc561', 'e69ddfeb-b568-470b-809f-bc2870552dc4', 'cb8d3376-7ad3-4e9e-acf9-1c8321040661', 'cb8d3376-7ad3-4e9e-acf9-1c8321040661', 'submitted', '2025-08-14'),
(4, '2572070e-f785-4d15-a3cc-0359ead17a5b', 'e69ddfeb-b568-470b-809f-bc2870552dc4', '841a6036-6fde-41c5-837b-6af2791cb560', '841a6036-6fde-41c5-837b-6af2791cb560', 'submitted', '2025-09-07'),
(5, 'd573e3c9-83ed-4929-a5fe-a119ad090d25', 'e69ddfeb-b568-470b-809f-bc2870552dc4', 'aef0c35e-c38d-4598-9c24-ceb8178a3640', 'aef0c35e-c38d-4598-9c24-ceb8178a3640', 'submitted', '2025-09-07');

-- --------------------------------------------------------

--
-- Table structure for table `bank_details`
--

CREATE TABLE `bank_details` (
  `bank_detail_id` int(11) NOT NULL,
  `uuid` char(33) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `bank_branch` varchar(100) NOT NULL,
  `account_name` varchar(100) NOT NULL,
  `account_number` varchar(100) NOT NULL,
  `applicant_uuid` char(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bank_details`
--

INSERT INTO `bank_details` (`bank_detail_id`, `uuid`, `bank_name`, `bank_branch`, `account_name`, `account_number`, `applicant_uuid`) VALUES
(1, '18a94ae01e7f931fe92276170758df12', 'nb', 'LL', 'great', '09880000', ''),
(9, '918946ed-0e76-4d25-8c87-24b2d28e4', 'NB', 'Lilongwe', 'Great', '89077', '7d1bded3-7ba3-4480-8d27-3e27bc65b76a'),
(10, 'c6dc9cda-e2e0-40d1-a008-3ec942437', 'Khumbo Banda', 'Lilongwe', 'cash account ', '10054567', 'cb8d3376-7ad3-4e9e-acf9-1c8321040661'),
(11, '4cddae57-7000-4154-84a8-477253cfc', 'Nationa Bank ', 'Gateway mall', 'emmie nkhata', '100567789', '841a6036-6fde-41c5-837b-6af2791cb560'),
(12, '3110b151-65d8-4936-9bf5-f8d7c05a6', 'Nationa Bank', 'll', 'mercy', 'Banda', 'aef0c35e-c38d-4598-9c24-ceb8178a3640');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('a17961fa74e9275d529f489537f179c05d50c2f3', 'i:1;', 1727836680),
('a17961fa74e9275d529f489537f179c05d50c2f3:timer', 'i:1727836679;', 1727836679);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cohort_programs`
--

CREATE TABLE `cohort_programs` (
  `uuid` char(36) NOT NULL,
  `references` varchar(255) DEFAULT NULL,
  `name` text NOT NULL,
  `descriptions` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `terms_conditions` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cohort_programs`
--

INSERT INTO `cohort_programs` (`uuid`, `references`, `name`, `descriptions`, `start_date`, `end_date`, `terms_conditions`, `status`, `created_at`, `updated_at`) VALUES
('7a76644a-fa85-4c83-8fc7-207a5d4d052a', '20241005DQYdXY', 'Cohort All', 'Great', '2024-10-10', '2024-10-25', 'grarata', 'inactive', '2024-10-05 14:26:34', '2024-10-05 14:26:34'),
('e69ddfeb-b568-470b-809f-bc2870552dc4', '202410057FkFqz', 'Advanced', 'great', '2024-10-10', '2024-10-16', 'great', 'active', '2024-10-05 14:31:41', '2024-10-05 14:31:41'),
('ff30096d-7657-4da8-92c4-915dbb2e011c', '20240919WCgqiR', '2024 -2025 graduate internship programs', 'great ', '2024-09-19', '2025-11-27', 'great work', 'inactive', '2024-09-19 16:26:46', '2024-09-19 16:30:21');

-- --------------------------------------------------------

--
-- Table structure for table `cohort_program_assignments`
--

CREATE TABLE `cohort_program_assignments` (
  `uuid` char(36) NOT NULL,
  `cohort_program_uuid` char(36) NOT NULL,
  `department_uuid` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cohort_program_assignments`
--

INSERT INTO `cohort_program_assignments` (`uuid`, `cohort_program_uuid`, `department_uuid`, `created_at`, `updated_at`) VALUES
('75c8f42a-a49d-4b47-973f-19b745d78121', 'ff30096d-7657-4da8-92c4-915dbb2e011c', '9dba7d65-4b32-4b66-b4c9-ed8453a9584f', '2024-09-20 03:07:18', '2024-09-20 03:07:18'),
('e8d44db9-1bbb-4c25-bbc2-e43f5bc70803', 'e69ddfeb-b568-470b-809f-bc2870552dc4', '327600a2-4a18-43e9-acf4-587a53e963e8', '2024-10-06 18:36:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cohort_program_assignment_details`
--

CREATE TABLE `cohort_program_assignment_details` (
  `uuid` char(36) NOT NULL,
  `assignment_uuid` char(36) NOT NULL,
  `general_uuid` char(36) NOT NULL,
  `major_uuid` char(36) NOT NULL,
  `total_recruits` int(11) NOT NULL,
  `gender_preference` enum('yes','no') NOT NULL DEFAULT 'no',
  `total_male` int(11) DEFAULT NULL,
  `total_female` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cohort_program_assignment_details`
--

INSERT INTO `cohort_program_assignment_details` (`uuid`, `assignment_uuid`, `general_uuid`, `major_uuid`, `total_recruits`, `gender_preference`, `total_male`, `total_female`, `created_at`, `updated_at`) VALUES
('024edebc-d01a-4510-b06b-0199367419c6', 'e8d44db9-1bbb-4c25-bbc2-e43f5bc70803', '75512195-e3fe-4702-9f69-6f0e234b254a', 'c85e4cb5-4496-42e4-bbff-71f237057bb5', 4, 'yes', 3, 1, '2024-10-06 18:36:10', '2024-10-06 18:36:10'),
('2a039080-2c57-4421-ad86-1b56f72ba733', '75c8f42a-a49d-4b47-973f-19b745d78121', '75512195-e3fe-4702-9f69-6f0e234b254a', '6ce084fe-f705-4aa2-80c4-bcb75d394552', 5, 'yes', 3, NULL, '2024-09-20 03:07:18', '2024-09-20 03:07:18');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `uuid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `post_address` text DEFAULT NULL,
  `physical_address` text DEFAULT NULL,
  `contacts` varchar(255) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `status` enum('government','other') NOT NULL DEFAULT 'government',
  `district_id` bigint(20) UNSIGNED NOT NULL,
  `ministry_uuid` char(36) DEFAULT NULL,
  `da_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`uuid`, `name`, `description`, `post_address`, `physical_address`, `contacts`, `email_address`, `status`, `district_id`, `ministry_uuid`, `da_uuid`, `created_at`, `updated_at`) VALUES
('327600a2-4a18-43e9-acf4-587a53e963e8', 'Mbvumbwe', 'research', 'Box 34', 'lilo', '', 'm@gmail.com', '', 10, '6e645678-6a51-4323-b2a7-98a4767cf000', NULL, '2024-10-06 14:26:36', '2024-10-06 14:26:36'),
('422ab470-12e3-4e82-aa1b-d3840ccf448d', 'Lilongwe water board lilongwe', 'Lilongwe water board', 'Lilongwe water board', '', '', 'amiduellan@gmail.com', '', 10, NULL, '8f110dde-0dee-4dd0-b474-de62e2684348', '2025-09-01 02:13:37', '2025-09-01 02:13:37'),
('592d65f7-1199-4546-a03f-3692d680db29', 'Secondary Education', 'great', 'box 34', 'Lilo', '7888888', 'l@gmail.com', 'government', 10, '552a213e-4971-4d71-b18f-882719b3faca', NULL, '2024-09-23 06:01:12', '2024-09-23 06:01:12'),
('9dba7d65-4b32-4b66-b4c9-ed8453a9584f', 'Higher Eductiona-Blantyre', 'Higher Eductiona-Blantyre', 'Higher Eductiona-Blantyre', 'Higher Eductiona-Blantyre', '0997175667,08812456', 'higherbt@gmail.com', 'other', 16, NULL, 'b52f990b-ded2-4a51-82ef-4da4b951f70f', '2024-09-19 19:11:06', '2024-09-19 19:11:06');

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `country_id` bigint(20) UNSIGNED DEFAULT NULL,
  `uuid` varchar(64) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `region` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`id`, `country_id`, `uuid`, `name`, `region`, `created_at`, `updated_at`) VALUES
(1, 135, NULL, 'Chitipa', 'north', NULL, NULL),
(2, 135, NULL, 'Karonga', 'north', NULL, NULL),
(3, 135, NULL, 'Likoma', 'north', NULL, NULL),
(4, 135, NULL, 'Mzimba', 'north', NULL, NULL),
(5, 135, NULL, 'Nkhata Bay', 'north', NULL, NULL),
(6, 135, NULL, 'Rumphi', 'north', NULL, NULL),
(7, 135, NULL, 'Dedza', 'central', NULL, NULL),
(8, 135, NULL, 'Dowa', 'central', NULL, NULL),
(9, 135, NULL, 'Kasungu', 'central', NULL, NULL),
(10, 135, NULL, 'Lilongwe', 'central', NULL, NULL),
(11, 135, NULL, 'Mchinji', 'central', NULL, NULL),
(12, 135, NULL, 'Nkhotakota', 'central', NULL, NULL),
(13, 135, NULL, 'Ntcheu', 'central', NULL, NULL),
(14, 135, NULL, 'Salima', 'central', NULL, NULL),
(15, 135, NULL, 'Balaka', 'central', NULL, NULL),
(16, 135, NULL, 'Blantyre', 'south', NULL, NULL),
(17, 135, NULL, 'Chikwawa', 'south', NULL, NULL),
(18, 135, NULL, 'Chiradzulu', 'south', NULL, NULL),
(19, 135, NULL, 'Machinga', 'south', NULL, NULL),
(20, 135, NULL, 'Mangochi', 'south', NULL, NULL),
(21, 135, NULL, 'Mulanje', 'south', NULL, NULL),
(22, 135, NULL, 'Mwanza', 'south', NULL, NULL),
(23, 135, NULL, 'Nsanje', 'south', NULL, NULL),
(24, 135, NULL, 'Thyolo', 'south', NULL, NULL),
(25, 135, NULL, 'Phalombe', 'south', NULL, NULL),
(26, 135, NULL, 'Neno', 'south', NULL, NULL),
(27, 135, NULL, 'Zomba', 'south', NULL, NULL),
(28, 135, NULL, 'Ntchisi', 'south', '2021-08-12 00:00:00', '2021-08-12 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `education_details`
--

CREATE TABLE `education_details` (
  `education_detail_id` int(11) NOT NULL,
  `program_general` varchar(255) NOT NULL,
  `major` char(36) NOT NULL,
  `name_of_institution` varchar(100) NOT NULL,
  `completion_date` date NOT NULL,
  `applicant_uuid` varchar(255) NOT NULL,
  `other_general` varchar(100) DEFAULT NULL,
  `specific_major` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `education_details`
--

INSERT INTO `education_details` (`education_detail_id`, `program_general`, `major`, `name_of_institution`, `completion_date`, `applicant_uuid`, `other_general`, `specific_major`) VALUES
(1, '75512195-e3fe-4702-9f69-6f0e234b254a', '6ce084fe-f705-4aa2-80c4-bcb75d394552', 'mzuni', '2024-09-19', '550e8400-e29b-41d4-a716-446655440045', NULL, 'great'),
(15, '75512195-e3fe-4702-9f69-6f0e234b254a', '45929fd4-f3d0-40d7-ade5-70392ea39963', 'mzuzu', '2024-10-03', '7d1bded3-7ba3-4480-8d27-3e27bc65b76a', 'mhh', 'ik'),
(16, '75512195-e3fe-4702-9f69-6f0e234b254a', '45929fd4-f3d0-40d7-ade5-70392ea39963', 'Mzuni', '2025-08-14', 'cb8d3376-7ad3-4e9e-acf9-1c8321040661', 'MZuni', 'Hortculture'),
(17, '75512195-e3fe-4702-9f69-6f0e234b254a', '6ce084fe-f705-4aa2-80c4-bcb75d394552', 'LUANAR', '2025-08-05', '841a6036-6fde-41c5-837b-6af2791cb560', 'O', 'Hortculture'),
(18, '75512195-e3fe-4702-9f69-6f0e234b254a', '6ce084fe-f705-4aa2-80c4-bcb75d394552', 'LUANAR', '2025-04-08', 'aef0c35e-c38d-4598-9c24-ceb8178a3640', 'nb', 'horticuture');

-- --------------------------------------------------------

--
-- Table structure for table `education_programs_details`
--

CREATE TABLE `education_programs_details` (
  `uuid` char(36) NOT NULL,
  `general_pg_uuid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `education_programs_details`
--

INSERT INTO `education_programs_details` (`uuid`, `general_pg_uuid`, `name`, `description`, `created_at`, `updated_at`) VALUES
('45929fd4-f3d0-40d7-ade5-70392ea39963', '75512195-e3fe-4702-9f69-6f0e234b254a', 'irrigation', 'engineering', '2024-09-20 03:05:43', '2024-09-20 03:05:43'),
('6ce084fe-f705-4aa2-80c4-bcb75d394552', '75512195-e3fe-4702-9f69-6f0e234b254a', 'Hortcultre', 'great', '2024-09-19 20:42:16', '2024-09-19 20:42:16'),
('7b76605b-3b62-433e-a752-424d9e5be14b', 'bb1efb9b-7db4-4bf0-86e5-503724c8d1f1', 'Pyschological', 'Pyschological', '2024-10-06 18:20:40', '2024-10-06 18:20:40'),
('85da4942-b86b-4272-9827-1d2c20c71189', '26a78b33-0650-4a69-bfa9-ef7044e5ee51', 'Community', 'Community', '2024-10-06 18:12:56', '2024-10-06 18:12:56'),
('9c0aeddb-2a5f-460b-982e-26561789b05a', 'bb1efb9b-7db4-4bf0-86e5-503724c8d1f1', 'Community', 'Community', '2024-10-06 18:20:40', '2024-10-06 18:20:40'),
('c85e4cb5-4496-42e4-bbff-71f237057bb5', '75512195-e3fe-4702-9f69-6f0e234b254a', 'Agronomy', 'agronomy', '2024-09-20 03:05:43', '2024-09-20 03:05:43'),
('e68f865b-5972-4ea3-a777-f11c5b9c93f6', '756667c6-9023-4214-ae3f-df1382bb2826', 'Community', 'Community', '2024-10-06 18:13:22', '2024-10-06 18:13:22');

-- --------------------------------------------------------

--
-- Table structure for table `education_programs_generals`
--

CREATE TABLE `education_programs_generals` (
  `uuid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `education_programs_generals`
--

INSERT INTO `education_programs_generals` (`uuid`, `name`, `description`, `created_at`, `updated_at`) VALUES
('26a78b33-0650-4a69-bfa9-ef7044e5ee51', 'Nursing', 'Health and Nursing', '2024-10-06 18:12:55', '2024-10-06 18:12:55'),
('75512195-e3fe-4702-9f69-6f0e234b254a', 'Agriculture', 'Agri', '2024-09-19 20:42:16', '2024-09-19 20:42:16'),
('75512195-e3fe-4702-9f69-6f0e234b255r', 'Accounting', 'Accounting', '2024-09-19 20:42:16', '2024-09-19 20:42:16'),
('756667c6-9023-4214-ae3f-df1382bb2826', 'Nursing', 'Health and Nursing', '2024-10-06 18:13:22', '2024-10-06 18:13:22'),
('bb1efb9b-7db4-4bf0-86e5-503724c8d1f1', 'Nursing', 'Health and Nursing', '2024-10-06 18:20:40', '2024-10-06 18:20:40');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `uuid` char(36) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `contacts` varchar(255) NOT NULL,
  `residential_address` varchar(255) NOT NULL,
  `postal_address` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `department_uuid` char(36) DEFAULT NULL,
  `role_uuid` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`uuid`, `first_name`, `middle_name`, `last_name`, `title`, `email_address`, `contacts`, `residential_address`, `postal_address`, `created_at`, `updated_at`, `department_uuid`, `role_uuid`) VALUES
('1c4c0902-b4e0-4a45-9ad1-90bdf4d23b96', 'Mhjor ', NULL, 'Muyaba', 'Mr', 'mahjor@gmail.com', '098788776', 'great', 'greatr', '2024-09-20 06:45:19', '2024-09-20 07:49:54', '9dba7d65-4b32-4b66-b4c9-ed8453a9584f', '4cada740-06d3-40d5-b531-2ccae71d9787'),
('42add169-0ec5-4bde-a962-66153ebdce6e', 'Alex', NULL, 'banda', 'Mr', 'alexbanda@gmail.com', '098788776', 'area 45', 'Box 34', '2024-09-20 07:39:27', '2024-09-20 07:39:27', '9dba7d65-4b32-4b66-b4c9-ed8453a9584f', '4cada740-06d3-40d5-b531-2ccae71d9787');

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `goal_id` char(36) NOT NULL,
  `goal_name` varchar(255) NOT NULL,
  `goal_description` text DEFAULT NULL,
  `dept_id` char(36) NOT NULL,
  `added_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `goals`
--

INSERT INTO `goals` (`goal_id`, `goal_name`, `goal_description`, `dept_id`, `added_by`, `created_at`) VALUES
('24810d1b-cfd4-44b0-b8fea28e8c28ad03', 'working tirelessly', 'worked', '327600a2-4a18-43e9-acf4-587a53e963e8', 1, '2025-03-16 19:00:15'),
('ff3c2bd4-92fa-4362-9ba987e075c42de0', 'excellence', 'descrption', '9dba7d65-4b32-4b66-b4c9-ed8453a9584f', 1, '2025-07-05 16:35:38');

-- --------------------------------------------------------

--
-- Table structure for table `graduate`
--

CREATE TABLE `graduate` (
  `id` int(30) UNSIGNED NOT NULL,
  `graduate_uuid` varchar(255) DEFAULT NULL,
  `name` varchar(250) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` enum('Male','Female') DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `email` mediumtext NOT NULL,
  `dob` date DEFAULT NULL,
  `national_id` varchar(100) DEFAULT NULL,
  `mobile` varchar(100) DEFAULT NULL,
  `password` mediumtext NOT NULL,
  `type` tinyint(2) NOT NULL DEFAULT 3,
  `disability` enum('yes','no') NOT NULL,
  `disability_descriptions` mediumtext DEFAULT NULL,
  `residential_address` mediumtext NOT NULL,
  `post_address` mediumtext NOT NULL,
  `role` varchar(30) NOT NULL DEFAULT 'graduate',
  `role_type` enum('applicant','employee') NOT NULL DEFAULT 'applicant',
  `role_uuid` varchar(40) NOT NULL DEFAULT '4cada740-06d3-40d5-b531-2ccae71d9790',
  `department_uuid` varchar(40) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `graduate`
--

INSERT INTO `graduate` (`id`, `graduate_uuid`, `name`, `first_name`, `middle_name`, `last_name`, `gender`, `email`, `dob`, `national_id`, `mobile`, `password`, `type`, `disability`, `disability_descriptions`, `residential_address`, `post_address`, `role`, `role_type`, `role_uuid`, `department_uuid`, `created_at`, `updated_at`) VALUES
(1, '550e8400-e29b-41d4-a716-446655440045', 'Administrator', 'ad', NULL, 'mi nin', 'Male', 'admin@mail.com', '2024-09-09', 'tyyyyyyyy', '0999', 'f865b53623b121fd34ee5426c792e5c33af8c227', 1, 'yes', NULL, '', '', 'admin', 'applicant', '4', NULL, '2022-06-27 09:25:11', '2024-10-05 06:12:22'),
(2, NULL, 'Mark Cooper', '', NULL, '', 'Male', 'mcooper@mail.com', NULL, '', NULL, '$2y$10$.vNmCUZoQIMPGqN/kbckoegJ7fTOPr9Zy2UnOJG1fcFJWIPc8gwk.', 2, 'yes', NULL, '', '', '', 'applicant', '4', NULL, '2022-06-27 13:35:22', '2022-06-27 13:47:33'),
(3, NULL, 'caleb kalagho', '', NULL, '', 'Male', 'calebadmin@gmail.com', NULL, '', NULL, '$2y$10$WzO.T15kUaer4O.6SptJnueibW36RESHTDV.qCUX3l9Cas30Ae58i', 1, 'yes', NULL, '', '', '', 'applicant', '4', NULL, '2024-01-31 10:14:54', '2024-01-31 10:16:00'),
(4, '550e8400-e29b-41d4-a716-446655440000', 'Fainess Nyamela', '', NULL, '', 'Male', 'fainess@gmail.com', NULL, '', NULL, '123456', 3, 'yes', NULL, '', '', 'graduate', 'applicant', '4', NULL, '2024-09-15 16:53:58', '2024-10-01 23:06:32'),
(5, '7d1bded3-7ba3-4480-8d27-3e27bc65b76a', 'Doro Manda', 'Doro', '', 'Manda', 'Female', 'doro@gmail.com', '2024-10-22', '34456663', '08845676', '7c4a8d09ca3762af61e59520943dc26494f8941b', 3, 'yes', NULL, '', '', 'graduate', 'applicant', '4cada740-06d3-40d5-b531-2ccae71d9790', NULL, '2024-10-04 04:47:48', '2024-10-06 15:16:25'),
(7, '7d1bded3-7ba3-4480-8d27-3e27bc99b76a', 'Frackson Banda', 'Frackson', '', 'banda', 'Female', 'frackson@gmail.com', '2024-10-22', '34456663', '088456768', '123456', 3, 'yes', NULL, '', '', 'hr', 'employee', '4cada740-06d3-40d5-b531-2ccae71d9787', '9dba7d65-4b32-4b66-b4c9-ed8453a9584f', '2024-10-04 04:47:48', '2024-10-06 15:16:49'),
(8, 'df28b7a9-6ca4-40df-a838-758c2d7f992a', 'Doreen banda', 'doreen', '', 'banda', 'Female', 'doreen@gmail.com', NULL, '', '0988373653', '7c4a8d09ca3762af61e59520943dc26494f8941b', 3, 'yes', NULL, '', '', 'graduate', 'employee', '4cada740-06d3-40d5-b531-2ccae71d9787', '592d65f7-1199-4546-a03f-3692d680db29', '2024-10-05 14:24:49', '2024-10-06 15:16:59'),
(9, '3beb7277-d234-4e85-a609-8b9da8820471', 'jane jere', 'jane', '', 'Jere', 'Female', 'janebanda@gmail.com', NULL, '', '0888856734', '7c4a8d09ca3762af61e59520943dc26494f8941b', 3, 'yes', NULL, '', '', 'hr', 'employee', 'e6fd4123-c5ec-45fb-a304-2ae975d7c084', '327600a2-4a18-43e9-acf4-587a53e963e8', '2024-10-06 15:58:17', '2024-10-19 01:02:47'),
(10, '59cfc422-d456-4228-ba6d-d716f6fd3236', '', 'Peter', '', 'banda', 'Male', 'peter@mail.com', '2024-10-15', '345555555', '09966666', '??LJ??ߋ?\0y?@?0', 3, 'yes', NULL, '', '', 'graduate', 'applicant', '4cada740-06d3-40d5-b531-2ccae71d9790', NULL, '2024-10-07 05:28:49', '2024-10-07 05:28:49'),
(11, '3261c84d-0982-4626-8c66-06f2261509ac', 'Caleb Kalagho', 'Caleb', '', 'Kalagho', 'Male', 'cralebkalagho@gmail.com', NULL, '', '0988977867', '7c4a8d09ca3762af61e59520943dc26494f8941b', 3, 'yes', NULL, '', '', 'hr', 'employee', 'e6fd4123-c5ec-45fb-a304-2ae975d7c084', '327600a2-4a18-43e9-acf4-587a53e963e8', '2025-07-08 14:06:11', '2025-08-31 22:30:22'),
(12, '97ce586d-f51c-4306-be7d-c0e786ce94fd', 'TiwongeMphiri', 'Tiwonge', '', 'Mphiri', 'Female', 'tiwongebanda@gmail.com', NULL, '', '086567431', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 3, 'yes', NULL, '', '', 'hr', 'employee', '4cada740-06d3-40d5-b531-2ccae71d9790', '327600a2-4a18-43e9-acf4-587a53e963e8', '2025-07-13 14:09:51', '2025-07-13 14:09:51'),
(13, 'cb8d3376-7ad3-4e9e-acf9-1c8321040661', 'NelliBanda', 'Nelli', '', 'Banda', 'Female', 'khumbonelli@gmail.com', '1990-08-14', '7890vgh1', '0881294998', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 3, 'yes', NULL, '', '', 'graduate', 'applicant', '4cada740-06d3-40d5-b531-2ccae71d9790', NULL, '2025-08-14 04:00:03', '2025-08-14 04:00:03'),
(14, '50ec61ff-91f7-48cb-ba27-72c334995777', 'AmiduMuwonjezera', 'Amidu', '', 'Muwonjezera', 'Male', 'admin@mail.com', NULL, NULL, '0999675432', 'f865b53623b121fd34ee5426c792e5c33af8c227', 3, 'yes', NULL, '', '', 'hr', 'employee', 'e6fd4123-c5ec-45fb-a304-2ae975d7c084', '327600a2-4a18-43e9-acf4-587a53e963e8', '2025-08-31 19:10:23', '2025-08-31 19:10:23'),
(19, '841a6036-6fde-41c5-837b-6af2791cb560', 'EmmieNkhata', 'Emmie', '', 'Nkhata', 'Female', 'emmie@gmail.com', '1996-01-01', 'ghv56090', '09971732677', '68f43c972677d88128ab5a9c572452494525b2ca', 3, 'yes', NULL, '', '', 'graduate', 'applicant', '4cada740-06d3-40d5-b531-2ccae71d9790', NULL, '2025-09-07 13:20:22', '2025-09-07 13:20:22'),
(20, 'aef0c35e-c38d-4598-9c24-ceb8178a3640', 'MercyBanda', 'Mercy', '', 'Banda', 'Female', 'mercybanda@gmail.com', '2000-01-04', 'rt56789d', '089897867456', '87b028babc389bb45e70f81d2ce4ccd29e619684', 3, 'yes', NULL, '', '', 'graduate', 'applicant', '4cada740-06d3-40d5-b531-2ccae71d9790', NULL, '2025-09-07 16:25:15', '2025-09-07 16:25:15');

-- --------------------------------------------------------

--
-- Table structure for table `guardians`
--

CREATE TABLE `guardians` (
  `guardian_id` int(11) NOT NULL,
  `uuid` char(36) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mobile_number` varchar(100) NOT NULL,
  `applicant_uuid` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guardians`
--

INSERT INTO `guardians` (`guardian_id`, `uuid`, `name`, `mobile_number`, `applicant_uuid`) VALUES
(1, 'a69eb58e7f8950146196bc4423ffe857', '0', '098989899', '550e8400-e29b-41d4-a716-446655440045'),
(19, '25f0779a-06df-4512-bee7-68c12262ebf8', 'great', '09997676', '7d1bded3-7ba3-4480-8d27-3e27bc65b76a'),
(20, '0a24846c-40b5-4975-825d-313e97d3277f', 'Peter Banda ', '0993774622', 'cb8d3376-7ad3-4e9e-acf9-1c8321040661'),
(21, 'f8011699-090f-4ff1-9e53-d3521bd099c4', 'Nellie ', '09981878', '841a6036-6fde-41c5-837b-6af2791cb560'),
(22, 'fe3af85f-e311-4124-9d8e-0885bd510fe9', 'Mercy', '0887675643', 'aef0c35e-c38d-4598-9c24-ceb8178a3640');

-- --------------------------------------------------------

--
-- Table structure for table `institutions`
--

CREATE TABLE `institutions` (
  `uuid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `post_address` text DEFAULT NULL,
  `physical_address` text DEFAULT NULL,
  `contacts` varchar(255) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `district_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `institutions`
--

INSERT INTO `institutions` (`uuid`, `name`, `description`, `post_address`, `physical_address`, `contacts`, `email_address`, `district_id`, `created_at`, `updated_at`) VALUES
('8f110dde-0dee-4dd0-b474-de62e2684348', 'Lilongwe water board ', 'Lilongwe water board ', 'Lilongwe water board ', 'Lilongwe water board ', '09967864543', 'amiduellan@gmail.com', 10, '2025-09-01 01:50:25', '2025-09-01 01:50:25'),
('b52f990b-ded2-4a51-82ef-4da4b951f70f', 'Higher Eductiona', 'Higher Eductiona', 'Box 84', 'area 24', '0997175667,08812456', 'higher@gmail.com', 10, '2024-09-19 18:07:12', '2024-09-19 18:07:12'),
('b52f990b-ded2-4a51-82ef-4da4b961f80f', 'ESCOM', 'ESCOM', 'Box  980', 'area 24', '0997175667,08812456', 'higher@gmail.com', 10, '2024-09-19 18:07:12', '2025-09-01 06:05:16'),
('f4780d16-e821-4be1-937f-3a59f7b13e07', 'Financial Consequences of Suspension', 'Financial Consequences of Suspension', 'Lilongwe Malawi', 'lilongwe', '09123456', 'limbeleaf@gmail.com', 14, '2025-07-13 14:35:01', '2025-07-13 14:35:01');

-- --------------------------------------------------------

--
-- Table structure for table `intern_performance`
--

CREATE TABLE `intern_performance` (
  `performance_id` char(36) NOT NULL,
  `intern_id` int(11) NOT NULL,
  `kpi_id` char(36) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `comments` text DEFAULT NULL,
  `evaluator_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `intern_performance`
--

INSERT INTO `intern_performance` (`performance_id`, `intern_id`, `kpi_id`, `score`, `comments`, `evaluator_id`, `created_at`) VALUES
('5d6a358c-4e7b-49b5-899a5e70f3e96f0d', 5, '73116b65-137d-4261-87479f1267447a18', 5.00, 'great work', 1, '2025-09-01 04:39:46'),
('670178f0-f919-4050-95d548943703c519', 4, '73116b65-137d-4261-87479f1267447a18', 70.00, 'ok', 1, '2025-07-13 14:42:21'),
('98de9d2a-c687-465f-842575cd32ddd83e', 4, '3df52601-8690-45e1-8b82fb24cb725ea0', 21.00, 'great', 1, '2025-03-16 20:51:38');

-- --------------------------------------------------------

--
-- Table structure for table `kpi_metrics`
--

CREATE TABLE `kpi_metrics` (
  `kpi_id` char(36) NOT NULL,
  `objective_id` char(36) NOT NULL,
  `kpi_name` varchar(255) NOT NULL,
  `kpi_description` text DEFAULT NULL,
  `kpi_weightage` decimal(5,2) NOT NULL,
  `min_target` int(11) NOT NULL,
  `max_target` int(11) NOT NULL,
  `measurement_unit` enum('percentage','score','number') NOT NULL,
  `added_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kpi_metrics`
--

INSERT INTO `kpi_metrics` (`kpi_id`, `objective_id`, `kpi_name`, `kpi_description`, `kpi_weightage`, `min_target`, `max_target`, `measurement_unit`, `added_by`, `created_at`) VALUES
('3df52601-8690-45e1-8b82fb24cb725ea0', '', 'great', 'great KPI s', 45.00, 23, 100, 'percentage', 1, '2025-03-16 20:20:43'),
('73116b65-137d-4261-87479f1267447a18', '9014677a-7b52-4c85-81c24bf7f6a27a00', 'Repor 3 times a month', 'report', 100.00, 50, 100, 'percentage', 1, '2025-07-05 16:39:49'),
('75039169-1655-45f8-8b225193d864eef4', '', 'Pactuality', 'puctuality', 100.00, 50, 100, 'percentage', 1, '2025-09-01 06:17:39'),
('7b1a5914-23f3-4014-98f05e3d5fa4f0de', '9014677a-7b52-4c85-81c24bf7f6a27a00', 'pactuality', 'kPI', 100.00, 50, 100, 'percentage', 1, '2025-09-01 07:06:09');

-- --------------------------------------------------------

--
-- Table structure for table `ministries`
--

CREATE TABLE `ministries` (
  `uuid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ministries`
--

INSERT INTO `ministries` (`uuid`, `name`, `description`, `created_at`, `updated_at`) VALUES
('552a213e-4971-4d71-b18f-882719b3faca', 'Ministry of Education ', 'Grreae', '2024-09-23 05:51:48', '2024-09-23 05:51:48'),
('655e7b83-4419-4ca5-be24-7076818a9f1a', 'Ministry of Finance', 'Finance', '2024-10-05 15:26:03', '2024-10-05 15:26:03'),
('6e645678-6a51-4323-b2a7-98a4767cf000', 'Ministry of Agriculture', 'Agriculture', '2024-10-05 15:27:46', '2024-10-05 15:27:46'),
('e4c03dbe-a8a1-4f8c-9be7-181e4f33f9d0', 'Ministry of gender', 'gender', '2024-10-05 15:24:33', '2024-10-05 15:24:33');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `graduate_uuid` char(36) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('new','read') DEFAULT 'new',
  `date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `graduate_uuid`, `title`, `description`, `status`, `date`) VALUES
(1, '550e8400-e29b-41d4-a716-446655440045', 'Application Status Updated', 'Your application status has been updated to: reviewed', 'read', '2024-10-05 19:31:09'),
(2, '550e8400-e29b-41d4-a716-446655440045', 'Application Status Updated', 'Congratulations! Your application has been successful, and you have been allocated to the \r\n                                    Mbvumbwe. \r\n                                    You are expected to report on 2024-11-04. \r\n                                    Please note that failure to report by this date will be considered as a withdrawal of interest in the opportunity.', 'new', '2024-10-06 15:53:09'),
(3, '7d1bded3-7ba3-4480-8d27-3e27bc65b76a', 'Application Status Updated', 'Your application status has been updated to: reviewed', 'read', '2024-10-06 20:28:08'),
(4, '7d1bded3-7ba3-4480-8d27-3e27bc65b76a', 'Application Status Updated', 'Congratulations! Your application has been successful, and you have been allocated to the \r\n                                    Mbvumbwe. \r\n                                    You are expected to report on 2024-11-04. \r\n                                    Please note that failure to report by this date will be considered as a withdrawal of interest in the opportunity.', 'read', '2024-10-06 20:42:41'),
(5, '7d1bded3-7ba3-4480-8d27-3e27bc65b76a', 'Application Status Updated', 'Congratulations! Your application has been successful, and you have been allocated to the \r\n                                    Mbvumbwe. \r\n                                    You are expected to report on 2024-11-04. \r\n                                    Please note that failure to report by this date will be considered as a withdrawal of interest in the opportunity.', 'new', '2024-10-17 19:20:56');

-- --------------------------------------------------------

--
-- Table structure for table `performance_objectives`
--

CREATE TABLE `performance_objectives` (
  `objective_id` char(36) NOT NULL,
  `goal_id` char(36) NOT NULL,
  `objective_name` varchar(255) NOT NULL,
  `objective_description` text DEFAULT NULL,
  `dept_id` char(36) NOT NULL,
  `added_by` int(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `performance_objectives`
--

INSERT INTO `performance_objectives` (`objective_id`, `goal_id`, `objective_name`, `objective_description`, `dept_id`, `added_by`, `created_at`) VALUES
('9014677a-7b52-4c85-81c24bf7f6a27a00', 'ff3c2bd4-92fa-4362-9ba987e075c42de0', 'report derivery', NULL, '9dba7d65-4b32-4b66-b4c9-ed8453a9584f', 1, '2025-07-05 16:37:37'),
('95b3fc7a-3b21-45ac-ac352f84477e5802', '24810d1b-cfd4-44b0-b8fea28e8c28ad03', 'working at fast', 'working ot fast ', '327600a2-4a18-43e9-acf4-587a53e963e8', 1, '2025-03-16 19:50:23');

-- --------------------------------------------------------

--
-- Table structure for table `performance_trends`
--

CREATE TABLE `performance_trends` (
  `trend_id` char(36) NOT NULL,
  `kpi_id` char(36) DEFAULT NULL,
  `intern_id` int(11) NOT NULL,
  `evaluation_period_id` int(11) NOT NULL,
  `previous_score` decimal(5,2) NOT NULL,
  `current_score` decimal(5,2) NOT NULL,
  `trend_status` enum('Improved','Declined','Consistent') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `uuid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`uuid`, `name`, `description`, `created_at`, `updated_at`) VALUES
('4cada740-06d3-40d5-b531-2ccae71d9787', 'IT director', 'great ', '2024-08-24 16:24:09', '2024-08-24 16:24:09'),
('4cada740-06d3-40d5-b531-2ccae71d9790', 'graduate', 'great ', '2024-08-24 16:24:09', '2024-08-24 16:24:09'),
('e6fd4123-c5ec-45fb-a304-2ae975d7c084', 'HR', 'Human Resource', '2024-10-05 15:34:19', '2024-10-05 15:34:19');

-- --------------------------------------------------------

--
-- Table structure for table `service_district`
--

CREATE TABLE `service_district` (
  `service_district_id` int(11) NOT NULL,
  `uuid` char(36) NOT NULL,
  `applicant_uuid` char(36) NOT NULL,
  `district_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_district`
--

INSERT INTO `service_district` (`service_district_id`, `uuid`, `applicant_uuid`, `district_id`) VALUES
(1, 'b428c49cdea29519008448980c0c67d5', '550e8400-e29b-41d4-a716-446655440045', 10),
(12, '57a75dd7-d782-47a3-99ed-f0e1b2b50252', '7d1bded3-7ba3-4480-8d27-3e27bc65b76a', 10),
(13, '5bd663e0-6f19-4b0f-97be-65b7204a1733', 'cb8d3376-7ad3-4e9e-acf9-1c8321040661', 10),
(14, '60934805-cf1d-483c-8c95-1f08c5c4fb31', '841a6036-6fde-41c5-837b-6af2791cb560', 10),
(15, '313af915-6867-4068-8bd0-936c61bfb660', 'aef0c35e-c38d-4598-9c24-ceb8178a3640', 10);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('gboah9D8DMXtK5XViOtfezKdjWBZjdPHKiRuhS5h', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:130.0) Gecko/20100101 Firefox/130.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoicG1SREVHMHNRTlVzM0R2eGhWUURka1JBWWVTMmF1SUxzSGFOaElMViI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQ0OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vZW1wbG95ZWVzL2NyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiRFRXBjZTFuVXpvZjdRQ2tGUEFraDIuNTBjT1VMWUxvMWxzQWlycjd6TkREYThMYk5RbG90UyI7fQ==', 1727843273),
('h1uKnZLelq6UC4SDhPxeq5c9TQPMwKTL1Vc3vNt0', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:130.0) Gecko/20100101 Firefox/130.0', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoidFF5d3VpOEZGRDFzVnZheVNSTW9xVlJqN1I2NnpYcENGTmJFNWl3SiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vdmFjYW5jaWVzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEyJEVFcGNlMW5Vem9mN1FDa0ZQQWtoMi41MGNPVUxZTG8xbHNBaXJyN3pORERhOExiTlFsb3RTIjtzOjg6ImZpbGFtZW50IjthOjA6e319', 1727105067);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `tbl_user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`tbl_user_id`, `name`, `username`, `password`, `role`) VALUES
(1, 'Lorem Ipsum', 'admin', 'admin', 'admin'),
(3, 'John Doe', 'user', 'user', 'user'),
(4, 'calebkalagho@gmail.com', 'calebkalagho@gmail.com', 'u#AhHMZ!]2G;r^D', 'admin'),
(5, 'Mercy', 'mercy', '123456', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_uuid` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `employee_uuid`) VALUES
(1, 'Tiwonge kamanga', 'tiwongekamanga@gmail.com', NULL, '$2y$12$EEpce1nUzof7QCkFPAkh2.50cOULYLo1lsAirr7zNDDa8LbNQlotS', NULL, '2024-08-22 22:43:52', '2024-09-20 07:45:42', '1c4c0902-b4e0-4a45-9ad1-90bdf4d23b96');

-- --------------------------------------------------------

--
-- Table structure for table `vacancies`
--

CREATE TABLE `vacancies` (
  `uuid` char(36) NOT NULL,
  `vacancy_title` text NOT NULL,
  `summary` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `duties` text NOT NULL,
  `qualifications` text NOT NULL,
  `experience` text NOT NULL,
  `department_uuid` char(36) NOT NULL,
  `opening_date` date NOT NULL,
  `closing_date` date NOT NULL,
  `employee_uuid` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` enum('draft','pending_approval','open','closed','filled','expired','cancelled','on_hold','reopened','under_review','interviewing','offer_made') NOT NULL DEFAULT 'draft',
  `type` enum('internship','temporary','full_time','part_time','contract') NOT NULL DEFAULT 'full_time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vacancies`
--

INSERT INTO `vacancies` (`uuid`, `vacancy_title`, `summary`, `description`, `duties`, `qualifications`, `experience`, `department_uuid`, `opening_date`, `closing_date`, `employee_uuid`, `created_at`, `updated_at`, `status`, `type`) VALUES
('adf93d3c-63f9-491f-80e6-0be99e0d8b64', 'Field Officer', 'great', 'great', 'work,work', 'bacherlors, diploma', '4 yrs, 2 yrs', '592d65f7-1199-4546-a03f-3692d680db29', '2024-09-23', '2024-09-29', '1c4c0902-b4e0-4a45-9ad1-90bdf4d23b96', '2024-09-23 12:47:30', '2024-09-23 13:00:18', 'open', 'full_time');

-- --------------------------------------------------------

--
-- Table structure for table `vacancy_details`
--

CREATE TABLE `vacancy_details` (
  `uuid` char(36) NOT NULL,
  `vacancy_uuid` char(36) NOT NULL,
  `program_general_uuid` char(36) NOT NULL,
  `major_uuid` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vacancy_details`
--

INSERT INTO `vacancy_details` (`uuid`, `vacancy_uuid`, `program_general_uuid`, `major_uuid`, `created_at`, `updated_at`) VALUES
('90da18bb-e84c-47b5-81c9-29843eb51aa4', 'adf93d3c-63f9-491f-80e6-0be99e0d8b64', '75512195-e3fe-4702-9f69-6f0e234b254a', '6ce084fe-f705-4aa2-80c4-bcb75d394552', '2024-09-23 12:47:30', '2024-09-23 12:47:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `allocate_applicants`
--
ALTER TABLE `allocate_applicants`
  ADD PRIMARY KEY (`allocate_applicant_id`);

--
-- Indexes for table `applicant_attachements`
--
ALTER TABLE `applicant_attachements`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `applicant_uuid` (`applicant_uuid`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `cohort_uuid` (`cohort_uuid`),
  ADD KEY `applicant_uuid` (`applicant_uuid`);

--
-- Indexes for table `bank_details`
--
ALTER TABLE `bank_details`
  ADD PRIMARY KEY (`bank_detail_id`),
  ADD UNIQUE KEY `uuid` (`uuid`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cohort_programs`
--
ALTER TABLE `cohort_programs`
  ADD PRIMARY KEY (`uuid`);

--
-- Indexes for table `cohort_program_assignments`
--
ALTER TABLE `cohort_program_assignments`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `cohort_program_assignments_department_uuid_foreign` (`department_uuid`),
  ADD KEY `cohort_program_assignments_cohort_program_uuid_foreign` (`cohort_program_uuid`);

--
-- Indexes for table `cohort_program_assignment_details`
--
ALTER TABLE `cohort_program_assignment_details`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `cohort_program_assignment_details_assignment_uuid_foreign` (`assignment_uuid`),
  ADD KEY `cohort_program_assignment_details_general_uuid_foreign` (`general_uuid`),
  ADD KEY `cohort_program_assignment_details_major_uuid_foreign` (`major_uuid`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `departments_district_id_foreign` (`district_id`),
  ADD KEY `departments_ministry_uuid_foreign` (`ministry_uuid`),
  ADD KEY `departments_da_uuid_foreign` (`da_uuid`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_districts_countries` (`country_id`);

--
-- Indexes for table `education_details`
--
ALTER TABLE `education_details`
  ADD PRIMARY KEY (`education_detail_id`),
  ADD KEY `program_general` (`program_general`),
  ADD KEY `major` (`major`);

--
-- Indexes for table `education_programs_details`
--
ALTER TABLE `education_programs_details`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `education_programs_details_general_pg_uuid_foreign` (`general_pg_uuid`);

--
-- Indexes for table `education_programs_generals`
--
ALTER TABLE `education_programs_generals`
  ADD PRIMARY KEY (`uuid`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`uuid`),
  ADD UNIQUE KEY `employees_email_address_unique` (`email_address`),
  ADD KEY `employees_department_uuid_foreign` (`department_uuid`),
  ADD KEY `employees_role_uuid_foreign` (`role_uuid`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`goal_id`);

--
-- Indexes for table `graduate`
--
ALTER TABLE `graduate`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mobile` (`mobile`),
  ADD UNIQUE KEY `graduate_uuid` (`graduate_uuid`);

--
-- Indexes for table `guardians`
--
ALTER TABLE `guardians`
  ADD PRIMARY KEY (`guardian_id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `applicant_uuid` (`applicant_uuid`);

--
-- Indexes for table `institutions`
--
ALTER TABLE `institutions`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `institutions_district_id_foreign` (`district_id`);

--
-- Indexes for table `intern_performance`
--
ALTER TABLE `intern_performance`
  ADD PRIMARY KEY (`performance_id`);

--
-- Indexes for table `kpi_metrics`
--
ALTER TABLE `kpi_metrics`
  ADD PRIMARY KEY (`kpi_id`);

--
-- Indexes for table `ministries`
--
ALTER TABLE `ministries`
  ADD PRIMARY KEY (`uuid`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `performance_objectives`
--
ALTER TABLE `performance_objectives`
  ADD PRIMARY KEY (`objective_id`);

--
-- Indexes for table `performance_trends`
--
ALTER TABLE `performance_trends`
  ADD PRIMARY KEY (`trend_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`uuid`);

--
-- Indexes for table `service_district`
--
ALTER TABLE `service_district`
  ADD PRIMARY KEY (`service_district_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`tbl_user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_employee_uuid_foreign` (`employee_uuid`);

--
-- Indexes for table `vacancies`
--
ALTER TABLE `vacancies`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `vacancies_department_uuid_foreign` (`department_uuid`),
  ADD KEY `vacancies_employee_uuid_foreign` (`employee_uuid`);

--
-- Indexes for table `vacancy_details`
--
ALTER TABLE `vacancy_details`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `vacancy_details_vacancy_uuid_foreign` (`vacancy_uuid`),
  ADD KEY `vacancy_details_program_general_uuid_foreign` (`program_general_uuid`),
  ADD KEY `vacancy_details_major_uuid_foreign` (`major_uuid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `allocate_applicants`
--
ALTER TABLE `allocate_applicants`
  MODIFY `allocate_applicant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `applicant_attachements`
--
ALTER TABLE `applicant_attachements`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bank_details`
--
ALTER TABLE `bank_details`
  MODIFY `bank_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `education_details`
--
ALTER TABLE `education_details`
  MODIFY `education_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `graduate`
--
ALTER TABLE `graduate`
  MODIFY `id` int(30) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `guardians`
--
ALTER TABLE `guardians`
  MODIFY `guardian_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `service_district`
--
ALTER TABLE `service_district`
  MODIFY `service_district_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `tbl_user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cohort_program_assignments`
--
ALTER TABLE `cohort_program_assignments`
  ADD CONSTRAINT `cohort_program_assignments_department_uuid_foreign` FOREIGN KEY (`department_uuid`) REFERENCES `departments` (`uuid`) ON DELETE CASCADE;

--
-- Constraints for table `cohort_program_assignment_details`
--
ALTER TABLE `cohort_program_assignment_details`
  ADD CONSTRAINT `cohort_program_assignment_details_assignment_uuid_foreign` FOREIGN KEY (`assignment_uuid`) REFERENCES `cohort_program_assignments` (`uuid`) ON DELETE CASCADE,
  ADD CONSTRAINT `cohort_program_assignment_details_major_uuid_foreign` FOREIGN KEY (`major_uuid`) REFERENCES `education_programs_details` (`uuid`) ON DELETE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_da_uuid_foreign` FOREIGN KEY (`da_uuid`) REFERENCES `institutions` (`uuid`) ON DELETE CASCADE,
  ADD CONSTRAINT `departments_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `departments_ministry_uuid_foreign` FOREIGN KEY (`ministry_uuid`) REFERENCES `ministries` (`uuid`) ON DELETE CASCADE;

--
-- Constraints for table `education_programs_details`
--
ALTER TABLE `education_programs_details`
  ADD CONSTRAINT `education_programs_details_general_pg_uuid_foreign` FOREIGN KEY (`general_pg_uuid`) REFERENCES `education_programs_generals` (`uuid`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_department_uuid_foreign` FOREIGN KEY (`department_uuid`) REFERENCES `departments` (`uuid`) ON DELETE CASCADE,
  ADD CONSTRAINT `employees_role_uuid_foreign` FOREIGN KEY (`role_uuid`) REFERENCES `roles` (`uuid`) ON DELETE CASCADE;

--
-- Constraints for table `institutions`
--
ALTER TABLE `institutions`
  ADD CONSTRAINT `institutions_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_employee_uuid_foreign` FOREIGN KEY (`employee_uuid`) REFERENCES `employees` (`uuid`) ON DELETE CASCADE;

--
-- Constraints for table `vacancies`
--
ALTER TABLE `vacancies`
  ADD CONSTRAINT `vacancies_department_uuid_foreign` FOREIGN KEY (`department_uuid`) REFERENCES `departments` (`uuid`) ON DELETE CASCADE,
  ADD CONSTRAINT `vacancies_employee_uuid_foreign` FOREIGN KEY (`employee_uuid`) REFERENCES `employees` (`uuid`) ON DELETE CASCADE;

--
-- Constraints for table `vacancy_details`
--
ALTER TABLE `vacancy_details`
  ADD CONSTRAINT `vacancy_details_major_uuid_foreign` FOREIGN KEY (`major_uuid`) REFERENCES `education_programs_details` (`uuid`) ON DELETE CASCADE,
  ADD CONSTRAINT `vacancy_details_program_general_uuid_foreign` FOREIGN KEY (`program_general_uuid`) REFERENCES `education_programs_generals` (`uuid`) ON DELETE CASCADE,
  ADD CONSTRAINT `vacancy_details_vacancy_uuid_foreign` FOREIGN KEY (`vacancy_uuid`) REFERENCES `vacancies` (`uuid`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

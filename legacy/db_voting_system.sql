-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 06, 2026 at 06:30 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_voting_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(6) UNSIGNED NOT NULL,
  `election_id` int(6) UNSIGNED NOT NULL,
  `fullname` varchar(150) NOT NULL,
  `position` varchar(100) NOT NULL,
  `bio` text NOT NULL,
  `school_id` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `election_id`, `fullname`, `position`, `bio`, `school_id`, `email`, `contact_number`) VALUES
(1, 1, 'Carlos Yulo', 'President', 'Empowering student voices through technology and action.', '2022-0101', 'carlos.yulo@candidate.edu.ph', '09171234567'),
(2, 1, 'Alex Eala', 'President', 'Building a transparent and inclusive student government.', '2022-0102', 'alex.eala@candidate.edu.ph', '09171234568'),
(3, 1, 'Hidilyn Diaz', 'Vice President', 'Strong leadership dedicated to service and discipline.', '2022-0103', 'hidilyn.diaz@candidate.edu.ph', '09171234569'),
(4, 1, 'EJ Obiena', 'Vice President', 'Reaching new heights together for student welfare.', '2022-0104', 'ej.obiena@candidate.edu.ph', '09171234570'),
(5, 1, 'Alyssa Valdez', 'Secretary', 'Organized, efficient, and reliable communication for all.', '2023-0201', 'alyssa.valdez@candidate.edu.ph', '09181234561'),
(6, 1, 'Mika Reyes', 'Secretary', 'Ensuring accurate records and open communication lines.', '2023-0202', 'mika.reyes@candidate.edu.ph', '09181234562'),
(7, 1, 'June Mar Fajardo', 'Treasurer', 'Accountable financial management and transparent budgets.', '2023-0203', 'junemar.fajardo@candidate.edu.ph', '09181234563'),
(8, 1, 'Scottie Thompson', 'Treasurer', 'Maximizing resources to fund impactful student projects.', '2023-0204', 'scottie.thompson@candidate.edu.ph', '09181234564'),
(9, 1, 'Dwight Ramos', 'Auditor', 'Checking balance sheets and ensuring financial integrity.', '2024-0301', 'dwight.ramos@candidate.edu.ph', '09191234565'),
(10, 1, 'Kai Sotto', 'Auditor', 'Overlooking budgets with precision and dedication.', '2024-0302', 'kai.sotto@candidate.edu.ph', '09191234566'),
(11, 1, 'Catriona Gray', 'PIO', 'Spreading student awareness with excellence and passion.', '2024-0303', 'catriona.gray@candidate.edu.ph', '09191234567'),
(12, 1, 'Pia Wurtzbach', 'PIO', 'Connecting students to updates with speed and accuracy.', '2024-0304', 'pia.wurtzbach@candidate.edu.ph', '09191234568'),
(13, 2, 'Gretchen Ho', 'President', 'Leadership with sportsmanship and drive.', '2022-0501', 'gretchen.ho@candidate.edu.ph', '09201234561'),
(14, 2, 'Kiefer Ravena', 'President', 'Leading the engineering department to victory.', '2022-0502', 'kiefer.ravena@candidate.edu.ph', '09201234562'),
(15, 2, 'Jodi Sta Maria', 'Vice President', 'Serving students with empathy and determination.', '2023-0503', 'jodi.stamaria@candidate.edu.ph', '09201234563'),
(16, 2, 'Anne Ojeda', 'Vice President', 'Advocating for student welfare and academic balance.', '2023-0504', 'anne.ojeda@candidate.edu.ph', '09201234564');

-- --------------------------------------------------------

--
-- Table structure for table `elections`
--

CREATE TABLE `elections` (
  `id` int(6) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `department` varchar(100) NOT NULL,
  `open_at` datetime NOT NULL,
  `close_at` datetime NOT NULL,
  `is_closed` int(1) DEFAULT 0,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `elections`
--

INSERT INTO `elections` (`id`, `name`, `department`, `open_at`, `close_at`, `is_closed`, `firstname`, `lastname`) VALUES
(1, 'SBO Supreme Student Council Election 2026', 'College of Computer Studies', '2026-08-01 08:00:00', '2026-08-05 17:00:00', 1, 'Admin', 'Officer'),
(2, 'Engineering Student Council Election 2026', 'College of Engineering', '2026-08-10 08:00:00', '2026-08-15 17:00:00', 0, 'Admin', 'Officer');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(6) UNSIGNED NOT NULL,
  `school_id` varchar(50) NOT NULL,
  `firstname` text NOT NULL,
  `lastname` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','moderator','admin') DEFAULT 'student',
  `is_online` int(1) DEFAULT 0,
  `session_id` varchar(255) DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `department` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `school_id`, `firstname`, `lastname`, `email`, `password`, `role`, `is_online`, `session_id`, `last_activity`, `department`) VALUES
(1, '123456', '', '', 'admin@example.com', 'admin', 'admin', 1, 'm7l1r8r33lq1q3h80sdkfr4kf7', '2026-08-06 12:02:06', 'IT Department'),
(2, 'MOD-001', '', '', 'moderator@university.edu.ph', 'mod123', 'moderator', 0, NULL, NULL, 'Student Affairs'),
(4, '2024-0002', '', '', 'maria.santos@student.edu.ph', 'pass123', 'student', 0, NULL, NULL, 'College of Computer Studies'),
(5, '2024-0003', '', '', 'mark.reyes@student.edu.ph', 'pass123', 'student', 0, NULL, NULL, 'College of Computer Studies'),
(6, '2024-0004', '', '', 'anna.garcia@student.edu.ph', 'pass123', 'student', 0, NULL, NULL, 'College of Computer Studies'),
(7, '2024-0005', '', '', 'john.ramos@student.edu.ph', 'pass123', 'student', 0, NULL, NULL, 'College of Computer Studies'),
(8, '2024-0006', '', '', 'grace.mendoza@student.edu.ph', 'pass123', 'student', 0, NULL, NULL, 'College of Computer Studies'),
(9, '2024-0007', '', '', 'paul.flores@student.edu.ph', 'pass123', 'student', 0, NULL, NULL, 'College of Computer Studies'),
(10, '2024-0008', '', '', 'sarah.bautista@student.edu.ph', 'pass123', 'student', 0, NULL, NULL, 'College of Computer Studies'),
(11, '2024-0009', '', '', 'david.gonzales@student.edu.ph', 'pass123', 'student', 0, NULL, NULL, 'College of Computer Studies'),
(12, '2024-0010', '', '', 'lisa.villanueva@student.edu.ph', 'pass123', 'student', 0, NULL, NULL, 'College of Computer Studies'),
(13, '2024-0011', '', '', 'kevin.castro@student.edu.ph', 'pass123', 'student', 0, NULL, NULL, 'College of Computer Studies'),
(14, '2024-0012', '', '', 'elena.aquino@student.edu.ph', 'pass123', 'student', 0, NULL, NULL, 'College of Computer Studies'),
(15, '2024-0013', '', '', 'james.navarro@student.edu.ph', 'pass123', 'student', 0, NULL, NULL, 'College of Computer Studies'),
(16, '2024-0014', '', '', 'rachel.torres@student.edu.ph', 'pass123', 'student', 0, NULL, NULL, 'College of Computer Studies'),
(17, '2024-0015', '', '', 'christian.lim@student.edu.ph', 'pass123', 'student', 0, NULL, NULL, 'College of Computer Studies'),
(18, '2024-0016', '', '', 'patricia.cruz@student.edu.ph', 'pass123', 'student', 0, NULL, NULL, 'College of Computer Studies'),
(19, '2024-0017', '', '', 'daniel.perez@student.edu.ph', 'pass123', 'student', 0, NULL, NULL, 'College of Computer Studies');

-- --------------------------------------------------------

--
-- Table structure for table `voter_submissions`
--

CREATE TABLE `voter_submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `election_id` int(6) UNSIGNED NOT NULL,
  `voter_id` int(6) UNSIGNED NOT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voter_submissions`
--

INSERT INTO `voter_submissions` (`id`, `election_id`, `voter_id`, `submitted_at`) VALUES
(2, 1, 4, '2026-08-01 10:30:12'),
(3, 1, 5, '2026-08-01 11:45:22'),
(4, 1, 6, '2026-08-01 14:05:10'),
(5, 1, 7, '2026-08-02 08:20:45'),
(6, 1, 8, '2026-08-02 09:50:30'),
(7, 1, 9, '2026-08-02 12:10:00'),
(8, 1, 10, '2026-08-02 15:33:18'),
(9, 1, 11, '2026-08-03 08:05:00'),
(10, 1, 12, '2026-08-03 10:12:44'),
(11, 1, 13, '2026-08-03 13:25:50'),
(12, 1, 14, '2026-08-03 16:40:11'),
(13, 1, 15, '2026-08-04 09:00:20'),
(14, 1, 16, '2026-08-04 11:15:33'),
(15, 1, 17, '2026-08-04 14:50:00'),
(16, 1, 18, '2026-08-05 16:02:10');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(10) UNSIGNED NOT NULL,
  `election_id` int(6) UNSIGNED NOT NULL,
  `voter_id` int(6) UNSIGNED NOT NULL,
  `position` varchar(100) NOT NULL,
  `candidate_id` int(6) UNSIGNED DEFAULT NULL,
  `voted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `election_id` (`election_id`);

--
-- Indexes for table `elections`
--
ALTER TABLE `elections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `voter_submissions`
--
ALTER TABLE `voter_submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_submission` (`election_id`,`voter_id`),
  ADD KEY `voter_id` (`voter_id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vote_per_position` (`election_id`,`voter_id`,`position`),
  ADD KEY `voter_id` (`voter_id`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `elections`
--
ALTER TABLE `elections`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `voter_submissions`
--
ALTER TABLE `voter_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `voter_submissions`
--
ALTER TABLE `voter_submissions`
  ADD CONSTRAINT `voter_submissions_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `voter_submissions_ibfk_2` FOREIGN KEY (`voter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`voter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_3` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

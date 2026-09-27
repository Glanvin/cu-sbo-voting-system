-- CU SBO Voting System - schema plus demo data. Migrate first, then load this:
--     php index.php migrate
--     mysql -u root db_voting_system < sql/voting_system.sql
-- Department values are full names, which is what form_rules.php whitelists and
-- Election_model::is_eligible() compares against. See README for the rest.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE votes;
TRUNCATE TABLE voter_submissions;
TRUNCATE TABLE candidates;
TRUNCATE TABLE elections;
TRUNCATE TABLE users;

SET FOREIGN_KEY_CHECKS = 1;


-- 1. Accounts: 1 admin, 1 moderator, 17 student voters. Passwords are plain text, as in the legacy app.
-- is_new is 0 for staff and 1 for students, so students hit the forced first-login change.
--   admin@example.com / admin    moderator@university.edu.ph / mod123    any @student.edu.ph / pass123

INSERT INTO users (`school_id`, `firstname`, `lastname`, `email`, `password`, `role`, `is_new`, `is_online`, `session_id`, `last_activity`, `department`) VALUES
('123456',   'Admin',    'Officer',  'admin@example.com',            'admin',   'admin',     0, 0, NULL, NULL, 'IT Department'),
('MOD-001',  'System',   'Moderator','moderator@university.edu.ph',  'mod123',  'moderator', 0, 0, NULL, NULL, 'Student Affairs'),
('2024-0001','Juan',     'Dela Cruz','juan.delacruz@student.edu.ph',  'pass123', 'student',   1, 0, NULL, NULL, 'College of Computer Studies'),
('2024-0002','Maria',    'Santos',   'maria.santos@student.edu.ph',   'pass123', 'student',   1, 0, NULL, NULL, 'College of Computer Studies'),
('2024-0003','Mark',     'Reyes',    'mark.reyes@student.edu.ph',    'pass123', 'student',   1, 0, NULL, NULL, 'College of Computer Studies'),
('2024-0004','Anna',     'Garcia',   'anna.garcia@student.edu.ph',   'pass123', 'student',   1, 0, NULL, NULL, 'College of Computer Studies'),
('2024-0005','John',     'Ramos',    'john.ramos@student.edu.ph',    'pass123', 'student',   1, 0, NULL, NULL, 'College of Computer Studies'),
('2024-0006','Grace',    'Mendoza',  'grace.mendoza@student.edu.ph', 'pass123', 'student',   1, 0, NULL, NULL, 'College of Computer Studies'),
('2024-0007','Paul',     'Flores',   'paul.flores@student.edu.ph',   'pass123', 'student',   1, 0, NULL, NULL, 'College of Computer Studies'),
('2024-0008','Sarah',    'Bautista', 'sarah.bautista@student.edu.ph', 'pass123', 'student',   1, 0, NULL, NULL, 'College of Computer Studies'),
('2024-0009','David',    'Gonzales', 'david.gonzales@student.edu.ph','pass123', 'student',   1, 0, NULL, NULL, 'College of Computer Studies'),
('2024-0010','Lisa',     'Villanueva','lisa.villanueva@student.edu.ph','pass123','student',   1, 0, NULL, NULL, 'College of Computer Studies'),
('2024-0013','Elena',    'Aquino',   'elena.aquino@student.edu.ph',  'pass123', 'student',   1, 0, NULL, NULL, 'College of Computer Studies'),
('2024-0014','James',    'Navarro',  'james.navarro@student.edu.ph', 'pass123', 'student',   1, 0, NULL, NULL, 'College of Computer Studies'),
('2024-0015','Rachel',   'Torres',   'rachel.torres@student.edu.ph', 'pass123', 'student',   1, 0, NULL, NULL, 'College of Computer Studies'),
('2024-0016','Christian','Lim',      'christian.lim@student.edu.ph', 'pass123', 'student',   1, 0, NULL, NULL, 'College of Computer Studies'),
('2024-0017','Patricia', 'Cruz',     'patricia.cruz@student.edu.ph', 'pass123', 'student',   1, 0, NULL, NULL, 'College of Computer Studies'),
('2024-0018','Daniel',   'Perez',    'daniel.perez@student.edu.ph',  'pass123', 'student',   1, 0, NULL, NULL, 'College of Computer Studies'),
('2024-0019','Angela',   'Sy',       'angela.sy@student.edu.ph',     'pass123', 'student',   1, 0, NULL, NULL, 'College of Computer Studies');


-- 2. Elections. Election 1 is closed and carries the ballot history below; election 2 is open but University Wide,
-- so election 3 is the one to use for a live ballot. Windows are relative to NOW() so every state is represented.

INSERT INTO elections (`name`, `department`, `open_at`, `close_at`, `is_closed`) VALUES
('SBO Supreme Student Council Election 2026', 'College of Computer Studies',
 DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_SUB(NOW(), INTERVAL 20 DAY), 1),
('University Wide Orientation Election 2026', 'University Wide',
 DATE_SUB(NOW(), INTERVAL 5 DAY),  DATE_ADD(NOW(), INTERVAL 5 DAY),  0),
('CCS Live Demo Election', 'College of Computer Studies',
 DATE_SUB(NOW(), INTERVAL 1 HOUR), DATE_ADD(NOW(), INTERVAL 6 DAY),  0),
('Student Affairs Officer Election 2026', 'Student Affairs',
 DATE_SUB(NOW(), INTERVAL 1 HOUR), DATE_ADD(NOW(), INTERVAL 6 DAY),  0);


-- 3. Candidates

INSERT INTO candidates (`election_id`, `fullname`, `position`, `bio`, `school_id`, `email`, `contact_number`) VALUES
-- Election 1, six positions, two candidates each
(1, 'Carlos Yulo',        'President',   'Empowering student voices through technology and action.',       '2022-0101', 'carlos.yulo@candidate.edu.ph',   '09171234567'),
(1, 'Alex Eala',          'President',   'Building a transparent and inclusive student government.',      '2022-0102', 'alex.eala@candidate.edu.ph',     '09171234568'),
(1, 'Hidilyn Diaz',       'Vice President', 'Strong leadership dedicated to service and discipline.',     '2022-0103', 'hidilyn.diaz@candidate.edu.ph',  '09171234569'),
(1, 'EJ Obiena',          'Vice President', 'Reaching new heights together for student welfare.',       '2022-0104', 'ej.obiena@candidate.edu.ph',     '09171234570'),
(1, 'Alyssa Valdez',      'Secretary',   'Organized, efficient, and reliable communication for all.',      '2023-0201', 'alyssa.valdez@candidate.edu.ph', '09181234561'),
(1, 'Mika Reyes',         'Secretary',   'Ensuring accurate records and open communication lines.',       '2023-0202', 'mika.reyes@candidate.edu.ph',    '09181234562'),
(1, 'June Mar Fajardo',   'Treasurer',   'Accountable financial management and transparent budgets.',     '2023-0203', 'junemar.fajardo@candidate.edu.ph','09181234563'),
(1, 'Scottie Thompson',   'Treasurer',   'Maximizing resources to fund impactful student projects.',     '2023-0204', 'scottie.thompson@candidate.edu.ph','09181234564'),
(1, 'Dwight Ramos',       'Auditor',     'Checking balance sheets and ensuring financial integrity.',    '2024-0301', 'dwight.ramos@candidate.edu.ph',  '09191234565'),
(1, 'Kai Sotto',          'Auditor',     'Overlooking budgets with precision and dedication.',           '2024-0302', 'kai.sotto@candidate.edu.ph',     '09191234566'),
(1, 'Catriona Gray',      'Public Information Officer', 'Spreading student awareness with excellence and passion.','2024-0303','catriona.gray@candidate.edu.ph','09191234567'),
(1, 'Pia Wurtzbach',      'Public Information Officer', 'Connecting students to updates with speed and accuracy.','2024-0304','pia.wurtzbach@candidate.edu.ph','09191234568'),
-- Election 2, a short University Wide slate
(2, 'Gretchen Ho',        'President',   'Leadership with sportsmanship and drive.',                    '2022-0501', 'gretchen.ho@candidate.edu.ph',   '09201234561'),
(2, 'Kiefer Ravena',      'President',   'Leading the way to victory for every college.',               '2022-0502', 'kiefer.ravena@candidate.edu.ph', '09201234562'),
(2, 'Jodi Sta Maria',     'Vice President', 'Serving students with empathy and determination.',         '2023-0503', 'jodi.stamaria@candidate.edu.ph','09201234563'),
-- Election 3, a small slate for trying a live ballot
(3, 'Bea Alonzo',         'President',   'A candidate for the live demo ballot.',                       '2022-0701', 'bea.alonzo@candidate.edu.ph',    '09301234571'),
(3, 'Crisanto Dela Cruz', 'President',   'A second candidate for the live demo ballot.',                 '2022-0702', 'cris.delacruz@candidate.edu.ph','09301234572'),
(3, 'Daniela Reyes',      'Secretary',   'A candidate for the live demo secretary race.',               '2023-0703', 'daniela.reyes@candidate.edu.ph','09301234573'),
-- Election 4 is a Student Affairs slate, so the department rule is demonstrable: a CCS student is not offered it.
(4, 'Nova Alcaraz',       'President',   'Running for the Student Affairs presidency.',                 '2022-0801', 'nova.alcaraz@candidate.edu.ph',  '09401234581'),
(4, 'Trisha Lim',         'President',   'A second Student Affairs presidential candidate.',           '2022-0802', 'trisha.lim@candidate.edu.ph',   '09401234582');


-- 4. Submitted ballots for election 1: voters 3 to 17. The UNIQUE key on (election_id, voter_id) blocks a second attempt.

INSERT INTO voter_submissions (`election_id`, `voter_id`, `submitted_at`) VALUES
(1,  3, DATE_SUB(NOW(), INTERVAL 25 DAY)),
(1,  4, DATE_SUB(NOW(), INTERVAL 25 DAY)),
(1,  5, DATE_SUB(NOW(), INTERVAL 24 DAY)),
(1,  6, DATE_SUB(NOW(), INTERVAL 24 DAY)),
(1,  7, DATE_SUB(NOW(), INTERVAL 23 DAY)),
(1,  8, DATE_SUB(NOW(), INTERVAL 23 DAY)),
(1,  9, DATE_SUB(NOW(), INTERVAL 22 DAY)),
(1, 10, DATE_SUB(NOW(), INTERVAL 22 DAY)),
(1, 11, DATE_SUB(NOW(), INTERVAL 21 DAY)),
(1, 12, DATE_SUB(NOW(), INTERVAL 21 DAY)),
(1, 13, DATE_SUB(NOW(), INTERVAL 20 DAY)),
(1, 14, DATE_SUB(NOW(), INTERVAL 20 DAY)),
(1, 15, DATE_SUB(NOW(), INTERVAL 20 DAY)),
(1, 16, DATE_SUB(NOW(), INTERVAL 20 DAY)),
(1, 17, DATE_SUB(NOW(), INTERVAL 20 DAY));


-- 5. Recorded votes for election 1: six positions per ballot, ninety rows.
-- Every fourth position is left NULL, a recorded abstention, which is what the reports "Abstained" row counts.
-- Candidate ids follow the insert order: 1-2 President, 3-4 Vice President, 5-6 Secretary,
-- 7-8 Treasurer, 9-10 Auditor, 11-12 Public Information Officer.

INSERT INTO votes (`election_id`, `voter_id`, `position`, `candidate_id`, `voted_at`) VALUES
-- voter 3
(1, 3, 'President', 1, NOW()), (1, 3, 'Vice President', 3, NOW()),
(1, 3, 'Secretary', 5, NOW()), (1, 3, 'Treasurer', NULL, NOW()),
(1, 3, 'Auditor', 9, NOW()), (1, 3, 'Public Information Officer', 11, NOW()),
-- voter 4
(1, 4, 'President', 2, NOW()), (1, 4, 'Vice President', 4, NOW()),
(1, 4, 'Secretary', 5, NOW()), (1, 4, 'Treasurer', 7, NOW()),
(1, 4, 'Auditor', 10, NOW()), (1, 4, 'Public Information Officer', 11, NOW()),
-- voter 5
(1, 5, 'President', 1, NOW()), (1, 5, 'Vice President', 4, NOW()),
(1, 5, 'Secretary', 6, NOW()), (1, 5, 'Treasurer', 7, NOW()),
(1, 5, 'Auditor', 9, NOW()), (1, 5, 'Public Information Officer', 12, NOW()),
-- voter 6
(1, 6, 'President', 2, NOW()), (1, 6, 'Vice President', 3, NOW()),
(1, 6, 'Secretary', 5, NOW()), (1, 6, 'Treasurer', NULL, NOW()),
(1, 6, 'Auditor', 10, NOW()), (1, 6, 'Public Information Officer', 12, NOW()),
-- voter 7
(1, 7, 'President', 1, NOW()), (1, 7, 'Vice President', 3, NOW()),
(1, 7, 'Secretary', 6, NOW()), (1, 7, 'Treasurer', 8, NOW()),
(1, 7, 'Auditor', 9, NOW()), (1, 7, 'Public Information Officer', 11, NOW()),
-- voter 8
(1, 8, 'President', 1, NOW()), (1, 8, 'Vice President', 4, NOW()),
(1, 8, 'Secretary', 5, NOW()), (1, 8, 'Treasurer', 7, NOW()),
(1, 8, 'Auditor', NULL, NOW()), (1, 8, 'Public Information Officer', 12, NOW()),
-- voter 9
(1, 9, 'President', 2, NOW()), (1, 9, 'Vice President', 3, NOW()),
(1, 9, 'Secretary', 6, NOW()), (1, 9, 'Treasurer', 8, NOW()),
(1, 9, 'Auditor', 9, NOW()), (1, 9, 'Public Information Officer', 11, NOW()),
-- voter 10
(1, 10, 'President', 1, NOW()), (1, 10, 'Vice President', 4, NOW()),
(1, 10, 'Secretary', 5, NOW()), (1, 10, 'Treasurer', NULL, NOW()),
(1, 10, 'Auditor', 10, NOW()), (1, 10, 'Public Information Officer', 12, NOW()),
-- voter 11
(1, 11, 'President', 2, NOW()), (1, 11, 'Vice President', 3, NOW()),
(1, 11, 'Secretary', 6, NOW()), (1, 11, 'Treasurer', 7, NOW()),
(1, 11, 'Auditor', 9, NOW()), (1, 11, 'Public Information Officer', 11, NOW()),
-- voter 12
(1, 12, 'President', 1, NOW()), (1, 12, 'Vice President', 3, NOW()),
(1, 12, 'Secretary', 5, NOW()), (1, 12, 'Treasurer', 8, NOW()),
(1, 12, 'Auditor', 10, NOW()), (1, 12, 'Public Information Officer', NULL, NOW()),
-- voter 13
(1, 13, 'President', 2, NOW()), (1, 13, 'Vice President', 4, NOW()),
(1, 13, 'Secretary', 6, NOW()), (1, 13, 'Treasurer', 7, NOW()),
(1, 13, 'Auditor', 9, NOW()), (1, 13, 'Public Information Officer', 12, NOW()),
-- voter 14
(1, 14, 'President', 1, NOW()), (1, 14, 'Vice President', 3, NOW()),
(1, 14, 'Secretary', 5, NOW()), (1, 14, 'Treasurer', 8, NOW()),
(1, 14, 'Auditor', NULL, NOW()), (1, 14, 'Public Information Officer', 11, NOW()),
-- voter 15
(1, 15, 'President', 2, NOW()), (1, 15, 'Vice President', 4, NOW()),
(1, 15, 'Secretary', 6, NOW()), (1, 15, 'Treasurer', 7, NOW()),
(1, 15, 'Auditor', 10, NOW()), (1, 15, 'Public Information Officer', 12, NOW()),
-- voter 16
(1, 16, 'President', 1, NOW()), (1, 16, 'Vice President', 3, NOW()),
(1, 16, 'Secretary', 5, NOW()), (1, 16, 'Treasurer', NULL, NOW()),
(1, 16, 'Auditor', 9, NOW()), (1, 16, 'Public Information Officer', 11, NOW()),
-- voter 17
(1, 17, 'President', 2, NOW()), (1, 17, 'Vice President', 3, NOW()),
(1, 17, 'Secretary', 6, NOW()), (1, 17, 'Treasurer', 8, NOW()),
(1, 17, 'Auditor', 10, NOW()), (1, 17, 'Public Information Officer', 12, NOW());

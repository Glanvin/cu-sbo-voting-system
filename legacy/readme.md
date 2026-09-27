## Core Features
* **Secure Authentication:** Verifies voter identity and eligibility using official university email and passwords against campus records.
* **Ballot Management:** Provides administrators with CRUD (Create, Read, Update, Delete) capabilities to manage election rules, configuration parameters (start/end times, vote limits), and candidate lists.
* **Vote Processing & Validation:** Securely records cast ballots while enforcing a strict one-vote-per-user validation rule to prevent manipulation.
* **Automated Tallying:** Instantly calculates final election results once the polling period closes, eliminating human error.
* **Voter Verification:** Generates a downloadable or emailed confirmation receipt (JPG/Email) as verifiable evidence for the student that their ballot was submitted correctly, without revealing their specific choices.
* **Real-time Monitoring:** Features a live dashboard allowing moderators to track participation volume and detect irregularities in real time.

## System Actors
* **Student (Voter):** Authenticates via university ID to view available elections, cast ballots, and receive confirmation receipts.
* **Moderator:** Monitors voting activity in real-time to detect irregularities and provide system oversight.
* **Administrator:** Manages election events, candidates, and critical configuration parameters.

## Technology Stack
* **Backend:** PHP
* **Database:** MySQL
* **Access Environment:** Browser-based access for all students across colleges and departments

## Setup Instructions
1. Ensure you have a local server environment (like XAMPP or WAMP) running with PHP and MySQL.
2. Clone or place the project repository in your web server directory (e.g., `htdocs`).
3. Open `voting-system/config.php` and verify your MySQL credentials (`SERVER_NAME`, `USERNAME`, `PASSWORD`).
4. Navigate to `setup.php` in your web browser (e.g., `http://localhost/voting-system/setup.php`).
5. Click **Run Database Setup** to automatically generate the `db_voting_system` database alongside the `users` and `elections` tables.

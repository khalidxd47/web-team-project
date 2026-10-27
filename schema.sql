-- =====================================================================
-- Computer Science Club Website - Database Schema
-- Course: CS3810T Web Development - Umm Al-Qura University
--
-- Usage: import this file in phpMyAdmin, or run:
--   mysql -u root -p < schema.sql
-- =====================================================================

-- Create the database with full Unicode support (utf8mb4 also covers Arabic text and emoji)
CREATE DATABASE IF NOT EXISTS club_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE club_db;

-- Drop the table first so the script can be re-run to reset the data
DROP TABLE IF EXISTS events;

-- ---------------------------------------------------------------------
-- Table: events
-- Stores every club activity shown on events.php, details.php and dashboard.php
-- ---------------------------------------------------------------------
CREATE TABLE events (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,   -- unique key, generated automatically
  name        VARCHAR(150)  NOT NULL,                  -- event title
  description TEXT          NOT NULL,                  -- full description for the details page
  category    VARCHAR(50)   NOT NULL,                  -- used for filtering and the card badge
  event_date  DATE          NOT NULL,                  -- stored as YYYY-MM-DD for correct sorting
  location    VARCHAR(150)  NOT NULL,                  -- building / hall / online
  image_url   VARCHAR(255)  NOT NULL,                  -- path or URL of the cover image
  PRIMARY KEY (id),
  INDEX idx_category (category),                       -- speeds up category filters
  INDEX idx_event_date (event_date)                    -- speeds up date ordering
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Seed data: 16 events
-- ---------------------------------------------------------------------
INSERT INTO events (name, description, category, event_date, location, image_url) VALUES
('Welcome Back Meetup',
 'Kick off the new semester with the CS Club. Meet the board members, learn about this year''s activities, and find teammates for upcoming competitions.',
 'Social', '2026-09-10', 'College of Computing - Main Hall', 'https://picsum.photos/seed/cs-welcome/800/500'),

('Intro to Git & GitHub Workshop',
 'A hands-on session covering commits, branches, merging, and pull requests. Bring your laptop with Git installed.',
 'Workshop', '2026-09-24', 'Lab 3, Building C', 'https://picsum.photos/seed/cs-git/800/500'),

('Python for Beginners Bootcamp',
 'A three-day bootcamp that teaches Python fundamentals: variables, loops, functions, and file handling, ending with a mini project.',
 'Bootcamp', '2026-10-05', 'Lab 1, Building C', 'https://picsum.photos/seed/cs-python/800/500'),

('Cybersecurity Awareness Seminar',
 'Security experts explain phishing, password hygiene, and how to protect personal data online. Open to all university students.',
 'Seminar', '2026-10-15', 'Auditorium A', 'https://picsum.photos/seed/cs-cyber/800/500'),

('Responsive Web Design Workshop',
 'Learn to build layouts that work on phones, tablets, and desktops using Flexbox, CSS Grid, and media queries.',
 'Workshop', '2026-10-22', 'Lab 3, Building C', 'https://picsum.photos/seed/cs-webdesign/800/500'),

('Problem Solving Contest - Round 1',
 'An individual competitive programming contest with algorithmic problems of increasing difficulty. Top performers qualify for the regional ACPC team.',
 'Competition', '2026-11-02', 'Lab 2, Building C', 'https://picsum.photos/seed/cs-contest1/800/500'),

('AI & Machine Learning Talk',
 'An introduction to how machine learning models are trained, with live demos of image classification and text generation.',
 'Seminar', '2026-11-12', 'Auditorium B', 'https://picsum.photos/seed/cs-ai/800/500'),

('48-Hour Hackathon: Smart Campus',
 'Teams of 3-5 build solutions that improve life on campus. Prizes for the top three teams, with mentors available throughout the event.',
 'Hackathon', '2026-11-26', 'College of Computing - Innovation Hub', 'https://picsum.photos/seed/cs-hackathon/800/500'),

('SQL & Database Design Workshop',
 'From ER diagrams to normalized tables: practice writing SELECT, JOIN, and GROUP BY queries on a real dataset.',
 'Workshop', '2026-12-03', 'Lab 1, Building C', 'https://picsum.photos/seed/cs-sql/800/500'),

('Career Day: Tech in Saudi Arabia',
 'Meet recruiters and engineers from leading Saudi tech companies. Learn about internships, CV tips, and in-demand skills under Vision 2030.',
 'Seminar', '2026-12-14', 'University Conference Center', 'https://picsum.photos/seed/cs-career/800/500'),

('Problem Solving Contest - Round 2',
 'The second qualification round for the ACPC team, featuring team-based problems on graphs, dynamic programming, and math.',
 'Competition', '2027-01-11', 'Lab 2, Building C', 'https://picsum.photos/seed/cs-contest2/800/500'),

('Mobile App Development Bootcamp',
 'A week-long bootcamp on building cross-platform mobile apps, from UI design to publishing on the app store.',
 'Bootcamp', '2027-01-25', 'Lab 4, Building C', 'https://picsum.photos/seed/cs-mobile/800/500'),

('Linux Command Line Workshop',
 'Navigate the terminal with confidence: file management, permissions, pipes, and basic shell scripting.',
 'Workshop', '2027-02-08', 'Lab 3, Building C', 'https://picsum.photos/seed/cs-linux/800/500'),

('Capture The Flag (CTF) Challenge',
 'A beginner-friendly security competition with challenges in web exploitation, cryptography, and forensics. Solve puzzles to capture flags and earn points.',
 'Competition', '2027-02-22', 'Online', 'https://picsum.photos/seed/cs-ctf/800/500'),

('Graduation Projects Showcase',
 'Senior students present their graduation projects to faculty and industry guests. Great inspiration for your own project ideas.',
 'Social', '2027-03-15', 'College of Computing - Main Hall', 'https://picsum.photos/seed/cs-showcase/800/500'),

('End of Year Club Gathering',
 'Celebrate the year''s achievements, honor active members and competition winners, and hand over to the next club board.',
 'Social', '2027-04-20', 'University Gardens', 'https://picsum.photos/seed/cs-gathering/800/500');

# Project Context: Umm Al-Qura University Web Development (CS3810T)
Role: Senior Full-Stack Web Developer & Academic Tutor.
Topic: University Club Website (Computer Science Club).

## Core Requirements & Constraints:
- Architecture: Pure Native Stack (Vanilla PHP, Vanilla JavaScript, Vanilla CSS, MySQL). No heavy external frameworks.
- Pages (7 minimum): index.php, about.php, events.php, gallery.php, contact.php, details.php, dashboard.php.
- Database: MySQL database named `club_db` with table `events` (fields: id, name, description, category, event_date, location, image_url). Must include 15+ pre-seeded records.
- PHP/Backend: PDO with prepared statements. Support full CRUD (Insert, Read, Update, Delete) + live search on dashboard.php.
- CSS Requirements:
  * Minimum 8 distinct color variables (:root).
  * Mix of Inline, Internal, and External CSS.
  * Flexbox for Navbar and Grid for Event/Gallery cards.
  * Responsive Design: Mobile (<768px), Tablet (768px-1024px), Desktop (>1024px) using Media Queries.
  * Interactive effects: CSS Transform, Filter, and Transition.
- JavaScript Requirements:
  * Modal/Card click to display event info dynamically.
  * Strict form validation on contact.php (required fields, valid email regex).
  * Smooth image slideshow with Next/Prev and auto-play in gallery.php.
- Code Standards: Clean, well-commented code. Always include a brief 2-bullet summary explaining the programming logic for student discussion readiness.

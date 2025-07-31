# task_tracker

🛑How to run (XAMPP)🛑

Create the folder: Put everything under C:\xampp\htdocs\task_tracker.

Start Apache + MySQL in XAMPP.

Open phpMyAdmin → Import database.sql.

Edit config.php if your DB password differs.

Visit http://localhost/task_tracker → Register → Login → Add tasks.

🛑Notes / Next steps🛑

The UI is designed to resemble your image: clear columns and status badges.

If you get an error like “Table 'task_tracker.tasks' doesn't exist”, ensure you imported database.sql and the DB name in config.php matches.

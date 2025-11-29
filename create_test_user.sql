-- SQL script to create a test user for login
-- You can run this in phpMyAdmin or MySQL command line

-- Option 1: Create user with plain text password (for testing)
-- Note: This is for development only. Use hashed passwords in production.
INSERT INTO `user_app` (`username`, `password`, `nama`, `role`, `created_at`) 
VALUES ('admin', 'admin123', 'Administrator', 'admin', NOW());

-- Option 2: Create user with hashed password (recommended for production)
-- The password 'admin123' will be hashed using PHP's password_hash()
-- After creating this, you may need to update it using the application's password reset feature
-- or manually hash it using: SELECT PASSWORD('admin123'); (for MySQL) 
-- OR better, use PHP: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO `user_app` (`username`, `password`, `nama`, `role`, `created_at`) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', NOW());
-- Note: The hash above is for 'admin123'. You can generate your own using PHP.

-- To verify the user was created:
-- SELECT id, username, nama, role FROM user_app WHERE username = 'admin';


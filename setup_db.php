<?php
// Usage (CLI):
//   php scripts/setup_db.php yourpassword
// Or via browser: /scripts/setup_db.php?pwd=yourpassword

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'final-blog-project';

$pwd = 'admin123';
if (PHP_SAPI === 'cli') {
    if (!empty($argv[1])) $pwd = $argv[1];
} else {
    if (!empty($_GET['pwd'])) $pwd = $_GET['pwd'];
}

try {
    // Connect without database to create it if needed
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "Database ensured: $dbname\n";

    // Connect to the new database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Create tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `username` VARCHAR(100) NOT NULL UNIQUE,
      `password` VARCHAR(255) NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `posts` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `title` VARCHAR(255) NOT NULL,
      `content` TEXT NOT NULL,
      `author_id` INT NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      CONSTRAINT fk_posts_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    echo "Tables ensured.\n";

    // Insert admin user if not exists
    $username = 'admin';
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo "Admin user already exists.\n";
    } else {
        $hash = password_hash($pwd, PASSWORD_DEFAULT);
        $ins = $pdo->prepare('INSERT INTO users (username, password) VALUES (?,?)');
        $ins->execute([$username, $hash]);
        echo "Inserted admin user with provided password. Username: admin\n";
    }

    // Insert sample posts if none exist
    $c = (int)$pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();
    if ($c === 0) {
        $uid = $pdo->query("SELECT id FROM users WHERE username='admin'")->fetchColumn();
        if ($uid) {
            $ins = $pdo->prepare('INSERT INTO posts (title, content, author_id) VALUES (?,?,?)');
            $ins->execute(['Welcome to the Blog','This is the first sample post. Edit or remove it.', $uid]);
            $ins->execute(['Second Post','Another example post to show listing and pagination.', $uid]);
            echo "Inserted sample posts.\n";
        }
    } else {
        echo "Posts already exist (skipping sample insert).\n";
    }

    echo "Setup complete. Visit / to view the site.\n";

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

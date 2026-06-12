<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/database.php';

// base path for links (adjust if you deploy to different folder)
$basePath = '/final-blog-projec';
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<link rel="stylesheet" href="<?=$basePath?>/css/style.css">
	<title>Final Blog Project</title>
</head>
<body>
<header>
	<nav>
		<a href="<?=$basePath?>/index.php">Home</a> |
		<a href="<?=$basePath?>/search.php">Search</a> |
		<?php if (!empty($_SESSION['user_id'])): ?>
			<a href="<?=$basePath?>/admin/dashboard.php">Dashboard</a> |
			<a href="<?=$basePath?>/auth/logout.php">Logout</a>
		<?php else: ?>
			<a href="<?=$basePath?>/auth/login.php">Login</a> |
			<a href="<?=$basePath?>/auth/register.php">Register</a>
		<?php endif; ?>
	</nav>
</header>
<main>
<?php
// content starts
?>

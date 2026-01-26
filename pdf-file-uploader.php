<?php
session_start();

// --- Configuration ---
$password = "123456";
$root_dir = "docs";

// --- Auth Logic ---
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['pass']) && $_POST['pass'] === $password) {
    $_SESSION['auth'] = true;
}

if (!isset($_SESSION['auth'])) {
    die('
    <!DOCTYPE html>
    <html>
    <head><title>Login</title><meta name="viewport" content="width=device-width, initial-scale=1"></head>
    <body style="font-family:sans-serif; background:#f0f2f5; display:flex; justify-content:center; align-items:center; height:100vh; margin:0;">
        <form method="post" style="background:#fff; padding:30px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.1); text-align:center;">
            <h2 style="margin-top:0;">Access Control</h2>
            <input type="password" name="pass" placeholder="Enter Password" autofocus style="padding:10px; width:200px; border:1px solid #ddd; border-radius:4px; margin-bottom:10px;"><br>
            <input type="submit" value="Login" style="padding:10px 20px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer;">
        </form>
    </body>
    </html>');
}

// --- Path Handling ---
// Sanitize path to prevent parent directory access (../)
$current_path = isset($_GET['path']) ? str_replace(['..', './'], '', $_GET['path']) : '';
$full_path = realpath($root_dir) . DIRECTORY_SEPARATOR . $current_path;
$full_path = rtrim($full_path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

// Ensure we stay inside the root uploads folder
if (strpos($full_path, realpath($root_dir)) !== 0) {
    die("Access Denied.");
}

// --- File Operations ---

// Delete PDF
if (isset($_GET['del'])) {
    $file_to_del = $full_path . basename($_GET['del']);
    if (is_file($file_to_del)) {
        unlink($file_to_del);
    }
    header("Location: ?path=" . urlencode($current_path));
    exit;
}

// Upload PDF
if (isset($_FILES['pdf'])) {
    $file_name = basename($_FILES['pdf']['name']);
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if ($ext === 'pdf') {
        move_uploaded_file($_FILES['pdf']['tmp_name'], $full_path . $file_name);
    }
}

// List contents
$items = array_diff(scandir($full_path), ['.', '..']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Manager</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8f9fa; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f1f1; padding-bottom: 15px; margin-bottom: 20px; }
        .upload-zone { background: #fdfdfd; border: 2px dashed #cbd5e0; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .breadcrumb { font-size: 14px; margin-bottom: 15px; color: #718096; }
        .breadcrumb a { text-decoration: none; color: #3182ce; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; background: #edf2f7; padding: 12px; font-size: 13px; color: #4a5568; }
        td { padding: 12px; border-bottom: 1px solid #edf2f7; }
        .item-link { text-decoration: none; color: #2d3748; font-weight: 500; display: flex; align-items: center; }
        .folder-icon { color: #ecc94b; margin-right: 8px; }
        .file-icon { color: #a0aec0; margin-right: 8px; }
        .btn-del { color: #e53e3e; text-decoration: none; font-size: 12px; font-weight: bold; border: 1px solid #feb2b2; padding: 4px 8px; border-radius: 4px; }
        .btn-del:hover { background: #fff5f5; }
        .logout { background: #4a5568; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2 style="margin:0;">PDF File Explorer</h2>
        <form method="post"><input type="submit" name="logout" value="Logout" class="logout"></form>
    </div>

    <div class="upload-zone">
        <form method="post" enctype="multipart/form-data">
            <span style="font-weight:bold; margin-right:10px;">Upload PDF:</span>
            <input type="file" name="pdf" accept=".pdf" required>
            <input type="submit" value="Upload Now" style="cursor:pointer;">
        </form>
    </div>

    <div class="breadcrumb">
        Current Path: <strong>/<?php echo htmlspecialchars($current_path); ?></strong>
        <?php if ($current_path != ''): ?>
            | <a href="?path=">Root Directory</a>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): 
                $is_folder = is_dir($full_path . $item);
            ?>
            <tr>
                <td>
                    <?php if ($is_folder): ?>
                        <a href="?path=<?php echo urlencode($current_path . $item . '/'); ?>" class="item-link">
                            <span class="folder-icon">📁</span> <?php echo htmlspecialchars($item); ?>
                        </a>
                    <?php else: ?>
                        <div class="item-link">
                            <span class="file-icon">📄</span> <?php echo htmlspecialchars($item); ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td style="text-align:right;">
                    <?php if (!$is_folder): ?>
                        <a href="?path=<?php echo urlencode($current_path); ?>&del=<?php echo urlencode($item); ?>" 
                           class="btn-del" onclick="return confirm('Confirm Delete?')">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
                <tr><td colspan="2" style="text-align:center; color:#a0aec0; padding:30px;">This directory is empty.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

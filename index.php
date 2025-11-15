<?php
require "db.php";

$search = "";

// If a search query exists
if (isset($_GET['q'])) {
    $search = $_GET['q'];
}

// Prepared SQL with LIKE for partial matching
$sql = "SELECT posts.*, users.name AS author_name
        FROM posts
        LEFT JOIN users ON posts.author_id = users.id
        WHERE posts.title LIKE ?
           OR posts.content LIKE ?
        ORDER BY posts.created_at DESC";

$stmt = $conn->prepare($sql);

$search_param = "%" . $search . "%";
$stmt->bind_param("ss", $search_param, $search_param);

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blog with Search</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        .post { border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 5px; }
        .post h2 { margin: 0; }
        .meta { font-size: 14px; color: #555; margin-bottom: 10px; }
    </style>
</head>
<body>

<h1>Blog Posts</h1>

<!-- Search Form -->
<form method="GET" action="index.php">
    <input type="text" name="q" placeholder="Search posts..." value="<?= htmlspecialchars($search) ?>" />
    <button type="submit">Search</button>
</form>

<br>

<!-- Display Results -->
<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
?>
        <div class="post">
            <h2><?= htmlspecialchars($row['title']); ?></h2>

            <p class="meta">
                Author: <strong><?= $row['author_name'] ?? 'Unknown'; ?></strong> |
                Posted on: <?= date('F j, Y, g:i a', strtotime($row['created_at'])); ?>
            </p>

            <p><?= htmlspecialchars($row['content']); ?></p>
        </div>
<?php
    }
} else {
    echo "<p>No results found.</p>";
}
?>

</body>
</html>

<?php
// Include the database connection file
include 'config.php'; // Ensure this file contains $conn

// Handle post submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_post'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $message = $conn->real_escape_string($_POST['message']);
    $sql = "INSERT INTO posts (title, message) VALUES ('$title', '$message')";
    $conn->query($sql);
}

// Handle comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_comment'])) {
    $post_id = $_POST['post_id'];
    $comment = $conn->real_escape_string($_POST['comment']);
    $sql = "INSERT INTO comments (post_id, comment) VALUES ('$post_id', '$comment')";
    $conn->query($sql);
}

// Fetch all posts
$posts = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Support Forum</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Arial', sans-serif; }
        .container { margin-top: 20px; }
        .post-box { background: #fff; padding: 15px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0,0,0,0.1); }
        .comment-box { background: #f9f9f9; padding: 10px; border-radius: 8px; margin-top: 10px; }
        .btn-custom { background: #007bff; color: white; border: none; padding: 10px 15px; border-radius: 5px; }
        .btn-custom:hover { background: #0056b3; }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center text-primary">Community Support Forum</h2>
    
    <!-- Create Post Form -->
    <div class="post-box mt-4">
        <h4>Create a New Discussion</h4>
        <form method="POST">
            <input type="text" name="title" class="form-control mb-2" placeholder="Post Title" required>
            <textarea name="message" class="form-control mb-2" placeholder="Write your message..." required></textarea>
            <button type="submit" name="create_post" class="btn btn-custom">Post</button>
        </form>
    </div>

    <!-- Display Posts -->
    <?php while ($post = $posts->fetch_assoc()): ?>
        <div class="post-box mt-4">
            <h5 class="text-dark"><?php echo htmlspecialchars($post['title']); ?></h5>
            <p><?php echo nl2br(htmlspecialchars($post['message'])); ?></p>
            <small class="text-muted">Posted on <?php echo $post['created_at']; ?></small>

            <!-- Comment Section -->
            <div class="comment-box mt-3">
                <form method="POST">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <textarea name="comment" class="form-control mb-2" placeholder="Write a comment..." required></textarea>
                    <button type="submit" name="add_comment" class="btn btn-custom btn-sm">Comment</button>
                </form>

                <!-- Fetch and Display Comments -->
                <?php
                $post_id = $post['id'];
                $comments = $conn->query("SELECT * FROM comments WHERE post_id = $post_id ORDER BY created_at ASC");
                while ($comment = $comments->fetch_assoc()):
                ?>
                    <div class="comment-box mt-2">
                        <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                        <small class="text-muted">Commented on <?php echo $comment['created_at']; ?></small>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>

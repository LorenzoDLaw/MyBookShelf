<?php 
require_once 'db.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$book = getBookById($id);

if($book === null){
        header('Location: index.php');
        exit;

}

function h(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Bookshelf</title>
        <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <a href="index.php" class = "btn-back">Back to Bookshelf</a>
        <main class = "book_details">
            <div>
                <!-- this div will display the cover of the book  -->
                 <?php if($book["image"]): ?>
                        <img src="book_cover/<?= h($book['image']) ?>"
                        id="book_cover"  
                        alt="Book Cover of <?= h($book['title']) ?>">
                <?php else: ?>
                        <div class="placeholder">No Image</div>
                <?php endif; ?>
            </div>
            <div class = "book_information">
                <!-- this div will display the information about the book  -->
                <h2><?= h($book['title']) ?></h2>
                
                <div class = "inner_book_information">
                    <p>Author: <?= h($book["author"]) ?></p>
                    <p>Category: <?= h($book["category"]) ?></p>
                    <p>Literary Genre: <?= h($book["literary_genre"]) ?></p>
                    <p>Language: <?= h($book["language"]) ?></p>
                    <p>Status: <?= h($book['isread'] ? 'Read' : 'Unread') ?></p>
                </div>
            </div>

            <div class= "description">
                <h3>Description</h3>
                <p><?= h($book["description"]) ?></p>
            </div>

            <a href="add_modify.php?id=<?= (int) $book['id'] ?>" class="btn-custom">Edit</a>
            <!-- Here we delete the book-->
            <form action="api/delete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this book?');">
                <input type="hidden" name="id" value="<?= (int) $book['id'] ?>"> <!-- with input hidden we get the id from the book -->
                <button type="submit" class="btn-custom">Delete Book</button> 
                <!--
                    Here we callthe delete function through the API delete-php
                    and delete.php call the function deleteBook() in db.php
                -->
            </form>
        </main>
</body>
</html>
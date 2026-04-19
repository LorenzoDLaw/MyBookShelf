<?php
require_once dirname(__DIR__) . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    //delete the image file too
    $book = getBookById($id);
    if ($book && !empty($book['image'])) {
        $imagePath = dirname(__DIR__) . '/' . $book['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath); // unlink() deletes a file from the disk
        }
    }

    deleteBook($id); //It go call the function deletebook() in db.php
}

// Redirect back to the library
header('Location: ../index.php');
exit;

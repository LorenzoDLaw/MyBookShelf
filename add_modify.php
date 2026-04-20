<?php   
require_once 'db.php';
//include 'index.php';

//first of all we have to get the --ID-- of the book, from witch we'll get the other data
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
//if we have the id in the get request, we use it, 
//otherwise we check if we have it in the post request, 
//if we don't have it in both of them we set it to 0

$isEdit = $id > 0; //if the id is greater than 0, we are in edit mode, otherwise we are in add mode
$book = null;
if($isEdit){
        $book = getBookById($id);
        if($book === null){
                header("Location: index.php");
                exit();
        }
}
//There I'll store every category and every literary genre, so I can use them in the form
//$allCategories[] = getEnumVal('book', 'category');
//$allGenres[] = getEnumVal('book', 'literary_genre');
// Inside add_modify.php, where you define $data

$categories_array = getEnumValues('book', 'category');
$genres_array     = getEnumValues('book', 'literary_genre');
$language_array     = getEnumValues('book', 'language');

//update the book
if($_SERVER['REQUEST_METHOD'] === 'POST'){
        //update the book in the database
        $data = [
                'id' => $id,
                'title' => $_POST['title'] ?? '',
                'author' => $_POST['author'] ?? '',
                'category' => $_POST['category'] ?? '',
                'literary_genre' => $_POST['literary_genre'] ?? '',
                'language' => $_POST['language'] ?? '',
                'isread' => isset($_POST['isread']) ? 1 : 0, //if the checkbox is checked, we set it to 1, otherwise we set it to 0
                'description' => $_POST['description'] ?? ''
        ];
        //check if ther is a error
        if ($data['title'] === '') {
                $errors[] = 'Il titolo è obbligatorio.';
        }
        if ($data['author'] === '') {
                $errors[] = "L'autore è obbligatorio.";
        }

        $validCategories = $categories_array; // Assuming $categories_array is defined and contains valid categories];
        if (!in_array($data['category'], $validCategories)) {
                $errors[] = 'Categoria non valida.';
        }

        // ---- Handle image upload ----
        // if we are editing the boook we want to keep the existing image path
        $data['image'] = $book['image'] ?? null; 

        // 2. Check if the user actually selected a NEW file in the form
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        try {
                // 3. Upload the new image
                $newImagePath = handleImageUpload($_FILES['image']);
                
                // If we are editing the book cover path, we delete the old one 
                if ($isEdit && !empty($book['image'])) {
                $oldFilePath = __DIR__ . '/' . $book['image'];
                if (file_exists($oldFilePath)) {
                        unlink($oldFilePath); // Deletes the old file
                }
                }
                
                // Update our data array with the NEW path
                $data['image'] = $newImagePath;

        } catch (RuntimeException $e) {
                $errors[] = $e->getMessage();
        }
        }
        // If the 'if' condition above is false (no new file), $data['image'] we will keep the old path

        // ---- Save if no errors ----
        if (empty($errors)) {
                if ($isEdit) {
                        updateBook($id, $data);
                        $savedId = $id;
                } else {
                        $savedId = insertBook($data);
                }

                // LESSON: Redirect after POST prevents duplicate submissions
                //         if the user refreshes the page ("Post/Redirect/Get" pattern).
                header('Location: book.php?id=' . $savedId);
                exit;
        }

}




function h(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
function fieldValue(string $field, ?array $book, string $default = ''): string
{
    // LESSON: After a failed POST, show what the user typed (from $_POST).
    //         On first load in edit mode, show the saved value (from $book).
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        return h(trim($_POST[$field] ?? $default));
    }
    return h($book[$field] ?? $default);
}


$pageTitle = $isEdit ? 'Modify Book' : 'Add Book';
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
        
        <header>
                <a href="index.php" class="btn-custom">Annulla</a>  <!--Per tornare alla pagina di index-->
                <h2><?= h($pageTitle) ?></h2>
        </header>
        <form action="add_modify.php" method="POST" enctype="multipart/form-data" class = "form-addModify"> 
                <!--enctype="multipart/form-data": we use this command for file upload
                in our case we use it for upload the cover of the book-->
                <?php if ($isEdit): ?> <!--we use this for save the id if we are modifying a book-->
                        <input type="hidden" name="id" value="<?= $id ?>">
                <?php endif; ?>
               
                <div> <!-- for the title -->
                        <div id=""></div>
                        <label for="title">Title:</label>
                        <input type="text"
                                name="title"
                                id="title"
                                value="<?= h($book['title'] ?? '') ?>"
                                required>
                </div>
                <div> <!-- for the author -->
                        <label for="author">Author:</label>

                        <input type="text"
                                name="author"
                                id="author"
                                value="<?= h($book['author'] ?? '') ?>"
                                required>
                </div>
                        
                <div>
                        
                        <label for="category">Category:</label>
                        <select name="category" id="category" required>
                                <?php foreach($categories_array as $catgory): ?>
                                <option value="<?= h($catgory) ?>"><?= h($catgory) ?></option>
                                <?php endforeach; ?>
                        </select>
                </div>
                <div>
                        <label for="literary_genre">Literary Genre:</label>
                        <select name="literary_genre" id="literary_genre" required multiple>
                                <?php foreach($genres_array as $genre): ?>
                                <option value="<?= h($genre) ?>"><?= h($genre) ?></option>
                                <?php endforeach; ?>
                        </select>
                </div>

                <div> <!-- Language for the is read -->
                        <label for="language">Lingua:</label>
                        <select id="language" name="language" required >
                                <?php foreach($language_array as $language): ?>
                                <option value="<?= h($language) ?>"><?= h($language) ?></option>
                                <?php endforeach; ?>
                        </select>

                </div>

                <div>
                        <label for="isread">Status of Reading</label>
                        <select id="isread" name="isread" required>
                        <?php
                                $currentRead = $_SERVER['REQUEST_METHOD'] === 'POST'
                                ? (int)($_POST['isread'] ?? 0)
                                : (int)($book['isread'] ?? 1);
                        ?>
                        <option value="0" <?= $currentRead == 0 ? 'selected' : '' ?>>To read</option>
                        <option value="1" <?= $currentRead == 1 ? 'selected' : '' ?>>Read</option>
                        </select>
                </div>

                <div class="form-group">
                        <label for="description">Description</label><br>
                        <textarea id="description" name="description" rows="8"
                                ><?= fieldValue('description', $book) ?></textarea>
                </div>
                <div>
                        <label for="image">Book Cover</label> <br>
                        <?php if ($isEdit && !empty($book['image'])): ?>
                                <input type="file" id="image" name="image" accept="image/*" src="book_cover/<?= h($book['image']) ?>" >
                                <img src="book_cover/<?= h($book['image']) ?>" alt = "book cover" class="book_cover"> 
                        <?php elseif (!$isEdit): ?>
                                <input type="file" id="image" name="image" accept="image/*">
                        <?php endif; ?>
                </div>
                <div> <!-- one for go back to the index page, the other one for save -->      
                        <a href=<?= $isEdit ? "book.php?id=" . $id : "'index.php'" ?> class="btn-custom">Back</a>
                
                        <button type="submit" class="btn-custom">Save</button>
                </div>
        </form>
</body>
</html>
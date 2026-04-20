<?php

require_once 'db.php';
//the possible filters for the search we can use
$filters = [
    'category'       => $_POST['category']       ?? '',
    'literary_genre' => $_POST['literary_genre'] ?? '',
    'language'       => $_POST['language']        ?? '',
    'isread'         => $_POST['isread']          ?? '',
    'search'         => $_POST['search']          ?? '',
];

$books = getBooks($filters, 50, 0);

function h(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}


function getEnumVal($table, $column): array
{
    global $pdo;

    $sql = "SHOW COLUMNS FROM `$table` LIKE '$column'";
    $stmt = $pdo->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        throw new Exception("Column '$column' not found in table '$table'.");
    }

    preg_match("/^enum\((.*)\)$/", $row['Type'], $matches);
    if (!isset($matches[1])) {
        throw new Exception("Column '$column' is not an ENUM type.");
    }

    // Split the enum values and trim the quotes
    return array_map(function ($value) {
        return trim($value, "'");
    }, explode(",", $matches[1]));
    
}


?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookshelf</title>
    <link rel="stylesheet" href="css/style.css">
        
</head>
<body>
    <header>
        <h1>My Bookshelf</h1>
        <a href="add_modify.php" class="btn-custom">Add a book</a>
    </header>
    
   <section method="POST" action = "index.php">
        <form action="index.php" method="post">
            <!-- Filters here I'll write  the code fot the HTML-->
            <input type="text" name="search" id="search" placeholder="Search by title..." >

            <select name="category" id="category"> <!-- Like book, manga, comics, manga -->
                <!-- fetch from database the enum values -->
                <?php foreach(getEnumVal('book', 'category') as $category): ?>
                <option value="<?= h($category) ?>">
                    <?= h($category) ?>
                </option>
                <?php endforeach; ?>
            </select>

            <select name="literary_genre[]" multiple>
                <?php foreach(getEnumVal('book', 'literary_genre') as $genre): ?>
                    <option value="<?= h($genre) ?>"><?= h($genre) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="language" id="language"> <!-- Like Italian, English, Spanish -->
                <!-- fetch from database the enum values -->
                <?php foreach(getEnumVal('book', 'language') as $language): ?>
                <option value="<?= h($language) ?>">
                    <?= h($language) ?>
                </option>
                <?php endforeach; ?>
                
            </select>
                    
            <select name="isread" id="isread">      
                <option value="">All</option>
                <!-- == performs type coercion and compares values after converting them to a common type
                 while === checks both value and type without coercion-->
                <option value="1" <?= $filters["isread"] === "1" ? "selected" : "" ?>>Read</option>
                <option value="0" <?= $filters["isread"] === "0" ? "selected" : "" ?>>Unread</option>
            </select>
            <button type="submit" class="btn-custom">Filtra</button>        
            <a href="index.php" class="btn-custom">Reset</a>
        </form>
   </section> 



   <main class = "book_grid" id = "book_grid">
    <?php if(empty($books)): ?>
        <p>No books found.</p>
    <?php else: ?>
        <?php  foreach($books as $book): ?>
            <!-- Display books here, we'll use a a href, that we will use 
            to go into the book.php, here we will display all the details of the book
            Everything will be contained in a div -->
        <a href="book.php?id=<?= h($book['id']) ?>" class="book_card">

        <!-- Book cover -->
        <?php if($book["image"]): ?>
            <img src="book_cover\<?= h($book['image']) ?>" id="book_cover" 
            alt="<?= h($book['title']) ?>"
            class="book_cover"
            loading="lazy"
            >
        <?php endif; ?>
        <!-- Info about the book -->
        <div class="books_info">
            <span class = "book_title"><?= h($book['title']) ?></span> <br>
            <span class = "book_author"><?= h($book['author']) ?></span> <br>
            <span class= "book_read"><?= h($book['isread'] ? 'Read' : 'Unread') ?></span>
        </div>   
        </a>
        <?php endforeach; ?>
        <?php endif; ?>
        
   </main>
<script src="js/main.js"></script>
</body>
</html>
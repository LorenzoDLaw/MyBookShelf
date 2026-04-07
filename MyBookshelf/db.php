<?php

//Define the constants for the database connection

define('DB_HOST', 'localhost');   // XAMPP always runs MySQL on localhost
define('DB_NAME', 'mybookshelf');
define('DB_USER', 'root');        // Default XAMPP user
define('DB_PASS', '');            // Default XAMPP password is empty

// ---- Create a PDO connection ----
/* PDO (PHP Data Objects) is the way to talk to MySQL.
        it help prevent SQL injection and has cleaner syntax.
        It prevent sql Injection because the data is sent separately from the command.
*/        

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            // These options make PDO throw exceptions on errors
            //         instead of silently failing. Always include them!
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

            // FETCH_ASSOC means query results come back as
            //         associative arrays: $row['title'] instead of $row[0]
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // Emulated prepares OFF = real prepared statements.
            //         This is more secure against SQL injection.
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

/**
 *  Fetch books with optional filters and pagination.
 * @param  array  $filters  Keys: category, literary_genre, language,
 *                          isread, search (title keyword)
 * @param  int    $limit    How many books to return (default 50)
 * @param  int    $offset   How many to skip (for Load More pagination)
 * @return array            Array of book rows
 */
function getBooks(array $filters = [], int $limit = 50, int $offset = 0): array
{
    global $pdo; 

    // Start with the base query
    $sql    = 'SELECT * FROM book WHERE 1=1';
    // WHERE 1=1 is always true — it lets every filter safely
    // start with AND without needing to check if it's the first condition

    $params = []; // We'll collect bound values here

    // We only add a condition if the filter was actually provided.
    //         empty() returns true for "", null, 0, [], etc.
    if (!empty($filters['category'])) {
        $sql      .= ' AND category = ?';
        $params[]  = $filters['category'];
    }
    if (!empty($filters['literary_genre'])) {
        $sql      .= ' AND literary_genre = ?';
        $params[]  = $filters['literary_genre'];
    }
    if (!empty($filters['language'])) {
        $sql      .= ' AND language = ?';
        $params[]  = $filters['language'];
    }
    if (isset($filters['isread']) && $filters['isread'] !== '') {
        $sql      .= ' AND isread = ?';
        $params[]  = (int) $filters['isread']; // cast to int: "0" → 0
    }
    if (!empty($filters['search'])) {
        // '%...%' for search a partial match.
        $sql      .= ' AND title LIKE ?';
        $params[]  = '%' . $filters['search'] . '%';
    }


    $sql .= ' ORDER BY date_added DESC LIMIT ? OFFSET ?';
    $params[] = $limit;
    $params[] = $offset;

    // prepare() compiles the SQL, execute() runs it with values.
    //This two-step process is what makes prepared statements safe.
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(); // Returns ALL rows as an array of arrays
}


/**
 * Fetch a single book by its ID.
 */
function getBookById(int $id): ?array
{
    global $pdo;

    $stmt = $pdo->prepare('SELECT * FROM book WHERE id = ?');
    $stmt->execute([$id]);

    // LESSON: fetch() returns ONE row (or false if not found).
    //         "?: null" converts false → null for cleaner checks.
    return $stmt->fetch() ?: null;
}


/**
 * Insert a new book into the database and return its new ID.
 */
function insertBook(array $data): int
{
    global $pdo;  //with global we can use the $pdo 
     
    $sql = '
        INSERT INTO book
            (title, author, category, literary_genre, language, isread, description, image)
        VALUES
            (:title, :author, :category, :literary_genre, :language, :isread, :description, :image)
    ';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':title'          => $data['title'],
        ':author'         => $data['author'],
        ':category'       => $data['category'],
        ':literary_genre' => $data['literary_genre'],
        ':language'       => $data['language'],
        ':isread'         => (int) $data['isread'],
        ':description'    => $data['description'] ?? null,
        ':image'          => $data['image']        ?? null,
    ]);

    // lastInsertId() gives us the ID MySQL just assigned.
    return (int) $pdo->lastInsertId();
}


/**
 * Update an existing book by ID.
 */
function updateBook(int $id, array $data): void
{
    global $pdo;

    $sql = '
        UPDATE book
        SET title          = :title,
            author         = :author,
            category       = :category,
            literary_genre = :literary_genre,
            language       = :language,
            isread         = :isread,
            description    = :description,
            image          = :image
        WHERE id = :id
    ';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':title'          => $data['title'],
        ':author'         => $data['author'],
        ':category'       => $data['category'],
        ':literary_genre' => $data['literary_genre'],
        ':language'       => $data['language'],
        ':isread'         => (int) $data['isread'],
        ':description'    => $data['description'] ?? null,
        ':image'          => $data['image']        ?? null,
        ':id'             => $id,
    ]);
}


/**
 * Delete a book by ID.
 */
function deleteBook(int $id): void
{
    global $pdo;

    $stmt = $pdo->prepare('DELETE FROM book WHERE id = ?');
    $stmt->execute([$id]);
}


/**
 * Handle image upload. Returns the saved file path, or null.
 *
 * $_FILES contains info about uploaded files.
 *         move_uploaded_file() moves the temp file to your folder.
 */
function handleImageUpload(array $file): ?string
{
    // No file selected
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    // Check the file extension for basic security.
    // Never trust $_FILES['type'] — it can be faked!
    $allowed    = ['jpg', 'jpeg', 'png', 'webp'];
    $ext        = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        throw new RuntimeException('Only jpg, png, webp images are allowed.');
    }

    // uniqid() creates a unique filename so uploads don't overwrite each other.
    $filename   = uniqid('cover_', true) . '.' . $ext;
    $uploadDir  = __DIR__ . '/book_cover/';
    $targetPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Failed to save uploaded image.');
    }

    return '' . $filename; // Store relative path in DB
}


//method for fetch the enum values from the database
function getEnumValues(string $table, string $column): array 
{
    global $pdo; 
    $query = $pdo->prepare("
        SELECT COLUMN_TYPE 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = ? 
          AND TABLE_NAME = ? 
          AND COLUMN_NAME = ?
    ");

    $query->execute([DB_NAME, $table, $column]);
    $row = $query->fetch();

    if (!$row) {
        return [];
    }

    $enumStr = $row['COLUMN_TYPE'];

    /**
     * https://www.w3schools.com/php/php_regex.asp  Regular expressions
     * preg_match_all explained:
     * /       = The start of the regex pattern
     * '       = Match a literal single quote
     * (       = START CAPTURING: Everything inside these parens is what we want to keep
     * [^']  = A "negated character set": Match ANY character EXCEPT a single quote
     * +     = Quantifier: Match one or more of the characters described above
     * )       = END CAPTURING
     * '       = Match the closing literal single quote
     * /       = The end of the regex pattern
     */
    preg_match_all("/'([^']+)'/", $enumStr, $matches);

    return $matches[1] ?? []; 
}
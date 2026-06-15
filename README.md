# MyBookShelf
Self-directed personal library management system for cataloging my personal collection of books, comics, and manga.

---

## Overwiew
 MyBookShelf is single user web application built in PHP and MySql. Create with the goal of learning PHP and MySql. The application prioritizes simplicity, security, and educational value through hands-on learning of core web development patterns.

---

## Technology Stack

| Layer | Technology | Rationale |
|-------|-----------|-----------|
| **Runtime** | PHP 7.4+ | Synchronous model simplifies single-user app logic; excellent MySQL integration |
| **Database** | MySQL 5.7+ | Robust ENUM support for structured metadata; native `INFORMATION_SCHEMA` introspection |
| **Local Dev** | XAMPP | All-in-one Apache + MySQL + PHP; no Docker overhead for personal projects |
| **Frontend** | HTML5 + CSS3 + Vanilla JS | Minimal dependencies; direct DOM manipulation for learning purposes |
| **Data Access** | PDO (PHP Data Objects) | Industry-standard abstraction layer; prepared statements prevent SQL injection |

### Why PHP over Node.js?

This project deliberately chose PHP + XAMPP for:
- **Learning**: Synchronous request/response model clearer for beginners
- **Setup**: XAMPP requires no npm install or build steps
- **Scope**: Personal app (single user) doesn't benefit from Node's async I/O
- **Database**: MySQL integration is native and straightforward in PHP
- **Future**: Node + React REST API identified as a candidate for Phase 7+ scaling

---

## Project Structure

```
mybookshelf/
├── index.php              # Home page: book grid, filtering UI
├── book.php               # Detail page: single book view
├── add_modify.php         # Form page: create/edit books
├── db.php                 # Database abstraction layer (all queries)
├── api/
│   └── delete.php         # Delete endpoint (POST-only)
│   └── load_more.php      # Load More pagination endpoint
├── book_cover/            # Uploaded image directory
├── css/
│   └── style.css          # Styling
├── js/
│   └── main.js            # Client-side interactions
├── db.sql                 # Schema definition
└── README.md              # This file
```
---

## How to setup

### Prerequisites

- **XAMPP** (Apache + MySQL + PHP) — [Download](https://www.apachefriends.org/)
- **MySQL 5.7+**
- **PHP 7.4+**

### Setup Steps

1. **Extract project files** into `htdocs/`:
   ```bash
   cp -r libreria-personale /path/to/xampp/htdocs/
   ```

2. **Start XAMPP** services (Apache + MySQL)

3. **Create database**:
   - Open phpMyAdmin at `http://localhost/phpmyadmin/`
   - Create a new database named `mybookshelf`
   - Import `db.sql`:
     ```sql
     mysql -u root mybookshelf < db.sql
     ```
   - Or paste the SQL schema directly into phpMyAdmin's SQL tab

4. **Verify database connection** in `db.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'mybookshelf');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Empty for default XAMPP
   ```

5. **Create upload directory**:
   ```bash
   mkdir -p book_cover/
   chmod 755 book_cover/
   ```

6. **Access the application**:
   ```
   http://localhost/libreria-personale/
   ```

---

## Database Schema

### Table: `book`

| Column | Type | Constraints | Purpose |
|--------|------|-------------|---------|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| `title` | VARCHAR(255) | NOT NULL | Book title |
| `author` | VARCHAR(255) | NOT NULL | Book author |
| `category` | ENUM | NOT NULL | Type: Book \| Comics \| Manga \| Manhua |
| `literary_genre` | ENUM | NOT NULL | Genre classification (26 options) |
| `language` | ENUM | NOT NULL | Italiano \| English |
| `isread` | TINYINT(1) | DEFAULT 0 | 0 = unread, 1 = read |
| `description` | TEXT | NULL | Custom notes |
| `image` | VARCHAR(255) | NULL | Relative path to cover image |
| `date_added` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Creation timestamp |

---

<?php
/**
 * Database Configuration
 * Hospital Information System
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'hospital_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// Character set
define('DB_CHARSET', 'utf8mb4');

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Log error and show user-friendly message
    error_log("Database Connection Error: " . $e->getMessage());
    die("Database connection failed. Please contact administrator.");
}

/**
 * Helper function to execute prepared statements
 */
function executeQuery($pdo, $sql, $params = []) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Query Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Helper function to get single row
 */
function getRow($pdo, $sql, $params = []) {
    $stmt = executeQuery($pdo, $sql, $params);
    return $stmt ? $stmt->fetch() : false;
}

/**
 * Helper function to get all rows
 */
function getRows($pdo, $sql, $params = []) {
    $stmt = executeQuery($pdo, $sql, $params);
    return $stmt ? $stmt->fetchAll() : false;
}

/**
 * Helper function to insert and get last insert id
 */
function insertAndGetId($pdo, $sql, $params = []) {
    $result = executeQuery($pdo, $sql, $params);
    return $result ? $pdo->lastInsertId() : false;
}

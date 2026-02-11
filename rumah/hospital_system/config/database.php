<?php
/**
 * Database Configuration
 * Hospital Information System
 * Native PHP with PDO
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'hospital_system');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Create PDO instance
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

/**
 * Execute a query and return affected rows
 * 
 * @param PDO $pdo Database connection
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return bool Success status
 */
function executeQuery($pdo, $sql, $params = [])
{
    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Query execution error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get a single row from database
 * 
 * @param PDO $pdo Database connection
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return array|false Single row or false
 */
function getRow($pdo, $sql, $params = [])
{
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Query error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get multiple rows from database
 * 
 * @param PDO $pdo Database connection
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return array Array of rows
 */
function getRows($pdo, $sql, $params = [])
{
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Query error: " . $e->getMessage());
        return [];
    }
}

/**
 * Insert a row and return the last insert ID
 * 
 * @param PDO $pdo Database connection
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return int|false Last insert ID or false
 */
function insertAndGetId($pdo, $sql, $params = [])
{
    try {
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            return $pdo->lastInsertId();
        }
        return false;
    } catch (PDOException $e) {
        error_log("Insert error: " . $e->getMessage());
        return false;
    }
}

/**
 * Count rows in a table with optional conditions
 * 
 * @param PDO $pdo Database connection
 * @param string $table Table name
 * @param string $where WHERE clause (optional)
 * @param array $params Parameters for prepared statement
 * @return int Row count
 */
function countRows($pdo, $table, $where = '', $params = [])
{
    try {
        $sql = "SELECT COUNT(*) as count FROM " . $table;
        if ($where) {
            $sql .= " WHERE " . $where;
        }
        $result = getRow($pdo, $sql, $params);
        return $result ? (int) $result['count'] : 0;
    } catch (PDOException $e) {
        error_log("Count error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Sanitize input data
 * 
 * @param string $data Input data
 * @return string Sanitized data
 */
function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Redirect to a URL
 * 
 * @param string $url URL to redirect to
 */
function redirect($url)
{
    header("Location: " . $url);
    exit;
}

/**
 * Check if user is logged in
 * 
 * @return bool Login status
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']) || isset($_SESSION['admin_id']);
}

/**
 * Get current user type
 * 
 * @return string|null User type or null
 */
function getUserType()
{
    return $_SESSION['user_type'] ?? null;
}

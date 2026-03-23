<?php

function dbpass()
{
    $server_ = 'localhost';
    $database_ = 'lugomax_db2';
    $user_ = 'root';
    $pass_ = '';
    $con = @mysqli_connect($server_, $user_, $pass_, $database_);
    return $con;
}
$k['con'] = dbpass();
function query_sql($query)
{
    global $k;
    $result = mysqli_query($k['con'], $query);
    return $result;
}

/**
 * Database Configuration
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'lugomax_db2');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_URL', 'http://localhost');
define('SITE_NAME', 'Lugomax Logistics');
define('SITE_EMAIL', 'info@lugomax.co.uk');

$site_name = 'Lugomax';
$siteFacebook = 'https://www.facebook.com/';
$siteTwitter = 'https://www.x.com/';
$siteInstagram = 'https://www.instagram.com/';
$siteLinkedin = 'https://www.linkedin.com/';

function getDB()
{
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}

function reduceTextLength($content, $length)
{
    if (strlen($content) < $length) {
        return $content;
    } else if (strlen($content) > $length) {
        return substr($content, 0, $length) . '...';
    }
}

function cleanStatus($status)
{
    return str_replace("_", " ", $status);
}

function formatDeliveryTime($datetime)
{
    $date = new DateTime($datetime);

    // Format: Friday, 24 Jan 2026 by 5:00 PM
    $dayName = $date->format('l');        // Full day name (Friday)
    $dayNum = $date->format('j');         // Day without leading zeros (24)
    $month = $date->format('M');          // Short month (Jan)
    $year = $date->format('Y');           // 4-digit year (2026)
    $time = $date->format('g:i A');       // 12-hour time (5:00 PM)

    return $dayName . ', ' . $dayNum . ' ' . $month . ' ' . $year . ' by ' . $time;
}


function passwordHash($password)
{
    $md5_value = hash('haval160,4', $password, FALSE);
    return $md5_value;
}

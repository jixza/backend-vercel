<?php
/**
 * Test Database Connection untuk Supabase PostgreSQL
 * Jalankan script ini untuk memverifikasi koneksi database sebelum deploy
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables dari .env.vercel
$envFile = __DIR__ . '/../.env.vercel';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') !== 0 && strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, '"\'');
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

echo "🔍 Testing Supabase PostgreSQL Connection...\n";
echo "=====================================\n\n";

// Database configuration
$host = $_ENV['DB_HOST'] ?? 'db.obwzncalwdmfjnkkqpjh.supabase.co';
$port = $_ENV['DB_PORT'] ?? '5432';
$database = $_ENV['DB_DATABASE'] ?? 'postgres';
$username = $_ENV['DB_USERNAME'] ?? 'postgres';
$password = $_ENV['DB_PASSWORD'] ?? '12345678';

echo "📋 Connection Details:\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "Database: $database\n";
echo "Username: $username\n";
echo "Password: " . str_repeat('*', strlen($password)) . "\n\n";

try {
    // Test menggunakan PDO
    $dsn = "pgsql:host=$host;port=$port;dbname=$database;sslmode=require";
    
    echo "🔌 Attempting to connect...\n";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 10,
        PDO::ATTR_PERSISTENT => false,
    ]);
    
    echo "✅ Database connection successful!\n\n";
    
    // Test basic query
    echo "🔍 Testing basic query...\n";
    $stmt = $pdo->query("SELECT version() as version, current_database() as database, current_user as user");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "✅ Query successful!\n";
    echo "📊 Database Info:\n";
    echo "Version: " . $result['version'] . "\n";
    echo "Database: " . $result['database'] . "\n";
    echo "User: " . $result['user'] . "\n\n";
    
    // Test CREATE TABLE permission
    echo "🛠️  Testing CREATE TABLE permission...\n";
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS connection_test (
            id SERIAL PRIMARY KEY,
            test_data VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";
    $pdo->exec($createTableSQL);
    echo "✅ CREATE TABLE permission OK!\n";
    
    // Test INSERT permission
    echo "📝 Testing INSERT permission...\n";
    $insertSQL = "INSERT INTO connection_test (test_data) VALUES (?)";
    $stmt = $pdo->prepare($insertSQL);
    $stmt->execute(['Test connection from ' . date('Y-m-d H:i:s')]);
    echo "✅ INSERT permission OK!\n";
    
    // Test SELECT permission
    echo "📖 Testing SELECT permission...\n";
    $selectSQL = "SELECT COUNT(*) as count FROM connection_test";
    $stmt = $pdo->query($selectSQL);
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✅ SELECT permission OK! (Found $count test records)\n";
    
    // Cleanup test table
    echo "🧹 Cleaning up test table...\n";
    $pdo->exec("DROP TABLE IF EXISTS connection_test");
    echo "✅ Cleanup completed!\n\n";
    
    // Test Laravel specific tables (if they exist)
    echo "🔍 Checking for Laravel tables...\n";
    $tablesSQL = "
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_name IN ('migrations', 'users', 'temporary_patient_tokens')
        ORDER BY table_name
    ";
    $stmt = $pdo->query($tablesSQL);
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "⚠️  No Laravel tables found. You may need to run migrations.\n";
        echo "💡 Run after deployment: php artisan migrate\n";
    } else {
        echo "✅ Found Laravel tables: " . implode(', ', $tables) . "\n";
    }
    
    echo "\n🎉 Database connection test completed successfully!\n";
    echo "📝 Summary:\n";
    echo "- ✅ Connection established\n";
    echo "- ✅ Basic queries working\n";
    echo "- ✅ CREATE/INSERT/SELECT permissions OK\n";
    echo "- ✅ Database is ready for Laravel deployment\n\n";
    
    echo "🚀 Next steps:\n";
    echo "1. Deploy to Vercel: vercel --prod\n";
    echo "2. Run migrations: php artisan migrate (after deployment)\n";
    echo "3. Test API endpoints\n\n";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    
    echo "🔧 Troubleshooting:\n";
    echo "1. Check if Supabase database is running\n";
    echo "2. Verify host: $host\n";
    echo "3. Verify credentials (username/password)\n";
    echo "4. Check firewall/network access\n";
    echo "5. Ensure SSL is enabled\n\n";
    
    echo "💡 Common solutions:\n";
    echo "- Check Supabase dashboard for database status\n";
    echo "- Verify password is correct: 12345678\n";
    echo "- Make sure database is not paused\n";
    echo "- Check if IP is whitelisted (if required)\n\n";
    
    exit(1);
} catch (Exception $e) {
    echo "❌ Unexpected error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
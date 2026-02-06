<?php
/**
 * Migration Script: Personnel-User Account Sync & Employment Type Normalization
 * 
 * Run this script once via browser: http://localhost/dogh_dtr/db/run_personnel_user_sync.php
 * 
 * What it does:
 *   1. Converts all 'Contract of Service' and 'COS / JO' to 'COS' in personnels table
 *   2. Resets ALL existing user_accounts passwords to 'dogh_2026'
 *   3. Creates user_accounts for all personnels that don't have one yet (password: dogh_2026)
 */

// Database configuration - adjust if needed
$host = 'localhost';
$dbname = 'snhs';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Personnel-User Sync Migration</h2>";
    echo "<pre>";

    // ============================================================
    // STEP 1: Normalize employment_type (ENUM column fix)
    // ============================================================
    echo "=== STEP 1: Normalizing employment types ===\n";

    // Check current column type
    $stmt = $pdo->query("SHOW COLUMNS FROM personnels WHERE Field = 'employment_type'");
    $col = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Current column type: {$col['Type']}\n";

    // Show current distribution
    $stmt = $pdo->query("SELECT employment_type, COUNT(*) as cnt FROM personnels GROUP BY employment_type");
    echo "Current values before fix:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $val = $row['employment_type'] === null ? 'NULL' : "'{$row['employment_type']}'";
        echo "  {$val}: {$row['cnt']}\n";
    }
    echo "\n";

    // Step 1a: First, convert 'Contract of Service' and 'COS / JO' to 'COS / JO' 
    // (a valid ENUM value) so nothing is blank
    $stmt = $pdo->prepare("UPDATE personnels SET employment_type = 'COS / JO' WHERE employment_type = 'Contract of Service'");
    $stmt->execute();
    echo "Converted 'Contract of Service' -> 'COS / JO': " . $stmt->rowCount() . " records\n";

    // Step 1b: Fix blank/NULL/empty values -> set to 'COS / JO' (valid ENUM value)
    $stmt = $pdo->prepare("UPDATE personnels SET employment_type = 'COS / JO' WHERE employment_type IS NULL OR employment_type = '' OR LENGTH(employment_type) = 0");
    $stmt->execute();
    echo "Fixed blank/NULL/empty -> 'COS / JO': " . $stmt->rowCount() . " records\n";

    // Step 1c: Now ALTER the column from ENUM to VARCHAR so we can use 'COS'
    $pdo->exec("ALTER TABLE personnels MODIFY COLUMN employment_type VARCHAR(50) NOT NULL DEFAULT 'COS'");
    echo "Altered column from ENUM to VARCHAR(50)\n";

    // Step 1d: Now convert all 'COS / JO' and 'Contract of Service' to 'COS'
    $stmt = $pdo->prepare("UPDATE personnels SET employment_type = 'COS' WHERE employment_type = 'COS / JO'");
    $stmt->execute();
    echo "Converted 'COS / JO' -> 'COS': " . $stmt->rowCount() . " records\n";

    $stmt = $pdo->prepare("UPDATE personnels SET employment_type = 'COS' WHERE employment_type = 'Contract of Service'");
    $stmt->execute();
    echo "Converted 'Contract of Service' -> 'COS': " . $stmt->rowCount() . " records\n";

    // Step 1e: Catch-all - anything not 'Regular' or 'COS' becomes 'COS'
    $stmt = $pdo->prepare("UPDATE personnels SET employment_type = 'COS' WHERE employment_type NOT IN ('Regular', 'COS') OR employment_type IS NULL OR LENGTH(TRIM(employment_type)) = 0");
    $stmt->execute();
    echo "Fixed remaining invalid values -> 'COS': " . $stmt->rowCount() . " records\n";

    // Verify
    $stmt = $pdo->query("SELECT employment_type, COUNT(*) as cnt FROM personnels GROUP BY employment_type");
    echo "\nAfter fix:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  '{$row['employment_type']}': {$row['cnt']}\n";
    }

    // ============================================================
    // STEP 2: Reset ALL existing user passwords to 'dogh_2026'
    // ============================================================
    echo "\n=== STEP 2: Resetting all user passwords to 'dogh_2026' ===\n";

    $hashed_password = password_hash('dogh_2026', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE user_accounts SET password = ?");
    $stmt->execute([$hashed_password]);
    echo "Reset passwords for " . $stmt->rowCount() . " user accounts\n";

    // ============================================================
    // STEP 3: Create user_accounts for personnels without accounts
    // ============================================================
    echo "\n=== STEP 3: Creating user accounts for personnels without accounts ===\n";

    // Get all personnels that don't have a user_account
    $stmt = $pdo->query("
        SELECT p.id, p.email, p.firstname, p.lastname 
        FROM personnels p 
        LEFT JOIN user_accounts ua ON ua.personnel_id = p.id 
        WHERE ua.id IS NULL 
        AND p.email IS NOT NULL 
        AND p.email != ''
    ");
    $personnels_without_accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($personnels_without_accounts) . " personnels without user accounts\n";

    $created = 0;
    $skipped = 0;

    foreach ($personnels_without_accounts as $person) {
        // Check if email already exists in user_accounts
        $check = $pdo->prepare("SELECT id FROM user_accounts WHERE email = ?");
        $check->execute([$person['email']]);
        if ($check->fetch()) {
            echo "  SKIPPED: {$person['email']} (email already exists in user_accounts)\n";
            $skipped++;
            continue;
        }

        // Generate username from email
        $base_username = strstr($person['email'], '@', true);
        $username_candidate = $base_username;
        $counter = 1;

        while (true) {
            $check = $pdo->prepare("SELECT id FROM user_accounts WHERE username = ?");
            $check->execute([$username_candidate]);
            if (!$check->fetch()) break;
            $username_candidate = $base_username . $counter;
            $counter++;
        }

        // Create the account
        $hashed = password_hash('dogh_2026', PASSWORD_BCRYPT);
        $now = date('Y-m-d H:i:s');

        $insert = $pdo->prepare("
            INSERT INTO user_accounts (personnel_id, username, password, email, status, approved_at, created_at) 
            VALUES (?, ?, ?, ?, 'approved', ?, ?)
        ");
        $insert->execute([
            $person['id'],
            $username_candidate,
            $hashed,
            $person['email'],
            $now,
            $now
        ]);

        echo "  CREATED: {$person['email']} -> username: {$username_candidate}\n";
        $created++;
    }

    echo "\nAccounts created: $created\n";
    echo "Accounts skipped: $skipped\n";

    // ============================================================
    // STEP 4: Ensure user_notifications table exists
    // ============================================================
    echo "\n=== STEP 4: Ensuring user_notifications table exists ===\n";

    $stmt = $pdo->query("SHOW TABLES LIKE 'user_notifications'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("
            CREATE TABLE `user_notifications` (
              `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `user_account_id` int(11) UNSIGNED NOT NULL,
              `title` varchar(255) NOT NULL,
              `message` text NOT NULL,
              `type` ENUM('info', 'warning', 'success', 'danger') DEFAULT 'info',
              `is_read` tinyint(1) DEFAULT 0,
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              `read_at` DATETIME NULL,
              PRIMARY KEY (`id`),
              KEY `idx_user_account_id` (`user_account_id`),
              KEY `idx_is_read` (`is_read`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='User notifications'
        ");
        echo "Created user_notifications table\n";
    } else {
        echo "user_notifications table already exists\n";
    }

    // ============================================================
    // SUMMARY
    // ============================================================
    echo "\n=== MIGRATION COMPLETE ===\n";

    // Show current stats
    $stmt = $pdo->query("SELECT employment_type, COUNT(*) as cnt FROM personnels GROUP BY employment_type");
    echo "\nEmployment Type Distribution:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['employment_type']}: {$row['cnt']}\n";
    }

    $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM user_accounts");
    $total_accounts = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];

    $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM personnels");
    $total_personnels = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];

    echo "\nTotal personnels: $total_personnels\n";
    echo "Total user accounts: $total_accounts\n";

    echo "</pre>";
    echo "<p style='color:green; font-weight:bold;'>Migration completed successfully! You can delete this file now.</p>";

} catch (PDOException $e) {
    echo "<h2 style='color:red;'>Migration Error</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}

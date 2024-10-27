<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Overzicht</title>
    <link rel="stylesheet" href="../css/basic.css">
    <link rel="stylesheet" href="../css/overzicht.css">
</head>
<body>

<header>
    <h2 id="title">MetaTech</h2>

    <div class="logo-container">
        <img src="../images/Logo.svg" alt="MetaTech Logo">
    </div>

    <nav class="nav-menu" id="nav-menu">
        <div class="nav-item"><a href="index.php">Home</a></div>
        <div class="nav-item"><a href="overzicht.php">Administratie</a></div>
    </nav>
</header>

<main>

    <div class="container">
        <h1>Overzicht van gegevens</h1>

        <?php
        // Enable error reporting for debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        try {
            // Database connection parameters
            $host = "localhost";
            $user = "root";
            $pass = "root";
            $db = "gegevensverzameling";

            // Connect to the database
            $connection = new mysqli($host, $user, $pass, $db);

            // Check for connection errors
            if ($connection->connect_error) {
                throw new Exception($connection->connect_error);
            }

            // Backup logic when the backup button is clicked
            if (isset($_POST['backup'])) {
                $backupDir = 'backups/';
                
                // Create backup directory if it doesn't exist
                if (!is_dir($backupDir)) {
                    mkdir($backupDir, 0777, true);
                }
            
                // Create a timestamped backup file name
                $backupFile = $backupDir . 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            
                // Command to execute mysqldump for database backup
                $command = "\"C:\\MAMP\\bin\\mysql\\bin\\mysqldump.exe\" --user=$user --password=$pass --host=$host $db > \"$backupFile\" 2>&1";
                
                // Execute the command
                exec($command, $output, $result);
            
                // Check the result of the backup command
                if ($result === 0) {
                    echo "<p>Backup successful. <a href='" . htmlspecialchars($backupFile) . "'>Download backup</a></p>";
                } else {
                    echo "<p>Backup failed. Error: " . implode("\n", $output) . "</p>";
                }
            }

            // Query to select user data
            $query = "SELECT id, email, telefoonnummer, klacht FROM users";
            $statement = $connection->prepare($query);

            // Execute the query and check for errors
            if (!$statement->execute()) {
                throw new Exception($statement->error);
            }

            // Bind the results to variables
            $statement->bind_result($id, $email, $telefoonnummer, $klacht);

            // Output the table headers
            echo "<table>
                    <tr>
                        <th>Email</th>
                        <th>Telefoonnummer</th>
                        <th>Klacht</th>
                        <th>Update</th>
                        <th>Delete</th>
                    </tr>";

            // Fetch and display results in the table
            while ($statement->fetch()) {
                echo "<tr>
                        <td>" . htmlspecialchars($email) . "</td>
                        <td>" . htmlspecialchars($telefoonnummer) . "</td>
                        <td>" . htmlspecialchars($klacht) . "</td>
                        <td><a href='update.php?id=" . htmlspecialchars($id) . "'>Update</a></td>
                        <td><a href='delete.php?id=" . htmlspecialchars($id) . "'>Delete</a></td>
                    </tr>";
            }
            echo "</table>";
        } catch (Exception $e) {
            // Display any errors encountered
            echo "<p>Er is iets misgegaan: " . htmlspecialchars($e->getMessage()) . "</p>";
        } finally {
            // Close the statement and connection
            if (isset($statement)) {
                $statement->close();
            }
            if (isset($connection)) {
                $connection->close();
            }
        }
        ?>

        <!-- Form to trigger database backup -->
        <form action="" method="post">
            <button class="backup" type="submit" name="backup" value="backup">Backup</button>
        </form>

        <!-- Link to export data to PDF -->
        <div class="pdf">
            <a href="export_pdf.php" target="_blank">Export to PDF</a>
        </div>

    </div>

</main>

<footer>
    <!-- Add footer content if needed -->
</footer>

</body>
</html>

<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize variables to hold form data
$email = $telefoonnummer = $klacht = "";

// Check if the form was submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Database connection parameters
        $host = "localhost";
        $gebruiker = "root";
        $password = "root";
        $database = "gegevensverzameling";

        // Collect and validate POST data
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL); // Validate email format
        $telefoonnummer = preg_replace('/[^0-9]/', '', $_POST['telefoonnummer']); // Strip non-numeric characters
        $klacht = trim($_POST['klacht']); // Trim whitespace from klacht

        // Check if any fields are empty
        if (!$email || empty($telefoonnummer) || empty($klacht)) {
            throw new Exception("Alle velden moeten worden ingevuld."); // Throw an exception if fields are empty
        }

        // Create a database connection
        $connectie = new mysqli($host, $gebruiker, $password, $database);

        // Check for connection errors
        if ($connectie->connect_error) {
            throw new Exception($connectie->connect_error);
        }

        // Prepare the SQL statement for inserting data
        $query = "INSERT INTO users (email, telefoonnummer, klacht) VALUES (?, ?, ?)";
        $statement = $connectie->prepare($query); // Prepare the statement
        $statement->bind_param("sis", $email, $telefoonnummer, $klacht); // Bind parameters to the statement

        // Execute the statement and check for success
        if ($statement->execute()) {
            // Redirect to the same page with a success parameter
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit(); // Exit to prevent further execution
        } else {
            throw new Exception("Databasefout: " . $statement->error); // Throw an exception for execution errors
        }
    } catch (Exception $e) {
        // Display any errors encountered
        echo "Oepsie: " . htmlspecialchars($e->getMessage());
    } finally {
        // Close the statement and connection if they were set
        if (isset($statement)) {
            $statement->close();
        }
        if (isset($connectie)) {
            $connectie->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="../css/basic.css">
    <link rel="stylesheet" href="../css/index.css">
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
        <h1>Persoonlijke gegevens</h1>
        <!-- Form to submit personal data -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Vul je e-mail in" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="telefoonnummer">Telefoonnummer:</label>
            <input type="tel" id="telefoonnummer" name="telefoonnummer" placeholder="Vul je telefoonnummer in" value="<?php echo htmlspecialchars($telefoonnummer); ?>" required>

            <label for="klacht">Klacht:</label>
            <input type="text" id="klacht" name="klacht" placeholder="Vul je klacht in" value="<?php echo htmlspecialchars($klacht); ?>" required>

            <input id="button" type="submit" value="Toevoegen"> <!-- Submit button -->
        </form>
    </div>
</main>

<footer>
    <!-- Footer content can be added here -->
</footer>

<script src="../js/javascript.js"></script>
<script src="../js/popup.js"></script>
</body>
</html>

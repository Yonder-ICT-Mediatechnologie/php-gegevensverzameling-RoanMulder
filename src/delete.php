<?php

$methodType = $_SERVER['REQUEST_METHOD']; // Get the HTTP request method

// Check if the method is GET and the 'id' parameter is set
if ($methodType == 'GET' && isset($_GET['id'])) { 
    try {
        // Database connection parameters
        $host = "localhost"; // Hostname for the database connection
        $username = "root"; // Username for the database connection
        $password = "root"; // Password for the database connection
        $database = "gegevensverzameling"; // Name of the database

        $Id = $_GET['id']; // Retrieve the value of the 'id' parameter

        // Create a new MySQLi connection
        $connection = new mysqli($host, $username, $password, $database); 

        // Check for connection errors
        if ($connection->connect_error) { 
            throw new Exception($connection->connect_error); // Throw an exception if connection fails
        }

        // SQL query to delete a user based on the ID
        $query = "DELETE FROM users WHERE id = ?"; 

        // Prepare the SQL statement
        $statement = $connection->prepare($query); 
        $statement->bind_param("i", $Id); // Bind the parameter to the prepared statement

        // Execute the prepared statement and check for errors
        if (!$statement->execute()) { 
            throw new Exception($statement->error); // Throw an exception if execution fails
        }

        // Redirect the user to the overview page after deletion
        header("Location: overzicht.php"); 
        exit(); // Stop further script execution after redirection
    } catch (Exception $e) { // Catch any exceptions
        // Display an error message if an exception occurs
        echo "Er is een fout opgetreden: " . $e->getMessage(); 
    } finally { // Code that runs regardless of whether an exception was thrown
        if ($statement) { // Check if the statement is set
            $statement->close(); // Close the prepared statement
        }
        if ($connection) { // Check if the database connection is set
            $connection->close(); // Close the database connection
        }
    }
}
?>

<?php

namespace PhpGegevensverzameling;

use Dompdf\Dompdf;

class ExportPdf {
    public function generate() {
        // Instantiate Dompdf
        $dompdf = new Dompdf();

        // Build the HTML content for the PDF
        $html = '<h1>Gegevens Overzicht</h1>';
        $html .= '<table border="1">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Telefoonnummer</th>
                            <th>Klacht</th>
                        </tr>
                    </thead>
                    <tbody>';

        // Database connection parameters
        $host = "localhost";
        $user = "root";
        $pass = "root";
        $db = "gegevensverzameling";

        // Create a connection to the database
        $connection = new \mysqli($host, $user, $pass, $db);

        // Prepare the SQL query to fetch data
        $query = "SELECT email, telefoonnummer, klacht FROM users";
        $statement = $connection->prepare($query);

        // Execute the prepared statement
        if ($statement->execute()) {
            // Bind results to variables
            $statement->bind_result($email, $telefoonnummer, $klacht);
            
            // Fetch each row and append it to the HTML
            while ($statement->fetch()) {
                $html .= "<tr>
                           <td>" . htmlspecialchars($email) . "</td>
                           <td>" . htmlspecialchars($telefoonnummer) . "</td>
                           <td>" . htmlspecialchars($klacht) . "</td>
                         </tr>";
            }
        }

        // Close the connection
        $statement->close();
        $connection->close();

        // Close the HTML table
        $html .= '</tbody></table>';

        // Load the HTML content into Dompdf
        $dompdf->loadHtml($html);

        // Set the paper size and orientation (A4, portrait)
        $dompdf->setPaper('A4', 'portrait');

        // Render the PDF from the loaded HTML
        $dompdf->render();

        // Output the generated PDF to the browser
        // Set "Attachment" => false to open in the browser, true to download
        $dompdf->stream("gegevens_overzicht.pdf", ["Attachment" => false]);
    }
}

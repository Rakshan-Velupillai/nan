<?php
include 'db_config.php';
require 'lib/fpdf.php';  // Path to FPDF library

// Get user input
$name = $_POST['name'];
$institution = $_POST['institution'];
$purpose = $_POST['purpose'];
$template = $_POST['template'];

// Save to database
$stmt = $conn->prepare("INSERT INTO bonafide_requests (name, institution, purpose, template) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $institution, $purpose, $template);
$stmt->execute();
$stmt->close();
$conn->close();

// Generate PDF
class PDF extends FPDF {
    function Header() {
        // Add header to match the template
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Bonafide Certificate', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Customize content based on the selected template
if ($template == "template1") {
    $pdf->Cell(0, 10, "This is to certify that $name is a bonafide student of $institution.", 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 10, "Purpose: $purpose", 0, 1);
} elseif ($template == "template2") {
    $pdf->Cell(0, 10, "Bonafide Certificate", 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->MultiCell(0, 10, "This certifies that $name, currently studying at $institution, is a valid and bonafide student. The purpose of this certificate is stated as follows: $purpose.");
} else {
    $pdf->Cell(0, 10, "Certified that $name is enrolled at $institution.", 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 10, "Purpose: $purpose", 0, 1);
}

$pdf->Ln(20);
$pdf->Cell(0, 10, "Signature of the Authority", 0, 1, 'R');
$pdf->Cell(0, 10, "Date: " . date('Y-m-d'), 0, 1, 'R');

// Output PDF for download
$pdf->Output("D", "Bonafide_Certificate_$name.pdf");
?>

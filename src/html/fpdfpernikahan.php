<?php
    include "library/fpdf.php";

    $servername = "localhost";
    $username = "root"; // Ganti dengan username database Anda
    $password = ""; // Ganti dengan password database Anda
    $dbname = "datapernikahananak"; // Ganti dengan nama database Anda

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM datapernikahan";
    $result = $conn->query($sql);

    $pdf = new FPDF();

    // Add a page
    $pdf->AddPage();
    
    // Set font properties
    $pdf->SetFont('Arial', 'B', 16);
    
    // Add a title
    $pdf->Cell(0, 10, 'Data Pernikahan', 0, 1, 'C'); 
    // Set font properties for data rows
    $pdf->SetFont('Arial', '', 10);
    
    // Add data rows from the database
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(45, 6, 'NIK Suami:', 0);
        $pdf->MultiCell(0, 6, $row['nik_suami'], 1);
        
        $pdf->Cell(45, 6, 'NIK Istri:', 0);
        $pdf->MultiCell(0, 6, $row['nik_istri'], 1);
        
        $pdf->Cell(45, 6, 'Nama Suami:', 0);
        $pdf->MultiCell(0, 6, $row['nama_suami'], 1);
        
        $pdf->Cell(45, 6, 'Nama Istri:', 0);
        $pdf->MultiCell(0, 6, $row['nama_istri'], 1);
        
        $pdf->Cell(45, 6, 'TTL Suami:', 0);
        $pdf->MultiCell(0, 6, $row['ttl_suami'], 1);
        
        $pdf->Cell(45, 6, 'TTL Istri:', 0);
        $pdf->MultiCell(0, 6, $row['ttl_istri'], 1);
        
        $pdf->Cell(45, 6, 'Usia Suami:', 0);
        $pdf->MultiCell(0, 6, $row['usia_suami'], 1);
        
        $pdf->Cell(45, 6, 'Usia Istri:', 0);
        $pdf->MultiCell(0, 6, $row['usia_istri'], 1);
        
        $pdf->Cell(45, 6, 'Tanggal Input:', 0);
        $pdf->MultiCell(0, 6, date('Y-m-d', strtotime($row['tanggal_penginputan'])), 1);
        
        $pdf->Cell(45, 6, 'Pendidikan Terakhir Suami:', 0);
        $pdf->MultiCell(0, 6, $row['pendidikanterakhir_suami'], 1);
        
        $pdf->Cell(45, 6, 'Pendidikan Terakhir Istri:', 0);
        $pdf->MultiCell(0, 6, $row['pendidikanterakhir_istri'], 1);
        
        $pdf->Cell(45, 6, 'Tanggal Nikah:', 0);
        $pdf->MultiCell(0, 6, date('Y-m-d', strtotime($row['tanggal_nikah'])), 1);
        
        $pdf->Cell(45, 6, 'Alamat Nikah:', 0);
        $pdf->MultiCell(0, 6, $row['alamat_nikah'], 1);

        $pdf->Cell(45, 6, 'Saksi Nikah:', 0);
        $pdf->MultiCell(0, 6, $row['saksi_nikah'], 1);
        
        $pdf->Cell(45, 6, 'Faktor Pernikahan:', 0);
        $pdf->MultiCell(0, 6, $row['faktor_pernikahan'], 1);
        
        $pdf->Cell(45, 6, 'Kabupaten:', 0);
        $pdf->MultiCell(0, 6, $row['kabkota_kua'], 1);
        
        $pdf->Ln(); // Move to the next line
    }
    
    // Output the PDF to the browser
    $pdf->Output();
?>

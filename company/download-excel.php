<?php
require_once('PHPExcel/Classes/PHPExcel.php');



// Create a new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set the active sheet
$objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getActiveSheet();

// Add headers
$sheet->setCellValue('A1', 'Candidate');
$sheet->setCellValue('B1', 'Highest Qualification');
$sheet->setCellValue('C1', 'Age');
$sheet->setCellValue('D1', 'Skills');
$sheet->setCellValue('E1', 'City');
$sheet->setCellValue('F1', 'State');

// Fetch data from the database
$sql = "SELECT users.* FROM job_post INNER JOIN apply_job_post ON job_post.id_jobpost=apply_job_post.id_jobpost INNER JOIN users ON users.id_user=apply_job_post.id_user WHERE apply_job_post.id_company='$_SESSION[id_company]' GROUP BY users.id_user";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $rowIndex = 2; // Start from row 2

  while ($row = $result->fetch_assoc()) {
    $skills = $row['skills'];
    $skills = explode(',', $skills);

    // Write data to the Excel sheet
    $sheet->setCellValue('A' . $rowIndex, $row['firstname'] . ' ' . $row['lastname']);
    $sheet->setCellValue('B' . $rowIndex, $row['qualification']);
    $sheet->setCellValue('C' . $rowIndex, $row['age']);
    $sheet->setCellValue('D' . $rowIndex, implode(', ', $skills));
    $sheet->setCellValue('E' . $rowIndex, $row['city']);
    $sheet->setCellValue('F' . $rowIndex, $row['state']);

    $rowIndex++;
  }
}

// Set the column widths
$sheet->getColumnDimension('A')->setWidth(20);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('C')->setWidth(10);
$sheet->getColumnDimension('D')->setWidth(30);
$sheet->getColumnDimension('E')->setWidth(20);
$sheet->getColumnDimension('F')->setWidth(20);

// Set the filename and mime type for the download
$filename = 'resume_database_' . date('Y-m-d') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Save the Excel file to the browser
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit();

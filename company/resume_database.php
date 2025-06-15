<?php
session_start();

if (empty($_SESSION['id_company'])) {
    header("Location: ../login.php");
    exit();
}

require_once("../db.php");

$pageTitle = "Resume Database | JobSeek";

include('../shared/header_dashboard.php');

// Fetch data for Grid.js
$sql = "SELECT candidates.* FROM jobs 
        INNER JOIN applications ON jobs.job_id = applications.job_id  
        INNER JOIN candidates ON candidates.candidate_id = applications.candidate_id 
        WHERE applications.company_id = '$_SESSION[id_company]' 
        GROUP BY candidates.candidate_id";
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            $row['first_name'] . ' ' . $row['last_name'],
            $row['age'],
            $row['address'],
            $row['education'],
            $row['experience'],
            $row['skills'],
            // Resume link HTML
            '<a href="../uploads/resume/' . $row['resume'] . '" download="' . $row['first_name'] . ' ' . $row['last_name'] . ' Resume"><i class="fa fa-file-pdf-o"></i></a>'
        ];
    }
}
?>

<div class="col-md-9 bg-white padding-2">
    <h2><i>Applications Database</i></h2>
    <p>In this section you can download individual resumes by clicking the PDF icon, or download all candidate details as a CSV file using the button below.</p>

    <div id="gridTable"></div>

    <button id="downloadBtn" class="btn btn-primary mt-3">Download CSV</button>
</div>

</section>
</div>

<?php include('../footer.php'); ?>

<!-- Include Grid.js CSS and JS -->
<link href="https://unpkg.com/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" />
<script src="https://unpkg.com/gridjs/dist/gridjs.umd.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const grid = new gridjs.Grid({
        columns: [
            "Full Name",
            "Age",
            "Address",
            "Education",
            "Experience",
            "Skills",
            {
              name: "Download Resume",
              formatter: (cell) => gridjs.html(cell),
              sortable: false,
              search: false
            }
        ],
        data: <?php echo json_encode($data); ?>,
        pagination: {
            enabled: true,
            limit: 10
        },
        search: true,
        sort: true,
        resizable: true,
        fixedHeader: true
    }).render(document.getElementById("gridTable"));

    document.getElementById("downloadBtn").addEventListener("click", function () {
        const csvRows = [];

        // CSV Header (excluding 'Download Resume')
        const headers = ["Full Name", "Age", "Address", "Education", "Experience", "Skills"];
        csvRows.push(headers.join(","));

        // Data rows
        <?php foreach ($data as $row): ?>
            <?php
                // Slice to exclude resume link (7th element)
                $csvRow = array_slice($row, 0, 6);
                $escapedRow = array_map(function($val) {
                    return '"' . str_replace('"', '""', $val) . '"';
                }, $csvRow);
                $csvLine = implode(",", $escapedRow);
            ?>
            csvRows.push(<?php echo json_encode($csvLine); ?>);
        <?php endforeach; ?>

        // Create and download CSV file
        const blob = new Blob([csvRows.join("\n")], {type: "text/csv;charset=utf-8;"});
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "resume_database.csv";
        link.click();
    });
});
</script>

</body>
</html>

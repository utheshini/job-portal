<?php
session_start();
require_once("db.php");

// Set dynamic page title
$pageTitle = "Company Profile | JobSeek";

// Include header
include('header.php');
?>

<div class="content-wrapper" style="margin-left: 0px;">
<?php
// Validate and sanitize 'id' parameter
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $companyId = (int) $_GET['id'];

    // Fetch company details securely using company id
    $sql = "SELECT * FROM companies WHERE company_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $companyId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
?>
    <section id="company-details" class="content-header">
        <div class="container">
            <div class="row">          
                <div class="col-md-12 bg-white padding-2">
                    <div class="col-md-3">
                        <div class="thumbnail">
                            <img src="uploads/logo/<?php echo htmlspecialchars($row['logo']); ?>" alt="Company Logo">
                        </div>
                    </div>
                    <div class="pull-left">
                        <h2><b><i><?php echo htmlspecialchars($row['company_name']); ?></i></b></h2>
                    </div>

                    <div class="clearfix"></div>
                    <hr>

                    <div>
                        <h4><b>About Us</b></h4>
                        <h4><?php echo nl2br(htmlspecialchars($row['about_company'])); ?></h4>
                    </div>
                    <div>
                        <div><span class="margin-right-10"><i class="fa fa-map-marker text-green"></i><b> Company Location: </b> <?php echo htmlspecialchars($row['address']); ?></span></div>
                        <div><span class="margin-right-10"><i class="fa fa-envelope text-green"></i><b> Email: </b> <?php echo htmlspecialchars($row['email']); ?></span></div>
                        <div><span class="margin-right-10"><i class="fa fa-phone text-green"></i><b> Phone: </b> <?php echo htmlspecialchars($row['contact_no']); ?></span></div>
                        <div>
                            <?php if (!empty($row['website'])): ?>
                                <span class="margin-right-10">
                                    <i class="fa fa-globe text-green"></i>
                                    <b>Website: </b>
                                    <a href="<?php echo htmlspecialchars($row['website']); ?>" target="_blank" rel="noopener noreferrer">
                                        <?php echo htmlspecialchars($row['website']); ?>
                                    </a>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
    } else {
        echo "<div class='container'><p class='text-danger'>Company not found.</p></div>";
    }

    $stmt->close();
} else {
    echo "<div class='container'><p class='text-danger'>Invalid company ID.</p></div>";
}
?>
</div>

<?php include('footer.php'); ?>

</body>
</html>

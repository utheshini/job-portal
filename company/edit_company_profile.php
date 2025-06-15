<?php
session_start();

if (empty($_SESSION['id_company'])) {
    header("Location: ../login.php");
    exit();
}

require_once("../db.php");

$pageTitle = "Edit Profile | JobSeek";

include('../shared/header_dashboard.php');

// Fetch city list for dropdown
$cities_query = "SELECT city_name FROM cities";
$cities_result = $conn->query($cities_query);
$cities = [];
if ($cities_result && $cities_result->num_rows > 0) {
    while ($row = $cities_result->fetch_assoc()) {
        $cities[] = $row['city_name'];
    }
}
?>

<div class="col-md-9 bg-white padding-2">
    <h2><i>Edit Company Profile</i></h2>
    <h4>In this section you can change your company details</h4><br>

    <?php
    // Show success alert and redirect to dashboard if profile updated
    if (isset($_SESSION['updateSuccess']) && $_SESSION['updateSuccess']) {
        echo "<script>
            alert('Profile updated successfully!');
            window.location.href = 'index.php';
        </script>";
        unset($_SESSION['updateSuccess']);
    }
    ?>

    <div class="row">
        <!-- Company Profile Update Form -->
        <form action="update_company_profile.php" method="post" enctype="multipart/form-data">
            <?php
            // Fetch current company details
            $sql = "SELECT * FROM companies WHERE company_id ='" . $_SESSION['id_company'] . "'";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
            ?>
                    <div class="col-md-6 latest-job">
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" class="form-control input-lg" name="companyname" value="<?php echo htmlspecialchars($row['company_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Website</label>
                            <input type="text" class="form-control input-lg" name="website" value="<?php echo htmlspecialchars($row['website']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input type="email" class="form-control input-lg" id="email" placeholder="Email" value="<?php echo htmlspecialchars($row['email']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>About Me</label>
                            <textarea class="form-control input-lg" rows="4" name="aboutme" required><?php echo htmlspecialchars($row['about_company']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-flat btn-success">Update Company Profile</button>
                        </div>
                    </div>

                    <div class="col-md-6 latest-job">
                        <div class="form-group">
                            <label for="contactno">Contact Number</label>
                            <input type="text" class="form-control input-lg" id="contactno" name="contactno" placeholder="Contact Number" maxlength="10" value="<?php echo htmlspecialchars($row['contact_no']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="city">City</label>
                            <select class="form-control input-lg" id="city" name="city" required>
                                <option value="">Select City</option>
                                <?php foreach ($cities as $city) { ?>
                                    <option value="<?php echo htmlspecialchars($city); ?>" <?php if ($city == $row['city']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($city); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea class="form-control input-lg" rows="3" name="address" required><?php echo htmlspecialchars($row['address']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Change Company Logo</label>
                            <input type="file" name="image" class="btn btn-default" accept="image/png, image/jpeg, image/jpg">
                            <?php if (!empty($row['logo'])) { ?>
                                <img src="../uploads/logo/<?php echo htmlspecialchars($row['logo']); ?>" class="img-responsive" style="max-height: 200px; max-width: 200px;">
                            <?php } ?>
                        </div>
                    </div>
            <?php
                }
            }
            ?>
        </form>
    </div>

    <!-- Upload Error Message -->
    <?php if (isset($_SESSION['uploadError'])) { ?>
        <div class="row">
            <div class="col-md-12 text-center text-danger">
                <?php echo htmlspecialchars($_SESSION['uploadError']); ?>
            </div>
        </div>
    <?php unset($_SESSION['uploadError']);
    } ?>

</div> 
</div>
</div>
</section>
</div>

<?php include('../footer.php'); ?>

</body>
</html>

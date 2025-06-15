<?php
// To Handle Session Variables on This Page
session_start();

// Include database connection
require_once("db.php");

// Set dynamic page title
$pageTitle = "Jobseek - Find your dream job";

// Include header
include('header.php');
?>

<!-- Main content -->
<div class="content-wrapper" style="margin-left: 0px;">

  <!-- Hero Banner -->
  <section class="content-header bg-main">
    <div class="container">
      <div class="row">
        <div class="col-md-12 text-center index-head">
          <h1>Find Your <strong>Dream Job</strong></h1>
          <p>Apply Now!</p>
          <p><a class="btn btn-success btn-lg" href="jobs.php" role="button">Search Jobs</a></p>
        </div>
      </div>
    </div>
  </section>
  <br>

  <!-- Featured Jobs Section -->
  <section class="content-header">
    <div class="container">
      <div class="row">
        <div class="col-md-12 latest-job margin-bottom-20">
          <h1 class="text-center">Featured Jobs</h1>            

          <?php
          // Retrieve latest 10 job posts with company details where deadline is still active
          $sql = "SELECT j.*, c.company_name, c.logo 
                  FROM jobs j 
                  JOIN companies c ON j.company_id = c.company_id 
                  WHERE j.deadline >= CURDATE() 
                  ORDER BY j.posted_date DESC 
                  LIMIT 10";

          $stmt = $conn->prepare($sql);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
              $logoPath = "uploads/logo/" . htmlspecialchars($row['logo']);
          ?>
              <div class="attachment-block clearfix">
                <img class="attachment-img" src="<?php echo htmlspecialchars($logoPath); ?>" alt="Company Logo">
                <div class="attachment-pushed">
                  <h4 class="attachment-heading">
                    <a href="view_job.php?id=<?php echo htmlspecialchars($row['job_id']); ?>">
                      <?php echo htmlspecialchars($row['job_title']); ?>
                    </a>
                    <span class="attachment-heading pull-right">
                      LKR<?php echo htmlspecialchars($row['max_salary']); ?>/Month
                    </span>
                  </h4>
                  <h5 class="text-muted"><?php echo htmlspecialchars($row['company_name']); ?></h5>
                  <div class="attachment-text">
                    <strong>
                      <p>
                        <span class="margin-right-10">
                          <i class="fa fa-clock-o text-green"></i> <?php echo htmlspecialchars($row['max_age']); ?> Max Age
                        </span>
                        <span class="margin-right-10">
                          <i class="fa fa-suitcase text-green"></i> <?php echo htmlspecialchars($row['job_type']); ?>
                        </span>
                        <span class="margin-right-10">
                          <i class="fa fa-map-marker text-green"></i> <?php echo htmlspecialchars($row['location']); ?>
                        </span>
                        <span class="margin-right-10">
                          <i class="fa fa-drivers-license-o text-green"></i> <?php echo htmlspecialchars($row['category']); ?>
                        </span>
                        <i class="fa fa-calendar-times-o text-green"></i> <?php echo date("d-M-Y", strtotime($row['deadline'])); ?>
                      </p>
                    </strong>
                  </div>
                </div>
              </div>
          <?php
            endwhile;
          else:
            echo "<p class='text-center'>No featured jobs available at the moment.</p>";
          endif;
          ?>
        </div> <!-- End of col-md-12 -->
      </div> <!-- End of row -->
    </div> <!-- End of container -->
  </section>
</div> <!-- End of content-wrapper -->

<?php include('footer.php'); ?>

</body>
</html>
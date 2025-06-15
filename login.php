<?php 
session_start(); // Start session

// Redirect to homepage if user is already logged in
if (isset($_SESSION['id_candidate']) || isset($_SESSION['id_company'])) { 
  header("Location: index.php");
  exit();
}

// Set dynamic page title
$pageTitle = "Login | JobSeek";

// Include header
include('header.php');
?>

<!-- Content Wrapper -->
<div class="content-wrapper" style="margin-left: 0px;">

  <!-- Page Header Section -->
  <section class="content-header">
    <div class="container">
      
      <!-- Login Options -->
      <div class="row latest-job margin-top-50 margin-bottom-20">
        <h1 class="text-center margin-bottom-20">Account Login</h1>

        <!-- Candidate Login Box -->
        <div class="col-md-6 latest-job">
          <div class="small-box bg-yellow padding-5">
            <div class="inner">
              <h3 class="text-center">Candidate</h3>
            </div>
            <a href="login_candidate.php" class="small-box-footer">
              Login <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Employer Login Box -->
        <div class="col-md-6 latest-job">
          <div class="small-box bg-red padding-5">
            <div class="inner">
              <h3 class="text-center">Employer</h3>
            </div>
            <a href="login_company.php" class="small-box-footer">
              Login <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

      </div>
    </div>
  </section>

</div> <!-- End of content-wrapper -->

<?php include('footer.php'); ?>

</body>
</html>

<?php 
session_start(); // Start session

// Redirect to homepage if user is already logged in
if(isset($_SESSION['id_candidate']) || isset($_SESSION['id_company'])) { 
  header("Location: index.php");
  exit();
}
// Set dynamic page title
$pageTitle = "Sign Up | JobSeek";

// Include header
include('header.php');
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="margin-left: 0px;">

   <!-- Section: Sign Up options -->
    <section class="content-header">
      <div class="container">
        <div class="row latest-job margin-top-50 margin-bottom-20">
          <h1 class="text-center margin-bottom-20">Sign Up</h1>

          <!-- Candidate Registration Box -->
          <div class="col-md-6 latest-job ">
            <div class="small-box bg-yellow padding-5">
              <div class="inner">
                <h3 class="text-center">Candidate</h3>
              </div>
              <a href="register_candidate.php" class="small-box-footer">
                Register <i class="fa fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <!-- Employer Registration Box -->
          <div class="col-md-6 latest-job ">
            <div class="small-box bg-red padding-5">
              <div class="inner">
                <h3 class="text-center">Employer</h3>
              </div>
              <a href="register_company.php" class="small-box-footer">
                Register <i class="fa fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
  </section>

</div> <!-- End of content-wrapper -->

<?php
include('footer.php');
?>

</body>
</html>

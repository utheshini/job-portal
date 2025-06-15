<?php
session_start();

if (empty($_SESSION['id_company'])) {
    header("Location: ../login.php");
    exit();
}

require_once("../db.php");

$pageTitle = "Post a Job | JobSeek";

include('../shared/header_dashboard.php');

?>

<div class="col-md-9 bg-white padding-2">
  <h2><i>Post a Job</i></h2>
  <div class="row">
    <form id="jobPostForm" method="post" action="add_job.php">
      <div class="col-md-12 latest-job ">
        

        <!-- Job Title -->
        <div class="form-group">
          <input class="form-control input-lg" type="text" id="jobtitle" name="jobtitle" placeholder="Job Title *" required>
        </div>

        <!-- Job Description -->
        <div class="form-group">
          <label>Description</label>
          <textarea class="form-control input-lg" id="description" name="description" placeholder="Job Description" required></textarea>
        </div>

        <!-- Minimum Salary -->
        <div class="form-group">
          <input type="number" class="form-control input-lg" id="minimumsalary" min="1000" autocomplete="off" name="minimumsalary" placeholder="Minimum Salary *" required>
        </div>

        <!-- Maximum Salary -->
        <div class="form-group">
          <input type="number" class="form-control input-lg" id="maximumsalary" name="maximumsalary" min="1001" placeholder="Maximum Salary *" required>
        </div>

        <!-- Experience (optional) -->
        <div class="form-group">
          <input type="number" class="form-control input-lg" id="experience" autocomplete="off" name="experience" min="0" placeholder="Experience (in Years) Optional">
        </div>

        <!-- Job Type -->
        <div class="form-group">
          <select class="form-control input-lg" id="job_type" name="job_type" required>
            <option value="">Select Job Type</option>
            <option value="Full Time">Full Time</option>
            <option value="Part Time">Part Time</option>
            <option value="Internship">Internship</option>
            <option value="Contract">Contract</option>
          </select>
        </div>
        

         
        <!-- Category -->
        <div class="form-group">
          <select class="form-control input-lg" id="category" name="category" required>
              <option value="">Select Category</option>
              <option value="Accounting & Finance">Accounting & Finance</option>
              <option value="Administrative & Office Support">Administrative & Office Support</option>
              <option value="Aerospace & Defense">Aerospace & Defense</option>
              <option value="Agriculture & Farming">Agriculture & Farming</option>
              <option value="Arts, Design, & Media">Arts, Design, & Media</option>
              <option value="Consulting">Consulting</option>
                <option value="Construction Management">Construction Management</option>
                      <option value="Construction & Trades">Construction & Trades</option>
                      <option value="Customer Service">Customer Service</option>
                      <option value="Education & Training">Education & Training</option>
                      <option value="Energy & Utilities">Energy & Utilities</option>
                      <option value="Engineering">Engineering</option>
                      <option value="Environmental Science">Environmental Science</option>
                      <option value="Fitness & Wellness">Fitness & Wellness</option>
                      <option value="Government & Public Sector">Government & Public Sector</option>
                      <option value="Healthcare & Medical">Healthcare & Medical</option>
                      <option value="Hospitality & Tourism">Hospitality & Tourism</option>
                      <option value="human-resources">Human Resources</option>
                      <option value="Information Technology ">Information Technology (IT)</option>
                      <option value="Insurance">Insurance</option>
                      <option value="Legal">Legal</option>
                      <option value="Legal & Compliance">Legal & Compliance</option>
                      <option value="Logistics & Supply Chain">Logistics & Supply Chain</option>
                      <option value="Manufacturing & Production">Manufacturing & Production</option>
                      <option value="Marketing & Advertising">Marketing & Advertising</option>
                      <option value="Media & Entertainment">Media & Entertainment</option>
                      <option value="Nonprofit & Volunteer">Nonprofit & Volunteer</option>
                      <option value="Pharmaceuticals">Pharmaceuticals</option>
                      <option value="Public Relations">Public Relations</option>
                      <option value="Publishing & Printing">Publishing & Printing</option>
                      <option value="Real Estate">Real Estate</option>
                      <option value="Real Estate Development">Real Estate Development</option>
                      <option value="Retail & Hospitality">Retail & Hospitality</option>
                      <option value="Retail Management">Retail Management</option>
                      <option value="Sales & Business Development">Sales & Business Development</option>
                      <option value="Science & Research">Science & Research</option>
                      <option value="Startups & Entrepreneurship">Startups & Entrepreneurship</option>
                      <option value="Telecommunications">Telecommunications</option>
                      <option value="Transportation & Logistics">Transportation & Logistics</option>
                      <option value="Transportation Management">Transportation Management</option>
                    </select>
        </div>

        <!-- Deadline -->
        <div class="form-group">
          <label>Deadline</label>
          <input class="form-control input-lg" type="date" id="deadline" name="deadline" placeholder="Deadline" required>
        </div>

        <!-- Location -->
        <div class="form-group">
          <select class="form-control input-lg" id="location" name="location" required>
            <option value="" disabled selected>Select a Location</option>
            <?php
            // Fetch cities securely and output escaping HTML
            $sql = "SELECT city_name FROM cities";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($row["city_name"]) . '">' . htmlspecialchars($row["city_name"]) . '</option>';
                }
            } else {
                echo '<option value="">No locations available</option>';
            }
            ?>
          </select>
        </div>

        <!-- Max Age -->
        <div class="form-group">
          <input type="number" class="form-control input-lg" id="maxage" autocomplete="off" name="maxage" min="18" placeholder="Max Age (in Years) Required" required>
        </div>

        <!-- Submit Button -->
        <div class="form-group">
          <button type="submit" class="btn btn-flat btn-success">Create</button>
        </div>
      </div>
     
    </form>
  </div>
</div>
</div>
</div>
</section>
</div>


<?php include('../footer.php'); ?>

<script>
document.addEventListener('DOMContentLoaded', (event) => {
  const dateInput = document.getElementById('deadline');
  const today = new Date();
  const tomorrow = new Date(today);
  tomorrow.setDate(tomorrow.getDate() + 1);
  const minDate = tomorrow.toISOString().split('T')[0];
  dateInput.setAttribute('min', minDate);

  document.getElementById('jobPostForm').addEventListener('submit', function(e) {
    const minimumSalary = parseFloat(document.getElementById('minimumsalary').value);
    const maximumSalary = parseFloat(document.getElementById('maximumsalary').value);
    const maxAge = parseInt(document.getElementById('maxage').value, 10);
    const deadline = new Date(document.getElementById('deadline').value);
    const description = document.getElementById('description').value.trim();
    const errors = [];

    // Check if max salary is greater than min salary
    if (maximumSalary <= minimumSalary) {
      errors.push('Maximum salary must be greater than minimum salary.');
    }

    // Check if age is above 18
    if (maxAge < 18) {
      errors.push('Age should be at least 18.');
    }

    // Check if deadline is a future date
    if (deadline <= today) {
      errors.push('The deadline must be a future date.');
    }

    // Check if description is provided
    if (description.length === 0) {
      errors.push('Description is required.');
    }

    if (errors.length > 0) {
      alert(errors.join('\n'));
      e.preventDefault();
    }
  });
});
</script>
  
</body>
</html>
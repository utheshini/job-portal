<?php
session_start();

if (empty($_SESSION['id_company'])) {
    header("Location: ../login.php");
    exit();
}

require_once("../db.php");

$pageTitle = "Edit Job | JobSeek";

include('../shared/header_dashboard.php');

// Check if a job post ID is provided
if (isset($_GET['id'])) {
    $id_jobpost = intval($_GET['id']); // Sanitize ID

    // Prepare SQL to fetch the job post for the logged-in company
    $sql = "SELECT * FROM jobs WHERE job_id = ? AND company_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_jobpost, $_SESSION['id_company']);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if job post exists
    if ($result->num_rows > 0) {
        $job = $result->fetch_assoc();
    } else {
        echo "No job post found with the specified ID.";
        exit();
    }
} else {
    echo "No job post ID specified.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    // Sanitize and validate inputs
    $jobtitle       = trim($_POST['jobtitle']);
    $description    = trim($_POST['description']);
    $minimumSalary  = floatval($_POST['minimumsalary']);
    $maximumSalary  = floatval($_POST['maximumsalary']);
    $experience     = intval($_POST['experience']);
    $job_type       = trim($_POST['job_type']);
    $category       = trim($_POST['category']);
    $location       = trim($_POST['location']);
    $maxAge         = intval($_POST['maxage']);
    $deadline       = $_POST['deadline'];
    $today          = date("Y-m-d");

    // Business rule: max salary > min salary
    if ($maximumSalary <= $minimumSalary) {
        $errors[] = "Maximum salary must be greater than minimum salary.";
    }

    // Business rule: age must be above 18
    if ($maxAge < 18) {
        $errors[] = "Age should be above 18.";
    }

    // Business rule: deadline must be a future date
    if ($deadline <= $today) {
        $errors[] = "The deadline must be a future date.";
    }

    // Display errors if any
    if (!empty($errors)) {
        echo '<div class="alert alert-danger"><ul>';
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo '</ul></div>';
    } else {
        // Update job post in database
        $sql = "UPDATE jobs SET 
                    job_title = ?, 
                    job_description = ?, 
                    min_salary = ?, 
                    max_salary = ?, 
                    experience = ?, 
                    job_type = ?, 
                    category = ?, 
                    location = ?, 
                    max_age = ?, 
                    deadline = ?
                WHERE job_id = ? AND company_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiiisssisii", 
            $jobtitle, $description, $minimumSalary, $maximumSalary, 
            $experience, $job_type, $category, $location, 
            $maxAge, $deadline, $id_jobpost, $_SESSION['id_company']
        );

        if ($stmt->execute()) {
            echo "<script>
                alert('Job updated successfully!');
                window.location.href = 'my_job_postings.php';
            </script>";
        } else {
            // Escape error output
            $error = htmlspecialchars($stmt->error);
            echo "<script>alert('Error updating job: {$error}');</script>";
        }
    }
}
?>

<div class="col-md-9 bg-white padding-2">
  <h2><i>Edit Job</i></h2>
  <div class="row">
    <form id="jobEditForm" method="post" action="">
      <div class="col-md-12 latest-job">
        <!-- Job Title -->
        <div class="form-group">
          <input class="form-control input-lg" type="text" id="jobtitle" name="jobtitle" placeholder="Job Title" value="<?php echo $job['job_title']; ?>" required>
        </div>

        <!-- Job Description -->
        <div class="form-group">
          <textarea class="form-control input-lg" id="description" name="description" placeholder="Job Description" required><?php echo $job['job_description']; ?></textarea>
        </div>

        <!-- Minimum Salary -->
        <div class="form-group">
          <input type="number" class="form-control input-lg" id="minimumsalary" min="1000" name="minimumsalary" placeholder="Minimum Salary" value="<?php echo $job['min_salary']; ?>" required>
        </div>

        <!-- Maximum Salary -->
        <div class="form-group">
          <input type="number" class="form-control input-lg" id="maximumsalary" min="1001" name="maximumsalary" placeholder="Maximum Salary" value="<?php echo $job['max_salary']; ?>" required>
        </div>

        <!-- Experience -->
        <div class="form-group">
          <input type="number" class="form-control input-lg" id="experience" min="0" name="experience" placeholder="Experience (in Years) Optional" value="<?php echo $job['experience']; ?>">
        </div>

        <!-- Job Type -->
        <div class="form-group">
          <select class="form-control input-lg" id="job_type" name="job_type">
            <option value="Full Time" <?php if ($job['job_type'] == 'Full Time') echo 'selected'; ?>>Full Time</option>
            <option value="Part Time" <?php if ($job['job_type'] == 'Part Time') echo 'selected'; ?>>Part Time</option>
            <option value="Internship" <?php if ($job['job_type'] == 'Internship') echo 'selected'; ?>>Internship</option>
            <option value="Contract" <?php if ($job['job_type'] == 'Contract') echo 'selected'; ?>>Contract</option>
          </select>
        </div>

        <!-- Category -->
        <div class="form-group">
          <select class="form-control input-lg" id="category" name="category" required>
            <option value="" disabled>Select Category</option>
            <?php
              $categories = [
                "Accounting & Finance", "Administrative & Office Support", "Aerospace & Defense", "Agriculture & Farming", "Arts, Design, & Media", "Consulting",
                "Construction & Trades", "Customer Service", "Education & Training", "Energy & Utilities", "Engineering", "Environmental Science", "Fitness & Wellness",
                "Government & Public Sector", "Healthcare & Medical", "Hospitality & Tourism", "Human Resources", "Information Technology", "Insurance", "Legal",
                "Legal & Compliance", "Logistics & Supply Chain", "Manufacturing & Production", "Marketing & Advertising", "Media & Entertainment", "Nonprofit & Volunteer",
                "Pharmaceuticals", "Public Relations", "Publishing & Printing", "Real Estate", "Real Estate Development", "Retail & Hospitality", "Retail Management",
                "Sales & Business Development", "Science & Research", "Startups & Entrepreneurship", "Telecommunications", "Transportation & Logistics", "Transportation Management"
              ];

              foreach ($categories as $category) {
                $selected = ($job['category'] == $category) ? 'selected' : '';
                echo "<option value=\"$category\" $selected>$category</option>";
              }
            ?>
          </select>
        </div>

        <!-- Deadline -->
        <div class="form-group">
          <label>Deadline</label>
          <input class="form-control input-lg" type="date" id="deadline" name="deadline" value="<?php echo $job['deadline']; ?>" required>
        </div>

        <!-- Location -->
        <div class="form-group">
          <select class="form-control input-lg" id="location" name="location" required>
            <option value="" disabled>Select a Location</option>
            <?php
              $sql = "SELECT city_name FROM cities";
              $result = $conn->query($sql);
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  $city = htmlspecialchars($row['city_name']);
                  $selected = ($job['location'] == $row['city_name']) ? 'selected' : '';
                  echo "<option value=\"$city\" $selected>$city</option>";
                }
              } else {
                echo '<option value="" disabled>No cities available</option>';
              }
            ?>
          </select>
        </div>

        <!-- Max Age -->
        <div class="form-group">
          <input type="number" class="form-control input-lg" id="maxage" min="18" name="maxage" placeholder="Max Age (in Years) Required" value="<?php echo $job['max_age']; ?>" required>
        </div>

        <!-- Submit Button -->
        <div class="form-group">
          <button type="submit" class="btn btn-flat btn-success">Update</button>
        </div>
      </div>
    </form>
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

  document.getElementById('jobEditForm').addEventListener('submit', function(e) {
    const minimumSalary = parseFloat(document.getElementById('minimumsalary').value);
    const maximumSalary = parseFloat(document.getElementById('maximumsalary').value);
    const maxAge = parseInt(document.getElementById('maxage').value, 10);
    const deadline = document.getElementById('deadline').value;
    const description = document.getElementById('description').value.trim();
    const errors = [];

    if (maximumSalary <= minimumSalary) {
      errors.push('Maximum salary must be greater than minimum salary.');
    }

    if (maxAge < 18) {
      errors.push('Age should be above 18.');
    }

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


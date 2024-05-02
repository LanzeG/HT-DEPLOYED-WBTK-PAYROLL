<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Responsive Sidebar with Bootstrap</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }

    .sidebar {
      background-color: #333;
      color: #fff;
      position: fixed;
    }

    /* Adjust sidebar styling as needed */
    .sidebar a {
      color: #fff;
      text-decoration: none;
    }

    .content {
      padding: 20px;
    }
 


  </style>



<div id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
  <div class="position-sticky">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link active" href="Employee/empDASHBOARD.php">
        Home
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="Employee/empAPPLYOvertime.php">
          Over time
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="Employee/empAPPLYLeave.php">
          Leave
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="Employee/empATTENDANCErecords.php">
          My Records
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="Employee/empLoans.php">
         Loans
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="Employee/empActivitylogs.php">
          Activity Logs
        </a>
      </li>
      
      <!-- Add more menu items as needed -->
    </ul>
  </div>
</div>

<!-- Bootstrap JS (optional, if you need Bootstrap JavaScript features) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

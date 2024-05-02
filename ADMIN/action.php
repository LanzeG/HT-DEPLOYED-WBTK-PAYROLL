<?php
include("../DBCONFIG.PHP");

if(isset($_POST['action'])){
    $output='';
    if($_POST['action']=='fetchData'){
        $query ="SELECT * FROM employees";
        getData($query);
    }
}
    function getData($query){
        include("../DBCONFIG.PHP");
        $output = "";
        $total_row = mysqli_query($conn,$query) or die('error');
        if(mysqli_num_rows($total_row)>0){
            foreach($total_row as $row){
                $output.='
                <tr>
                 <td><a href="adminVIEWprofile.php?id=' . $row['emp_id'] . '">' . $row['prefix_ID'] . $row['emp_id'] . '</a></td>
                 <td>'.$row['fingerprint_id'].'</td>
                  <td>'.$row['last_name'].'</td>
                  <td>'.$row['first_name'].'</td>
                  <td>'.$row['middle_name'].'</td>
                  <td>'.$row['user_name'].'</td>
                  <td>'.$row['dept_NAME'].'</td>
                  <td>'.$row['employment_TYPE'].'</td>
                  <td>'.$row['shift_SCHEDULE'].'</td>
                  <td>'.$row['contact_number'].'</td>
                  <td>'.$row['date_hired'].'</td>
                  <td>'.$row['date_regularized'].'</td>
                  <td>'.$row['date_resigned'].'</td>
                  <td>
                  <center>
                      <a href="adminEDITMasterfile.php?id=' . $row['emp_id'] . '" class="btn btn-info btn-mini"><span class="icon"><i class="icon-edit"></i></span> Edit</a>
                      <a href="adminDELETEMasterfile.php?id=' . $row['emp_id'] . '" class="btn btn-danger btn-mini"><span class="icon"><i class="icon-trash"></i></span> Delete</a>
                  </center>
              </td>
                </tr>';

            }

        }else{
            $output="<h4>No records found</h4>";
        }
        echo $output;
    }
?>
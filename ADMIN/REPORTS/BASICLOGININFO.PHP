<?php
              if(isset($_SESSION['uname'])){
              
                $uname = $_SESSION['uname'];

                $sql = mysqli_query($conn,"select * from employees where user_name= '$uname'")
                or die ("Failed to query database ".mysql_error());

                $row = mysqli_fetch_array($sql);
                if($row){
                
                  $empid = $row['emp_id'];
                  $lastname = $row['last_name'];
                  $firstname = $row['first_name'];
                  $middlename = $row['middle_name'];
                    } else {
                      die ("Error ".error_reporting());
                    }


                if($row['acct_type'] == "Administrator"){

                  $accttype = 'Administrator';
                } else if ($row['acct_type'] == "Employee"){
                  $accttype = 'Employee';
                }
              
              }else {
                  //die ("Something went wrong. ".error_reporting());
                  header("Location: ../../ADMINLOGIN.PHP");
                      
                    }

?>
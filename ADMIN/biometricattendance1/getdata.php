<?php  
//Connect to database
require 'connectDB.php';

if (isset($_POST['FingerID'])) {
	
	$fingerID = $_POST['FingerID'];

	$sql = "SELECT * FROM fingerprint WHERE fingerprint_id=?";
    $result = mysqli_stmt_init($conn1);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error_Select_card";
        exit();
    }
    else{
    	mysqli_stmt_bind_param($result, "s", $fingerID);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        if ($row = mysqli_fetch_assoc($resultl)){
        	//*****************************************************
            //An existed fingerprint has been detected for Login or Logout
            if (!empty($row['emp_id'])){
            	$emp_id = $row['emp_id'];
            	$user_name = $row['user_name'];
                $sql = "SELECT * FROM time_keeping WHERE fingerprint_id=? AND timekeep_day=CURDATE() AND out_afternoon=''";
                $result = mysqli_stmt_init($conn1);
                if (!mysqli_stmt_prepare($result, $sql)) {
                    echo "SQL_Error_Select_logs";
                    exit();
                }
                else{
                    $sqlcheck = "SELECT * FROM time_keeping WHERE emp_id='$emp_id' AND timekeep_day=CURDATE() AND out_afternoon !='00:00:00'";
                    $resultcheck = mysqli_query($conn1, $sqlcheck);

                    if (!mysqli_num_rows($resultcheck) > 0) {
                        mysqli_stmt_bind_param($result, "i", $fingerID);
                        mysqli_stmt_execute($result);
                        $resultl = mysqli_stmt_get_result($result);
                        //*****************************************************
                        //Login
                        if (!$row = mysqli_fetch_assoc($resultl)){

                            $sql = "INSERT INTO time_keeping (emp_id, fingerprint_id, timekeep_day, in_morning, out_afternoon) VALUES (?, ?, CURDATE(), CURTIME(), ?)";
                            $result = mysqli_stmt_init($conn1);
                            if (!mysqli_stmt_prepare($result, $sql)) {
                                echo "SQL_Error_Select_login1";
                                exit();
                            }
                            else{
                                $timeout = "";
                                mysqli_stmt_bind_param($result, "iis", $emp_id, $fingerID, $timeout);
                                mysqli_stmt_execute($result);

                                $deleteQuery = "DELETE FROM absences WHERE emp_id = '$emp_id' AND absence_date = CURDATE()";
                                $deleteResult = mysqli_query($conn1, $deleteQuery);

                                echo "login".$user_name;
                                exit();
                            }
                        }
                        //*****************************************************
                        //Logout
                        else{

                                $update_sql = "UPDATE time_keeping SET out_afternoon = CURTIME() WHERE fingerprint_id = ? AND timekeep_day = CURDATE()";
                                $update_result = mysqli_stmt_init($conn1);
                    
                                if (!mysqli_stmt_prepare($update_result, $update_sql)) {
                                    echo "SQL_Error_insert_logout1";
                                    exit();
                                }
                                else {
                                    mysqli_stmt_bind_param($update_result, "i", $fingerID);
                                    mysqli_stmt_execute($update_result);

                                    // Display the time difference
                                    echo "logout" . $user_name;
                                    mysqli_stmt_close($update_result);


                                    $select_sql = "SELECT in_morning, out_afternoon, hours_work FROM time_keeping WHERE fingerprint_id = $fingerID AND timekeep_day = CURDATE()";
                                    $select_result = mysqli_query($conn1, $select_sql);

                                    $time_in_row = mysqli_fetch_assoc($select_result);
                                    $time_in = $time_in_row['in_morning'];
                                    $time_out = $time_in_row['out_afternoon'];
                                    $hours_work = $time_in_row['hours_work'];

                                    if ($time_out > '19:00:00') {
                                        // If out_afternoon is later than 7:00 PM, set it to 7:00 PM
                                        $time_out = '19:00:00';
                                    }

                                    $hoursquery = "SELECT shift_SCHEDULE FROM employees WHERE emp_id = $emp_id";
                                    // Perform the query
                                    $hoursresult = mysqli_query($conn1, $hoursquery);
                                    
                                    // Fetch the hoursresult row as an associative array
                                    $hoursrow = mysqli_fetch_assoc($hoursresult);
                                    
                                    // Extract the value of shift_SCHEDULE from the fetched hoursrow
                                    $requiredhourswork = $hoursrow['shift_SCHEDULE'];

                                    if ($hours_work < $requiredhourswork) {
                                        // Calculate undertime
                                        $undertime = $requiredhourswork - $hours_work;
                                        // echo "Undertime: " . $undertime . " hours";
                                        $update_undertime_sql = "UPDATE time_keeping SET undertime_hours =  $undertime WHERE fingerprint_id = $fingerID AND timekeep_day = CURDATE()";
                                        $update_undertime_result = mysqli_query($conn1, $update_undertime_sql);
                                    }    
           
                            }
                     }
                    }
                }
            }
    
        }
    }
}//if (isset($_POST['FingerID']))
if (isset($_POST['Get_Fingerid'])) {
    
    if ($_POST['Get_Fingerid'] == "get_id") {
        $sql= "SELECT fingerprint_id FROM fingerprint WHERE add_fingerid=1";
        $result = mysqli_stmt_init($conn1);
        if (!mysqli_stmt_prepare($result, $sql)) {
            echo "SQL_Error_Select";
            exit();
        }
        else{
            mysqli_stmt_execute($result);
            $resultl = mysqli_stmt_get_result($result);
            if ($row = mysqli_fetch_assoc($resultl)) {
                echo "add-id".$row['fingerprint_id'];
                exit();
            }
            else{
                echo "Nothing";
                exit();
            }
        }
    }
    else{
        exit();
    }
}
if (!empty($_POST['confirm_id'])) {

    $fingerid = $_POST['confirm_id'];

    // $sql="UPDATE fingerprint SET fingerprint_select=0 WHERE fingerprint_select=1";
    // $result = mysqli_stmt_init($conn);
    // if (!mysqli_stmt_prepare($result, $sql)) {
    //     echo "SQL_Error_Select";
    //     exit();
    // }
    // else{
        // mysqli_stmt_execute($result);
        
        $sql="UPDATE fingerprint SET add_fingerid=0 WHERE fingerprint_id=?";
        $result = mysqli_stmt_init($conn1);
        if (!mysqli_stmt_prepare($result, $sql)) {
            echo "SQL_Error_Select";
            exit();
        }
        else{
            mysqli_stmt_bind_param($result, "s", $fingerid);
            mysqli_stmt_execute($result);
            echo "Fingerprint has been added!";

            exit();
        // }
    }  
}
if (isset($_POST['DeleteID'])) {

	if ($_POST['DeleteID'] == "check") {
        $sql = "SELECT fingerprint_id FROM users WHERE del_fingerid=1";
        $result = mysqli_stmt_init($conn1);
        if (!mysqli_stmt_prepare($result, $sql)) {
            echo "SQL_Error_Select";
            exit();
        }
        else{
            mysqli_stmt_execute($result);
            $resultl = mysqli_stmt_get_result($result);
            if ($row = mysqli_fetch_assoc($resultl)) {
                
                echo "del-id".$row['fingerprint_id'];

                $sql = "DELETE FROM users WHERE del_fingerid=1";
                $result = mysqli_stmt_init($conn1);
                if (!mysqli_stmt_prepare($result, $sql)) {
                    echo "SQL_Error_delete";
                    exit();
                }
                else{
                    mysqli_stmt_execute($result);
                    exit();
                }
            }
            else{
                echo "nothing";
                exit();
            }
        }
	}
	else{
		exit();
	}
}
?>
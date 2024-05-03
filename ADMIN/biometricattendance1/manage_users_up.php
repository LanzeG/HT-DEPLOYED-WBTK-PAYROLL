<table cellpadding="0" cellspacing="0" border="0">
<tbody>
<?php
  //Connect to database
  require'connectDB.php';

    $sql = "SELECT * FROM fingerprint";
    $result = mysqli_stmt_init($conn1);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo '<p class="error">SQL Error</p>';
    }
    else{
      mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
      if (mysqli_num_rows($resultl) > 0){
          while ($row = mysqli_fetch_assoc($resultl)){
  ?>
              <TR>
              	<TD>
                  <!-- <?php  
                		if ($row['fingerprint_select'] == 1) {
                			echo "<img src='icons/ok_check.png' title='The selected UID'>";
                		}
                    $fingerid = $row['fingerprint_id'];
                	?> -->
                	<form>
                		<button type="button" class="select_btn" id="<?php echo $fingerid;?>" title="select this UID"><?php echo $fingerid;?></button>
                	</form>
                </TD>
              <TD><?php echo $row['emp_id'];?></TD>
              <TD><?php echo $row['last_name'];?></TD>
              <TD><?php echo $row['user_name'];?></TD>
              <td>
              <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="delete" value="<?php echo $row['emp_id'] ?>">
                <button type="submit">Delete</button>
            </form>

                </td>
              </TR>
<?php
        }   
    }
  }
?>
</tbody>
</table>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $empIdToDelete = $_POST['delete'];
    $deleteQuery = "DELETE FROM fingerprint WHERE emp_id = $empIdToDelete";

    $deleteResult = mysqli_query($conn1, $deleteQuery);

    if ($deleteResult) {
        echo "<script>alert('Row with Employee ID $empIdToDelete has been deleted.');</script>";
        header("Location: ManageUsers.php");
    } else {
        echo "<script>alert('Error deleting row: " . mysqli_error($conn1) . "');</script>";
    }
}
?>


<?php

// check user is logged in
if (isset($_SESSION['admin'])) {

  $quote_ID = $_REQUEST['ID'];

  $deletequote_sql = "SELECT * FROM `quotes` WHERE `ID` =".$_REQUEST['ID'];
  $deletequote_query = mysqli_query($dbconnect, $deletequote_sql);
  $deletequote_rs = mysqli_fetch_assoc($deletequote_query);

  ?>

  <h2>Delete Quote - Confirm</h2>

  <p>Do you really want to delete the following quote...
    <br/>
    <i><?php echo $deletequote_rs['Quote']; ?></i>
  </p>

  <p>
    <a href="index.php?page=../admin/deletequote&ID=<?php echo $_REQUEST['ID'];?>">Delete</a> | <a href="index.php?page=../admin/admin_panel">Cancel</a>
  </p>


  <?php

} // end user logged in if

else {
  $login_error = 'Please login to access this page';
  header('Location: index.php?page=../admin/login&error='.$login_error);
} // end user not logged in else

?>

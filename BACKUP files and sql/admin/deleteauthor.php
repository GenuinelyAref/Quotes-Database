<?php

// check user is logged in
if (isset($_SESSION['admin'])) {

  // delete quotes
  $deletequote_sql = "DELETE FROM `quotes` WHERE `Author_ID` =".$_REQUEST['ID'];
  $deletequote_query = mysqli_query($dbconnect, $deletequote_sql);

  // delete author
  $delete_author_sql = "DELETE FROM `author` WHERE `Author_ID` = ".$_REQUEST['ID'];
  $delete_author_query = mysqli_query($dbconnect, $delete_author_sql);
  ?>

  <h1>Delete Success</h1>

  <p>The author and any associated quotes have been deleted</p>

  <?php
} // end user logged in if

else {
  $login_error = 'Please login to access this page';
  header('Location: index.php?page=../admin/login&error='.$login_error);
} // end user not logged in else

?>

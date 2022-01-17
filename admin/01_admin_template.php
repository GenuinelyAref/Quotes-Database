<?php

// check user is logged in
if (isset($_SESSION['admin'])) {
  echo "you are logged in";
} // end user logged in if

else {
  $login_error = 'Please login to access this page';
  header('Location: index.php?page=../admin/login&error='.$login_error);
} // end user not logged in else

?>

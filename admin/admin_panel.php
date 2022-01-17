<?php

// check user is logged in
if (isset($_SESSION['admin'])) {

  // get authors from database
  $all_authors_sql = "SELECT * FROM `author` ORDER BY `Last` ASC";
  $all_authors_query = mysqli_query($dbconnect, $all_authors_sql);
  $all_authors_rs = mysqli_fetch_assoc($all_authors_query);

  // initialise author form
  $first = "";
  $middle = "";
  $last = "";

  // Code below executes when the form is submitted
  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // get values from form
    $author_ID = mysqli_real_escape_string($dbconnect, $_POST['author']);

    header('Location: index.php?page=author&authorID='.$authorID);

  } // end submit button pushed if


} // end user logged in if

else {
  $login_error = 'Please login to access this page';
  header('Location: index.php?page=../admin/login&error='.$login_error);
} // end user not logged in else

?>

<h1>Admin Panel</h1>

<h2>Quotes...</h2>
<p>
    To <a href="index.php?page=../admine/new_quote">add a quote</a>, use the preceding link or the '+' symbol at the top right of the page.
</p>
<p>
    Quotes can edited / deleted by searching for a quote and then cilcking on the 'edit' / 'delete' icons at the bottom right of each quote. If you
    don't see icons to edit / delete quotes, it means that you are logged out.
</p>

<!-- horizontal line break -->
<hr/>

<h2>Authors...</h2>

<p>
  Either <a href="index.php?page=../admin/add_author">add an author</a> or choose an
  author from the dropdown box below to edit / delete an existing author.
</p>

<form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]."?page=../admin/admin_panel");?>">
    <div>
      <b>Quote Author:</b> &nbsp;

      <!-- dropdown menu -->
      <select name="author">
          <!-- default option (new author) -->
          <option value="unknown" selected>Choose...</option>

          <!-- existing authors -->
          <?php
          do {

            // concatenate author names into full name (first + middle + last)
            $author_full = $all_authors_rs['Last'].", ".$all_authors_rs['First']." ".$all_authors_rs['Middle'];

            ?>

            <option value="<?php echo $all_authors_rs['Author_ID'];?>">
              <?php echo $author_full; ?>
            </option>

            <?php
          } // end of author options 'do'

          while ($all_authors_rs=mysqli_fetch_assoc($all_authors_query))

           ?>


      </select>

      &nbsp;

      <input class="short" type="submit" name="quote_author" value="Next..."/>


    </div>
</form>

&nbsp;

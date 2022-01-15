<?php

// check user is logged in
if (isset($_SESSION['admin'])) {

    $ID = $_REQUEST['ID'];
    echo "Author ID: ".$ID." space";

    // Get author ID
    $find_sql = "SELECT * FROM `quotes`
    JOIN `author` ON (`author`.`Author_ID` = `quotes`.`Author_ID`)
    WHERE `quotes`.`ID` = $ID
    ";

    $find_query = mysqli_query($dbconnect, $find_sql);
    $find_rs = mysqli_fetch_assoc($find_query);

    $author_ID = $find_rs['Author_ID'];
    $first = $find_rs['First'];
    $middle = $find_rs['Middle'];
    $last = $find_rs['Last'];

    $current_author = $last.", ".$first." ".$middle;


    // Get subject / topic list from database
    $all_tags_sql = "SELECT * FROM `subject` ORDER BY `Subject` ASC";
    $all_subjects = autocomplete_list($dbconnect, $all_tags_sql, 'Subject');

    // retrieve data to populate form
    $quote = $find_rs['Quote'];
    $notes = $find_rs['Notes'];

    // get subject ids
    $subject1_ID = $find_rs['Subject1_ID'];
    $subject2_ID = $find_rs['Subject2_ID'];
    $subject3_ID = $find_rs['Subject3_ID'];

    // retrieve subject names from subject table
    $tag_1_rs = get_rs($dbconnect, "SELECT * FROM `subject` WHERE `Subject_ID` = $subject1_ID");
    $tag_2_rs = get_rs($dbconnect, "SELECT * FROM `subject` WHERE `Subject_ID` = $subject2_ID");
    $tag_3_rs = get_rs($dbconnect, "SELECT * FROM `subject` WHERE `Subject_ID` = $subject3_ID");


    $tag_1 = $tag_1_rs['Subject'];
    $tag_2 = $tag_2_rs['Subject'];
    $tag_3 = $tag_3_rs['Subject'];

    // initialise tag IDs
    $tag_1_ID = $tag_2_ID = $tag_3_ID = 0;

    $has_errors = "no";

    // setup error fields / visibility
    $quote_error = $tag_1_error = "no-error";
    $quote_field = "form-ok";
    $tag_1_field = "tag-ok";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // get data from form
        $author_ID = mysqli_real_escape_string($dbconnect, $_POST['author']);
        $quote = mysqli_real_escape_string($dbconnect, $_POST['quote']);
        $notes = mysqli_real_escape_string($dbconnect, $_POST['notes']);
        $tag_1 = mysqli_real_escape_string($dbconnect, $_POST['Subject_1']);
        $tag_2 = mysqli_real_escape_string($dbconnect, $_POST['Subject_2']);
        $tag_3 = mysqli_real_escape_string($dbconnect, $_POST['Subject_3']);


        // check data is valid

        // check quote is not blank
        if ($quote == "") {
          $has_errors = "yes";
          $quote_error = "error-text";
          $quote_field = "form-error";
        } // end check quote is not blank if

        // check that first subject has been filled in
        if ($tag_1 == "") {
          $has_errors = "yes";
          $tag_1_error = "error-text";
          $tag_1_field = "tag-error";
        } // end check first subject not blank if


        if ($has_errors != "yes") {

            // Get subject ID's via get_ID function...
            $subjectID_1 = get_ID($dbconnect, 'subject', 'Subject_ID', 'Subject', $tag_1);
            $subjectID_2 = get_ID($dbconnect, 'subject', 'Subject_ID', 'Subject', $tag_2);
            $subjectID_3 = get_ID($dbconnect, 'subject', 'Subject_ID', 'Subject', $tag_3);

            // add entry to database
            $editentry_sql = "UPDATE `quotes` SET `Author_ID` = '$author_ID',
             `Quote` = '$quote', `Notes` = '$notes', `Subject1_ID` = '$subjectID_1',
             `Subject2_ID` = '$subjectID_2', `Subject3_ID` = '$subjectID_3'
             WHERE `quotes`.`ID` = $ID;";
            $editentry_query = mysqli_query($dbconnect, $editentry_sql);

            // get quote ID for next page
            $get_quote_sql = "SELECT * FROM `quotes` WHERE `Quote` = '$quote'";
            $get_quote_query = mysqli_query($dbconnect, $get_quote_sql);
            $get_quote_rs = mysqli_fetch_assoc($get_quote_query);

            $quote_ID = $get_quote_rs['ID'];
            $_SESSION['Quote_Success'] = $quote_ID;

            // Go to success page
            header("Location: index.php?page=../content/editquote_success");

        } // end add entry to database if

    }

} // end user logged in if

else {
  $login_error = 'Please login to access this page';
  header("Location: index.php?page=../admin/login&error=$login_error");
} // end user not logged in else

?>

<h1>Edit Quote...</h1>

<form autocomplete="off" method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]."?page=../admin/editquote&ID=$ID");?>">

    <!-- dropdown menu -->
    <select name="author">
        <!-- default option (new author) -->
        <option value="<?php echo $author_ID; ?>" selected>
          <?php echo $current_author; ?>
        </option>

        <!-- existing authors -->
        <?php

        // get authors from database
        $all_authors_sql = "SELECT * FROM `author` ORDER BY `Last` ASC";
        $all_authors_query = mysqli_query($dbconnect, $all_authors_sql);
        $all_authors_rs = mysqli_fetch_assoc($all_authors_query);


        do {

          $author_ID = $all_authors_rs['Author_ID'];
          $first = $all_authors_rs['First'];
          $middle = $all_authors_rs['Initial'];
          $last = $all_authors_rs['Last'];

          $author_full = $last.", ".$first." ".$middle;


          ?>

          <option value="<?php echo $author_ID; ?>">
              <?php echo $author_full; ?>
          </option>

          <?php
        } // end of author options 'do'

        while ($all_authors_rs=mysqli_fetch_assoc($all_authors_query))

         ?>
    </select>

    <!-- 2 line breaks -->
    </br></br>


    <!-- Quote text area  error message -->
    <div class="<?php echo $quote_error; ?>">
        This field can't be blank
    </div>

    <!-- Quote text area -->
    <textarea class="add-field <?php echo $quote_field; ?>" name="quote" rows="6" placeholder="Please type your quote here"><?php echo $quote; ?></textarea>

    <!-- 2 line breaks -->
    </br></br>

    <!-- Quote notes input box -->
    <input class="add-field" type="text" name="notes" value="<?php echo $notes; ?>" placeholder="Notes (optional) ..." />

    <!-- 2 line breaks -->
    </br></br>

    <!-- Tag 1 input error message -->
    <div class="<?php echo $tag_1_error; ?>">
        Please enter at least one subject tag
    </div>

    <!-- Tag 1 input -->
    <div class="autocomplete">
        <input id="subject1" class="<?php echo $tag_1_field; ?>" type="text" value="<?php echo $tag_1; ?>" name="Subject_1" placeholder="Subject 1 (start typing)..." />
    </div>

    <!-- 2 line breaks -->
    </br></br>

    <!-- Tag 2 input -->
    <div class="autocomplete">
        <input id="subject2" type="text" value="<?php echo $tag_2; ?>" name="Subject_2" placeholder="Subject 2 (start typing, optional)..." />
    </div>

    <!-- 2 line breaks -->
    </br></br>

    <!-- Tag 3 input -->
    <div class="autocomplete">
        <input id="subject3" type="text" value="<?php echo $tag_3; ?>" name="Subject_3" placeholder="Subject 3 (start typing, optional)..." />
    </div>

    <!-- 2 line breaks -->
    </br></br>


    <!-- Submit button -->
    <p>
        <input type="submit" value="Submit" />
    </p>

</form>

<!-- script to make autocomplete work -->
<script>
<?php include 'autocomplete.php'; ?>

var all_tags = <?php print("$all_subjects"); ?>;
autocomplete(document.getElementById("subject1"), all_tags);
autocomplete(document.getElementById("subject2"), all_tags);
autocomplete(document.getElementById("subject3"), all_tags);

</script>

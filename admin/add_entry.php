<?php

// check user is logged in
if (isset($_SESSION['admin'])) {

    $author_ID = $_SESSION['Add_Quote'];

    if ($author_ID == "unknown") {

      // Get job list from database
      $all_jobs_sql = "SELECT * FROM `job` ORDER BY `job`.`Job_ID` ASC";
      $all_jobs = autocomplete_list($dbconnect, $all_jobs_sql, 'Job');

      // Get country list from database
      $all_countries_sql = "SELECT * FROM `country` ORDER BY `country`.`Country_ID` ASC";
      $all_countries = autocomplete_list($dbconnect, $all_countries_sql, 'Country');

      // initialise form variables for countries and jobs
      $job_1 = "";
      $job_2 = "";
      $country_1 = "";
      $country_2 = "";
      $first = "";
      $middle = "";
      $last = "";
      $yob = "";
      $gender_code = "";

      // initialise job IDs
      $job_1_ID = $job_2_ID = 0;

      // initialise country IDs
      $country_1_ID = $country_2_ID = 0;

      // setup error fields / visibility
      $job_1_error = $country_1_error = $gender_error = $yob_error = $last_error = "no-error";

      $last_field = $yob_field = $gender_field = "form-ok";
      $country_1_field = $job_1_field = "tag-ok";


    } // end author variable initialisation if

    // Get subject / topic list from database
    $all_tags_sql = "SELECT * FROM `subject` ORDER BY `Subject` ASC";
    $all_subjects = autocomplete_list($dbconnect, $all_tags_sql, 'Subject');

    // initialise form variables for quote
    $quote = "";
    $notes = "";
    $tag_1 = "";
    $tag_2 = "";
    $tag_3 = "";

    // initialise tag IDs
    $tag_1_ID = $tag_2_ID = $tag_3_ID = 0;

    $has_errors = "no";

    // setup error fields / visibility
    $quote_error = $tag_1_error = "no-error";
    $quote_field = "form-ok";
    $tag_1_field = "tag-ok";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // get data from form
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
        } // check first subject not blank if

        if ($has_errors != "yes") {

            // Get subject ID's via get_ID function...
            $subjectID_1 = get_ID($dbconnect, 'subject', 'Subject_ID', 'Subject', $tag_1);
            $subjectID_2 = get_ID($dbconnect, 'subject', 'Subject_ID', 'Subject', $tag_2);
            $subjectID_3 = get_ID($dbconnect, 'subject', 'Subject_ID', 'Subject', $tag_3);

            // add entry to database
            $addentry_sql = "INSERT INTO `quotes` (`ID`, `Author_ID`, `Quote`, `Notes`, `Subject1_ID`, `Subject2_ID`, `Subject3_ID`) VALUES (NULL, '$author_ID', '$quote', '$notes', '$subjectID_1', '$subjectID_2', '$subjectID_3');";
            $addentry_query = mysqli_query($dbconnect, $addentry_sql);

            // get quote ID for next page
            $get_quote_sql = "SELECT * FROM `quotes` WHERE `Quote` = '$quote'";
            $get_quote_query = mysqli_query($dbconnect, $get_quote_sql);
            $get_quote_rs = mysqli_fetch_assoc($get_quote_query);

            $quote_ID = $get_quote_rs['ID'];
            $_SESSION['Quote_Success']=$quote_ID;

            // Go to success page
            header('Location: index.php?page=quote_success');

        } // end add entry to database if

    }

} // end user logged in if

else {
  $login_error = 'Please login to access this page';
  header("Location: index.php?page=../admin/login&error=$login_error");
} // end user not logged in else

?>

<h1>Add Quote...</h1>

<form autocomplete="off" method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]."?page=../admin/add_entry");?>">

    <!-- Quote text area  error message -->
    <div class="<?php echo $quote_error; ?>">
        This field can't be blank
    </div>

    <!-- Quote text area -->
    <textarea class="add-field <?php echo $quote_field; ?>" name="quote" rows="6" placeholder="Please type your quote here"></textarea>

    <!-- 2 line breaks -->
    </br></br>

    <!-- Quote notes input box -->
    <input class="add-field <?php echo $notes; ?>" type="text" name="notes" value="<?php echo $notes; ?>" placeholder="Notes (optional) ..." />

    <!-- 2 line breaks -->
    </br></br>

    <!-- Tag 1 input error message -->
    <div class="<?php echo $tag_1_error; ?>">
        Please enter at least one subject tag
    </div>

    <!-- Tag 1 input -->
    <div class="autocomplete">
        <input id="subject1" type="text" value="<?php echo $tag_1; ?>" name="Subject_1" placeholder="Subject 1 (start typing)..." />
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

var all_jobs = <?php print("$all_jobs"); ?>;
autocomplete(document.getElementById("job1"), all_jobs);
autocomplete(document.getElementById("job1"), all_jobs);

var all_countries = <?php print("$all_countries"); ?>;
autocomplete(document.getElementById("country1"), all_countries);
autocomplete(document.getElementById("country2"), all_countries);


</script>

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
      $gender = "";

      // initialise job IDs
      $job_1_ID = $job_2_ID = 0;

      // initialise country IDs
      $country_1_ID = $country_2_ID = 0;

      // setup error fields / visibility
      $job_1_error = $country_1_error = $gender_error = $yob_error = $first_error = $last_error = "no-error";

      $first_field = $last_field = $yob_field = $gender_field = "form-ok";
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

        // if author is unkown
        if ($author_ID == "unknown") {
          // get new author data from form
          $first = mysqli_real_escape_string($dbconnect, $_POST['first']);
          $middle = mysqli_real_escape_string($dbconnect, $_POST['middle']);
          $last = mysqli_real_escape_string($dbconnect, $_POST['last']);
          $yob = mysqli_real_escape_string($dbconnect, $_POST['yob']);
          // gender code retrieved later on to allow for isset verification
          $job_1 = mysqli_real_escape_string($dbconnect, $_POST['Job_1']);
          $job_2 = mysqli_real_escape_string($dbconnect, $_POST['Job_2']);
          $country_1 = mysqli_real_escape_string($dbconnect, $_POST['Country_1']);
          $country_2 = mysqli_real_escape_string($dbconnect, $_POST['Country_2']);
        }

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

        // special error checking for new author data
        if ($author_ID == "unknown") {

          // check that first name is not blank
          if ($first == "") {
            $has_errors = "yes";
            $first_error = "error-text";
            $first_field = "form-error";
          } // end first name not blank if

          // check that last name is not blank
          if ($last == "") {
            $has_errors = "yes";
            $last_error = "error-text";
            $last_field = "form-error";
          } // end last name not blank if

          // check gender code and assign proper value
          if (isset($_POST['gender'])) {
            $gender_code = mysqli_real_escape_string($dbconnect, $_POST['gender']);
            if ($gender_code == "F") {
              $gender = "Female";
            }
            elseif ($gender_code == "M") {
              $gender = "Male";
            }
            elseif ($gender_code == "O") {
              $gender = "Other";
            }
          }
          // if none of the above, then no gender was selected
          else {
            $has_errors = "yes";
            $gender_error = "error-text";
            $gender_field = "form-error";
          }

          // check that year of birth is not blank
          if ($yob == "") {
            $has_errors = "yes";
            $yob_error = "error-text";
            $yob_field = "form-error";
          } // end year of birth not blank if

          // check that occupation 1 is not blank
          if ($job_1 == "") {
            $has_errors = "yes";
            $job_1_error = "error-text";
            $job_1_field = "form-error";
          } // end occupation 1 not blank if

          // check that country 1 is not blank
          if ($country_1 == "") {
            $has_errors = "yes";
            $country_1_error = "error-text";
            $country_1_field = "form-error";
          } // end country 1 not blank if

          // get country IDs
          $country_1_ID = get_ID($dbconnect, 'country', 'Country_ID', 'Country', $country_1);
          $country_2_ID = get_ID($dbconnect, 'country', 'Country_ID', 'Country', $country_2);

          // get occupation IDs
          $job_1_ID = get_ID($dbconnect, 'job', 'Job_ID', 'Job', $job_1);
          $job_2_ID = get_ID($dbconnect, 'job', 'Job_ID', 'Job', $job_2);


        } // end error checking for new author if

        if ($has_errors != "yes") {

            // Get subject ID's via get_ID function...
            $subjectID_1 = get_ID($dbconnect, 'subject', 'Subject_ID', 'Subject', $tag_1);
            $subjectID_2 = get_ID($dbconnect, 'subject', 'Subject_ID', 'Subject', $tag_2);
            $subjectID_3 = get_ID($dbconnect, 'subject', 'Subject_ID', 'Subject', $tag_3);

            // add author to dataase if we have a new author
            if ($author_ID == "unknown") {

              $new_author_sql = "INSERT INTO `author` (`Author_ID`, `First`, `Middle`, `Last`, `Gender`
                , `Born`, `Country1_ID`, `Country2_ID`, `Job1_ID`, `Job2_ID`) VALUES (NULL, '$first'
                  , '$middle', '$last', '$gender_code', '$yob', '$country_1_ID'
                , '$country_2_ID', '$job_1_ID', '$job_2_ID');";
              $new_author_query = mysqli_query($dbconnect, $new_author_sql);

              // Get Author ID
              $find_author_sql = "SELECT * FROM `author` WHERE `Last` = '$last'";
              $find_author_query = mysqli_query($dbconnect, $find_author_sql);
              $find_author_rs = mysqli_fetch_assoc($find_author_query);

              $new_authorID = $find_author_rs['Author_ID'];
              echo "New Author ID: ".$new_authorID;

              $author_ID = $new_authorID;

            } // end add author to database

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
  header('Location: index.php?page=../admin/login&error=$login_error');
} // end user not logged in else

?>

<h1>Add a Quote...</h1>

<form autocomplete="off" method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]."?page=../admin/add_entry");?>">

    <?php
    // fields to add new author information
    if ($author_ID == "unknown") {
      ?>

      <!-- Author first name error message -->
      <div class="<?php echo $first_error; ?>">
          This field can't be blank
      </div>

      <!-- Author first name -->
      <div>
          <input type="text" class="add-field <?php echo $first_field; ?>" value="<?php echo $first; ?>" name="first" placeholder="Author's First Name" />
      </div>

      <!-- line break -->
      </br>

      <!-- Author middle name -->
      <div>
          <input type="text" class="add-field" value="<?php echo $middle; ?>" name="middle" placeholder="Author's Middle Name" />
      </div>

      <!-- line break -->
      </br>

      <!-- Author last name error message -->
      <div class="<?php echo $last_error; ?>">
          This field can't be blank
      </div>

      <!-- Author last name -->
      <div>
          <input type="text" class="add-field <?php echo $last_field; ?>" value="<?php echo $last; ?>" name="last" placeholder="Author's Last Name" />
      </div>

      <!-- line break -->
      </br>

      <!-- Author year of birth error message -->
      <div class="<?php echo $yob_error; ?>">
          This field can't be blank
      </div>

      <!-- Author year of birth -->
      <div class="yob">
          <input type="text" class="add-field <?php echo $yob_field; ?>" maxlength="7" value="<?php echo $yob; ?>" name="yob" placeholder="Year of birth" />
      </div>

      <!-- line break -->
      </br>

      <!-- Author gender error message -->
      <div class="<?php echo $gender_error; ?>">
          This field can't be blank - you must choose an option
      </div>

      <!-- Author gender -->
      <select name="gender" class="adv <?php echo $gender_field; ?>">

          <?php
          if ($gender_code == "") {
            ?>
            <option value="" selected disabled>Gender (Choose something)...</option>
            <option value="F">Female</option>
            <option value="M">Male</option>
            <option value="O">Other</option>

            <?php
          } // end gender not chose if

          elseif ($gender_code == "F") {
            ?>
            <option value="" disabled>Gender (Choose something)...</option>
            <option value="F" selected>Female</option>
            <option value="M">Male</option>
            <option value="O">Other</option>
            <?php
          } // end gender female elseif

          elseif ($gender_code == "M") {
            ?>
            <option value="" disabled>Gender (Choose something)...</option>
            <option value="F">Female</option>
            <option value="M" selected>Male</option>
            <option value="O">Other</option>
            <?php
          } // end gender male elseif

          elseif ($gender_code == "O") {
            ?>
            <option value="" disabled>Gender (Choose something)...</option>
            <option value="F">Female</option>
            <option value="M">Male</option>
            <option value="O" selected>Other</option>
            <?php
          } // end gender other elseif
           ?>

      </select>

      <!-- 2 line breaks -->
      </br></br>

      <!-- Job 1 input error message -->
      <div class="<?php echo $job_1_error; ?>">
          Please enter at least one occupation
      </div>

      <!-- Job 1 input -->
      <div class="autocomplete">
          <input id="job1" class="<?php echo $job_1_field; ?>" type="text" value="<?php echo $job_1; ?>" name="Job_1" placeholder="Occupation 1 (start typing)..." />
      </div>

      <!-- 2 line breaks -->
      </br></br>

      <!-- Job 2 input -->
      <div class="autocomplete">
          <input id="job2" type="text" value="<?php echo $job_2; ?>" name="Job_2" placeholder="Occupation 2 (start typing, optional)..." />
      </div>

      <!-- 2 line breaks -->
      </br></br>

      <!-- Country 1 input error message -->
      <div class="<?php echo $country_1_error; ?>">
          Please enter at least one country
      </div>

      <!-- Country 1 input -->
      <div class="autocomplete">
          <input id="country1" class="<?php echo $country_1_field; ?>" type="text" value="<?php echo $country_1; ?>" name="Country_1" placeholder="Country 1 (start typing)..." />
      </div>

      <!-- 2 line breaks -->
      </br></br>

      <!-- Country 2 input -->
      <div class="autocomplete">
          <input id="country2" type="text" value="<?php echo $country_2; ?>" name="Country_2" placeholder="Country 2 (start typing, optional)..." />
      </div>

      <!-- 2 line breaks -->
      </br></br>

      <?php
    } // end new author fields
    ?>

    <!-- Quote text area  error message -->
    <div class="<?php echo $quote_error; ?>">
        This field can't be blank
    </div>

    <!-- Quote text area -->
    <textarea class="add-field <?php echo $quote_field; ?>" name="quote" rows="6" placeholder="Please type your quote here"></textarea>

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

var all_jobs = <?php print("$all_jobs"); ?>;
autocomplete(document.getElementById("job1"), all_jobs);
autocomplete(document.getElementById("job2"), all_jobs);

var all_countries = <?php print("$all_countries"); ?>;
autocomplete(document.getElementById("country1"), all_countries);
autocomplete(document.getElementById("country2"), all_countries);


</script>

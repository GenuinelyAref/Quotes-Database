<?php

// check user is logged in
if (isset($_SESSION['admin'])) {

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

    $has_errors = "no";


    // when form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // get new author data from form
        // first name
        $first = mysqli_real_escape_string($dbconnect, $_POST['first']);
        // middle name
        $middle = mysqli_real_escape_string($dbconnect, $_POST['middle']);
        // last name
        $last = mysqli_real_escape_string($dbconnect, $_POST['last']);
        // year of birth
        $yob = mysqli_real_escape_string($dbconnect, $_POST['yob']);

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

        // occupations
        $job_1 = mysqli_real_escape_string($dbconnect, $_POST['Job_1']);
        $job_2 = mysqli_real_escape_string($dbconnect, $_POST['Job_2']);
        // countries
        $country_1 = mysqli_real_escape_string($dbconnect, $_POST['Country_1']);
        $country_2 = mysqli_real_escape_string($dbconnect, $_POST['Country_2']);


        // check data is valid

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
        $countryID_1 = get_ID($dbconnect, 'country', 'Country_ID', 'Country', $country_1);
        $countryID_2 = get_ID($dbconnect, 'country', 'Country_ID', 'Country', $country_2);

        // get occupation IDs
        $jobID_1 = get_ID($dbconnect, 'job', 'Job_ID', 'Job', $job_1);
        $jobID_2 = get_ID($dbconnect, 'job', 'Job_ID', 'Job', $job_2);



        if ($has_errors != "yes") {

            // add author to database
            $add_author_sql = "INSERT INTO `author`(`Author_ID`, `First`, `Middle`, `Last`, `Gender`, `Born`, `Country1_ID`, `Country2_ID`, `Job1_ID`, `Job2_ID`)
            VALUES (NULL, '$first', '$middle', '$last', '$gender_code', '$yob', '$countryID_1', '$countryID_2', '$jobID_1', '$jobID_2');
            ";
            $add_author_query = mysqli_query($dbconnect, $add_author_sql);

            // Get Author ID
            $find_author_sql = "SELECT * FROM `author` WHERE `Last` = '$last'";
            $find_author_query = mysqli_query($dbconnect, $find_author_sql);
            $find_author_rs = mysqli_fetch_assoc($find_author_query);

            $new_authorID = $find_author_rs['Author_ID'];

            $author_ID = $new_authorID;

            // Go to success page
            header('Location: index.php?page=../content/author&authorID='.$author_ID);

        } // end 'add entry to database' if

    } // end 'form submitted' if

} // end user logged in if

else {
  $login_error = 'Please login to access this page';
  header('Location: index.php?page=../admin/login&error='.$login_error);
} // end user not logged in else

?>

<h1>Add an Author...</h1>

<form autocomplete="off" method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]."?page=../admin/add_author");?>">


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


    <!-- Submit button -->
    <p>
        <input type="submit" value="Submit" />
    </p>

</form>

<!-- script to make autocomplete work -->
<script>
  <?php include 'autocomplete.php'; ?>

  var all_jobs = <?php print("$all_jobs"); ?>;
  autocomplete(document.getElementById("job1"), all_jobs);
  autocomplete(document.getElementById("job2"), all_jobs);

  var all_countries = <?php print("$all_countries"); ?>;
  autocomplete(document.getElementById("country1"), all_countries);
  autocomplete(document.getElementById("country2"), all_countries);

</script>

<?php

if (!isset($_REQUEST['author_ID'])) {
  header('Location: index.php');
}

$author_to_find = $_REQUEST['author_ID'];

$find_sql = "SELECT * FROM `quotes`
JOIN `author` ON (`author`.`Author_ID` = `quotes`.`Author_ID`)
WHERE `quotes`.`Author_ID` = $author_to_find
";
$find_query = mysqli_query($dbconnect, $find_sql);
$find_rs = mysqli_fetch_assoc($find_query);

$country1 = $find_rs['Country1_ID'];
$country2 = $find_rs['Country2_ID'];

$occupation1 = $find_rs['Job1_ID'];
$occupation2 = $find_rs['Job2_ID'];

// get author name
include("get_author.php");

?>

<!-- line break-->
</br>

<div class="about">
    <h2> <!-- author's full name -->
        <?php echo $full_name ?> - About
    </h2> <!-- end of author's full name -->

    <p> <!-- author birthyear -->
      <b>Born:</b>
      <?php echo $find_rs['Born']; ?>
    </p> <!-- end of author birthyear -->

    <p> <!-- author countries -->
        <?php
            // show countries
            country_job($dbconnect, $country1, $country2, "Country", "Countries", "country", "Country_ID", "Country")
         ?>
    </p> <!-- end of author countries -->

    <p> <!-- author occupations -->
        <?php
            // show occupations
            country_job($dbconnect, $occupation1, $occupation2, "Occupation", "Occupations", "job", "Job_ID", "Job")
         ?>
    </p> <!-- end of author occupations -->

</div> <!-- end about the author div -->

<!-- line break-->
</br>

<?php

// loop through the results and display them...
do {

    $quote = preg_replace('/[^A-Za-z0-9.,?\s\'\-]/', ' ', $find_rs['Quote']);


    ?>

  <div class="results">
      <p>
          <?php echo $quote; ?>

      </p>

    <!-- subject tags go here -->
    <?php include("show_subjects.php"); ?>

  </div>

  <br />

  <?php
} // end of display results 'do' loop

while ($find_rs = mysqli_fetch_assoc($find_query));

?>

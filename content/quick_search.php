<?php

$quick_find = mysqli_real_escape_string($dbconnect, $_POST['quick_search']);

// Find subject ID
$subject_sql = "SELECT * FROM `subject` WHERE `Subject` LIKE '%$quick_find%'";
$subject_query = mysqli_query($dbconnect, $subject_sql);
$subject_rs = mysqli_fetch_assoc($subject_query);

$subject_count = mysqli_num_rows($subject_query);

if ($subject_count > 0) {
  $subject_ID = $subject_rs['Subject_ID'];
}

else {
  $subject_ID = "-1";
}

$find_sql = "SELECT * FROM `quotes`
JOIN `author` ON (`author`.`Author_ID` = `quotes`.`Author_ID`)
WHERE `Last` LIKE '%$quick_find%'
OR `Middle` LIKE '%$quick_find%'
OR `First` LIKE '%$quick_find%'
OR `Subject1_ID` = $subject_ID
OR `Subject2_ID` = $subject_ID
OR `Subject3_ID` = $subject_ID
";
$find_query = mysqli_query($dbconnect, $find_sql);
$find_rs = mysqli_fetch_assoc($find_query);
$count = mysqli_num_rows($find_query);

?>

<!-- results header -->
<h2>
    Search Results for "<?php echo $quick_find ?>"
</h2>

<?php

if ($count > 0) {

// loop through the results and display them...
do {

    $quote = preg_replace('/[^A-Za-z0-9.,?\s\'\-]/', ' ', $find_rs['Quote']);

    include("get_author.php");

    ?>

  <div class="results">
      <p>
          <?php echo $quote; ?>

          <!-- line break -->
          <br />

          <a href="index.php?page=author&authorID=<?php echo $find_rs['Author_ID']; ?>">
            <?php echo $full_name; ?>
          </a>
      </p>

    <!-- subject tags go here -->
    <?php include("show_subjects.php"); ?>

  </div>

  <br />

  <?php
} // end of display results 'do' loop

while ($find_rs = mysqli_fetch_assoc($find_query));

  } // end if results exist if

else { // happens when there are no results
  ?>

    <h2>No Results</h2>

    <div class="error">
        Sorry - there are no quotes that match the search term <i><b><?php echo $quick_find ?></b></i>. Please try again.
    </div>

    <p>&nbsp;</p>

  <?php
}

?>

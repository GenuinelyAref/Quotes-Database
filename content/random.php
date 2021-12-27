<h2>Random Quotes</h2>

<?php

$find_sql = "SELECT * FROM `quotes`
JOIN `author` ON (`author`.`Author_ID` = `quotes`.`Author_ID`)
ORDER BY RAND()
LIMIT 10
";
$find_query = mysqli_query($dbconnect, $find_sql);
$find_rs = mysqli_fetch_assoc($find_query);

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

          <a href="index.php?page=author&author_ID=<?php echo $find_rs['Author_ID']; ?>">
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

?>

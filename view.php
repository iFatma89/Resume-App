<!-- Model -->
<?php
require_once('pdo.php');
?>

<!-- View -->
<!DOCTYPE html>
<html>
 <head>
  <title>Fatma Al-Shehhi</title>
  <script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous"></script>
  <script
  src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
  integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
  crossorigin="anonymous"></script>
 </head>
<body>
  <h1>Profile information</h1>
    
  <?php
  $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id=:p_id");
  $stmt->execute(array(":p_id" => $_GET['profile_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  // Declare variables
  $fname = htmlentities($row['first_name']);
  $lname = htmlentities($row['last_name']);
  $email = htmlentities($row['email']);
  $headline = htmlentities($row['headline']);
  $summary = htmlentities($row['summary']);
 
  ?> 
  <p>First Name: <?= $fname; ?></p>
  <p>Last Name: <?= $lname; ?></p>
  <p>Email: <?= $email; ?></p>
  <p>Headline: <br/>
  <?= $headline; ?></p>
  <p>Summary <br/>
  <?= $summary; ?></p>
  

  <?php

  // Load the education rows 
  $stmt=$pdo->prepare('SELECT year, name FROM Education AS e JOIN Institution AS i ON e.institution_id=i.institution_id WHERE profile_id=:pid ORDER BY rank');
  $stmt->execute(array(':pid' => $_GET['profile_id']));
  $count = $stmt->rowCount();
  if($count>0){
    echo('<p>Education'."\n".'<ul>'."\n"); 
    $edu=0;
    while($school=$stmt->fetch(PDO::FETCH_ASSOC)){
      $edu++;
      echo('<li>'.$school['year'].': '.$school['name'].'</li>');
    }
    echo ('</ul>'."\n".'</p>'."\n");
  }

  // Load the postion rows 
  $stmt=$pdo->prepare('SELECT * FROM Position WHERE profile_id=:pid ORDER BY rank');
  $stmt->execute(array(':pid' => $_GET['profile_id']));
  $count = $stmt->rowCount();
  if($count>0){
    echo('<p>Position'."\n".'<ul>'."\n"); 
    $pos=0;
    while($position=$stmt->fetch(PDO::FETCH_ASSOC)){
      $pos++;
      echo('<li>'.$position['year'].': '.$position['description'].'</li>');
    }
    echo ('</ul>'."\n".'</p>'."\n");
  }
  ?>
  <a href="index.php">Done</a>

</body>
</html>
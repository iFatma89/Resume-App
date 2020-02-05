<!-- Model -->
<?php
require_once('pdo.php');
session_start();

if(!isset($_SESSION['user_id'])){
  die('ACCESS DENIED');
  return;
}

// If the user requested cancel, go to index.php
if(isset($_POST['cancel'])){
  header('Location: index.php');
  return;
}


if(isset($_POST['delete']) && isset($_POST['profile_id']) && isset($_SESSION['user_id'])){
  
    // Check 1st if the entry belongs to the logged in user
  $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id=:p_id");
  $stmt->execute(array(":p_id" => $_GET['profile_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  
  // If the current logged in user does not own the entry in the DB
  if($_SESSION['user_id']!=$row['user_id']){
    header( 'Location: index.php' ) ;
    return;
  }

  $sql = "DELETE FROM profile WHERE profile_id = :p_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':p_id' => $_POST['profile_id']));
  $_SESSION['success'] = 'Profile deleted';
  header( 'Location: index.php' ) ;
  return;
}

// Guardian: Make sure that profile_id is present
if(!isset($_GET['profile_id'])){
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id=:p_id");
$stmt->execute(array(":p_id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if($row === false){
  $_SESSION['error'] = 'Bad value for profile_id';
  header( 'Location: index.php' ) ;
  return;
}

?>

<!-- View -->
<!DOCTYPE html>
<html>
 <head>
  <title>Fatma Al-Shehhi</title>
 </head>
<body>
  
<h1>Deleting Profile</h1>

<p>First Name: <?= htmlentities($row['first_name']); ?></p>
<p>Last Name: <?= htmlentities($row['last_name']); ?></p>

<form method="post">
<input type="hidden" name="profile_id" value="<?= $row['profile_id']; ?>">
<input type="submit" name="delete" value="Delete">
<input type="submit" name="cancel" value="Cancel">
</form>

</body>
</html>

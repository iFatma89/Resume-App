<!-- Model -->
<?php
require_once('pdo.php');
require_once('util.php');
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
 
// If the user clicked the save btn
if(isset($_POST['save']) && isset($_POST['profile_id'])){
    // Validate the input
    if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){

            $msg=validateProfile();

            if(is_string($msg)){
                $_SESSION['error']=$msg;
                header('Location: edit.php?profile_id='.$_POST['profile_id']);
                return;
            }
            

            $msg=validatePosition();

            if(is_string($msg)){
                $_SESSION['error']=$msg;
                header('Location: edit.php?profile_id='.$_POST['profile_id']);
                return;
            }

            $msg=validateEducation();

            if(is_string($msg)){
                $_SESSION['error']=$msg;
                header('Location: edit.php?profile_id='.$_POST['profile_id']);
                return;
            }

            // **Success**
            $stmt = $pdo->prepare('UPDATE Profile SET 
              first_name=:fn, 
              last_name=:ln, 
              email=:em, 
              headline=:he, 
              summary=:su 
              WHERE profile_id=:pid AND user_id=:uid'); 
 
            $stmt->execute(array(
              ':uid' => $_SESSION['user_id'],
              ':fn' => $_POST['first_name'],
              ':ln' => $_POST['last_name'],
              ':em' => $_POST['email'],
              ':he' => $_POST['headline'],
              ':su' => $_POST['summary'],
              ':pid' => $_POST['profile_id']
            ));


            // Clear old position D
            $stmt=$pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
            $stmt->execute(array(
              ':pid' => $_POST['profile_id']
            ));
            
            // Insert the new position D
            insertPositions($pdo, $_POST['profile_id']);

            // Clear old education D
            $stmt=$pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
            $stmt->execute(array(
              ':pid' => $_POST['profile_id']
            ));
            // Insert the new education D
            insertEducations($pdo, $_POST['profile_id']);

            $_SESSION['success'] = "Profile Updated";
            // When D is successfully added to DB
            header('Location: index.php');
            return;   
    }
}
?>

<!-- View -->
<!DOCTYPE html>
<html>
 <head>
  <title>Fatma Al-Shehhi</title>
  <link rel="stylesheet" 
  href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" 
  integrity="sha384-xewr6kSkq3dBbEtB6Z/3oFZmknWn7nHqhLVLrYgzEFRbU/DHSxW7K3B44yWUN60D" 
  crossorigin="anonymous">
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

  <?php 

  $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id=:p_id");
  $stmt->execute(array(":p_id" => $_GET['profile_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  
  // If the current logged in user does not own the entry in the DB
  if($_SESSION['user_id']!=$row['user_id']){
    // die('Cannot edit other users profiles');
    // return;
    $_SESSION['error']="Could not load profile";
    header('Location: index');
    return;
  }

  // Declare variables
  $first_name = htmlentities($row['first_name']);
  $last_name = htmlentities($row['last_name']);
  $email = htmlentities($row['email']);
  $headline = htmlentities($row['headline']);
  $summary = htmlentities($row['summary']);
  $profile_id = $row['profile_id'];

  ?>
  <h1>Editing Profile for <?php echo htmlentities($first_name); ?></h1>

  <?= flashMessages(); ?>     
    <form method="post" action="edit.php">
        <p>First Name:
        <input type="text" name="first_name" value="<?= $first_name; ?>" size="60"/></p>
        <p>Last Name:
        <input type="text" name="last_name" value="<?= $last_name; ?>" size="60"/></p>
        <p>Email:
        <input type="text" value="<?= $email; ?>" name="email"/></p>
        <p>Headline:<br/>
        <input type="text" name="headline" value="<?= $headline; ?>" size="71"/></p>
        <p>Summary:<br/>
        <textarea name="summary" rows="10" cols="72"><?= $summary; ?></textarea></p>
        <?php
         
        // Load the education rows 
        $edu=0;
        echo('<p>Education: <input type="submit" id="addEdu" value="+">'."\n");
        echo ('<div id="edu_fields">'."\n");

        $stmt=$pdo->prepare('SELECT year, name FROM Education AS e JOIN Institution AS i ON e.institution_id=i.institution_id WHERE profile_id=:pid ORDER BY rank');
        $stmt->execute(array(':pid' => $profile_id));
        while($school=$stmt->fetch(PDO::FETCH_ASSOC)){
          $edu++;
          echo('<div id="edu'.$edu.'">'."\n");
          echo ('<p>Year: <input type="text" name="edu_year'.$edu.'" value="'.$school['year'].'" />'."\n");
          echo('<input type="button" value="-" onclick="$(\'#edu'.$edu.'\').remove(); return false;">'."\n");
          echo('</p>'."\n");

          echo('<p>School:<input type="text" size="80" name="edu_school'.$edu.'" class="school" value="'.htmlentities($school['name']).'"/>'."\n");
          echo('</div>'."\n");
        }
        echo('</div></p>'."\n");


        // Load the postion rows 
        $pos=0;
        echo('<p>Position: <input type="submit" id="addPos" value="+">'."\n");
        echo ('<div id="position_fields">'."\n");
        $stmt=$pdo->prepare('SELECT * FROM Position WHERE profile_id=:pid ORDER BY rank');
        $stmt->execute(array(':pid' => $profile_id));
        while($position=$stmt->fetch(PDO::FETCH_ASSOC)){
          $pos++;
          echo('<div id="position'.$pos.'">'."\n");
          echo ('<p>Year: <input type="text" name="year'.$pos.'" value="'.$position['year'].'" />'."\n");
          echo('<input type="button" value="-" onclick="$(\'#position'.$pos.'\').remove(); return false;">'."\n");
          echo('</p>'."\n");
          echo('<textarea name="desc'.$pos.'" rows="8" cols="72">'."\n");
          echo(htmlentities($position['description'])."\n");
          echo('</textarea></div>');
        }
        echo('</div></p>'."\n");
       ?>

        <input type="hidden" name="profile_id" value="<?= $profile_id; ?>">
        <input type="submit" name="save" value="Save">
        <input type="submit" name="cancel" value="Cancel">
    </form>

    <script>
    countPos=<?= $pos; ?>;
    countEdu=<?= $edu; ?>;

    $(document).ready(function(){
      window.console && console.log('Document ready called');
      $('#addPos').click(function(event){
        event.preventDefault();
        if(countPos>= 9){
          alert("Maximum of nine position entries exceeded");
          return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
          '<div id="position'+countPos+'"> \
          <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
          <input type="button" value="-" \
          onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
          <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
          </div>');
      });
    });
    $('#addEdu').click(function(event){
        event.preventDefault();
        if ( countEdu >= 9 ) {
          alert("Maximum of nine education entries exceeded");
          return;
        }
      countEdu++;
      window.console && console.log("Adding education "+countEdu);

      $('#edu_fields').append(
        '<div id="edu'+countEdu+'"> \
        <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
        <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"><br>\
        <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
        </p></div>'
      );

      $('.school').autocomplete({
        source: "school.php"
      });

    });
    </script>
</body>
</html>
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

// If the user clicked the Add btn
if(isset($_POST['add'])){
    // Validate the input
    if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){
            $msg=validateProfile();

            if(is_string($msg)){
                $_SESSION['error']=$msg;
                header('Location: add.php');
                return;
            }

            $msg=validatePosition();

            if(is_string($msg)){
                $_SESSION['error']=$msg;
                header('Location: add.php');
                return;
            }

            $msg=validateEducation();

            if(is_string($msg)){
                $_SESSION['error']=$msg;
                header('Location: add.php');
                return;
            }
 
            // **Success**
            $stmt = $pdo->prepare('INSERT INTO Profile
            (user_id, first_name, last_name, email, headline, summary)
            VALUES ( :uid, :fn, :ln, :em, :he, :su)');

            $stmt->execute(array(
              ':uid' => $_SESSION['user_id'],
              ':fn' => $_POST['first_name'],
              ':ln' => $_POST['last_name'],
              ':em' => $_POST['email'],
              ':he' => $_POST['headline'],
              ':su' => $_POST['summary'])
            );
            
            $profile_id=$pdo->lastInsertId();

            // Insert the new position D
            insertPositions($pdo, $profile_id);
            // Insert the new education D
            insertEducations($pdo, $profile_id);

            $_SESSION['success'] = "Profile added";
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
    <h1>Adding Profile for <?php
    if(isset($_SESSION['name'])){
      echo htmlentities($_SESSION['name']);
    }?></h1>
    
    <?= flashMessages(); ?>      
    <form method="post">
        <p>First Name:
        <input type="text" name="first_name" size="60"/></p>
        <p>Last Name:
        <input type="text" name="last_name" size="60"/></p>
        <p>Email:
        <input type="text" name="email"/></p>
        <p>Headline:<br/>
        <input type="text" name="headline" size="71"/></p>
        <p>Summary:<br/>
        <textarea name="summary" rows="10" cols="72"></textarea></p>
        Education: <input type="submit" id="addEdu" value="+">
        <div id="edu_fields"></div>
        Position: <input type="submit" id="addPos" value="+">
        <div id="position_fields"></div>
        <input type="submit" name="add" value="Add">
        <input type="submit" name="cancel" value="Cancel">
    </form>

    <script>
        countPos=0;
        $(document).ready(function(){


            window.console && console.log('Document ready called');
            $('#addPos').click(function(event){
                // http://api.jquery.com/event.preventdefault/
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
        
        countEdu=0;
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








        // $('#addEdu').click(function(event){
        //     // http://api.jquery.com/event.preventdefault/
        //     event.preventDefault();
        //     if(countEdu >= 9){
        //         alert("Maximum of nine education entries exceeded");
        //         return;
        //     }
        //     countEdu++;
        //     window.console && console.log("Adding education "+countEdu);

        //     // Grab some html with hot spots and insert into the DOM
        //     var source=$('#edu_template').html();
        //     $('#edu_fields').append(source.replace(/@COUNT@/g,countEdu));

        //     // Add the event handler to the new ones
        //     $('.school').autocomplete({
        //         source: "school.php"
        //     });
        // });
            // $('.school').autocomplete({
            //     source: "school.php"
            // });
        // });
    </script>

    <!-- HIDDEN -->
    <!-- HTML with Substitution hot spots -->
    <!-- <script id="edu_template" type="text">
      <div id="edu@COUNT@">
        <p>Year: <input type="text" name="edu_year@COUNT@" value="" />
        <input type="button" value="-" onclick="$('#edu@COUNT@').remove(); return false;"><br></p>
        <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value="" autocomplete="off"/></p>
      </div>
    </script> -->
</body>
</html>
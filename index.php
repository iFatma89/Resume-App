<!-- Model -->
<?php
session_start(); 
require_once('pdo.php');
require_once('util.php');
?>
<!-- View -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fatma Al-Shehhi</title>
</head>
<body>
    <h1>Fatma Al-Shehhi's Resume Registry</h1>
    <?php 

            $stmt = $pdo->query("SELECT * FROM profile");
            $count = $stmt->rowCount();

            // Not logged in user
            if(!isset($_SESSION["user_id"])){
                echo('<a href="login.php">Please log in</a><br><br>'); 
               
                if($count>0){
                echo('<table border="1">'."\n");
                echo('<thead><tr><td><b>Name</b></td><td><b>Headline</b></td></tr></thead>');
                while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                    echo "<tr><td>";
                    echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row["first_name"].$row["last_name"]).'</a>');
                    echo("</td><td>");
                    echo(htmlentities($row['headline']));
                    echo("</td><tr>\n");
                }
                echo('</table><br>');
            } 
            // Logged in user
            }else{ 
                flashMessages();
                echo('<a href="logout.php">Logout</a><br><br>');
               
                if($count>0){
                    echo('<table border="1">'."\n");
                    echo('<thead><tr><td><b>Name</b></td><td><b>Headline</b></td><td><b>Action</b></td></tr></thead>');
                    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                        echo "<tr><td>";
                        echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row["first_name"].' '.$row["last_name"]).'</a>');
                        echo("</td><td>");
                        echo(htmlentities($row['headline']));
                        echo("</td><td>");
                        echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
                        echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
                        echo("</td></tr>\n");
                    }
                    echo('</table><br>');
                }else{
                    echo('No Rows Found<br><br>');
                }

                echo('<a href="add.php">Add New Entry</a><br><br>');
                echo('<p><b>Note</b>: Your implementation should retain data across multiple logout/login sessions. This sample implementation clears all its data on logout - which you should not do in your implementation.</p>');
            }
    ?>
    
</body>
</html>
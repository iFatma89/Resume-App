<!-- Model -->
<?php
require_once('pdo.php');
require_once('util.php');
session_start();

if(isset($_POST['cancel'])){
    // Redirect the browser to index.php
    header("Location: index.php");
    return;
}
$salt = 'XyZzy12*_';
// $stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123

// Check to see if we have some POST data, if we do process it
if(isset($_POST['email']) && isset($_POST['pass'])){
	  unset($_SESSION["name"]);  // Logout current user
	  unset($_SESSION["user_id"]);
    if(strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1){
      $_SESSION["error"] = "User name and password are required";
      header('Location: login.php');
      return;
    }else {
				if(strpos($_POST['email'], '@') !== false){  
					$check = hash('md5', $salt.$_POST['pass']);

            $stmt = $pdo->prepare('SELECT * FROM users
						WHERE email = :em AND password = :pw');

            $stmt->execute(array(
            	':em' => $_POST['email'],
            	':pw' => $check
            ));
					  $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $stmt->rowCount();
            // 1 user is found
            if($count>0){
            	$_SESSION["user_id"] = $user["user_id"];
            	$_SESSION["name"] = $user["name"];
	            $_SESSION["success"] = "Logged in.";
		        	// error_log("Login success ".$_POST['email']);
		          // Redirect the browser to index.php
		          header("Location: index.php");
	          	return;
            }
          //   else{
          //   	$_SESSION["error"] = "No user found";
				      // header('Location: login.php');
				      // return;
          //   }

            else{
	          $_SESSION["error"] = "Incorrect password";
	          // error_log("Login fail ".$_POST['email']." $check");
	          header('Location: login.php');
            return;
	        }
				}else{
					  $_SESSION["error"] = "Email must have an at-sign (@)";
					  header('Location: login.php');
            return;
				}
    } 
}

?>
<!-- View -->
<!DOCTYPE html>
<html>
<head>
	<title>Fatma Al-Shehhi</title>
</head>
<body>
	<h1>Please Log In</h1>
	<?= flashMessages(); ?> 
	<form method="POST" action="login.php">
		<label for="email">Email</label>
		<input type="text" name="email" id="email"><br/>
		<label for="id_1723">Password</label>
		<input type="password" name="pass" id="id_1723"><br/>
		<input type="submit" onclick="return doValidate();" value="Log In">
		<input type="submit" name="cancel" value="Cancel">
	</form>
	<p>
	For a password hint, view source and find an account and password hint
	in the HTML comments.
	<!-- Hint: 
	The account is umsi@umich.edu
	The password is the three character name of the 
	programming language used in this class (all lower case) 
	followed by 123. -->
	</p>
	<script>
	function doValidate() {
	    console.log('Validating...');
	    try {
	        addr = document.getElementById('email').value;
	        pw = document.getElementById('id_1723').value;
	        console.log("Validating addr="+addr+" pw="+pw);
	        if (addr == null || addr == "" || pw == null || pw == "") {
	            alert("Both fields must be filled out");
	            return false;
	        }
	        if ( addr.indexOf('@') == -1 ) {
	            alert("Invalid email address");
	            return false;
	        }
	        return true;
	    } catch(e) {
	        return false;
	    }
	    return false;
	}
	</script>



</body>
</html>


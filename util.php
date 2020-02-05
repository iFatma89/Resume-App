<?php 

function flashMessages(){
	if(isset($_SESSION["error"])){
	  echo('<p style="color: red;">'.htmlentities($_SESSION["error"])."</p>\n");
	  unset($_SESSION["error"]);
	}
	if(isset($_SESSION['success'])){
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
  	unset($_SESSION['success']);
  }
}

function validateProfile(){
	if( strlen($_POST['first_name']) < 1 || 
		  strlen($_POST['last_name']) < 1 || 
      strlen($_POST['email']) < 1 ||
      strlen($_POST['headline']) < 1 || 
      strlen($_POST['summary']) < 1 ){
		return "All fields are required";
  }
  if(strpos($_POST['email'], '@') === false){
  	return "Email address must contain @";
  }
  return true;	
}

function validatePosition(){
	for($i=1; $i <+9 ; $i++){ 
		if(!isset($_POST['year'.$i])) continue;
    if(!isset($_POST['desc'.$i])) continue;
    $year=$_POST['year'.$i];
    $desc=$_POST['desc'.$i];
    if(strlen($year)<1 || strlen($desc)<1){
    	return "All fields are required";
    }
    if(!is_numeric($year)){
			return "Position year must be numeric";
	  }
	}
	return true;
}

function validateEducation(){
  for($i=1; $i <+9 ; $i++){ 
    if(!isset($_POST['edu_year'.$i])) continue;
    if(!isset($_POST['edu_school'.$i])) continue;
    $year=$_POST['edu_year'.$i];
    $school=$_POST['edu_school'.$i];
    if(strlen($year)<1 || strlen($school)<1){
      return "All fields are required";
    }
    if(!is_numeric($year)){
      return "Education year must be numeric";
    }
  }
  return true;
}

function insertPositions($pdo, $profile_id){
  $rank=1;
  for($i=1; $i <=9 ; $i++){
    if(!isset($_POST['year'.$i])) continue;
    if(!isset($_POST['desc'.$i])) continue;
    $year=$_POST['year'.$i];
    $desc=$_POST['desc'.$i];

    $stmt=$pdo->prepare('INSERT INTO Position
           (profile_id, rank, year, description)
    VALUES ( :pid, :rank, :year, :desc)');

    $stmt->execute(array(
        ':pid' => $profile_id,
        ':rank' => $rank,
        ':year' => $year,
        ':desc' => $desc
    ));
    $rank++;
  }
}

function insertEducations($pdo, $profile_id){
  $rank=1;
  for($i=1; $i <=9 ; $i++){
    if(!isset($_POST['edu_year'.$i])) continue;
    if(!isset($_POST['edu_school'.$i])) continue;
    $year=$_POST['edu_year'.$i];
    $school=$_POST['edu_school'.$i];

    // Loooking up the school if its there
    $institution_id=false;
    $stmt=$pdo->prepare('SELECT institution_id FROM Institution WHERE name=:name'); 

    $stmt->execute(array(        
      ':name' => $school
    ));

    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    if($row!=false){
      $institution_id=$row['institution_id'];
    }

    // If there was no institution, insert it
    if($institution_id===false){
      $stmt=$pdo->prepare('INSERT INTO Institution (name)
             VALUES (:name)');
      $stmt->execute(array(        
          ':name' => $school
      ));
      $institution_id=$pdo->lastInsertId();
    } 

    $stmt=$pdo->prepare('INSERT INTO Education
           (profile_id, rank, year, institution_id)
    VALUES ( :pid, :rank, :year, :iid)');

    $stmt->execute(array(
        ':pid' => $profile_id,
        ':rank' => $rank,
        ':year' => $year,
        ':iid' => $institution_id
    ));
    $rank++;
  }
}



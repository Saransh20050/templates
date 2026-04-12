<?php
include("db.php");
session_start();
?>

<html>
  <head>
    <title>
    Login
	</title>

    <style>
      body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #667eea, #764ba2);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
      }

      form {
        background: #fff;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        width: 300px;
      }

      label {
        font-weight: bold;
        display: block;
        margin-bottom: 6px;
        color: #333;
      }

      input[type="text"],
      input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        outline: none;
        transition: 0.3s;
      }

      input[type="text"]:focus,
      input[type="password"]:focus {
        border-color: #667eea;
        box-shadow: 0 0 5px rgba(102,126,234,0.5);
      }

      input[type="submit"] {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 8px;
        background: #667eea;
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
      }

      input[type="submit"]:hover {
        background: #5a67d8;
      }
    </style>

  </head>
  <body>
    <form method="post" action="login.php">
	  <label>Username</label>
	  <input type="text" name="uname"><br><br>
	   <label>Password</label>
	  <input type="password" name="pw"><br><br>
	  <input type="submit" name="sub" value="SUBMIT">
	</form>
  </body>
</html>


<?php
if($_SERVER["REQUEST_METHOD"]=="POST"){
	$last_success;
	$last_failure;
	if(isset($_POST["uname"])&&isset($_POST["pw"])){
	 $uname=$_POST["uname"];
	 $pw=$_POST["pw"];
	 if(strlen($pw)<6){echo"Password too short<br>";}
	else{
	  $res=mysqli_query($conn,"SELECT * FROM reg where uname='$uname' and pw='$pw'");
	  if(mysqli_num_rows($res)>0){
		$row=mysqli_fetch_assoc($res);
        echo"Welcome {$row["uname"]}<br>";	
		$stats="success";
		$now=new DateTime();
		$now1 = $now->format('Y-m-d H:i:s');
	    mysqli_query($conn,"INSERT INTO users(uname,pw,time,status) VALUES('$uname','$pw','$now1','$stats')"); 
		$_SESSION["uname"]=$uname;
		$_SESSION["pw"]=$pw;
	  $last_success="{$uname},{$now1}";
	  $_SESSION["last_success"]="{$last_success} is the last successful login attempt";
		header("Location:home.php");
		exit;
	  }
      else{
		echo"User not found!<br>";
		$now=new DateTime();
		$now1=$now->format('Y-m-d H:i:s');
		$stats="failure";
	    mysqli_query($conn,"INSERT INTO users(uname,pw,time,status) VALUES('$uname','$pw','$now1','$stats')"); 
		$last_failure="{$uname},{$now1}";
		$_SESSION["last_failure"]="{$last_failure} is the last failed login attempt";
		header("Location:register.php");
		exit;
	  }	  
	}
	}
	else{
       echo"Both fields mandatory<br>";
	}
}
?>
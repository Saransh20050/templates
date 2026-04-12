<?php
include("db.php");
?>

<html>
  <head>
    <title>
    Register
	</title>

    <style>
      body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #43cea2, #185a9d);
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
        border-color: #43cea2;
        box-shadow: 0 0 5px rgba(67,206,162,0.5);
      }

      input[type="submit"] {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 8px;
        background: #43cea2;
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
      }

      input[type="submit"]:hover {
        background: #36b28a;
      }
    </style>

  </head>
  <body>
    <form method="post" action="register.php">
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
	if(isset($_POST["uname"])&&isset($_POST["pw"])){
	 $uname=$_POST["uname"];
	 $pw=$_POST["pw"];
	 if(strlen($pw)<6){echo"Password too short<br>";}
	else{
	$res=mysqli_query($conn,"SELECT * FROM reg where uname='$uname'");
	  if(mysqli_num_rows($res)>0){
        echo"User already exists<br>";		
	  }
      else{
        mysqli_query($conn,"INSERT INTO reg(uname,pw) VALUES('$uname','$pw')"); 
		echo"Succesfully Registered!<br>";
		header("Location:login.php");
		exit;
	  }	  
	}
	}
	else{
       echo"Both fields mandatory<br>";
	}
}
?>
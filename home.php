<?php
include("db.php");
session_start();
if(!isset($_SESSION["uname"])||!isset($_SESSION["pw"])){exit;}
$uname=$_SESSION["uname"];
$pw=$_SESSION["pw"];
echo"Welcome {$uname}<br>";

?>

<html>
<head>
 <title>
   homepage
 </title>
 <style>
  body{
	  text-align:center;
	 
  }
   button{
	border:solid 1px;
border-radius:50%;
background-color:yellow;	
   }
   .container{
	   background-color:black;
	   width:200px;
	   height:100px;
	   margin-left:650px;
   }
  
 </style>
</head>
<body>

 <h1>Your Calculator!</h1>
<form method="post" action="home.php">
 <div class="container">
   <div>
   <input type="text" value="0" id="screen" name="display"/>
   </div>
   <div>
      <button type="button" class="operator">+</button>
	  <button type="button" class="operator">-</button>
	  <button type="button" class="operator">*</button>
	  <button type="button" class="operator">/</button>
   </div>
   <div>
     <button type="button" class="operand">0</button>
     <button type="button" class="operand">1</button>
	 <button type="button" class="operand">2</button>
	 <button type="button" class="operand">3</button>
	 <button type="button" class="operand">4</button>
   </div>
   <div>
   <button type="button" class="operand">5</button>
	 <button type="button" class="operand">6</button>
	 <button type="button" class="operand">7</button>
	 <button type="button" class="operand">8</button>
	 <button type="button" class="operand">9</button>
   </div>
   <div>
    <button type="submit" name="sub" id="cal">Calc</button>
	<button type="button" name="rst" id="rst">Reset</button>
   </div>
 </div>
 </form>
 <script>
  let operators=document.querySelectorAll(".operator");
  let operands=document.querySelectorAll(".operand");
  let cal=document.querySelector("#cal");
  let screen=document.querySelector("#screen");
  let rst=document.querySelector("#rst");
  let x="";
  for(let i=0;i<operators.length;i++){
	  operators[i].addEventListener("click",()=>{
	   x+=operators[i].innerText;
       screen.value=x;	   
	  });
  }
   for(let i=0;i<operands.length;i++){
	  operands[i].addEventListener("click",()=>{
	   x+=operands[i].innerText; 	 
       screen.value=x;	   
	  });
  }
  cal.addEventListener("click",()=>{
   x=eval(x);
  alert("answer is "+x);
  });
  rst.addEventListener("click",()=>{
  x="";
  screen.value=x;
  });
  
 </script>
</body>
</html>

<?php
if($_SERVER["REQUEST_METHOD"]=="POST"){
	if(isset($_POST["sub"])){
	  $screen=$_POST["display"];
	  $uname=$_SESSION["uname"];
	  mysqli_query($conn,"INSERT INTO calc(uname,expr) VALUES('$uname','$screen')");
	}
}


?>


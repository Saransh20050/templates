<?php
include("db.php");
session_start();
if(!isset($_SESSION["uname"])||!isset($_SESSION["pw"])){exit;}
$uname=$_SESSION["uname"];
$pw=$_SESSION["pw"];
echo "Welcome {$uname}<br>";
?>

<html>
<head>
<title>Product System</title>

<style>
body{
  font-family: Arial;
  text-align:center;
  background:#2c3e50;
  color:white;
}

table{
  margin:auto;
  margin-top:20px;
  border-collapse:collapse;
  background:white;
  color:black;
}

th,td{
  border:1px solid black;
  padding:8px;
}

button{
  padding:5px 10px;
  margin:2px;
  cursor:pointer;
}

.qty{
  width:40px;
  text-align:center;
}

input[type="submit"]{
  margin-top:20px;
  padding:10px;
  background:#27ae60;
  color:white;
  border:none;
  cursor:pointer;
}

#search{
  padding:8px;
  width:250px;
}
</style>

</head>

<body>

<h2>🛍️ Products</h2>

<input type="text" id="search" placeholder="Search product...">

<form method="post">

<table id="productTable">
<tr>
<th>Product</th>
<th>Price</th>
<th>Quantity</th>
</tr>

<?php
$res=mysqli_query($conn,"SELECT * FROM products");
while($row=mysqli_fetch_assoc($res)){
  echo "<tr>
  <td>{$row['name']}</td>
  <td>{$row['price']}</td>
  <td>
    <button type='button' onclick='dec(this)'>-</button>
    <input type='text' name='qty[{$row['name']}]' value='0' class='qty'>
    <button type='button' onclick='inc(this)'>+</button>
  </td>
  </tr>";
}
?>
</table>

<input type="submit" name="sub" value="Place Order">

</form>

<script>
// increment
function inc(btn){
  let input = btn.previousElementSibling;
  input.value = parseInt(input.value) + 1;
}

// decrement
function dec(btn){
  let input = btn.nextElementSibling;
  if(input.value > 0){
    input.value = parseInt(input.value) - 1;
  }
}

// search filter
let search=document.getElementById("search");
search.addEventListener("keyup",function(){
  let val=search.value.toLowerCase();
  let rows=document.querySelectorAll("#productTable tr");

  for(let i=1;i<rows.length;i++){
    let text=rows[i].innerText.toLowerCase();
    rows[i].style.display = text.includes(val) ? "" : "none";
  }
});
</script>

</body>
</html>

<?php
// ===== ORDER INSERT + GRAND TOTAL =====
if(isset($_POST["sub"])){

  $grand_total = 0;

  if(isset($_POST["qty"])){

    foreach($_POST["qty"] as $item=>$qty){

      if($qty > 0){

        $res=mysqli_query($conn,"SELECT price FROM products WHERE name='$item'");
        $row=mysqli_fetch_assoc($res);

        if($row){
          $price=$row["price"];
          $total=$price*$qty;

          $grand_total += $total;

          mysqli_query($conn,"
          INSERT INTO orders(uname,item,qty,price,total)
          VALUES('$uname','$item','$qty','$price','$total')
          ");
        }
      }
    }

    echo "<br>✅ Order placed successfully!<br>";
    echo "<h3>🧾 Your Total Bill = ₹$grand_total</h3>";
  }
}
?>

<!-- ================= ADMIN ================= -->

<h2>📊 Item Summary</h2>

<table>
<tr>
<th>Item</th>
<th>Total Qty</th>
</tr>

<?php
$res=mysqli_query($conn,"
SELECT item, SUM(qty) as total_qty
FROM orders
GROUP BY item
");

while($row=mysqli_fetch_assoc($res)){
  echo "<tr>
  <td>{$row['item']}</td>
  <td>{$row['total_qty']}</td>
  </tr>";
}
?>
</table>

<h2>👤 Orders Grouped by User</h2>

<table>
<tr>
<th>User</th>
<th>Item</th>
<th>Qty</th>
<th>Total</th>
</tr>

<?php
$res=mysqli_query($conn,"
SELECT uname,item,qty,total
FROM orders
ORDER BY uname
");

while($row=mysqli_fetch_assoc($res)){
  echo "<tr>
  <td>{$row['uname']}</td>
  <td>{$row['item']}</td>
  <td>{$row['qty']}</td>
  <td>{$row['total']}</td>
  </tr>";
}
?>
</table>

<h2>💰 Total Spending Per User</h2>

<table>
<tr>
<th>User</th>
<th>Grand Total</th>
</tr>

<?php
$res=mysqli_query($conn,"
SELECT uname, SUM(total) as grand_total
FROM orders
GROUP BY uname
");

while($row=mysqli_fetch_assoc($res)){
  echo "<tr>
  <td>{$row['uname']}</td>
  <td>{$row['grand_total']}</td>
  </tr>";
}
?>
</table>
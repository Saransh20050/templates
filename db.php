<?php
$conn=mysqli_connect("localhost","root","","auth");


//DB: auth
//tables:reg(id,uname,pw),users(id,uname,pw,time,status)


//for products:
/*
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50),
  price INT
);

-- 2. Create Orders Table
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  uname VARCHAR(50),
  item VARCHAR(50),
  qty INT,
  price INT,
  total INT
);*/

/* insert sample products:
INSERT INTO products(name,price) VALUES
('Tyre',500),
('Tube',200),
('Brake',300),
('Chain',400),
('Shirt',700),
('Pant',800),
('Blouse',600),
('Suit',1500);


*/
?>

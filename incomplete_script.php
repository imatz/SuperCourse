<?php 
$con=mysql_connect("localhost","supeshop_shopadm","2oP^NGLt4oEh");
mysql_select_db("supeshop_shop",$con);

$query = mysql_query("SELECT order_id FROM cscart_orders WHERE notification_flag = '0' AND status = 'N'");


$i = -1;
while($row = mysql_fetch_array($query))
{
	$i++;
	$order_id[$i]['order_id']=$row['order_id'];	
}

$counter = count($order_id);
$headers = "From: Super Course ELT Publishing eShop <admin@supercourse-eshop.gr>\r\n";
$headers .= "Reply-To: admin@supercourse-eshop.gr\r\n";
$headers .= "Return-Path: admin@supercourse-eshop.gr\r\n";


for($i = 0; $i < $counter; $i++)
{
	$comma_separated = implode(",", $order_id[$i]);
	$to = "SC eShop Support Team <support@supercourse-eshop.gr>";
	$subject = "Super Course ELT Publishing: Error with Incompleted Order CC";
	$msg = "Please, login to the Admin Panel and check for any payment problems (Credit Card) with incompleted order No: \n" . $comma_separated;
	$mail = mail($to, $subject, $msg, $headers);
	$oid = $order_id[$i]['order_id'];
	$query = mysql_query("UPDATE cscart_orders SET notification_flag = '1' WHERE order_id = '$oid'");
}

if($mail)
{
	echo 'Mail Sent';
}
else
{
	echo 'Mail Failed';
}
?>
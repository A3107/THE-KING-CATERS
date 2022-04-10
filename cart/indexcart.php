<?php
session_start();
include('classes.php');
if(isset($_SESSION['s_em']) == false)
{
	header('location: login1.php');
}
else
{
	$a=$_SESSION['s_em'];
}
?>
<?php
	require_once("dbcontroller1.php");
		$db_handle = new DBController();
		if(!empty($_GET["action"])) {
		switch($_GET["action"]) {
			case "add":
				if(!empty($_POST["quantity"])) 
				{
					$productByCode = $db_handle->runQuery("SELECT * FROM tblitems WHERE code='" . $_GET["code"] . "'");
					$itemArray = array($productByCode[0]["code"]=>array('name'=>$productByCode[0]["name"], 'code'=>$productByCode[0]["code"], 'quantity'=>$_POST["quantity"], 'price'=>$productByCode[0]["price"]));
					
					if(!empty($_SESSION["cart_item"]))
					{
						if(in_array($productByCode[0]["code"],array_keys($_SESSION["cart_item"])))
						{
							foreach($_SESSION["cart_item"] as $k => $v)
							{
								if($productByCode[0]["code"] == $k)
								{
									if(empty($_SESSION["cart_item"][$k]["quantity"])) 
									{
										$_SESSION["cart_item"][$k]["quantity"] = 0;
									}
									$_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
								}
							}
						}
						else
						{
							$_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
						}
					}
					else
					{
						$_SESSION["cart_item"] = $itemArray;
					}
				}
			break;
			case "remove":
				if(!empty($_SESSION["cart_item"])) {
					foreach($_SESSION["cart_item"] as $k => $v) {
							if($_GET["code"] == $k)
								unset($_SESSION["cart_item"][$k]);				
								if(empty($_SESSION["cart_item"]))
								unset($_SESSION["cart_item"]);
					}
				}
			break;
			case "empty":
				unset($_SESSION["cart_item"]);
			break;	
		}
		}
	?>
<!DOCTYPE html>
<html>
<title>Online Order</title>
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<link rel="stylesheet" href="style.css" type="text/css"  />

</head>
<body style="background:url(img/77.jpeg);">
<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<span ><a href="../index3.php"style="text-decoration:none;padding:16px;color:#000000;"> Go To Home</a></span>
			<h1 class="spl_head" style="padding:50px;">Special Menu</h1>	
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6" style="box-shadow:1px 1px 15px black inset;overflow-y:scroll;height:500px;">
			<div id="product-grid" >
				<div class="txt-heading">Special Menus List</div>
				<?php
				$product_array = $db_handle->runQuery("SELECT * FROM tblitems ORDER BY id ASC");
				if (!empty($product_array)) { 
					foreach($product_array as $key => $value){
				?>			
						
						<div class="product-item">
						<form method="post" action="indexcart.php?action=add&code=<?php echo $product_array[$key]["code"]; ?>">
						<div style="box-shadow:1px 1px 12x black;"><strong ><?php echo $product_array[$key]["name"]; ?></strong></div>
						<div class="product-price"><?php echo "Rs ".$product_array[$key]["price"]; ?></div>
						<div><input type="text" name="quantity" value="1" size="2" /><input type="submit" value="Add to cart" class="btnAddAction" /></div>
						</form>
						</div>
				<?php
						}
				}
				?>
			</div>
		</div>
		<div class="col-lg-6" style="box-shadow:1px 1px 15px black inset;overflow-y:scroll;height:500px;">
			<div id="shopping-cart">
			<div class="txt-heading" >Add Menu items <a id="btnEmpty" href="indexcart.php?action=empty">Empty Cart</a></div>
			<?php
			if(isset($_SESSION["cart_item"])){
				$item_total = 0;
			?>	
			<table cellpadding="10" cellspacing="1">
			<tbody>
			<tr>
			<th style="text-align:left;padding:5px;"><strong>Name</strong></th>
			<th style="text-align:left;"><strong>Code</strong></th>
			<th style="text-align:right;"><strong>Quantity</strong></th>
			<th style="text-align:right;"><strong>Price</strong></th>
			<th style="text-align:center;"><strong>Action</strong></th>
			</tr>	
			<?php		
				foreach ($_SESSION["cart_item"] as $item){
					
					?>
							<tr>
							<td style="text-align:left;border-bottom:#F0F0F0 1px solid;"><strong><?php echo $item["name"]; ?></strong></td>
							<td style="text-align:left;border-bottom:#F0F0F0 1px solid;"><?php echo $item["code"]; ?></td>
							<td style="text-align:right;border-bottom:#F0F0F0 1px solid;"><?php echo $item["quantity"]; ?></td>
							<td style="text-align:right;border-bottom:#F0F0F0 1px solid;"><?php echo "Rs ".$item["price"]; ?></td>
							<td style="text-align:center;border-bottom:#F0F0F0 1px solid;"><a href="indexcart.php?action=remove&code=<?php echo $item["code"]; ?>" class="btnRemoveAction">Remove Item</a></td>
							</tr>
					
					<?php
					$item_total += ($item["price"]*$item["quantity"]);
					}
					?>

					<tr>
					<td colspan="5" align=right><strong>Total:</strong> <?php echo "Rs.= ".$item_total; ?></td>
					</tr>
					</tbody>
					</table>
					<div style="padding:10px;margin-top:10px;">
					<form method="POST" action="">
					<input type="submit" name="subbtn" value="Confirm Order" style="padding:10px;text-decoration:none;background:white;">
					</form>
					 </div>
			</div>
		</div>
	</div>
</div>
 <?php
  if(isset($_POST['subbtn']))
  {
	  echo "<script>alert('Your Order is confirmed!');</script>";
	  echo('<script>location.href="login1.php"</script>');
	  
  }
}

?> 	
<li style="position:fixed;top:0;right:0;">
	<a href="logout.php" name="logout" style="color:#000000;font-size:20px;margin:20px;padding:10px;text-decoration:none;">Logout</a>
</li>

</BODY>
</HTML>	
<?php
/*************************************************************************************************************************************
*
*	88      a8P   ad88888ba   88888888ba,    
*	88    ,88'   d8"     "8b  88      `"8b   
*	88  ,88"     Y8,          88        `8b  
*	88,d88'      `Y8aaaaa,    88         88  
*	8888"88,       `"""""8b,  88         88  
*	88P   Y8b            `8b  88         8P  
*	88     "88,  Y8a     a8P  88      .a8P   
*	88       Y8b  "Y88888P"   88888888Y"'    
*
* 	This file is part of KSD's Wes software.
*   Copyright 2010 Karl Steltenpohl Development LLC. All Rights Reserved.
*	
*	Licensed under a Commercial Wes License (a "Wes License");
*	either the Wes Forever License (the "Forever License"),
*	or the Wes Annual Licencse (the "Annual License");
*	you may not use this file exept in compliance
*	with at least one Wes License.
*
*	You may obtain a copy of the Wes Licenses at	
*	http://www.wescms.com/license
*
*************************************************************************************************************************************/
include'../../../includes/config.php';

$Ecommerce = new Ecommerce();

// GET ORDER
$select = "SELECT * FROM ecommerce_orders WHERE order_id='".$_REQUEST['order_id']."' LIMIT 1";
$result = doQuery($select);
$row = mysql_fetch_array($result);

// SETUP PDF
$pdf=new FPDF('P','pt','Letter'); 
$pdf->AddPage(); 
$pdf->SetDrawColor(75);
$pdf->SetFont('Arial','B',18); 

// SET TITLE
$pdf->SetTitle('Order # '.$row['order_id'].'');

// LOGO
$pdf->Image('../../../uploads/'.$_SETTINGS['invoice_logo_image'].'',null,null,100,null); 

// START APPLICATION
// COMPANY  INFORMATION


$pdf->SetFont('Arial','',8); 
$pdf->Cell(420); $pdf->Cell(100,10,''.$_SETTINGS['return_address_name'].'',0,1,'R');
$pdf->Cell(420); $pdf->Cell(100,10,''.$_SETTINGS['return_address1'].' '.$_SETTINS['return_address2'].'',0,1,'R');
$pdf->Cell(420); $pdf->Cell(100,10,''.$_SETTINGS['return_address_city'].', '.lookupDbValue('state','state',$_SETTINGS['return_address_state'],'state_id').' '.$_SETTINGS['return_address_zip'].'',0,1,'R'); 
$pdf->Ln(10);
$pdf->Cell(420); $pdf->Cell(100,10,'Phone: '.$_SETTINGS['business_phone'].'',0,1,'R');
$pdf->Cell(420); $pdf->Cell(100,10,'Toll Free: '.$_SETTINGS['business_toll_free_phone'].'',0,1,'R');
$pdf->Cell(420); $pdf->Cell(100,10,'Fax: '.$_SETTINGS['business_fax'].'',0,1,'R');
$pdf->Cell(420); $pdf->Cell(100,10,'Email: '.$_SETTINGS['invoice_email'].'',0,1,'R');
$pdf->Cell(420); $pdf->Cell(100,10,'Website: '.$_SETTINGS['website_domain'].'',0,1,'R');
$pdf->Ln(20);

$pdf->Cell(200,10,'Order Number: '.$row['order_id'].'',0,1,'L');
$pdf->Cell(200,10,'Order Date: '.TimestampIntoDate($row['created']).'',0,1,'L');
$pdf->Cell(200,10,'Invoice Date: '.TimestampIntoDate($row['created']).'',0,1,'L');
$pdf->Ln(10);

$pdf->Cell(300,10,''.$_SETTINGS['invoice_note'].'',0,1,'L');
$pdf->Ln(10);

$pdf->Cell(300,10,'Line items for '.lookupDbValue('user_account','name',$row['account_id'],'account_id').'',0,1,'L');
$pdf->Ln(5);

// DISPLAY CART HERE
$pdf->Cell(100,10,'Product Number',1,0,'L');
$pdf->Cell(270,10,'Item',1,0,'L');
$pdf->Cell(30,10,'Qty',1,0,'R');
$pdf->Cell(50,10,'Price',1,0,'R');
$pdf->Cell(50,10,'Total',1,1,'R');
$pdf->Ln(10);
// GET CART
$select = "SELECT * FROM ecommerce_shopping_carts WHERE shopping_cart_id='".$row['shopping_cart_id']."' LIMIT 1";
$result = doQuery($select);
$shopping_cart = mysql_fetch_array($result);

// LIST CART ITEMS
$select = "SELECT * FROM ecommerce_product_cart_relational WHERE shopping_cart_id='".$shopping_cart['shopping_cart_id']."'";
$result = doQuery($select);
$num = mysql_num_rows($result);
$i = 0;
while($i<$num){
	$item = mysql_fetch_array($result);
	// GET THE PRODUCT DETAILS
	$sel = "SELECT * FROM ecommerce_products WHERE product_id='".$item['product_id']."'";
	$res = doQuery($sel);
	$product = mysql_fetch_array($res);
	
	//
	// FORMAT PRICE
	//
	$discount = '0.00';
	if($item['flat_discount'] != '0.00' || $item['rate_discount'] != '0.00'){
		
		// IF FLAT DISCOUNT
		if($item['flat_discount'] != '0.00'){
			$discount = $item['flat_discount'];
			
		}
		// IF RATE DISCOUNT
		elseif($item['rate_discount'] != '0.00'){
			$discount = $item['price'] * $item['rate_discount'];
		}
		
		
	}
	
	$list_price	= money_format('%i',$item['price']);
	$new_price = $item['price'] - $discount;
	$new_price = money_format('%i',$new_price);	
	
	
	$pdf->Cell(100,10,''.$product['product_number'].'',1,0,'L');
	$pdf->Cell(270,10,''.$product['name'].'',1,0,'L');
	$pdf->Cell(30,10,''.$item['qty'].'',1,0,'R');
	
	
	
	$pdf->Cell(50,10,''.$Ecommerce->currency.money_format('%i',$new_price).'',1,0,'R');
	
	$rowtotal = ($new_price * $item['qty']);
	$pdf->Cell(50,10,''.$Ecommerce->currency.money_format('%i',$rowtotal).'',1,1,'R');
	
	
	//
	// ATTRIBUTES
	//
	
	/**
	 *
	 * ATTRIBUTES FOR PDF
	 *
	 */
	
		// GET THE RELATIONAL ATTRIBUTES FOR THE CART ITEM ORDER BY ATTRIBUTE ID TO GROUP THE ATTRIBUETES
		$selecta = "SELECT * FROM ecommerce_product_attribute_cart_relational WHERE relational_item_id='".$item['item_id']."' ORDER BY attribute_id";
		$resulta = doQuery($selecta);
		$numa = mysql_num_rows($resulta);
		$ia = 0;
		while($ia<$numa){
			$rowa = mysql_fetch_array($resulta);
			
			// GET THE ATTRIBUTE
			$select1a = "SELECT * FROM ecommerce_product_attributes WHERE attribute_id='".$rowa['attribute_id']."' LIMIT 1";
			$result1a = doQuery($select1a);
			$attribute = mysql_fetch_array($result1a);
			
			// GET THE ATTRIBUTE VALUE
			$select1a = "SELECT * FROM ecommerce_product_attribute_values WHERE attribute_value_id='".$rowa['attribute_value_id']."' LIMIT 1";
			$result1a = doQuery($select1a);
			$attributeValue = mysql_fetch_array($result1a);
			
			$pdf->Cell(100,10,' ',1,0,'L');
			$pdf->Cell(270,10,''.$attribute['label'].' - '.$attributeValue['name'].' ',1,0,'L');
			$pdf->Cell(30,10,' ',1,0,'R');
			$pdf->Cell(50,10,' ',1,0,'R');
			$pdf->Cell(50,10,' ',1,1,'R');
			//$pdf->Ln(20);
			$ia++;
		}
		
		// NEW LINE
		
		$selecta = "SELECT * FROM ecommerce_product_cart_relational WHERE item_id='".$item['item_id']."' LIMIT 1";
		$resulta = doQuery($selecta);
		$rowa = mysql_fetch_array($resulta);
		if($rowa['note'] != ""){
			$pdf->Cell(500,10,'NOTE: '.$rowa['note'].'',1,1,'L');
			//
		}		
		$pdf->Ln(10);
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	$i++;	
}
$pdf->Ln(20);


// TOTALS
$pdf->Cell(400); $pdf->Cell(50,10,'Subtotal',1,0,'L'); $pdf->Cell(50,10,''.$Ecommerce->currency.money_format('%i',$row['subtotal']).'',1,1,'R');
if($row['discount'] > 0){
	$pdf->Cell(400); $pdf->Cell(50,10,'Discount',1,0,'L'); $pdf->Cell(50,10,'- '.$Ecommerce->currency.money_format('%i',$row['discount']).'',1,1,'R');
}
$pdf->Cell(400); $pdf->Cell(50,10,'S&H',1,0,'L'); $pdf->Cell(50,10,''.$Ecommerce->currency.money_format('%i',$row['sh']).'',1,1,'R');
$pdf->Cell(400); $pdf->Cell(50,10,'Tax',1,0,'L'); $pdf->Cell(50,10,''.$Ecommerce->currency.money_format('%i',$row['tax']).'',1,1,'R');
$pdf->Cell(400); $pdf->Cell(50,10,'Total',1,0,'L'); $pdf->Cell(50,10,''.$Ecommerce->currency.money_format('%i',$row['total']).'',1,1,'R');
$pdf->Ln(20);


// GET SHIPPING AND BILLING INFO
$select = "SELECT * FROM user_contact WHERE contact_id='".$row['shipping_id']."' LIMIT 1";
$result = doQuery($select);
$shipping = mysql_fetch_array($result);

$select = "SELECT * FROM user_contact WHERE contact_id='".$row['billing_id']."' LIMIT 1";
$result = doQuery($select);
$billing = mysql_fetch_array($result);

// PAYMENT METHOD
$pdf->Cell(300,10,'Payment Method: '.lookupDbValue('ecommerce_payment_methods','name',$row['payment_method_id'],'payment_method_id').'',0,1,'L');

if($row['payment_method_id'] == '1'){
	// IF CC THEN MASK AND SHOW CC
	$sel = "SELECT * FROM ecommerce_order_transactions WHERE order_id='".$row['order_id']."' ORDER BY transaction_id DESC LIMIT 1";
	$res = doQuery($sel);
	$transaction = mysql_fetch_array($res);
	$pdf->Cell(300,10,'Credit Card Type: '.lookupDbValue('ecommerce_cc_types','name',$transaction['cc_type'],'cc_type_id').'',0,1);
	$pdf->Cell(300,10,'Credit Card #: '.$Ecommerce->maskCreditCard($transaction['cc_number'],0,12).'',0,1);
}

$pdf->Ln(5);
// BILL TO 
$pdf->Cell(300,10,'Bill To:',0,1,'L');
$pdf->Cell(300,10,''.$billing['first_name'].' '.$billing['last_name'].'',0,1,'L');
$pdf->Cell(300,10,''.$billing['address1'].' '.$billing['address2'].' ',0,1,'L');
$pdf->Cell(300,10,''.$billing['city'].', '.lookupDbValue('state','state',$billing['state'],'state_id').' '.$billing['zip'].'',0,1,'L');
$pdf->Cell(300,10,''.lookupDbValue('country','country',$billing['country'],'country_id').'',0,1,'L');
$pdf->Ln(5);
$pdf->Cell(300,10,'Billing Phone: '.FormatPhone($billing['phone']).'',0,1,'L');
$pdf->Cell(300,10,'Billing Email: '.$billing['email'].'',0,1,'L');
$pdf->Ln(10);

// SHIPPING METHOD
$pdf->Cell(300,10,'Shipping Method: '.lookupDbValue('ecommerce_shipping_methods','name',$row['shipping_method_id'],'shipping_method_id').'',0,1,'L');
$pdf->Ln(5);
// SHIP TO
$pdf->Cell(300,10,'Ship To:',0,1,'L');
$pdf->Cell(300,10,''.$shipping['first_name'].' '.$shipping['last_name'].'',0,1,'L');
$pdf->Cell(300,10,''.$shipping['address1'].' '.$shipping['address2'].' ',0,1,'L');
$pdf->Cell(300,10,''.$shipping['city'].', '.lookupDbValue('state','state',$shipping['state'],'state_id').' '.$shipping['zip'].'',0,1,'L');
$pdf->Cell(300,10,''.lookupDbValue('country','country',$shipping['country'],'country_id').'',0,1,'L');
$pdf->Ln(10);

// SHIPPING METHOD
$pdf->Cell(300,10,'Special Instructions',0,1,'L');
$pdf->Ln(5);
$pdf->MultiCell(300, 10, ''.$row['note'].'', 0, 'L', false);
$pdf->Ln(10);

// SHIPPING METHOD
$pdf->Cell(300,10,'Gift Message',0,1,'L');
$pdf->Ln(5);
//$pdf->Cell(300,10,''.$row['gift_message'].'',0,1,'L');
$pdf->MultiCell(300, 10, ''.$row['gift_message'].'', 0, 'L', false);
$pdf->Ln(5);
$pdf->Ln(20);



$pdf->Output(); 


?>
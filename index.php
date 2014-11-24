<?php
	require("mysql_class.php");
	try{
		$m=new MySQLDB("infosystem");
		/*-----------------------------------------
		Insertion using basic Query method
		------------------------------------------*/
		$Qry="
			INSERT INTO personalinformation (firstname,lastname,address1,address2,city,state,country,zip,phone) 
			VALUES('abhay','Patankar','10 No Stop','-',1,1,1,222333,'555-2266-8896')
		";
		$m->query($Qry);

		/*-----------------------------------------
		Insertion using insertRow method
		------------------------------------------*/	$qryArray=array("firstname"=>"Bramha","lastname"=>"Bhatt","address1"=>"Kolar","address2"=>'Barkhedi',"city"=>1,"state"=>1,"country"=>1,"zip"=>222333,"phone"=>"555-2266-8896");
		$m->insertRow($qryArray,"personalinformation");

		/*-----------------------------------------
		Update using updateRow method
		------------------------------------------*/
		$A=array("address1"=>"Fatehganj","address2"=>"Vadodara");
		/*-----------------------------------------
		Method 1
		------------------------------------------*/		
		$m->updateRow($A,"personalinformation","id=3");
		/*-----------------------------------------
		Method 2
		------------------------------------------*/
		$m->updateRow($A,"personalinformation","id=3 AND firstname='Lokesh'");
		/*-----------------------------------------
		Method 3
		------------------------------------------*/
		$ID=3;
		$m->updateRow($A,"personalinformation","id=".$ID."");
		/*-----------------------------------------
		Method 4
		------------------------------------------*/
		$FirstName="'Lokesh'";
		$m->updateRow($A,"personalinformation","id=".$ID." AND firstname=".$FirstName."");

		/*-----------------------------------------
		Fetching Row using query method
		------------------------------------------*/
		$Qry="SELECT * FROM personalinformation WHERE id=3";
		$m->query($Qry);
		$Row=$m->fetchRow(2);
		echo $Row['firstname']."<br>";

		/*-----------------------------------------
		Fetching Row using query method & 
		displaying using fieldValue method
		------------------------------------------*/
		$Qry="SELECT * FROM personalinformation";
		$m->query($Qry);
		$i=1;
		echo $m->rowCount()."<br>";
		while($m->fetchRow(2)){
			//echo $i."<br>";
			//$i++;
		echo $m->fieldValue("firstname")." ".$m->fieldValue("address1")." ".$m->fieldValue("address2")."<br>";
		}

	}catch (Exception  $e) {      // Will be caught
		echo "Caught my exception\n", $e->getMessage();
	}
?>
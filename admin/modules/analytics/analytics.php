<?
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
* 	wes Version 1.0 Copyright 2010 Karl Steltenpohl Development LLC. All Rights Reserved.
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
//
// CALL CLASSES
//
$Analytics = new Analytics();
$Settings = new Settings();

//
// UPDATE SETTINGS
//
if($_POST['UPDATE_SETTINGS'] != ""){

	//
	// LOOP THROUGH SETTINGS
	// 
	$sel = "SELECT * FROM settings WHERE active='1' AND group_id='7'";
	$res = doQuery($sel);
	$num = mysql_num_rows($res);
	$i = 0;
	while($i<$num)
	{
		$row = mysql_fetch_array($res);
		
		//
		// UPDATE SETTING
		//
		$Settings->updateSetting($row);
		
		$i++;
	}
	
	//var_dump($_POST);
	//exit();
	
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST['VIEW']."&SUB=".$_REQUEST['SUB']."&REPORT=Settings Updated&SUCCESS=1");
	exit();
	

}

//
// REPORT
//
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);

if($_REQUEST['SUB'] == 'ANALYTICS' OR $_REQUEST['SUB'] == ''){

	echo tableHeader("Analytics",2,'100%');
		?>

			<tr><td colspan="2" style="margin:0px; padding:0px;">
			
			<script type="text/javascript">
			$(function() {
				$("#tabs").tabs();
			});
			</script>
			
			<div class="demo">
				<div id="tabs">
					<ul style="border-bottom:1px solid #f1f1f1;">
						
							<li><a href="#tabs-1">Accounts</a></li>
							<li><a href="#tabs-2">Pages</a></li>
							<li><a href="#tabs-3">Keywords</a></li>
							<li><a href="#tabs-4">Motion Chart</a></li>
							<!-- <li><a href="#tabs-5">Testing</a></li> -->
							<li style="float:right;"> 
								<form action="#">
								  <span id="aprofile" style="visibility:hidden; font-size:2px;"></span>
								  <span id="aname" style="font-size:10px; font-weight:bold;">No Account Selected</span>&nbsp;::&nbsp;
								  <span id="status" style="font-size:10px; ">Loading API Libraries...</span>
								  <input id="loginButton" type="button" value="Login"/>
								  <!-- <input type="button" value="Get Data" onclick="getAccountFeed();"/> -->
								</form>
							</li>
							
					</ul>
				
						
					<div id="tabs-1">						
									
						<div id="allAccountsDiv"></div>
									
					</div>		

					<div id="tabs-2">		
						<?
						//
						// PAGES
						//
						?>
						<div style="background-color:#fff; height:70px;">			
						<form name='pagedateform' action='#'>
							<p style="float:right; margin-right:25px;">
							  Start Date:
							  <?
							  //
							  // SUBTRACT TIME
							  //
							  $startdate = strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " -3 month");
							  ?>
								<script type="text/javascript">
								$(function() {
									$("#startDatePages").datepicker();
									$("#endDatePages").datepicker();
								});
								</script>
							  <input type='text' id='startDatePages' value='<?=date("m/d/Y",$startdate)?>'/>
							  &nbsp; &nbsp;
							  End Date:
							  <input type='text' id='endDatePages' value='<?=date("m/d/Y")?>'/>
							  <input type='button' value='Search' onclick='getDataFeed(document.getElementById("aprofile").innerHTML);'/>
							</p>
						</form>
						</div>
						<div id="cstatus1"><center style="background:#fff; padding:50px 0; display:block; font-size:14px; color:red;">No Account Selected.</center></div>
						<div id='sourceColumnChartDiv'></div>
						<div id='sourceChartDiv'></div>
						<!-- For the API to work in IE, an image must be requested from the same
								 domain the script is hosted on. The image doesn't actually have
								 to exist, just be requested. More info here :
								 http://code.google.com/apis/gdata/client-js.html
						-->
						<div id='sourceTableDiv'></div>
						<img style='visibility:hidden' src='__utm.gif' alt='required for GData'/>	
					</div>	

					<div id="tabs-3">						
						<?
						//
						// KEYWORDS
						//
						?>
						<div style="background-color:#fff; height:50px;">			
						<form name='keyworddateform' action='#'>
							<p style="float:right; margin-right:25px;">
							  Start Date:
							  <?
							  //
							  // SUBTRACT TIME
							  //
							  $startdate = strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " -3 month");
							  ?>
								<script type="text/javascript">
								$(function() {
									$("#startDateKeywords").datepicker();
									$("#endDateKeywords").datepicker();
								});
								</script>
							  <input type='text' id='startDateKeywords' value='<?=date("m/d/Y",$startdate)?>'/>
							  &nbsp; &nbsp;
							  End Date:
							  <input type='text' id='endDateKeywords' value='<?=date("m/d/Y")?>'/>
							  <input type='button' value='Search' onclick='getDataFeedK(document.getElementById("aprofile").innerHTML);'/>
							</p>
						</form>
						</div>
						<div id="cstatus2"><center style="background:#fff; padding:50px 0; display:block; font-size:14px; color:red;">No Account Selected.</center></div>
						<?
						//
						// KEYWORDS HTML
						//
						?>
						<div id='vizDiv'>
						  <div id='visitsChartDiv'></div>
						  <form name='matchForm' action='#'>
							<p style="float:right; margin-right:25px;">
							  Match Keywords To This Pattern:
							  <input type='text' id='filterInput' value=''/>
							  <input type='button' value='Match' onclick='drawViz();'/>
							</p>
						  </form>
						  <div id='pieChartDiv'></div>
							<div id='leftDiv'>
							  
								<p style="text-align:center;">Keywords That Matched
								  <br/>had <span id='matchedVisitsSpan'></span> visits
								</p>
								<div id='matchedTableDiv'></div>
							 
							</div>
							<div id='rightDiv'>
							  
							  <p style="text-align:center;">Keywords That Didn't Match
								<br/>had <span id='notMatchedVisitsSpan'></span> visits
							  </p>
							  <div id='notMatchedTableDiv'></div>
							
						  </div>
						</div>
						<!-- For the API to work in IE, an image must be requested from the same
								 domain the script is hosted on. The image doesn't actually have
								 to exist, just be requested. More info here :
								 http://code.google.com/apis/gdata/client-js.html
						-->
						<img style='visibility:hidden' src='__utm.gif' alt='required for GData'/>

									
					</div>	

					<div id="tabs-4">						
							

						<?
						//
						// MOTION CHART
						//
						?>
						<div style="background-color:#fff; height:70px;">			
						<form name='keyworddateform' action='#'>
							<p style="float:right; margin-right:25px;">
							  Year:
							  <?
							  //
							  // SUBTRACT TIME
							  //
							  $startdate = strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " -3 month");
							  ?>
		
							<select id="yearChart" style="float:none;">							
								<option value="<?=date("Y");?>"><?=date("Y");?></option>
								<option value="<?=date("Y")-1;?>"><?=date("Y")-1;?></option>
								<option value="<?=date("Y")-2;?>"><?=date("Y")-2;?></option>
								<option value="<?=date("Y")-3;?>"><?=date("Y")-3;?></option>
								<option value="<?=date("Y")-4;?>"><?=date("Y")-4;?></option>							
								<option value="<?=date("Y")-5;?>"><?=date("Y")-5;?></option>
							</select>
							
		
							  <input type='button' value='Search' onclick='getDataFeedM(document.getElementById("aprofile").innerHTML);'/>
							</p>
						</form>
						</div>
						<div id="cstatus3"><center style="background:#fff; padding:50px 0; display:block; font-size:14px; color:red;">No Account Selected.</center></div>
						<?
						//
						// MOTION CHART HTML
						//
						?>
						<div id='motionChartDiv'></div>
						<!-- For the API to work in IE, an image must be requested from the same
								 domain the script is hosted on. The image doesn't actually have
								 to exist, just be requested. More info here :
								 http://code.google.com/apis/gdata/client-js.html
						-->
						<img style='visibility:hidden' src='__utm.gif' alt='required for GData'/>

									
					</div>					

					
					<!--
					<div id="tabs-5">						
									
						<?
						//
						// TESTING
						//
						?>
						
						<h2>GA Export API Account Feed Demo</h2>
						<br/>
						
						<div id="topLevelDiv"></div>
						<div id="segmentDiv"></div>
						<div id="customVarDiv"></div>
						<div id="goalsDiv"></div>
						
						<img src="__utm.gif" style="visibility:hidden" alt="required for JS GData Library"/>
						
									
					</div>	
					-->
				</div>
			</div>
			
			
			
			
		</td></tr>
	</table>	
		
		<div id="submit">
		
		
		
		</div>
<?
} elseif($_REQUEST['SUB'] == 'SETTINGS') {

//
// CONTACT SETTINGS
//

		$button = "Update Settings";
		$doing = "Analytics Settings";

	?>
	<FORM method="post" enctype="multipart/form-data" ACTION="<?=$_SERVER["PHP_SELF"]?>?VIEW=<?=$_GET["VIEW"]?>&SUB=<?=$_GET['SUB']?>" name="settingsform" id="settingsform">
	
		<?
		echo tableHeader("$doing ".$_POST['name']."",2,'100%');
		?>
		
			<?
			$sela = "SELECT * FROM settings WHERE active=1 AND group_id='7' ORDER BY type ASC";

			$resa = doQuery($sela);
			$numa = mysql_num_rows($resa);
			$ja = 0;
			
			//echo $sela;
			
			while($ja<$numa){
				$rowa = mysql_fetch_array($resa);
				
				$Settings->displaySettingField($rowa,0,$ja);
				
				$ja++;
			}

			?>		
						
		</table>
		<?		
		//
		// Submit FORM
		//
		?>
		<div id="submit">
			<a href="?VIEW=<?=$_GET['VIEW']?>">Back</a> &nbsp;&nbsp;&nbsp;
			<?		
			echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
			?>		
		</div>
	</form>

<?
}
?>
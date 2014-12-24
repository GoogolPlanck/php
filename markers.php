<?php
session_start();
include ("access_db.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html" />
<script type="text/javascript" src="style/resolution.js"></script>
<link rel="stylesheet" href="function/menu_support_files/menu_main_style.css" type="text/css" />
<script src="jqGrid-4/js/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="jqGrid-4/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="jqGrid-4/js/jquery.jqGrid.src.js" type="text/javascript"></script>
<?php
$query = "SELECT permission FROM user WHERE id = '1'";
$rs = mysql_query($query);
while(list($permission) = mysql_fetch_row($rs)) {
  if ($permission == 0) {	
    $permission1 = $permission;
    $_SESSION['perm'] = 0;
  }
  else{
    $_SESSION['perm'] = 1;
  }
}
$perm = $_SESSION['perm'];
if ($perm == 1 && $_SESSION['flag']== NULL){
  header("Location:error1.html");
}
else{?>
<script>
jQuery(document).ready(function() {
  $.ajax({
    type: 'GET',
    cache: false,
    contentType: 'application/json; charset=utf-8',
    url: 'load_matrix_session_markers.php',
    success: function() {}
  }); 
  $.ajax({
    type: 'GET',
    cache: false,
    contentType: 'application/json; charset=utf-8',
    url: 'load_matrix_session_ephys.php',
    success: function() {}
  }); 
  $.ajax({
    type: 'GET',
    cache: false,
    contentType: 'application/json; charset=utf-8',
    url: 'load_matrix_session_morphology.php',
    success: function() {}
  });
  $.ajax({
    type: 'GET',
    cache: false,
    contentType: 'application/json; charset=utf-8',
    url: 'load_matrix_session_connectivity.php',
    success: function() {}
  });
  $('div#menu_main_button_new_clr').css('display','block');
});
</script>
<?php 
}
?>

<?php
//include ("access_db.php");
$jsonStr = $_SESSION['markers'];
if($_SESSION['check']=="no_reload")
	$_SESSION['check']='reload';
//include ("getMarkers.php");
require_once('class/class.type.php');
require_once('class/class.property.php');
require_once('class/class.evidencepropertyyperel.php');
require_once('class/class.temporary_result_neurons.php');
	
$width1='25%';
$width2='2%';

$research = "";
if(isset($_REQUEST['research']))
	$research = $_REQUEST['research'];

$table_result ="";
if(isset($_REQUEST['table_result']))
	$table_result = $_REQUEST['table_result'];

?>

<?php include ("function/icon.html"); ?>
<title>Molecular Markers Matrix</title>
<script type="text/javascript" src="style/resolution.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="jqGrid-4/css/ui-lightness/jquery-ui-1.10.3.custom.css" />
<link rel="stylesheet" type="text/css" media="screen" href="jqGrid-4/css/ui.jqgrid.css" />
<style>

#nGrid_PV,#nGrid_vGluT3,#nGrid_VIP,#nGrid_RLN
{
	border-right:medium solid #ABCCE4;
	width:auto !important;
}
.ui-jqgrid tr.jqgrow td
{
	height:18px !important;
}
.highlighted{
	border-right: solid 1px Chartreuse !important;
	border-left: solid 1px Chartreuse !important;
	border-bottom:solid 1px Chartreuse !important; 
}
.highlighted_top{
	border: solid 1px Chartreuse !important;
}
.rotate 
{
    -webkit-transform: rotate(-90deg);    /* Safari 3.1+, Chrome */
    -moz-transform: rotate(-90deg);    /* Firefox 3.5+ */
    -o-transform: rotate(-90deg); /* Opera starting with 10.50 */
    /* Internet Explorer: */
    /*filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3); /* IE6, IE7 */
   /*-ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=3)" /* IE8 */
   -ms-transform: rotate(-90deg);
   /*left:8px;*/
   top:25px;
   left:3px;
   font-size:12px;
   font-weight:bold;
   padding:0px;
   font:Verdana;
}
.rotateIE9 
{
    -webkit-transform: rotate(-90deg);    /* Safari 3.1+, Chrome */
    -moz-transform: rotate(-90deg);    /* Firefox 3.5+ */
    -o-transform: rotate(-90deg); /* Opera starting with 10.50 */
    /* Internet Explorer: */
   /* filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3); /* IE6, IE7 */
   /*-ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=3)" /* IE8 */
   -ms-transform: rotate(-90deg);
   top:25px;
   left:3px; 
   font-size:12px;
   font-weight:bold;
   padding:0px;
   font:Verdana;
}
</style>
<script language="javascript">
function OpenInNewTab(aEle)
{
	var win = window.open(aEle.href,'_self');
	win.focus();
}
function ctr(select_nick_name2, color, select_nick_name_check)
{

	if (document.getElementById(select_nick_name_check).checked == false)
	{	
		document.getElementById(select_nick_name2).bgColor = "#FFFFFF";
		
	}
	else if (document.getElementById(select_nick_name_check).checked == true)
		document.getElementById(select_nick_name2).bgColor = "#EBF283";	
}

function getIEVersion() {
    var rv = -1; // Return value assumes failure.
    if (navigator.appName == 'Microsoft Internet Explorer') {
        var ua = navigator.userAgent;
        var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
        if (re.test(ua) != null)
            rv = parseFloat( RegExp.$1 );
    }
    return rv;
}


function checkVersion() {
    var ver = getIEVersion();
	//alert("Version : "+ver);
    /*if ( ver != -1 ) {
        if (ver <= 9.0) {
            // do something
        }
    }*/
    return ver;
}
checkVersion();

</script>
<?php if($_SESSION['flag']!='1'){?>
<script>
 
	window.onload = function() 
	{ 
	if (!window.location.search) 
	{ 
	setTimeout("window.location+='?refreshed';", .1000); 
	} 
	} 
 
</script>
<?php }?>
<script type="text/javascript">
$(function(){
	var dataStr = <?php echo $jsonStr?>;
	function Merger(gridName,cellName){
		var mya = $("#" + gridName + "").getDataIDs();	
		var rowCount = mya.length;
		//alert(mya.length);
		var rowSpanCount = 1;
		var countRows = 0;
		var lastRowDelete =0;
		var firstElement = 0;

		for(var i=0;i<=rowCount;i=i+countRows)
		{ 
			var before = $("#" + gridName + "").jqGrid('getRowData', mya[i]); // Fetch me the data for the first row
			for (j = i+1; j <=rowCount; j++) 
			{
				var end = $("#" + gridName + "").jqGrid('getRowData', mya[j]); // Fetch me the data for the next row
				if (before[cellName] == end[cellName]) // If the previous row and the next row data are the same
				{
					$("#" + gridName + "").setCell(mya[j], cellName,'&nbsp;');
					$("tr#"+j+" td#type"+j).css("border-bottom","none");
					if(rowSpanCount > 1) // For the first row Don't delete the cell and its contents
					{ 
						$("tr#"+j+" td#type"+j).css("border-bottom","none");
					}
					else
					{
						firstElement = j;
					}
					rowSpanCount++;	
                } 
                else 
                {
					$("tr#"+j).css("border-bottom", "2px red");
					countRows = rowSpanCount;
                	rowSpanCount = 1;
                	break;
                }
			}
		} 
	}
	var research = "<?php echo $research?>";
	var table = "<?php if(isset($_REQUEST['table_result'])){echo $_REQUEST['table_result'];}?>";
	
	$("#nGrid").jqGrid({
	datatype: "jsonstring",
	datastr: dataStr,
    /* url:'getMarkers.php',
    datatype: 'json', */
    mtype: 'GET',
    /* ajaxGridOptions :{
		contentType : "application/json"
        }, */
    postData: {
        researchVar: research,
        table_result : table
    },

      
    //  colNames:['','Neuron Type','<a href="neuron_by_marker.php?marker=5HT-3" onClick="OpenInNewTab(this);">5HT-3</a>','<a href="neuron_by_marker.php?marker=alpha-actinin-2" onClick="OpenInNewTab(this);">&prop;-act2</a>','<a href="neuron_by_marker.php?marker=AChE" onClick="OpenInNewTab(this);">AChE</a>','<a href="neuron_by_marker.php?marker=CB" onClick="OpenInNewTab(this);">CB</a>','<a href="neuron_by_marker.php?marker=CB1" onClick="OpenInNewTab(this);">CB1</a>','<a href="neuron_by_marker.php?marker=CCK" onClick="OpenInNewTab(this);">CCK</a>','<a href="neuron_by_marker.php?marker=CGRP" onClick="OpenInNewTab(this);">CGRP</a>','<a href="neuron_by_marker.php?marker=ChAT" onClick="OpenInNewTab(this);">ChAT</a>','<a href="neuron_by_marker.php?marker=CoupTF II" onClick="OpenInNewTab(this);">CoupTF II</a>','<a href="neuron_by_marker.php?marker=CR" onClick="OpenInNewTab(this);">CR</a>','<a href="neuron_by_marker.php?marker=DYN" onClick="OpenInNewTab(this);">DYN</a>','<a href="neuron_by_marker.php?marker=DYN" onClick="OpenInNewTab(this);">EAAT3</a>','<a href="neuron_by_marker.php?marker=ENK" onClick="OpenInNewTab(this);">ENK</a>','<a href="neuron_by_marker.php?marker=Gaba-a-alpha" onClick="OpenInNewTab(this);">GABAa &prop;1</a>','<a href="neuron_by_marker.php?marker=GAT-1" onClick="OpenInNewTab(this);">GAT-1</a>','<a href="neuron_by_marker.php?marker=Gly T2" onClick="OpenInNewTab(this);">Gly T2</a>','<a href="neuron_by_marker.php?marker=mGLuR1a" onClick="OpenInNewTab(this);">mGLuR1a</a>','<a href="neuron_by_marker.php?marker=mGluR2/3" onClick="OpenInNewTab(this);">mGluR2/3</a>','<a href="neuron_by_marker.php?marker=mGLuR7a" onClick="OpenInNewTab(this);">mGLuR7a</a>','<a href="neuron_by_marker.php?marker=mGluR8a" onClick="OpenInNewTab(this);">mGluR8a</a>','<a href="neuron_by_marker.php?marker=MOR" onClick="OpenInNewTab(this);">MOR</a>','<a href="neuron_by_marker.php?marker=Mus2R" onClick="OpenInNewTab(this);">Mus2R</a>','<a href="neuron_by_marker.php?marker=NG" onClick="OpenInNewTab(this);">NG</a>','<a href="neuron_by_marker.php?marker=NKB" onClick="OpenInNewTab(this);">NKB</a>','<a href="neuron_by_marker.php?marker=nNos" onClick="OpenInNewTab(this);">nNos</a>','<a href="neuron_by_marker.php?marker=NPY" onClick="OpenInNewTab(this);">NPY</a>','<a href="neuron_by_marker.php?marker=PPTA" onClick="OpenInNewTab(this);">PPTA</a>','<a href="neuron_by_marker.php?marker=PPTB" onClick="OpenInNewTab(this);">PPTB</a>','<a href="neuron_by_marker.php?marker=PV" onClick="OpenInNewTab(this);">PV</a>','<a href="neuron_by_marker.php?marker=RLN" onClick="OpenInNewTab(this);">RLN</a>','<a href="neuron_by_marker.php?marker=SOM" onClick="OpenInNewTab(this);">SOM</a>','<a href="neuron_by_marker.php?marker=Sub P Rec" onClick="OpenInNewTab(this);">Sub P Rec</a>','<a href="neuron_by_marker.php?marker=vAChT" onClick="OpenInNewTab(this);">vAChT</a>','<a href="neuron_by_marker.php?marker=vGluT2" onClick="OpenInNewTab(this);">vGluT2</a>','<a href="neuron_by_marker.php?marker=vGluT3" onClick="OpenInNewTab(this);">vGlu T3<a/>','<a href="neuron_by_marker.php?marker=VIAAT" onClick="OpenInNewTab(this);">VIAAT</a>','<a href="neuron_by_marker.php?marker=VIP" onClick="OpenInNewTab(this);">VIP</a>'],	
    //   colNames:['','Neuron Type','<a href="neuron_by_marker.php?marker=CB" onClick="OpenInNewTab(this);">CB</a>','<a href="neuron_by_marker.php?marker=CR" onClick="OpenInNewTab(this);">CR</a>','<a href="neuron_by_marker.php?marker=PV" onClick="OpenInNewTab(this);">PV</a>','<a href="neuron_by_marker.php?marker=5HT-3" onClick="OpenInNewTab(this);">5HT-3</a>','<a href="neuron_by_marker.php?marker=CB1" onClick="OpenInNewTab(this);">CB1</a>','<a href="neuron_by_marker.php?marker=Gaba-a-alpha" onClick="OpenInNewTab(this);">GABAa &prop;1</a>','<a href="neuron_by_marker.php?marker=mGLuR1a" onClick="OpenInNewTab(this);">mGLuR1a</a>','<a href="neuron_by_marker.php?marker=Mus2R" onClick="OpenInNewTab(this);">Mus2R</a>','<a href="neuron_by_marker.php?marker=Sub P Rec" onClick="OpenInNewTab(this);">Sub P Rec</a>','<a href="neuron_by_marker.php?marker=vGluT3" onClick="OpenInNewTab(this);">vGluT3<a/>','<a href="neuron_by_marker.php?marker=CCK" onClick="OpenInNewTab(this);">CCK</a>','<a href="neuron_by_marker.php?marker=ENK" onClick="OpenInNewTab(this);">ENK</a>','<a href="neuron_by_marker.php?marker=NG" onClick="OpenInNewTab(this);">NG</a>','<a href="neuron_by_marker.php?marker=NPY" onClick="OpenInNewTab(this);">NPY</a>','<a href="neuron_by_marker.php?marker=SOM" onClick="OpenInNewTab(this);">SOM</a>','<a href="neuron_by_marker.php?marker=VIP" onClick="OpenInNewTab(this);">VIP</a>','<a href="neuron_by_marker.php?marker=alpha-actinin-2" onClick="OpenInNewTab(this);">&prop;-act2</a>','<a href="neuron_by_marker.php?marker=CoupTF II" onClick="OpenInNewTab(this);">CoupTF II</a>','<a href="neuron_by_marker.php?marker=nNos" onClick="OpenInNewTab(this);">nNos</a>','<a href="neuron_by_marker.php?marker=RLN" onClick="OpenInNewTab(this);">RLN</a>','<a href="neuron_by_marker.php?marker=AChE" onClick="OpenInNewTab(this);">AChE</a>','<a href="neuron_by_marker.php?marker=CGRP" onClick="OpenInNewTab(this);">CGRP</a>','<a href="neuron_by_marker.php?marker=ChAT" onClick="OpenInNewTab(this);">ChAT</a>','<a href="neuron_by_marker.php?marker=DYN" onClick="OpenInNewTab(this);">DYN</a>','<a href="neuron_by_marker.php?marker=DYN" onClick="OpenInNewTab(this);">EAAT3</a>','<a href="neuron_by_marker.php?marker=GAT-1" onClick="OpenInNewTab(this);">GAT-1</a>','<a href="neuron_by_marker.php?marker=Gly T2" onClick="OpenInNewTab(this);">Gly T2</a>','<a href="neuron_by_marker.php?marker=mGluR2/3" onClick="OpenInNewTab(this);">mGluR2/3</a>','<a href="neuron_by_marker.php?marker=mGLuR7a" onClick="OpenInNewTab(this);">mGLuR7a</a>','<a href="neuron_by_marker.php?marker=mGluR8a" onClick="OpenInNewTab(this);">mGluR8a</a>','<a href="neuron_by_marker.php?marker=MOR" onClick="OpenInNewTab(this);">MOR</a>','<a href="neuron_by_marker.php?marker=NKB" onClick="OpenInNewTab(this);">NKB</a>','<a href="neuron_by_marker.php?marker=PPTA" onClick="OpenInNewTab(this);">PPTA</a>','<a href="neuron_by_marker.php?marker=PPTB" onClick="OpenInNewTab(this);">PPTB</a>','<a href="neuron_by_marker.php?marker=vAChT" onClick="OpenInNewTab(this);">vAChT</a>','<a href="neuron_by_marker.php?marker=vGluT2" onClick="OpenInNewTab(this);">vGluT2</a>','<a href="neuron_by_marker.php?marker=VIAAT" onClick="OpenInNewTab(this);">VIAAT</a>'],
	// colNames:['','Neuron Type','<a href="neuron_by_marker.php?marker=CB" onClick="OpenInNewTab(this);">CB</a>','<a href="neuron_by_marker.php?marker=CR" onClick="OpenInNewTab(this);">CR</a>','<a href="neuron_by_marker.php?marker=PV" onClick="OpenInNewTab(this);">PV</a>','<a href="neuron_by_marker.php?marker=5HT-3" onClick="OpenInNewTab(this);">5HT-3</a>','<a href="neuron_by_marker.php?marker=CB1" onClick="OpenInNewTab(this);">CB1</a>','<a href="neuron_by_marker.php?marker=Gaba-a-alpha" onClick="OpenInNewTab(this);">GABAa &prop;1</a>','<a href="neuron_by_marker.php?marker=mGLuR1a" onClick="OpenInNewTab(this);">mGLuR1a</a>','<a href="neuron_by_marker.php?marker=Mus2R" onClick="OpenInNewTab(this);">Mus2R</a>','<a href="neuron_by_marker.php?marker=Sub P Rec" onClick="OpenInNewTab(this);">Sub P Rec</a>','<a href="neuron_by_marker.php?marker=vGluT3" onClick="OpenInNewTab(this);">vGluT3<a/>','<a href="neuron_by_marker.php?marker=CCK" onClick="OpenInNewTab(this);">CCK</a>','<a href="neuron_by_marker.php?marker=ENK" onClick="OpenInNewTab(this);">ENK</a>','<a href="neuron_by_marker.php?marker=NG" onClick="OpenInNewTab(this);">NG</a>','<a href="neuron_by_marker.php?marker=NPY" onClick="OpenInNewTab(this);">NPY</a>','<a href="neuron_by_marker.php?marker=SOM" onClick="OpenInNewTab(this);">SOM</a>','<a href="neuron_by_marker.php?marker=VIP" onClick="OpenInNewTab(this);">VIP</a>','<a href="neuron_by_marker.php?marker=alpha-actinin-2" onClick="OpenInNewTab(this);">&prop;-act2</a>','<a href="neuron_by_marker.php?marker=CoupTF II" onClick="OpenInNewTab(this);">CoupTF II</a>','<a href="neuron_by_marker.php?marker=nNos" onClick="OpenInNewTab(this);">nNos</a>','<a href="neuron_by_marker.php?marker=RLN" onClick="OpenInNewTab(this);">RLN</a>','<a href="neuron_by_marker.php?marker=AChE" onClick="OpenInNewTab(this);">AChE</a>','<a href="neuron_by_marker.php?marker=AMIGO2" onClick="OpenInNewTab(this);">AMIGO2</a>','<a href="neuron_by_marker.php?marker=AMPAR 2/3" onClick="OpenInNewTab(this);">AMPAR2/3</a>','<a href="neuron_by_marker.php?marker=BDNF" onClick="OpenInNewTab(this);">BDNF</a>','<a href="neuron_by_marker.php?marker=Bok" onClick="OpenInNewTab(this);">Bok</a>','<a href="neuron_by_marker.php?marker=Caln" onClick="OpenInNewTab(this);">Caln</a>','<a href="neuron_by_marker.php?marker=CaM" onClick="OpenInNewTab(this);">CaM</a>','<a href="neuron_by_marker.php?marker=CGRP" onClick="OpenInNewTab(this);">CGRP</a>','<a href="neuron_by_marker.php?marker=Cx36" onClick="OpenInNewTab(this);">Cx36</a>','<a href="neuron_by_marker.php?marker=ChAT" onClick="OpenInNewTab(this);">ChAT</a>','<a href="neuron_by_marker.php?marker=Chma2" onClick="OpenInNewTab(this);">Chma2</a>','<a href="neuron_by_marker.php?marker=CRF" onClick="OpenInNewTab(this);">CRF</a>','<a href="neuron_by_marker.php?marker=Ctip2" onClick="OpenInNewTab(this);">Ctip2</a>','<a href="neuron_by_marker.php?marker=Disc1" onClick="OpenInNewTab(this);">Disc1</a>','<a href="neuron_by_marker.php?marker=DYN" onClick="OpenInNewTab(this);">DYN</a>','<a href="neuron_by_marker.php?marker=EAAT3" onClick="OpenInNewTab(this);">EAAT3</a>','<a href="neuron_by_marker.php?marker=ErbB4" onClick="OpenInNewTab(this);">ErbB4</a>','<a href="neuron_by_marker.php?marker=GABAa\alpha 2" onClick="OpenInNewTab(this);">GABAa&prop;2</a>','<a href="neuron_by_marker.php?marker=GABAa\alpha 3" onClick="OpenInNewTab(this);">GABAa&prop; 3</a>','<a href="neuron_by_marker.php?marker=GABAa&prop; 4" onClick="OpenInNewTab(this);">GABAa&prop; 4</a>','<a href="neuron_by_marker.php?marker=GABAa\alpha 5" onClick="OpenInNewTab(this);">GABAa&prop; 5</a>','<a href="neuron_by_marker.php?marker=GABAa\alpha 6" onClick="OpenInNewTab(this);">GABAa&prop; 6</a>','<a href="neuron_by_marker.php?marker=GABAa\beta 1" onClick="OpenInNewTab(this);">GABAa&beta; 1</a>','<a href="neuron_by_marker.php?marker=GABAa\beta 2" onClick="OpenInNewTab(this);">GABAa&beta; 2</a>','<a href="neuron_by_marker.php?marker=GABAa\beta 3" onClick="OpenInNewTab(this);">GABAa&beta; 3</a>','<a href="neuron_by_marker.php?marker=GABAa\delta" onClick="OpenInNewTab(this);">GABAa&Delta;</a>','<a href="neuron_by_marker.php?marker=GABAa\gamma 1" onClick="OpenInNewTab(this);">GABAa&gamma; 1</a>','<a href="neuron_by_marker.php?marker=GABAa\gamma 2" onClick="OpenInNewTab(this);">GABAa&gamma; 2</a>','<a href="neuron_by_marker.php?marker=GABA-B1" onClick="OpenInNewTab(this);">GABA-B1</a>','<a href="neuron_by_marker.php?marker=GAT-1" onClick="OpenInNewTab(this);">GAT-1</a>','<a href="neuron_by_marker.php?marker=GAT-3" onClick="OpenInNewTab(this);">GAT-3</a>','<a href="neuron_by_marker.php?marker=GluR2/3" onClick="OpenInNewTab(this);">GluR2/3</a>','<a href="neuron_by_marker.php?marker=GluA2" onClick="OpenInNewTab(this);">GluA2</a>','<a href="neuron_by_marker.php?marker=GluA1" onClick="OpenInNewTab(this);">GluA1</a>','<a href="neuron_by_marker.php?marker=GluA3" onClick="OpenInNewTab(this);">GluA3</a>','<a href="neuron_by_marker.php?marker=GluA4" onClick="OpenInNewTab(this);">GluA4</a>','<a href="neuron_by_marker.php?marker=Gly T2" onClick="OpenInNewTab(this);">Gly T2</a>','<a href="neuron_by_marker.php?marker=Id-2" onClick="OpenInNewTab(this);">Id-2</a>','<a href="neuron_by_marker.php?marker=Kv3.1" onClick="OpenInNewTab(this);">Kv3.1</a>','<a href="neuron_by_marker.php?marker=Man1a" onClick="OpenInNewTab(this);">Man1a</a>','<a href="neuron_by_marker.php?marker=Math-2" onClick="OpenInNewTab(this);">Math-2</a>','<a href="neuron_by_marker.php?marker=mGluR1" onClick="OpenInNewTab(this);">mGluR1</a>','<a href="neuron_by_marker.php?marker=mGluR2/3" onClick="OpenInNewTab(this);">mGluR2/3</a>','<a href="neuron_by_marker.php?marker=mGluR4" onClick="OpenInNewTab(this);">mGluR4</a>','<a href="neuron_by_marker.php?marker=mGluR5" onClick="OpenInNewTab(this);">mGluR5</a>','<a href="neuron_by_marker.php?marker=mGluR5a" onClick="OpenInNewTab(this);">mGluR5a</a>','<a href="neuron_by_marker.php?marker=mGLuR7a" onClick="OpenInNewTab(this);">mGluR7a</a>','<a href="neuron_by_marker.php?marker=mGluR8a" onClick="OpenInNewTab(this);">mGluR8a</a>','<a href="neuron_by_marker.php?marker=Mus1R" onClick="OpenInNewTab(this);">Mus1R</a>','<a href="neuron_by_marker.php?marker=Mus3R" onClick="OpenInNewTab(this);">Mus3R</a>','<a href="neuron_by_marker.php?marker=Mus4R" onClick="OpenInNewTab(this);">Mus4R</a>','<a href="neuron_by_marker.php?marker=MOR" onClick="OpenInNewTab(this);">MOR</a>','<a href="neuron_by_marker.php?marker=NECAB1" onClick="OpenInNewTab(this);">NECAB1</a>','<a href="neuron_by_marker.php?marker=Neuropilin2" onClick="OpenInNewTab(this);">Neuropil2</a>','<a href="neuron_by_marker.php?marker=NKB" onClick="OpenInNewTab(this);">NKB</a>','<a href="neuron_by_marker.php?marker=PCP4" onClick="OpenInNewTab(this);">PCP4</a>','<a href="neuron_by_marker.php?marker=p-CREB" onClick="OpenInNewTab(this);">p-CREB</a>','<a href="neuron_by_marker.php?marker=PPTA" onClick="OpenInNewTab(this);">PPTA</a>','<a href="neuron_by_marker.php?marker=PPTB" onClick="OpenInNewTab(this);">PPTB</a>','<a href="neuron_by_marker.php?marker=Prox1" onClick="OpenInNewTab(this);">Prox1</a>','<a href="neuron_by_marker.php?marker=PSA-NCAM" onClick="OpenInNewTab(this);">PSA-NCAM</a>','<a href="neuron_by_marker.php?marker=SATB1" onClick="OpenInNewTab(this);">SATB1</a>','<a href="neuron_by_marker.php?marker=SATB2" onClick="OpenInNewTab(this);">SATB2</a>','<a href="neuron_by_marker.php?marker=SCIP" onClick="OpenInNewTab(this);">SCIP</a>','<a href="neuron_by_marker.php?marker=SPO" onClick="OpenInNewTab(this);">SPO</a>','<a href="neuron_by_marker.php?marker=Sub P" onClick="OpenInNewTab(this);">Sub P</a>','<a href="neuron_by_marker.php?marker=vAChT" onClick="OpenInNewTab(this);">vAChT</a>','<a href="neuron_by_marker.php?marker=vGAT" onClick="OpenInNewTab(this);">vGAT</a>','<a href="neuron_by_marker.php?marker=vGluR2" onClick="OpenInNewTab(this);">vGluR2</a>','<a href="neuron_by_marker.php?marker=vGluR3" onClick="OpenInNewTab(this);">vGluR3</a>',/*'<a href="neuron_by_marker.php?marker=vGluR8a" onClick="OpenInNewTab(this);">vGluR8a</a>',*/'<a href="neuron_by_marker.php?marker=vGlut1" onClick="OpenInNewTab(this);">vGlut1</a>','<a href="neuron_by_marker.php?marker=vGluT2" onClick="OpenInNewTab(this);">vGluT2</a>','<a href="neuron_by_marker.php?marker=VIAAT" onClick="OpenInNewTab(this);">VIAAT</a>','<a href="neuron_by_marker.php?marker=VILIP" onClick="OpenInNewTab(this);">VILIP</a>','<a href="neuron_by_marker.php?marker=Y1" onClick="OpenInNewTab(this);">Y1</a>'],
	 colNames:['','Neuron Type','<a href="neuron_by_marker.php?marker=CB" onClick="OpenInNewTab(this);">CB</a>','<a href="neuron_by_marker.php?marker=CR" onClick="OpenInNewTab(this);">CR</a>','<a href="neuron_by_marker.php?marker=PV" onClick="OpenInNewTab(this);">PV</a>','<a href="neuron_by_marker.php?marker=5HT-3" onClick="OpenInNewTab(this);">5HT-3</a>','<a href="neuron_by_marker.php?marker=CB1" onClick="OpenInNewTab(this);">CB1</a>','<a href="neuron_by_marker.php?marker=Gaba-a-alpha" onClick="OpenInNewTab(this);">GABAa &prop;1</a>','<a href="neuron_by_marker.php?marker=mGLuR1a" onClick="OpenInNewTab(this);">mGLuR1a</a>','<a href="neuron_by_marker.php?marker=Mus2R" onClick="OpenInNewTab(this);">Mus2R</a>','<a href="neuron_by_marker.php?marker=Sub P Rec" onClick="OpenInNewTab(this);">Sub P Rec</a>','<a href="neuron_by_marker.php?marker=vGluT3" onClick="OpenInNewTab(this);">vGluT3<a/>','<a href="neuron_by_marker.php?marker=CCK" onClick="OpenInNewTab(this);">CCK</a>','<a href="neuron_by_marker.php?marker=ENK" onClick="OpenInNewTab(this);">ENK</a>','<a href="neuron_by_marker.php?marker=NG" onClick="OpenInNewTab(this);">NG</a>','<a href="neuron_by_marker.php?marker=NPY" onClick="OpenInNewTab(this);">NPY</a>','<a href="neuron_by_marker.php?marker=SOM" onClick="OpenInNewTab(this);">SOM</a>','<a href="neuron_by_marker.php?marker=VIP" onClick="OpenInNewTab(this);">VIP</a>','<a href="neuron_by_marker.php?marker=alpha-actinin-2" onClick="OpenInNewTab(this);">&prop;-act2</a>','<a href="neuron_by_marker.php?marker=CoupTF II" onClick="OpenInNewTab(this);">CoupTF II</a>','<a href="neuron_by_marker.php?marker=nNos" onClick="OpenInNewTab(this);">nNos</a>','<a href="neuron_by_marker.php?marker=RLN" onClick="OpenInNewTab(this);">RLN</a>'],      
              /* ,'SMi','SG','H','SLM','SR','SL','SP','SO','SLM','SR','SP','SO','SLM','SR','SP','SO','SM','SP','PL','I','II','III','IV','V','VI']*/
    colModel :[
	  {name:'type', index:'type', width:50,sortable:false,cellattr: function (rowId, tv, rawObject, cm, rdata) {
          return 'id=\'type' + rowId + "\'";   
      } },
      {name:'Neuron type', index:'nickname', width:175,sortable:false},
          //,searchoptions: {sopt: ['bw','bn','cn','in','ni','ew','en','nc']}},
 
	  {name:'CB', index:'CB', width:15,height:200,search:false,sortable:false},
      {name:'CR', index:'CR', width:15,height:200,search:false,sortable:false},
      {name:'PV', index:'PV', width:15,height:200,search:false,sortable:false,
    	  cellattr: function(rowId, tv, rawObject, cm, rdata) 
          {
             return 'style="border-right:medium solid #ABCCE4;"';
          }
          },
      
      {name:'5HT-3', index:'5HT-3', width:15,height:200,search:false,sortable:false},
      {name:'CB1', index:'CB1', width:15,height:200,search:false,sortable:false},
      {name:'GABAa', index:'GABAa', width:15,height:200,search:false,sortable:false},
      {name:'mGLuR1a', index:'mGLuR1a', width:15,height:200,search:false,sortable:false},
      {name:'Mus2R', index:'Mus2R', width:15,height:200,search:false,sortable:false},
      {name:'SubPRec', index:'SubPRec', width:15,height:200,search:false,sortable:false},
      {name:'vGluT3', index:'vGluT3', width:15,height:200,search:false,sortable:false,
    	  cellattr: function(rowId, tv, rawObject, cm, rdata) 
          {
             return 'style="border-right:medium solid #ABCCE4;"';
          }
          },

      {name:'CCK', index:'CCK', width:15,height:200,search:false,sortable:false},
      {name:'ENK', index:'ENK', width:15,height:200,search:false,sortable:false},
      {name:'NG', index:'NG', width:15,height:200,search:false,sortable:false},
      {name:'NPY', index:'NPY', width:15,height:200,search:false,sortable:false},
      {name:'SOM', index:'SOM', width:15,height:200,search:false,sortable:false},
      {name:'VIP', index:'VIP', width:15,height:200,search:false,sortable:false,
    	  cellattr: function(rowId, tv, rawObject, cm, rdata) 
          {
             return 'style="border-right:medium solid #ABCCE4;"';
          }
          },

      
      {name:'act2', index:'act2', width:15,height:200,search:false,sortable:false},
      {name:'CoupTFII', index:'CoupTFII', width:15,height:200,search:false,sortable:false},
      {name:'nNos', index:'nNos', width:15,height:200,search:false,sortable:false},
      {name:'RLN', index:'RLN', width:15,height:200,search:false,sortable:false,
    	  cellattr: function(rowId, tv, rawObject, cm, rdata) 
          {
             return 'style="border-right:medium solid #ABCCE4;"';
          }
          }

 
    ], 
    //multiselect: true,
   /* pager: '#pager',*/
    rowNum:122,
    rowList:[122],
   /*  sortname: 'invid',
    sortorder: 'desc',*/
    viewrecords: true, 
    gridview: true,
    jsonReader : {
      page: "page",
      total: "total",
      records: "records",
      root:"rows",
      repeatitems: true,
      onSelectRow: function() {
    	     return false;
    	},
      cell:"cell",
      id: "invid"
   },
    //caption: 'Morphology Matrix',
    scrollerbar:true,
    shrinkToFit:false,
    height:"440",
    width:"650",
    gridComplete: function () {
    	var gridName = "nGrid"; // Access the grid Name
    	Merger(gridName,"type");
    	
		} 
    });
	if(checkVersion()=="9")
	{
		
	$("#jqgh_nGrid_CB").addClass("rotateIE9");
		$("#jqgh_nGrid_CR").addClass("rotateIE9");
		$("#jqgh_nGrid_PV").addClass("rotateIE9");
		
		$("#jqgh_nGrid_5HT-3").addClass("rotateIE9");
		$("#jqgh_nGrid_CB1").addClass("rotateIE9");
		$("#jqgh_nGrid_GABAa").addClass("rotateIE9");
		$("#jqgh_nGrid_mGLuR1a").addClass("rotateIE9");
		$("#jqgh_nGrid_Mus2R").addClass("rotateIE9");
		$("#jqgh_nGrid_SubPRec").addClass("rotateIE9");
		$("#jqgh_nGrid_vGluT3").addClass("rotateIE9");

		$("#jqgh_nGrid_CCK").addClass("rotateIE9");
		$("#jqgh_nGrid_ENK").addClass("rotateIE9");
		$("#jqgh_nGrid_NG").addClass("rotateIE9");
		$("#jqgh_nGrid_NPY").addClass("rotateIE9");
		$("#jqgh_nGrid_SOM").addClass("rotateIE9");
		$("#jqgh_nGrid_VIP").addClass("rotateIE9");
		$("#jqgh_nGrid_act2").addClass("rotateIE9");
		$("#jqgh_nGrid_CoupTFII").addClass("rotateIE9");
		$("#jqgh_nGrid_nNos").addClass("rotateIE9");
		$("#jqgh_nGrid_RLN").addClass("rotateIE9");


		$("#jqgh_nGrid_ache").addClass("rotateIE9");
		$("#jqgh_nGrid_AMIGO2").addClass("rotateIE9");
		$("#jqgh_nGrid_AMPAR_2_3").addClass("rotateIE9");
		$("#jqgh_nGrid_BDNF").addClass("rotateIE9");
		$("#jqgh_nGrid_Bok").addClass("rotateIE9");
		$("#jqgh_nGrid_Caln").addClass("rotateIE9");
		$("#jqgh_nGrid_CaM").addClass("rotateIE9");
		$("#jqgh_nGrid_CB1").addClass("rotateIE9");
		$("#jqgh_nGrid_CGRP").addClass("rotateIE9");
		$("#jqgh_nGrid_Cx36").addClass("rotateIE9");
		$("#jqgh_nGrid_ChAT").addClass("rotateIE9");
		$("#jqgh_nGrid_Chma2").addClass("rotateIE9");
		$("#jqgh_nGrid_CRF").addClass("rotateIE9");
		$("#jqgh_nGrid_Ctip2").addClass("rotateIE9");
		$("#jqgh_nGrid_Disc1").addClass("rotateIE9");
		$("#jqgh_nGrid_DYN").addClass("rotateIE9");
		$("#jqgh_nGrid_EAAT3").addClass("rotateIE9");
		$("#jqgh_nGrid_ErbB4").addClass("rotateIE9");
		$("#jqgh_nGrid_GABAa_alpha_2").addClass("rotateIE9");
		$("#jqgh_nGrid_GABAa_alpha_3").addClass("rotateIE9");
		$("#jqgh_nGrid_GABAa_alpha_4").addClass("rotateIE9");
		$("#jqgh_nGrid_GABAa_alpha_5").addClass("rotateIE9");
		$("#jqgh_nGrid_GABAa_alpha_6").addClass("rotateIE9");
		$("#jqgh_nGrid_GABAa_beta_1").addClass("rotateIE9");
		$("#jqgh_nGrid_GABAa_beta_2").addClass("rotateIE9");
		$("#jqgh_nGrid_GABAa_beta_3").addClass("rotateIE9");
		$("#jqgh_nGrid_GABAa_delta").addClass("rotateIE9");
		$("#jqgh_nGrid_GABAa_gamma_1").addClass("rotateIE9");
		$("#jqgh_nGrid_GABAa_gamma_2").addClass("rotateIE9");
		$("#jqgh_nGrid_GABA-B1").addClass("rotateIE9");
		$("#jqgh_nGrid_GAT-1").addClass("rotateIE9");
		$("#jqgh_nGrid_GAT-3").addClass("rotateIE9");
		$("#jqgh_nGrid_GluR2_3").addClass("rotateIE9");
		$("#jqgh_nGrid_GluA2").addClass("rotateIE9");
		$("#jqgh_nGrid_GluA1").addClass("rotateIE9");
		$("#jqgh_nGrid_GluA3").addClass("rotateIE9");
		$("#jqgh_nGrid_GluA4").addClass("rotateIE9");
		$("#jqgh_nGrid_GlyT2").addClass("rotateIE9");
		$("#jqgh_nGrid_Id-2").addClass("rotateIE9");
		$("#jqgh_nGrid_Kv3_1").addClass("rotateIE9");
		$("#jqgh_nGrid_Man1a").addClass("rotateIE9");
		$("#jqgh_nGrid_Math-2").addClass("rotateIE9");
		$("#jqgh_nGrid_mGluR1").addClass("rotateIE9");
		$("#jqgh_nGrid_mGluR2_3").addClass("rotateIE9");
		$("#jqgh_nGrid_mGluR4").addClass("rotateIE9");
		$("#jqgh_nGrid_mGluR5").addClass("rotateIE9");
		$("#jqgh_nGrid_mGluR5a").addClass("rotateIE9");
		$("#jqgh_nGrid_mGluR7a").addClass("rotateIE9");
		$("#jqgh_nGrid_mGluR8a").addClass("rotateIE9");
		$("#jqgh_nGrid_Mus1R").addClass("rotateIE9");
		$("#jqgh_nGrid_Mus3R").addClass("rotateIE9");
		$("#jqgh_nGrid_Mus4R").addClass("rotateIE9");
		$("#jqgh_nGrid_MOR").addClass("rotateIE9");
		$("#jqgh_nGrid_NECAB1").addClass("rotateIE9");
		$("#jqgh_nGrid_Neuropilin2").addClass("rotateIE9");
		$("#jqgh_nGrid_NKB").addClass("rotateIE9");
		$("#jqgh_nGrid_PCP4").addClass("rotateIE9");
		$("#jqgh_nGrid_p-CREB").addClass("rotateIE9");
		$("#jqgh_nGrid_PPTA").addClass("rotateIE9");
		$("#jqgh_nGrid_PPTB").addClass("rotateIE9");
		$("#jqgh_nGrid_Prox1").addClass("rotateIE9");
		$("#jqgh_nGrid_PSA-NCAM").addClass("rotateIE9");
		$("#jqgh_nGrid_SATB1").addClass("rotateIE9");
		$("#jqgh_nGrid_SATB2").addClass("rotateIE9");
		$("#jqgh_nGrid_SCIP").addClass("rotateIE9");
		$("#jqgh_nGrid_SPO").addClass("rotateIE9");
		$("#jqgh_nGrid_Sub_P").addClass("rotateIE9");
		$("#jqgh_nGrid_vAChT").addClass("rotateIE9");
		$("#jqgh_nGrid_vGAT").addClass("rotateIE9");
		$("#jqgh_nGrid_vGluR2").addClass("rotateIE9");
		$("#jqgh_nGrid_vGluR3").addClass("rotateIE9");
//		$("#jqgh_nGrid_vGluR8a").addClass("rotateIE9");
		$("#jqgh_nGrid_vGlut1").addClass("rotateIE9");
		$("#jqgh_nGrid_vGluT2").addClass("rotateIE9");
		$("#jqgh_nGrid_VIAAT").addClass("rotateIE9");
		$("#jqgh_nGrid_VILIP").addClass("rotateIE9");
		$("#jqgh_nGrid_Y1").addClass("rotateIE9");
	
		
	}
	else
	{
		$("#jqgh_nGrid_CB").addClass("rotate");
		$("#jqgh_nGrid_CR").addClass("rotate");
		$("#jqgh_nGrid_PV").addClass("rotate");
		
		$("#jqgh_nGrid_5HT-3").addClass("rotate");
		$("#jqgh_nGrid_CB1").addClass("rotate");
		$("#jqgh_nGrid_GABAa").addClass("rotate");
		$("#jqgh_nGrid_mGLuR1a").addClass("rotate");
		$("#jqgh_nGrid_Mus2R").addClass("rotate");
		$("#jqgh_nGrid_SubPRec").addClass("rotate");
		$("#jqgh_nGrid_vGluT3").addClass("rotate");

		$("#jqgh_nGrid_CCK").addClass("rotate");
		$("#jqgh_nGrid_ENK").addClass("rotate");
		$("#jqgh_nGrid_NG").addClass("rotate");
		$("#jqgh_nGrid_NPY").addClass("rotate");
		$("#jqgh_nGrid_SOM").addClass("rotate");
		$("#jqgh_nGrid_VIP").addClass("rotate");
		
		$("#jqgh_nGrid_act2").addClass("rotate");
		$("#jqgh_nGrid_CoupTFII").addClass("rotate");
		$("#jqgh_nGrid_nNos").addClass("rotate");
		$("#jqgh_nGrid_RLN").addClass("rotate");



	$("#jqgh_nGrid_ache").addClass("rotate");
	$("#jqgh_nGrid_AMIGO2").addClass("rotate");
	$("#jqgh_nGrid_AMPAR_2_3").addClass("rotate");
	$("#jqgh_nGrid_BDNF").addClass("rotate");
	$("#jqgh_nGrid_Bok").addClass("rotate");
	$("#jqgh_nGrid_Caln").addClass("rotate");
	$("#jqgh_nGrid_CaM").addClass("rotate");
	$("#jqgh_nGrid_CB1").addClass("rotate");
	$("#jqgh_nGrid_CGRP").addClass("rotate");
	$("#jqgh_nGrid_Cx36").addClass("rotate");
	$("#jqgh_nGrid_ChAT").addClass("rotate");
	$("#jqgh_nGrid_Chma2").addClass("rotate");
	$("#jqgh_nGrid_CRF").addClass("rotate");
	$("#jqgh_nGrid_Ctip2").addClass("rotate");
	$("#jqgh_nGrid_Disc1").addClass("rotate");
	$("#jqgh_nGrid_DYN").addClass("rotate");
	$("#jqgh_nGrid_EAAT3").addClass("rotate");
	$("#jqgh_nGrid_ErbB4").addClass("rotate");
	$("#jqgh_nGrid_GABAa_alpha_2").addClass("rotate");
	$("#jqgh_nGrid_GABAa_alpha_3").addClass("rotate");
	$("#jqgh_nGrid_GABAa_alpha_4").addClass("rotate");
	$("#jqgh_nGrid_GABAa_alpha_5").addClass("rotate");
	$("#jqgh_nGrid_GABAa_alpha_6").addClass("rotate");
	$("#jqgh_nGrid_GABAa_beta_1").addClass("rotate");
	$("#jqgh_nGrid_GABAa_beta_2").addClass("rotate");
	$("#jqgh_nGrid_GABAa_beta_3").addClass("rotate");
	$("#jqgh_nGrid_GABAa_delta").addClass("rotate");
	$("#jqgh_nGrid_GABAa_gamma_1").addClass("rotate");
	$("#jqgh_nGrid_GABAa_gamma_2").addClass("rotate");
	$("#jqgh_nGrid_GABA-B1").addClass("rotate");
	$("#jqgh_nGrid_GAT-1").addClass("rotateIE9");
	$("#jqgh_nGrid_GAT-3").addClass("rotateIE9");
	$("#jqgh_nGrid_GluR2_3").addClass("rotateIE9");
	$("#jqgh_nGrid_GluA2").addClass("rotateIE9");
	$("#jqgh_nGrid_GluA1").addClass("rotate");
	$("#jqgh_nGrid_GluA3").addClass("rotate");
	$("#jqgh_nGrid_GluA4").addClass("rotate");
	$("#jqgh_nGrid_GlyT2").addClass("rotate");
	$("#jqgh_nGrid_Id-2").addClass("rotate");
	$("#jqgh_nGrid_Kv3_1").addClass("rotate");
	$("#jqgh_nGrid_Man1a").addClass("rotate");
	$("#jqgh_nGrid_Math-2").addClass("rotate");
	$("#jqgh_nGrid_mGluR1").addClass("rotate");
	$("#jqgh_nGrid_mGluR2_3").addClass("rotate");
	$("#jqgh_nGrid_mGluR4").addClass("rotate");
	$("#jqgh_nGrid_mGluR5").addClass("rotate");
	$("#jqgh_nGrid_mGluR5a").addClass("rotate");
	$("#jqgh_nGrid_mGluR7a").addClass("rotate");
	$("#jqgh_nGrid_mGluR8a").addClass("rotate");
	$("#jqgh_nGrid_Mus1R").addClass("rotateIE9");
	$("#jqgh_nGrid_Mus3R").addClass("rotate");
	$("#jqgh_nGrid_Mus4R").addClass("rotate");
	$("#jqgh_nGrid_MOR").addClass("rotate");
	$("#jqgh_nGrid_NECAB1").addClass("rotate");
	$("#jqgh_nGrid_Neuropilin2").addClass("rotate");
	$("#jqgh_nGrid_NKB").addClass("rotate");
	$("#jqgh_nGrid_PCP4").addClass("rotate");
	$("#jqgh_nGrid_p-CREB").addClass("rotate");
	$("#jqgh_nGrid_PPTA").addClass("rotate");
	$("#jqgh_nGrid_PPTB").addClass("rotate");
	$("#jqgh_nGrid_Prox1").addClass("rotate");
	$("#jqgh_nGrid_PSA-NCAM").addClass("rotate");
	$("#jqgh_nGrid_SATB1").addClass("rotate");
	$("#jqgh_nGrid_SATB2").addClass("rotate");
	$("#jqgh_nGrid_SCIP").addClass("rotate");
	$("#jqgh_nGrid_SPO").addClass("rotate");
	$("#jqgh_nGrid_Sub_P").addClass("rotate");
	$("#jqgh_nGrid_vAChT").addClass("rotate");
	$("#jqgh_nGrid_vGAT").addClass("rotate");
	$("#jqgh_nGrid_vGluR2").addClass("rotate");
	$("#jqgh_nGrid_vGluR3").addClass("rotate");
//	$("#jqgh_nGrid_vGluR8a").addClass("rotate");
	$("#jqgh_nGrid_vGlut1").addClass("rotate");
	$("#jqgh_nGrid_vGluT2").addClass("rotate");
	$("#jqgh_nGrid_VIAAT").addClass("rotate");
	$("#jqgh_nGrid_VILIP").addClass("rotate");
	$("#jqgh_nGrid_Y1").addClass("rotate");




    }

	$("#nGrid_5HT-3").css("height","120");
	$("#nGrid_act2").css("height","120");
	$("#nGrid_vGluT3").css("height","120");
	$("#nGrid_RLN").css("height","120");
	

var cm = $("#nGrid").jqGrid('getGridParam', 'colModel');
	
	$("#nGrid").mouseover(function(e) {

	var count = $("#nGrid").jqGrid('getGridParam', 'records') + 1;
    var $td = $(e.target).closest('td'), $tr = $td.closest('tr.jqgrow'),
        rowId = $tr.attr('id');
    
   	if (rowId) {
        var ci = $.jgrid.getCellIndex($td[0]); // works mostly as $td[0].cellIndex
		$row = "#"+rowId+" td"; 
		$($row).addClass('highlighted_top');

		/* for(var i=0;i<count;i++)
		{
			$colSelected = "tr#"+i+" td:eq("+ci+")";
			$($colSelected).addClass('highlighted');
			
		}  */
	}
	});

	  jQuery("#nGrid").jqGrid('setGroupHeaders', {
	    useColSpanStyle: true, 
	    groupHeaders:[
	      {startColumnName: 'CB', numberOfColumns: 3, titleText: 'Ca2+ binding proteins',
	    	  cellattr: function(rowId, tv, rawObject, cm, rdata) 
	          {
	             return 'style="border-right:medium solid #ABCCE4;"';
	          }
          },
	      {startColumnName: '5HT-3', numberOfColumns: 7, titleText: 'Receptors/Transporters',
	    	  cellattr: function(rowId, tv, rawObject, cm, rdata) 
	          {
	             return 'style="border-right:medium solid #ABCCE4;"';
	          }
          },
	      {startColumnName: 'CCK', numberOfColumns: 6, titleText: 'Neuropeptides',
	    	  cellattr: function(rowId, tv, rawObject, cm, rdata) 
	          {
	             return 'style="border-right:medium solid #ABCCE4;"';
	          }
          },
	      {startColumnName: 'act2', numberOfColumns: 4, titleText: 'Misc',
	    	  cellattr: function(rowId, tv, rawObject, cm, rdata) 
	          {
	             return 'style="border-right:medium solid #ABCCE4;"';
	          }
          },
	      {startColumnName: 'ache', numberOfColumns: 76, titleText: 'Others',
	    	  cellattr: function(rowId, tv, rawObject, cm, rdata) 
	          {
	             return 'style="border-right:medium solid #ABCCE4;"';
	          }
          }
	      ]   
	  });

		var $i=0;
		$(".jqg-second-row-header").children().each(function()
		{
			if($i>1){
				$(this).css("border-right","medium solid");
				$(this).css("border-right-color","#ABCCE4");
			}
			$i++;	
		});
	  
	$("#nGrid").mouseout(function(e) {
		var count = $("#nGrid").jqGrid('getGridParam', 'records') + 1;
    	var $td = $(e.target).closest('td'), $tr = $td.closest('tr.jqgrow'),
        	rowId = $tr.attr('id'), ci;
   		if (rowId) {
        ci = $.jgrid.getCellIndex($td[0]); // works mostly as $td[0].cellIndex
        	$row = "#"+rowId+" td";  
			$($row).removeClass('highlighted_top');
			/* for(var i=0;i<count;i++)
			{
				$colSelected = "tr#"+i+" td:eq("+ci+")";
				$($colSelected).removeClass('highlighted');
			} */ 
		}
	}); 
});




//----------


</script>
</head>


<body> 
<!-- COPY IN ALL PAGES -->
<?php 
	include ("function/title.php");
	include ("function/menu_main.php");
?>	

<div class='title_area'>
	<font class="font1">Browse molecular markers matrix</font>
	
	<a href="markers.php"><img src='images/Active_button.png' width="115" height="24" style='padding-top: 2px;'/></a>
	<a href="markers_all.php"><img src='images/all_markers_button.png' width="115" height="24" style='padding-top: 2px;'/></a>
	

		
</div>

<!-- Submenu no tabs
<div class='sub_menu'>
	<?php
		if ($research);
		else
		{
	?>
			<table width="90%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="100%" align="left">
					<font class='font1'><em>Matrix:</em></font> &nbsp; &nbsp; 
					<a href='morphology.php'><font class="font7">Morphology</font></a> <font class="font7_A">|</font> 
					<font class="font7_B"> Markers</font> <font class="font7_A">|</font> 
					<a href='ephys.php'><font class="font7">Electrophysiology</font> </a><font class="font7_A">|</font> 
					<a href='connectivity.php'><font class="font7"> Connectivity</font></a>
					</font>	
				</td>
			</tr>
			</table>
	<?php
		}
	?>		
</div>
 -->
<!-- ------------------------ -->

<div class="table_position">
<table border="0" cellspacing="0" cellpadding="0" class="tabellauno">
	<tr>
 		<td width="988">
			<table id="nGrid"></table>
			<div id="pager"></div>
		</td>
	</tr>
</table>			
<table width="100%" border="0" cellspacing="0" cellpadding="0" class='body_table'>
  <tr>
    <td>
		<!-- ****************  BODY **************** -->
		<?php 
			if ($research){
				$full_search_string = $_SESSION['full_search_string'];
				if ($number_type == 1)
					print ("<font class='font3'> $number_type Result  [$full_search_string]</font>");
				else
					print ("<font class='font3'> $number_type Results  [$full_search_string]</font>");			
			}
		?>		
		<font class='font5'><strong>Legend:</strong> </font>&nbsp; &nbsp;
		<img src='images/positive.png' width="13px" border="0"/> <font class='font5'>Positive </font> &nbsp; &nbsp; 
		<img src='images/negative.png' width="13px" border="0"/> <font class='font5'>Negative </font>&nbsp; &nbsp; 
		<img src="images/positive-negative-subtypes.png" width="13px" border="0"/> <font class='font5'>Positive-Negative (subtypes) </font> &nbsp; &nbsp; 
		<img src="images/positive-negative-species.png" width="13px" border="0"/> <font class='font5'>Positive-Negative (species/protocol differences) </font> &nbsp; &nbsp; 
		<img src="images/positive-negative-conflicting.png" width="13px" border="0"/> <font class='font5'>Positive-Negative (conflicting data) </font> &nbsp; &nbsp; 
		<img src="images/unknown.png" width="13px" border="0"/> <font class='font5'>No Information Available </font> &nbsp; &nbsp; 
		<img src="images/searching.png" width="13px" border="0"/> <font class='font5'>Search ongoing </font> &nbsp; &nbsp;
		<br />
		&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
		<font face="Verdana, Arial, Helvetica, sans-serif" color="#339900" size="2"> +/green: </font> <font face="Verdana, Arial, Helvetica, sans-serif" size="2"> Excitatory</font>
		&nbsp; &nbsp; 
		<font face="Verdana, Arial, Helvetica, sans-serif" color="#CC0000" size="2"> -/red: </font> <font face="Verdana, Arial, Helvetica, sans-serif" size="2"> Inhibitory</font>
		<br />
		&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
		<font class='font5'>Pale versions of the colors in the matrix indicate interpretations of neuronal property information that have not yet been fully verified.</font>
</td>
</tr>
</table>
</div>
</body>
</html>

<?php
  include ("access_db.php");
?>
<?php
session_start();
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
//if ($perm == NULL)
if ($perm == 1 && $_SESSION['flag']== NULL)
	header("Location:error1.html");
?>
<?php
/*session_start();
$perm = $_SESSION['perm'];
if ($perm == NULL)
	header("Location:error1.html");
*/?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php
//include ("access_db.php");
require_once('class/class.type.php');
require_once('class/class.property.php');
require_once('class/class.evidencepropertyyperel.php');
require_once('class/class.evidenceevidencerel.php');
require_once('class/class.epdataevidencerel.php');
require_once('class/class.epdata.php');
require_once('class/class.typetyperel.php');

include ("function/name_ephys.php");
include ("function/stm_lib.php");

$type = new type($class_type);
$type -> retrive_id();
$number_type = $type->getNumber_type();
$property_1 = new property($class_property);
$evidencepropertyyperel = new evidencepropertyyperel($class_evidence_property_type_rel);
$evidenceevidencerel = new evidenceevidencerel($class_evidenceevidencerel);
$epdataevidencerel = new epdataevidencerel($class_epdataevidencerel);
$epdata = new epdata($class_epdata);
$typetyperel = new typetyperel();

$morphology_properties_query =
"SELECT DISTINCT t.name, t.subregion, t.nickname, p.subject, p.predicate, p.object, eptr.Type_id, eptr.Property_id
      FROM EvidencePropertyTypeRel eptr
      JOIN (Property p, Type t) ON (eptr.Property_id = p.id AND eptr.Type_id = t.id)
      WHERE predicate = 'in' AND object REGEXP ':'";


// Function to create the temporary table for the search field: ++++++++++++++++++++++++++++++++++
function create_result_table_result ($name_temporary_table)
{	
	$drop_table ="DROP TABLE $name_temporary_table";
	$query = mysql_query($drop_table);
	
	$creatable=	"CREATE TABLE IF NOT EXISTS $name_temporary_table (
				   id int(4) NOT NULL AUTO_INCREMENT,
				   id_type varchar(200),
				   PRIMARY KEY (id));";
	$query = mysql_query($creatable);
}	
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


// Function to insert the type_id in the temporary table AND: ++++++++++++++++++++++++++++++++++++++
function insert_result_table_result($table, $id_type, $n_type_id)
{
	for ($i=0; $i<$n_type_id; $i++)
	{
		$query_i = "INSERT INTO $table
		  (id,
			id_type
		   )
		VALUES
		  (NULL,
			'$id_type[$i]'
		   )";
		$rs2 = mysql_query($query_i);	
	}
}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


// Function to retrieve information in the temporary table by ID: +++++++++++++++++++++++++++++++++++
function information_by_id ($name_temporary_table, $id)
{
	$query = "SELECT property, part, relation, value FROM $name_temporary_table WHERE id='$id'";
	$rs = mysql_query($query);
	while(list($property, $part, $relation, $value) = mysql_fetch_row($rs))
	{
		$varr[0] = $property;
		$varr[1] = $part;
		$varr[2] = $relation;
		$varr[3] = $value;
	}

	return $varr;
}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// SEARCH Function for MORPHOLOGY: ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function morphology_search_for_hippocampal_formation ($evidencepropertyyperel, $property_1, $part, $rel, $val)
{	
	
	if ($val == 'Hippocampal formation')
		$property_1 -> retrive_ID(4, $part, $rel, NULL);
	else if ($val == 'DG')
		$property_1 -> retrive_ID(5, $part, $rel, $val);
	else if ($val == 'CA3')
		$property_1 -> retrive_ID(5, $part, $rel, $val);
	else if ($val == 'CA2')
		$property_1 -> retrive_ID(5, $part, $rel, $val);		
	else if ($val == 'CA1')
		$property_1 -> retrive_ID(5, $part, $rel, $val);		
	else if ($val == 'SUB')
		$property_1 -> retrive_ID(5, $part, $rel, $val);
	else if ($val == 'EC')
		$property_1 -> retrive_ID(5, $part, $rel, $val);
	else
		$property_1 -> retrive_ID(1, $part, $rel, $val);
	
	$n_property_id = $property_1 -> getNumber_type();
	
	$n_tot = 0;
	for ($i1=0; $i1<$n_property_id; $i1++)
	{
		$property_id = $property_1 -> getProperty_id($i1);

	
		$evidencepropertyyperel -> retrive_Type_id_by_Property_id($property_id);
		$n_type_id = $evidencepropertyyperel -> getN_Type_id();
	
		for ($i2=0; $i2<$n_type_id; $i2++)
		{	
			$type_id[$n_tot] = $evidencepropertyyperel -> getType_id_array($i2);
			$n_tot = $n_tot + 1;	
		}
	}	
	// Now, the program must remove the doubble or more type_id:	
	if ($type_id != NULL)
		$new_type_id=array_unique($type_id);
	
	return $new_type_id;
}
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


// SEARCH Function for MARKERS: ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function markers_search($evidencepropertyyperel, $property_1, $type, $subject, $predicate)
{
	$new_type_id_nan = array();

	// retrieve id_property from Property table using SUBJECT and PREDICATE:
	if ($predicate == 'is expressed')
	{
		$predicate3[1] = 'positive';
		$predicate3[2] = 'unknown';
		$nn = 2;
		//i'm here
	}
	if ($predicate == 'is not expressed')
	{
		$predicate3[1] = 'negative';
		$predicate3[2] = 'unknown';
		$nn = 2;
	}
	if ($predicate == 'unknown')
	{
		$predicate3[1] = 'unknown';
		$nn = 1;		
	}

	$n_tot = 0;
	for ($i=1; $i<=$nn; $i++)
	{ 
		$new_type_id = NULL;
		
		
		//MY DELETE
		//print("...predicate3:-".$predicate3[$i]);
		 
		//MyDelete ends
		
		$property_1 -> retrive_ID(2, $subject, NULL, $predicate3[$i]);
		
		//MY DELETE
		//print("...property_1:-");
		//print_r($property_1->retrive_ID(2, $subject, NULL, $predicate3[$i]));
		//MyDelete ends
		
		$n_property_id = $property_1 -> getNumber_type();
		//MY DELETE
		//print("...n_property_id:-".$n_property_id);
		//MyDelete ends
		
		for ($i0=0; $i0<$n_property_id; $i0++)
		{
			$property_id = $property_1 -> getProperty_id($i0);
		
			// retrieve the Type_id from EvidencePropertyTypeRel by using property_id:
			$evidencepropertyyperel -> retrive_Type_id_by_Property_id($property_id);
			//MY DELETE
			//print("....evidencepropertyyperel:-");
		//print_r($evidencepropertyyperel);
		//MyDelete ends
		
			$n_type_id = $evidencepropertyyperel -> getN_Type_id();
			//MY DELETE
		//print("...n_type_id:-".$n_type_id);
		//MyDelete ends
		
		
			for ($i1=0; $i1<$n_type_id; $i1++)
			{
				
		
				if ($i == 1)
					$type_id[$n_tot] = $evidencepropertyyperel -> getType_id_array($i1);
					//MY DELETE
		//print("...type_id[n_tot]:-".$type_id[$n_tot]);
		//MyDelete ends		
				if ($i == 2)
				{
					$type_r = $evidencepropertyyperel -> getType_id_array($i1);
					$type_id[$n_tot] = "10_".$type_r;
					//MY DELETE
		//print("...type_id[n_tot]:-".$type_id[$n_tot]);
		//MyDelete ends	
				}					
				$n_tot = $n_tot + 1;
			} // END $i1
		} // END $i0
		
		// Now, the program must remove the doubble or more type_id:	
		if ($type_id != NULL)
			$new_type_id=array_unique($type_id);
			//MY DELETE
			//print("....new_type_id:-");
		//print_r($new_type_id);
		//MyDelete ends	

			
	} // END $i
	
	return $new_type_id;
}
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


// SEARCH Function for MARKERS: ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function major_neurontransmitter_search($property_1, $type, $subject, $predicate)
{
	$new_type_id_nan = array();

	$type -> retrive_by_excit_inhib($predicate);
	$n_type= $type -> getNumber_type();
	
	for ($i=0; $i<$n_type; $i++){
		
		$type_id[$i]= $type -> getID_array($i);
	}
		
	if ($type_id != NULL)
		$new_type_id=array_unique($type_id);

	return $new_type_id;
}
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


// SEARCH Function for EPHYS: ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// STM Alternative Ephys search
//function ephys_search($conditions) {
  //$base_query =
    //"SELECT  t.name, t.position, t.nickname, p.subject, p.predicate, p.object, eptr.Type_id, eptr.Property_id, eptr.Evidence_id
    //FROM EvidencePropertyTypeRel eptr
    //JOIN (Property p, Type t) ON (eptr.Property_id = p.id AND eptr.Type_id = t.id)";
  //$where_clause = ' ' . create_where_clause_from_conditions($conditions);  // see stm_lib
  //$order_clause = " ORDER BY t.position";
  //$query = $base_query . $where_clause . $order_clause;
  //$result = mysql_query($query);
  //$records = result_set_to_array($result, "Type_id");
  //return $records;
//}


function ephys_search($evidencepropertyyperel, $property_1, $type, $subject, $predicate, $class_evidence_property_type_rel, $epdataevidencerel, $value, $epdata)
{
	// retrieve id_property from property:
	$property_1 -> retrive_ID(3, $subject, NULL, NULL);
	$n_property_id = $property_1 -> getNumber_type();
	
	$n_tot = 0;
	for ($i1=0; $i1<$n_property_id; $i1++)
	{
		$property_id = 	$property_1 -> getProperty_id($i1);
	
		$evidencepropertyyperel -> retrive_evidence_id1($property_id);
		$n_evidence_id = $evidencepropertyyperel -> getN_evidence_id();

		$n_epdata_result = 0;
		for ($i2=0; $i2<$n_evidence_id; $i2++)
		{
			// Evidence_ID1:
			$evidence_id = $evidencepropertyyperel -> getEvidence_id_array($i2);
		
			// With this evidence_id retrieve the Epdata_id from EpdataEvidenceRel:
			$epdataevidencerel -> retrive_Epdata($evidence_id); 
			
			$id_epdata = $epdataevidencerel -> getEpdata_id();
			
    	// STM this must be fixed later... temporary hack to make ephys search work
			//$value_1 = str_replace(' mV', '', $value);
      		//$value_1 = str_replace(' ms', '', $value1);
      		//$value_1 = str_replace(' Hz', '', $value1);
      		//$value_1 = str_replace(' mOm', '', $value1);
      		$value_1 = preg_replace('/[^\d\.\-]/', '', $value);
			
			$epdata -> retrive_all_information($id_epdata);
			$epdata_value1 = $epdata -> getValue1();
			
			if ($predicate == '=')
			{
				if ((float) $epdata_value1 == (float) $value_1)
				{
					$id_epdata_result[$n_epdata_result]=$id_epdata;
					$id_evidence_result[$n_epdata_result]=$evidence_id;
					$n_epdata_result = $n_epdata_result + 1;
				}
				else;
			}
			if ($predicate == '<')
			{
				if ((float) $epdata_value1 < (float) $value_1)
				{
					$id_epdata_result[$n_epdata_result]=$id_epdata;
					$id_evidence_result[$n_epdata_result]=$evidence_id;
					$n_epdata_result = $n_epdata_result + 1;
				}
				else;	
			}
			if ($predicate == '<=')
			{
				if ((float) $epdata_value1 <= (float) $value_1)
				{
					$id_epdata_result[$n_epdata_result]=$id_epdata;
					$id_evidence_result[$n_epdata_result]=$evidence_id;
					$n_epdata_result = $n_epdata_result + 1;
				}
				else;
			}			
			if ($predicate == '>')
			{
				if ((float) $epdata_value1 > (float) $value_1)
				{
					$id_epdata_result[$n_epdata_result]=$id_epdata;
					$id_evidence_result[$n_epdata_result]=$evidence_id;
					$n_epdata_result = $n_epdata_result + 1;
				}
				else;				
			}
			if ($predicate == '>=')
			{
				if ((float) $epdata_value1 >= (float) $value_1)
				{
					$id_epdata_result[$n_epdata_result]=$id_epdata;
					$id_evidence_result[$n_epdata_result]=$evidence_id;
					$n_epdata_result = $n_epdata_result + 1;
				}
				else;
			}			
			
		} // END $i2

	} // END $i1
	
	$n_tot = 0;
	for ($i1=0; $i1<count($id_epdata_result); $i1++)
	{
		$evidencepropertyyperel -> retrive_type_id_by_evidence($id_evidence_result[$i1]);
	
		$n_typ_id = $evidencepropertyyperel -> getN_Type_id();
	
		for ($i2=0; $i2<$n_typ_id; $i2++)
		{
			$type_id[$n_tot] = $evidencepropertyyperel -> getType_id_array($i2);
			$n_tot = $n_tot + 1;
		} // END for $i2
	} // END for $i1

	// Now, the program must remove the doubble or more type_id:	
	if ($type_id != NULL)
		$new_type_id=array_unique($type_id);
	
	return $new_type_id;
}


// SEARCH Function for CONNECTIVITY: ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function connectivity_search ($evidencepropertyyperel, $property_1, $type, $part, $rel, $val)
{	
	$id = $type -> getId();
	$evidencepropertyyperel -> retrive_evidence_id2($id);
	$n_evidence_id_3 = $evidencepropertyyperel -> getN_evidence_id();
	
	$morphology_properties_query =
	"SELECT DISTINCT t.name, t.subregion, t.nickname, p.subject, p.predicate, p.object, eptr.Type_id, eptr.Property_id
		      FROM EvidencePropertyTypeRel eptr
		      JOIN (Property p, Type t) ON (eptr.Property_id = p.id AND eptr.Type_id = t.id)
		      WHERE predicate = 'in' AND object REGEXP ':'";
	
	$explicit_target_and_source_base_query =
	"SELECT
		      t1.id as t1_id, t1.subregion as t1_subregion, t1.nickname as t1_nickname,
		      t2.id as t2_id, t2.subregion as t2_subregion, t2.nickname as t2_nickname
		      FROM TypeTypeRel ttr
		      JOIN (Type t1, Type t2) ON ttr.Type1_id = t1.id AND ttr.Type2_id = t2.id";
	
	$one_type_query = $morphology_properties_query . " AND eptr.Type_id = '$id'";
	
	if (strpos($rel,'known to come from') === 0) {
		$explicit_target_query = $explicit_target_and_source_base_query . " WHERE Type1_id = '$id' AND connection_status = 'positive'";
		$result = mysql_query($explicit_target_query);
		$explicit_targets = result_set_to_array($result, "t2_id");
		$conn_search_result_array = $explicit_targets;
	}
	elseif (strpos($rel,'known not to come from') === 0) {
		$explicit_nontarget_query = $explicit_target_and_source_base_query . " WHERE Type1_id = '$id' AND connection_status = 'negative'";
		$result = mysql_query($explicit_nontarget_query);
		$explicit_nontargets = result_set_to_array($result, "t2_id");
		$conn_search_result_array = $explicit_nontargets;
	}
	elseif (strpos($rel,'known to target') === 0) {
		$explicit_source_query = $explicit_target_and_source_base_query . " WHERE Type2_id = '$id' AND connection_status = 'positive'";
		$result = mysql_query($explicit_source_query);
		$explicit_sources = result_set_to_array($result, "t1_id");
		$conn_search_result_array = $explicit_sources;
	}
	elseif (strpos($rel,'known not to target') === 0) {
		$explicit_nonsource_query = $explicit_target_and_source_base_query . " WHERE Type2_id = '$id' AND connection_status = 'negative'";
		$result = mysql_query($explicit_nonsource_query);
		$explicit_nonsources = result_set_to_array($result, "t1_id");
		$conn_search_result_array = $explicit_nonsources;
	}
	elseif (strpos($rel,'potentially from') === 0) {
		$axon_query = $one_type_query . " AND subject = 'axons'";
		$result = mysql_query($axon_query);
		$axon_parcels = result_set_to_array($result, 'object');
		$possible_targets = filter_types_by_morph_property('dendrites', $axon_parcels);
		
		$explicit_target_query = $explicit_target_and_source_base_query . " WHERE Type1_id = '$id' AND connection_status = 'positive'";
		$result = mysql_query($explicit_target_query);
		$explicit_targets = result_set_to_array($result, "t2_id");
		
		$explicit_nontarget_query = $explicit_target_and_source_base_query . " WHERE Type1_id = '$id' AND connection_status = 'negative'";
		$result = mysql_query($explicit_nontarget_query);
		$explicit_nontargets = result_set_to_array($result, "t2_id");		
		$conn_search_result_array = array_merge(array_diff($possible_targets, $explicit_nontargets), $explicit_targets);
	}
	elseif (strpos($rel,'potentially targeting') === 0) {	
		$dendrite_query = $one_type_query . " AND subject = 'dendrites'";
		$result = mysql_query($dendrite_query);
		$dendrite_parcels = result_set_to_array($result, 'object');
		$possible_sources = filter_types_by_morph_property('axons', $dendrite_parcels);
				
		$explicit_source_query = $explicit_target_and_source_base_query . " WHERE Type2_id = '$id' AND connection_status = 'positive'";
		$result = mysql_query($explicit_source_query);
		$explicit_sources = result_set_to_array($result, "t1_id");
		
		$explicit_nonsource_query = $explicit_target_and_source_base_query . " WHERE Type2_id = '$id' AND connection_status = 'negative'";
		$result = mysql_query($explicit_nonsource_query);
		$explicit_nonsources = result_set_to_array($result, "t1_id");		
		$conn_search_result_array = array_merge(array_diff($possible_sources, $explicit_nonsources), $explicit_sources);
	}
			
	if ($conn_search_result_array != NULL) {
		$conn_search_result_array = array_unique($conn_search_result_array);
		$conn_search_result_array = get_sorted_records($conn_search_result_array);
	}
	
	$n_tot = 0;
	for ($i2=0; $i2<count($conn_search_result_array); $i2++) {
		$type_id[$n_tot] = $conn_search_result_array[$i2]['id'];//$evidencepropertyyperel -> getType_id_array($i2);
		$n_tot = $n_tot + 1;
	} // END for $i2
	
	// Now, the program must remove the double or more type_id:	
	if ($type_id != NULL)
		$new_type_id=array_unique($type_id);
	
	return $new_type_id;
}
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++




$time1 = microtime_float();


// Search engine for FIND NEURON *****************************************************************************************
// ***********************************************************************************************************************
// ***********************************************************************************************************************

// 1) Retrive the number of search line from temporary table:
$name_temporary_table_search = $_REQUEST['name_table'];
//MY DELETE
//print("...name_temporary_table_search:-".$name_temporary_table_search);
//MyDelete ends

$query = "SELECT id FROM $name_temporary_table_search";
$rs = mysql_query($query);
$n_line = 0;
while(list($id) = mysql_fetch_row($rs))
{
	$id_line[$n_line]=$id;
	$n_line = $n_line + 1;
}

// 2) The program MUST separate the AND and the OR: --------------------------------------
$a = 0;	// stores the number of OR lines
$b = 0; // stores the number of AND lines + 1 (for the first line)
for ($i=0; $i<$n_line; $i++)
{
	$query = "SELECT id, operator FROM $name_temporary_table_search WHERE id = '$id_line[$i]'";
	$rs = mysql_query($query);
	while(list($id, $operator) = mysql_fetch_row($rs))
	{	
		if ( ($operator == '') || ($operator == 'AND') )
		{
			$id_res[$a][$b]= $id;
			$b = $b + 1;
		}
		else
		{
			$a = $a + 1;
			$b = 0;
			$id_res[$a][$b]= $id;
			$b = $b + 1;		
		}
	}	
	// ----------------------------------------------------------------------------------------
} // end for $n_line



// The research +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// Creates $a table to insert the results for each AND:
$ip_address = $_SERVER['REMOTE_ADDR'];
$ip_address = str_replace('.', '_', $ip_address);
$time_t = time();

$name_temporary_table_result = "search_result_table_".$ip_address."__".$time_t;
//MY DELETE
//print("...name_temporary_table_result:-".$name_temporary_table_result);
//MyDelete ends



create_result_table_result($name_temporary_table_result);

$n_res1 = 0;

for ($i=0; $i<=$a; $i++)   // Count for each OR
{
	$id_type_res = array(); // Arrays where will be inserted the results of ID TYPE

	$n_b = count($id_res[$i]);
	//MY DELETE
//print("...n_b:-".$n_b);
//MyDelete ends
	
	
	
	// Association for AND results
	for ($i1=0; $i1<$n_b; $i1++) 
	{
		// in $id_res[$i][$i1] there are the id from temporary table divided from OR.
		
		// Retrieve the information from temporary table:
		$varr = information_by_id($name_temporary_table_search, $id_res[$i][$i1]);
		
		$property = $varr[0];
		$part = $varr[1];
		$relation = $varr[2];
		$value = $varr[3];

		if ($relation == 'is found in')
			$predicate = 'in';			
		if ($relation == 'is not found in')
			$predicate = 'not in';
			
		if ($relation == 'is expressed')	
			$predicate = $relation;
		if ($relation == 'is not expressed')
			$predicate = $relation;
		if ($relation == 'unknown')
			$predicate = $relation;			 
			
			
		if ($part == 'Soma')
			$subject = 'somata';			
		if ($part == 'Axon')
			$subject = 'axons';	
		if ($part == 'Dendrite')
			$subject = 'dendrites';	
					
		// Script for MORPHOLOGY +++++++++++++++++++++++++++++++++++++++++++		
		if ($property == 'Morphology')
		{
			$res = morphology_search_for_hippocampal_formation ($evidencepropertyyperel, $property_1, $subject, $predicate, $value);	
			
			if ($res != NULL)
				$id_type_res = array_merge($id_type_res, $res); 	
	
		}
		// END Script for MORPHOLOGY +++++++++++++++++++++++++++++++++++++++
		
		
		// Script for MARKERS +++++++++++++++++++++++++++++++++++++++++++		
		if ($property == 'Molecular markers')
		{
			
			$subject = $part;	
			
			if (strpos($subject, 'GABAa') == 'TRUE')
				$subject='Gaba-a-alpha';
			if (strpos($subject, '-act2') == 6)
				$subject='alpha-actinin-2';				

			$res_marker = markers_search($evidencepropertyyperel, $property_1, $type, $subject, $predicate);
		
			if ($res_marker != NULL)
				$id_type_res = array_merge($id_type_res, $res_marker);
				
				//MY DELETE
			//	print("...id_type_res:-");
		//print_r($id_type_res);
		//MyDelete ends	
		}
		// END Script for MARKERS +++++++++++++++++++++++++++++++++++++++		
		
		
		// Script for Major Neurontransmitter +++++++++++++++++++++++++++++++++++++++++++
		if ($property == 'Major Neurontransmitter')
		{
			
			$subject = $part;
			//check for GABA
			if (($subject == 'GABA' & $relation == 'is expressed')||($subject == 'Glutamate' & $relation == 'is not expressed')) {
				
				$predicate='i';
			}
			
			//check for Glutamate
			if (($subject == 'Glutamate' & $relation == 'is expressed')||($subject == 'GABA' & $relation == 'is not expressed')) {
			
				$predicate='e';
			}
			
			
			$res_marker = major_neurontransmitter_search($property_1, $type, $subject, $predicate) ;
		
			if ($res_marker != NULL)
				$id_type_res = array_merge($id_type_res, $res_marker);
		
		}
		// END Script for Major Neurontransmitter +++++++++++++++++++++++++++++++++++++++
		
		
		// Script for ELECTROPHYSIOLOGY +++++++++++++++++++++++++++++++++++++++++++		
		if ($property == 'Electrophysiology')
		{
			$predicate = $relation;	
			$subject=real_name_ephys($part);
			$res_ephys = ephys_search($evidencepropertyyperel, $property_1, $type, $subject, $predicate, $class_evidence_property_type_rel, $epdataevidencerel, $value, $epdata);
				
			if ($res_ephys != NULL)
				$id_type_res = array_merge($id_type_res, $res_ephys); 	
		}
		// END Script for ELETROPHISIOLOGY +++++++++++++++++++++++++++++++++++++++			
		
		// Script for CONNECTIVITY +++++++++++++++++++++++++++++++++++++++++++
		if ($property == 'Connectivity')
		{
			$colPos = strpos($value, ':');
			$theSubregion = substr($value, 0, $colPos);
			$theNickname = substr($value, $colPos+1, strlen($value)-1);
						
			$aSubregion = '';
			$aNickname = '';
			$aType = 0;
			
			while ( !(($aSubregion == $theSubregion) And ($aNickname == $theNickname)) And ($aType < $number_type)) {
				$id_type_row = $type->getID_array($aType);
				$type -> retrive_by_id($id_type_row);
				$aSubregion = $type->getSubregion();
				$aNickname = $type->getNickname();
				$aType = $aType + 1;
			}
			
			$res_connectivity = connectivity_search($evidencepropertyyperel, $property_1, $type, $part, $relation, $value);
			
			if ($res_connectivity != NULL)
				$id_type_res = array_merge($id_type_res, $res_connectivity);
		}
		// END Script for CONNECTIVITY +++++++++++++++++++++++++++++++++++++++
				
	}  // End FOR $i1 (AND)


	// The program do the AND in the temporary table (in $n_b):
	$n1 = count($id_type_res);

	for ($q=0; $q<$n1; $q++)
	{
		$ww=$id_type_res[$q];
		$count_result = array_count_values($id_type_res);
		
		if ($count_result[$ww] == $n_b)
		{
			$id_result[$n_res1] = $ww;
			$n_res1 = $n_res1 + 1;
		}
	}
	
	// Insert the result AND & OR in the temporary results table:
	insert_result_table_result($name_temporary_table_result, $id_result, $n_res1);

} // END for count OR($i)
// END The research +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++



// END Search engine for FIND NEURON *************************************************************************************
// ***********************************************************************************************************************
// ***********************************************************************************************************************


$time2 = microtime_float();
$delta_time = $time2 - $time1;

$delta_time_format = number_format($delta_time,2,'.',',');
?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php include ("function/icon.html"); ?>

<title>Find Neurons</title>

 <script type="text/javascript" src="style/resolution.js"></script>

</head>

<body>

<!-- COPY IN ALL PAGES -->
<?php 
	include ("function/title.php");
	include ("function/menu_main.php");
?>	

<div class='title_area'>
	<font class="font1">Search by neuron type results</font>
</div>

<!-- 
<div align="center" class="title_3">
	<table width="90%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%">
			<font size='5' color="#990000" face="Verdana, Arial, Helvetica, sans-serif">Results Page</font>
		</td>
	</tr>
	</table>
</div>
 -->
<!-- ------------------------ -->

<div class="table_position_search_page">
<table width="95%" border="0" cellspacing="5" cellpadding="0" class='body_table'>
  <tr>
    <td width="80%">
		<!-- ****************  BODY **************** -->
		<?php
		
		
			$query = "SELECT DISTINCT id_type FROM $name_temporary_table_result";
			$rs = mysql_query($query);
			$n_result_tot=0;
			$n_result_tot_unknown=0;
			while(list($id) = mysql_fetch_row($rs))
			{			
				if (strpos($id, '0_') == 1)
				{
					$id = str_replace('10_', '', $id);
				
					$type -> retrive_by_id($id);
					$status = $type -> getStatus();	
								
					if ($status == 'active')
					{
						$id_t_unknown[$n_result_tot_unknown] = $id;
						$name_type_unknown[$n_result_tot_unknown] = $type -> getNickname();
						$subregion_type_unknown[$n_result_tot_unknown] = $type -> getSubregion();
						$position_type_unknown[$n_result_tot_unknown] = $type -> getPosition();
						
						//print("/nickname:".$name_type_unknown[$n_result_tot_unknown]);
						//print("/subregion:".$subregion_type_unknown[$n_result_tot_unknown]);
						$pos=0;
						$pos= strpos($name_type_unknown[$n_result_tot_unknown],$subregion_type_unknown[$n_result_tot_unknown]);
						
						if($pos!==false)
						{
							
							$name_type_unknown[$n_result_tot_unknown]=str_replace($subregion_type_unknown[$n_result_tot_unknown],"", $name_type_unknown[$n_result_tot_unknown]);
							//substr_replace($subregion_type_unknown[$n_result_tot_unknown],"",$pos);
							
						}	
						
						$n_result_tot_unknown = $n_result_tot_unknown +1;
					}						
				}
				else
				{
					$type -> retrive_by_id($id);
					$status = $type -> getStatus();		
									
					if ($status == 'active')
					{
						$id_t[$n_result_tot] = $id;
						$name_type[$n_result_tot] = $type -> getNickname();
						$subregion_type[$n_result_tot] = $type -> getSubregion();
						$position_type[$n_result_tot] = $type -> getPosition();
						
						
						//print("/nickname:".$name_type[$n_result_tot]);
						//print("/subregion:".$subregion_type[$n_result_tot]);
						$pos=0;
						$pos= strpos($name_type[$n_result_tot],$subregion_type[$n_result_tot]);
						
						if($pos!== false)
						{
							
							
							$name_type[$n_result_tot]= str_replace($subregion_type[$n_result_tot],"", $name_type[$n_result_tot]);
							//print(substr_replace($subregion_type[$n_result_tot_unknown],"",$pos));
							
						}	

						$n_result_tot = $n_result_tot +1;
					}
				}
			} // END While
			
			$full_search_string = $_SESSION['full_search_string'];
			$full_search_string_to_print = str_replace('OR', '<br>OR', $full_search_string);
			$full_search_string_to_print = str_replace('AND', '<br>AND', $full_search_string_to_print);
			
			print ("<br><br>" . $full_search_string_to_print . "<br><br>");
			
			if ($n_result_tot == 1)
				print ("<font class='font3'> returned $n_result_tot result ($delta_time_format seconds)</font><br>");
			else
				print ("<font class='font3'> returned $n_result_tot results ($delta_time_format seconds)</font><br>");			
		
		?>
		<br /><br />

		<table border="0" cellspacing="3" cellpadding="0" class='table_result'>
		<tr>
			<td align="center" width="5%">  </td>
			<td align="center" width="10%">  </td>
			
			<?php
				if($n_result_tot_unknown)
				{
					if($n_result_tot)
					{
						$query_string = "SELECT N, operator, property, part, relation, value FROM $name_temporary_table";
						//print ("$part : ($relation $value) ");
						print ("<td align='center' width='30%' class='table_neuron_page3'> Neurons </td>");
						//print ("<td align='center' width='30%' class='table_neuron_page3'> $part : ($relation $value) </td>");
						//print ("<td align='center' width='30%' class='table_neuron_page3'> Is Expressed / Is Not Expressed </td>");
					}					
				}
				else
				{
					if($n_result_tot)
						print ("<td align='center' width='30%' class='table_neuron_page3'> Neuron Types </td>");
				}
			
			?>
			<td align="right" width="55%"> </td>
		</tr>
		</table>
		
		<table border="0" cellspacing="3" cellpadding="0" class='table_result'>
		
		<?php		
			if ($n_result_tot)
			{
				//array_multisort($position_type, $id_t, $name_type);
				array_multisort($position_type,$id_t,$subregion_type,$name_type);
				for ($i=0; $i<$n_result_tot; $i++)
				{
					$i9=$i+1;
					print ("
							<tr>
								<td align='center' width='5%'>  </td>
								<td align='center' width='10%' class='table_neuron_page4'> $i9 </td>
								<td align='center' width='30%' class='table_neuron_page4'> 
									<a href='neuron_page.php?id=$id_t[$i]'>
										<font class='font13'>$subregion_type[$i] $name_type[$i] </font>
									</a>
								</td>
								<td align='right' width='55%'> </td>
							</tr>				
					");
				}		
			}
		?>
		</table>

		<br />
		
		<?php
			if ($n_result_tot_unknown)
			{
				print ("
					<table border='0' cellspacing='3' cellpadding='0' class='table_result'>
						<tr>
							<td align='center' width='5%'>  </td>
							<td align='center' width='10%'>  </td>
							<td align='center' width='30%' class='table_neuron_page3'> Neurons with unknown expression </td>
							<td align='right' width='55%'> </td>
						</tr>
					</table>
				");
		
				print ("<table border='0' cellspacing='3' cellpadding='0' class='table_result'>");
				//	array_multisort($position_type_unknown, $id_t_unknown, $name_type_unknown);
				array_multisort($position_type_unknown, $id_t_unknown, $subregion_type_unknown, $name_type_unknown);
					for ($i=0; $i<$n_result_tot_unknown; $i++)
					{
						$i9=$i+1;
						print ("
								<tr>
									<td align='center' width='5%'>  </td>
									<td align='center' width='10%' class='table_neuron_page4'> $i9 </td>
									<td align='center' width='30%' class='table_neuron_page4'> 
										<a href='neuron_page.php?id=$id_t_unknown[$i]'>
											<font class='font13'>$subregion_type_unknown[$i]  $name_type_unknown[$i] </font>
										</a>
									</td>
									<td align='right' width='55%'> </td>
								</tr>				
						");
					}		
				print ("</table>");		
			}
		?>

		<br /><br /><br />

		<?php
		if ($n_result_tot == 0);
		else {
		
			print ("<table border='0' cellspacing='3' cellpadding='3' class='table_result'>
				<tr>
					<td align='center' width='20%'>  </td>	
					<td align='left'  class='table_neuron_page3' width='75%' colspan='4'> ");
			
			if ($n_result_tot == 1)
				print ("View Result in a Matrix");
			else
				print ("View Results in a Matrix");	
			
			print ("
				</td>		
				</tr>
				<tr>
					<td align='center' width='20%'>  </td>
					<td align='center' width='18%'>
					<form action='morphology_search.php' method='post' style='display:inline' target='_blank'>
						<input type='submit' name='morpology_matrix' value='MORPHOLOGY' />
						<input type='hidden' name='table_result' value=$name_temporary_table_result />
						<input type='hidden' name='research' value='1' />
					</form>	
					</td>			
					<td align='center' width='18%'> 
					<form action='markers_search.php' method='post' style='display:inline' target='_blank'>
						<input type='submit' name='markers_matrix' value='MARKERS' />
						<input type='hidden' name='table_result' value=$name_temporary_table_result />
						<input type='hidden' name='research' value='1' />
					</form>				
					</td>
					<td align='center' width='18%'> 
					<form action='ephys_search.php' method='post' style='display:inline' target='_blank'>
						<input type='submit' name='ephys_matrix' value='EPHYS' />
						<input type='hidden' name='table_result' value=$name_temporary_table_result />
						<input type='hidden' name='research' value='1'  />
					</form>	
					</td>
					<td align='center' width='18%'> 
					<form action='connectivity.php' method='post' style='display:inline' target='_blank'>
						<input type='submit' name='connectivity_matrix' value='CONNECTIVITY' />
						<input type='hidden' name='table_result' value=$name_temporary_table_result />
						<input type='hidden' name='research' value='1'  />
					</form>	
					</td>			
					<td align='right' width='10%'> </td>
				</tr>
				</table> <br /><br />");
			}
		?>
		
	</td>
  </tr>
</table>
</div>

</body>
</html>

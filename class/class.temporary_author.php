<?php

class temporary_author
{
	private $_id;
	private $_letter;
	private $_author;
	private $_name_table;
	private $_n_id;
	private $_id_array;
	private $_author_array;


	public function create_temp_table ()
	{	
		$table=$this->getName_table();
	
		$drop_table ="DROP TABLE $table";
		$query = mysql_query($drop_table);
		
		$creatable=	"CREATE TABLE IF NOT EXISTS $table (
					   id int(4) NOT NULL AUTO_INCREMENT,
					   letter varchar(3),
					   author varchar(200),
					   PRIMARY KEY (id));";
		$query = mysql_query($creatable);
	}

	public function insert_temporary($letter, $author)
	{
	set_magic_quotes_runtime(0);
		if (get_magic_quotes_gpc()) {
        	$author = stripslashes($author);    
    	}
		$author= mysql_real_escape_string($author);
		$table=$this->getName_table();
			
		$query_i = "INSERT INTO $table (id, letter, author) VALUES (NULL, '$letter', '$author')";
		$rs2 = mysql_query($query_i);	
	}

	public function update_temporary($letter, $author, $flag, $id)
	{
	set_magic_quotes_runtime(0);
	
    	if (get_magic_quotes_gpc()) {
        	$author = stripslashes($author);    
    	}
    
		$author= mysql_real_escape_string($author);	
		$table=$this->getName_table();
	
		if ($flag == 1) // Update letter:
		{
			$query_i = "UPDATE $table SET letter = '$letter', author = '$author' WHERE id='$id'";				
		}
		if ($flag == 2) // Update author:
		{
			$query_i = "UPDATE $table SET author = '$author' WHERE id='$id'";	
		}
		$rs2 = mysql_query($query_i);
	}


	public function retrieve_id()
	{
		$table=$this->getName_table();
	
		$query = "SELECT id, author FROM $table";
		$rs = mysql_query($query);
		$n = 0;
		while(list($id, $author) = mysql_fetch_row($rs))
		{
			$this->setID_array($id, $n);
			$this->setAuthor_array($author, $n);
			$n = $n + 1;
		}
		$this->setN_id($n);
	}

	public function retrieve_letter_from_id($id)
	{
		$table=$this->getName_table();
	
		$query = "SELECT letter FROM $table WHERE id='$id'";
		$rs = mysql_query($query);
		while(list($var) = mysql_fetch_row($rs))
		{
			$this->setLetter($var);
		}
	}

	public function retrieve_author_from_id($id)
	{
		$table=$this->getName_table();
	
		$query = "SELECT author FROM $table WHERE id='$id'";
		$rs = mysql_query($query);
		while(list($var) = mysql_fetch_row($rs))
		{
			$this->setAuthor($var);
		}
	}
	
	public function remove($id)
	{
		$table=$this->getName_table();
	
		$query = "DELETE FROM $table WHERE id='$id'";
		$rs = mysql_query($query);
	}
	
		
	// SET -------------------------------------	
 	public function setName_table($var)
    {
		  $this->_name_table = $var;
    }	
		
 	public function setN_id($var)
    {
		  $this->_n_id = $var;
    }	

 	public function setID_array($var, $n)
    {
		  $this->_id_array[$n] = $var;
    }	

 	public function setAuthor_array($var, $n)
    {
		  $this->_author_array[$n] = $var;
    }	
	
 	public function setLetter($var)
    {
		  $this->_letter = $var;
    }	

 	public function setAuthor($var)
    {
		  $this->_author = $var;
    }	
	
	// GET ++++++++++++++++++++++++++++++++++++++	
    public function getName_table()
    {
    	return $this->_name_table;
    }	
		
    public function getID()
    {
    	return $this->_id;
    }

    public function getLetter()
    {
    	return $this->_letter;
    }

    public function getAuthor()
    {
    	return $this->_author;
    }

    public function getN_id()
    {
    	return $this->_n_id;
    }

    public function getID_array($i)
    {
    	return $this->_id_array[$i];
    }
	
    public function getAuthor_array($i)
    {
    	return $this->_author_array[$i];
    }	
	public function getCanonical_author($author)
    {
    	$author = substr($author, 0,(strpos($author, ' ')+2)); //1st parameter string,2nd parameter starting index ,3rd parameter total length after the space
		$author= mysql_real_escape_string($author);
		$query_canonical="SELECT name FROM Author WHERE name LIKE '$author%'"; //Bring all the authors with first name and first initial of the last name same.
		$rs = mysql_query($query_canonical);	
		$i=0;				
		while(list($author_name) = mysql_fetch_row($rs))	
		{	
			$canonical[$i]=$author_name;
			$i++;
		}
		return $canonical;
    }
    
	
}
?>

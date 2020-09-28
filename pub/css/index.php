<?php
/*
Ref: http://net.tutsplus.com/tutorials/html-css-techniques/how-to-add-variables-to-your-css-files/
*/
$cssFile = 'app.css';
if (isset($cssFile)) 
{
	
    $oCss = new EnhancedCss($cssFile);
	$oCss->display(); 
}

class EnhancedCss
{
	function __construct($cssFile)
	{
		if (!file_exists($cssFile)) 
		{
	        //header('HTTP/1.0 404 Not Found');
	        print 'File not found';
	        exit;
	    }
	    
	    $this->cssFile = $cssFile;
	}
	

	private function parse()
	{
	    $content = '';
	    $lines = file($this->cssFile);
	    foreach($lines as $line)
	    { 
	        $content .= $this->findAndReplaceVars($line); 
	    }
	    return $content;
	}
	
	private function findAndReplaceVars($line)
    {
	    preg_match_all('/\s*\\$([A-Za-z1-9_\-]+)(\s*:\s*(.*?);)?\s*/', $line, $vars); 
	    $found     = $vars[0];
	    $varNames  = $vars[1];
	    $varValues = $vars[3];
	    $count     = count($found);    
	                    
	    for($i = 0; $i < $count; $i++)
	    {
	        $varName  = trim($varNames[$i]);
	        $varValue = trim($varValues[$i]);            
	        if ($varValue)
	        {
	            $this->values[$varName] = $this->findAndReplaceVars($varValue);
	        } 
	        else if (isset($this->values[$varName])) 
	        { 
	            $line = preg_replace('/\\$'.$varName.'(\W|\z)/', $this->values[$varName].'\\1', $line);
	        }
	    }
	    $line = str_replace($found, '', $line);
	    return $line;
	}
	
	public function display()
    {        
    	header('Content-type: text/css'); 
    	echo $this->parse();
	}	
	
}
?>

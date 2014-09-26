<?php	// API/(README.GET).AJAX.PHP	// Returns the contents of a file
	// =========================	///whose name is in $_GET['file'],
	//				///with HTML specific characters < >
	//   (c) C Lester 2013,2014	///and & turned into &lt; &gt; and
	//				///&amp;, respectively
    
    function leftPad($n)
      { if ($n<10)    return "    $n:  ";
        if ($n<100)   return  "   $n:  ";
        if ($n<1000)  return   "  $n:  ";
        if ($n<10000) return    " $n:  ";
	return                   "$n:  "; }

    if (!isset($_GET['file']))
      { echo "<span style='color:red'><b>README.GET needs a parameter.</b></span>";
        exit; }

    $filename = $_GET['file'];
    $filename = '../'.$_GET['file'];

    if (DEBUG)
	echo "Raw filename is $filename<br>----------------===================<br><br>";

    $filename = str_replace('%20',' ',$filename);
    $filename = str_replace('%23','#',$filename);
    $filename = 'file:///'.getcwd().'/'.$filename;

    if (DEBUG)
	echo "Corrected filename is $filename<br>----------------------====================<br><br>";

    if (!file_exists($filename))
      { echo "<span style='color:red'><b>$filename does not exist.</b></span>";
        exit; }

    $file = FILE($filename);
    
    for ($i=0; $i<count($file); $i++)
      {	$line = $file[$i];
        $line = str_replace('&','&amp;',$line);
        $line = str_replace('<','&lt;',$line);
        $line = str_replace('>','&gt;',$line);
	echo leftPad($i).$line; }
    
?>

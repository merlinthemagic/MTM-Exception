<?php
//© 2019 Martin Peter Madsen
namespace MTM\Exception\Tools;

class Display
{
	//limit the output to x chars
	private $_maxArgChars=1000;
	
	public function formatUncaught($bool, $format="std")
	{
		if ($bool === true) {
			if ($format == "std") {
				set_exception_handler(array($this, "standard"));
			} else {
				throw new \Exception("Unknown Format: " . $format);
			}
		} else {
			//TODO:
			throw new \Exception("Not able to disable just yet");
		}
	}
	public function standard($e)
	{
		echo "<center><font color=\"#FF0000\"><h3>Class: ".get_class($e)."</h3></font></center>";
		echo '<table border=1 width="100%">';
		
		echo "<tr>";
		echo "<th align=\"center\" colspan=\"4\">----- Detail -----</th>";
		echo "</tr>";
		echo "<tr>";
		echo "<th colspan=\"1\">Info</th><th colspan=\"3\">Value</th>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<tr><td colspan=\"1\">Message</td><td colspan=\"3\">" . $e->getMessage() . "</td></tr>";
		echo "<tr><td colspan=\"1\">File:Line</td><td colspan=\"3\">" . $e->getFile() . ":" . $e->getLine() . "</td></tr>";
		echo "<tr><td colspan=\"1\">Code</td><td colspan=\"3\">" . $e->getCode() . "</td></tr>";
		
		//will be filled by JS functions
		echo "<tr><td colspan=\"1\">Server Time</td><td colspan=\"3\"><div id=\"serverTime\"></div></td></tr>";
		echo "<tr><td colspan=\"1\">Client Time</td><td colspan=\"3\"><div id=\"clientTime\"></div></td></tr>";
		echo "<tr><td colspan=\"1\">Client IP</td><td colspan=\"3\">".$_SERVER["REMOTE_ADDR"]."</td></tr>";
		
		if (defined('MTM_LOADED_TIME') === true) {
			echo "<tr><td colspan=\"1\">Run Time</td><td colspan=\"3\">".(\MTM\Utilities\Factories::getTime()->getMicroEpoch() - MTM_LOADED_TIME)."</td></tr>";
		} else {
			echo "<tr><td colspan=\"1\">Run Time</td><td colspan=\"3\">N/A</td></tr>";
		}

		echo "<tr>";
		echo "<th align=\"center\" colspan=\"4\">----- Trace -----</th>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<th>Class</th><th>Method</th><th>File</th><th>Line#</th>";
		echo "</tr>";
		
		foreach ($e->getTrace() as $tItem) {
			echo "<tr>";
			
			$file	= "N/A";
			if (isset($tItem["file"]) === true) {
			    $file	= $tItem["file"];
			}
			
			$line	= "N/A";
			if (isset($tItem["line"]) === true) {
			    $line	= $tItem["line"];
			}
			
			$class	= "N/A";
			if (isset($tItem["class"]) === true) {
				$class	= $tItem["class"];
			}
			$function	= "N/A";
			if (isset($tItem["function"]) === true) {
				$function	= $tItem["function"];
			}
			
			echo "<td>" . $class. "</td><td>" . $function;
			
			$argLine	= "";
			if (isset($tItem["args"]) === true) {
				foreach ($tItem["args"] as $argId => $arg) {
	
					$color	= null;
					if (is_bool($arg) === true) {
						
						$color	= "#800020";
						if ($arg === true) {
							$arg	= "<b>true</b>";
						} elseif ($arg === false) {
							$arg	= "<b>false</b>";
						}
						
					} elseif ($arg === null) {
						$color	= "#800020";
						$arg	= "null";
					} elseif (is_object($arg) === true) {
						$color	= "#00AA00";
						$arg	= get_class($arg);
					} elseif (is_array($arg) === true) {
						$color		= "#CC0000";
						$indexCount	= count($arg);
						$arg		= "array([".$indexCount."])";
					} elseif (is_numeric($arg) === true) {
						//nothing to add for numbers
					} elseif (is_string($arg) === true) {
						//encapsulate string
						$arg	= "'" . substr($arg, 0, $this->_maxArgChars) . "'";
					}
					
					//add attributes
					if ($argId > 0) {
						$argLine	.= ", ";
					}
					if ($color !== null) {
						$argLine	.= "<font color=\"".$color."\">";
					}
					
					//add the argument
					$argLine	.= $arg;
					
					//close attributes
					if ($color !== null) {
						$argLine	.= "</font>";
					}
				}
			}
			
			echo "(" .$argLine. ")";
			echo "</td>";
			
			echo "<td>";
			echo $file;
			echo "</td>";
			echo "<td>";
			echo $line;
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
		
		//scripts
		echo "<script>";
		
		echo "function getTime(d) {";
		
		echo "	function pad(n){return n<10 ? '0'+n : n};";
		echo "	return d.getUTCFullYear()+'-'";
		echo "		+ pad(d.getUTCMonth()+1)+'-'";
		echo "		+ pad(d.getUTCDate()) +' '";
		echo "		+ pad(d.getUTCHours())+':'";
		echo "		+ pad(d.getUTCMinutes())+':'";
		echo "		+ pad(d.getUTCSeconds())";
		echo "}";
		
		echo "	var curTime = new Date();";
		
		echo "	var clientEpoch		= Math.ceil(curTime.getTime() / 1000);";
		echo "	var serverEpoch		= ".time().";";
		echo "	var serverToClient	= (serverEpoch - clientEpoch);";
		
		echo "if (serverToClient < 0) {";
		echo "	var sTocText	= 'Ahead of Server by: ' + Math.abs(serverToClient) + ' seconds';";
		echo "} else if (serverToClient > 0) {";
		echo "	var sTocText	= 'Behind Server by: ' + Math.abs(serverToClient) + ' seconds';";
		echo "} else {";
		echo "	var sTocText	= 'Matches server';";
		echo "}";

		echo "document.getElementById(\"serverTime\").innerHTML		= '".date("Y-m-d H:i:s")."' + ' -- Epoch: ' + serverEpoch;";
		echo "document.getElementById(\"clientTime\").innerHTML		= getTime(curTime) + ' -- ' + sTocText;";
		
		echo "</script>";
	}
}
<?php

class csvReader
{
	public function read($path) {
		$fileContent = file_get_contents($path);
		
		$rows = str_getcsv($fileContent, "\n");		
		$header = str_getcsv($rows[0]);
		$rows = array_slice($rows, 1);
		
		$csv = array();
		
		$rowIndex = 0;
		$colIndex = 0;
		
		foreach ($rows as $row) {
			if (empty($row)) {
				continue;
			}
			$fields = str_getcsv($row);
			$csvRow = array();
			$colIndex = 0;
			
			foreach ($header as $heading) {				
				$csvRow[$heading] = $fields[$colIndex];
				
				$colIndex++;
			}
			
			$csv[] = $csvRow;
			
			$rowIndex++;
		}
		
		return $csv;
	}
	
	public function read_callback($path, $callback, stdClass $extraParameters) {		
		$fileContent = file_get_contents($path);
		
		$rows = str_getcsv($fileContent, "\n");		
		$header = str_getcsv($rows[0]);
		$rows = array_slice($rows, 1);
				
		$rowIndex = 0;
		$colIndex = 0;
		
		foreach ($rows as $row) {
			if (empty($row)) {
				continue;
			}
			$fields = str_getcsv($row);
			$csvRow = array();
			$colIndex = 0;
			
			foreach ($header as $heading) {				
				$csvRow[$heading] = $fields[$colIndex];
				
				$colIndex++;
			}
			
			call_user_func_array($callback, array($csvRow, $extraParameters));
			
			$rowIndex++;
		}
	}
}

?>

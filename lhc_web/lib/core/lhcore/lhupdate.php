<?php

class erLhcoreClassUpdate
{
	const DB_VERSION = 112;
	const LHC_RELEASE = 237;
		
	public static function doTablesUpdate($definition){
		$updateInformation = self::getTablesStatus($definition);
		$db = ezcDbInstance::get();
		
		$errorMessages = array();
		
		foreach ($updateInformation as $table => $tableData) {
			if ($tableData['error'] == true) {
				foreach ($tableData['queries'] as $query) {
					try {
						$db->query($query);
					} catch (Exception $e) {
						$errorMessages[] = $e->getMessage();
					}
				}
			}
		}
		
		return $errorMessages;		
	}
	
	public static function getTablesStatus($definition){
		$db = ezcDbInstance::get();
		
		$tablesStatus = array();		
		foreach ($definition['tables'] as $table => $tableDefinition) {
			$tablesStatus[$table] = array('error' => false,'status' => '','queries' => array());
			try {
				$sql = 'SHOW COLUMNS FROM '.$table;
				$stmt = $db->prepare($sql);
				$stmt->execute();
				$columnsData = $stmt->fetchAll(PDO::FETCH_ASSOC);				
				$columnsDesired = (array)$tableDefinition;
				
				$status = array();
				
				foreach ($columnsDesired as $columnDesired) {
					$columnFound = false;
					$typeMatch = true;
					foreach ($columnsData as $column) {
						if ($columnDesired['field'] == $column['field']) {
							$columnFound = true;
							
							if ($columnDesired['type'] != $column['type']) {
								$typeMatch = false;
							}
						}	
					}

					if ($typeMatch == false) {
						$tablesStatus[$table]['error'] = true;
						$status[] = "[{$columnDesired['field']}] column type is not correct";

						$extra = '';
						if ($columnDesired['extra'] == 'auto_increment') {
						    $extra = ' AUTO_INCREMENT';
						}
						
						$tablesStatus[$table]['queries'][] = "ALTER TABLE `{$table}`
						CHANGE `{$columnDesired['field']}` `{$columnDesired['field']}` {$columnDesired['type']} NOT NULL{$extra};";
					}
					
					if ($columnFound == false) {
						$tablesStatus[$table]['error'] = true;
						$status[] = "[{$columnDesired['field']}] column was not found";
						
						$default = '';
						if ($columnDesired['default'] != null){
							$default = " DEFAULT '{$columnDesired['default']}'";
						}
								
						$tablesStatus[$table]['queries'][] = "ALTER TABLE `{$table}`
						ADD `{$columnDesired['field']}` {$columnDesired['type']} NOT NULL{$default},
						COMMENT='';";
					}					
				}
				
				if (!empty($status)) {
					$tablesStatus[$table]['status'] = implode(", ", $status);
					$tablesStatus[$table]['error'] = true;
				}
								
			} catch (Exception $e) {
				$tablesStatus[$table]['error'] = true;
				$tablesStatus[$table]['status'] = "table does not exists";
				$tablesStatus[$table]['queries'][] = $definition['tables_create'][$table];
			}			
		}
				
		foreach ($definition['tables_indexes'] as $table => $dataTableIndex) {		    
		    try {
    		    $sql = 'SHOW INDEX FROM '.$table;
    		    $stmt = $db->prepare($sql);
    		    $stmt->execute();
    		    $columnsData = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    		    $status = array();
    		    
    		    $existingIndexes = array();
    		    foreach ($columnsData as $indexData) {
    		        $existingIndexes[] = $indexData['key_name'];
    		    }
    		    
    		    $existingIndexes = array_unique($existingIndexes);
    		    
    		    $newIndexes = array_diff(array_keys($dataTableIndex['new']), $existingIndexes);
    		    
    		    foreach ($newIndexes as $newIndex) {
    		        $tablesStatus[$table]['queries'][] = $dataTableIndex['new'][$newIndex];
    		        $status[] = "{$newIndex} index was not found";
    		    }
    		    
    		    $removeIndexes = array_intersect($dataTableIndex['old'], $existingIndexes);
    		   
    		    foreach ($removeIndexes as $removeIndex) {
    		        $tablesStatus[$table]['queries'][] = "ALTER TABLE `{$table}` DROP INDEX `{$removeIndex}`;";
    		        $tablesStatus[$table]['error'] = true;
    		        $status[] = "{$removeIndex} legacy index was found";
    		    }
    		    
    		    if (!empty($status)) {
    		        $tablesStatus[$table]['status'] = implode(", ", $status);
    		        $tablesStatus[$table]['error'] = true;
    		    }
    		    
		    } catch (Exception $e) {
		        // Just not existing table perhaps
		    }	    
		}
				
		foreach ($definition['tables_data'] as $table => $dataTable) {
			$tableIdentifier = $definition['tables_data_identifier'][$table];
			
			$status = array();
			// Check that table has all required records
			foreach ($dataTable as $record) {	

				try {
					$sql = "SELECT COUNT(*) as total_records FROM `{$table}` WHERE `{$tableIdentifier}` = :identifier_value";				
					$stmt = $db->prepare($sql);
					$stmt->bindValue(':identifier_value',$record[$tableIdentifier]);
					$stmt->execute();
					$columnsData = $stmt->fetchColumn();
					if ($columnsData == 0){
						$status[] = "Record with identifier {$tableIdentifier} = {$record[$tableIdentifier]} was not found";
						
						$columns = array();
						$values = array();
						foreach ($record as $column => $value) {
							$columns[] = '`' . $column . '`';
							$values[] = $db->quote($value);
						}					
						$tablesStatus[$table]['queries'][] = "INSERT INTO `{$table}` (".implode(',', $columns).") VALUES (".implode(',', $values).")";					
					}
				} catch (Exception $e) {
					
					$status[] = "Record with identifier {$tableIdentifier} = {$record[$tableIdentifier]} was not found";
					
					$columns = array();
					$values = array();
					foreach ($record as $column => $value) {
						$columns[] = '`' . $column . '`';
						$values[] = $db->quote($value);
					}
					$tablesStatus[$table]['queries'][] = "INSERT INTO `{$table}` (".implode(',', $columns).") VALUES (".implode(',', $values).")";										
					// Perhaps table does not exists
				}			
			}
			
			if (!empty($status)){
				$tablesStatus[$table]['status'] .= implode(", ", $status);
				$tablesStatus[$table]['error'] = true;
			}
		}
		
		return $tablesStatus;
	}
}

?>
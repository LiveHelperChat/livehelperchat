<?php

class erLhcoreClassUpdate
{
	const DB_VERSION = 83;
	const LHC_RELEASE = 204;
		
	public static function doTablesUpdate($definition){
		$updateInformation = self::getTablesStatus($definition);
		$db = ezcDbInstance::get();
		
		foreach ($updateInformation as $table => $tableData) {
			if ($tableData['error'] == true) {
				foreach ($tableData['queries'] as $query) {
					try {
						$db->query($query);
					} catch (Exception $e) {
						
					}
				}
			}
		}
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
					foreach ($columnsData as $column) {
						if ($columnDesired['field'] == $column['field']){
							$columnFound = true;
						}						
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
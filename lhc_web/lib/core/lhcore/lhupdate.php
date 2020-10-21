<?php

class erLhcoreClassUpdate
{
	const DB_VERSION = 222;
	const LHC_RELEASE = 349;

	public static function doTablesUpdate($definition){
		$updateInformation = self::getTablesStatus($definition);
		$db = ezcDbInstance::get();

        $errorMessages = array();

		try {
            $db->query('SET GLOBAL innodb_file_per_table=1;');
            $db->query('SET GLOBAL innodb_large_prefix=1;');
        } catch (Exception $e) {
            //$errorMessages[] = $e->getMessage();
        }

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
		
		// Get archive tables		
		$archives = erLhcoreClassModelChatArchiveRange::getList(array('ignore_fields' => array('year_month','range_from','range_to','older_than','last_id','first_id'),'offset' => 0, 'limit' => 1000000,'sort' => 'id ASC'));
			
		if (isset($definition['tables']['lh_chat']) && isset($definition['tables']['lh_msg']))
		{
    		// Update archives tables also
    		foreach ($archives as $archive) {
    		    $archive->setTables();
    		    $definition['tables'][erLhcoreClassModelChatArchiveRange::$archiveTable] = $definition['tables']['lh_chat'];
    		    $definition['tables'][erLhcoreClassModelChatArchiveRange::$archiveMsgTable] = $definition['tables']['lh_msg'];
    		}
		}

		if (isset($definition['tables_collation'])){
            foreach ($definition['tables_collation'] as $table => $dataTableCollation) {
                $tablesStatus[$table] = array('error' => false, 'status' => '', 'queries' => array());
                try {
                    $stmt = $db->prepare("show table status like '{$table}'");
                    $stmt->execute();
                    $tableData = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!empty($tableData) && $tableData['collation'] != $dataTableCollation) {
                        $tablesStatus[$table]['queries'][] = "ALTER TABLE `{$table}` COMMENT='' COLLATE '{$dataTableCollation}';";
                        $tablesStatus[$table]['error'] = true;
                        $tablesStatus[$table]['status'] = "{$table} collation {$tableData['collation']} mismatch expected {$dataTableCollation}";
                    }

                } catch (Exception $e) {
                    // Just not existing table perhaps
                }
            }
        }

		foreach ($definition['tables'] as $table => $tableDefinition) {

		    if (!isset( $tablesStatus[$table])) {
                $tablesStatus[$table] = array('error' => false, 'status' => '', 'queries' => array());
            }

			try {
				$sql = 'SHOW FULL COLUMNS FROM '.$table;
				$stmt = $db->prepare($sql);
				$stmt->execute();
				$columnsData = $stmt->fetchAll(PDO::FETCH_ASSOC);				
				$columnsDesired = (array)$tableDefinition;

				$status = array();
				$fieldsHandled = array();
				$existingColumns = array();
				
				foreach ($columnsData as $column) {
				    $existingColumns[] = $column['field'];
				}

				foreach ($columnsData as $column) {
					if (isset($definition['tables_alter'][$table][$column['field']])) {
					    
					    if (!in_array($definition['tables_alter'][$table][$column['field']]['new'], $existingColumns)) {
    						$status[] = '['.$column['field'] . "] field will be renamed";
    						$tablesStatus[$table]['queries'][] = $definition['tables_alter'][$table][$column['field']]['sql'];
    						$fieldsHandled[] = $definition['tables_alter'][$table][$column['field']]['new'];
					    } else {
					        $status[] = '['.$column['field'] . "] field will be dropped";
					        $tablesStatus[$table]['queries'][] = "ALTER TABLE `{$table}` DROP `{$column['field']}`,COMMENT=''";
					    }
					}

					if (isset($definition['tables_drop_column'][$table])) {
					    if (in_array($column['field'], $definition['tables_drop_column'][$table])) {
    						$status[] = '['.$column['field'] . "] field will be dropped";
                            $tablesStatus[$table]['queries'][] = "ALTER TABLE `{$table}` DROP `{$column['field']}`,COMMENT=''";
					    }
					}
				}

				$tableDataChanged = false;
				$queriesChangeType = array();

				foreach ($columnsDesired as $columnDesired) {
					$columnFound = false;
					$typeMatch = true;
					$collationMatch = true;
					foreach ($columnsData as $column) {
						if ($columnDesired['field'] == $column['field']) {
							$columnFound = true;

                            if (($columnDesired['type'] != $column['type'] && strpos($column['type'],'(') !== false) || (strpos($column['type'],'(') === false && explode('(',$columnDesired['type'])[0] != explode(' ',$column['type'])[0])) {
								$typeMatch = false;
							}

							if ($columnDesired['default'] != $column['default']) {
								$typeMatch = false;
							}

							if ($column['collation'] != '' && isset($columnDesired['collation']) && $columnDesired['collation'] != $column['collation']) {
                                $typeMatch = $collationMatch = false;
							}
						}


					}

					if ($typeMatch == false) {
                        $tableDataChanged = true;

						$tablesStatus[$table]['error'] = true;
						$status[] = "[{$columnDesired['field']}] column type/collation is not correct";

						$extra = '';
						if ($columnDesired['extra'] == 'auto_increment') {
						    $extra = ' AUTO_INCREMENT';
						} elseif ($columnDesired['default'] !== null) {
                            $extra = " DEFAULT '{$columnDesired['default']}'";
                        }

						$collation = '';
                        if ($collationMatch == false) {
                            $collation = " COLLATE '".$columnDesired['collation']."' ";
                        }

                        $queriesChangeType[] = "CHANGE `{$columnDesired['field']}` `{$columnDesired['field']}` {$columnDesired['type']}{$collation} NOT NULL{$extra}";
					}
					
					if ($columnFound == false && !in_array($columnDesired['field'], $fieldsHandled)) {
						
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

				if ($tableDataChanged == true) {
                    $tablesStatus[$table]['queries'][] = "ALTER TABLE `{$table}` " . implode(', ', $queriesChangeType) . ';';
                }
				
				if (!empty($status)) {
					$tablesStatus[$table]['status'] = implode(", ", $status);
					$tablesStatus[$table]['error'] = true;
				}
								
			} catch (Exception $e) {
		        if (isset($definition['tables_create'][$table]))
                {
                    $tablesStatus[$table]['error'] = true;
                    $tablesStatus[$table]['status'] = "table does not exists";
                    $tablesStatus[$table]['queries'][] = $definition['tables_create'][$table];
                }
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
                    array_unshift($tablesStatus[$table]['queries'],"ALTER TABLE `{$table}` DROP INDEX `{$removeIndex}`;");
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
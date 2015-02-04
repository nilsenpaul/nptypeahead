<?php
namespace Craft;

class NpTypeAhead_DataService extends BaseApplicationComponent
{
	private $results = array();
	
    public function getValues($fieldName, $settings)
    {
    	// Reset results
    	$this->results = array();
    	
    	// Get fields by ID
        if(!empty($settings->getValuesFrom)) {
        	// Get all plain text fields from DB
        	$fields = craft()->db->createCommand()
        		->select('handle')
        		->from('fields')
        		->where('id IN('.implode(', ', $settings->getValuesFrom).')')
        		->queryAll();
        } else {
        	return '[]';
        }

        // Make array containing all field handles
        $fieldHandles = array_map(array($this, 'getHandle'), $fields);
        
        // Get all field content and iterate through to find the fields we need. There must be a better way, though.
        $fieldContent = craft()->db->createCommand()
       		->selectDistinct(implode(', ', $fieldHandles))
       		->from('content')
       		->queryAll();
        
        foreach($fieldContent as $fields) {
        	foreach($fields as $fieldKey => $fieldValue) {
        		if($fieldValue) {
        			$this->results[] = $fieldValue;
        		}
        	}
        }
        
		return json_encode($this->results);
    }
    
    public function getHandle($a)
    {
    	return 'field_'.$a['handle'];
    }
}
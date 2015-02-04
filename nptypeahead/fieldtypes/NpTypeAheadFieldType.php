<?php
namespace Craft;

/**
 * NP TypeAhead field type
 */
class NpTypeAheadFieldType extends BaseFieldType
{
    public function getName()
    {
        return Craft::t('Plain Text w/typeahead');
    }

    protected function defineSettings()
    {
        return array(
            'getValuesFrom' => array(AttributeType::Mixed),
            'minLength' => array(AttributeType::Number),
        );
    }

    public function getSettingsHtml()
    {
    	$allFields = craft()->fields->getAllFields();
    	
    	foreach($allFields as $field) {
	    	if(in_array($field->type, array('PlainText', 'NpTypeAhead'))) {
	    		$fields[] = array(
	    			'value' => $field->id,
	    			'label' => $field->name.' ('.$field->handle.')',
	    		);
	    	}
	    }
	    
        return craft()->templates->render('nptypeahead/settings', array(
        	'fields' => $fields,
            'settings' => $this->getSettings()
        ));
    }

    public function getInputHtml($name, $value)
    {
        // Reformat the input name into something that looks more like an ID
        $id = craft()->templates->formatInputId($name);
        $namespacedId = craft()->templates->namespaceInputId($id);

        $settings = $this->getSettings();
        $values = craft()->npTypeAhead_data->getValues($name, $settings);
        $minLength = $settings->minLength !== null ? $settings->minLength : 0;
        
        // Include our Javascript
        craft()->templates->includeCssResource('nptypeahead/css/input.css');
        craft()->templates->includeJsResource('nptypeahead/js/typeahead.bundle.js');
        craft()->templates->includeJsResource('nptypeahead/js/input.js');
        craft()->templates->includeJs("var {$id}_data = ".$values);
        craft()->templates->includeJs("$('#{$namespacedId}').typeahead({
        	hint: false,
        	highlight: true,
        	minLength: {$minLength},
        },
        {
        	name: 'fieldValues',
        	displayKey: 'value',
    	  	source: substringMatcher({$id}_data)
        });");

        $class  = 'nptypeahead-field';

        return craft()->templates->render('nptypeahead/input', array(
            'id'    => $id,
            'name'  => $name,
            'value' => $value,
            'class' => $class,
        ));
    }

    public function prepValueFromPost($value)
    {
        return $value;
    }
}
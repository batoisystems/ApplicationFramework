<?php
require_once (dirname(__FILE__).'/sanitize.cls.php');
class OSF_Form extends ArrayIterator
{
    private $_aFormData;                       // Form data
    private $_aFormValidation;                 // Validation Rules
    private $_aErrors = array();               // All the validation arrays
    private $_aCurrentElement = array();       // Current element, which will be returned on each iteration
    private $_iCurrentIndex;                   // Current array index
    private $_bValid;                          // Whether current element is valid or not
    private $_aFormValidationRules = array();
    
    public function __construct(&$aFormDefinition, $aOptionalValidationRules = null) // Class constructor
    {
    	$aFormData = $_POST;
    	//make sure we include missing fields
    	foreach($aFormDefinition as $sFieldName => $aValidationRules)
    	{
    		if(!array_key_exists($sFieldName, $aFormData)) $aFormData[$sFieldName] = '';
    	}
        parent::__construct($aFormData);
        $this->_formValidationArray = $aFormDefinition;
        $this->_initValidationRules($aOptionalValidationRules);
    }


    /**
     * Return the current element
     * Sanitizes and validates each filed before returning it
     * 
     * Similar to the current() function for arrays.
     * Implement Iterator::current()
     *
     * @return  string
     * @see   Iterator::current()
     */
    public function current()
    {
        $this->_currentElement['original_field_value'] = parent::current();
        $this->_currentElement['sanitized_field_value'] = $this->_sanitize();
        $error = $this->_validate();
        if(empty($error)) $error = false;
        $this->_currentElement['field_error_message'] = $error;
        $this->_currentElement['field_name'] = $this->key();
        return $this->_currentElement;
    }

    /**
     * Return the identifying key of the current element
     * Similar to the key() function for arrays.
     * Implement Iterator::key()
     *
     * @return  int
     * @see     Iterator::key()
     */
    public function key()
    {
        return parent::key();
    }

    /**
     * Move forward to next element
     * Similar to the next() function for arrays.
     * Implement Iterator::next()
     *
     * @return  void
     * @throws  Exception
     * @see     Iterator::next()
     */
    public function next()
    {
        parent::next();
    }

    /**
     * Rewind the Iterator to the first element
     * Similar to the reset() function for arrays.
     * Implement Iterator::rewind()
     *
     * @return  void
     * @see     Iterator::rewind()
     */
    public function rewind()
    {
        parent::rewind();
    }

    /**
     * Check if there is a current element after calls to
     * rewind() or next().
     * Used to check if we've iterated to the end of the collection.
     *
     * This method checks if the next row is a valid row.
     *
     * @return   bool    FALSE if there's nothing more to iterate over.
     */
    public function valid()
    {
        return parent::valid();
    }

    /**
     * Sanitizes fields
     * If enabled sanitizes the field using cake_sanitize.php
     *
     * @return   string  sanitized field.
     */
    private function _sanitize() 
    {
        $sFieldName = $this->key();
        $sFieldValue = $this->_currentElement['original_field_value'];
        $this->_trim($sFieldValue);

        if(array_key_exists($sFieldName, $this->_formValidationArray))
        {
            if( (isset($this->_formValidationArray[$sFieldName]['sanitize'])) && ($this->_formValidationArray[$sFieldName]['sanitize'] == 1) )
            {
                $sanitizationType = $this->_formValidationArray[$sFieldName]['sanitize_type'];
                if(method_exists('Sanitize', $sanitizationType))
                {
                	$sFieldValue = call_user_func(array('Sanitize', $sanitizationType), $sFieldValue);
                }
            }
        }
        return $sFieldValue;
    }

    /**
     * Validates fields 
     *
     * If enabled validates the filed based on rulkes.
     * 
     * @return   string  sanitized field.
     */
    private function _validate()
    {

        $sFieldValue = $this->_currentElement['sanitized_field_value'];
        $sFieldName = $this->key();
        if(!array_key_exists($sFieldName, $this->_formValidationArray))
        {
            return false;
        } else {
            if( (isset($this->_formValidationArray[$sFieldName]['validate'])) && ($this->_formValidationArray[$sFieldName]['validate'] == 0) )
            {
                return false;
            }
            elseif( (isset($this->_formValidationArray[$sFieldName]['validate'])) && ($this->_formValidationArray[$sFieldName]['validate'] == 1) )
            {
                $validationType = (isset($this->_formValidationArray[$sFieldName]['validation_type'])) ? $this->_formValidationArray[$sFieldName]['validation_type'] : '';
                $validationError = (isset($this->_formValidationArray[$sFieldName]['error_message'])) ? $this->_formValidationArray[$sFieldName]['error_message'] : '';
            }
        }
        $error = '';
        $validationType = (isset($validationType)) ? $validationType : '';

        switch($validationType)
        {
                //Using your regex
            case "reg_exp":
                $this->_formValidationRulesArray['reg_exp'] = $this->_formValidationArray[$sFieldName]['reg_exp'];
                $pattern = $this->_formValidationRulesArray[$validationType];
                if(!preg_match($pattern, $sFieldValue)) 
                {
                    $error = $this->_formValidationArray[$sFieldName]['error_message'];
                }
                break;
                //this matches the value you have specified
            case "checkbox":
                if($sFieldValue != $this->_formValidationArray[$sFieldName]['values'])
                {
                    $error = $this->_formValidationArray[$sFieldName]['error_message'];
                }
                break;

                //this tries to match one of the pipe delimited values
            case "radio":
                $valuesArray = explode('|', $this->_formValidationArray[$sFieldName]['values']);
                if(!in_array($sFieldValue, $valuesArray))
                {
                    $error = $this->_formValidationArray[$sFieldName]['error_message'];
                }
                break;

            //any of the predefined values
            //see _initValidationRule
			default:
                if(array_key_exists($validationType, $this->_formValidationRulesArray))
                {
                    $pattern = $this->_formValidationRulesArray[$validationType];
                    if(!preg_match($pattern, $sFieldValue))
                    {
                        $error = $this->_formValidationArray[$sFieldName]['error_message'];
                    }
                }
                break;
        }


        if(empty($error)) return false;
        else return $error;
    }

	/**
     * Initializes validation rules 
     * 
     * Initializes some known validation rules
     * You could also pass an optional validation array of rules
     * The format basically is name of the rule => regex
     * 
     * @param 	 array $aValidationRulesArray 
     * @return   none
     */
    private function _initValidationRules($aOptionalValidationRules)
    {
        $this->_formValidationRulesArray['numeric'] = '/^[0-9]+$/';
        $this->_formValidationRulesArray['alpha'] = '/^[a-zA-Z]+$/';
        $this->_formValidationRulesArray['alphanumeric'] = '/^[a-zA-Z0-9]+$/';
        $this->_formValidationRulesArray['email'] = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
        $this->_formValidationRulesArray['string'] = '/^[^"\r\n]+$/';
        $this->_formValidationRulesArray['empty'] = '/^.+$/';
        $this->_formValidationRulesArray['required'] = '/^.+$/';
        $this->_formValidationRulesArray['phone'] = '/^\(?[0-9]{3}\)?[\x20|\-|\.]?[0-9]{3}[\x20|\-|\.]?[0-9]{4}$/';
        $this->_formValidationRulesArray['currency'] = '/^[0-9]*\.?[0-9]+$/';
        $this->_formValidationRulesArray['zip'] = '/^[0-9]{5}$/';
        $this->_formValidationRulesArray['state_code'] = '/^[A-Za-z]{2}$/';

        if(is_array($aOptionalValidationRules) && !empty($aOptionalValidationRules))
        {
            $this->_formValidationRulesArray = array_merge($this->_formValidationRulesArray, $aOptionalValidationRules);
        }
    }

	/**
     * Initializes validation rules 
     * 
     * Initializes some known validation rules
     * You could also pass an optional validation array of rules
     * 
     * @param 	 array $aValidationRulesArray 
     * @return   none
     */
    private function _trim(&$aTrimData)
    {
        if(is_array($aTrimData)) $aTrimData = array_map('trim', $aTrimData);
        else $aTrimData = trim($aTrimData);
    }
    
    /* __destruct
    *-------------------------------------------*/
    function __destruct() {}
}
?>

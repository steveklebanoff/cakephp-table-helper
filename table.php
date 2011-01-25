<?php

class TableHelper extends AppHelper
{
    var $helpers = array('Html');
    
    private function _getField($defaultModelName, $entry, $entryDisplayField)
    {
        // Determine model names and field name
        $modelsAndFields = explode('.', $entryDisplayField);
        if ((sizeof($modelsAndFields)) == 1)
        {
            // Field doesn't have any model names in it (i.e. 'allows_pets') 
            $entryModelNames = array($defaultModelName);
            $entryField = $entryDisplayField;
        }
        else
        {
            // Field has model names (i.e. 'Property.Address.address1')
            $modelsAndFieldsLastItemIndex = sizeof($modelsAndFields) - 1;
            
            $entryModelNames = array_slice($modelsAndFields, 0, $modelsAndFieldsLastItemIndex );
            $entryField = $modelsAndFields[$modelsAndFieldsLastItemIndex];
        }
        
        // Get field
        $fieldToDisplay = $entry;
        foreach ($entryModelNames as $entryModelName)
        {
            $fieldToDisplay = $fieldToDisplay[$entryModelName];
        }
        return $fieldToDisplay[$entryField];
    }
    
    
    public function createTable($tableModelName, $tableEntries, $tableDisplayFields,
                                $tableActions = array(), $noItemsMessage = "There are
                                no items to display")
    {
        if (empty($tableEntries))
        {
            return $noItemsMessage;
        }
        
        // Create header
        $output = "<table>";
        $output .= "<tr>";
        foreach($tableDisplayFields as $tableDisplayFieldName => $tableDisplayField)
        {
            $output .= "<th>" . $tableDisplayFieldName . "</th>";
        }
        
        $hasActions = (!empty($tableActions));
        if ($hasActions)
        {
            $output .= "<th>Actions</th>";
        }
        $output .= "</tr>";
        
        // Create entries
        foreach ($tableEntries as $entry)
        {
            $output .= "<tr>";
            foreach($tableDisplayFields as $tableDisplayFieldName => $tableDisplayField)
            {
                $fieldToDisplay = $this->_getField($tableModelName, $entry, $tableDisplayField);
                $output .= "<td>" . $fieldToDisplay . "</td>";
            }
            
            if ($hasActions)
            {
                $output .= "<td>";
                foreach ($tableActions as $tableActionKey => $tableActionValue)
                {
                    list($actionName, $actionUrlPrefix,$actionUrlFieldName)  =
                        array($tableActionKey, $tableActionValue[0], $tableActionValue[1]);
                    
                    $actionConfirm = false;
                    if (isset($tableActionValue[2]))
                    {
                        $actionConfirm = $tableActionValue[2];
                    }
                    
                    $actionUrl = $actionUrlPrefix . $this->_getField($tableModelName,
                                                                     $entry,
                                                                     $actionUrlFieldName);
                    
                    $output .= $this->Html->link($actionName, $actionUrl, array(), $actionConfirm);
                    $output .= " ";
                    
                }
                $output .= "</td>";
            }
            $output .= "</tr>";
        }
        $output .= "</table>";
        
        return $output;
    }
}

?>

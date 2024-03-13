<?php

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getAttributesFromDca']['huhFormAutocomplete'] = [\HeimrichHannot\FormAutocompleteBundle\EventListener\AddAutocompleteValuesListener::class, 'onGetAttributesFromDca'];
$GLOBALS['TL_HOOKS']['loadFormField']['huhFormAutocomplete'] = [\HeimrichHannot\FormAutocompleteBundle\EventListener\AddAutocompleteValuesListener::class, 'onLoadFormField'];

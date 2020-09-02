<?php

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getAttributesFromDca']['huhFormAutocomplete'] = [\HeimrichHannot\FormAutocompleteBundle\EventListener\GetAttributesFromDcaListener::class, 'onGetAttributesFromDca'];

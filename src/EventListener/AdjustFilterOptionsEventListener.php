<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\FormAutocompleteBundle\EventListener;

use HeimrichHannot\FilterBundle\Event\AdjustFilterOptionsEvent;

class AdjustFilterOptionsEventListener
{
    public function __invoke(AdjustFilterOptionsEvent $event)
    {
        $options = $event->getOptions();
        $element = $event->getElement();
        $filterConfig = $event->getConfig();
        $filter = $filterConfig->getFilter();

        $field = $element->field ?: $element->name;
        $dcaField = $GLOBALS['TL_DCA'][$filter['dataContainer']]['fields'][$field] ?? null;

        if (!$dcaField || !isset($dcaField['eval']['autocomplete']) || !$dcaField['eval']['autocomplete']) {
            return;
        }

        $options['attr']['autocomplete'] = $dcaField['eval']['autocomplete'];

        $event->setOptions($options);
    }
}

<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\FormAutocompleteBundle\EventListener;

use Contao\DataContainer;
use Contao\Widget;
use HeimrichHannot\UtilsBundle\Container\ContainerUtil;
use HeimrichHannot\UtilsBundle\Dca\DcaUtil;

class AddAutocompleteValuesListener
{
    /**
     * @var array
     */
    protected $bundleConfig;

    /**
     * @var ContainerUtil
     */
    protected $containerUtil;

    /**
     * @var DcaUtil
     */
    protected $dcaUtil;

    protected static $mappingCache = [];

    public function __construct(array $bundleConfig, ContainerUtil $containerUtil, DcaUtil $dcaUtil)
    {
        $this->bundleConfig = $bundleConfig;
        $this->containerUtil = $containerUtil;
        $this->dcaUtil = $dcaUtil;
    }

    /**
     * @Hook("getAttributesFromDca")
     *
     * @param DataContainer $dc
     */
    public function onGetAttributesFromDca(array $attributes, $dc = null): array
    {
        if ($this->containerUtil->isBackend()) {
            return $attributes;
        }

        $autocompleteValue = $this->getAutocompleteValueFromField($attributes['name'], null !== $dc ? $dc->table : null);

        if (false !== $autocompleteValue) {
            $attributes['autocomplete'] = $autocompleteValue;
        }

        return $attributes;
    }

    /**
     * @Hook("loadFormField")
     */
    public function onLoadFormField(Widget $objWidget, string $formId, array $arrData, \Contao\Form $form): Widget
    {
        if ($this->containerUtil->isBackend() || null === $objWidget->name) {
            return $objWidget;
        }

        $autocompleteValue = $this->getAutocompleteValueFromField($objWidget->name);

        if (false !== $autocompleteValue) {
            $objWidget->addAttribute('autocomplete', $autocompleteValue);
        }

        return $objWidget;
    }

    public function getAutocompleteValueFromField(string $field, ?string $table = null)
    {
        if (!$field || isset(static::$mappingCache[$field])) {
            return static::$mappingCache[$field];
        }

        $config = $this->bundleConfig['mapping'];

        // DCA?
        if ($table) {
            $this->dcaUtil->loadDc($table);

            $dca = $GLOBALS['TL_DCA'][$table];

            if (isset($dca['fields'][$field]['eval']['autocomplete']) && $dca['fields'][$field]['eval']['autocomplete']) {
                static::$mappingCache[$field] = $dca['fields'][$field]['eval']['autocomplete'];

                return $dca['fields'][$field]['eval']['autocomplete'];
            }
        }

        // equal?
        foreach (array_keys($config) as $autocompleteValue) {
            if ($this->compareValues($autocompleteValue, $field, true)) {
                static::$mappingCache[$field] = str_replace('_', '-', $autocompleteValue);

                return static::$mappingCache[$field];
            }
        }

        // synonym?
        foreach ($config as $autocompleteValue => $data) {
            foreach ($data['synonyms'] as $synonym) {
                if ($this->compareValues($synonym, $field, true)) {
                    static::$mappingCache[$field] = str_replace('_', '-', $autocompleteValue);

                    return static::$mappingCache[$field];
                }
            }
        }

        // try again but fuzzy
        // equal?
        foreach (array_keys($config) as $autocompleteValue) {
            if ($this->compareValues($autocompleteValue, $field)) {
                static::$mappingCache[$field] = str_replace('_', '-', $autocompleteValue);

                return static::$mappingCache[$field];
            }
        }

        // synonym?
        foreach ($config as $autocompleteValue => $data) {
            foreach ($data['synonyms'] as $synonym) {
                if ($this->compareValues($synonym, $field)) {
                    static::$mappingCache[$field] = str_replace('_', '-', $autocompleteValue);

                    return static::$mappingCache[$field];
                }
            }
        }

        static::$mappingCache[$field] = false;

        return static::$mappingCache[$field];
    }

    protected function compareValues(string $value1, string $value2, bool $strict = false): bool
    {
        $value1 = strtolower(preg_replace('@[^a-zA-Z0-9]@i', '', $value1));
        $value2 = strtolower(preg_replace('@[^a-zA-Z0-9]@i', '', $value2));

        return $value1 === $value2 || (!$strict && false !== strpos($value1, $value2)) || (!$strict && false !== strpos($value2, $value1));
    }
}

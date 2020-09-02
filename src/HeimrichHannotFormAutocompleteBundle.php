<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\FormAutocompleteBundle;

use HeimrichHannot\FormAutocompleteBundle\DependencyInjection\FormAutocompleteExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotFormAutocompleteBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new FormAutocompleteExtension();
    }
}

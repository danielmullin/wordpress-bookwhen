<?php

/*
 * Copyright (c) Alexandre Gomes Gaigalas <alganet@gmail.com>
 * SPDX-License-Identifier: MIT
 */
declare (strict_types=1);
namespace InShore\Bookwhen\Vendor\Respect\Validation\Exceptions;

/**
 * @author Henrique Moody <henriquemoody@gmail.com>
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */
final class NifException extends ValidationException
{
    /**
     * {@inheritDoc}
     */
    protected $defaultTemplates = [self::MODE_DEFAULT => [self::STANDARD => '{{name}} must be a NIF'], self::MODE_NEGATIVE => [self::STANDARD => '{{name}} must not be a NIF']];
}

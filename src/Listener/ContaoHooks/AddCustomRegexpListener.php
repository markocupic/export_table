<?php

declare(strict_types=1);

/*
 * This file is part of Export Table for Contao CMS.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

namespace Markocupic\ExportTable\Listener\ContaoHooks;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Widget;
use Markocupic\ExportTable\Config\Config;
use Markocupic\ExportTable\Helper\Str;

/**
 * @Hook(AddCustomRegexpListener::HOOK,  priority=AddCustomRegexpListener::PRIORITY)
 */
class AddCustomRegexpListener
{
    public const HOOK = 'addCustomRegexp';
    public const PRIORITY = 10;

    /**
     * @var bool
     */
    public static $disableHook = false;

    /**
     * @var Str
     */
    private $str;

    /**
     * @var Config
     */
    private $config;

    public function __construct(Str $str, Config $config)
    {
        $this->str = $str;
        $this->config = $config;
    }

    public function __invoke(string $regexp, $input, Widget $widget): bool
    {
        if (static::$disableHook) {
            return false;
        }

        if ('jsonarray' === $regexp) {
            $array = json_decode($input);

            if (!\is_array($array)) {
                $widget->addError(
                    'Invalid expression. Please insert a json array.'
                );
            } elseif ('' !== $input && $this->str->testAgainstSet(strtolower($input), $this->config->getNotAllowedFilterExpr())) {
                $widget->addError(
                    sprintf(
                        'Illegal filter expression! Do not use "%s" in your filter expression.',
                        strtoupper(implode(', ', self::ILLEGAL_EXPR)),
                    )
                );
            }

            return true;
        }

        return false;
    }
}

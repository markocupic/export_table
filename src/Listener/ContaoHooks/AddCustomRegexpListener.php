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
use Symfony\Contracts\Translation\TranslatorInterface;

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
    private $strHelper;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Str $strHelper, Config $config, TranslatorInterface $translator)
    {
        $this->strHelper = $strHelper;
        $this->config = $config;
        $this->translator = $translator;
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
                    $this->translator->trans('ERR.exportTblInvalidFilterExpression', [], 'contao_default')
                );
            } elseif ('' !== $input && $this->strHelper->testAgainstSet(strtolower($input), $this->config->getNotAllowedFilterExpr())) {
                $widget->addError(
                    $this->translator->trans('ERR.exportTblNotAllowedFilterExpression', [strtoupper(implode(', ', self::ILLEGAL_EXPR))], 'contao_default')
                );
            }

            return true;
        }

        return false;
    }
}

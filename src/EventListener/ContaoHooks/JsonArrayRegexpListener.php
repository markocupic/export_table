<?php

declare(strict_types=1);

/*
 * This file is part of Contao Export Table.
 *
 * (c) Marko Cupic <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

namespace Markocupic\ExportTable\EventListener\ContaoHooks;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Widget;
use Markocupic\ExportTable\Config\Config;
use Markocupic\ExportTable\Helper\StringHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsHook(JsonArrayRegexpListener::HOOK, priority: JsonArrayRegexpListener::PRIORITY)]
class JsonArrayRegexpListener implements ListenerInterface
{
    public const HOOK = 'addCustomRegexp';
    public const PRIORITY = 100;
    private static bool $disableHook = false;

    public function __construct(
        private readonly StringHelper $stringHelper,
        private readonly Config $config,
        private readonly TranslatorInterface $translator,
    ) {
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
            } elseif ('' !== $input && $this->stringHelper->testAgainstSet(strtolower($input), $this->config->getNotAllowedFilterExpr())) {
                $widget->addError(
                    $this->translator->trans('ERR.exportTblNotAllowedFilterExpression', [strtoupper(implode(', ', $this->config->getNotAllowedFilterExpr()))], 'contao_default')
                );
            }

            return true;
        }

        return false;
    }

    public static function disableHook(): void
    {
        self::$disableHook = true;
    }

    public static function enableHook(): void
    {
        self::$disableHook = false;
    }

    public static function isEnabled(): bool
    {
        return self::$disableHook;
    }
}

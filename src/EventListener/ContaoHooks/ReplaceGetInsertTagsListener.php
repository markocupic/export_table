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
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Input;

#[AsHook(ReplaceGetInsertTagsListener::HOOK, priority: ReplaceGetInsertTagsListener::PRIORITY)]
class ReplaceGetInsertTagsListener implements ListenerInterface
{
    public const HOOK = 'replaceInsertTags';

    public const PRIORITY = 10;

    public static bool $disableHook = false;

    public function __construct(
        private readonly ContaoFramework $framework,
    ) {
    }

    public function __invoke(string $insertTag, bool $useCache, string $cachedValue, array $flags, array $tags, array $cache, int $_rit, int $_cnt)
    {
        if (static::$disableHook) {
            return false;
        }

        $inputAdapter = $this->framework->getAdapter(Input::class);
        $elements = explode('::', $insertTag);
        $key = strtoupper($elements[0]);

        // Replace {{GET::key}} with a given value of certain $_GET parameter.
        if ('GET' === $key) {
            if ($elements[1] && '' !== $inputAdapter->get($elements[1])) {
                return $inputAdapter->get($elements[1]);
            }

            return 'empty-string';
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

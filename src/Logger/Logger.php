<?php

declare(strict_types=1);

/*
 * This file is part of Contao Export Table.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/export_table
 */

namespace Markocupic\ExportTable\Logger;

use Contao\CoreBundle\Monolog\ContaoContext;
use Psr\Log\LoggerInterface;

class Logger
{

    private ?LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function log(string $strText, string $strLogLevel, string $strContaoLogLevel, string $strMethod): void
    {
        if (null !== $this->logger) {
            $this->logger->log(
                $strLogLevel,
                $strText,
                [
                    'contao' => new ContaoContext($strMethod, $strContaoLogLevel),
                ]
            );
        }
    }
}

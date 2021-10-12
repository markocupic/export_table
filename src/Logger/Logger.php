<?php

declare(strict_types=1);

/*
 * This file is part of Contao Multi File Download.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-multifile-download
 */

namespace Markocupic\ExportTable\Logger;

use Contao\CoreBundle\Monolog\ContaoContext;
use Psr\Log\LoggerInterface;

class Logger
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger = null)
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

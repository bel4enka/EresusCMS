<?php
/**
 * Команда "eresus:setperms"
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 */

namespace Eresus\CmsBundle\Command;

use Eresus\CmsBundle\Kernel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Команда "eresus:setperms"
 */
class SetPermsCommand extends ContainerAwareCommand
{
    /**
     * Регистрирует команду
     *
     * @see Symfony\Component\Console\Command.Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('eresus:setperms')
            ->setDescription('Set valid permissions on CMS files');
    }

    /**
     * Выполняет команду
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     *
     * @see Symfony\Component\Console\Command.Command::execute()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Kernel $kernel */
        $kernel = $this->getContainer()->get('kernel');

        chmod($kernel->getRootDir() . '/config/global.yml', 0666);
        chmod($kernel->getRootDir() . '/config/plugins.yml', 0666);
        $this->chmodRecursive($kernel->getRootDir() . '/cache');
        $this->chmodRecursive($kernel->getRootDir() . '/logs');
        $this->chmodRecursive($kernel->getRootDir() . '/public');
        $output->writeln('Done.');
    }

    /**
     * Рекурсивно выставляет нужные права на папки и файлы
     * @param string $path
     */
    private function chmodRecursive($path)
    {
        chmod($path, 0777);
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        while ($it->valid())
        {
            /** @var RecursiveDirectoryIterator $it */
            if (!$it->isDot())
            {
                /** @var \SplFileInfo $it */
                if ($it->isDir())
                {
                    chmod($it->getPathname(), 0777);
                }
                else
                {
                    chmod($it->getPathname(), 0666);
                }
            }
            /** @var RecursiveIteratorIterator $it */
            $it->next();
        }
    }
}


<?php

declare(strict_types=1);

namespace App\Console\Command\Cache\Clear;

use App\Console\Command\BaseCommand;
use RuntimeException;
use Slim\Views\Twig;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Cache\CacheInterface;
use Twig\Cache\FilesystemCache;
use Twig\Cache\NullCache;

class ViewCommand extends BaseCommand
{
    /** {@inheritDoc} */
    protected static $defaultName = 'cache:clear:view';

    /** @var Twig */
    protected $view;

    public function __construct(Twig $view)
    {
        $this->view = $view;

        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var CacheInterface $cache */
        $cache = $this->view->getEnvironment()->getCache(false);

        if ($cache instanceof FilesystemCache) {
            // FilesystemCacheからはディレクトリを取得できないので
            /** @var string $cacheDir */
            $cacheDir = $this->view->getEnvironment()->getCache();
            $this->clearFilesystemCache($cacheDir, $output);
        } elseif ($cache instanceof NullCache) {
            $output->writeln('Disable cache.');
        } else {
            throw new RuntimeException(sprintf('This cache interface is not supported (%s).', get_class($cache)));
        }

        $output->writeln('Command exit.');

        return 0;
    }

    protected function clearFilesystemCache(string $dir, OutputInterface $output): void
    {
        $output->writeln('Clear filesystem chace.');

        $filesystem = new Filesystem();
        $filesystem->remove($dir);

        $output->writeln(sprintf('dir- %s', $dir));
    }
}

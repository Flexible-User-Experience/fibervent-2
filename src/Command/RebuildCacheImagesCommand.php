<?php

namespace App\Command;

use App\Entity\BladePhoto;
use App\Entity\Photo;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class RebuildCacheImagesCommand
 *
 * @category Command
 */
class RebuildCacheImagesCommand extends AbstractBaseCommand
{
    /**
     * @var int
     */
    private $photosFound = 0;

    /**
     * @var int
     */
    private $photosNotFound = 0;

    /**
     * @var int
     */
    private $blPhotosFound = 0;

    /**
     * @var int
     */
    private $blPhotosNotFound = 0;

    /**
     * Methods.
     */

    /**
     * RebuildCacheImagesCommand constructor.
     *
     * @param EntityManagerInterface $em
     * @param UploaderHelper $uh
     * @param DataManager $dm
     * @param CacheManager $cm
     * @param FilterManager $fm
     */
    public function __construct(EntityManagerInterface $em, UploaderHelper $uh, DataManager $dm, CacheManager $cm, FilterManager $fm)
    {
        $this->em = $em;
        $this->uploaderHelper = $uh;
        $this->dataManager = $dm;
        $this->cacheManager = $cm;
        $this->filterManager = $fm;

        parent::__construct();
    }

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName('app:rebuild:cache:images')
            ->setDescription('Rebuild cache images command');
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Welcome to Rebuild cache images command</info>');
        $output->writeln('Loading pictures, please wait...');

        $photos = $this->em->getRepository('AppBundle:Photo')->findAll();
        $this->buildImagesCacheCollection($photos, $output, 'Photo');

        $bladePhotos = $this->em->getRepository('AppBundle:BladePhoto')->findAll();
        $this->buildImagesCacheCollection($bladePhotos, $output, 'Blade Photo');

        $output->writeln('Total records '.($this->photosFound + $this->photosNotFound + $this->blPhotosFound + $this->blPhotosNotFound));
        $output->writeln('Created Photos '.$this->photosFound);
        $output->writeln('Errors Photos '.$this->photosNotFound);
        $output->writeln('Created BladePhotos '.$this->blPhotosFound);
        $output->writeln('Errors BladePhotos '.$this->blPhotosNotFound);

        return true;
    }

    /**
     * @param array           $collection
     * @param OutputInterface $output
     * @param string          $type
     */
    private function buildImagesCacheCollection($collection, $output, $type)
    {
        foreach ($collection as $item) {
            $path = $this->uploaderHelper->asset($item, 'imageFile');
            if ($path) {
                $this->buildImageCache($output, $item, $type, '960x540', $path);
                $this->buildImageCache($output, $item, $type, '600x960', $path);
            }
        }
    }

    /**
     * @param OutputInterface  $output
     * @param Photo|BladePhoto $object
     * @param string           $type
     * @param string           $filter
     * @param string           $path
     */
    private function buildImageCache(OutputInterface $output, $object, $type, $filter, $path)
    {
        $binary = $this->dataManager->find($filter, $path);
        if ($binary) {
            $this->cacheManager->store(
                $this->filterManager->applyFilter($binary, $filter),
                $path,
                $filter
            );
            $this->writeMessage($output, $object, $type, $filter, $path, true);
            if ($object instanceof Photo) {
                $this->photosFound = $this->photosFound + 1;
            } else {
                $this->blPhotosFound = $this->blPhotosFound + 1;
            }
        } else {
            $this->writeMessage($output, $object, $type, $filter, $path, false);
            if ($object instanceof Photo) {
                $this->photosNotFound = $this->photosNotFound + 1;
            } else {
                $this->blPhotosNotFound = $this->blPhotosNotFound + 1;
            }
        }
    }

    /**
     * @param OutputInterface  $output
     * @param Photo|BladePhoto $object
     * @param string           $type
     * @param string           $filter
     * @param string           $path
     * @param bool             $isCreated
     */
    private function writeMessage(OutputInterface $output, $object, $type, $filter, $path, $isCreated)
    {
        if ($isCreated) {
            $ending = 'Created';
        } else {
            $ending = '<error>Not created</error>';
        }
        $output->writeln($type.' #'.$object->getId().' '.$filter.' '.$object->getImageName().' '.$path.' '.$ending);
    }
}

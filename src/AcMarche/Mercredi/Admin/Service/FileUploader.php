<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 19/09/16
 * Time: 15:09
 */

namespace AcMarche\Mercredi\Admin\Service;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class FileUploader
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(ParameterBagInterface $parameterBag, FlashBagInterface $flashBag)
    {
        $this->parameterBag = $parameterBag;
        $this->flashBag = $flashBag;
    }

    public function uploadEnfant($type, $id, UploadedFile $file, $fileName)
    {
        switch ($type) {
            case 'sante':
                $directory = $this->parameterBag->get('enfant_sante').DIRECTORY_SEPARATOR.$id;
                break;
            case 'inscription':
                $directory = $this->parameterBag->get('enfant_inscription').DIRECTORY_SEPARATOR.$id;
                break;
            case 'photo':
                $directory = $this->parameterBag->get('enfant_photo');
                break;
            default:
                $directory = "lost".DIRECTORY_SEPARATOR.$id;
                break;
        }

        $result = $file->move($directory, $fileName);

        return $result;
    }

    public function traitementFiles(Enfant $enfant)
    {
        if ($santeName = $this->traitementFile($enfant->getFile(), $enfant, "sante")) {
            $enfant->setFicheName($santeName);
        }

        if ($inscriptionName = $this->traitementFile($enfant->getFiche(), $enfant, "inscription")) {
            $enfant->setFileName($inscriptionName);
        }

        if ($photoName = $this->traitementFile($enfant->getImage(), $enfant, "photo")) {
            $enfant->setImageName($photoName);
        }
    }

    protected function traitementFile($file, Enfant $entity, $type)
    {
        if ($file instanceof UploadedFile) {
            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            try {
                $this->uploadEnfant($type, $entity->getId(), $file, $fileName);

                return $fileName;
            } catch (FileException $error) {
                $this->flashBag->add('danger', $error->getMessage());
            }
        }

        return null;
    }
}

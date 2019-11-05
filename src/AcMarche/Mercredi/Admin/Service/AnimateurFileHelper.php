<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 7/11/18
 * Time: 16:35.
 */

namespace AcMarche\Mercredi\Admin\Service;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class AnimateurFileHelper
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(FlashBagInterface $flashBag, ParameterBagInterface $parameterBag)
    {
        $this->flashBag = $flashBag;
        $this->parameterBag = $parameterBag;
    }

    public function traitementFiles(Animateur $animateur)
    {
        if ($cvName = $this->traitementFile($animateur->getFile(), $animateur, 'cv')) {
            $animateur->setFileName($cvName);
        }

        if ($diplomeName = $this->traitementFile($animateur->getDiplomeFile(), $animateur, 'diplome')) {
            $animateur->setDiplomeName($diplomeName);
        }

        if ($certificatName = $this->traitementFile($animateur->getCertificat(), $animateur, 'certificat')) {
            $animateur->setCertificatName($certificatName);
        }

        if ($casierName = $this->traitementFile($animateur->getCasier(), $animateur, 'casier')) {
            $animateur->setCasierName($casierName);
        }

        if ($photoName = $this->traitementFile($animateur->getImage(), $animateur, 'photo')) {
            $animateur->setImageName($photoName);
        }
    }

    public function traitementFile($file, Animateur $animateur, $type)
    {
        if ($file instanceof UploadedFile) {
            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            try {
                $this->uploadAnimateur($type, $animateur->getId(), $file, $fileName);

                return $fileName;
            } catch (FileException $error) {
                $this->flashBag->add('danger', $error->getMessage());
            }
        }

        return null;
    }

    public function uploadAnimateur($type, $id, UploadedFile $file, $fileName)
    {
        switch ($type) {
            case 'cv':
                $directory = $this->parameterBag->get('animateur_cv').DIRECTORY_SEPARATOR.$id;
                break;
            case 'casier':
                $directory = $this->parameterBag->get('animateur_casier').DIRECTORY_SEPARATOR.$id;
                break;
            case 'certificat':
                $directory = $this->parameterBag->get('animateur_certificat').DIRECTORY_SEPARATOR.$id;
                break;
            case 'diplome':
                $directory = $this->parameterBag->get('animateur_diplome').DIRECTORY_SEPARATOR.$id;
                break;
            case 'photo':
                $directory = $this->parameterBag->get('animateur_photo');
                break;
            default:
                $directory = 'lost-animateur'.DIRECTORY_SEPARATOR.$id;
                break;
        }

        $result = $file->move($directory, $fileName);

        return $result;
    }
}

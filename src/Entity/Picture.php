<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use Doctrine\ORM\Mapping as ORM;
use datetime;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=picturesRepository::class)
  * @Vich\Uploadable
 */
class Picture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

   /**
     *
     * @Vich\UploadableField(mapping="picture_file", fileNameProperty="pictureName")
     *
     * @var File
     */
    private $pictureFile;

      /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */

    private $pictureName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     */
    private $user;
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|null $imageFile
     */
    public function setpictureFile($pictureFile = null)
    {
        $this->pictureFile = $pictureFile;

        if ($pictureFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime("now");
        }
    }

    public function getpictureFile()
    {
        return $this->pictureFile;
    }

    public function setpictureName(?string $pictureName): void
    {
        $this->pictureName = $pictureName;
    }

    public function getpictureName(): ?string
    {
        return $this->pictureName;
    }

/*
    * Get the value of updatedAt
    *
    * @return  Datetime
    */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /*
     * Set the value of updatedAt
     *
     * @param  Datetime  $updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt(\Datetime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}

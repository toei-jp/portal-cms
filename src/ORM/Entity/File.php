<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * File entity class
 *
 * @todo 削除のイベントでファイルも削除される仕組み
 *
 * @ORM\Entity
 * @ORM\Table(name="file", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class File extends AbstractEntity
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected int $id;

    /** @ORM\Column(type="string", unique=true) */
    protected string $name;

    /** @ORM\Column(type="string", name="original_name") */
    protected string $originalName;

    /** @ORM\Column(type="string", name="mime_type") */
    protected string $mimeType;

    /** @ORM\Column(type="integer", options={"unsigned"=true}) */
    protected int $size;

    protected static string $blobContainer = 'file';

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): void
    {
        $this->originalName = $originalName;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    public static function getBlobContainer(): string
    {
        return self::$blobContainer;
    }

    /**
     * @param string $file original file
     */
    public static function createName(string $file): string
    {
        $info = pathinfo($file);

        return md5(uniqid('', true)) . '.' . $info['extension'];
    }
}

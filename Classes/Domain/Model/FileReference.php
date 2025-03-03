<?php
namespace Extension14v\Imagecredits14v\Domain\Model;
use TYPO3\CMS\Extbase\Domain\Model\FileReference as BaseFileReference;
class FileReference extends BaseFileReference {
    protected int $uidForeign = 0;
    protected string $tablenames = '';
    protected string $fieldname = '';
    protected $uidLocal = 0;

    public function getUidForeign(): int
    {
        return $this->uidForeign;
    }

    public function setUidForeign(int $uidForeign): FileReference
    {
        $this->uidForeign = $uidForeign;
        return $this;
    }

    public function getTablenames(): string
    {
        return $this->tablenames;
    }

    public function setTablenames(string $tablenames): FileReference
    {
        $this->tablenames = $tablenames;
        return $this;
    }

    public function getFieldname(): string
    {
        return $this->fieldname;
    }

    public function setFieldname(string $fieldname): FileReference
    {
        $this->fieldname = $fieldname;
        return $this;
    }

    public function getUidLocal(): int {
        return $this->uidLocal;
    }

    public function setUidLocal(int $uidLocal): FileReference {
        $this->uidLocal = $uidLocal;
        return $this;
    }
}
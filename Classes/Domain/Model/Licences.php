<?php

declare(strict_types=1);

namespace Extension14v\Imagecredits14v\Domain\Model;

use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * This file is part of the "Gemeinde-Paket" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2022 Oliver Busch <ob@14v.de>, one4vision GmbH
 */

/**
 * Category
 */
class Licences extends AbstractEntity {
    protected string $name='';
    protected string $licenceName='';
    protected string $licenceUrl='';
    protected Uri $editLink;

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getLicenceName(): string {
        return $this->licenceName;
    }

    public function setLicenceName(string $licenceName): void {
        $this->licenceName = $licenceName;
    }

    public function getLicenceUrl(): string {
        return $this->licenceUrl;
    }

    public function setLicenceUrl(string $licenceUrl): void {
        $this->licenceUrl = $licenceUrl;
    }

    public function getEditLink(): Uri
    {
        return $this->editLink;
    }

    public function setEditLink(Uri $editLink): void {
        $this->editLink = $editLink;
    }
}
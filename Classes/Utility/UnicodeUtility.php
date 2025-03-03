<?php
namespace Extension14v\Imagecredits14v\Utility;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Charset\CharsetConverter;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Utility method for charset conversion
 */
class UnicodeUtility implements SingletonInterface {

	protected CharsetConverter $charsetConverter;

	public function convertValues(array $metadata): array
    {

		foreach ($metadata as $key => $value) {
			$metadata[$key] = $this->convert($value);
		}

		return $metadata;
	}

	public function convert(string $value): string
    {

		// iso-8859-15 is assumed to be the standard encoding for file metadata
//		$inputEncoding = 'iso-8859-15';
		$inputEncoding = 'utf-8';

		// This function would also do the job, in case: mb_convert_encoding($value, 'UTF-8', 'auto')
		return $this->getCharsetConverter()->conv($value, $inputEncoding, 'utf-8');
	}

	protected function getCharsetConverter(): CharsetConverter
    {
        return $this->charsetConverter;
	}
}

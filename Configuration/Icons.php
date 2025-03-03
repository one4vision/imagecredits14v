<?php
$iconList = [];
foreach([
            'imagecredits14v-plugin-imglist' => 'user_plugin_imglist.png',
            'module-imagecredits' => 'user_mod_imagecredits14v.png'
        ] as $identifier => $path) {
    $iconList[$identifier] = [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
        'source' => 'EXT:imagecredits14v/Resources/Public/Icons/' . $path,
    ];
}

return $iconList;
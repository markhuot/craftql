<?php

namespace markhuot\CraftQL\Types;

use aelvan\imager\Imager;
use craft\elements\Asset;
use GraphQL\Error\UserError;
use markhuot\CraftQL\Builders\Schema;

class ImagerTransformedImages extends Schema {

    function boot() {
        $imagesField = $this->addField('images')
            ->type(ImagerTransformedImageModel::class)
            ->lists()
            ->resolve(function (Asset $asset, $args, $context, $info) {
                if (empty($args['transforms'])) {
                    throw new UserError('You must specify the `transforms:` argument with Imager');
                }

                foreach ($args['transforms'] as &$transform) {

                    if (!empty($transform['effects']['colorBlend'])) {
                        $transform['effects']['colorBlend'] = array_values($transform['effects']['colorBlend']);
                    }

                    if (!empty($transform['effects']['tint'])) {
                        foreach ($transform['effects']['tint'] as &$tint) {
                            $tint = @$tint['color'];
                        }
                    }

                    if (!empty($transform['effects']['posterize'])) {
                        $transform['effects']['posterize'] = [
                            @$transform['effects']['posterize']['levels'],
                            @$transform['effects']['posterize']['dither']
                        ];
                    }

                    if (!empty($transform['effects']['levels'])) {
                        $transform['effects']['levels'] = [
                            @$transform['effects']['levels']['blackPoint'],
                            @$transform['effects']['levels']['gamma'],
                            @$transform['effects']['levels']['whitePoint'],
                            @$transform['effects']['levels']['channel'],
                        ];
                    }

                    if (empty($transform['ignoreFocalPoint']) && $asset->hasFocalPoint) {
                        $transform['position'] = $asset->getFocalPoint(true);
                    }

                }

                return Imager::$plugin->imager->transformImage($asset, $args['transforms']);
            });

        /** @var InputSchema $transforms */
        $transforms = $this->createInputObjectType('ImagerTransformOptions');
        $imagesField->addArgument('transforms')->nonNull()->type($transforms)->lists();
        $transforms->addEnumArgument('format')->values(['jpg', 'png', 'gif', 'webp']);
        $transforms->addIntArgument('width');
        $transforms->addIntArgument('height');
        $transforms->addFloatArgument('ratio');
        $transforms->addStringArgument('position');
        $transforms->addBooleanArgument('ignoreFocalPoint');
        $transforms->addEnumArgument('mode')->values(['crop', 'fit', 'stretch', 'croponly', 'letterbox']);
        $transforms->addFloatArgument('cropZoom');
        $transforms->addStringArgument('frames');
        $transforms->addIntArgument('jpegQuality');
        $transforms->addIntArgument('pngCompressionLevel');
        $transforms->addIntArgument('webpQuality');
        $transforms->addBooleanArgument('allowUpscale');
        $transforms->addEnumArgument('resizeFilter')->values(['point', 'box', 'triangle', 'hermite', 'hanning', 'hamming', 'blackman', 'gaussian', 'quadratic', 'cubic', 'catrom', 'mitchell', 'lanczos', 'bessel', 'sinc']);

        $watermark = $transforms->createInputObjectType('ImagerWatermark');
        $transforms->addArgument('watermark')->type($watermark);
        $watermark->addStringArgument('image');
        $watermark->addIntArgument('width');
        $watermark->addIntArgument('height');
        $watermark->addIntArgument('opacity');
        $watermark->addEnumArgument('blendMode')->values(['blend', 'darken', 'lighten', 'modulate', 'multiply', 'overlay', 'screen']);

        $watermarkPosition = $watermark->createInputObjectType('ImagerWatermarkPosition');
        $watermark->addArgument('position')->type($watermarkPosition);
        $watermarkPosition->addIntArgument('left');
        $watermarkPosition->addIntArgument('right');
        $watermarkPosition->addIntArgument('top');
        $watermarkPosition->addIntArgument('bottom');

        $effects = $transforms->createInputObjectType('ImagerEffects');
        $transforms->addArgument('effects')->type($effects);
        $transforms->addArgument('preEffects')->type($effects);
        $effects->addBooleanArgument('grayscale');
        $effects->addBooleanArgument('negative');
        $effects->addFloatArgument('blur');
        $effects->addBooleanArgument('sharpen');
        $effects->addFloatArgument('gamma');
        $effects->addStringArgument('colorize');
        $effects->addArgument('colorBlend')->type(ImagerColorBlendEffect::class);
        $effects->addArgument('tint')->type(ImagerTintEffect::class)->lists();
        $effects->addIntArgument('sepia');
        $effects->addFloatArgument('contrast');
        $effects->addIntArgument('modulate')->lists();
        $effects->addBooleanArgument('normalize');
        $effects->addFloatArgument('contrastStretch')->lists();
        $effects->addArgument('posterize')->type(ImagerPosterizeEffect::class);
        $effects->addFloatArgument('unsharpMask')->lists();
        $effects->addStringArgument('clut');
        $effects->addIntArgument('quantize')->lists();
        $effects->addArgument('levels')->type(ImagerLevelsEffect::class);

        $placeholderField = $this->addStringField('placeholder')
            ->resolve(function (Asset $asset, $args) {
                $config = array_merge($args, [
                    'source' => $asset->getPath(),
                ]);
                return Imager::$plugin->placeholder->placeholder($config);
            });

        $placeholderField->addEnumArgument('type')->values(['svg', 'gif', 'silhouette']);
        $placeholderField->addIntArgument('width');
        $placeholderField->addIntArgument('height');
        $placeholderField->addStringArgument('color');
        $placeholderField->addStringArgument('fgColor');
        $placeholderField->addStringArgument('size');
        $placeholderField->addEnumArgument('silhouetteType')->values(['curve']);
    }

}
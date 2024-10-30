<?php

namespace Mapado\ImageUrlBuilder\Twig;

use Mapado\ImageUrlBuilder\Builder;
/**
 * ImageExtension
 *
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class UrlBuilderExtension extends \Twig_Extension
{
    /**
     * @var Builder
     */
    private $builder;
    /**
     * __construct
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }
    /**
     * getFilters
     *
     * @return array
     */
    public function getFilters()
    {
        return [new \Twig_SimpleFilter('imageUrl', [$this, 'imageUrl'])];
    }
    /**
     * buildUrl
     */
    public function imageUrl($image, $width = 0, $height = 0, array $options = [])
    {
        return $this->builder->buildUrl($image, $width, $height, $options);
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mapado_image_url_builder';
    }
}
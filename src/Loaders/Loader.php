<?php

namespace Waavi\Translation\Loaders;

use Illuminate\Translation\LoaderInterface;

abstract class Loader implements LoaderInterface
{
    /**
     * The default locale.
     *
     * @var string
     */
    protected $defaultLocale;

    /**
     *  Create a new loader instance.
     *
     *  @param  \Waavi\Translation\Repositories\LanguageRepository      $languageRepository
     *  @param  \Waavi\Translation\Repositories\TranslationRepository   $translationRepository
     *  @param  \Illuminate\Config\Repository                           $config
     */
    public function __construct($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * Load the messages for the given locale.
     *
     * @param string $locale
     * @param string $group
     * @param string $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null)
    {
        if ($locale != $this->defaultLocale && !\Config::get('translator.record.create_record')) {
            return array_replace_recursive(
                $this->loadSource($this->defaultLocale, $group, $namespace),
                $this->loadSource($locale, $group, $namespace)
            );
        }

        return $this->loadSource($locale, $group, $namespace);
    }

    public function getLoaderType()
    {
        return class_basename($this);
    }

    /**
     * Load the messages for the given locale from the loader source (cache, file, database, etc...).
     *
     * @param string $locale
     * @param string $group
     * @param string $namespace
     *
     * @return array
     */
    abstract public function loadSource($locale, $group, $namespace = null);

    /**
     * Add a new namespace to the loader.
     *
     * @param string $namespace
     * @param string $hint
     *
     * @return void
     */
    abstract public function addNamespace($namespace, $hint);
}

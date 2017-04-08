<?php

namespace Waavi\Translation;

use Illuminate\Support\Arr;

class Translator extends \Illuminate\Translation\Translator
{
    protected function getLine($namespace, $group, $locale, $item, array $replace)
    {
        $key = $item;
        if (\Config::get('translator.record.create_record')) {
            if (str_contains($item, '.')) {
                $key = stristr($item, '.', true);
            } else {
                $key = strtok($item, ' ');
            }
        }

        $line = Arr::get($this->loaded[$namespace][$group][$locale], $key);

        if (is_string($line)) {
            return $this->makeReplacements($line, $replace);
        } elseif (is_array($line) && count($line) > 0) {
            return $line;
        }

        if (!$line && \Config::get('translator.record.create_record')) {
            if ($this->getLoader()->getLoaderType() == 'DatabaseLoader') {
                $exploded = explode('.', $item);
                $line = end($exploded);

                $this->getLoader()->getRepository()->create([
                   'locale'    => $locale,
                   'group'     => $group,
                   'namespace' => $namespace,
                   'item'      => $key,
                   'text'      => $line,
               ]);

                return $line;
            }
        }
    }
}

<?php

namespace App\Traits;

trait Translatable
{
    /**
     * Get translated attribute value
     */
    public function getTranslation($attribute, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $translationField = $attribute . '_translations';

        if ($this->hasTranslationColumn($translationField) && $this->{$translationField}) {
            $translations = is_string($this->{$translationField})
                ? json_decode($this->{$translationField}, true)
                : $this->{$translationField};

            if (is_array($translations) && isset($translations[$locale])) {
                return $translations[$locale];
            }
        }

        // Fallback to the base attribute
        return $this->{$attribute};
    }

    /**
     * Set translation for an attribute
     */
    public function setTranslation($attribute, $locale, $value)
    {
        $translationField = $attribute . '_translations';

        if ($this->hasTranslationColumn($translationField)) {
            $translations = $this->{$translationField} ?: [];
            $translations[$locale] = $value;
            $this->{$translationField} = $translations;
        }

        return $this;
    }

    /**
     * Whether the model carries the given translations column.
     *
     * NB: intentionally NOT named hasAttribute() — that is an Eloquent method,
     * and overriding it breaks accessor resolution in Model::getAttribute().
     */
    protected function hasTranslationColumn($column)
    {
        return in_array($column, $this->fillable) ||
               array_key_exists($column, $this->attributes);
    }
}

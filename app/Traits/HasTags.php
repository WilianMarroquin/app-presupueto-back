<?php

namespace App\Traits;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTags
{
    /**
     * Relación polimórfica inversa.
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Helper para guardar keywords rápidamente.
     * Ej: $category->syncKeywords(['shell', 'texaco']);
     */
    public function syncKeywords(array $keywords)
    {
        $ids = [];
        foreach ($keywords as $word) {
            // Busca o crea el tag tipo 'keyword'
            $tag = Tag::firstOrCreate(
                ['slug' => \Str::slug($word), 'type' => 'keyword'],
                ['name' => $word] // Si se crea nuevo, usa el nombre original
            );
            $ids[] = $tag->id;
        }

        // Sincroniza (borra los viejos que no estén en la lista y agrega los nuevos)
        $this->tags()->where('type', 'keyword')->sync($ids);
    }
}

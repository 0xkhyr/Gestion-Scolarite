<?php

namespace App\Support;

class FilamentHelper
{
    /**
     * Get pagination settings for Filament tables
     */
    public static function getTablePagination(): array
    {
        $itemsPerPage = setting('system.items_per_page', 25);
        
        return [
            'pagination' => [
                'defaultPageSize' => $itemsPerPage,
                'pageSizeOptions' => [10, 25, 50, 100],
            ]
        ];
    }
    
    /**
     * Get default page size for Filament tables
     */
    public static function getDefaultPageSize(): int
    {
        return setting('system.items_per_page', 25);
    }
    
    /**
     * Configure a Filament table with settings-based pagination
     */
    public static function configurePagination($table)
    {
        return $table->defaultPaginationPageOption(self::getDefaultPageSize());
    }
    
    /**
     * Get items per page options for forms
     */
    public static function getItemsPerPageOptions(): array
    {
        return [
            '10' => '10 items per page',
            '25' => '25 items per page',
            '50' => '50 items per page',
            '100' => '100 items per page',
        ];
    }
}
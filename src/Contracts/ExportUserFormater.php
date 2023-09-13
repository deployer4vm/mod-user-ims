<?php

namespace hpsynapse\moduser\Contracts;

use hpsynapse\moduser\Facades\UserRepo;

interface ExportUserFormater
{
    public static function downloadUserData(
        $exportData,
        $listingParams
    ): UserRepo;

    public static function dataUserCoreRowFormater(
        $exportData,
        array $insertRow = [],
        array $dataRow = [],
        int $indexExcelRow,
        int $indexData
    ): array;

    public static function columnDownload(): array;
}

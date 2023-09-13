<?php

namespace hpsynapse\moduser\Contracts;

interface ExportUserFormater
{
    public static function downloadUserData(
        $exportData,
        $listingParams
    );

    public static function dataUserCoreRowFormater(
        $exportData,
        array $insertRow = [],
        array $dataRow = [],
        int $indexExcelRow,
        int $indexData
    ): array;

    public static function columnDownload(): array;
}

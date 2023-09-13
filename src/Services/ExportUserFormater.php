<?php

namespace hpsynapse\moduser\Services;

use Exception;

use Carbon\Carbon;

use App\Base\BaseRepository;
use Illuminate\Support\Facades\Log;

use App\Facades\Excel;
use App\Facades\Export;
use App\Facades\Import;
use hpsynapse\moduser\Facades\UserRepo;
use App\MainApp\Modules\Project\Facades\Project;
use Carbon\CarbonInterval;
use hpsynapse\moduser\Contracts\ExportUserFormater as ContractsExportUserFormater;

class ExportUserFormater extends BaseRepository implements ContractsExportUserFormater
{
    /**
     * Sumber data project atp, return harus builder
     *
     * @param Array $exportData data info export yang sedang dieksekusi (dari cache)
     * @param Array $listingParams
     * @return EloquentModel
     */
    public static function downloadUserData(
        $exportData,
        $listingParams
    ): UserRepo {
        $model = UserRepo::listUser(
            $listingParams['filter'],
            false,
            0,
            [],
            true
        );

        return $model;
    }

    /**
     * Core Row Formater - Data Rekap pinjman.
     * Memformat / menstrukturkan 1 row data untuk diinsertkan ke excel.
     *
     * @param Array $exportData data info export yang sedang dieksekusi (dari cache)
     * @param Array $insertRow record data (array) yang telah diformat awal (yang nanti akan diinsert ke excel)
     * @param Array $dataRow record data original dari databasenya
     * @param Int $indexExcelRow
     * @param Int $indexData
     *
     * @return Array list data yang akan diinsert ke excel
     */
    public static function dataUserCoreRowFormater(
        $exportData,
        array $insertRow = [],
        array $dataRow = [],
        int $indexExcelRow,
        int $indexData
    ): array {
        $rolesString = '';
        foreach ($dataRow['roles'] as $key => $role) {
            $rolesString .= $role['role']['name']. (isset($dataRow['roles'][$key+1]) ? ', ' : '');
        }

        $insertRow = [
            $dataRow['name'] ?? '',
            $dataRow['username'] ?? '',
            $dataRow['email'] ?? '',
            $dataRow['phone'] ?? '',
            $rolesString ?? '',
        ];

        return $insertRow;
    }

    public static function columnDownload(): array
    {
        return [
            [
                'default' => '',
                'caption' => 'Name',
                'type' => '',
            ],
            [
                'default' => '',
                'caption' => 'Username',
                'type' => '',
            ],
            [
                'default' => '',
                'caption' => 'Email',
                'type' => '',
            ],
            [
                'default' => '',
                'caption' => 'Phone',
                'type' => '',
            ],
            [
                'default' => '',
                'caption' => 'Roles',
                'type' => '',
            ],
        ];
    }
}

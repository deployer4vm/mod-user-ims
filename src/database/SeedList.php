<?php

return config('AppConfig.packageLocal.moduser.database.run_seed',true)?[
    \hpsynapse\moduser\database\seeds\UserAuthSeeds::class,
    \hpsynapse\moduser\database\seeds\UpdateTenantIdPerTenant::class,
    \hpsynapse\moduser\database\seeds\UpdateTenantIdTenantManager::class
]:[];
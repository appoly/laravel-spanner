<?php
/**
 * Copyright 2019 Colopl Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Colopl\Spanner\Concerns;

use Google\Cloud\Core\LongRunning\LongRunningOperation;
use Exception;
use Google\Cloud\Spanner\Database;

/**
 * @method Database getSpannerDatabase
 */
trait ManagesDataDefinitions
{
    /**
     * @param string $ddl
     * @return LongRunningOperation
     */
    public function runDdl(string $ddl): LongRunningOperation
    {
        return $this->getSpannerDatabase()->updateDdl($ddl);
    }

    /**
     * @param string[] $ddls
     * @return LongRunningOperation
     */
    public function runDdls(array $ddls): LongRunningOperation
    {
        return $this->getSpannerDatabase()->updateDdlBatch($ddls);
    }

    /**
     * @param string[] $statements Additional DDL statements
     * @throws Exception
     */
    public function createDatabase($statements = [])
    {
        $operation = $this->getSpannerDatabase()->create(['statements' => $statements]);
        $operation->pollUntilComplete();
        $error = $operation->error();
        if ($error !== null) {
            throw new Exception(json_encode($operation->error()));
        }
    }

    /**
     * @return void
     */
    public function dropDatabase()
    {
        $this->getSpannerDatabase()->drop();
    }

    /**
     * @return bool
     */
    public function databaseExists()
    {
        return $this->getSpannerDatabase()->exists();
    }
}

<?php


namespace CrudGeneratorTools\Util;


use ArrayToString\ArrayToStringTool;
use Bat\FileSystemTool;
use QuickPdo\QuickPdoInfoTool;

/**
 * Generate default foreignKeyPreferredColumns preferences in $path/auto/$db.php.
 * To override those preferences, manually create $path/manual/$db.php.
 *
 *
 */
class ForeignKeyPreferredColumnUtil
{

    private $cacheDir;

    public function __construct()
    {
        $this->cacheDir = '/tmp/ForeignKeyPreferredColumnUtil';
    }

    public static function create()
    {
        return new static();
    }


    /**
     * Return the preferred column for a (foreign) table, or false if there is no preferred column
     * for the given table.
     *
     *
     * @param $db
     * @param $table
     * @param bool $useCache
     * @return false|string
     */
    public function getPreferredForeignKey($db, $table, $useCache = true)
    {
        $path = $this->cacheDir;
        $autoPath = $path . "/auto/$db.php";
        $manualPath = $path . "/manual/$db.php";

        $preferredColumns = [];
        if (file_exists($manualPath)) {
            include $manualPath;
        } else {
            if (false === file_exists($autoPath) || false === $useCache) {
                $this->generatePreferredForeignKey($db, $autoPath);
            }
            include $autoPath;
        }

        if (array_key_exists($table, $preferredColumns)) {
            return $preferredColumns[$table];
        }
        return false;
    }


    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
        return $this;
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private function generatePreferredForeignKey($db, $file)
    {
        $tables = QuickPdoInfoTool::getTables($db);
        $table2Fk = [];
        foreach ($tables as $table) {
            $fkInfos = QuickPdoInfoTool::getForeignKeysInfo($table, $db);
            foreach ($fkInfos as $fkInfo) {

                $fkTable = $fkInfo[1];
                if (!array_key_exists($fkTable, $table2Fk)) {
                    $types = QuickPdoInfoTool::getColumnDataTypes($fkTable, false);
                    foreach ($types as $column => $type) {
                        if ('varchar' === $type) {
                            break;
                        }
                    }
                    $table2Fk[$fkTable] = $column;
                }
            }
        }

        $sArr = ArrayToStringTool::toPhpArray($table2Fk);
        $s = '<?php ' . PHP_EOL . PHP_EOL;
        $s .= '$preferredColumns = ';
        $s .= $sArr . ';' . PHP_EOL . PHP_EOL;
        FileSystemTool::mkfile($file, $s);
    }

}
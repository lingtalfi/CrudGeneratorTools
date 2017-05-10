<?php

namespace CrudGeneratorTools\Skinny\Generator;


use Kamille\Services\XLog;

class SkinnyModelGenerator implements SkinnyModelGeneratorInterface
{


    private $dstDir; // the dir where generated files are stored
    private $databases;
    private $cache;


    public function __construct()
    {
        $this->databases = null;
        $this->cache = [];
    }


    public static function create()
    {
        return new static();
    }

    public function generateFormModel($db, $table, array &$snippets, array &$uses)
    {
        if (false !== ($types = $this->getColTypes($db, $table))) {
            foreach ($types as $column => $type) {
                $p = explode('+', $type, 2);
                $typeId = $p[0];
                $this->generateFormControlModel($typeId, $type, $column, $db, $table, $snippets, $uses);
            }
        }
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function setDstDir($dstDir)
    {
        $this->dstDir = $dstDir;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function generateFormControlModel($typeId, $type, $column, $db, $table, array &$snippets, array &$uses)
    {
        switch ($typeId) {
            case 'auto_increment':

//                        $snippets[] = <<<EEE
//            ->addControl("$column", InputTextControl::create()
//                ->label("$column")
//                ->addHtmlAttribute("readonly", "readonly")
//                ->name("$column")
//            )
//EEE;
//                        $uses[] = 'FormModel\Control\InputTextControl';

                break;
            case 'input':
                $snippets[] = <<<EEE
            ->addControl("$column", InputTextControl::create()
                ->label("$column")
                ->name("$column")
            )
EEE;
                $uses[] = 'FormModel\Control\InputTextControl';
                break;
            case 'textarea':
                $snippets[] = <<<EEE
            ->addControl("$column", TextAreaControl::create()
                ->label("$column")
                ->name("$column")
            )
EEE;
                $uses[] = 'FormModel\Control\TextAreaControl';
                break;
            case 'pass':
                $snippets[] = <<<EEE
            ->addControl("$column", InputPasswordControl::create()
                ->label("$column")
                ->name("$column")
            )
EEE;
                $uses[] = 'FormModel\Control\InputPasswordControl';
                break;
            default:
                break;
        }
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function getColTypes($db, $table)
    {
        $fullTable = "$db.$table";
        if (!array_key_exists($fullTable, $this->cache)) {
            $f = $this->dstDir . "/" . $db . '.php';
            if (file_exists($f)) {
                $types = [];
                include $f;

                foreach ($types as $table => $col2Types) {
                    $this->cache["$db.$table"] = $col2Types;
                }
            } else {
                XLog::error("SkinnyModelGenerator: file not found: $f");
                return false;
            }
        }
        return $this->cache[$fullTable];
    }

}
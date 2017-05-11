<?php

namespace CrudGeneratorTools\Skinny\Generator;


use CrudGeneratorTools\Skinny\Util\SkinnyTypeUtil;

class SkinnyModelGenerator implements SkinnyModelGeneratorInterface
{



    protected $_useCache;

    /**
     * @var SkinnyTypeUtil $skinnyTypeUtil
     */
    private $skinnyTypeUtil;


    public function __construct()
    {
        $this->_useCache = true;
    }


    public static function create()
    {
        return new static();
    }

    public function generateFormModel($db, $table, array &$snippets, array &$uses)
    {
        $this->prepare();
        if (false !== ($types = $this->skinnyTypeUtil->getTypes($db, $table, $this->_useCache))) {
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
    public function setSkinnyTypeUtil(SkinnyTypeUtil $skinnyTypeUtil)
    {
        $this->skinnyTypeUtil = $skinnyTypeUtil;
        return $this;
    }

    public function useCache($useCache)
    {
        $this->_useCache = $useCache;
        return $this;
    }




    //--------------------------------------------
    //
    //--------------------------------------------
    protected function prepare()
    {
        if (null === $this->skinnyTypeUtil) {
            $this->skinnyTypeUtil = SkinnyTypeUtil::create();
        }
    }

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
}
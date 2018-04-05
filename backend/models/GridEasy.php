<?php
/*
 * @author: Nishant Bhardwaj
 * @since: 20-August-2017
 */

namespace backend\models;

use kartik\grid\GridView as KartikGridView;
use Yii;
use yii\bootstrap\Widget;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>

<div class="layout">
    <?php
    $GLOBALS = array(
        'flag' => 0,
        'headCount' => 0,
        'last' => 0,
        'arrIndex' => -1,
        'beforeHead' => 1,
    );

    class GridEasy extends Widget {

        public $arrGrid = [];
        public $arrColumnsToSort = [];
        public $dynamicPagination = false;

        public function init() {

            $view = $this->getView();
            $txtTitle = $view->title;

            $arrParams = Yii::$app->request->queryParams;
            $gridParameter = $this->arrGrid;


//            if (!array_key_exists('befHead', $gridParameter)) {
//                $gridParameter['befHead'] = false;
//            }
            //If beforeHeader Set to false No parent Header is shown
//            if (array_key_exists('befHead', $gridParameter)) {
//                if (!$gridParameter['befHead']) {
//                    $GLOBALS['beforeHead'] = 0;
//                }
//            }

            if (!is_array($gridParameter)):return [];
            endif;


            // Sets Grid Attributes like showPageSummary,layout, etc.
            $gridParameter = $this->setBasicAttributes($gridParameter);
            $arrGridColumnsModified = [];
            $lastCounter = 0;
            // Iterating over each columns
            foreach ($gridParameter['columns'] as $columnKey => $arrGridColumns) {
                if (is_array($arrGridColumns)) {


                    if (array_key_exists('visible', $arrGridColumns) && (!$arrGridColumns['visible'])) {
                        $GLOBALS['headCount'] = 1;
                    }
                    if (array_key_exists('visible', $arrGridColumns) && ($arrGridColumns['visible'])) {
                        $GLOBALS['headCount'] = 0;
                        //                    echo '<pre>';print_r($arrGridColumns);
                    }
                    if (!array_key_exists('visible', $arrGridColumns)) {
                        $GLOBALS['headCount'] = 0;
                    }
                }
                if (!is_array($arrGridColumns)) {
                    $GLOBALS['headCount'] = 1;
                }
//                    echo '<pre>';print_r($arrGridColumns);exit;
                //columns is array or string
                if (is_array($arrGridColumns)):
                    $arrGridColumnsModified[$columnKey] = $this->setColumnAttributes($gridParameter, $columnKey, $arrGridColumns, $lastCounter); //Column attributes
                else:
                    if ($gridParameter['dataProvider']->className() == ArrayDataProvider::className() && array_key_exists('filterModel', $gridParameter)) {

                        $arrColumn['attribute'] = $arrGridColumns;
                        $arrColumn['filter'] = '<input class="form-control" name="' . $arrColumn['attribute'] . '" value="' . (!empty($arrParams[$arrColumn['attribute']]) ? $arrParams[$arrColumn['attribute']] : '') . //$gridParameter['filterModel']['attributes']['int_product_id'] 
                                '" type="text">';
                        $arrColumn['value'] = $arrColumn['attribute'];
                        $arrGridColumnsModified[$columnKey] = $arrColumn;
                    } else {
                        $arrGridColumnsModified[$columnKey] = $arrGridColumns;
                    }

                endif;


                //ONLY IF 'query' is used:
                if ($GLOBALS['flag'] == 2) {
                    $GLOBALS['flag'] = 1;
                    $indexCounter = sizeof($arrGridColumnsModified[$columnKey]);
                    $arrIndex = array_keys($arrGridColumnsModified[$columnKey]);
                    $iter = 0;
                    for ($update = $lastCounter; $update < $lastCounter + $indexCounter; $update++) {
                        $tempArrGridColumnsModified[$update] = $arrGridColumnsModified[$columnKey][$arrIndex[$iter]];
                        $iter = $iter + 1;
                    }
                    $lastCounter = $lastCounter + $indexCounter;
                } else {
                    $tempArrGridColumnsModified[$lastCounter] = $arrGridColumnsModified[$columnKey];
                    $lastCounter = $lastCounter + 1;
                }
            }

            unset($gridParameter['parentHeader']);

            $gridParameter['columns'] = $tempArrGridColumnsModified;
            $this->setDataProviderAttributes($gridParameter);

            echo "<style>
                .panel-heading{
                    display:none;
                }
                .dropdown-menu li .btn-group{
                    margin-left:30px !important;
                }
                .dropdown-menu {
                    position: absolute;
                    top: 100%;
                    left: 0;
                    z-index: 1022;
                    display: none;
                    float: left;
                    min-width: 160px;
                    padding: 5px 0;
                    margin: 2px 0 0;
                    font-size: 14px;
                    text-align: left;
                    list-style: none;
                    background-color: #fff;
                    -webkit-background-clip: padding-box;
                    background-clip: padding-box;
                    border: 1px solid #ccc;
                    border: 1px solid rgba(0, 0, 0, .15);
                    border-radius: 4px;
                    -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
                    box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
                }
                 .table-hover > tbody > tr:hover {
                    background-color: #cbdcea;
                }   
                [name=export_content]{
                    display:none;
                }
                </style>";

            if (array_key_exists('search', $gridParameter) && (!$gridParameter['search'])) {
                unset($gridParameter['filterModel']);
            }
            unset($gridParameter['search']);

            $gridParameter['export']['fontAwesome'] = true;
//          To define export options:

            if (!array_key_exists('exportConfig', $gridParameter)) {
                //Header Center:
                if (!array_key_exists('txtTitle', $gridParameter)) {
                    if (!empty($txtTitle)) {
                        $gridParameter['txtTitle'] = $txtTitle;
                    } else {
                        $gridParameter['txtTitle'] = 'Dayal_Doc';
                    }
                }
                if (!array_key_exists('title', $gridParameter) && !empty($gridParameter['txtTitle'])) {
                    $gridParameter['title'] = $gridParameter['txtTitle'];
                }
                if (!array_key_exists('txtHeaderLeft', $gridParameter)) {
                    $gridParameter['txtHeaderLeft'] = '';
                }
                if (!array_key_exists('txtHeaderRight', $gridParameter)) {
                    $gridParameter['txtHeaderRight'] = '';
                }
                if (!array_key_exists('txtFooterLeft', $gridParameter)) {
                    $gridParameter['txtFooterLeft'] = "Prepared by eSaarthi Version 1.0<br> Printed  on " . date('d-M-Y H:i:s');
                }
                if (!array_key_exists('txtFooterRight', $gridParameter)) {
                    $gridParameter['txtFooterRight'] = 'Page {PAGENO} of {nbpg}';
                }
                $gridParameter['exportConfig'] = $this->getExportConfiguration($gridParameter);
            }
//            echo '<pre>';print_r($gridParameter);exit;
            unset($gridParameter['txtTitle']);
            unset($gridParameter['pdfOrientation']);
            unset($gridParameter['pdfSize']);
            unset($gridParameter['title']);
            unset($gridParameter['txtHeaderLeft']);
            unset($gridParameter['txtHeaderRight']);
            unset($gridParameter['txtFooterLeft']);
            unset($gridParameter['txtFooterRight']);
            unset($gridParameter['arrMargin']);
//            unset($gridParameter['criteria']);

            if (!array_key_exists('condensed', $gridParameter)) {
                $gridParameter['condensed'] = true;
            }
            if (!array_key_exists('responsive', $gridParameter)) {
                $gridParameter['responsive'] = false;
            }
            if (!array_key_exists('hover', $gridParameter)) {
                $gridParameter['bootstrap'] = true;
                $gridParameter['hover'] = true;
            }

            if ($GLOBALS['beforeHead'] == 0) {
                unset($gridParameter['beforeHeader']);
            }
            unset($gridParameter['befHead']);
            unset($gridParameter['hideTools']);

            if (!array_key_exists('pageSummaryRowOptions', $gridParameter)) {
                $gridParameter['pageSummaryRowOptions'] = ['class' => 'kv-page-summary info text-right', 'style' => 'white-space:no-wrap;color:#0778f9;'];
            }
            if (!array_key_exists('headerRowOptions', $gridParameter)) {
                $gridParameter['headerRowOptions'] = ['class' => 'kv-page-summary info text-right', 'style' => 'white-space:no-wrap;'];
            }

            // 
            /**
             * Searching in Array Data Provider with the help of array filter 
             * @author Shubham Rastogi
             * @since 26-Oct-2017
             */
//            if (($gridParameter['dataProvider']->className() == ArrayDataProvider::className()) && !empty($gridParameter['filterModel'])) {
////                echo '111';exit;
//                $arrDatas = [];
//                $arrData = $gridParameter['dataProvider']->allModels;
//                $arrParams = Yii::$app->request->queryParams;
//                    
//                if (!empty($arrParams) && !isset($gridParameter['enableGridFilter'])):
//                    $keys = [];
//                    $resultFromUrl = false;
//                    foreach ($arrParams as $paramKey => $paramValue) {
//                        $arrDatas = [];
//                        $arrRemoveAttributes = [
//                            '_csrf',
//                            'page' ,
//                            'pageSize' ,
//                            'per-page',
//                            '_toga654c069'                            
//                        ];
//                        if(!empty($gridParameter['skipColumnsFromFilter'])):
//                            $arrRemoveAttributes = array_merge($arrRemoveAttributes, $gridParameter['skipColumnsFromFilter']);
//                        endif;
//
//                        if (!empty($paramValue) && !in_array($paramKey, $arrRemoveAttributes) ):
//                            if(!is_array($paramValue)):
//                                $arrKeysToKeep = preg_grep('/\b' .trim(preg_quote($paramValue),'\'') . '/i', ArrayHelper::getColumn($arrData, $paramKey, true));
//                            else:
//                                continue;
//                            endif;
//                            if ($resultFromUrl):
//                                $keys = array_intersect($keys, array_keys($arrKeysToKeep));
//                            else:
//                                $keys = ArrayHelper::merge($keys, array_keys($arrKeysToKeep));
//                            endif;
//                            $resultFromUrl = true;
//                        endif;
//
//                        if (!empty($keys) && $resultFromUrl):
//
//                            foreach ($keys as $key) {
//                                $arrDatas[$key] = $arrData[$key];
//                            }
//                            $arrData = $arrDatas; 
//                            elseif (empty($keys) && $resultFromUrl):
//                            $arrData = [];
//                        endif;
//                    }
//
//                endif;
//                $gridParameter['dataProvider']->allModels = $arrData;
//            }
//            unset($gridParameter['beforeHeader']);
                if(!empty($gridParameter['beforeHeader'])):
                    $arrBefHeader = ArrayHelper::getColumn($gridParameter['beforeHeader'], function($element){
                        return array_filter(ArrayHelper::getColumn($element['columns'],'content'));
                    });
                    if(empty(array_filter($arrBefHeader))):
                        unset($gridParameter['beforeHeader']);
                    endif;
                endif;
                if(!empty($gridParameter['skipColumnsFromFilter'])){
                    unset($gridParameter['skipColumnsFromFilter']);
                }
                if(isset($gridParameter['enableGridFilter'])){
                    unset($gridParameter['enableGridFilter']);
                }
            echo KartikGridView::widget($gridParameter);
        }

        /**
         * @author Nishant Bhardwaj
         * @tutorial Shubham
         * @param type $gridParameter
         */
        public function setBasicAttributes($gridParameter) {
            $arrParams = Yii::$app->request->queryParams;


            //Setting Userwise pagination
            if (!empty($arrParams['pageSize'])) {
                $intPageSize = $arrParams['pageSize'];
                $gridParameter['dataProvider']->pagination->pageSize = $intPageSize;
            }

            if (!array_key_exists('hideTools', $gridParameter) || (!$gridParameter['hideTools'])) {
                //DEFAULT COMMON FUNCTIONALITIES:
                if (!array_key_exists('panel', $gridParameter)) {
                    $gridParameter['panel']['footer'] = false;
                }
                if (!array_key_exists('panelTemplate', $gridParameter)) {
                    if (!array_key_exists('toggleDataOptions', $gridParameter)) {
                        $gridParameter['toggleDataOptions'] = [
                            'all' => [
                                'label' => Yii::t('app', 'All Records'),
                                'class' => '',
                                'title' => 'Show all data'
                            ],
                            'page' => [
                                'label' => Yii::t('app', 'Page Wise'),
                                'class' => '',
                                'title' => 'Show first page data'
                            ],
                        ];
                    }
                    if (!array_key_exists('pageSizes', $gridParameter)) {
                        $sizeArray = array(20, 50, 100, 150);
                    } else {
                        $sizeArray = $gridParameter['pageSizes'];
                        unset($gridParameter['pageSizes']);
                    }
                    $strPageList = '';
                    foreach ($sizeArray as $pageCount) {
                        $url = Url::current();
                        $arrParams = $_GET;
                        $queryParams = '';
                        if (!empty($arrParams)):
                            $url .= '&';
                            $url .= 'pageSize=' . $pageCount;
                        else:
                            $url .= '?';
                            $url .= 'pageSize=' . $pageCount;
                        endif;

                        $strPageList .= '<li class="' . (!empty($_GET['pageSize']) && $_GET['pageSize'] == $pageCount ? 'active' : '' ) . '"><a href="' . Url::to($url) . '">' . $pageCount . ' Records</a></li>';
                    }

//                    $arrParams = Yii::$app->request->queryparams;
//                    $orientationSelected=" ";
//                    $pageSizeSelected=" ";
//                    if(!empty($arrParams)){
//                        $orientationSelected = $arrParams['orientation'];
//                        $pageSizeSelected = $arrParams['pdfPageSize'];
//                    }

                    if (!array_key_exists('panelBeforeTemplate', $gridParameter) || array_key_exists('panelBeforeTemplate', $gridParameter)) {

//                        $urlConfig = '<a 
//                            class="view" 
//                            href="javascript:void(0);" 
//                            title="" 
//                            data-url="/Saarthi/Sales/index.php/order/pending-orders?id=26&amp;intOrderId=9" 
//                            data-header="<h4 class=\'modal-title\' id=\'myModalLabel\'>Pending Orders</h4>" 
//                            data-original-title="Pending Orders">Set Export Configuration</a>';
//                    <div class="configuration">'.$urlConfig.'</div>       
//                    $arrPageSize = ['A3','A4','Letter','Legal','Folio','Tabloid'];
//                    $arrOrientation = ['Landscape','Portrait'];
//                    
////                    echo "<p class='control-label pdf'>".Yii::t('app','Page Format (pdf)').'</p>';
//                    $gridParameter['panelBeforeTemplate'] ="<p class='control-label pdf'>".Yii::t('app','Page Size').'</p>';
//                    $gridParameter['panelBeforeTemplate'] .= Select2::widget([
//                            'name' => 'pdfPageSize',
//                            'value' => $pageSizeSelected,
//                            'data' => $arrPageSize,
//                            'options' => ['multiple' => false, 'placeholder' => 'Select Page-Size']
//                        ]);
//                    
//                    $gridParameter['panelBeforeTemplate'] .="<p class='control-label pdf'>".Yii::t('app','Orientation').'</p>';
//                    $gridParameter['panelBeforeTemplate'] .= Select2::widget([
//                            'name' => 'orientation',
//                            'value' => $orientationSelected,
//                            'data' => $arrOrientation,
//                            'options' => ['multiple' => false, 'placeholder' => 'Select Orientation']
//                        ]);
//                    
//                    
//                    $gridParameter['panelBeforeTemplate'] .= "<p class='control-label pdf'>".Yii::t('app','Margin(Left)').'</p>'.Html::textInput([
//                        'name' =>'leftMargin',
//                        'value' => '', 
//                        'options' =>''
//                    ]);
//                    
//                    $gridParameter['panelBeforeTemplate'] .= "<p class='control-label pdf'>".Yii::t('app','Margin(Right)').'</p>'.Html::textInput('rightMargin', '', '');
//                    
//                    $gridParameter['panelBeforeTemplate'] .= "<p class='control-label pdf'>".Yii::t('app','Margin(Top)').'</p>'.Html::textInput('topMargin', '', '');
//                    
//                    $gridParameter['panelBeforeTemplate'] .= "<p class='control-label pdf'>".Yii::t('app','Margin(Bottom)').'</p>'.Html::textInput('bottomMargin', '', '');
                        $panelBeforeTemplate = '';
                        if (!empty($gridParameter['panelBeforeTemplate'])):
                            $panelBeforeTemplate = $gridParameter['panelBeforeTemplate'];
                        endif;
                        $gridParameter['panelBeforeTemplate'] = $panelBeforeTemplate . '
                            
                    <div class="text-right">{export} <span class="dropdown">
                    <button  type="button" data-toggle="dropdown" class="btn btn-default fa fa-file-text" style="height:35px;width:130px"> Page Length
                        <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        ' . $strPageList . '
                        <li>{toggleData}</li>
                    </ul>
                    </span></div>';

                        if (array_key_exists('pagination', $gridParameter)) {
                            if ($gridParameter['pagination'] == '') {
                                $gridParameter['panelBeforeTemplate'] = '<div class="text-right">{export} <span class="dropdown">

                            <ul class="dropdown-menu">
                                ' . $strPageList . '
                                <li>{toggleData}</li>
                            </ul>
                            </span></div>';
                            }
                            unset($gridParameter['pagination']);
                        }
                    }

                    $gridParameter['panelTemplate'] = '{panelBefore}{items}{pager}{summary}';
                }
            }
            if (!array_key_exists('layout', $gridParameter)) {
                $gridParameter['layout'] = "{items}\n{pager}\n{summary}";
            }
            if (!array_key_exists('floatHeader', $gridParameter)) {
                $gridParameter['floatHeader'] = true;
            }
            /*
             'pager' =>[
                'firstPageLabel' => 'First',
                'lastPageLabel'  => 'Last'
            ],
             */
            if (!array_key_exists('pager', $gridParameter)) {
                $gridParameter['pager'] = [
                    'firstPageLabel' => 'First',
                    'lastPageLabel'  => 'Last'
                ];
            }
            return $gridParameter;
        }

        public function applyAggregateFunction($arrGridColumns) {
            if (array_key_exists('aggregateFunc', $arrGridColumns)):
                $arrGridColumns['pageSummaryFunc'] = $arrGridColumns['aggregateFunc'];
                $arrGridColumns['pageSummary'] = true;
            endif;
            return $arrGridColumns;
        }

        /**
         * @author Nishant Bhardwaj
         * @param type $arrGridColumns
         * @param type $gridParameter
         * @param type $preHeaderCount
         * @param type $checkFlag
         * @param type $size
         * @return type
         */
        public function generateHeader($arrGridColumns, &$gridParameter, $preHeaderCount, $checkFlag, $size) {
            if (!array_key_exists('parentHeader', $arrGridColumns)) {
                $gridParameter['beforeHeader'][0]['columns'][$preHeaderCount]['content'] = '';
            } else {
                $gridParameter['beforeHeader'][0]['columns'][$preHeaderCount]['content'] = $arrGridColumns['parentHeader'];
            }
            //Header is for a dynamic generated columns
            if ($checkFlag == 0) {
                $gridParameter['beforeHeader'][0]['columns'][$preHeaderCount]['options']['colspan'] = $size;
            } else if ($checkFlag == 1) {
                $gridParameter['beforeHeader'][0]['columns'][$preHeaderCount]['options']['colspan'] = 1;
                $GLOBALS['last'] += 1;
            }
            $gridParameter['beforeHeader'][0]['columns'][$preHeaderCount]['options']['class'] = 'text-center';
            return $arrGridColumns;
        }

        /**
         * @author Nishant Bhardwaj
         * @param type $arrColumns
         * @param boolean $gridParameter
         * @param type $preHeaderCount
         * @return type
         */
        public function generateColumnQuery($arrColumns, &$gridParameter, $preHeaderCount) {
            $arrQueryColumns = $arrColumns['gridCol'];
            $arrParams = Yii::$app->request->queryParams;
            $arrQueryColumns = array_map('strval', $arrQueryColumns);
            $GLOBALS['arrIndex'] = array_keys($arrQueryColumns);
            $size = sizeof($arrQueryColumns);
            $arrModifiedColumns = [];
            unset($arrModifiedColumns['parentHeader']);
            unset($arrModifiedColumns['gridCol']);
            unset($arrColumns['gridCol']);
            unset($arrColumns['parentHeader']);


            foreach ($GLOBALS['arrIndex'] as $colIterator) {
                $arrModifiedColumns[$colIterator]['attribute'] = $arrQueryColumns[$colIterator];
//                echo gettype($arrModifiedColumns[$colIterator]['attribute']);exit;
//        echo'<pre>';print_r($arrModifiedColumns);exit;
                if ($gridParameter['dataProvider']->className() == ArrayDataProvider::className()) {

                    if (!array_key_exists('search', $gridParameter) || $gridParameter['search']) {
//                        echo '<pre>';print_r((!empty($arrParams[$arrQueryColumns[$colIterator]])?$arrParams[$arrQueryColumns[$colIterator]]:''));exit;
                        $arrModifiedColumns[$colIterator]['filter'] = '<input class="form-control" name="' . $arrQueryColumns[$colIterator] . '" value="' . (!empty($arrParams[$arrQueryColumns[$colIterator]]) ? $arrParams[$arrQueryColumns[$colIterator]] : '') . //$gridParameter['filterModel']['attributes']['int_product_id'] 
                                '" type="text">';
                        $arrModifiedColumns[$colIterator]['value'] = $arrQueryColumns[$colIterator];
                    }
                }
                $arrModifiedColumns[$colIterator] = ArrayHelper::merge($arrModifiedColumns[$colIterator], $arrColumns);

                if (array_key_exists('pageSummaryFunc', $arrModifiedColumns[$colIterator])) {
                    $arrModifiedColumns[$colIterator]['pageSummaryFunc'] = ['decimal', 2];
                }

                //RECURSION FUNCTION FOR EACH COLUMN
                if (array_key_exists('aggregateFunc', $arrModifiedColumns[$colIterator])) {
                    $gridParameter['showPageSummary'] = true;
                    $arrModifiedColumns[$colIterator]['pageSummary'] = true;
                    $arrModifiedColumns[$colIterator]['pageSummaryFunc'] = $arrModifiedColumns[$colIterator]['aggregateFunc'];
                    unset($arrModifiedColumns[$colIterator]['aggregateFunc']);
                    if (!array_key_exists('format', $arrModifiedColumns[$colIterator])) {
                        $arrModifiedColumns[$colIterator]['format'] = ['decimal', 2];
                    }
                }
            }

            return $arrModifiedColumns;
        }

        /**
         * @author Nishant Bhardwaj
         * @param type $gridParameter
         * @param type $columnKey
         * @param type $arrColumn
         * @param type $preHeaderCount
         * @return type
         */
        public function setColumnAttributes(&$gridParameter, $columnKey, $arrColumn, $preHeaderCount) {
            $headCount = 0;
            if (!is_array($arrColumn)):
                return $arrColumn;
            endif;

//              if(array_key_exists('headerOptions', $arrColumn)){
//                    echo '<pre>';print_r($arrColumn);exit;
////                    $arrGridColumns['headerOptions']=['style' => "width: 50%; vertical-align: right;text-align: right !important; color: red !important;"];
//                }

            $arrParams = Yii::$app->request->queryParams;
            if (array_key_exists('attribute', $arrColumn) && $gridParameter['dataProvider']->className() == ArrayDataProvider::className() && array_key_exists('filterModel', $gridParameter) && !is_object($gridParameter['filterModel'])) {
                

//                $arrColumn['filter'] = Html::input('string', $arrColumn['attribute']);
                $arrColumn['filter'] = '<input class="form-control" name="' . $arrColumn['attribute'] . '" value="' . (!empty($arrParams[$arrColumn['attribute']]) ? $arrParams[$arrColumn['attribute']] : '') . //$gridParameter['filterModel']['attributes']['int_product_id'] 
                        '" type="text">';
                $arrColumn['value'] = $arrColumn['attribute'];
            }

            if (!array_key_exists('pageSummaryOptions', $arrColumn) && (!array_key_exists('class', $arrColumn))) {
                $arrColumn['pageSummaryOptions'] = ['class' => 'kv-page-summary info text-right', 'style' => 'column-span: all;!important'];
                $gridParameter['footerRowOptions'] = ['style' => 'color:red;'];
            }
            $arrColumnModifiedGenerate = $arrColumn;
            if (array_key_exists('serial', $arrColumn)) {
                $arrColumnModifiedGenerate['class'] = "kartik\grid\SerialColumn";
                if (array_key_exists('aggregateName', $arrColumn)) {
                    $gridParameter['showPageSummary'] = true;
                    $arrColumnModifiedGenerate['pageSummary'] = $arrColumn['aggregateName'];
                    $arrColumnModifiedGenerate['format'] = ['decimal', 2];
                }
                unset($arrColumnModifiedGenerate['parentHeader']);
                unset($arrColumnModifiedGenerate['aggregateName']);
                unset($arrColumnModifiedGenerate['serial']);
            }
            //GENERATE TABLE COLUMNS:
            if (array_key_exists('gridCol', $arrColumn) && (!empty($arrColumn['gridCol']))) {
                if ($GLOBALS['headCount'] == 0) {
                    $arrColumn = $this->generateHeader($arrColumn, $gridParameter, $preHeaderCount, 0, count($arrColumn['gridCol'])); //last 1 is size
                }
                unset($arrColumn['parentHeader']);
                $arrColumnModifiedGenerate = $this->generateColumnQuery($arrColumn, $gridParameter, $preHeaderCount, $headCount);

                $GLOBALS['flag'] = 2;
                unset($arrColumn['gridCol']);
            }
            if (array_key_exists('pageSummaryFunc', $arrColumn)) {
//            echo '<pre>';print_r($arrColumn);exit; //14-Oct-2017
                if (!array_key_exists('format', $arrColumn)) {
                    $arrColumnModifiedGenerate['format'] = ['decimal', 2];
                }
                if (!array_key_exists('headerOptions', $arrColumn)) {
                    $arrColumnModifiedGenerate['headerOptions'] = ['style' => 'vertical-align: right;text-align: right;'];
                }
                if (!array_key_exists('contentOptions', $arrColumn)) {
                    $arrColumnModifiedGenerate['contentOptions'] = ['style' => 'vertical-align: right;text-align: right;'];
                }
            }
            if (array_key_exists('aggregateFunc', $arrColumn)) {
                $gridParameter['showPageSummary'] = true;
                $arrColumnModifiedGenerate = $this->applyAggregateFunction($arrColumnModifiedGenerate);
                unset($arrColumnModifiedGenerate['aggregateFunc']);
                if (!array_key_exists('format', $arrColumn)) {
                    $arrColumnModifiedGenerate['format'] = ['decimal', 2];
                }
                if (!array_key_exists('headerOptions', $arrColumn)) {
                    $arrColumnModifiedGenerate['headerOptions'] = ['style' => 'width: 10%; vertical-align: middle;text-align: center;'];
                }
                if (!array_key_exists('contentOptions', $arrColumn)) {
                    $arrColumnModifiedGenerate['contentOptions'] = ['style' => 'vertical-align: right;text-align: right;'];
                }
            }
//            echo '<pre>';print_r($arrColumnModifiedGenerate['attributes']);exit;
//            if (array_key_exists('befHead', $arrColumnModifiedGenerate)) { //manual //TODO: By Nishant Bhardwaj
//                $arrColumnModifiedGenerate = $this->generateHeader($arrColumnModifiedGenerate, $gridParameter, $preHeaderCount, 1, 1); //last 1 is size
//                unset($arrColumnModifiedGenerate['parentHeader']);
//            }

            return $arrColumnModifiedGenerate;
        }

        public function setDataProviderAttributes(&$gridParameter) {
            if (array_key_exists('dataProvider', $gridParameter) && (is_object($gridParameter['dataProvider']))) {
                if ($gridParameter['dataProvider']->className() == 'yii\data\ArrayDataProvider') {
//                    if (!array_key_exists('search', $gridParameter) || $gridParameter['search']) {
////                        echo '<pre>';print_r((!empty($arrParams[$arrQueryColumns[$colIterator]])?$arrParams[$arrQueryColumns[$colIterator]]:''));exit;
//                        $arrModifiedColumns[$colIterator]['filter'] = '<input class="form-control" name="' . $arrQueryColumns[$colIterator] . '" value="' . (!empty($arrParams[$arrQueryColumns[$colIterator]])?$arrParams[$arrQueryColumns[$colIterator]]:'').//$gridParameter['filterModel']['attributes']['int_product_id'] 
//                                '" type="text">';
//                        $arrModifiedColumns[$colIterator]['value'] = $arrQueryColumns[$colIterator];
//                        
//                    }
                    if (!empty($gridParameter['dataProvider']->allModels)) {

//                        $queryColumnsSort = array_keys($gridParameter['dataProvider']->allModels[0]);
//                        if(array_key_exists('filterModel', $gridParameter)) {
                        $queryColumnsSort = array_keys($gridParameter['dataProvider']->allModels);
//                        }
//                        echo '<pre>';print_r($gridParameter['dataProvider']->allModels);exit;
//                    echo '<pre>';print_r($queryColumnsSort);exit;
                        foreach ($queryColumnsSort as $column) {
                            $arrColumnsToSort[$column] = [
                                'asc' => [$column => SORT_ASC],
                                'desc' => [$column => SORT_DESC],
                            ];
                        }
//                         echo '<pre>';print_r($gridParameter['dataProvider']->allModels);exit;
                        $gridParameter['dataProvider']->setSort(['attributes' => $arrColumnsToSort]);
                    }
                }
            }
        }

        /**
         * 
         * @param type $gridParameter
         * @return type
         */
        private function getExportConfiguration($gridParameter) {

            $pdfOrientation = 'P';

            if (array_key_exists('pdfOrientation', $gridParameter)) {
                if ($gridParameter['pdfOrientation'] == 0) {
                    $pdfOrientation = 'L';
                }
            }


//            if(array_key_exists('criteria', $gridParameter)){
//                $criteria= $gridParameter['criteria'];
//            }
            $pageFormat = "A4";
            if (array_key_exists('pdfSize', $gridParameter)) {
                $pageFormat = $gridParameter['pdfSize'];
            }
            // Setting Page Margins for PDF Export 
            // Default Margins
            $arrMargin = [
                'marginTop' => 0,
                'marginBottom' => 15,
                'marginRight' => 15,
                'marginLeft' => 20
            ];
//            if(array_key_exists('arrMargin', $gridParameter)){
//                $arrMargins = $gridParameter['arrMargin'];
////                $arrMargin = ArrayHelper::merge($arrMargin,$arrMargins);
//                foreach ($arrMargins as $key=>$val){
////                    echo $key."->".$val."</br>";
//                    if($val!=" " || !empty($val)){
//                        $arrMargins[$key] = $val;
//                    }
//                }
//            }
            // Setting PDF Export Headers and Footers
            $pdfHeader = [
                'L' => [
                    'content' => Yii::t('app', $gridParameter['txtHeaderLeft']),
                    'font-size' => 8,
                    'color' => '#333333',
                ],
                'C' => [
                    'content' => Yii::t('app', $gridParameter['txtTitle']),
                    'font-size' => 0,
                    'encodeLabel' => true,
                    'color' => '#333333'
                ],
                'R' => [
                    'content' => Yii::t('app', $gridParameter['txtHeaderRight']),
                    'font-size' => 8,
                    'color' => '#333333'
                ]
            ];


            $pdfFooter = [
                'L' => [
                    'content' => Yii::t('app', $gridParameter['txtFooterLeft']),
                    'font-size' => 8,
                    'font-style' => '',
                    'color' => '#999999'
                ],
                'R' => [
                    'content' => Yii::t('app', $gridParameter['txtFooterRight']),
                    'font-size' => 10,
                    'font-style' => '',
                    'font-family' => 'serif',
                    'color' => '#333333'
                ],
                'line' => true,
            ];
            $isFa = $gridParameter['export']['fontAwesome'];
            return [
                KartikGridView::CSV => [
                    'label' => Yii::t('app', 'CSV'),
                    'icon' => $isFa ? 'file-code-o' : 'floppy-open',
                    'iconOptions' => ['class' => 'text-primary'],
                    'showHeader' => true,
                    'showPageSummary' => true,
                    'showFooter' => true,
                    'showCaption' => true,
                    'filename' => Yii::t('app', $gridParameter['title'] . "_" . date('Ymd_His')),
                    'alertMsg' => Yii::t('app', 'The CSV export file will be generated for download.'),
                    'options' => ['title' => Yii::t('app', 'Comma Separated Values')],
                    'mime' => 'application/csv',
                    'config' => [
                        'colDelimiter' => ",",
                        'rowDelimiter' => "\r\n",
                    ]
                ],
                KartikGridView::EXCEL => [
                    'label' => Yii::t('app', 'Excel'),
                    'icon' => $isFa ? 'file-excel-o' : 'floppy-remove',
                    'iconOptions' => ['class' => 'text-success'],
                    'showHeader' => true,
                    'showPageSummary' => true,
                    'showFooter' => true,
                    'showCaption' => true,
                    'filename' => Yii::t('app', $gridParameter['title'] . "_" . date('Ymd_His')),
                    'alertMsg' => Yii::t('app', 'The EXCEL export file will be generated for download.'),
                    'options' => ['title' => Yii::t('app', 'Microsoft Excel 95+')],
                    'mime' => 'application/vnd.ms-excel',
                    'config' => [
                        'worksheet' => Yii::t('app', $gridParameter['txtTitle']),
                        'cssFile' => '',
                    ]
                ],
                KartikGridView::PDF => [
                    'label' => Yii::t('app', 'PDF'),
                    'icon' => $isFa ? 'file-pdf-o' : 'floppy-disk',
                    'iconOptions' => ['class' => 'text-danger'],
                    'showHeader' => true,
                    'showPageSummary' => true,
                    'showFooter' => true,
                    'showCaption' => true,
                    'filename' => Yii::t('app', $gridParameter['title'] . "_" . date('Ymd_His')),
                    'alertMsg' => Yii::t('app', 'The PDF export file will be generated for download.'),
                    'options' => ['title' => Yii::t('app', 'Portable Document Format')],
                    'mime' => 'application/pdf',
                    'config' => [
                        'mode' => 'c',
                        'format' => $pageFormat, //Potrait or Landscape
                        'destination' => 'D',
                        'orientation' => $pdfOrientation,
                        'marginTop' => 35 + $arrMargin['marginTop'],
                        'marginBottom' => $arrMargin['marginBottom'],
                        'marginRight' => $arrMargin['marginRight'],
                        'marginLeft' => $arrMargin['marginLeft'],
                        'cssInline' => '.kv-wrap{padding:20px;}' .
                        '.kv-align-center{text-align:center;}' .
                        '.kv-align-left{text-align:left; width:30px!important}' .
                        '.kv-align-right{text-align:right;}' .
                        '.kv-align-top{vertical-align:top!important;}' .
                        '.kv-align-bottom{vertical-align:bottom!important;}' .
                        '.kv-align-middle{vertical-align:middle!important;}' .
                        '.kv-page-summary{border-top:4px double #ddd;font-weight: bold;}' .
                        '.kv-table-footer{border-top:4px double #ddd;font-weight: bold;}' .
                        '.kv-table-caption{font-size:1.5em;padding:8px;border:1px solid #ddd;border-bottom:none;}
                            table.kv-grid-table thead tr th{font-size:14px;}
                            table.kv-grid-table tfoot tr td{font-size:12px;}
                            table.kv-grid-table tbody tr td{font-size:12px;}',
                        'methods' => [
                            'SetHeader' => [
                                ['odd' => $pdfHeader, 'even' => $pdfHeader]
                            ],
//                            'SetHTMLHeader'=> ,
                            'SetFooter' => [
                                ['odd' => $pdfFooter, 'even' => $pdfFooter]
                            ],
                        ],
                        'options' => [
                            'title' => Yii::t('app', $gridParameter['title']),
                            'subject' => Yii::t('app', 'PDF export generated by kartik-v/yii2-grid extension'),
                            'keywords' => Yii::t('app', 'saarthi')
                        ],
                        'contentBefore' => '',
                        'contentAfter' => ''
                    ]
                ],
            ];
        }

    }
    ?> 
</div>
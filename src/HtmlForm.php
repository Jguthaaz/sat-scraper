<?php

declare(strict_types=1);

namespace Blacktrue\Scraping;

use DOMDocument;

class HtmlForm
{
    public $xpathForm;
    public $htmlSource;

    /**
     * HTMLForm constructor.
     *
     * @param string $htmlSource
     * @param string $xpathForm
     */
    public function __construct(string $htmlSource, string $xpathForm)
    {
        $this->xpathForm = $xpathForm;
        $this->htmlSource = $htmlSource;
    }

    /**
     * @return array
     */
    public function getFormValues() : array
    {
        $inputValues = $this->readInputValues();     //OK
        $selectValues = $this->readSelectValues();    //OK

        $values = array_merge($inputValues, $selectValues);

        return $values;
    }

    /**
     * @return array
     */
    public function readInputValues() : array
    {
        return $this->readAndGetValues('input');
    }

    /**
     * @return array
     */
    public function readSelectValues() : array
    {
        return $this->readAndGetValues('select');
    }

    /**
     * @param $element
     *
     * @return array
     */
    public function readAndGetValues($element) : array
    {
        $old = libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML($this->htmlSource);

        libxml_use_internal_errors($old);
        $sxe = simplexml_import_dom($dom);

        $document = $sxe;
        $inputValues = [];

        $xpath = $document->xpath('//'.$this->xpathForm.'/'.$element);

        foreach ($xpath as $input) {
            $name = (string) $input->attributes()->{'name'};
            $value = (string) $input->attributes()->{'value'};
            $inputValues[$name] = $value;
        }

        return $inputValues;
    }
}

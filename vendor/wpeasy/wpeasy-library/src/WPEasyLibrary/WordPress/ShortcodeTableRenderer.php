<?php


namespace WPEasyLibrary\WordPress;


class ShortcodeTableRenderer
{
    static function render($shortcodes, $echo = false)
    {
        $rows = '';
        foreach ($shortcodes as $shortcode=>$details) {
            $attsStr = '';
            if(is_array($details['attributes']) && count($details['attributes'])){
                $attsStr = self::renderAttsString($details['attributes']);
            }
            $attTable = self::renderAttributeTable($details['attributes']);
            $sc = $details['enclosing'] ? '[' . $shortcode . " $attsStr ]CONTENT[/" . $shortcode . ']' : "[$shortcode $attsStr]";
            $rows.= '<tr>';
            $rows.= '<td><span class="d-block wpe_clickToCopy">' . $sc . '</span></td>';
            $rows.= '<td>' . $details['description'] . '</td>';
            $rows.= '<td>' . $attTable . '</td>';
            $rows.= '</tr>';
        }

        $out = <<<OUT
<table class="table table-bordered table-striped">
<thead>
<tr>
<th>Shortcode</th>
<th>Description</th>
<th>Attributes</th>
</tr>
</thead>
<tbody>
{$rows}
</tbody>
</table>
OUT;

        if($echo) echo $out;

        return $out;
    }


    /**
     * Renders attribute string with default values
     * @param $shortcodeAtts
     * @return string
     */
    static function renderAttsString($shortcodeAtts)
    {
        $str = '';
        foreach ($shortcodeAtts as $att=>$detail){
            if(@$detail['showInExample'] === true){
                $str.= " $att=\"" . $detail['default'] . "\"";
            }

        }

        return $str;
    }

    static function renderAttributeTable($shortcodeAtts)
    {
        if(!is_array($shortcodeAtts) || count($shortcodeAtts) === 0 ) return 'NA';

        $rows = '';
        foreach ($shortcodeAtts as $att=>$detail){
            $rows.= '<tr>';
            $rows.= "<td>" . $att ."</td>";
            $rows.= '<td>"' . $detail['default'] . '"</td>';
            $rows.= "<td>" . $detail['description'] . "</td>";
            $rows.= "<td>" . $detail['values'] . "</td>";
            $rows.= '</tr>';
        }

        $out = <<<OUT
<table class="table table-striped table-sm table-dark">
<thead>
<th>Att</th>
<th>Default</th>
<th>Description</th>
<th>Values</th>
</thead>
<tbody>
{$rows}
</tbody>
</table>
OUT;

        return $out;

    }
}
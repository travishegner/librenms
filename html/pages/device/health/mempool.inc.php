<?php

$graph_type = 'mempool_usage';

echo "<div style='margin-top: 5px; padding: 0px;'>";
echo '<table width=100% cellpadding=6 cellspacing=0>';

$i = '1';

// FIXME css alternating colours
foreach (dbFetchRows('SELECT * FROM `mempools` WHERE device_id = ?', array($device['device_id'])) as $mempool) {
    if (!is_integer($i / 2)) {
        $row_colour = $list_colour_a;
    }
    else {
        $row_colour = $list_colour_b;
    }

    if ($config['memcached']['enable'] === true) {
        $state = $memcache->get('mempool-'.$mempool['mempool_id'].'-state');
        if ($debug) {
            print_r($state);
        }

        if (is_array($state)) {
            $mempool = array_merge($mempool, $state);
        }

        unset($state);
    }

    $text_descr = rewrite_entity_descr($mempool['mempool_descr']);

    $mempool_url = 'graphs/id='.$mempool['mempool_id'].'/type=mempool_usage/';
    $mini_url    = 'graph.php?id='.$mempool['mempool_id'].'&amp;type='.$graph_type.'&amp;from='.$config['time']['day'].'&amp;to='.$config['time']['now'].'&amp;width=80&amp;height=20&amp;bg=f4f4f4';

    $mempool_popup  = "onmouseover=\"return overlib('<div class=list-large>".$device['hostname'].' - '.$text_descr;
    $mempool_popup .= "</div><img src=\'graph.php?id=".$mempool['mempool_id'].'&amp;type='.$graph_type.'&amp;from='.$config['time']['month'].'&amp;to='.$config['time']['now']."&amp;width=400&amp;height=125\'>";
    $mempool_popup .= "', RIGHT".$config['overlib_defaults'].');" onmouseout="return nd();"';

    $total = formatStorage($mempool['mempool_total']);
    $used  = formatStorage($mempool['mempool_used']);
    $free  = formatStorage($mempool['mempool_free']);

    $perc = round(($mempool['mempool_used'] / $mempool['mempool_total'] * 100));

    $background       = get_percentage_colours($percent);
    $right_background = $background['right'];
    $left_background  = $background['left'];

    echo "<tr bgcolor=$row_colour><td class=tablehead><a href='".$mempool_url."' $mempool_popup>".$text_descr."</a></td>
        <td width=90><a href='".$mempool_url."'  $mempool_popup><img src='$mini_url'></a></td>
        <td width=200><a href='".$mempool_url."' $mempool_popup>
        ".print_percentage_bar(400, 20, $perc, "$used / $total", 'ffffff', $left_background, $free, 'ffffff', $right_background).'
        </a></td>
        <td width=50>'.$perc.'%</td>
        </tr>';

    echo "<tr bgcolor='$row_colour'><td colspan=5>";

    $graph_array['id']   = $mempool['mempool_id'];
    $graph_array['type'] = $graph_type;

    include 'includes/print-graphrow.inc.php';

    echo '</td></tr>';

    $i++;
}//end foreach

echo '</table>';
echo '</div>';

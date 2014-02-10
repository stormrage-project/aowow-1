<?php
require_once('includes/alllocales.php');

// Для списка creatureinfo()
$npc_cols[0] = array('name', 'subname', 'minlevel', 'maxlevel', 'type', 'rank', 'A','H');
$npc_cols[1] = array('subname', 'minlevel', 'maxlevel', 'type', 'rank', 'minhealth', 'maxhealth', 'minmana', 'maxmana', 'mingold', 'maxgold', 'lootid', 'skinloot', 'pickpocketloot', 'spell1', 'spell2', 'spell3', 'spell4', 'A', 'H', 'mindmg', 'maxdmg', 'attackpower', 'dmg_multiplier', 'armor');

$type = array(
    '',
    LOCALE_TYPE_BEAST,
    LOCALE_TYPE_DRAGONKIN,
    LOCALE_TYPE_DEMON,
    LOCALE_TYPE_ELEMENTAL,
    LOCALE_TYPE_GIANT,
    LOCALE_TYPE_UNDEAD,
    LOCALE_TYPE_HUMANOID,
    LOCALE_TYPE_CRITTER,
    LOCALE_TYPE_MECHANICAL,
    LOCALE_TYPE_UNCATEGORIZED
);

$rank = array(
    LOCALE_RANK_NORMAL,
    LOCALE_RANK_ELITE,
    LOCALE_RANK_RARE_ELITE,
    LOCALE_RANK_BOSS,
    LOCALE_RANK_RARE
);

// Функция информации о создании
function creatureinfo2($Row, $level = 0)
{
	$creature = array(
		'entry'				=> $Row['entry'],
		'name'				=> str_replace(' (1)', LOCALE_HEROIC, localizedName($Row)), // FIXME
		'subname'			=> localizedName($Row, 'subname'),
		'minlevel'			=> $Row['minlevel'],
		'maxlevel'			=> $Row['maxlevel'],
		'react'				=> $Row['A'].','.$Row['H'],
		'type'				=> $Row['type'],
		'classification'	=> $Row['rank']
	);

    if ($level > 0)
        $creature['info'] = render_npc_tooltip($Row);

	return $creature;
}

// Функция информации о создании
function creatureinfo($id, $level = 0)
{
	global $DB;
	global $npc_cols;

	$row = $DB->selectRow('
			SELECT ?#, c.entry
			{
				, l.name_loc'.$_SESSION['locale'].' as `name_loc`
				, l.subname_loc'.$_SESSION['locale'].' as `subname_loc`
				, ?
			}
			FROM ?_factiontemplate, creature_template c
			{
				LEFT JOIN (locales_creature l)
				ON l.entry=c.entry AND ?
			}
			WHERE
				c.entry = ?d
				AND factiontemplateID = faction_A
			LIMIT 1
		',
		$npc_cols[0],
		($_SESSION['locale']>0)? 1: DBSIMPLE_SKIP,
		($_SESSION['locale']>0)? 1: DBSIMPLE_SKIP,
		$id
	);

	return creatureinfo2($row, $level);
}

function render_npc_tooltip(&$Row)
{
    global $type;
    global $rank;

    $x = '<table>';
    $x .= '<tr><td><b class="q">' . localizedName($Row) . '</b></td></tr>';
    $x .= '<tr><td>' . localizedName($Row, 'subname') . '</td></tr>';
    $x .= '<tr><td>Level ';

    if ($Row['rank'] == 3)
        $x .= '??';
    else
    {
        $x .= $Row['minlevel'];

        if ($Row['minlevel'] != $Row['maxlevel'])
            $x .= ' - ' . $Row['maxlevel'];
    }

    $x .= ' ' . $type[$Row['type']] . ' (' . $rank[$Row['rank']] . ')</td></tr>';
    $x .= '</table>';

    return $x;
}

?>

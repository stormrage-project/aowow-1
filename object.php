<?php

require_once('includes/allobjects.php');
require_once('includes/allitems.php');
require_once('includes/allcomments.php');
require_once('includes/allquests.php');
require_once('includes/allevents.php');

$smarty->config_load($conf_file, 'object');

// номер объекта;
$id = intval($podrazdel);

$cache_key = cache_key($id);

if(!$object = load_cache(OBJECT_PAGE, $cache_key))
{
	unset($object);

	// Данные об объекте:
	$object = array();
	$object = objectinfo($id, 1);

	// Начиниают квесты...
	$rows_qs = $DB->select('
		SELECT o.?#
		FROM gameobject_questrelation q, quest_template o
		WHERE
			q.id = ?d
			AND o.entry = q.quest
		',
		$quest_cols[2],
		$id
	);
	if($rows_qs)
	{
		$object['starts'] = array();
		foreach($rows_qs as $numRow=>$row)
			$object['starts'][] = GetQuestInfo($row, 0xFFFFFF);
	}
	unset($rows_qs);

	// Заканчивают квесты...
	$rows_qe = $DB->select('
		SELECT ?#
		FROM gameobject_involvedrelation i, quest_template q
		WHERE
			i.id = ?d
			AND q.entry = i.quest
		',
		$quest_cols[2],
		$id
	);
	if($rows_qe)
	{
		$object['ends'] = array();
		foreach($rows_qe as $numRow=>$row)
			$object['ends'][] = GetQuestInfo($row, 0xFFFFFF);
	}
	unset($rows_qe);

	// Положения объектофф:
	$object['position'] = position($object['entry'], 'gameobject');

	// Исправить type, чтобы подсвечивались event-овые объекты
	if ($object['position'])
		foreach ($object['position'] as $z => $zone)
			foreach ($zone['points'] as $p => $pos)
				if ($pos['type'] == 0 && ($events = event_find(array('object_guid' => $pos['guid']))))
				{
					$names = array_select_key(event_name($events), 'name');
					$object['position'][$z]['points'][$p]['type'] = 4;
					$object['position'][$z]['points'][$p]['events'] = implode(", ", $names);
				}

	save_cache(OBJECT_PAGE, $cache_key, $object);
}

global $page;
$page = array(
	'Mapper' => true,
	'Book' => $object['pagetext'] ? true : false,
	'Title' => $object['name'].' - '.$smarty->get_config_vars('Objects'),
	'tab' => 0,
	'type' => 2,
	'typeid' => $object['entry'],
	'path' => path(0, 5, $object['type'])
);

$smarty->assign('page', $page);

// Комментарии
$smarty->assign('comments', getcomments($page['type'], $page['typeid']));

// Количество MySQL запросов
$smarty->assign('mysql', $DB->getStatistics());

$smarty->assign('object', $object);
$smarty->display('object.tpl');

?>
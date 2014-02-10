<?php
header('Content-type: application/x-javascript');
require_once('configs/config.php');
require_once('includes/allutil.php');

// Для Ajax отключаем debug
$AoWoWconf['debug'] = false;
// Для Ajax ненужен реалм
$AoWoWconf['realmd'] = false;
// Настройка БД
require_once('includes/db.php');

// Параметры передаваемые скрипту
@list($what, $id) = explode('=', $_SERVER['QUERY_STRING']);
$id = intval($id);

$x = '';

switch($what)
{
	case 'npc':
		if (!$npc = load_cache(NPC_TOOLTIP, $id))
		{
			require_once('includes/allnpcs.php');
			$npc = creatureinfo($id, 1);
			save_cache(NPC_TOOLTIP, $id, $npc);
		}

		$x .= '$WowheadPower.registerNpc('.$id.', '.$_SESSION['locale'].',{';

		if ($npc['name'])
			$x .= 'name_'.$locales[$_SESSION['locale']].': \''.ajax_str_normalize($npc['name']).'\',';

		if ($npc['info'])
			$x .= 'tooltip_'.$locales[$_SESSION['locale']].': \''.ajax_str_normalize($npc['info']).'\'';

		$x .= '});';

		break;
	case 'object':
		if (!$object = load_cache(OBJECT_TOOLTIP, $id))
		{
			require_once('includes/allobjects.php');
			$object = objectinfo($id, 1);
            save_cache(OBJECT_TOOLTIP, $id, $object);
        }

        $x .= '$WowheadPower.registerObject('.$id.', '.$_SESSION['locale'].',{';

        if ($object['name'])
            $x .= 'name_'.$locales[$_SESSION['locale']].': \''.ajax_str_normalize($object['name']).'\',';

        if ($object['info'])
            $x .= 'tooltip_'.$locales[$_SESSION['locale']].': \''.ajax_str_normalize($object['info']).'\'';

        $x .= '});';

		break;
	case 'item':
		if(!$item = load_cache(ITEM_TOOLTIP, $id))
		{
			require_once('includes/allitems.php');
			$item = allitemsinfo($id, 1);
			save_cache(ITEM_TOOLTIP, $id, $item);
		}
		$x .= '$WowheadPower.registerItem('.$id.', '.$_SESSION['locale'].', {';
		if ($item['name'])
			$x .= 'name_'.$locales[$_SESSION['locale']].': \''.ajax_str_normalize($item['name']).'\',';
		if ($item['quality'])
			$x .= 'quality: '.$item['quality'].',';
		if ($item['icon'])
			$x .= 'icon: \''.ajax_str_normalize($item['icon']).'\',';
		if ($item['info'])
			$x .= 'tooltip_'.$locales[$_SESSION['locale']].': \''.ajax_str_normalize($item['info']).'\'';
		$x .= '});';
		break;
	case 'spell':
		if(!$spell = load_cache(SPELL_TOOLTIP, $id))
		{
			require_once('includes/allspells.php');
			$spell = allspellsinfo($id, 1);
			save_cache(SPELL_TOOLTIP, $id, $spell);
		}
		$x .= '$WowheadPower.registerSpell('.$id.', '.$_SESSION['locale'].',{';
		if ($spell['name'])
			$x .= 'name_'.$locales[$_SESSION['locale']].': \''.ajax_str_normalize($spell['name']).'\',';
		if ($spell['icon'])
			$x .= 'icon: \''.ajax_str_normalize($spell['icon']).'\',';
		if ($spell['info'])
			$x .= 'tooltip_'.$locales[$_SESSION['locale']].': \''.ajax_str_normalize($spell['info']).'\'';
		$x .= '});';
		break;
	case 'quest':
		if(!$quest = load_cache(QUEST_TOOLTIP, $id))
		{
			require_once('includes/allquests.php');
			$quest = GetDBQuestInfo($id, QUEST_DATAFLAG_AJAXTOOLTIP);
			$quest['tooltip'] = GetQuestTooltip($quest);
			save_cache(QUEST_TOOLTIP, $id, $quest);
		}
		$x .= '$WowheadPower.registerQuest('.$id.', '.$_SESSION['locale'].',{';
		if($quest['Title'])
			$x .= 'name_'.$locales[$_SESSION['locale']].': \''.ajax_str_normalize($quest['Title']).'\',';
		if($quest['tooltip'])
			$x .= 'tooltip_'.$locales[$_SESSION['locale']].': \''.ajax_str_normalize($quest['tooltip']).'\'';
		$x .= '});';
		break;
	default:
		break;
}

echo $x;

?>

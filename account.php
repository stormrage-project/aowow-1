<?php

// Загружаем файл перевода для smarty
$smarty->config_load($conf_file, 'account');

function GetRealIp()
{
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) { $ip=$_SERVER['HTTP_CLIENT_IP']; }
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { $ip=$_SERVER['HTTP_X_FORWARDED_FOR']; }
	else { $ip=$_SERVER['REMOTE_ADDR']; }
	return $ip;
}

// Создание аккаунта
if($_REQUEST['account'] == 'signup')
//if($_REQUEST['account'] == 'signup' && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['c_password']) && $AoWoWconf['register'] == true)
{
  /*header("HTTP/1.1 301 Moved Permanently");
  header("Location: http://valkyrie-wow.com/user/reg");
  exit();*/

    require_once('includes/recaptchalib.php');
    $privatekey = "6LeWKcASAAAAAOInaBRHkofj8uf_f-1aRcbkQPnH";
    $resp = recaptcha_check_answer ($privatekey,
                                        $_SERVER["REMOTE_ADDR"],
                                        $_POST["recaptcha_challenge_field"],
                                        $_POST["recaptcha_response_field"]);

    if (!$resp->is_valid) {
        $smarty->assign('signup_error', $smarty->get_config_vars('reCapcha_error'));
    } 
    else 
    {
        if (preg_match("/^[a-z]+([-_.]?[a-z0-9]+)*$/i", $_POST['username']) == 0)
        {
            $smarty->assign('signup_error', $smarty->get_config_vars('Wrong_username'));
        }
        elseif ($_POST['password'] != $_POST['c_password'])
        {
            $smarty->assign('signup_error', $smarty->get_config_vars('Different_passwords'));
        }
        else
        {
            // Существует ли уже такой пользователь?
            if($rDB->selectCell('SELECT Count(id) FROM account WHERE username=? LIMIT 1', $_POST['username']) == 1)
            {
                $smarty->assign('signup_error', $smarty->get_config_vars('Such_user_exists'));
            }
            else
            {
                $ip = GetRealIp();
                // Вроде все нормально, создаем аккаунт
                $success = $rDB->selectCell('
                        INSERT INTO account (username, sha_pass_hash, email, joindate, gmlevel, reg_ip)
                        VALUES (?, ?, ?, NOW(), 0, ?)
                    ',
                    strtoupper($_POST['username']),
                    strtoupper(create_usersend_pass($_POST['username'], $_POST['password'])),
                    (isset($_POST['email']))? $_POST['email'] : '',
                    $ip				
                );
                if($success > 0)
                    // Все отлично, авторизуем
                    $_REQUEST['account']='signin';
                else
                    // Неизвестная ошибка
                    $smarty->assign('signup_error', $smarty->get_config_vars('Unknow_error_on_account_create'));
            }
        }
    } 
}

if($_REQUEST['account'] == 'signin' && isset($_POST['username']) && isset($_POST['password']))
{
	$usersend_pass = create_usersend_pass($_POST['username'], $_POST['password']);
	$user = CheckPwd($_POST['username'], $usersend_pass);
	if($user == -1)
	{
		del_user_cookie();
		if(isset($_SESSION['username']))
			unset($_SESSION['username']);
		$smarty->assign('signin_error', $smarty->get_config_vars('Such_user_doesnt_exists'));
	}
	elseif($user == 0)
	{
		del_user_cookie();
		if(isset($_SESSION['username']))
			unset($_SESSION['username']);
		$smarty->assign('signin_error', $smarty->get_config_vars('Wrong_password'));
	}
	else
	{
		// Имя пользователя и пароль совпадают
		$_SESSION['username'] = $user['name'];
		$_SESSION['shapass'] = $usersend_pass;
		$_REQUEST['account'] = 'signin_true';
		$_POST['remember_me'] = isset($_POST['remember_me']) ? $_POST['remember_me'] : 'no';
		if($_POST['remember_me'] == 'yes')
		{
			// Запоминаем пользователя
			$remember_time = time() + 3000000;
			SetCookie('remember_me', $_POST['username'].$usersend_pass, $remember_time);
		}
		else
			del_user_cookie();
	}
}


switch($_REQUEST['account'])
{
	case '':
		// TODO: Настройки аккаунта (Account Settings)
		break;
	case 'signin_false':
	case 'signin':
        require_once('includes/recaptchalib.php');
        $publickey = "6LeWKcASAAAAAEk0meQUJaBeeEu43JLtsjOp-vrV";
		// Вход в систему
		$smarty->assign('register', $AoWoWconf['register']);
		$smarty->display('signin.tpl');
		break;
	case 'signup_false':
	case 'signup':
		// Регистрация аккаунта
		$smarty->display('signup.tpl');
		break;
	case 'signout':
		// Выход из пользователя
		unset($user);
		session_unset();
		session_destroy();
		$_SESSION = array();
		del_user_cookie();
	case 'signin_true':
	default:
		// На предыдущую страницу
		// Срабатывает при:
		//  1. $_REQUEST['account'] = 'signout' (выход)
		//  2. $_REQUEST['account'] = 'signok' (успешная авторизация)
		//  3. Неизвестное значение $_REQUEST['account']
		$_REQUEST['next'] = (isset($_REQUEST['next']))? $_REQUEST['next'] : '';
		if(($_REQUEST['next'] == '?account=signin') or ($_REQUEST['next'] == '?account=signup'))
			$_REQUEST['next']='';
		header('Location: ?'.$_REQUEST['next']);
		break;
}
?>

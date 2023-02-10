<?php

function registration(): bool{
	global $pdo;
	$login = !empty($_POST['login']) ? trim($_POST['login']) : '';
	$pass = !empty($_POST['pass']) ? trim($_POST['pass']) : '';
	$email = !empty($_POST['email']) ? trim($_POST['email']) : '';
	if(empty($login) || empty($pass) || empty($email)){
		$_SESSION['errors'] = 'Поля имя/пароль/email обязательны!';
		return false;
	}

	$res = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
	$res->execute([$email]);
	if($res->fetchColumn()){
		$_SESSION['errors'] = 'Данный email уже используется!';
		return false;
	}	

	$pass = password_hash($pass, PASSWORD_DEFAULT);
	$res = $pdo->prepare("INSERT INTO users (login, pass, email) VALUES(?,?,?)");
	if($res->execute([$login, $pass, $email])){
		$_SESSION['success'] = 'Успешная регистрация!';
		return true;
	}else{
		$_SESSION['errors'] = 'Ошибка регистрации!';
		return false;
	}
}

function get_message($id)
	{
		global $pdo;
		$res = $pdo->prepare("SELECT * FROM messages WHERE id = ?");
		$res->execute([$id]);
		return $res->fetch();
	}

function save_message($id, $message,$status){
	global $pdo;
	$query = $pdo->prepare("UPDATE messages SET message=?, status=?, admin_changed=? WHERE id=?");
	if($query->execute([$message, $status, 'изменен администратором', $id])){
		$_SESSION['success'] = 'Успешное изменение!';
		return true;
	}else{
		$_SESSION['errors'] = 'Ошибка изменения!';
		return false;
	}
}

function login(): bool
{
	global $pdo;
	$login = !empty($_POST['login']) ? trim($_POST['login']) : '';
	$pass = !empty($_POST['pass']) ? trim($_POST['pass']) : '';

	if(empty($login) || empty($pass)){
		$_SESSION['errors'] = 'Поля имя/пароль обязательны!';
		return false;
	}

	$res =$pdo->prepare("SELECT * FROM users WHERE login = ?");
	$res->execute([$login]);
	if(!$user = $res->fetch()){
		$_SESSION['errors'] = 'Имя/пароль введены неверно!';
		return false;
	}

	if(!password_verify($pass, $user['pass'])){
		$_SESSION['errors'] = 'Имя/пароль введены неверно!';
		return false;
	}else{
		$_SESSION['success'] = 'Вы успешно авторизовались!';
		$_SESSION['user']['name'] = $user['login'];
		$_SESSION['user']['email'] = $user['email'];
		$_SESSION['user']['id']=$user['id'];
		return true;
	}
}

function add_message(): bool
{
	global $pdo;
	$message = !empty($_POST['message']) ? trim($_POST['message']) : '';
	$image = !empty($_FILES['image'])?$_FILES['image']:'';

	if(!isset($_SESSION['user']['name'])){
		$_SESSION['errors'] = 'Необходимо авторизоваться!';
		return false;
	}

	if(empty($message)){
		$_SESSION['errors'] = 'Введите текст сообщения!';
		return false;
	}
	if(empty($image['name'])){
		$res = $pdo->prepare("INSERT INTO messages (name, email, message,image) VALUES (?,?,?,?)");
		if($res->execute([$_SESSION['user']['name'], $_SESSION['user']['email'], $message, $image['name']])){
			$_SESSION['success'] = 'Отзыв добавлено!';
			return true;
		}else{
			$_SESSION['errors'] = 'Ошибка!';
			return false;
		}
	}else{
		$image_name =$image['name'];
		$tmp_name = $image['tmp_name'];
		$allow_type = array('png', 'jpg','gif',);
		$size =$image['size'];
		$img_type = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
		$filename = strtolower(pathinfo($image_name, PATHINFO_FILENAME));
		$filename1 = $filename.date('sidmY').".".$img_type;
		$filename2 = preg_replace('/\s+/', '', $filename1);
		$destination = "images/".$filename2;
		if(in_array($img_type, $allow_type)){
			if($size <=1000000){
				move_uploaded_file($tmp_name, $destination);
				$res = $pdo->prepare("INSERT INTO messages (name, email, message,image) VALUES (?,?,?,?)");
				if($res->execute([$_SESSION['user']['name'], $_SESSION['user']['email'], $message, $filename2])){
					$_SESSION['success'] = 'Отзыв добавлен!';
					return true;
				}else{
					$_SESSION['errors'] = 'Ошибка!';
					return false;
				}
			}else{
				$_SESSION['errors'] = 'Размер файла превышает 1мб!';
				return false;
			}
		}else{
			$_SESSION['ERRORS'] = "Недоступный формат файла! Доступные форматы файла(gif, jpg, png)";
			return false;
		}
		
	}

}

function get_messages(): array
{
	global $pdo;
	$res = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
	return $res->fetchAll();
}
function get_messages_asc(): array{
	global $pdo;
	$res = $pdo->query("SELECT * FROM messages");
	return $res->fetchAll();
}
function get_messages_by_name(): array{
	global $pdo;
	$res = $pdo->query("SELECT * FROM messages ORDER BY name");
	return $res->fetchAll();
}
function get_messages_by_email(): array
{
	global $pdo;
	$res = $pdo->query("SELECT * FROM messages ORDER BY email");
	return $res->fetchAll();
}



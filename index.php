<?php
session_start();
header('Content-Type: text/html; charset = utf-8');
error_reporting(E_ALL);

//отправка письма
function send_mail($message){
	$mail_to = $stringEmail;// почта, на которую придет письмо
	$subject = 'Заявка с сайта www.домен.ру';// тема письма
	$headers= 'MIME-Version: 1.0\r\n';// заголовок письма
	$headers .= 'Content-type: text/html; charset=utf-8\r\n'; // кодировка письма
	$headers .= 'From: Тестовое письмо <no-reply@test.com>\r\n'; // от кого письмо
	mail($mail_to, $subject, $message, $headers);// отправляем письмо 
}
//вывод уведомлений
function send_notice($msg, $param = 2) {
	if ($param == 1) {
		$show = '<div class="message-green"><span class="message-text">'.$msg.'</span></div>';//зеленый фон. успешн.
	}
	elseif ($param == 0) {
		$show = '<div class="message-red"><span class="message-text">'.$msg.'</span></div>';//красный фон. ошибка
	}
	else{
		$show = '<div class="message-blue"><span class="message-text">'.$msg.'</span></div>';//синий фон. информация
	}
	echo $show.'<br>';
}
/////////////////////////////////////////////////////////////

if (isset($_SESSION['user_name'])) {
	$msg = $_SESSION['user_name'].', ваше сообщение успешно отправлено! <a href="/">Отправить ещё раз </a>';//выведем сообщение об успехе
	send_notice($msg,1);
	$show_form = 0; //переменная для показа/скрытия формы
    unset($_SESSION['user_name']);
	session_destroy();
}
else{
	$show_form = 1; //переменная для показа/скрытия формы
}

$questions = [
    'Сколько будет 1+2?' => '3',
    'Сколько будет 5+2?' => '7',
    'Напишите буквами цифру 9' => 'девять',
    'Напишите цифрами число двенадцать' => '12'
];

$question = array_rand($questions, 1);

	if(isset($_POST['submit'])){
		$errors = array(); // массив для ошибок
		// проверяем корректность полей

		if($_POST['user_name'] == "") {
			$errors[] = 'Поле "Ваше имя" не заполнено!';
		}
		if($_POST['user_email'] == "") {
			$errors[] = 'Поле "Ваш e-mail" не заполнено!';
		}
		if($_POST['user_text'] == "") {
			$errors[] = 'Поле "Текст сообщения" не заполнено!';
		}
        if($_POST['question'] == "") {
			$errors[] = 'Вы не ответили на вопрос!';
		}
        if(!empty($_POST['question']) && mb_strtolower($_POST['question']) !== $questions[$_POST['questions']]) {
			$errors[] = 'Вы неправильно ответили на вопрос!';
		}

		// если форма без ошибок, то
		if(empty($errors)){
			$show_form = 0; //переменная для показа/скрытия формы
			$message  = 'Имя пользователя: ' . $_POST['user_name'] . ' | ';//собираем данные из формы
			$message .= 'E-mail пользователя: ' . $_POST['user_email'] . ' | ';
			$message .= 'Текст письма: ' . $_POST['user_text'];		
			send_mail($message); //отправим письмо
			$_SESSION['user_name'] = $_POST['user_name'];//создадим сессию с именем отправителя
			
			echo '<script>
					setTimeout(`document.location.href="/"`);
				</script>';

		}else{
			// если были ошибки, то выводим их
			$show_form = 1; //переменная для показа/скрытия формы
			$msg = '';
			foreach($errors as $one_error){
				$msg .= $one_error.'<br>';
			}
			send_notice($msg,0);
		}
	}


$username = (isset($_POST['user_name'])) ? $_POST['user_name'] : '';//переменные для сохранения введенных данных в полях ввода, чтоб не писать повторно
$usermail = (isset($_POST['user_email'])) ? $_POST['user_email'] : '';
$usertext = (isset($_POST['user_text'])) ? $_POST['user_text'] : '';

?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Обратная связь</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>

<?php if($show_form) { ?>

	<form method="POST">
		<label>Ваше имя:</label><br>
		<input type="text" name="user_name" value="<?= $username; ?>"><br>
		
		<label>Ваш e-mail:</label><br>
		<input type="text" name="user_email" value="<?= $usermail; ?>"><br>
		
		<label>Текст сообщения:</label><br>
		<textarea rows="5" cols="22" name="user_text"><?= $usertext; ?></textarea><br>
        
        <label for="question"><?=$question;?></label><br>
        <input type="text" name="question" id="question"><br><br>
        <input type="hidden" name="questions" value="<?=$question;?>">
        
		<input type="submit" name="submit" value="Отправить"/>
	</form>

<?php } ?>
	

</body>
</html>

<?php
/** 
	# Hacktoberfest Event

	Hacktoberfest adalah sebuah event yang digelar DigitalOcean bersama Dev di mana para pesertanya diminta untuk mengirim sebanyak minimal 4 pull request (PR) ke repo publik (alias opensource) yang disimpan di GitHub.

	# Automated Pull Request and Merging

	Tool ini sebagai jalan pintas yang didedikasikan khusus untuk mengikuti event Hacktoberfest
	Untuk penggunaanya cukup masukkan : 
	- repository url
	- filename (untuk commit)
	- detail akun (akun participant dan akun owner dari repository)

	# Author

	Dibuat dengan cinta oleh : 
	------------------------------
		Agrin Fauzi
		hello@agrinfauzi.web.id
	------------------------------
**/

function create_config(){
	echo "Mohon maaf, isi konfigurasi repository Github dahulu!\n\n";
	echo "----------------------------------\n";
	echo "Config Repository\n";
	echo "----------------------------------\n";
	echo "Repository: ";
	$input_repository = fopen("php://stdin","r");
	$repository = trim(fgets($input_repository));
	echo "Filename: ";
	$input_filename = fopen("php://stdin","r");
	$filename = trim(fgets($input_filename));
	echo "\n";
	echo "----------------------------------";
	$myFile = 'config.ini';
	$fh = fopen($myFile, 'a');
	$text = 'repository="'.$repository.'"'.PHP_EOL.'filename="'.$filename.'"';
	fwrite($fh,$text);
	fclose($fh);
	return parse_ini_file('config.ini');
}

function create_user(){
	echo "Mohon maaf, isi konfigurasi user Github dahulu!\n\n";
	echo "----------------------------------\n";
	echo "User Account\n";
	echo "----------------------------------\n";
	echo "Username Participant: ";
	$userP = fopen("php://stdin","r");
	$userP = trim(fgets($userP));
	echo "\n";
	echo "Password Participant: ";
	$pwdP = fopen("php://stdin","r");
	$pwdP = trim(fgets($pwdP));
	echo "\n";
	echo "----------------------------------\n";
	echo "Username Owner: ";
	$userO = fopen("php://stdin","r");
	$userO = trim(fgets($userO));
	echo "\n";
	echo "Password Owner: ";
	$pwdO = fopen("php://stdin","r");
	$pwdO = trim(fgets($pwdO));
	echo "\n";
	echo "----------------------------------";
	$myFile = 'user.ini';
	$fh = fopen($myFile, 'a');
	$text = 'username_participant="'.$userP.'"'.PHP_EOL.'password_participant="'.$pwdP.'"'.PHP_EOL.'username_owner="'.$userO.'"'.PHP_EOL.'password_owner="'.$pwdO.'"';
	fwrite($fh,$text);
	fclose($fh);
	return parse_ini_file('user.ini');
}

exec('reset');
echo "# AUTO PULL REQUEST HACKTOBERFEST\n";
echo "# Developed by Agrin Fauzi\n\n";

# Initialize User
$detail_config = 'config.ini';
if (file_exists($detail_config)) $config = parse_ini_file('config.ini');
else $config = create_config();

$user_config = 'user.ini';
if (file_exists($user_config)) $user = parse_ini_file('user.ini');
else $user = create_user();

echo "--------- MENU ---------\n";
echo "1. First PR\n";
echo "2. Continue PR\n";
echo "3. Approve PR\n\n";

echo "Pilih menu : ";
$pilih = trim(fgets(STDIN));
echo "\n";

switch ($pilih) {
	case '1':
		# Removing Folder
		exec('rm -rf hf2020/');
		if (file_exists('~/.config/hub')) exec('rm ~/.config/hub');
		if (file_exists('~/.git-credentials')) exec('rm ~/.git-credentials');
		# Adding Stored User
		exec('echo "https://'.$user['username_participant'].':'.$user['password_participant'].'@github.com" > ~/.git-credentials');
		# Cloning URL
		exec('hub clone '.$config['repository'].' hf2020');
		chdir('hf2020');
		# Adding File
		$fh = fopen($config['filename'], 'a');
		$text = 'var smallPiece = true;';
		fwrite($fh,$text);
		fclose($fh);
		# Git Add & Commit
		exec('git add .');
		exec('git commit -am "Update "'.$config['filename']);
		# Git Fork
		// exec('hub delete '.$user['username_participant'].'/'.explode('.',explode('/',$config['repository'])[4])[0]);
		exec('hub fork '.$config['repository'].' origin');
		# Git Push to Forked Repository
		exec('git pull '.$user['username_participant'].' master');
		exec('git push '.$user['username_participant'].' master');
		# Git Pull Request
		exec('hub pull-request');
		break;
	case '2':
		# Removing Folder
		exec('rm -rf hf2020/');
		# Cloning URL
		exec('hub clone '.$config['repository'].' hf2020');
		chdir('hf2020');
		# Adding File
		exec('echo "var smallPiece = true;" >> '.$config['filename']);
		# Git Add & Commit
		exec('git add .');
		exec('git commit -am "Update "'.$config['filename']);
		# Git Push to Forked Repository
		exec('git pull '.$user['username_participant'].' master');
		exec('git push '.$user['username_participant'].' master');
		# Git Pull Request
		exec('hub pull-request');
		break;
	case '3':
		// Re-initialize
		if (file_exists('~/.config/hub')) exec('rm ~/.config/hub');
		if (file_exists('~/.git-credentials')) exec('rm ~/.git-credentials');
		exec('echo "https://'.$user['username_owner'].':'.$user['password_owner'].'@github.com" > ~/.git-credentials');
		// Eksekusi Approve PR
		chdir('hf2020');
		$get_pr = exec('hub pr list --limit 1');
		$clean = str_replace(['     ','  ','upstream','#'],'',$get_pr);
		exec('hub issue update '.$clean.' -l "hacktoberfest-accepted"');
		exec('git checkout -b '.$user['username_participant'].'-master master');
		exec('git pull https://github.com/'.$user['username_participant'].'/'.explode('/',$config['repository'])[4].' master');
		exec('git checkout master');
		exec('git merge --no-ff '.$user['username_participant'].'-master');
		exec('git push origin master');
		break;
	default:
		echo "Menu tidak valid";
		break;
}
?>

